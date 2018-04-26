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
     *  HS Code product weight
     */
    const AVATAX_PRODUCT_LANDED_COST_ATTR_UNIT_OF_WEIGHT = 'avatax_lc_unit_of_weight';

    /**
     *  Landed Cost product agreement
     */
    const AVATAX_PRODUCT_LANDED_COST_AGREEMENT = 'avatax_lc_agreement';

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
    public function getLandedCostMode($storeId = null, $destinationCountry)
    {
        $mode = null;
        $originCountryCode = Mage::getStoreConfig('shipping/origin/country_id', $storeId);
        if ($this->isLandedCostEnabled($storeId) && $destinationCountry != $originCountryCode) {
            if (in_array($destinationCountry, $this->getLandedCostDDPCountries())) {
                $mode = 'DDP';
            } elseif (in_array($destinationCountry, $this->getLandedCostDAPCountries())) {
                $mode = 'DAP';
            }
        }

        return $mode;
    }

    /**
     * Get Product HTS Code
     *
     * @param int $productId
     * @return string
     *
     * @todo Should be refactored. Collection of HTS code should be initialized before request building
     * example taxClassCollection
     */
    public function getProductHTSCode($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        $htsCode = $product->getData('avatax_hts_code');

        return $htsCode;
    }
}
