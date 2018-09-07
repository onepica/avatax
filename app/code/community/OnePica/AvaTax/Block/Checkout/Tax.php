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
        return $this->getTotal()->getValue() - $this->getLandedCostAmount() - $this->getFixedTaxAmount();
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
     * @throws \Varien_Exception
     */
    public function showLandedCostBlock()
    {
        $destinationCountry = $this->getTotal()->getAddress()->getCountryId();

        return $this->_getLandedCostHelper()->getLandedCostMode($destinationCountry, $this->getStore());
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


    /**
     * Show Fixed Tax block or no
     *
     * @return bool
     * @throws \Varien_Exception
     */
    public function showFixedTaxBlock()
    {
        return $this->getFixedTaxAmount() != 0;
    }

    /**
     * @return string
     */
    public function getFixedTaxTitle()
    {
        return $this->__('Fixed Tax');
    }

    /**
     * @return float|null
     * @throws \Varien_Exception
     */
    public function getFixedTaxAmount()
    {
        if ($this->_getFixedTaxHelper()->isFixedTaxEnabled($this->getStore())) {
            return (float)$this->getTotal()->getFixedTaxAmount();
        }

        return null;
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_FixedTax
     */
    protected function _getFixedTaxHelper()
    {
        return Mage::helper('avatax/fixedTax');
    }
}
