<?php

/**
 * Class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit
 */
class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'avatax';
        $this->_controller = 'adminhtml_landedcost_hsCode';

        $this->_updateButton('save', 'label', $this->__('Save HS Code Group'));
        $this->_updateButton('delete', 'label', $this->__('Delete Item'));
        $this->_addButton(
            'save_and_edit_button',
            array(
                'label'   => Mage::helper('widget')->__('Save and Continue Edit'),
                'class'   => 'save',
                'onclick' => 'editForm.submit(\'' . $this->getSaveAndContinueUrl() . '\');'
            ),
            100
        );
    }

    /**
     * Return save and continue url for edit form
     *
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/hscodeSave', array('_current' => true, 'back' => 'hscodeEdit'));
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/hscode');
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('hsCode_data') && Mage::registry('hsCode_data')->getId()) {
            return $this->__("Edit HS Code Group '%s'", $this->escapeHtml(Mage::registry('hsCode_data')->getHsCode()));
        } else {
            return $this->__('Add HS Code Group');
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            '*/*/hscodeDelete', array(
                $this->_objectId              => $this->getRequest()->getParam($this->_objectId),
                Mage_Core_Model_Url::FORM_KEY => $this->getFormKey()
            )
        );
    }
}
