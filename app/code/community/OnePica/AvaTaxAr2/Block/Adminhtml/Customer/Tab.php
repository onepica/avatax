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
 * The AvaTaxAr2 Adminhtml Custome Tab class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Block_Adminhtml_Customer_Tab extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Set the template for the block
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('onepica/avataxar2/customer/tab.phtml');
        $this->initForm();
    }

    /**
     * @return $this
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('avatax_customer_');
        $customer = Mage::registry('current_customer');

        /** @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setEntity($customer)
                     ->setFormCode(OnePica_AvaTaxAr2_Helper_Data::AVATAX_CUSTOMER_EXEMPTION_FORM_CODE)
                     ->initDefaultValues();

        $fieldset = $form->addFieldset(
            'base_fieldset', array('legend' => $this->__('Exemption Information'))
        );

        $attributes = $customerForm->getAttributes();
        foreach ($attributes as $attribute) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();
        }

        $this->_setFieldset($attributes, $fieldset);

        if ($customer->isReadonly()) {
            foreach ($customer->getAttributes() as $attribute) {
                $element = $form->getElement($attribute->getAttributeCode());
                if ($element) {
                    $element->setReadonly(true, true);
                }
            }
        }

        $form->setValues($customer->getData());

        $this->setForm($form);

        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('AvaTax Exemptions');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Click here to view AvaTax Exemption certificates');
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
