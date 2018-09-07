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
 * @copyright  Copyright (c) 2016 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class OnePica_AvaTax_Helper_LandedCost
 */
class OnePica_AvaTax_Helper_LandedCost extends Mage_Core_Helper_Abstract
{
    /**
     *  Landed Cost Product Group Tab
     */
    const AVATAX_PRODUCT_GROUP_LANDED_COST = 'AvaTax Customs Duty';

    /**
     *  HS Code product attribute
     */
    const AVATAX_PRODUCT_LANDED_COST_ATTR_HSCODE = 'avatax_lc_hs_code';

    /**
     *  HS Code product attribute label
     */
    const AVATAX_PRODUCT_LANDED_COST_ATTR_PARAMETER_LABEL = 'Parameter';

    /**
     *  Product Unit of Measurement
     */
    const AVATAX_PRODUCT_LANDED_COST_ATTR_PARAMETER = 'avatax_lc_parameter';

    /**
     *  Landed Cost product agreement
     */
    const AVATAX_PRODUCT_LANDED_COST_AGREEMENT = 'avatax_lc_agreement';

    /**
     *  Landed Cost tax type for tax detail
     */
    const AVATAX_LANDED_COST_TAX_TYPE = 'LandedCost';

    /**
     * Xml path to landed cost enabled
     */
    const XML_PATH_TO_AVATAX_LANDED_COST_ENABLED = 'tax/avatax_landed_cost/landed_cost_enabled';

    /**
     * Xml path to landed cost DDP countries
     */
    const XML_PATH_TO_AVATAX_LANDED_COST_DDP_COUNTRIES = 'tax/avatax_landed_cost/landed_cost_ddp_countries';

    /**
     * Xml path to landed cost DAP countries
     */
    const XML_PATH_TO_AVATAX_LANDED_COST_DAP_COUNTRIES = 'tax/avatax_landed_cost/landed_cost_dap_countries';

    /**
     * Xml path to landed cost DAP countries
     */
    const XML_PATH_TO_AVATAX_LANDED_COST_DEFAULT_UNITS_OF_MEASUREMENT = 'tax/avatax_landed_cost/landed_cost_units_of_measurement';

    /**
     * Xml path to landed cost shipping insurance sku
     */
    const XML_PATH_TO_AVATAX_LANDED_COST_SHIPPING_INSURANCE_SKU = 'tax/avatax_landed_cost/shipping_insurance_sku';

    /**
     * Xml path to landed cost shipping insurance tax code
     */
    const XML_PATH_TO_AVATAX_LANDED_COST_SHIPPING_INSURANCE_TAX_CODE = 'tax/avatax_landed_cost/shipping_insurance_tax_code';

    /**
     *  Seller is an importer for customer
     */
    const AVATAX_CUSTOMER_LANDED_COST_ATTR_SELLER_IS_AN_IMPORTER = 'avatax_lc_seller_is_importer';

    /**
     *  Seller is an importer for customer
     */
    const XML_PATH_TO_AVATAX_LANDED_COST_EXPRESS_SHIPPING = 'tax/avatax_landed_cost/landed_cost_express_shipping';

    /** Landed cost tax subtypes */
    const XML_PATH_TO_AVATAX_LANDED_COST_TAX_SUBTYPES = 'tax/avatax_landed_cost/landed_cost_tax_subtypes';

    /** Landed param type Mass */
    const XML_PATH_TO_AVATAX_LANDED_COST_PARAM_TYPE_MASS = 'tax/avatax_landed_cost/param_type_mass';

    /**
     * Default Unit Of Measurement
     *
     * @var null
     */
    private $defaultUnitOfMeasurement = null;

    /**
     * Get if Landed Cost is Enabled
     *
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     */
    public function isLandedCostEnabled($store)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_TO_AVATAX_LANDED_COST_ENABLED, $store);
    }

    /**
     * Get Landed Cost DDP countries
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return array
     */
    public function getLandedCostDDPCountries($storeId = null)
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_TO_AVATAX_LANDED_COST_DDP_COUNTRIES, $storeId));
    }

    /**
     * Get Landed Cost DAP countries
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return array
     */
    public function getLandedCostDAPCountries($storeId = null)
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_TO_AVATAX_LANDED_COST_DAP_COUNTRIES, $storeId));
    }

    /**
     * Is Landed Cost Transaction
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @param string                    $destinationCountry
     * @return null|string
     */
    public function getLandedCostMode($destinationCountry, $storeId = null)
    {
        return $this->isSellerImporterOfRecord($storeId, $destinationCountry);
    }

    /**
     * @param null $storeId
     * @param $destinationCountry
     * @return bool
     */
    public function isSellerImporterOfRecord($storeId = null, $destinationCountry)
    {
        $result = false;
        $originCountryCode = Mage::getStoreConfig('shipping/origin/country_id', $storeId);
        if ($this->isLandedCostEnabled($storeId) && $destinationCountry != $originCountryCode) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param OnePica_AvaTax_Model_Sales_Quote_Address $object
     * @return true|false|null
     */
    public function isSellerImporterOfRecordForTheCustomer($object)
    {
        $result = null;
        $customerId = $object->getCustomerId();
        if ($customerId) {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = Mage::getModel('customer/customer');
            $customer->load($customerId);
            $result = isset($customer) ? $customer->getData(
                self::AVATAX_CUSTOMER_LANDED_COST_ATTR_SELLER_IS_AN_IMPORTER
            ) : $result;
            switch ($result) {
                case OnePica_AvaTax_Model_Entity_Attribute_Source_Boolean::VALUE_YES:
                    $result = true;
                    break;
                case OnePica_AvaTax_Model_Entity_Attribute_Source_Boolean::VALUE_NO:
                    $result = false;
                    break;
                default:
                    $result = null;
                    break;
            }
        }

        return $result;
    }

    /**
     * @param OnePica_AvaTax_Model_Sales_Quote_Address $quoteAddress
     * @return true|false|null
     * @throws \Varien_Exception
     */
    public function isSellerImporterOfRecordForQuote($quoteAddress)
    {
        $result = null;

        if (!$quoteAddress instanceof Mage_Sales_Model_Quote_Address) {
            return $result;
        }

        $quoteValue = $quoteAddress->getQuote()->getCustomerAvataxLcSellerIsImporter();

        switch ($quoteValue) {
            case OnePica_AvaTax_Model_Entity_Attribute_Source_Boolean::VALUE_YES:
                $result = true;
                break;
            case OnePica_AvaTax_Model_Entity_Attribute_Source_Boolean::VALUE_NO:
                $result = false;
                break;
            default:
                $result = null;
                break;
        }

        return $result;
    }

    /**
     * Get Product HTS Code
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @param string                         $countryCode
     * @return string
     * @throws \Varien_Exception
     */
    public function getProductHTSCode($product, $countryCode)
    {
        $product = is_int($product) ? Mage::getModel('catalog/product')->load($product) : $product;
        $hsCode = $product->getData(self::AVATAX_PRODUCT_LANDED_COST_ATTR_HSCODE);

        /** @var OnePica_AvaTax_Model_Records_HsCode $hsCode */
        $model = Mage::getModel('avatax_records/hsCode')->load($hsCode, 'hs_code');

        /** @var OnePica_AvaTax_Model_Records_HsCodeCountry $code */
        $code = $model->getCodeForCountry($countryCode);

        return $code->getId() > 0 ? $code->getHsFullCode() : null;
    }

    /**
     * Get Landed Cost Default Units Of Measurement
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return OnePica_AvaTax_Model_Records_Parameter|null
     */
    public function getDefaultUnitsOfMeasurement($storeId = null)
    {
        return null;
        if (!$this->defaultUnitOfMeasurement) {
            $this->defaultUnitOfMeasurement = Mage::getModel('avatax_records/parameter')->load(
                Mage::getStoreConfig(
                    self::XML_PATH_TO_AVATAX_LANDED_COST_DEFAULT_UNITS_OF_MEASUREMENT,
                    $storeId
                )
            );
        }

        return $this->defaultUnitOfMeasurement;
    }

    /**
     * Get Product Avalara Parameter
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @param string                         $countryCode
     * @return Varien_Object|null
     *
     */
    public function getProductParameter($product, $countryCode)
    {
        $result = null;

        $product = is_int($product) ? Mage::getModel('catalog/product')->load($product) : $product;

        // get accurate units
        $units = $product->getData(self::AVATAX_PRODUCT_LANDED_COST_ATTR_PARAMETER);
        $units = is_string($units)
            ? Mage::getModel('avatax/catalog_product_attribute_backend_parameter')->decodeParameter($units)
            : $units;
        $units = empty($units) ? array() : $units;

        // search for accurate unit
        if (!empty($units)) {

            $ids = array();
            foreach ($units as $u) {
                array_push($ids, $u['parameter']);
            }

            $collection = Mage::getModel('avatax_records/parameter')->getCollection();
            $foundUnit = $collection->getUnitForCountry($ids, $countryCode);

            if ($foundUnit->getId()) {
                $resultUnit = null;
                foreach ($units as $u) {
                    if ($u['parameter'] == $foundUnit->getId()) {
                        $resultUnit = $u;
                        $resultUnit['avalara_uom'] = $foundUnit->getAvalaraUom();
                        $resultUnit['avalara_parameter_type'] = $foundUnit->getAvalaraParameterType();
                        $resultUnit['parameter_obj'] = $foundUnit;
                        break;
                    }
                }

                $result = !empty($resultUnit) ? new \Varien_Object($resultUnit) : null;
            }
        }

        // try to search default unit if nothing was found
        if(empty($result)) {
            $productDefaultUnit = $this->getDefaultUnitOfMeasurementForProduct($product);
            if(!empty($productDefaultUnit)) {
                $collection = Mage::getModel('avatax_records/parameter')->getCollection();
                $foundUnit = $collection->getUnitForCountry(array($productDefaultUnit['parameter']), $countryCode);

                if ($foundUnit->getId()) {
                    $resultUnit = $productDefaultUnit;
                    $resultUnit['avalara_uom'] = $foundUnit->getAvalaraUom();
                    $resultUnit['avalara_parameter_type'] = $foundUnit->getAvalaraParameterType();
                    $resultUnit['parameter_obj'] = $foundUnit;

                    $result = !empty($resultUnit) ? new \Varien_Object($resultUnit) : null;
                }
            }
        }

        return $result;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getDefaultUnitOfMeasurementForProduct($product)
    {
        $result = array();

        $weight = $product->getWeight();
        $defaultUnit = $this->getDefaultUnitsOfMeasurement($product->getStoreId());
        if (!empty($defaultUnit) && (!empty($weight)) && $weight > 0) {
            $result = array(
                'value'                => (float)$weight,
                'parameter' => $defaultUnit->getId(),
                'default'             => true
            );
        }

        return $result;
    }

    /**
     * Get Product HTS Code
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @param string                         $countryCodeFrom
     * @param string                         $countryCodeTo
     * @return string[];
     */
    public function getProductAgreements($product, $countryCodeFrom, $countryCodeTo)
    {
        $result = array();

        $product = is_int($product) ? Mage::getModel('catalog/product')->load($product) : $product;
        $agreements = $product->getData(self::AVATAX_PRODUCT_LANDED_COST_AGREEMENT);
        if (!empty($agreements)) {
            $agreements = explode(',', $agreements);
            /** @var OnePica_AvaTax_Model_Records_Mysql4_Agreement_Collection $collection */
            $collection = Mage::getModel('avatax_records/agreement')->getCollection();
            $collection->addFieldToFilter('id', array('in' => $agreements));
            $collection->getSelect()->where('country_list REGEXP ?', $countryCodeFrom);
            $collection->getSelect()->where('country_list REGEXP ?', $countryCodeTo);
            $collection->load();

            /** @var OnePica_AvaTax_Model_Records_Agreement $agr */
            foreach ($collection as $agr) {
                array_push($result, $agr->getAvalaraAgreementCode());
            }
        }

        return $result;
    }

    /**
     * Return precision for the unit
     *
     * @return int
     */
    public function getUnitPrecision()
    {
        return 4;
    }

    /**
     * Returns shipping insurance line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getShippingInsuranceSku($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_AVATAX_LANDED_COST_SHIPPING_INSURANCE_SKU, $store);
    }

    /**
     * Returns shipping insurance line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getShippingInsuranceTaxCode($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_AVATAX_LANDED_COST_SHIPPING_INSURANCE_TAX_CODE, $store);
    }

    /**
     * Get shipping tax class
     *
     * @param int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getLandedCostExpressShipping($store = null)
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_TO_AVATAX_LANDED_COST_EXPRESS_SHIPPING, $store);
    }

    /**
     * Get tax subtypes
     *
     * @param int|Mage_Core_Model_Store $store
     * @return array
     */
    public function getLandedCostTaxSubtypes($store = null)
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_TO_AVATAX_LANDED_COST_TAX_SUBTYPES, $store));
    }

    /**
     * @param TaxDetail|array $taxDetail
     * @return bool
     */
    public function isLandedCostTax($taxDetail)
    {
        $landedCostSubtypes = $this->getLandedCostTaxSubtypes();

        if ($taxDetail instanceof TaxDetail) {
            return in_array($taxDetail->getTaxSubTypeId(), $landedCostSubtypes);
        }

        if (is_array($taxDetail) && isset($taxDetail['avatax_tax_subtype'])) {
            return in_array($taxDetail['avatax_tax_subtype'], $landedCostSubtypes);
        }

        return false;
    }

    /**
     * @param $unit OnePica_AvaTax_Model_Records_Parameter
     * @return bool
     */
    public function isMassType($unit)
    {
       return $unit->getAvalaraParameterType() == $this->getMassType();
    }

    /**
     * Get Mass Type
     *
     * @return string
     */
    public function getMassType()
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_TO_AVATAX_LANDED_COST_PARAM_TYPE_MASS);
    }
}
