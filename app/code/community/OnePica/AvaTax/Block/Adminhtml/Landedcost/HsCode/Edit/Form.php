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

        return parent::_prepareForm();
    }
}
