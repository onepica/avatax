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

class OnePica_AvaTax_Adminhtml_GridController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Additional initialization
     *
     */
    protected function _construct() {
        $this->setUsedModuleName('OnePica_AvaTax');
    }

	public function clearQueueAction() {
        Mage::getModel('avatax_records/queue_process')->clear();
        $this->_redirect('*/*/queue');
    }

    
    public function logAction() {
        $this->_setTitle($this->__('Sales'))->_setTitle($this->__('Tax'))->_setTitle($this->__('AvaTax Log'));

        $this->loadLayout()
            ->_setActiveMenu('sales/tax/avatax_log')
            ->_addContent($this->getLayout()->createBlock('avatax/adminhtml_export_log_grid'))
            ->renderLayout();
    }

	public function logViewAction() {
		$this->_setTitle($this->__('Sales'))->_setTitle($this->__('Tax'))->_setTitle($this->__('AvaTax Log'));

		$logId = $this->getRequest()->getParam('id');
        $model   = Mage::getModel('avatax/records_log')->load($logId);

        if (!$model->getId()) {
            $this->_redirect('*/*/');
            return;
        }

		Mage::register('current_event', $model);

        $this->loadLayout()
        	->_setActiveMenu('sales/tax/avatax_log')
        	->renderLayout();
	}
    
    public function queueAction() {
        $this->_setTitle($this->__('Sales'))->_setTitle($this->__('Tax'))->_setTitle($this->__('AvaTax Queue'));

        $this->loadLayout()
            ->_setActiveMenu('sales/tax/avatax_queue')
            ->_addContent($this->getLayout()->createBlock('avatax/adminhtml_export_queue_grid'))
            ->renderLayout();
    }
    
    public function processQueueAction() {
        Mage::getModel('avatax_records/queue_process')->run();
        $this->_redirect('*/*/queue');
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('avatax');
    }
    
    /**
     * Magento <1.4 does not let the title be set
     *
     * @param string
     * @return self
     */
    protected function _setTitle($title) {
    	if(method_exists($this, '_title')) {
    		$this->_title($title);
    	}
    	return $this;
    }

}