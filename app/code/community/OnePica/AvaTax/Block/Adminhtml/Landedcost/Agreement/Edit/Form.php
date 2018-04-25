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
 * Avatax admin Agreement edit form
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_Landedcost_Agreement_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
                'action' => $this->getUrl('*/*/agreementSave', array('id' => $this->getRequest()->getParam('id'),)),
                'method' => 'post',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('agreement_form', array('legend' => $this->__('Item information')));

        $fieldset->addField(
            'avalara_agreement_code', 'text', array(
                'name'     => 'avalara_agreement_code',
                'label'    => $this->__('Avalara Agreement code'),
                'class'    => 'required-entry',
                'required' => true
            )
        );

        $fieldset->addField(
            'description', 'text', array(
                'name'     => 'description',
                'label'    => $this->__('Description'),
                'class'    => 'required-entry',
                'required' => false,
            )
        );

        /** @var \Mage_Directory_Model_Resource_Country_Collection $countryList */
        $countryCollection = Mage::getModel('directory/country')->getResourceCollection()->loadByStore();

        $countryList = $countryCollection->toOptionArray(' ');

        $fieldset->addField(
            'country_list', 'multiselect', array(
                'name'     => 'country_list[]',
                'label'    => $this->__('Country List'),
                'title'    => $this->__('Country List'),
                'required' => true,
                'values'   => $countryList,
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getAgreementData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getAgreementData());
            Mage::getSingleton('adminhtml/session')->setAgreementData(null);
        } elseif (Mage::registry('agreement_data')) {
            $form->setValues(Mage::registry('agreement_data')->getData());
        }

        return parent::_prepareForm();
    }
}
