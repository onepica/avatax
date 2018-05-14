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
 * Total Tax and Landed Cost DDP Renderer
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Checkout_Tax extends Mage_Tax_Block_Checkout_Tax
{
    /**
     * Template used in the block
     *
     * @var string
     */
    protected $_template = 'onepica/avatax/checkout/tax.phtml';

    /**
     * @return float|null
     * @throws \Varien_Exception
     */
    public function getLandedCostAmount()
    {
        if ($this->_getLandedCostHelper()->isLandedCostEnabled($this->getStore())) {
            return (float)$this->getTotal()->getLandedCostAmount();
        }

        return null;
    }

    /**
     * @return float
     * @throws \Varien_Exception
     */
    public function getTaxAmount()
    {
        return $this->getTotal()->getValue() - $this->getLandedCostAmount();
    }

    /**
     * @return string
     */
    public function getLandedCostTitle()
    {
        return $this->__('Customs Duty and Import Tax');
    }

    /**
     * Show Landed Cost block or no
     *
     * @return bool
     * @throws \Mage_Core_Model_Store_Exception
     * @throws \Varien_Exception
     */
    public function showLandedCostBlock()
    {
        $result = false;
        $storeCountryId = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());

        /* Show when LC is enabled */
        if ($this->_getLandedCostHelper()->isLandedCostEnabled($this->getStore()->getId())) {
            $result = true;
        }

        /* don't show when destination country same as origin and zero */
        if ($storeCountryId === $this->getTotal()->getAddress()->getCountryId() && $this->getLandedCostAmount() == 0) {
            $result = false;
        }

        return $result;
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_LandedCost
     */
    protected function _getLandedCostHelper()
    {
        return Mage::helper('avatax/landedCost');
    }
}
