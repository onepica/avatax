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
            Mage::throwException("Incorrect Customer Code Format Attribute type. Should be Text Field");
        }

        if($this->_attributeIsVisibleOnFrontend())
        {
            Mage::throwException("Customer Code Format Attribute shouldn't be showed on frontend'");
        }

        return parent::save();
    }

    /**
     * Check if a attribute exists
     *
     * @return bool
     */
    public function _attributeExists()
    {
        $entity = 'customer';
        $code = $this->getValue();
        $attr = Mage::getModel('customer/attribute')->loadByCode($entity, $code);

        $attrExists = $attr->getId() ? true : false;

        return $attrExists;
    }

    /**
     * Check if a attribute type is correct
     *
     * @return bool
     */
    public function _attributeTypeIsCorrect()
    {
        $entity = 'customer';
        $code = $this->getValue();
        $attr = Mage::getModel('customer/attribute')->loadByCode($entity, $code);

        $attrTypeIsCorrect = ($attr->getFrontendInput() == 'text') ? true : false;

        return $attrTypeIsCorrect;
    }

    /**
     * Check if a attribute is visible on storefront
     *
     * @return bool
     */
    public function _attributeIsVisibleOnFrontend()
    {
        $entity = 'customer';
        $code = $this->getValue();
        $attr = Mage::getModel('customer/attribute')->loadByCode($entity, $code);

        $attrIsVisibleOnFrontend = $attr->getIsVisible() ? true : false;

        return $attrIsVisibleOnFrontend;
    }
}
