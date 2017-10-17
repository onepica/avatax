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
 * Log export entity model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Export_Entity_Order_Quote_Item extends OnePica_AvaTax_Model_Export_Entity_Order_Abstract
{
    /**
     * OnePica_AvaTax_Model_Export_Entity_Order_QuoteItem constructor.
     *
     * @param $storeId
     */
    public function __construct($storeId)
    {
        $this->_storeId = $storeId;
    }

    /**
     * Get export columns list
     *
     * @return array
     */
    protected function _getExportColumns()
    {
        $tableName = $this->getResource()->getTableName('sales/quote_item');

        return array_keys($this->getReadConnection()->describeTable($tableName));
    }

    /**
     * Get collection
     *
     * @return Mage_Sales_Model_Resource_Quote_Item_Collection|null
     */
    protected function _getCollection()
    {
        /** @var \Mage_Sales_Model_Resource_Quote_Item_Collection|null $collection */
        $collection = null;

        if ($this->getQuoteId() && $this->getStoreId()) {
            /** @var \Mage_Core_Model_Store $store */
            $store = Mage::getSingleton('core/store')->load($this->getStoreId());
            /** @var \Mage_Sales_Model_Quote $quote */
            $quote = Mage::getModel('sales/quote')->setStore($store)->load($this->getQuoteId());

            $collection = Mage::getResourceModel('sales/quote_item_collection');
            $collection->setQuote($quote);
            $collection->load();
        }

        return $collection;
    }
}
