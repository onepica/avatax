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
     * Types of complex properties
     *
     * @var array
     */
    protected $_propertyComplexTypes = array(
        '_address' => array(
            'type' => 'OnePica_AvaTax16_Document_Part_Location_Address'
        ),
        '_latlong' => array(
            'type' => 'OnePica_AvaTax16_Document_Part_Location_LatLong'
        ),
        '_feedback' => array(
            'type' => 'OnePica_AvaTax16_Document_Part_Feedback'
        ),
    );

    /**
     * Tax Location Purpose
     * (Required)
     *
     * @var string
     */
    protected $_taxLocationPurpose;
    /**
     * Address
     *
     * @var OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected $_address;

    /**
     * Latitude and longitude
     *
     * @var OnePica_AvaTax16_Document_Part_Location_LatLong
     */
    protected $_latlong;

    /**
     * Location code
     * (Not currently supported)
     *
     * @var string
     */
    protected $_locationCode;

    /**
     * Ip Address
     * (Not currently supported)
     *
     * @var string
     */
    protected $_ipAddress;

    /**
     * Resolution Quality
     *
     * @var string
     */
    protected $_resolutionQuality;

    /**
     * Address Tax Payer Code
     * (Not currently supported)
     *
     * @var string
     */
    protected $_addressTaxPayerCode;

    /**
     * Address Buyer Type
     *
     * @var string
     */
    protected $_addressBuyerType;

    /**
     * Address Use Type
     *
     * @var string
     */
    protected $_addressUseType;

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
}
