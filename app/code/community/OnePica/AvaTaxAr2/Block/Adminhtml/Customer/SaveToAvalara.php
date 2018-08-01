<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

/**
 * Adminhtml Tax Class Edit
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class OnePica_AvaTaxAr2_Block_Adminhtml_Customer_SaveToAvalara extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * OnePica_AvaTaxAr2_Block_Adminhtml_Customer_SaveToAvalara constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $data = $this->_getFormData();

        if (!$this->hasData('template')) {
            $this->setTemplate('widget/form/container.phtml');
        }

        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $data->getBackUrl() . '\')',
            'class'     => 'back',
        ), -1);

        $this->_addButton('save', array(
            'label'     => $data->getIsNew() ? $this->_getHelper()->__('Register') : $this->_getHelper()->__('Update'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'save',
        ), 1);
    }

    /**
     * @return mixed
     */
    private function _getFormData()
    {
        return  Mage::registry('save_customer_to_avalara');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return $this->_getFormData()->getIsNew()
                ? $this->_getHelper()->__('Registration in Avalara.')
                : $this->_getHelper()->__('Update Customer Information in Avalara.');
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $form = $this->getLayout()->createBlock('avataxar2/adminhtml_customer_saveToAvalara_form');
        $this->setChild('form', $form);

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getFormHtml()
    {
        $this->getChild('form')->setData('action', null);
        return $this->getChildHtml('form');
    }

    /**
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper()
    {
        return Mage::helper('adminhtml');
    }
}
