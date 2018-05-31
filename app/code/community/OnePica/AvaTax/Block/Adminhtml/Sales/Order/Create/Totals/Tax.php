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
 * Class OnePica_AvaTax_Block_Adminhtml_Sales_Order_Create_Totals_Tax
 */
class OnePica_AvaTax_Block_Adminhtml_Sales_Order_Create_Totals_Tax
    extends Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default
{
    protected $_template = 'onepica/avatax/sales/order/create/totals/tax.phtml';

    /**
     * @return float|null
     * @throws \Varien_Exception
     */
    public function getLandedCostAmount()
    {
        if ($this->_getLandedCostHelper()->isLandedCostEnabled($this->getStore())) {
            $amount = 0;
            foreach ($this->getLandedCostItems() as $landedCostItem) {
                $amount += $landedCostItem['amount'];
            }

            return $amount;
        }

        return null;
    }

    /**
     * @return array
     */
    public function getLandedCostItems()
    {
        $items = array();
        foreach ($this->getTotal()->getFullInfo() as $info) {
            if ($this->_getLandedCostHelper()->isLandedCostTax($info)) {
                $items[] = $info;
            }
        }

        return $items;
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
}
