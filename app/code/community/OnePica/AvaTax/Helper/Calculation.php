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
        $itemCode = $this->_getUpcCode($product, $storeId);
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
