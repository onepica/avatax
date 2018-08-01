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
        $formData = new \Varien_Object(array());
        if($model->getIsNew()) {
            $formData = new \Varien_Object(array(
                'mage_id' => $model->getMageCustomer()->getId(),
                'customer_code' => $model->getCustomerCode(),
                'name' => $model->getMageCustomer()->getName(),
                'email_address' => $model->getMageCustomer()->getEmail(),
                'is_new' => $model->getIsNew()
            ));
        } else {
            $formData = new \Varien_Object(array(
                'id' => $model->getAvaCustomer()->getId(),
                'mage_id' => $model->getMageCustomer()->getId(),
                'customer_code' => $model->getCustomerCode(),
                'name' => $model->getAvaCustomer()->getName(),
                'email_address' => $model->getAvaCustomer()->getEmailAddress(),
                'line1' => $model->getAvaCustomer()->getLine1(),
                'city' => $model->getAvaCustomer()->getCity(),
                'postal_code' => $model->getAvaCustomer()->getPostalCode(),
                'country' => $model->getAvaCustomer()->getCountry(),
                'region' => $model->getAvaCustomer()->getRegion(),
                'is_new' => $model->getIsNew()
            ));
        }
        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
        ));

        $this->setTitle($model->getIsNew()
            ? $this->_getHelper()->__('Register Customer in Avalara.')
            : $this->_getHelper()->__('Avalara Customer Information.')
        );

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => $model->getIsNew()
                ? $this->_getHelper()->__('Register Customer in Avalara.')
                : $this->_getHelper()->__('Avalara Customer Information.')
        ));

        $fieldset->addField('id', 'hidden',
            array(
                'name'      => 'id',
                'value'     => $formData->getId(),
                'no_span'   => true
            )
        );

        $fieldset->addField('mage_id', 'hidden',
            array(
                'name'      => 'mage_id',
                'value'     => $formData->getMageId(),
                'no_span'   => true
            )
        );

        $fieldset->addField('customer_code', 'hidden',
            array(
                'name'  => 'customer_code',
                'label' => $this->_getHelper()->__('Customer Code'),
                'class' => 'required-entry',
                'value' => $formData->getCustomerCode(),
                'readonly' => true,
                'required' => true,
            )
        );

        $fieldset->addField('customer_code_label', 'label',
            array(
                'name'  => 'customer_code_label',
                'label' => $this->_getHelper()->__('Customer Code'),
                'class' => 'required-entry',
                'value' => $formData->getCustomerCode(),
                'required' => true
            )
        );

        $fieldset->addField('name', 'text',
            array(
                'name'      => 'name',
                'label'     => $this->_getHelper()->__('Name'),
                'class'     => 'required-entry',
                'value'     => $formData->getName(),
                'required'  => true,
            )
        );

        $fieldset->addField('email_address', 'text',
            array(
                'name'      => 'email_address',
                'label'     => $this->_getHelper()->__('Email Address'),
                'class'     => 'required-entry',
                'value'     => $formData->getEmailAddress(),
                'required'  => true,
            )
        );

        $fieldset->addField('line1', 'text',
            array(
                'name'      => 'line1',
                'label'     => $this->_getHelper()->__('Address Line'),
                'class'     => 'required-entry',
                'value'     => $formData->getLine1(),
                'required'  => true,
            )
        );

        $fieldset->addField('city', 'text',
            array(
                'name'      => 'city',
                'label'     => $this->_getHelper()->__('City'),
                'class'     => 'required-entry',
                'value'     => $formData->getCity(),
                'required'  => true,
            )
        );

        $fieldset->addField('postal_code', 'text',
            array(
                'name'      => 'postal_code',
                'label'     => $this->_getHelper()->__('Postal Caode'),
                'class'     => 'required-entry',
                'value'     => $formData->getPostalCode(),
                'required'  => true,
            )
        );

        $countryCode = $formData->getCountry();
        $countryCode = ($countryCode) ? $countryCode : 'US';
        $countryList = Mage::getModel('directory/country')
            ->getResourceCollection()
            ->loadByStore()
            ->toOptionArray($this->_getHelper()->__('-- Please select --'));
        $fieldset->addField('country', 'select', array(
            'name'      => 'country',
            'label'     => $this->_getHelper()->__('Country'),
            'title'     => $this->_getHelper()->__('Country'),
            'required'  => true,
            'value'     => $countryCode,
            /*'disabled'  => true,*/
            'values'    => $countryList,
            'onchange'  => "AvaTaxCertCustomerForm.onCountryChanged();"
        ));

        $regionCode = $formData->getRegion();
        $regionList = $this->_getRegions($countryCode, $this->_getHelper()->__('-- Please select --'));
        $fieldset->addField('regions', 'select', array(
            'name'      => 'regions',
            'label'     => $this->_getHelper()->__('Region'),
            'title'     => $this->_getHelper()->__('Region'),
            'value'     => $regionCode,
            'values'    => $regionList,
            'onchange'  => "$('region').value = this.value"
        ))->setAfterElementHtml(
            "<input id='region' name='region' value='{$regionCode}' class='required-entry input-text required-entry' type='text' style=''/>"
            ."<script type='text/javascript'>
                var actionUrl ='{$this->getUrl('*/json/countryRegion')}';
                AvaTaxCertCustomerForm.init(actionUrl, 'country', 'regions', 'region');
              </script>"
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

    /**
     * @return array
     */
    protected function _getRegions($countryCode, $emptyLabel = '')
    {
        try {
            /** @var \Mage_Directory_Model_Region_Api $regionApiModel */
            $regionApiModel = Mage::getModel('directory/region_api');
            $regionApiItems = $regionApiModel->items($countryCode);

            $result = array();
            foreach ($regionApiItems as $item) {
                if(!$result) {
                    // push empty first
                    array_push($result, array('value' => null, 'label' => $emptyLabel));
                }

                array_push($result, array('value' => $item['code'], 'label' => $item['name']));
            }

            return $result;
        } catch (Exception $exception) {
            return array();
        }
    }
}
