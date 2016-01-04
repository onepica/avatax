<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * Class OnePica_AvaTax_Helper_Calculation
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Helper_Calculation
    extends Mage_Core_Helper_Abstract
{
    /**
     * Sets the customer info if available
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @return $this
     */
    public function getCustomerCode($object)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer');

        if ($object->getCustomerId()) {
            $customer->load($object->getCustomerId());
        }
        // get store id from object or from quote
        $storeId = $this->_getStoreIdFromSalesObject($object);

        switch ($this->_getConfigHelper()->getCustomerCodeFormat($storeId)) {
            case OnePica_AvaTax_Model_Source_Customercodeformat::LEGACY:
                $customerCode = $this->_getLegacyCustomerCode($object, $customer);
                break;
            case OnePica_AvaTax_Model_Source_Customercodeformat::CUST_EMAIL:
                $customerCode = $this->_getCustomerEmail($object, $customer)
                    ?: $this->_getCustomerId($object);
                break;
            case OnePica_AvaTax_Model_Source_Customercodeformat::CUST_ID:
            default:
                $customerCode = $this->_getCustomerId($object);
                break;
        }

        return $customerCode;
    }

    /**
     * Get store id from quote address or order object
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @return int|null
     */
    protected function _getStoreIdFromSalesObject($object)
    {
        $storeId = null;
        if ($object instanceof Mage_Sales_Model_Order) {
            $storeId = $object->getStoreId();
        } elseif ($object instanceof OnePica_AvaTax_Model_Sales_Quote_Address) {
            $storeId = $object->getQuote()->getStoreId();
        }

        return $storeId;
    }

    /**
     * Retrieve customer email
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @param Mage_Customer_Model_Customer                                    $customer
     * @return string
     */
    protected function _getCustomerEmail($object, $customer)
    {
        $email = null;
        if ($object instanceof OnePica_AvaTax_Model_Sales_Quote_Address) {
            $email = $object->getEmail();
            if (!$email) {
                // get email from billing in case the $object is shipping address
                $email = $object->getQuote()->getBillingAddress()
                       ? $object->getQuote()->getBillingAddress()->getEmail()
                       : null;
            }
        } elseif ($object instanceof Mage_Sales_Model_Order) {
            $email = $object->getCustomerEmail();
        }

        if (!$email) {
            $email = $customer->getEmail();
        }

        return $email;
    }

    /**
     * Retrieve customer id
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @return string
     */
    protected function _getCustomerId($object)
    {
        return $object->getCustomerId()
            ? $object->getCustomerId()
            : 'guest-' . $object->getId();
    }

    /**
     * Get legacy customer code
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @param Mage_Customer_Model_Customer                                    $customer
     * @return string
     */
    protected function _getLegacyCustomerCode($object, $customer)
    {
        if ($customer->getId()) {
            $customerCode = $customer->getName() . ' (' . $customer->getId() . ')';

            return $customerCode;
        }

        $address = $object->getBillingAddress() ?: $object;
        $customerCode = $address->getFirstname() . ' ' . $address->getLastname() . ' (Guest)';

        return $customerCode;
    }

    /**
     * Get customer usage type
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @return string
     */
    public function getCustomerOpAvataxCode($object)
    {
        return Mage::getModel('tax/class')->load($this->_getTaxClassId($object))->getOpAvataxCode();
    }

    /**
     * Get tax class id
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address|Mage_Sales_Model_Order $object
     * @return int
     */
    protected function _getTaxClassId($object)
    {
        if ($object instanceof OnePica_AvaTax_Model_Sales_Quote_Address) {
            return $object->getQuote()->getCustomerTaxClassId();
        }

        return Mage::getSingleton('customer/group')->load($object->getCustomerGroupId())->getTaxClassId();
    }

    /**
     * Get simple product id from configurable item
     *
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Creditmemo_Item|Mage_Sales_Model_Order_Invoice_Item $item
     * @return int
     */
    public function getSimpleProductIdByConfigurable($item)
    {
        if (($item instanceof Mage_Sales_Model_Quote_Item
             || $item instanceof Mage_Sales_Model_Quote_Address_Item)
            && $this->isConfigurable($item)
        ) {
            /** @var Mage_Catalog_Model_Product[] $children */
            $children = $item->getChildren();
            if (isset($children[0]) && $children[0]->getProductId()) {
                return $children[0]->getProductId();
            }
        }

        if (($item instanceof Mage_Sales_Model_Order_Invoice_Item
             || $item instanceof Mage_Sales_Model_Order_Creditmemo_Item)
            && $this->isConfigurable($item)
        ) {
            $children = $item->getOrderItem()->getChildrenItems();
            if (isset($children[0]) && $children[0]->getProductId()) {
                return $children[0]->getProductId();
            }
        }

        return 0;
    }

    /**
     * Checks if item is configurable
     *
     * @param Mage_Sales_Model_Quote_Address_Item|Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Creditmemo_Item|Mage_Sales_Model_Order_Invoice_Item $item
     * @return bool
     */
    public function isConfigurable($item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            return $item->getProductType() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
        }

        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            return $item->getProduct()->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
        }

        if (($item instanceof Mage_Sales_Model_Order_Invoice_Item
             || $item instanceof Mage_Sales_Model_Order_Creditmemo_Item)
        ) {
            return $item->getOrderItem()->getProductType() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
        }

        return false;
    }

    /**
     * Get item code
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int|Mage_Core_Model_Store  $storeId
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     * @return string
     */
    public function getItemCode($product, $storeId, $item = null)
    {
        $itemCode = '';
        if (null !== $product) {
            $itemCode = $this->_getUpcCode($product, $storeId);
        }

        if (empty($itemCode)) {
            $itemCode = (null !== $item) ? $item->getSku() : $product->getSku();
        }

        return substr($itemCode, 0, 50);
    }

    /**
     * Get UPC code from product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int|Mage_Core_Model_Store  $storeId
     * @return string
     */
    protected function _getUpcCode($product, $storeId)
    {
        $upc = $this->getProductAttributeValue(
            $product,
            $this->_getUpcAttributeCode($storeId)
        );

        return !empty($upc) ? 'UPC:' . $upc : '';
    }

    /**
     * Get product attribute value
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string                     $code
     * @return string
     */
    public function getProductAttributeValue($product, $code)
    {
        $value = '';
        if ($code && $product->getResource()->getAttribute($code)) {
            try {
                $value = (string)$product->getResource()
                    ->getAttribute($code)
                    ->getFrontend()
                    ->getValue($product);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $value;
    }

    /**
     * Get UPC attribute code
     *
     * @param int|Mage_Core_Model_Store $store
     * @return string
     */
    protected function _getUpcAttributeCode($store)
    {
        return $this->_getConfigHelper()->getUpcAttributeCode($store);
    }

    /**
     * Get data helper
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }
}
