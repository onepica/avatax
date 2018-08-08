<?php

/**
 * Class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Tab_Grid
 */
class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Tab_Countries
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'avatax';
        $this->_controller = 'adminhtml_landedcost_hsCode_edit_tab_countries';
        $this->_headerText = $this->__('HS code for countries');

        parent::__construct();
        $this->_updateButton('add', 'label', $this->__('Add HS Code'));
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/hscodecountriesNew', array('hs_code_id' => $this->getRequest()->getParam('id')));
    }

    /**
     * Get header HTML
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        return '';
    }
} 
