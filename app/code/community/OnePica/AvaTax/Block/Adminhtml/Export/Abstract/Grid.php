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

abstract class OnePica_AvaTax_Block_Adminhtml_Export_Abstract_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Constructor: sets grid id and sort order
     *
     */
	public function __construct() {
	  parent::__construct();
	  $this->setId('export_grid');
	  $this->setDefaultDir('DESC');
	  $this->setSaveParametersInSession(false);
	  
	  $url = Mage::helper('avatax')->getDocumentationUrl();
	  Mage::helper('adminhtml')->setPageHelpUrl($url);
	}

    /**
     * Mass adds columns based on passed in array
     *
     * @param array $columns array(columnName => dataType)
     * @return self
     */
	protected function _addColumnsForExport($columns) {
		foreach ($columns as $name=>$type) {
			if(is_array($type)) {
				$this->addColumn($name, array(
					'header'    => Mage::helper('avatax')->__(ucwords(str_replace('_', ' ', $name))),
					'index'     => $name,
					'type'      => 'options',
					'options'	=> $type
				));
			} else {
				$this->addColumn($name, array(
					'header'    => Mage::helper('avatax')->__(ucwords(str_replace('_', ' ', $name))),
					'index'     => $name,
					'type'      => $type
				));
			}
		}
		return $this;
	}

    /**
     * Creates SQL code from collection
     *
     * @return string
     */
	public function getSql() {
	    $this->_isExport = true;
	    $this->_prepareGrid();
	    $this->getCollection()->getSelect()->limit();
	    $this->getCollection()->setPageSize(0);
	    $this->getCollection()->load();
	    $this->_afterLoadCollection();
	    
	    $columns = array();
	    foreach ($this->_columns as $column) {
	        if (!$column->getIsSystem()) {
	            $columns[] = $column->getIndex();
	        }
	    }
	    
	    $resourceModel = $this->getCollection()->getResource();
	    $adapter = $resourceModel->getReadConnection();
	    $version = Mage::getResourceModel('core/resource')->getDbVersion('avatax_records_setup');
	    $stores = count(Mage::app()->getStores());
	    
	    $sql  = '-- ' . strtoupper($resourceModel->getMainTable()) . " EXPORT\n";
	    $sql .= '-- Created at: ' . gmdate(DATE_W3C) . "\n";
	    $sql .= '-- Created by: ' . Mage::getUrl('/') . "\n";
	    $sql .= '-- Magento v' . Mage::getVersion() . ' // OP_AvaTax v' . $version . ' // Stores: ' . $stores . "\n";
	    $sql .= '-- Total rows: ' . $this->getCollection()->count() . "\n\n";
	    
	    $rows = array();
	    foreach ($this->getCollection() as $item) {
	    	$values = array();
	        foreach($columns as $column) {
	        	$values[] = $adapter->quote($item->getData($column));
	        }
	        $rows[] = "(" . implode(", ", $values) . ")";
	    }
	    
	    $chunks = array_chunk($rows, 50);
	    unset($rows);
	    
	    foreach($chunks as $chunk) {
	    	 $sql .= 'INSERT INTO `' . $resourceModel->getMainTable() . '` (`' . implode('`, `', $columns) . '`) VALUES ';
	    	 $sql .= "\n" . implode(",\n", $chunk);
	    	 $sql .= ";\n\n";
	    }
	    
	    return $sql;
	}
    
}