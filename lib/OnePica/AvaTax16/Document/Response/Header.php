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
 * Class OnePica_AvaTax16_Document_Response_Header
 */
class OnePica_AvaTax16_Document_Response_Header extends OnePica_AvaTax16_Document_Part
{
    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $_propertyComplexTypes = array(
        '_defaultLocations' => array(
            'type' => 'OnePica_AvaTax16_Document_Part_Location',
            'isArrayOf' => 'true'
        ),
    );

    /**
     * Account Id
     * (Required)
     *
     * @var string
     */
    protected $_accountId;

    /**
     * Company Code
     * (Required)
     *
     * @var string
     */
    protected $_companyCode;

    /**
     * Transaction Type
     * (Required)
     *
     * @var string
     */
    protected $_transactionType;

    /**
     * Document Code
     * (Required)
     *
     * @var string
     */
    protected $_documentCode;

    /**
     * Customer Code
     * (Required)
     *
     * @var string
     */
    protected $_customerCode;

    /**
     * Vendor Code
     * (Required)
     *
     * @var string
     */
    protected $_vendorCode;

    /**
     * Transaction Date
     * (Required)
     *
     * @var string
     */
    protected $_transactionDate;

    /**
     * Currency
     * (Not currently supported)
     *
     * @var string
     */
    protected $_currency;

    /**
     * Total Tax Override Amount
     * (Not currently supported)
     *
     * @var float
     */
    protected $_totalTaxOverrideAmount;

    /**
     * Tax Calculation Date
     *
     * @var string
     */
    protected $_taxCalculationDate;

    /**
     * Default Avalara Goods And Services Modifier Type
     * (Not currently supported)
     *
     * @var string
     */
    protected $_defaultAvalaraGoodsAndServicesModifierType;

    /**
     * Default locations
     * (Required)
     *
     * @var OnePica_AvaTax16_Document_Part_Location[]
     */
    protected $_defaultLocations;

    /**
     * Default Tax Payer Code
     * (Not currently supported)
     *
     * @var string
     */
    protected $_defaultTaxPayerCode;

    /**
     * Default Buyer Type
     *
     * @var string
     */
    protected $_defaultBuyerType;

    /**
     * Default Use Type
     *
     * @var string
     */
    protected $_defaultUseType;

    /**
     * Purchase Order Number
     *
     * @var string
     */
    protected $_purchaseOrderNumber;

    /**
     * Metadata
     *
     * @var array
     */
    protected $_metadata;

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
