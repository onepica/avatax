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
 * Config form field landed cost block
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_System_Config_Form_Fieldset_Shipping_Methods extends
    Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /** @var OnePica_AvaTax_Helper_Landedcost_Shipping $_shippingHelper */
    protected $_shippingHelper;

    protected $_dummyElement;

    protected $_fieldRenderer;

    protected $_fieldMultiselectRenderer;

    /**
     * Initialize factory instance
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_shippingHelper = Mage::helper('avatax/landedcost_shipping');
        parent::__construct($args);
    }

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     * @throws \Varien_Exception
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '';

        /** @var OnePica_AvaTax_Model_Landedcost_Shipping_Method[] $allShippingMethods */
        $allShippingMethods = $this->_shippingHelper->getAllShippingMethods();
        foreach ($allShippingMethods as $shippingMethod) {
            $html .= $this->_getShippingMethodSelectHtml($element, $shippingMethod);

            if ($shippingMethod->getCarrierMethods()) {
                $html .= '<tbody id="' . $shippingMethod->getId() . '_carrier_methods' . '">';

                foreach ($this->_shippingHelper->getSelectValues() as $value) {
                    $html .= $this->_getShippingMethodMultiselectHtml($element, $shippingMethod, $value);
                }

                $html .= '</tbody>';
            }
        }

        return $html;
    }

    /**
     * Returns fieldset renderer
     *
     * @return Mage_Adminhtml_Block_System_Config_Form_Field
     */
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }

        return $this->_fieldRenderer;
    }

    /**
     * Returns field multiselect renderer
     *
     * @return OnePica_AvaTax_Block_Adminhtml_System_Config_Form_Field_Carrier_Methods
     */
    protected function _getMultiselectFieldRenderer()
    {
        if (empty($this->_fieldMultiselectRenderer)) {
            $this->_fieldMultiselectRenderer
                = Mage::getBlockSingleton('avatax/adminhtml_system_config_form_field_carrier_methods');
        }

        return $this->_fieldMultiselectRenderer;
    }

    /**
     * Generate html for single shipping method
     *
     * @param $fieldset       Varien_Data_Form_Abstract
     * @param $shippingMethod Varien_Object
     * @return mixed
     * @throws \Varien_Exception
     */
    protected function _getShippingMethodSelectHtml($fieldset, $shippingMethod)
    {
        $formId = $shippingMethod->getId();

        $isMultiSelect = $shippingMethod->getCarrierMethods() !== null;
        $field = $fieldset->addField(
            $formId, 'select', array(
                'name'                  => 'groups[avatax_landed_cost_shipping][fields][' . $formId . '][value]',
                'label'                 => $shippingMethod->getTitle(),
                'comment'               => (!$isMultiSelect
                                                ? 'Avalara shipping code for this shipping method'
                                                : 'Avalara shipping code for shipping methods selected in multiselect below.'
                                            ),
                'value'                 => $this->_shippingHelper->getConfigFormData($formId),
                'values'                => $this->_shippingHelper->getSelectValues($isMultiSelect),
                'class'                 => "avatax-lc-carrier-method",
                'can_use_default_value' => $this->getForm()->canUseDefaultValue(1),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue(1),
            )
        )->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }

    /**
     * Generate multiselect html for single shipping method type
     *
     * @param $fieldset             Varien_Data_Form_Abstract
     * @param $shippingMethod       OnePica_AvaTax_Model_Landedcost_Shipping_Method
     * @param $value                array
     * @return mixed
     */
    protected function _getShippingMethodMultiselectHtml($fieldset, $shippingMethod, $value)
    {
        $formId = $shippingMethod->getId() . '_' . $value['value'];

        $field = $fieldset->addField(
            $formId, 'multiselect', array(
                'name'     => 'groups[avatax_landed_cost_shipping][fields][' . $formId . '][value][]',
                'title'    => $value['label'],
                'required' => false,
                'class'    => "avatax-lc-carrier-method-multiselect",
                'values'   => $shippingMethod->getCarrierMethods(),
                'value'    => $this->_shippingHelper->getConfigFormData($formId),
            )
        )->setRenderer($this->_getMultiselectFieldRenderer());

        return $field->toHtml();
    }
}
