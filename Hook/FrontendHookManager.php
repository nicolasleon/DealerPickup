<?php
/*************************************************************************************/
/*      Copyright (c) Nicolas Léon                                                   */
/*      email : nicolas@omnitic.com                                                  */
/*      web : http://www.omnitic.com                                                 */
/*************************************************************************************/

namespace DealerPickup\Hook;

use DealerPickup\DealerPickup;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

class FrontendHookManager extends BaseHook
{
    public function onOrderDeliveryExtra(HookRenderEvent $event)
    {
        // Clear the session context
        $this->getSession()->remove(DealerPickup::SESSION_SELECTED_DEALER_ID);

        $addressId = $this->getRequest()->get('address_id', 0);

        $event->add(
            $this->render(
                'dealerpickup/order-delivery-extra.html',
                [
                    'module_id' => DealerPickup::getModuleId(),
                    'address_id' => $addressId,
                    'postage_amount' => DealerPickup::POSTAGE_AMOUNT,
                    'session_selected_dealer_id' => DealerPickup::SESSION_SELECTED_DEALER_ID
                ]
            )
        );
    }

    public function onAccountOrderDeliveryAddress(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                'dealerpickup/order-delivery-address.html',
                [
                    'order_id' => $event->getArgument('order'),
                    'module_id' => $event->getArgument('module'),
                ]
            )
        );
    }

    public function onOrderInvoiceDeliveryAddress(HookRenderEvent $event)
    {
        $addressId = $this->getRequest()->getSession()->get(DealerPickup::SESSION_SELECTED_DEALER_ID);

        $event->add(
            $this->render(
                'dealerpickup/delivery-address.html',
                [
                    'order_id' => $event->getArgument('order'),
                    'module_id' => $event->getArgument('module'),
                    'address_id' => $addressId
                ]
            )
        );
    }
}
