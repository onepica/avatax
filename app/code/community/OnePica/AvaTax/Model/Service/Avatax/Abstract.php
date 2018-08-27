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
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Avatax service abstract model
 *
 * @method getService() OnePica_AvaTax_Model_Service_Avatax
 * @method $this setLandedCostMode(string $mode)
 * @method string getLandedCostMode()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Service_Avatax_Abstract extends OnePica_AvaTax_Model_Service_Abstract_Tools
{
    /**
     * Avatax cache tag
     */
    const AVATAX_SERVICE_CACHE_GROUP = 'avatax_cache_tags';

    /**
     * Avatax cache tag
     */
    const AVATAX_CACHE_GROUP = 'avatax';

    /**
     * Flag that states if there was an error
     *
     * @var bool
     */
    protected static $_hasError = false;

    /**
     * The request data object
     *
     * @var GetTaxRequest
     */
    protected $_request = null;

    /**
     * Product collection for items to be calculated
     *
     * @var Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $_productCollection = null;

    /**
     * Tax class collection for items to be calculated
     *
     * @var Mage_Tax_Model_Resource_Class_Collection
     */
    protected $_taxClassCollection = null;

    /**
     * The module helper
     *
     * @var OnePica_AvaTax_Helper_Data
     */
    protected $_helper = null;

    /**
     * The module address helper
     *
     * @var OnePica_AvaTax_Helper_Address
     */
    protected $_getAddressHelper = null;

    /**
     * The module config helper
     *
     * @var OnePica_AvaTax_Helper_Config
     */
    protected $_getConfigHelper = null;

    /**
     * The module config helper
     *
     * @var OnePica_AvaTax_Model_Service_Avatax_Invoice
     */
    protected $_errorsHelper = null;

    /**
     * The module config helper
     *
     * @var OnePica_AvaTax_Model_Service_Avatax_Tax
     */
    protected $_libHelper = null;

    /**
     * Model cache tag for clear cache in after save and after delete
     */
    protected $_cacheTag = self::AVATAX_SERVICE_CACHE_GROUP;

    /**
     * Logs a debug message
     *
     * @param string          $type
     * @param string          $request    the request string
     * @param string          $result     the result string
     * @param int             $storeId    id of the store the call is make for
     * @param mixed           $additional any other info
     * @param \TaxServiceSoap $connection for logging soap request/response
     * @param Varien_Object   $quoteData
     * @return $this
     * @throws \Varien_Exception
     */
    protected function _log(
        $type,
        $request,
        $result,
        $storeId = null,
        $additional = null,
        $connection = null,
        $quoteData = null
    ) {
        if ($result->getResultCode() == SeverityLevel::$Success) {
            switch ($this->_getHelper()->getLogMode($storeId)) {
                case OnePica_AvaTax_Model_Source_Logmode::ERRORS:
                    return $this;
                    break;
                case OnePica_AvaTax_Model_Source_Logmode::NORMAL:
                    $additional = null;
                    break;
            }
        }

        $soapRequest = null;
        $soapRequestHeaders = null;
        $soapResponse = null;
        $soapResponseHeaders = null;

        if ($connection) {
            $soapRequest = $connection->__getLastRequest();
            $soapRequestHeaders = $connection->__getLastRequestHeaders();
            $soapResponse = $connection->__getLastResponse();
            $soapResponseHeaders = $connection->__getLastResponseHeaders();
        }

        $quoteId = $quoteData ? $quoteData->getQuoteId() : null;
        $quoteAddressId = $quoteData ? $quoteData->getQuoteAddressId() : null;

        if (in_array($type, $this->_getHelper()->getLogType($storeId))) {
            Mage::getModel('avatax_records/log')
                ->setStoreId($storeId)
                ->setLevel($result->getResultCode())
                ->setType($type)
                ->setRequest(print_r($request, true))
                ->setResult(print_r($result, true))
                ->setAdditional($additional)
                ->setSoapRequest($soapRequest)
                ->setSoapRequestHeaders($soapRequestHeaders)
                ->setSoapResult($soapResponse)
                ->setSoapResultHeaders($soapResponseHeaders)
                ->setQuoteId($quoteId)
                ->setQuoteAddressId($quoteAddressId)
                ->save();
        }

        return $this;
    }

    /**
     * Returns the AvaTax session.
     *
     * @return OnePica_AvaTax_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('avatax/session');
    }

    /**
     * Sets the company code on the request
     *
     * @param int|null $storeId
     * @return $this
     * @throws \Varien_Exception
     */
    protected function _setCompanyCode($storeId = null)
    {
        $config = Mage::getSingleton('avatax/service_avatax_config');
        $this->_request->setCompanyCode($config->getCompanyCode($storeId));

        return $this;
    }

    /**
     * Get gift wrapping tax class id
     *
     * @param Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo|Mage_Sales_Model_Quote_Address $object
     * @return int
     */
    protected function _getGwTaxClassId($object)
    {
        if (!$object->getGwPrice()
            && !$object->getGwItemsPrice()
            && !$object->getGwPrintedCardPrice()
        ) {
            return 0;
        }

        if ($object instanceof Mage_Sales_Model_Quote_Address) {
            $storeId = $object->getQuote()->getStoreId();
        } else {
            $storeId = $object->getStoreId();
        }

        return $this->_getWrappingTaxClass($storeId);
    }

    /**
     * Get gift wrapping tax class config value
     *
     * @param int $storeId
     * @return int
     */
    protected function _getWrappingTaxClass($storeId)
    {
        if (Mage::getEdition() !== Mage::EDITION_ENTERPRISE) {
            return 0;
        }

        return (int)$this->_getGiftWrappingDataHelper()->getWrappingTaxClass($storeId);
    }

    /**
     * Adds additional transaction based data
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @return $this
     */
    protected function _addGeneralInfo($object)
    {
        $storeId = $this->_getStoreIdByObject($object);
        $this->_setCompanyCode($storeId);
        $this->_request->setBusinessIdentificationNo($this->_getVatId($object));
        $this->_request->setDetailLevel(DetailLevel::$Document);
        $this->_request->setDocDate($this->_convertGmtDate(Mage::helper('avatax')->varienDateNow(), $storeId));
        $this->_request->setExemptionNo('');
        $this->_request->setDiscount(0.00); //cannot be used in Magento
        $this->_request->setSalespersonCode($this->_getConfigHelper()->getSalesPersonCode($storeId));
        $this->_request->setLocationCode($this->_getConfigHelper()->getLocationCode($storeId));
        $this->_request->setCountry($this->_getConfigHelper()->getShippingOriginCountryId($storeId));
        $this->_request->setCurrencyCode(Mage::app()->getStore($storeId)->getBaseCurrencyCode());
        $this->_addCustomer($object);
        if ($object instanceof Mage_Sales_Model_Order && $object->getIncrementId()) {
            $this->_request->setReferenceCode('Magento Order #' . $object->getIncrementId());
        }

        return $this;
    }

    /**
     * Init Landed Cost Mode Param
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @return $this
     * @throws \Varien_Exception
     */
    protected function _initLandedCostModeParam($object)
    {
        $storeId = $this->_getStoreIdByObject($object);
        $address = $this->_getShippingAddressFromObject($object);
        $countryId = ($address) ? $address->getCountryId() : null;
        $mode = $this->_getLandedCostHelper()->getLandedCostMode($countryId, $storeId);
        $this->setLandedCostMode($mode);

        return $this;
    }

    /**
     * Adds general Landed Cost info data
     *
     * @return $this
     * @throws \Varien_Exception
     */
    protected function _addGeneralLandedCostInfo($address)
    {
        if ($this->getLandedCostMode()) {

            $isSellerImporter = $this->_getLandedCostHelper()->isSellerImporterOfRecordForTheCustomer($address);
            $isSellerImporterQuote = $this->_getLandedCostHelper()->isSellerImporterOfRecordForQuote($address);

            /* set from quote first */
            if ($isSellerImporterQuote !== null) {
                $this->_request->setIsSellerImporterOfRecord((boolean)$isSellerImporterQuote);
            } elseif ($isSellerImporter !== null) {
                $this->_request->setIsSellerImporterOfRecord((boolean)$isSellerImporter);
            }

            $bagItemsParams = array();

            /** @var OnePica_AvaTax_Helper_Landedcost_Shipping $shippingHelper */
            $shippingHelper = Mage::helper('avatax/landedcost_shipping');
            $mode = $shippingHelper->getShippingMode($address);
            if ($mode) {
                $shippingMode = new ParameterBagItem();
                $shippingMode->setName('AvaTax.LandedCost.ShippingMode');
                $shippingMode->setValue($mode);

                array_push($bagItemsParams, $shippingMode);
            }

            $isExpress = $shippingHelper->getShippingIsExpress($address);
            if ($isExpress) {
                $shippingExpress = new ParameterBagItem();
                $shippingExpress->setName('AvaTax.LandedCost.Express');
                $shippingExpress->setValue('true');

                array_push($bagItemsParams, $shippingExpress);
            }

            if (!empty($bagItemsParams)) {
                $this->_request->setParameterBagItems($bagItemsParams);
            }
        }

        return $this;
    }

    /**
     * Add Landed Cost Params To Line
     *
     * @param Line                           $line
     * @param Mage_Catalog_Model_Product     $product
     * @param Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address $address
     * @return $this
     * @throws \Varien_Exception
     */
    protected function _addLandedCostParamsToLine($line, $product, $address)
    {
        if ($this->getLandedCostMode()) {

            /* @var OnePica_AvaTax_Helper_LandedCost $lcHelper */
            $lcHelper = $this->_getLandedCostHelper();

            // HS Code
            $hsCodeValue = $lcHelper->getProductHTSCode($product, $address->getCountryId());
            $line->setHSCode($hsCodeValue);

            $bagItemsParams = array();

            // Unit
            $unit = $lcHelper->getProductParameter($product, $address->getCountryId());
            if (!empty($unit)) {
                $unitValue = $unit->getValue();
                $htsMeasurement = new ParameterBagItem();
                $htsMeasurement->setName($unit->getAvalaraParameterType());
                $htsMeasurement->setValue($unitValue);
                $htsMeasurement->setUOMCode($unit->getAvalaraUom());

                array_push($bagItemsParams, $htsMeasurement);
            }

            //Trade Agreement
            $storeId = $this->_getStoreIdByObject($address);
            $agreements = $lcHelper->getProductAgreements(
                $product,
                $this->_getConfigHelper()->getShippingOriginCountryId($storeId),
                $address->getCountryId()
            );
            if (!empty($agreements)) {
                $agreements = implode(',', $agreements);
                $bagAgreement = new ParameterBagItem();
                $bagAgreement->setName('AvaTax.LC.PreferredProgram');
                $bagAgreement->setValue($agreements);

                array_push($bagItemsParams, $bagAgreement);
            }

            if (!empty($bagItemsParams)) {
                $line->setParameterBagItems($bagItemsParams);
            }
        }

        return $this;
    }

    /**
     * Sets the customer info if available
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @return $this
     */
    protected function _addCustomer($object)
    {
        $this->_request->setCustomerUsageType($this->_getCalculationHelper()->getCustomerOpAvataxCode($object));
        $this->_request->setCustomerCode($this->_getCalculationHelper()->getCustomerCode($object));

        return $this;
    }

    /**
     * Applies origin address for request
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $entity
     * @return $this
     */
    protected function _applyOriginAddressForRequest($entity)
    {
        $this->_setOriginAddress($entity->getStoreId());

        return $this;
    }

    /**
     * Adds the origin address to the request
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return \GetTaxRequest
     * @throws \Varien_Exception
     */
    protected function _setOriginAddress($store = null)
    {
        $country = Mage::getStoreConfig('shipping/origin/country_id', $store);
        $zip = Mage::getStoreConfig('shipping/origin/postcode', $store);
        $regionId = Mage::getStoreConfig('shipping/origin/region_id', $store);
        $state = Mage::getModel('directory/region')->load($regionId)->getCode();
        $city = Mage::getStoreConfig('shipping/origin/city', $store);
        $street = Mage::getStoreConfig('shipping/origin/street', $store);
        $address = $this->_newAddress($street, '', $city, $state, $zip, $country);

        return $this->_request->setOriginAddress($address);
    }

    /**
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $model
     * @return \GetTaxRequest
     * @throws \Varien_Exception
     */
    protected function _setOriginAddressFromModel($model)
    {
        return $this->_setOriginAddress($model->getStoreId());
    }

    /**
     * Adds the shipping address to the request
     *
     * @param \Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address|Address $address
     * @return bool|\GetTaxRequest
     * @throws \Varien_Exception
     */
    protected function _setDestinationAddress($address)
    {
        $street1 = $address->getStreet(1);
        $street2 = $address->getStreet(2);
        $city = (string)$address->getCity();
        $zip = $address->getPostcode();
        $state = Mage::getModel('directory/region')->load($address->getRegionId())->getCode();
        $country = $address->getCountry();

        if (($city && $state) || $zip) {
            $address = $this->_newAddress($street1, $street2, $city, $state, $zip, $country);

            return $this->_request->setDestinationAddress($address);
        } else {
            return false;
        }
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
     * @return Address
     */
    protected function _newAddress($line1, $line2, $city, $state, $zip, $country = 'USA')
    {
        $address = new Address();
        $address->setLine1($line1);
        $address->setLine2($line2);
        $address->setCity($city);
        $address->setRegion($state);
        $address->setPostalCode($zip);
        $address->setCountry($country);

        return $address;
    }

    /**
     * Init product collection for items to be calculated
     *
     * @param Mage_Sales_Model_Mysql4_Order_Invoice_Item_Collection|array $items
     * @return $this
     */
    protected function _initProductCollection($items)
    {
        $productIds = array();
        foreach ($items as $item) {
            if (!$this->isProductCalculated($item)) {
                $productIds[] = $item->getProductId();
                $simpleProductId = $this->_getCalculationHelper()->getSimpleProductIdByConfigurable($item);
                if ($simpleProductId) {
                    $productIds[] = $simpleProductId;
                }
            }
        }

        $this->_productCollection = Mage::getModel('catalog/product')->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addAttributeToFilter('entity_id', array('in' => $productIds));

        return $this;
    }

    /**
     * Init tax class collection for items to be calculated
     *
     * @param Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo|Mage_Sales_Model_Quote_Address $object
     * @return $this
     * @throws OnePica_AvaTax_Exception
     */
    protected function _initTaxClassCollection($object)
    {
        $taxClassIds = array();
        foreach ($this->_getProductCollection() as $product) {
            if (!in_array($product->getTaxClassId(), $taxClassIds)) {
                $taxClassIds[] = $product->getTaxClassId();
            }
        }

        $gwTaxClassId = $this->_getGwTaxClassId($object);

        if (0 !== $gwTaxClassId) {
            $taxClassIds[] = $gwTaxClassId;
        }

        $this->_taxClassCollection = Mage::getModel('tax/class')->getCollection()
                                         ->addFieldToFilter('class_id', array('in' => $taxClassIds));

        return $this;
    }

    /**
     * Get product collection for items to be calculated
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     * @throws OnePica_AvaTax_Exception
     */
    protected function _getProductCollection()
    {
        if (!$this->_productCollection) {
            throw new OnePica_AvaTax_Exception('Product collection should be set before usage');
        }

        return $this->_productCollection;
    }

    /**
     * Get tax class collection for items to be calculated
     *
     * @return Mage_Tax_Model_Resource_Class_Collection
     * @throws OnePica_AvaTax_Exception
     */
    protected function _getTaxClassCollection()
    {
        if (!$this->_taxClassCollection) {
            throw new OnePica_AvaTax_Exception('Tax class collection should be set before usage');
        }

        return $this->_taxClassCollection;
    }

    /**
     * Get Avatax tax code for given product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     * @throws \OnePica_AvaTax_Exception
     * @throws \Varien_Exception
     */
    protected function _getTaxClassCodeByProduct($product)
    {
        $taxClass = $this->_getTaxClassCollection()->getItemById($product->getTaxClassId());

        return $taxClass ? $taxClass->getOpAvataxCode() : '';
    }

    /**
     * Get gift Avatax tax class code
     *
     * @param int $storeId
     * @return string
     * @throws \OnePica_AvaTax_Exception
     * @throws \Varien_Exception
     */
    protected function _getGiftTaxClassCode($storeId)
    {
        $taxClassId = $this->_getWrappingTaxClass($storeId);
        $taxClass = $this->_getTaxClassCollection()->getItemById($taxClassId);

        return $taxClass ? $taxClass->getOpAvataxCode() : '';
    }

    /**
     * Get product from collection by given product id
     *
     * @param int $productId
     * @return Mage_Catalog_Model_Product
     * @throws OnePica_AvaTax_Exception
     */
    protected function _getProductByProductId($productId)
    {
        return $this->_getProductCollection()->getItemById($productId);
    }

    /**
     * Get proper ref value for given product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int                        $refNumber
     * @param int                        $storeId
     * @return null|string
     */
    protected function _getRefValueByProductAndNumber($product, $refNumber, $storeId)
    {
        $value = null;
        $helperMethod = 'getRef' . $refNumber . 'AttributeCode';
        $code = $this->_getConfigHelper()->{$helperMethod}($storeId);
        $value = $this->_getCalculationHelper()->getProductAttributeValue($product, $code);

        return $value;
    }

    /**
     * Init tax override object
     *
     * @param string $taxOverrideType
     * @param string $reason
     * @param float  $taxAmount
     * @return TaxOverride
     */
    protected function _getTaxOverrideObject($taxOverrideType, $reason, $taxAmount)
    {
        $taxOverride = new TaxOverride();
        $taxOverride->setTaxOverrideType($taxOverrideType);
        $taxOverride->setReason($reason);
        $taxOverride->setTaxAmount($taxAmount);

        return $taxOverride;
    }

    /**
     * Get gift wrapping data helper
     *
     * @return \Enterprise_GiftWrapping_Helper_Data
     */
    protected function _getGiftWrappingDataHelper()
    {
        return Mage::helper('enterprise_giftwrapping');
    }

    /**
     * Get date model
     *
     * @return Mage_Core_Model_Date
     */
    protected function _getDateModel()
    {
        return Mage::getSingleton('core/date');
    }

    /**
     * Retrieve storeId from object
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order_Address|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @return int
     */
    protected function _getStoreIdByObject($object)
    {
        if ($object instanceof OnePica_AvaTax_Model_Sales_Quote_Address) {
            return $object->getQuote()->getStoreId();
        }

        if ($object instanceof Mage_Sales_Model_Order_Address) {
            return $object->getOrder()->getStoreId();
        }

        if ($object instanceof Mage_Sales_Model_Order_Invoice) {
            return $object->getStore()->getId();
        }

        if ($object instanceof Mage_Sales_Model_Order_Creditmemo) {
            return $object->getStore()->getId();
        }

        return $object->getStoreId();
    }

    /**
     * Retrieve shipping addresss from object
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order_Address|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo|Mage_Sales_Model_Order $object
     * @return int
     */
    protected function _getShippingAddressFromObject($object)
    {
        if ($object instanceof OnePica_AvaTax_Model_Sales_Quote_Address) {
            return $object;
        }

        if ($object instanceof Mage_Sales_Model_Order_Address) {
            return $object;
        }

        if ($object instanceof Mage_Sales_Model_Order_Invoice) {
            return $object->getShippingAddress();
        }

        if ($object instanceof Mage_Sales_Model_Order_Creditmemo) {
            return $object->getShippingAddress();
        }

        if ($object instanceof Mage_Sales_Model_Order) {
            return $object->getShippingAddress();
        }

        return null;
    }

    /**
     * Retrieve application instance
     *
     * @return Mage_Core_Model_App
     */
    protected function _getApp()
    {
        return Mage::app();
    }

    /**
     * Get calculation helper
     *
     * @return \OnePica_AvaTax_Helper_Calculation
     */
    protected function _getCalculationHelper()
    {
        return Mage::helper('avatax/calculation');
    }
}
