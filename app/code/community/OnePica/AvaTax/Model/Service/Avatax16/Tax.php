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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Avatax16 service tax model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax16_Tax extends OnePica_AvaTax_Model_Service_Avatax16_Abstract
{
    /**
     * The document request data object
     *
     * @var OnePica_AvaTax16_Document_Request
     */
    protected $_request = null;

    /**
     * An array of line items
     *
     * @var array
     */
    protected $_lines = array();

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
     * Get the orgin address for the request
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected function _getOriginAddress($store = null)
    {
        $country = Mage::getStoreConfig('shipping/origin/country_id', $store);
        $zip = Mage::getStoreConfig('shipping/origin/postcode', $store);
        $regionId = Mage::getStoreConfig('shipping/origin/region_id', $store);
        $state = Mage::getModel('directory/region')->load($regionId)->getCode();
        $city = Mage::getStoreConfig('shipping/origin/city', $store);
        $street = Mage::getStoreConfig('shipping/origin/street', $store);
        $address = $this->_newAddress($street, '', $city, $state, $zip, $country);
        return $address;
    }

    /**
     * Get the shipping address for the request
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address $address
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected function _getDestinationAddress($address)
    {
        $street1 = $address->getStreet(1);
        $street2 = $address->getStreet(2);
        $city = (string)$address->getCity();
        $zip = $address->getPostcode();
        $state = Mage::getModel('directory/region')->load($address->getRegionId())->getCode();
        $country = $address->getCountry();
        $address = $this->_newAddress($street1, $street2, $city, $state, $zip, $country);

        return $address;
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
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected function _newAddress($line1, $line2, $city, $state, $zip, $country = 'USA')
    {
        $address = $this->_getNewDocumentPartLocationAddressObject();
        // set required field line1 if it is empty
        $line1 = ($line1) ? $line1 : '_';
        $address->setLine1($line1);
        $address->setLine2($line2);
        $address->setCity($city);
        $address->setState($state);
        $address->setZipcode($zip);
        $address->setCountry($country);
        return $address;
    }

    /**
     * Get default locations
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address
     * @return array
     */
    protected function _getHeaderDefaultLocations($address)
    {
        $entity = $address->getQuote() ? $address->getQuote() : $address->getOrder();
        $storeId = $entity->getStoreId();

        $locationFrom = $this->_getNewDocumentPartLocationObject();
        $locationFrom->setTaxLocationPurpose(self::TAX_LOCATION_PURPOSE_SHIP_FROM);
        $locationFrom->setAddress($this->_getOriginAddress($storeId));

        $locationTo = $this->_getNewDocumentPartLocationObject();
        $locationTo->setTaxLocationPurpose(self::TAX_LOCATION_PURPOSE_SHIP_TO);
        $locationTo->setAddress($this->_getDestinationAddress($address));

        $defaultLocations = array(
            self::TAX_LOCATION_PURPOSE_SHIP_FROM => $locationFrom,
            self::TAX_LOCATION_PURPOSE_SHIP_TO   => $locationTo
        );

        return $defaultLocations;
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
     * Get gift wrapping tax class id
     *
     * @param Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo|Mage_Sales_Model_Quote_Address $object
     * @return int
     */
    protected function _getGwTaxClassId($object)
    {
        if (Mage::getEdition() !== Mage::EDITION_ENTERPRISE) {
            return 0;
        }
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
        return (int)$this->_getGiftWrappingDataHelper()->getWrappingTaxClass($storeId);
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
     * Get Avatax tax code for given product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
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
     */
    protected function _getGiftTaxClassCode($storeId)
    {
        $taxClassId = $this->_getWrappingTaxClass($storeId);
        $taxClass = $this->_getTaxClassCollection()->getItemById($taxClassId);
        return $taxClass ? $taxClass->getOpAvataxCode() : '';
    }

    /**
     * Get proper ref value for given product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $refNumber
     * @param int $storeId
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
     * Get new line code
     *
     * @return string
     */
    protected function _getNewLineCode()
    {
        return count($this->_lines) + 1;
    }

    /**
     * Set lines to request object
     * Avalara should receive lines as numeric array starting from 0
     *
     * @return $this
     */
    protected function _setLinesToRequest()
    {
        $this->_request->setLines(array_values($this->_lines));
    }

    /**
     * Get Request Header With Main Values
     *
     * @param int                                                             $storeId
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @return \OnePica_AvaTax16_Document_Request_Header
     */
    protected function _getRequestHeaderWithMainValues($storeId, $object)
    {
        $configModel = $this->getServiceConfig()->init($storeId);
        $config = $configModel->getLibConfig();
        // header generation
        $header = $this->_getNewDocumentRequestHeaderObject();
        $header->setAccountId($config->getAccountId());
        $header->setCompanyCode($config->getCompanyCode());
        $header->setTransactionType(self::TRANSACTION_TYPE_SALE);
        $header->setCustomerCode($this->_getCalculationHelper()->getCustomerCode($object));
        $header->setDefaultTaxPayerCode($this->_getVatId($object));
        $metadata = array('salesPersonCode' => $this->_getConfigHelper()->getSalesPersonCode($storeId));
        $header->setMetadata($metadata);
        /** @todo: Remove this code if this field is not required by extesion and Avalara
        $header->setVendorCode(self::DEFAULT_VENDOR_CODE); */
        $header->setCurrency(Mage::app()->getStore($storeId)->getBaseCurrencyCode());
        $header->setDefaultBuyerType($this->_getCalculationHelper()->getCustomerOpAvataxCode($object));
        /** @todo: Remove this code if we will not use those properties
        $header->setDefaultAvalaraGoodsAndServicesType($this->_getConfigHelper()
            ->getDefaultAvalaraGoodsAndServicesType($storeId));
        $header->setDefaultAvalaraGoodsAndServicesModifierType($this->_getConfigHelper()
            ->getDefaultAvalaraGoodsAndServicesModifierType($storeId));
        $header->setDefaultTaxPayerCode($this->_getConfigHelper()->getDefaultTaxPayerCode($storeId));
        $header->setDefaultUseType($this->_getConfigHelper()->getDefaultUseType($storeId));
        $header->setDefaultBuyerType($this->_getConfigHelper()->getDefaultBuyerType($storeId));
        */

        return $header;
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
