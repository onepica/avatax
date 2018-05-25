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
class OnePica_AvaTax_Block_Adminhtml_System_Config_Form_Field_Company
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
        $style = '<style>
.form-list td.value .avatax-company-select select {
    width: 65%;
    float: left;
    margin-right: 3px;
}

.form-list td.value .avatax-company-button button {
    width: 34%;
}
</style>';

        $ajaxUrl = Mage::helper('adminhtml')->getUrl('adminhtml/avaTax_ajax/getCompanies');
        $buttonCompanies = array(
            'label'   => Mage::helper('avatax')->__('Get Companies'),
            'onclick' => 'AvaTax._config.updateCompaniesSelect(\'' . $ajaxUrl . '\')',
            'class'   => '',
        );

        $select = $element->getElementHtml();
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData($buttonCompanies)->toHtml();

        $html = sprintf(
            '<div class="avatax-company-select">%s</div><div class="avatax-company-button">%s</div>', $select, $button
        );

        return $style . $html;
    }
}
