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
 * Config Backend CustomerCodeFormatAttribute model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Config_Backend_CustomerCodeFormatAttribute extends Mage_Core_Model_Config_Data
{
    /**
     * Save and validate attribute
     *
     * @return Mage_Core_Model_Abstract
     */
    public function save()
    {
        if(!$this->_attributeExists())
        {
            Mage::throwException("Customer Code Format Attribute doesn't exists");
        }

        if(!$this->_attributeTypeIsCorrect())
        {
            Mage::throwException("Incorrect Customer Code Format Attribute type."
                . "Please, set 'Input Type' as 'Text Field' for attribute.");
        }

        if($this->_attributeIsVisibleOnFrontend())
        {
            Mage::throwException("Customer Code Format Attribute field shouldn't be shown on frontend."
                . "Please, set 'Show on Frontend' as 'No' for attribute.");
        }

        if(!$this->_attributeIsUsedInCorrectForms())
        {
            Mage::throwException("Incorrect Customer Code Format Attribute form usage."
                . "Please, set 'Forms to Use In' as 'Customer Account Edit' or empty for attribute.");
        }

        return parent::save();
    }

    /**
     * Get attribute
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected function _getAttribute()
    {
        $entity = 'customer';
        $code = $this->getValue();
        $attr = Mage::getModel('customer/attribute')->loadByCode($entity, $code);

        return $attr;
    }

    /**
     * Check if a attribute exists
     *
     * @return bool
     */
    protected function _attributeExists()
    {
        $attr = $this->_getAttribute();
        $attrExists = $attr->getId() ? true : false;

        return $attrExists;
    }

    /**
     * Check if a attribute type is correct
     *
     * @return bool
     */
    protected function _attributeTypeIsCorrect()
    {
        $attr = $this->_getAttribute();
        $attrTypeIsCorrect = ($attr->getFrontendInput() == 'text') ? true : false;

        return $attrTypeIsCorrect;
    }

    /**
     * Check if a attribute is visible on storefront
     *
     * @return bool
     */
    protected function _attributeIsVisibleOnFrontend()
    {
        $attr = $this->_getAttribute();
        $attrIsVisibleOnFrontend = $attr->getIsVisible() ? true : false;

        return $attrIsVisibleOnFrontend;
    }

    /**
     * Check if a attribute is used in correct forms
     *
     * @return bool
     */
    protected function _attributeIsUsedInCorrectForms()
    {
        $attr = $this->_getAttribute();
        $forms = $attr->getUsedInForms();
        $attrUsedFormsIsCorrect = true;
        if (in_array('checkout_register', $forms)
            || in_array('customer_account_create', $forms)
            || in_array('adminhtml_checkout', $forms)
        ) {
            $attrUsedFormsIsCorrect = false;
        }

        return $attrUsedFormsIsCorrect;
    }
}
