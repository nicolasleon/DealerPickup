<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace DealerPickup;

use Thelia\Module\AbstractDeliveryModule;
use Thelia\Model\Country;

class DealerPickup extends AbstractDeliveryModule
{
    /** @var string */
    const DOMAIN_NAME = 'dealerpickup';
    const SESSION_SELECTED_DEALER_ID  = 'pickup_dealer_id';
    const DEFAULT_DELIVERY_DAYS  = 3;
    const TRACKING_MESSAGE_NAME  = 'dealerpickup_tracking_message';
    const POSTAGE_AMOUNT  = 0.0;

    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */

    public function isValidDelivery(Country $country)
    {
        return true; // This delivery method is always available
    }

    /**
     *
     *  Compute and return the delivery price
     *
     * @param Country $country
     * @return float
     */
    public function getPostage(Country $country)
    {
        return self::POSTAGE_AMOUNT;
    }
}
