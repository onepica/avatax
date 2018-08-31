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
class OnePica_AvaTax_Block_Adminhtml_System_Config_Form_Field_Carrier_Methods
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Decorate field row html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param string                            $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '" style="display: none;">' . $html . '</tr>';
    }
}
