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
 * Class OnePica_AvaTax_Block_Adminhtml_Sales_Order_Create_Form_Account
 */
class OnePica_AvaTax_Block_Adminhtml_Sales_Order_Create_Form_Account
    extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Account
{
    /**
     * Prepare Form and add elements to form
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Form_Account
     * @throws \Varien_Exception
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var \Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->_form->getElement('main');

        /** @var OnePica_AvaTax_Model_Entity_Attribute_Source_Boolean $source */
        $source = Mage::getModel('avatax/entity_attribute_source_boolean');

        $fieldset->addField(
            'avatax_lc_seller_is_importer', 'select', array(
                'name'   => 'order[account][avatax_lc_seller_is_importer]',
                'label'  => Mage::helper('avatax')->__('Seller is an importer'),
                'values' => $source->getOptionArray(),
            )
        );

        $this->_form->setValues($this->getFormValues());

        return $this;
    }
}
