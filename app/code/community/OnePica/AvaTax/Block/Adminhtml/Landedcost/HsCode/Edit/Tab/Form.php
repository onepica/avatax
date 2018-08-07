<?php

class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('hs_code_form', array('legend' => $this->__('Item information')));

        $fieldset->addField(
            'hs_code', 'text', array(
                'name'     => 'hs_code',
                'label'    => $this->__('HS Code Group Name'),
                'class'    => 'required-entry',
                'required' => true,
                'note'     => 'You can find full list of HS Codes at <a href="https://www.avalara.com/hs-codes/">Avalara website</a>',
            )
        );

        $fieldset->addField(
            'description', 'text', array(
                'name'     => 'description',
                'label'    => $this->__('Description'),
                'required' => true,
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getHsCodeData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getHsCodeData());
            Mage::getSingleton('adminhtml/session')->setHsCodeData(null);
        } elseif (Mage::registry('hsCode_data')) {
            $form->setValues(Mage::registry('hsCode_data')->getData());
        }

        return parent::_prepareForm();
    }
} 
