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
 * Config tab in order view
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_Order_View_Tab_Avatax extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('onepica/avatax/order/view/tab/avatax.phtml');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('AvaTax');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('AvaTax');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * This order
     *
     * @return mixed
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Head text
     *
     * @return string
     */
    public function getHeadText()
    {
        return $this->__('Logs');
    }

    /**
     * Get element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     * @throws Exception
     */
    protected function getExportButtonHtml()
    {
        $buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button');
        $btnClass = 'disabled';
        $btnOnClick = '';

        if (Mage::helper('avatax/config')->getConfigAdvancedLog()) {
            $orderId = $buttonBlock->getRequest()->getParam('order_id');
            /** @var \Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($orderId);

            if ($order->getId()) {
                $params = array(
                    'website'  => $buttonBlock->getRequest()->getParam('website'),
                    'order_id' => $order->getId(),
                    'quote_id' => $order->getQuoteId(),
                    'store_id' => $order->getStoreId(),
                );
                $exportUrl = Mage::helper('adminhtml')->getUrl('adminhtml/avaTax_export/orderinfo', $params);

                $btnClass = '';
                $btnOnClick = 'setLocation(\'' . $exportUrl . '\')';
            }
        }

        $logsData = array(
            'label'   => Mage::helper('avatax')->__('Export Logs for this order'),
            'onclick' => $btnOnClick,
            'class'   => $btnClass,
        );

        $html = $buttonBlock->setData($logsData)->toHtml();

        return $html;
    }
}
