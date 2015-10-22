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
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Service_Avatax_Abstract extends Varien_Object
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
     * Class pre-constructor
     */
    protected function _construct()
    {
        $this->_getApp()->useCache(self::AVATAX_CACHE_GROUP);
        $this->addData(array('cache_lifetime' => false));
        $this->addCacheTag(array(
            self::AVATAX_SERVICE_CACHE_GROUP,
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure()
        ));
    }

    /**
     * Get Key for caching block content
     *
     * @return string
     */
    public function getCacheKey()
    {
        $key = $this->getCacheKeyInfo();
        $key = array_values($key); // ignore array keys
        $key = implode('|', $key);
        $key = sha1($key);
        return $key;
    }

    /**
     * Get list of cache tags applied to model object.
     * Return false if cache tags are not supported by model
     *
     * @return array | false
     */
    public function getCacheTags()
    {
        $tags = false;
        if ($this->_cacheTag) {
            if ($this->_cacheTag === true) {
                $tags = array();
            } else {
                if (is_array($this->_cacheTag)) {
                    $tags = $this->_cacheTag;
                } else {
                    $tags = array($this->_cacheTag);
                }
                $idTags = $this->getCacheIdTags();
                if ($idTags) {
                    $tags = array_merge($tags, $idTags);
                }
            }
        }
        return $tags;
    }

    /**
     * Get cache key for tags
     *
     * @param string $cacheKey
     * @return string
     */
    protected function _getTagsCacheKey($cacheKey = null)
    {
        $cacheKey = !empty($cacheKey) ? $cacheKey : $this->getCacheKey();
        $cacheKey = md5($cacheKey . '_tags');
        return $cacheKey;
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'AVATAX_CACHE',
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure(),
        );
    }

    /**
     * Get block cache life time
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return $this->getData('cache_lifetime');
    }

    /**
     * Add tag to block
     *
     * @param string|array $tag
     * @return Mage_Core_Block_Abstract
     */
    public function addCacheTag($tag)
    {
        $tag = is_array($tag) ? $tag : array($tag);
        $tags = !$this->hasData(self::AVATAX_CACHE_GROUP) ?
            $tag : array_merge($this->getData(self::AVATAX_CACHE_GROUP), $tag);
        $this->setData(self::AVATAX_CACHE_GROUP, $tags);
        return $this;
    }

    /**
     * Load block html from cache storage
     *
     * @return string | false
     */
    protected function _loadCache()
    {
        if (!$this->_getApp()->useCache(self::AVATAX_CACHE_GROUP)) {
            return false;
        }
        $cacheKey = $this->getCacheKey();
        $cacheData = $this->_getApp()->loadCache($cacheKey);
        return $cacheData;
    }

    /**
     * Save cache
     * @param $data
     * @param $id
     * @return $this
     */
    protected function _saveCache($data)
    {
        $cacheKey = $this->getCacheKey();
        $tags = $this->getCacheTags();
        Mage::app()->saveCache($data, $cacheKey, $tags, $this->getCacheLifetime());
        $this->_getApp()->saveCache(
            json_encode($tags),
            $this->_getTagsCacheKey($cacheKey),
            $tags,
            $this->getCacheLifetime()
        );
        return $this;
    }

    /**
     * Logs a debug message
     *
     * @param string $type
     * @param string $request the request string
     * @param string $result the result string
     * @param int $storeId id of the store the call is make for
     * @param mixed $additional any other info
     * @return $this
     */
    protected function _log($type, $request, $result, $storeId = null, $additional = null)
    {
        if ($result->getResultCode() == SeverityLevel::$Success) {
            switch ($this->getHelper()->getLogMode($storeId)) {
                case OnePica_AvaTax_Model_Source_Logmode::ERRORS:
                    return $this;
                    break;
                case OnePica_AvaTax_Model_Source_Logmode::NORMAL:
                    $additional = null;
                    break;
            }
        }

        if (in_array($type, $this->getHelper()->getLogType($storeId))) {
            Mage::getModel('avatax_records/log')
                ->setStoreId($storeId)
                ->setLevel($result->getResultCode())
                ->setType($type)
                ->setRequest(print_r($request, true))
                ->setResult(print_r($result, true))
                ->setAdditional($additional)
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
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    public function getHelper()
    {
        if (!$this->_helper) {
            $this->_helper = Mage::helper('avatax');
        }
        return $this->_helper;
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Address
     */
    public function _getAddressHelper()
    {
        if (!$this->_getAddressHelper) {
            $this->_getAddressHelper = Mage::helper('avatax/address');
        }
        return $this->_getAddressHelper;
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    public function _getConfigHelper()
    {
        if (!$this->_getConfigHelper) {
            $this->_getConfigHelper = Mage::helper('avatax/config');
        }
        return $this->_getConfigHelper;
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    public function _getErrorsHelper()
    {
        if (!$this->_errorsHelper) {
            $this->_errorsHelper = Mage::helper('avatax/errors');
        }
        return $this->_errorsHelper;
    }

    //@startSkipCommitHooks
    /**
     * Alias to the helper translate method.
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        return call_user_func_array(array($this->getHelper(), '__'), $args);
    }
    //@finishSkipCommitHooks

    /**
     * Sets the company code on the request
     *
     * @param int|null $storeId
     * @return $this
     */
    protected function _setCompanyCode($storeId = null)
    {
        $config = Mage::getSingleton('avatax/config');
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
        $this->_request->setDocDate($this->_getDateModel()->date('Y-m-d'));
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
     * Retrieve Vat Id
     *
     * @param Mage_Sales_Model_Order|OnePica_AvaTax_Model_Sales_Quote_Address $object
     * @return string
     */
    protected function _getVatId($object)
    {
        if ($object instanceof Mage_Sales_Model_Order) {
            return $this->_getVatIdByOrder($object);
        }

        return $this->_getVatIdByQuoteAddress($object);
    }

    /**
     * Retrieve Vat Id from quote address
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address $address
     * @return string
     */
    protected function _getVatIdByQuoteAddress($address)
    {
        $vatId = $address->getVatId()
            ?: $address->getQuote()->getBillingAddress()->getVatId();
        return (string)$vatId;
    }

    /**
     * Retrieve Vat Id from order
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _getVatIdByOrder($order)
    {
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress && $shippingAddress->getVatId()) {
            return $shippingAddress->getVatId();
        }
        return $order->getBillingAddress()->getVatId();
    }

    /**
     * Sets the customer info if available
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $object
     * @return $this
     */
    protected function _addCustomer($object)
    {
        $format = Mage::getStoreConfig('tax/avatax/cust_code_format', $object->getStoreId());
        $customer = Mage::getModel('customer/customer');

        if ($object->getCustomerId()) {
            $customer->load($object->getCustomerId());
            $taxClass = Mage::getModel('tax/class')->load($customer->getTaxClassId())->getOpAvataxCode();
            $this->_request->setCustomerUsageType($taxClass);
        }

        switch ($format) {
            case OnePica_AvaTax_Model_Source_Customercodeformat::LEGACY:
                if ($customer->getId()) {
                    $customerCode = $customer->getName() . ' (' . $customer->getId() . ')';
                } else {
                    $address = $object->getBillingAddress() ? $object->getBillingAddress() : $object;
                    $customerCode = $address->getFirstname() . ' ' . $address->getLastname() . ' (Guest)';
                }
                break;
            case OnePica_AvaTax_Model_Source_Customercodeformat::CUST_EMAIL:
                $customerCode = $object->getCustomerEmail() ? $object->getCustomerEmail() : $customer->getEmail();
                break;
            case OnePica_AvaTax_Model_Source_Customercodeformat::CUST_ID:
            default:
                $customerCode = $object->getCustomerId() ? $object->getCustomerId() : 'guest-' . $object->getId();
                break;
        }

        $this->_request->setCustomerCode($customerCode);
        return $this;
    }

    /**
     * Adds the orgin address to the request
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return Address
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
     * Adds the shipping address to the request
     *
     * @param Address
     * @return bool
     */
    protected function _setDestinationAddress($address)
    {
        $street1 = $address->getStreet(1);
        $street2 = $address->getStreet(2);
        $city = $address->getCity();
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
     * Test to see if the product carries its own numbers or is calculated based on parent or children
     *
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item|mixed $item
     * @return bool
     */
    public function isProductCalculated($item)
    {
        // check if item has methods as far as shipping, gift wrapping, printed card item comes as Varien_Object
        if (method_exists($item, 'isChildrenCalculated') || method_exists($item, 'getParentItem')) {
            return true;
        }
        return false;
    }

    /**
     * Adds a comment to order history. Method choosen based on Magento version.
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $comment
     * @return self
     */
    protected function _addStatusHistoryComment($order, $comment)
    {
        if (method_exists($order, 'addStatusHistoryComment')) {
            $order->addStatusHistoryComment($comment)->save();
        } elseif (method_exists($order, 'addStatusToHistory')) {
            $order->addStatusToHistory($order->getStatus(), $comment, false)->save();;
        }
        return $this;
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
     * @param int $refNumber
     * @param int $storeId
     * @return null|string
     */
    protected function _getRefValueByProductAndNumber($product, $refNumber, $storeId)
    {
        $value = null;
        $helperMethod = 'getRef' . $refNumber . 'AttributeCode';
        $refCode = $this->_getConfigHelper()->{$helperMethod}($storeId);
        if ($refCode && $product->getResource()->getAttribute($refCode)) {
            try {
                $value = (string)$product->getResource()->getAttribute($refCode)->getFrontend()->getValue($product);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $value;
    }

    /**
     * Init tax override object
     *
     * @param string $taxOverrideType
     * @param string $reason
     * @param float $taxAmount
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
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @return int
     */
    protected function _getStoreIdByObject($object)
    {
        if ($object instanceof OnePica_AvaTax_Model_Sales_Quote_Address) {
            return $object->getQuote()->getStoreId();
        }

        return $object->getStoreId();
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
}
