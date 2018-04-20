<?php

class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('hscode_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('HS Code'));
    }

    /**
     * @return \Mage_Core_Block_Abstract
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_section', array(
                'label'   => $this->__('HS code information'),
                'title'   => $this->__('HS code information'),
                'content' => $this->getLayout()->createBlock('avatax/adminhtml_landedcost_hsCode_edit_tab_form')
                                  ->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}
