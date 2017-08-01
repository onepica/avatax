<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * The Onepage Shipping Method Available block
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Checkout_Onepage_Shipping_Method_Available
    extends OnePica_AvaTax_Block_Checkout_Onepage_Method
{
    /**
     * Normalization Disabler Block Name
     * @var null
     */
    protected $_disablerBlockName = 'avatax/checkout_onepage_address_normalization_disabler';

    /**
     * Check if Normalization Notification Is Allowed on current checkout step
     *
     * @return bool
     */
    protected function _showNotification()
    {
        return true;
    }
}
