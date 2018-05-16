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
 * HS code for country model
 *
 * @method int getId()
 * @method $this setId(int $id)
 * @method int getHsId()
 * @method $this setHsId(int $hsId)
 * @method string getHsFullCode()
 * @method $this setHsFullCode(string $hsCode)
 * @method string getCountryCodes()
 * @method $this setCountryCodes(string $countryCodes)
 * @method OnePica_AvaTax_Model_Records_Mysql4_HsCodeCountry getResource()
 * @method OnePica_AvaTax_Model_Records_Mysql4_HsCodeCountry_Collection getCollection()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_HsCodeCountry extends Mage_Core_Model_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('avatax_records/hsCodeCountry');
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     * @throws \Varien_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $this->_checkCountryCodes();

        return $this;
    }

    /**
     * Check if country is unique for this HS code
     *
     * @return $this
     * @throws \Varien_Exception
     */
    protected function _checkCountryCodes()
    {
        $countryCodesArray = explode(',', $this->getCountryCodes());
        $needReset = false;

        foreach ($countryCodesArray as $key => $countryCode) {
            $hsCodeForCountry = $this->_getCodeForSameCountry($countryCode);

            if ($hsCodeForCountry->getId()) {
                $message = sprintf(
                    'Country "%s" already has a Full Code "%s" assigned. "%s" will be omitted in the list for "%s" code.',
                    $countryCode, $hsCodeForCountry->getHsFullCode(), $countryCode, $this->getHsFullCode()
                );
                Mage::getSingleton('core/session')->addError($message);

                unset($countryCodesArray[$key]);
                $needReset = true;
            }
        }

        if ($needReset) {
            $this->setCountryCodes(implode(',', $countryCodesArray));
        }

        return $this;
    }

    /**
     * Get HS Code from country code
     *
     * @param string $countryCode
     * @return $this
     */
    protected function _getCodeForSameCountry($countryCode)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('hs_id', array("eq" => $this->getHsId()));
        $collection->addFieldToFilter('id', array("neq" => $this->getId()));
        $collection->addFieldToFilter('country_codes', array("regexp" => $countryCode));

        return $collection->getFirstItem();
    }
}
