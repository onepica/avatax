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
abstract class OnePica_AvaTax_Model_Export_Entity_Order_Abstract extends OnePica_AvaTax_Model_Export_Entity_Abstract
{
    /** @var int|null $_quoteId Quote id to get log collection only for this quote */
    protected $_quoteId = null;

    /** @var int|null $_storeId Store id to load collection of items related to this store */
    protected $_storeId = null;

    /** @var null|Mage_Core_Model_Resource $_resource */
    protected $_resource = null;

    /** @var null|Varien_Db_Adapter_Interface $_readConnection */
    protected $_readConnection = null;

    /** @var array $_exportColumns */
    protected $_exportColumns = array();

    /** @var array $_exportColumns */
    protected $_relatedOrderIds = array();

    /**
     * Get quote id to get collection only for this quote
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->_quoteId;
    }

    /**
     * Get store id to get collection of items
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Get quote id to get collection only for this quote
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        $this->_quoteId = $quoteId;

        return $this;
    }

    /**
     * Get store id to get collection of items
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;

        return $this;
    }

    /**
     * @return \Mage_Core_Model_Resource|null
     */
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getSingleton('core/resource');
        }

        return $this->_resource;
    }

    /**
     * @return null|\Varien_Db_Adapter_Interface
     */
    public function getReadConnection()
    {
        if (!$this->_readConnection) {
            $this->_readConnection = $this->getResource()->getConnection('core_read');;
        }

        return $this->_readConnection;
    }

    /**
     * @return null|array
     */
    public function getExportColumns()
    {
        if (!$this->_exportColumns) {
            $this->_exportColumns = $this->_getExportColumns();
        }

        return $this->_exportColumns;
    }

    /**
     * @return array
     */
    public function getRelatedOrderIds()
    {
        if (!$this->_relatedOrderIds) {
            $items = Mage::getResourceModel('sales/order_collection')
                         ->addFieldToFilter('quote_id', $this->getQuoteId())
                         ->addFieldToSelect('entity_id')
                         ->getItems();

            $this->_relatedOrderIds = array_keys($items);
        }

        return $this->_relatedOrderIds;
    }
}
