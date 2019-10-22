<?php
/*************************************************************************************/
/*      Copyright (c) Nicolas Léon                                                   */
/*      email : nicolas@omnitic.com                                                  */
/*      web : http://www.omnitic.com                                                 */
/*************************************************************************************/

namespace DealerPickup\EventListeners;

use DealerPickup\DealerPickup;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ConfigQuery;

class SendDeliveryEmail implements EventSubscriberInterface
{
    /**
     * @var MailerFactory
     */
    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param OrderEvent $event
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateStatus(OrderEvent $event)
    {
        $order = $event->getOrder();

        $deliveryRef = $order->getDeliveryRef();

        die($order->getDeliveryModuleId());
        die(DealerPickup::getModuleId());
        if ($order->isSent()
            &&
            ! empty($deliveryRef)
            &&
            $order->getDeliveryModuleId() == DealerPickup::getModuleId()
        ) {
            if (null !== $contactEmail = ConfigQuery::read('store_email')) {
                $this->mailer->sendEmailToCustomer(
                    DealerPickup::TRACKING_MESSAGE_NAME,
                    $order->getCustomer(),
                    [
                        'order_id' => $order->getId(),
                    ]
                );
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS => array("updateStatus", 128)
        );
    }
}
