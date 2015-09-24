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
 * Class OnePica_AvaTax16_Document_Part_Location_Address
 */
class OnePica_AvaTax16_Document_Part_Location_Address extends OnePica_AvaTax16_Document_Part
{
    /**
     * Address Code
     * (Required)
     *
     * @var string
     */
    protected $_addressCode;

    /**
     * Line 1
     * (Required)
     *
     * @var string
     */
    protected $_line1;

    /**
     * Line 2
     *
     * @var string
     */
    protected $_line2;

    /**
     * Line3
     *
     * @var string
     */
    protected $_line3;

    /**
     * City
     *
     * @var string
     */
    protected $_city;

    /**
     * Region
     *
     * @var string
     */
    protected $_region;

    /**
     * Country
     *
     * @var string
     */
    protected $_country;

    /**
     * Postal Code
     *
     * @var string
     */
    protected $_postalCode;

    /**
     * Latitude
     *
     * @var string
     */
    protected $_latitude;

    /**
     * Longitude
     *
     * @var string
     */
    protected $_longitude;

    /**
     * Tax Region Id
     *
     * @var int
     */
    protected $_taxRegionId;
}
