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

class OnePica_AvaTax_Block_Adminhtml_Export_Queue_Grid extends OnePica_AvaTax_Block_Adminhtml_Export_Abstract_Grid
{

    /**
     * Sets default sort to id field
     *
     */
	public function __construct() {
		parent::__construct();
		$this->setDefaultSort('id');
		$this->setGridHeader(
			'<h3 class="icon-head" style="background-image:url(' . $this->getSkinUrl('images/fam_application_view_tile.gif') . ');">' . 
				$this->__('AvaTax Order Sync Queue') . 
			'</h3>'
		);
	}
	
    /**
     * Adds custom buttons
     *
     */
    public function getMainButtonsHtml()
    {
		$html = $this->getButtonHtml($this->__('Clear Queue Now'), 'setLocation(\'' . $this->getUrl('*/*/clearQueue') . '\')');
    	$html .= $this->getButtonHtml($this->__('Process Queue Now'), 'setLocation(\'' . $this->getUrl('*/*/processQueue') . '\')');
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    /**
     * Adds columns to grid
     *
     * @return self
     */
	protected function _prepareColumns() {
		return $this->_addColumnsForExport(array(
			'id' => 'number',
			'store_id' => 'number',
			'entity_id' => 'number',
			'entity_increment_id' => 'number',
			'type' => Mage::getModel('avatax/records_queue')->getTypeOptions(), 
			'status' => Mage::getModel('avatax/records_queue')->getStatusOptions(), 
			'attempt' => 'number', 
			'message' => 'default', 
			'created_at' => 'datetime', 
			'updated_at' => 'datetime'
		));
		return $this;
	}

    /**
     * Adds collection
     *
     * @return unknown
     */
	protected function _prepareCollection() {
		$collection = Mage::getModel('avatax_records/queue')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

}