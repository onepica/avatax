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
 * Class OnePica_AvaTax16_Document_Part_Header
 */
class OnePica_AvaTax16_Document_Part_Header extends OnePica_AvaTax16_Document_Part
{
    /**
     * Account Id
     * (Required)
     *
     * @var string
     */
    private $_accountId;

    /**
     * Company Code
     * (Required)
     *
     * @var string
     */
    private $_companyCode;

    /**
     * Transaction Type
     * (Required)
     *
     * @var string
     */
    private $_transactionType;

    /**
     * Document Code
     * (Required)
     *
     * @var string
     */
    private $_documentCode;

    /**
     * Customer Code
     * (Required)
     *
     * @var string
     */
    private $_customerCode;

    /**
     * Vendor Code
     * (Required)
     *
     * @var string
     */
    private $_vendorCode;

    /**
     * Transaction Date
     * (Required)
     *
     * @var string
     */
    private $_transactionDate;

    /**
     * Currency
     * (Not currently supported)
     *
     * @var string
     */
    private $_currency;

    /**
     * Total Tax Override Amount
     * (Not currently supported)
     *
     * @var float
     */
    private $_totalTaxOverrideAmount;

    /**
     * Tax Calculation Date
     *
     * @var string
     */
    private $_taxCalculationDate;

    /**
     * Default Avalara Goods And Services Modifier Type
     * (Not currently supported)
     *
     * @var string
     */
    private $_defaultAvalaraGoodsAndServicesModifierType;

    /**
     * Default locations
     * (Required)
     *
     * @var OnePica_AvaTax16_Document_Part_Locations
     */
    private $_defaultLocations;

    /**
     * Default Tax Payer Code
     * (Not currently supported)
     *
     * @var string
     */
    private $_defaultTaxPayerCode;

    /**
     * Default Tax Payer Code
     * (Not currently supported)
     *
     * @var string
     */
    private $_defaultEntityUseType;

    /**
     * Purchase Order Number
     *
     * @var string
     */
    private $_purchaseOrderNumber;

    /**
     * Metadata
     *
     * @var array
     */
    private $_metadata;

    /**
     * Set Account Id
     *
     * @param string $value
     * @return $this
     */
    public function setAccountId($value)
    {
        $this->_accountId = $value;
        return $this;
    }

    /**
     * Get Account Id
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->_accountId;
    }

    /**
     * Set Company Code
     *
     * @param string $value
     * @return $this
     */
    public function setCompanyCode($value)
    {
        $this->_companyCode = $value;
        return $this;
    }

    /**
     * Get Company Code
     *
     * @return string
     */
    public function getCompanyCode()
    {
        return $this->_companyCode;
    }

    /**
     * Set Transaction Type
     *
     * @param string $value
     * @return $this
     */
    public function setTransactionType($value)
    {
        $this->_transactionType = $value;
        return $this;
    }

    /**
     * Get Transaction Type
     *
     * @return string
     */
    public function getTransactionType()
    {
        return $this->_transactionType;
    }

    /**
     * Set Document Code
     *
     * @param string $value
     * @return $this
     */
    public function setDocumentCode($value)
    {
        $this->_documentCode = $value;
        return $this;
    }

    /**
     * Get Document Code
     *
     * @return string
     */
    public function getDocumentCode()
    {
        return $this->_documentCode;
    }

    /**
     * Set Customer Code
     *
     * @param string $value
     * @return $this
     */
    public function setCustomerCode($value)
    {
        $this->_customerCode = $value;
        return $this;
    }

    /**
     * Get Customer Code
     *
     * @return string
     */
    public function getCustomerCode()
    {
        return $this->_customerCode;
    }

    /**
     * Set Vendor Code
     *
     * @param string $value
     * @return $this
     */
    public function setVendorCode($value)
    {
        $this->_vendorCode = $value;
        return $this;
    }

    /**
     * Get Vendor Code
     *
     * @return string
     */
    public function getVendorCode()
    {
        return $this->_vendorCode;
    }

    /**
     * Set Transaction Date
     *
     * @param string $value
     * @return $this
     */
    public function setTransactionDate($value)
    {
        $this->_transactionDate = $value;
        return $this;
    }

    /**
     * Get Transaction Date
     *
     * @return string
     */
    public function getTransactionDate()
    {
        return $this->_transactionDate;
    }

    /**
     * Set Currency
     *
     * @param string $value
     * @return $this
     */
    public function setCurrency ($value)
    {
        $this->_currency = $value;
        return $this;
    }

    /**
     * Get Currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * Set Total Tax Override Amount
     *
     * @param float $value
     * @return $this
     */
    public function setTotalTaxOverrideAmount($value)
    {
        $this->_totalTaxOverrideAmount = $value;
        return $this;
    }

    /**
     * Get Total Tax Override Amount
     *
     * @return float
     */
    public function getTotalTaxOverrideAmount()
    {
        return $this->_totalTaxOverrideAmount;
    }

    /**
     * Set Tax Calculation Date
     *
     * @param string $value
     * @return $this
     */
    public function setTaxCalculationDate($value)
    {
        $this->_taxCalculationDate = $value;
        return $this;
    }

    /**
     * Get Tax Calculation Date
     *
     * @return string
     */
    public function getTaxCalculationDate()
    {
        return $this->_taxCalculationDate;
    }

    /**
     * Set Default Avalara Goods And Services Modifier Type
     *
     * @param string $value
     * @return $this
     */
    public function setDefaultAvalaraGoodsAndServicesModifierType($value)
    {
        $this->_defaultAvalaraGoodsAndServicesModifierType = $value;
        return $this;
    }

    /**
     * Get Default Avalara Goods And Services Modifier Type
     *
     * @return string
     */
    public function getDefaultAvalaraGoodsAndServicesModifierType()
    {
        return $this->_defaultAvalaraGoodsAndServicesModifierType;
    }

    /**
     * Get Default Avalara Goods And Services Modifier Type
     *
     * @return OnePica_AvaTax16_Document_Part_Locations
     */
    public function getDefaultLocations()
    {
        return $this->_defaultLocations;
    }

    /**
     * Set Default Tax Payer Code
     *
     * @param string $value
     * @return $this
     */
    public function setDefaultTaxPayerCode($value)
    {
        $this->_defaultTaxPayerCode = $value;
        return $this;
    }

    /**
     * Get Default Tax Payer Code
     *
     * @return string
     */
    public function getDefaultTaxPayerCode()
    {
        return $this->_defaultTaxPayerCode;
    }

    /**
     * Set Default Entity Use Type
     *
     * @param string $value
     * @return $this
     */
    public function setDefaultEntityUseType($value)
    {
        $this->_defaultEntityUseType = $value;
        return $this;
    }

    /**
     * Get Default Entity Use Type
     *
     * @return string
     */
    public function getDefaultEntityUseType()
    {
        return $this->_defaultEntityUseType;
    }

    /**
     * Set Purchase Order Number
     *
     * @param string $value
     * @return $this
     */
    public function setPurchaseOrderNumber($value)
    {
        $this->_purchaseOrderNumber = $value;
        return $this;
    }

    /**
     * Get Purchase Order Number
     *
     * @return string
     */
    public function getPurchaseOrderNumber()
    {
        return $this->_purchaseOrderNumber;
    }

    /**
     * Set Metadata
     *
     * @param array $value
     * @return $this
     */
    public function setMetadata($value)
    {
        $this->_metadata = $value;
        return $this;
    }

    /**
     * Get Metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }
}
