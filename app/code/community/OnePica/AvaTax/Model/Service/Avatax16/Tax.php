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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Avatax16 service tax model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax16_Tax extends OnePica_AvaTax_Model_Service_Avatax16_Abstract
{
    /**
     * The document request data object
     *
     * @var OnePica_AvaTax16_Document_Request
     */
    protected $_request = null;

    /**
     * An array of line items
     *
     * @var array
     */
    protected $_lines = array();

    /**
     * Get the orgin address for the request
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected function _getOriginAddress($store = null)
    {
        $country = Mage::getStoreConfig('shipping/origin/country_id', $store);
        $zip = Mage::getStoreConfig('shipping/origin/postcode', $store);
        $regionId = Mage::getStoreConfig('shipping/origin/region_id', $store);
        $state = Mage::getModel('directory/region')->load($regionId)->getCode();
        $city = Mage::getStoreConfig('shipping/origin/city', $store);
        $street = Mage::getStoreConfig('shipping/origin/street', $store);
        $address = $this->_newAddress($street, '', $city, $state, $zip, $country);
        return $address;
    }

    /**
     * Get the shipping address for the request
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected function _getDestinationAddress($address)
    {
        $street1 = $address->getStreet(1);
        $street2 = $address->getStreet(2);
        $city = $address->getCity();
        $zip = $address->getPostcode();
        $state = Mage::getModel('directory/region')->load($address->getRegionId())->getCode();
        $country = $address->getCountry();
        $address = $this->_newAddress($street1, $street2, $city, $state, $zip, $country);
        return $address;
    }

    /**
     * Generic address maker
     *
     * @param string $line1
     * @param string $line2
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $country
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected function _newAddress($line1, $line2, $city, $state, $zip, $country = 'USA')
    {
        $address = new OnePica_AvaTax16_Document_Part_Location_Address();
        $address->setLine1($line1);
        $address->setLine2($line2);
        $address->setCity($city);
        $address->setState($state);
        $address->setZipcode($zip);
        $address->setCountry($country);
        return $address;
    }

    /**
     * Get default locations
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address
     * @return array
     */
    protected function _getHeaderDefaultLocations($address)
    {
        $quote = $address->getQuote();

        $locationFrom = new OnePica_AvaTax16_Document_Part_Location();
        $locationFrom->setTaxLocationPurpose(self::TAX_LOCATION_PURPOSE_SHIP_FROM);
        $locationFrom->setAddress($this->_getOriginAddress($quote->getStoreId()));

        $locationTo = new OnePica_AvaTax16_Document_Part_Location();
        $locationTo->setTaxLocationPurpose(self::TAX_LOCATION_PURPOSE_SHIP_TO);
        $locationTo->setAddress($this->_getDestinationAddress($address));

        $defaultLocations = array(
            self::TAX_LOCATION_PURPOSE_SHIP_FROM => $locationFrom,
            self::TAX_LOCATION_PURPOSE_SHIP_TO   => $locationTo
        );

        return $defaultLocations;
    }
}
