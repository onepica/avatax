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
 * Config form field export block
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_System_Config_Form_Field_Title
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Get element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     * @throws Exception
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return "";
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $newData = array('can_use_website_value' => false,
                         'can_use_default_value' => false, 'scope' => '');

        //save origin element data
        $orgData = array();
        foreach ($newData as $key => $value) {
            $orgData[$key] = $element->getData($key);
        }

        //configure element data to render it properly
        foreach ($newData as $key => $value) {
            $element->setData($key, $value);
        }

        //render element
        $result = parent::render($element);

        //restore data if it did not changed in renderer
        foreach ($orgData as $key => $value) {
            if ($element->getData($key) === $newData[$key]) {
                $element->setData($key, $value);
            }

        }

        return $result;
    }
}
