<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category  OnePica
 * @package   OnePica_AvaTax
 * @copyright Copyright (c) 2015 One Pica, Inc. (http://www.onepica.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class OnePica_AvaTax16_Document_Part_Location
 */
class OnePica_AvaTax16_Document_Part_Location extends OnePica_AvaTax16_Document_Part
{
    /**
     * Address
     *
     * @var OnePica_AvaTax16_Document_Part_Location_Address
     */
    private $_address;

    /**
     * latitude and longitude
     *
     * @var OnePica_AvaTax16_Document_Part_Location_LatLong
     */
    private $_latlong;

    /**
     * Location code
     * (Not currently supported)
     *
     * @var string
     */
    private $_locationCode;

    /**
     * Ip Address
     * (Not currently supported)
     *
     * @var string
     */
    private $_ipAddress;

    /**
     * Resolution Quality
     *
     * @var string
     */
    private $_resolutionQuality;

    /**
     * Address Tax Payer Code
     * (Not currently supported)
     *
     * @var string
     */
    private $_addressTaxPayerCode;

    /**
     * Address Entity Use Type
     * (Not currently supported)
     *
     * @var string
     */
    private $_addressEntityUseType;

    /**
     * Set Address
     *
     * @param OnePica_AvaTax16_Document_Part_Location_Address $value
     * @return $this
     */
    public function setAddress($value)
    {
        $this->_address = $value;
        return $this;
    }

    /**
     * Get Address
     *
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * Set Latlong
     *
     * @param OnePica_AvaTax16_Document_Part_Location_LatLong $value
     * @return $this
     */
    public function setLatlong($value)
    {
        $this->_latlong = $value;
        return $this;
    }

    /**
     * Get Latlong
     *
     * @return OnePica_AvaTax16_Document_Part_Location_LatLong
     */
    public function getLatlong()
    {
        return $this->_latlong;
    }

    /**
     * Set Location Code
     *
     * @param string $value
     * @return $this
     */
    public function setLocationCode($value)
    {
        $this->_locationCode = $value;
        return $this;
    }

    /**
     * Get Location Code
     *
     * @return string
     */
    public function getLocationCode()
    {
        return $this->_locationCode;
    }

    /**
     * Set Ip Address
     *
     * @param string $value
     * @return $this
     */
    public function setIpAddress($value)
    {
        $this->_ipAddress = $value;
        return $this;
    }

    /**
     * Get Ip Address
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->_ipAddress;
    }

    /**
     * Set Resolution Quality
     *
     * @param string $value
     * @return $this
     */
    public function setResolutionQuality($value)
    {
        $this->_resolutionQuality = $value;
        return $this;
    }

    /**
     * Get Resolution Quality
     *
     * @return string
     */
    public function getResolutionQuality()
    {
        return $this->_resolutionQuality;
    }

    /**
     * Set Address Tax Payer Code
     *
     * @param string $value
     * @return $this
     */
    public function setAddressTaxPayerCode($value)
    {
        $this->_addressTaxPayerCode = $value;
        return $this;
    }

    /**
     * Get Address Tax Payer Code
     *
     * @return string
     */
    public function getAddressTaxPayerCode()
    {
        return $this->_addressTaxPayerCode;
    }

    /**
     * Set Address Entity Use Type
     *
     * @param string $value
     * @return $this
     */
    public function setAddressEntityUseType($value)
    {
        $this->_addressEntityUseType = $value;
        return $this;
    }

    /**
     * Get Address Entity Use Type
     *
     * @return string
     */
    public function getAddressEntityUseType()
    {
        return $this->_addressEntityUseType;
    }
}
