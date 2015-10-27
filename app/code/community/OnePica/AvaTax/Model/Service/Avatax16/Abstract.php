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
 * Avatax 16 service abstract model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Service_Avatax16_Abstract
{
    /**
     * Transaction type sale
     */
    const TRANSACTION_TYPE_SALE = 'Sale';

    /**
     * Tax location purpose ship from
     */
    const TAX_LOCATION_PURPOSE_SHIP_FROM = 'ShipFrom';

    /**
     * Tax location purpose ship to
     */
    const TAX_LOCATION_PURPOSE_SHIP_TO = 'ShipTo';

    /**
     * Default GW items sku
     */
    const DEFAULT_GW_ITEMS_SKU = 'GwItemsAmount';

    /**
     * Default GW items description
     */
    const DEFAULT_GW_ITEMS_DESCRIPTION = 'Gift Wrap Items Amount';

    /**
     * Default shipping items sku
     */
    const DEFAULT_SHIPPING_ITEMS_SKU = 'Shipping';

    /**
     * Default shipping items description
     */
    const DEFAULT_SHIPPING_ITEMS_DESCRIPTION = 'Shipping costs';

    /**
     * Default GW order sku
     */
    const DEFAULT_GW_ORDER_SKU = 'GwOrderAmount';

    /**
     * Default GW order description
     */
    const DEFAULT_GW_ORDER_DESCRIPTION = 'Gift Wrap Order Amount';

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
     * Get gift wrapping data helper
     *
     * @return \Enterprise_GiftWrapping_Helper_Data
     */
    protected function _getGiftWrappingDataHelper()
    {
        return Mage::helper('enterprise_giftwrapping');
    }

    /**
     * Logs a debug message
     *
     * @param string $type
     * @param string $request the request string
     * @param string $result the result string
     * @param int $storeId id of the store the call is make for
     * @param mixed $additional any other info
     * @return $this
     */
    protected function _log($type, $request, $result, $storeId = null, $additional = null)
    {
        if ($result->getHasError() === false) {
            switch ($this->_getHelper()->getLogMode($storeId)) {
                case OnePica_AvaTax_Model_Source_Logmode::ERRORS:
                    return $this;
                    break;
                case OnePica_AvaTax_Model_Source_Logmode::NORMAL:
                    $additional = null;
                    break;
            }
        }
        $level = $result->getHasError() ? OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_ERROR
            : OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_SUCCESS;

        if (in_array($type, $this->_getHelper()->getLogType($storeId))) {
            Mage::getModel('avatax_records/log')
                ->setStoreId($storeId)
                ->setLevel($level)
                ->setType($type)
                ->setRequest(print_r($request, true))
                ->setResult(print_r($result, true))
                ->setAdditional($additional)
                ->save();
        }
        return $this;
    }

    /**
     * Get date model
     *
     * @return Mage_Core_Model_Date
     */
    protected function _getDateModel()
    {
        return Mage::getSingleton('core/date');
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
}
