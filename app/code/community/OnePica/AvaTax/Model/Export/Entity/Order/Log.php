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
class OnePica_AvaTax_Model_Export_Entity_Order_Log extends OnePica_AvaTax_Model_Export_Entity_Log
{
    /**
     * Quote id to get log collection only for this quote
     *
     * @var int|null
     */
    protected $_quoteId = null;

    /** @var array $_exportColumns */
    protected $_exportColumns = array();

    /**
     * Get collection
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _getCollection()
    {
        /** @var OnePica_AvaTax_Model_Records_Mysql4_Log_Collection $collection */
        $collection = parent::_getCollection();

        /* collection to export only for one quote */
        if ($this->getQuoteId()) {
            $collection->selectOnlyForQuote($this->getQuoteId());
        }

        return $collection;
    }

    /**
     * Set quote id to get collection only for this quote
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
     * Get quote id to get collection only for this quote
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->_quoteId;
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
}
