<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * The AvaTaxAr2 Adminhtml Customer Tab class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Block_Adminhtml_Customer_Documents extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Set the template for the block
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('onepica/avataxar2/customer/documents.phtml');
        $this->initForm();
    }

    /**
     * @return $this
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('avatax_customer_');
        $form->setFieldNameSuffix('avatax_customer');

        $customer = Mage::registry('current_customer');

        /** @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setEntity($customer)
                     ->setFormCode(OnePica_AvaTaxAr2_Helper_Data::AVATAX_CUSTOMER_DOCUMENTS_FORM_CODE)
                     ->initDefaultValues();

        $fieldset = $form->addFieldset(
            'base_fieldset', array('legend' => $this->__('General Information'))
        );

        $attributes = $customerForm->getAttributes();
        foreach ($attributes as $attribute) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();
        }

        $this->_setFieldset($attributes, $fieldset);

        $buttons = array();

        /** @var OnePica_AvaTax_Helper_Config $avaHelper */
        $avaHelper = Mage::helper('avatax/config');
        array_push($buttons,
            $fieldset->addField('send_invitation', 'button', array(
                'label' => '',
                'value' => $avaHelper->__('Send invitation e-mail to customer.'),
                'name'  => 'send-invitation',
                'class' => 'form-button',
                'onclick' => "
                              var customerCode = $('avatax_customer_{$avaHelper->getCustomerCodeFormatAttribute()}').value;
                              var url = '{$this->getUrl('*/avaTaxAr2_customer/sendInvitation/id/' . $customer->getId())}customerCode/' + customerCode;
                              setLocation(url);",
            ))
        );

        array_push($buttons,
            $fieldset->addField('save_customer_to_avalara', 'button', array(
                'label' => '',
                'value' => $avaHelper->__('Update Customer Information in Avalara.'),
                'name'  => 'save-customer-to-avalara',
                'class' => 'form-button',
                'onclick' => "
                              var customerCode = $('avatax_customer_{$avaHelper->getCustomerCodeFormatAttribute()}').value;
                              var url = '{$this->getUrl('*/avaTaxAr2_customer/saveCustomerToAvalara/id/'. $customer->getId())}customerCode/' + customerCode;
                              setLocation(url);",
            ))
        );

        if ($customer->isReadonly()) {
            foreach ($customer->getAttributes() as $attribute) {
                $element = $form->getElement($attribute->getAttributeCode());
                if ($element) {
                    $element->setReadonly(true, true);
                }
            }
        }

        $values = array();
        foreach ($buttons as $button) {
            $values[$button->getData('html_id')] = $button->getValue();
        }
        $values = array_merge($values, $customer->getData());
        $form->setValues($values);

        $this->setForm($form);

        return $this;
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'documents_grid',
            $this->getLayout()->createBlock('avataxar2/adminhtml_customer_documents_grid', 'documents.grid')
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('AvaTax Documents');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Click here to view AvaTax Documents');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
