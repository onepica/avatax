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
class OnePica_AvaTax_Block_Adminhtml_System_Config_Form_Field_Export
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
        $buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button');
        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );

        $buttonExportLogs = array(
            'label'   => Mage::helper('avatax')->__('Export Logs'),
            'onclick' => 'setLocation(\'' .
                Mage::helper('adminhtml')->getUrl('adminhtml/avaTax_export/log', $params) . '\')',
            'class'   => '',
        );
        $buttonExportQueue = array(
            'label'   => Mage::helper('avatax')->__('Export Queue'),
            'onclick' => 'setLocation(\'' .
                Mage::helper('adminhtml')->getUrl('adminhtml/avaTax_export/queue', $params) . '\')',
            'class'   => '',
        );
        $buttonExportConfig = array(
            'label'   => Mage::helper('avatax')->__('Export Config'),
            'onclick' => 'setLocation(\''
                . Mage::helper('adminhtml')->getUrl('adminhtml/avaTax_export/config', $params) . '\')',
            'class'   => '',
        );

        $buttons = array(
            $buttonBlock->setData($buttonExportLogs)->toHtml(),
            $buttonBlock->setData($buttonExportQueue)->toHtml(),
            $buttonBlock->setData($buttonExportConfig)->toHtml()
        );

        $html = implode(' &nbsp; ', $buttons);

        return $html;
    }
}
