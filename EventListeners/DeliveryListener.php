<?php
/*************************************************************************************/
/*      Copyright (c) Nicolas Léon                                                   */
/*      email : nicolas@omnitic.com                                                  */
/*      web : http://www.omnitic.com                                                 */
/*************************************************************************************/

namespace DealerPickup\EventListeners;

use DealerPickup\DealerPickup;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Session\Session;
use Dealer\Model\DealerQuery;
use Thelia\Model\OrderAddressQuery;
use Thelia\Model\CountryQuery;
use Thelia\Model\ConfigQuery;

class DeliveryListener extends BaseAction implements EventSubscriberInterface
{
    /** @var RequestStack */
    protected $requestStack;

    /**
     * DeliveryPostageListener constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    /**
     * @param DeliveryPostageEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function processDeliveryPostageEvent(DeliveryPostageEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $valid = false;
        $price = DealerPickup::POSTAGE_AMOUNT; // Pick up from a dealer is free

        /** @var Request $session */
        $request = $this->requestStack->getCurrentRequest();

        /** @var Session $session */
        $session = $request->getSession();

        $deliveryDate = (new \DateTime())->add(new \DateInterval("P" . DealerPickup::DEFAULT_DELIVERY_DAYS . "D"));

        $event->setPostage($price)->setDeliveryDate($deliveryDate);
        $event->setValidModule(true);

        $event->stopPropagation();
    }



    /**
     * Update the order delivery address with dealer info
     *
     * @param OrderEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateOrderDeliveryAddress(OrderEvent $event)
    {
        /** @var Session $session */
        $session = $this->requestStack->getCurrentRequest()->getSession();

        if (null !== $store_id = $session->get(DealerPickup::SESSION_SELECTED_DEALER_ID)) {
            $dealer = DealerQuery::create()->joinWithI18n('fr_FR')->findPK($store_id);

            if (null !== $orderAddress = OrderAddressQuery::create()->findPK($event->getOrder()->getDeliveryOrderAddressId())) {
                $orderAddress
                    ->setCompany(ConfigQuery::read('store_name'))
                    ->setFirstname($dealer->getTitle())
                    ->setLastname($store_id) // Keep a reference to the store/dealer id
                    ->setAddress1($dealer->getAddress1())
                    ->setAddress2($dealer->getAddress2())
                    ->setAddress3($dealer->getAddress3())
                    ->setZipcode($dealer->getZipcode())
                    ->setCity($dealer->getCity())
                    ->setCountry(CountryQuery::create()->findPK($dealer->getCountryId()))
                    ->save();
            }
        }
    }

    /**
     * @param OrderEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateCurrentDeliveryAddress(OrderEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        $dealer_address_id = $request->get(DealerPickup::SESSION_SELECTED_DEALER_ID, null);

        /** @var Session $session */
        $session = $request->getSession();

        if ($event->getDeliveryModule() == DealerPickup::getModuleId()) {
            $session->set(DealerPickup::SESSION_SELECTED_DEALER_ID, $dealer_address_id);
        }
    }

    /**
     * Clear stored information once the order has been processed.
     *
     * @param OrderEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function clearDeliveryData(OrderEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

        // Clear the session context
        $session->remove(DealerPickup::SESSION_SELECTED_DEALER_ID);
    }

    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::getModuleEvent(
                TheliaEvents::MODULE_DELIVERY_GET_POSTAGE,
                DealerPickup::getModuleCode()
            ) => [ "processDeliveryPostageEvent", 128 ],
            TheliaEvents::ORDER_SET_DELIVERY_MODULE => ['updateCurrentDeliveryAddress', 64],
            TheliaEvents::ORDER_BEFORE_PAYMENT => ['updateOrderDeliveryAddress', 256],
            TheliaEvents::ORDER_CART_CLEAR => ['clearDeliveryData', 256],

        ];
    }
}
