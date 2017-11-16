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
class OnePica_AvaTax_Model_Export_Entity_Order_Creditmemo_Item extends OnePica_AvaTax_Model_Export_Entity_Order_Abstract
{
    /** @var array $_relatedCreditmemoIds */
    protected $_relatedCreditmemoIds = array();

    /**
     * Get export columns list
     *
     * @return array
     */
    protected function _getExportColumns()
    {
        $tableName = $this->getResource()->getTableName('sales/creditmemo_item');

        return array_keys($this->getReadConnection()->describeTable($tableName));
    }

    /**
     * Get collection
     *
     * @return Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection
     */
    protected function _getCollection()
    {
        /** @var \Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_creditmemo_item_collection');

        /* collection to export only for one quote */
        if ($this->getQuoteId()) {
            $collection->addFieldToFilter('parent_id', array('in' => $this->getRelatedCreditmemoIds()));
        }

        return $collection;
    }

    /**
     * @return array
     */
    public function getRelatedCreditmemoIds()
    {
        if (!$this->_relatedCreditmemoIds) {
            $items = Mage::getResourceModel('sales/order_creditmemo_collection')
                         ->addFieldToFilter('order_id', array('in' => $this->getRelatedOrderIds()))
                         ->addFieldToSelect('entity_id')
                         ->getItems();

            $this->_relatedCreditmemoIds = array_keys($items);
        }

        return $this->_relatedCreditmemoIds;
    }
}
