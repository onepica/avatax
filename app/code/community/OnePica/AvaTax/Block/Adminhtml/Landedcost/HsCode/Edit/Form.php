<?php

/**
 * Class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Form
 */
class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     * @throws \Varien_Exception
     * @throws \Exception
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id'     => 'edit_form',
                'action' => $this->getUrl('*/*/hscodeSave', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('hs_code_form', array('legend' => $this->__('Item information')));

        $fieldset->addField(
            'hs_code', 'text', array(
                'name'     => 'hs_code',
                'label'    => $this->__('HS Code'),
                'class'    => 'required-entry',
                'required' => true,
                'note'  => 'You can find full list of HS codes at <a href="https://www.avalara.com/hs-codes/">Avalara website</a>',
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
