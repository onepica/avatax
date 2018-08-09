<?php

/**
 * Class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Tab_Countries_Edit_Form
 */
class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Tab_Countries_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
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
                'action' => $this->getUrl(
                    '*/*/hscodecountriesSave', array(
                        'id'         => $this->getRequest()->getParam('id'),
                        'hs_code_id' => $this->getRequest()->getParam('hs_code_id')
                    )
                ),
                'method' => 'post',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('hs_code_form', array('legend' => $this->__('Item information')));

        $fieldset->addField(
            'hs_full_code', 'text', array(
                'name'     => 'hs_full_code',
                'label'    => $this->__('HS Code'),
                'class'    => 'required-entry',
                'required' => true,
                'note'     => 'You can find full list of HS Codes at <a href="https://www.avalara.com/hs-codes/">Avalara website</a>',
            )
        );

        /** @var \Mage_Directory_Model_Resource_Country_Collection $countryList */
        $countryCollection = Mage::getModel('directory/country')->getResourceCollection()->loadByStore();

        $countryList = $countryCollection->toOptionArray(' ');

        $fieldset->addField(
            'country_codes', 'multiselect', array(
                'name'     => 'country_codes[]',
                'label'    => $this->__('Country Codes'),
                'title'    => $this->__('Country Codes'),
                'required' => true,
                'values'   => $countryList,
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getHsCodeCountriesData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getHsCodeCountriesData());
            Mage::getSingleton('adminhtml/session')->setHsCodeCountriesData(null);
        } elseif (Mage::registry('hs_code_countries_data')) {
            $form->setValues(Mage::registry('hs_code_countries_data')->getData());
        }

        return parent::_prepareForm();
    }
}
