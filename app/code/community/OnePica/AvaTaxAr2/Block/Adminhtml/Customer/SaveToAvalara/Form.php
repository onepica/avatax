<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * The AvaTaxAr2 Adminhtml Customer Tab class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Block_Adminhtml_Customer_SaveToAvalara_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * OnePica_AvaTaxAr2_Block_Adminhtml_Customer_SaveToAvalara_Form constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('saveCustomerToAvalara');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model  = Mage::registry('save_customer_to_avalara');
        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $this->setTitle($model->getIsNew()
            ? $this->_getHelper()->__('Register Customer in Avalara')
            : $this->_getHelper()->__('Update Customer Information in Avalara')
        );

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => $model->getIsNew()
                ? $this->_getHelper()->__('Register Customer in Avalara')
                : $this->_getHelper()->__('Update Customer Information in Avalara')
        ));

        $fieldset->addField('id', 'hidden',
            array(
                'name'      => 'id',
                'value'     => $model->getAvaCustomer() ? $model->getAvaCustomer()->getId() : '',
                'no_span'   => true
            )
        );

        $fieldset->addField('mage_id', 'hidden',
            array(
                'name'      => 'mage_id',
                'value'     => $model->getMageCustomer()->getId(),
                'no_span'   => true
            )
        );

        $fieldset->addField('customer_code', 'text',
            array(
                'name'  => 'customer_code',
                'label' => $this->_getHelper()->__('Customer Code'),
                'class' => 'required-entry',
                'value' => $model->getCustomerCode(),
                'required' => true,
            )
        );

        $fieldset->addField('name', 'text',
            array(
                'name'      => 'name',
                'label'     => $this->_getHelper()->__('Name'),
                'class'     => 'required-entry',
                'value'     => $model->getMageCustomer()->getName(),
                'required'  => true,
            )
        );

        $fieldset->addField('customer_email', 'text',
            array(
                'name'      => 'customer_email',
                'label'     => $this->_getHelper()->__('Email'),
                'class'     => 'required-entry',
                'value'     => $model->getMageCustomer()->getEmail(),
                'required'  => true,
            )
        );

        $fieldset->addField('line1', 'text',
            array(
                'name'      => 'line1',
                'label'     => $this->_getHelper()->__('Address Line'),
                'class'     => 'required-entry',
                'value'     => ($model->getAvaCustomer()) ? $model->getAvaCustomer()->getLine1() : '',
                'required'  => true,
            )
        );

        $fieldset->addField('city', 'text',
            array(
                'name'      => 'city',
                'label'     => $this->_getHelper()->__('City'),
                'class'     => 'required-entry',
                'value'     => ($model->getAvaCustomer()) ? $model->getAvaCustomer()->getCity() : '',
                'required'  => true,
            )
        );

        $fieldset->addField('postal_code', 'text',
            array(
                'name'      => 'postal_code',
                'label'     => $this->_getHelper()->__('Postal Caode'),
                'class'     => 'required-entry',
                'value'     => ($model->getAvaCustomer()) ? $model->getAvaCustomer()->getPostalCode() : '',
                'required'  => true,
            )
        );

        $fieldset->addField('country', 'text',
            array(
                'name'      => 'country',
                'label'     => $this->_getHelper()->__('Country'),
                'class'     => 'required-entry',
                'value'     => ($model->getAvaCustomer()) ? $model->getAvaCustomer()->getCountry() : '',
                'required'  => true,
            )
        );

        $fieldset->addField('region', 'text',
            array(
                'name'      => 'region',
                'label'     => $this->_getHelper()->__('Region'),
                'class'     => 'required-entry',
                'value'     => ($model->getAvaCustomer()) ? $model->getAvaCustomer()->getRegion() : '',
                'required'  => true,
            )
        );

        $saveUrl = $model->getIsNew()
                    ? $this->getUrl('*/*/registerAvalaraCustomer', array('need_invitation' => $model->getNeedInvitation()))
                    : $this->getUrl('*/*/updateAvalaraCustomer');
        $form->setAction($saveUrl);
        $form->setUseContainer(true);
        $this->setForm($form);


        return parent::_prepareForm();
    }

    /**
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper()
    {
        return Mage::helper('adminhtml');
    }
}
