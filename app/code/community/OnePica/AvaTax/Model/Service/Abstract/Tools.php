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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * @class OnePica_AvaTax_Model_Service_Abstract_Tools
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Abstract_Tools extends Varien_Object
{
    /**
     * Length of time in minutes for cached rates
     *
     * @var int
     */
    const CACHE_TTL = 120;

    /**
     * Can send request
     *
     * @var bool
     */
    protected $_canSendRequest = true;

    /**
     * Alias to the helper translate method.
     *
     * @return string
     * @skipPublicMethodNaming __
     */
    public function __()
    {
        $args = func_get_args();
        return call_user_func_array(array($this->_getHelper(), '__'), $args);
    }

    /**
     * Retrieve converted date taking into account the current time zone and store.
     *
     * @param string $gmt
     * @param int    $storeId
     * @return string
     */
    protected function _convertGmtDate($gmt, $storeId)
    {
        return $this->_getHelper()
            ->storeDate($storeId, $gmt, false, Varien_Date::DATETIME_INTERNAL_FORMAT)
            ->toString(Varien_Date::DATE_INTERNAL_FORMAT);
    }

    /**
     * Retrieve application instance
     *
     * @return Mage_Core_Model_App
     */
    protected function _getApp()
    {
        return Mage::app();
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avatax');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Address
     */
    protected function _getAddressHelper()
    {
        return Mage::helper('avatax/address');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getErrorsHelper()
    {
        return Mage::helper('avatax/errors');
    }

    /**
     * Test to see if the product carries its own numbers or is calculated based on parent or children
     *
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item|mixed $item
     * @return bool
     */
    public function isProductCalculated($item)
    {
        // check if item has methods as far as shipping, gift wrapping, printed card item comes as Varien_Object
        if (method_exists($item, 'isChildrenCalculated') && method_exists($item, 'getParentItem')) {
            if ($item->isChildrenCalculated() && !$item->getParentItem()) {
                return true;
            }
            if (!$item->isChildrenCalculated() && $item->getParentItem()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve Vat Id
     *
     * @param Mage_Sales_Model_Order|OnePica_AvaTax_Model_Sales_Quote_Address $object
     * @return string
     */
    protected function _getVatId($object)
    {
        if ($object instanceof Mage_Sales_Model_Order) {
            return $this->_getVatIdByOrder($object);
        }

        return $this->_getVatIdByQuoteAddress($object);
    }

    /**
     * Retrieve Vat Id from quote address
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address $address
     * @return string
     */
    protected function _getVatIdByQuoteAddress($address)
    {
        $vatId = $address->getVatId()
            ?: $address->getQuote()->getBillingAddress()->getVatId();
        return (string)$vatId;
    }

    /**
     * Retrieve Vat Id from order
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _getVatIdByOrder($order)
    {
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress && $shippingAddress->getVatId()) {
            return $shippingAddress->getVatId();
        }
        return $order->getBillingAddress()->getVatId();
    }

    /**
     * Is can send request
     *
     * @return bool
     */
    public function isCanSendRequest()
    {
        return $this->_canSendRequest;
    }

    /**
     * Set can send request
     *
     * @param bool $canSendRequest
     * @return $this
     */
    public function setCanSendRequest($canSendRequest)
    {
        $this->_canSendRequest = $canSendRequest;

        return $this;
    }
}
