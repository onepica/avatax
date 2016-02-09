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
 * Class OnePica_AvaTax16_AddressResolution_ResolveSingleAddressResponse
 */
class OnePica_AvaTax16_AddressResolution_ResolveSingleAddressResponse extends OnePica_AvaTax16_Document_Part
{
    /**
     * Has error
     *
     * @var bool
     */
    protected $_hasError = false;

    /**
     * Errors
     *
     * @var array
     */
    protected $_errors;

    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $_propertyComplexTypes = array(
        '_address' => array(
            'type' => 'OnePica_AvaTax16_Document_Part_Location_Address'
        ),
        '_coordinates' => array(
            'type' => 'OnePica_AvaTax16_Document_Part_Location_LatLong'
        ),
        '_taxAuthorities' => array(
            'type' => 'OnePica_AvaTax16_AddressResolution_TaxAuthority',
            'isArrayOf' => 'true'
        ),
    );

    /**
     * Address
     *
     * @var OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected $_address;

    /**
     * Coordinates
     *
     * @var OnePica_AvaTax16_Document_Part_Location_LatLong
     */
    protected $_coordinates;

    /**
     * Resolution Quality
     *
     * @var string
     */
    protected $_resolutionQuality;

    /**
     * Tax Authorities
     *
     * @var array
     */
    protected $_taxAuthorities;
}
