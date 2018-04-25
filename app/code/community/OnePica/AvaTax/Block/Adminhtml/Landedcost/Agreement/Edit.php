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
 * Avatax admin Agreement edit
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_Landedcost_Agreement_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * OnePica_AvaTax_Block_Adminhtml_Landedcost_Agreement_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'avatax';
        $this->_controller = 'adminhtml_landedcost_agreement';

        $this->_updateButton('save', 'label', $this->__('Save Agreement'));
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
        return $this->getUrl('*/*/agreementSave', array('_current' => true, 'back' => 'agreementEdit'));
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/agreement');
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $agreementData = Mage::registry('agreement_data');

        if ($agreementData && $agreementData->getId()) {
            return $this->__("Edit Item '%s'", $this->escapeHtml($agreementData->getAvalaraAgereementCode()));
        } else {
            return $this->__('Add Item');
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            '*/*/agreementDelete', array(
                $this->_objectId              => $this->getRequest()->getParam($this->_objectId),
                Mage_Core_Model_Url::FORM_KEY => $this->getFormKey()
            )
        );
    }
}
