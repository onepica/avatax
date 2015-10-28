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
 * Admin grid controller
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Adminhtml_AvaTax_GridController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Additional initialization
     */
    protected function _construct()
    {
        $this->setUsedModuleName('OnePica_AvaTax');
    }

    /**
     * Clear queue action
     *
     * @return $this
     */
    public function clearQueueAction()
    {
        Mage::getModel('avatax_records/queue_process')->clear();
        $this->_redirect('*/*/queue');
        return $this;
    }

    /**
     * Log action
     *
     * @return $this
     */
    public function logAction()
    {
        $this->_setTitle($this->__('Sales'))->_setTitle($this->__('Tax'))->_setTitle($this->__('AvaTax Log'));

        $this->loadLayout()
            ->_setActiveMenu('sales/tax/avatax_log')
            ->renderLayout();
        return $this;
    }

    /**
     * Log view action
     *
     * @return $this
     */
    public function logViewAction()
    {
        $this->_setTitle($this->__('Sales'))->_setTitle($this->__('Tax'))->_setTitle($this->__('AvaTax Log'));

        $logId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('avatax/records_log')->load($logId);

        if (!$model->getId()) {
            $this->_redirect('*/*/log');
            return $this;
        }

        Mage::register('current_event', $model);

        $this->loadLayout()
            ->_setActiveMenu('sales/tax/avatax_log')
            ->renderLayout();
        return $this;
    }

    /**
     * Queue action
     *
     * @return $this
     */
    public function queueAction()
    {
        $this->_setTitle($this->__('Sales'))->_setTitle($this->__('Tax'))->_setTitle($this->__('AvaTax Queue'));

        $this->loadLayout()
            ->_setActiveMenu('sales/tax/avatax_queue')
            ->renderLayout();
        return $this;
    }

    /**
     * Process queue action
     *
     * @return $this
     */
    public function processQueueAction()
    {
        Mage::getModel('avatax_records/queue_process')->run();
        $this->_redirect('*/*/queue');
        return $this;
    }

    /**
     * Check if is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('avatax');
    }

    /**
     * Magento <1.4 does not let the title be set
     *
     * @param string $title
     * @return $this
     */
    protected function _setTitle($title)
    {
        if (method_exists($this, '_title')) {
            $this->_title($title);
        }
        return $this;
    }
}
