<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * Class OnePica_AvaTaxAr2_Block_Popup_LoginMessage
 */
class OnePica_AvaTaxAr2_Block_Popup_LoginMessage extends Mage_Core_Block_Template
{
    use OnePica_AvaTaxAr2_Block_Secure_Url;

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->_getHelperCustomer()->getLoginUrl();
    }
    /**
     * @return string
     */
    public function getLoginMessage()
    {
        return $this->__('Tax exemption enforced for logged in customers only');
    }

    /**
     * @return \Mage_Customer_Helper_Data
     */
    protected function _getHelperCustomer()
    {
        return Mage::helper('customer');
    }
}
