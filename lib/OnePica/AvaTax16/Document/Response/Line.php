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
 * Class OnePica_AvaTax16_Document_Response_Line
 */
class OnePica_AvaTax16_Document_Response_Line extends OnePica_AvaTax16_Document_Part
{
    /**
     * Required properties
     *
     * @var array
     */
    protected $_requiredProperties = array('_lineCode', '_lineAmount');

    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $_propertyComplexTypes = array(
        '_locations' => array(
            'type' => 'OnePica_AvaTax16_Document_Part_Location',
            'isArrayOf' => 'true'
        ),
        '_calculatedTax' => array(
            'type' => 'OnePica_AvaTax16_Document_Response_Line_CalculatedTax',
        ),
    );

    /**
     * Line Code
     * (Required)
     *
     * @var string
     */
    protected $_lineCode;

    /**
     * Item code
     *
     * @var string
     */
    protected $_itemCode;

    /**
     * Avalara Goods And Services Type
     *
     * @var string
     */
    protected $_avalaraGoodsAndServicesType;

    /**
     * Avalara Goods And Services Modifier Type
     *
     * @var string
     */
    protected $_avalaraGoodsAndServicesModifierType;

    /**
     * Number Of Items
     *
     * @var float
     */
    protected $_numberOfItems;

    /**
     * Line Amount
     *
     * @var float
     */
    protected $_lineAmount;

    /**
     * Item Description
     *
     * @var string
     */
    protected $_itemDescription;

    /**
     * Unit Of Measure
     * (Not currently supported)
     *
     * @var string
     */
    protected $_unitOfMeasure;

    /**
     * Locations
     *
     * @var OnePica_AvaTax16_Document_Part_Location[]
     */
    protected $_locations;

    /**
     * Tax Payer Code
     * (Not currently supported)
     *
     * @var string
     */
    protected $_taxPayerCode;

    /**
     * Buyer Type
     *
     * @var string
     */
    protected $_buyerType;

    /**
     * Use Type
     *
     * @var string
     */
    protected $_useType;

    /**
     * Line Attributes
     * (Not currently supported)
     *
     * @var string
     */
    protected $_lineAttributes;

    /**
     * Tax Override Amount
     * (Not currently supported)
     *
     * @var float
     */
    protected $_taxOverrideAmount;

    /**
     * Tax Included
     * (Not currently supported)
     *
     * @var bool
     */
    protected $_taxIncluded;

    /**
     * Tax Included
     *
     * @var array
     */
    protected $_metadata;

    /**
     * Calculated Tax
     * (Only response)
     *
     * @var OnePica_AvaTax16_Document_Response_Line_CalculatedTax
     */
    protected $_calculatedTax;

    /**
     * Set Metadata
     *
     * @param array|StdClass $value
     * @return $this
     */
    public function setMetadata($value)
    {
        if ($value instanceof StdClass) {
            // convert object data to array
            // it is used during filling data from response
            $this->_metadata = (array) $value;
        } else {
            $this->_metadata = $value;
        }
    }
}
