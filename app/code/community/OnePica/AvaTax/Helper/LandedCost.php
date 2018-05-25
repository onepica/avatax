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
    const AVATAX_PRODUCT_GROUP_LANDED_COST = 'AvaTax Landed Cost';

    /**
     *  HS Code product attribute
     */
    const AVATAX_PRODUCT_LANDED_COST_ATTR_HSCODE = 'avatax_lc_hs_code';

    /**
     *  Product Unit of Measurement
     */
    const AVATAX_PRODUCT_LANDED_COST_ATTR_UNIT_OF_MEASUREMENT = 'avatax_lc_unit_of_measurement';

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
     * Get Landed Cost Mode
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @param string                    $destinationCountry
     * @return null|string
     */
    public function getLandedCostMode($destinationCountry, $storeId = null)
    {
        $mode = $this->isSellerImporterOfRecord($storeId, $destinationCountry) ? 'DDP' : null;
//        $originCountryCode = Mage::getStoreConfig('shipping/origin/country_id', $storeId);
//        if ($this->isLandedCostEnabled($storeId) && $destinationCountry != $originCountryCode) {
//            if (in_array($destinationCountry, $this->getLandedCostDDPCountries())) {
//                $mode = 'DDP';
//            } elseif (in_array($destinationCountry, $this->getLandedCostDAPCountries())) {
//                $mode = 'DAP';
//            }
//        }

        return $mode;
    }

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
     * @return OnePica_AvaTax_Model_Records_UnitOfMeasurement|null
     */
    public function getDefaultUnitsOfMeasurement($storeId = null)
    {
        if (!$this->defaultUnitOfMeasurement) {
            $this->defaultUnitOfMeasurement = Mage::getModel('avatax_records/unitOfMeasurement')->load(
                Mage::getStoreConfig(
                    self::XML_PATH_TO_AVATAX_LANDED_COST_DEFAULT_UNITS_OF_MEASUREMENT,
                    $storeId
                )
            );
        }

        return $this->defaultUnitOfMeasurement;
    }

    /**
     * Get Product Avalara Unit Of Measurement
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @param string                         $countryCode
     * @return Varien_Object|null
     *
     */
    public function getProductUnitOfMeasurement($product, $countryCode)
    {
        $result = null;

        $product = is_int($product) ? Mage::getModel('catalog/product')->load($product) : $product;

        // get accurate units
        $units = $product->getData(self::AVATAX_PRODUCT_LANDED_COST_ATTR_UNIT_OF_MEASUREMENT);
        $units = is_string($units)
            ? Mage::getModel('avatax/catalog_product_attribute_backend_unit')->decodeUnitOfMeasurement($units)
            : $units;
        $units = empty($units) ? array() : $units;

        // add default unit
        $weight = $product->getWeight();
        $defaultUnit = $this->getDefaultUnitsOfMeasurement($product->getStoreId());
        if (!empty($defaultUnit) && (!empty($weight)) && $weight > 0) {
            array_push(
                $units,
                array(
                    'unit'                => (float)$weight,
                    'unit_of_measurement' => $defaultUnit->getId(),
                    'default'             => true
                )
            );
        }

        // gather all unit ids
        $ids = array();
        if (!empty($units)) {
            foreach ($units as $u) {
                array_push($ids, $u['unit_of_measurement']);
            }
        }

        // find first any unit by country
        if (!empty($ids)) {
            $collection = Mage::getModel('avatax_records/unitOfMeasurement')
                              ->getCollection()
                              ->addFieldToFilter('id', array('in' => $ids));
            $collection->getSelect()->where('country_list REGEXP ?', $countryCode);

            /** @var OnePica_AvaTax_Model_Records_UnitOfMeasurement $unit */
            $unit = $collection->getFirstItem();

            // search for final unit and measurement,
            // accurate units first
            if ($unit->getId()) {
                $resultUnit = null;
                foreach ($units as $u) {
                    if ($u['unit_of_measurement'] == $unit->getId()) {
                        $resultUnit = $u;
                        $resultUnit['avalara_code'] = $unit->getAvalaraCode();
                        $resultUnit['avalara_measurement_type'] = $unit->getAvalaraMeasurementType();
                        $resultUnit['unit_obj'] = $unit;
                        break;
                    }
                }
            }

            $result = !empty($resultUnit) ? new \Varien_Object($resultUnit) : null;
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
}
