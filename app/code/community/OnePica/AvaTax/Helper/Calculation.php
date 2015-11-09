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
