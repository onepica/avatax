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
class OnePica_AvaTax_Helper_FixedTax extends Mage_Core_Helper_Abstract
{
    /** Landed cost tax subtypes */
    const XML_PATH_TO_AVATAX_FIXED_TAX_SUBTYPES = 'tax/avatax/field_tax_subtypes';

    /**
     * Get if Fixed Tax is Enabled
     *
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     */
    public function isFixedTaxEnabled($store)
    {
        /** @var OnePica_AvaTax_Helper_LandedCost $helper */
        $helper = Mage::helper('avatax/landedCost');
        return $helper->isLandedCostEnabled($store);
    }

    /**
     * Get tax subtypes
     *
     * @param int|Mage_Core_Model_Store $store
     * @return array
     */
    public function getFixedTaxSubtypes($store = null)
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_TO_AVATAX_FIXED_TAX_SUBTYPES, $store));
    }

    /**
     * @param TaxDetail|array $taxDetail
     * @return bool
     */
    public function isFixedTax($taxDetail)
    {
        $landedCostSubtypes = $this->getFixedTaxSubtypes();

        if ($taxDetail instanceof TaxDetail) {
            return in_array($taxDetail->getTaxSubTypeId(), $landedCostSubtypes);
        }

        if (is_array($taxDetail) && isset($taxDetail['avatax_tax_subtype'])) {
            return in_array($taxDetail['avatax_tax_subtype'], $landedCostSubtypes);
        }

        return false;
    }
}
