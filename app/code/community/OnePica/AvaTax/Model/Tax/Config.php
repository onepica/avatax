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
 * Configuration paths storage
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Tax_Config extends Mage_Tax_Model_Config
{
    /**
     * Get configuration setting "Apply Discount On Prices Including Tax" value
     * Always apply discount first since AvaTax does not support line-level item discount amounts
     *
     * @param   null|int $store
     * @return  bool
     */
    public function discountTax($store = null)
    {
        if (Mage::helper('avatax')->isAvataxEnabled($store)) {
            return false;
        }

        return parent::discountTax($store);
    }
}
