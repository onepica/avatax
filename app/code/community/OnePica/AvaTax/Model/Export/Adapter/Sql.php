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
 * Sql export adapter
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Export_Adapter_Sql extends OnePica_AvaTax_Model_Export_Adapter_Abstract
{
    /**
     * Columns to export
     *
     * @var array
     */
    protected $_columns = null;

    /**
     * Get content
     *
     * @return string
     * @throws OnePica_AvaTax_Exception
     */
    public function getContent()
    {
        if (!$this->getCollection()) {
            throw new OnePica_AvaTax_Exception('Collection should be set before export process');
        }

        $content = $this->_getExportHeader();
        $content .= $this->_getExportQueries();

        return $content;
    }

    /**
     * Get export header
     *
     * @return string
     */
    protected function _getExportHeader()
    {
        $version = Mage::getResourceModel('core/resource')->getDbVersion('avatax_records_setup');
        $stores = count(Mage::app()->getStores());
        $content = '-- ' . strtoupper($this->getCollection()->getMainTable()) . " EXPORT\n";
        $content .= '-- Created at: ' . $this->_getDateModel()->gmtDate(DATE_W3C) . "\n";
        $content .= '-- Created by: ' . Mage::getUrl('/') . "\n";
        $content .= '-- Magento v' . Mage::getVersion() . ' // OP_AvaTax v' . $version
            . ' // Stores: ' . $stores . "\n";
        $content .= '-- Total rows: ' . $this->getCollection()->getSize() . "\n\n";
        return $content;
    }

    /**
     * Get columns to export
     *
     * @return array
     */
    protected function _getColumns()
    {
        if ($this->_columns === null) {
            $this->_columns = array_keys(
                $this->getCollection()->setPageSize(1)->setCurPage(1)->getFirstItem()->getData()
            );
        }

        return $this->_columns;
    }

    /**
     * Get export queries
     *
     * @return string
     */
    protected function _getExportQueries()
    {
        $content = '';
        $chunks = array_chunk($this->_getInsertValues(), 50);
        foreach ($chunks as $chunk) {
            $content .= 'INSERT INTO `' . $this->getCollection()->getMainTable()
                . '` (`'. implode('`, `', $this->_getColumns()) . '`) VALUES ';
            $content .= "\n" . implode(",\n", $chunk);
            $content .= ";\n\n";
        }
        return $content;
    }

    /**
     * Get array with insert values
     *
     * @return array
     */
    protected function _getInsertValues()
    {
        $items = $this->getCollection();
        $rows = array();
        foreach ($items as $item) {
            $values = array();
            foreach ($this->_getColumns() as $column) {
                $values[] = $this->getCollection()->getResource()->getReadConnection()->quote($item->getData($column));
            }
            $rows[] = "(" . implode(", ", $values) . ")";
        }
        return $rows;
    }

    /**
     * Get core date model
     *
     * @return \Mage_Core_Model_Date
     */
    protected function _getDateModel()
    {
        return Mage::getSingleton('core/date');
    }
}
