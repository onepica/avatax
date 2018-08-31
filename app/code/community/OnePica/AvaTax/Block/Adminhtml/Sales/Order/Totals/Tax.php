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
 * Class OnePica_AvaTax_Block_Adminhtml_Sales_Order_Totals_Tax
 */
class OnePica_AvaTax_Block_Adminhtml_Sales_Order_Totals_Tax extends Mage_Adminhtml_Block_Sales_Order_Totals_Tax
{
    /**
     * Template used in the block
     *
     * @var string
     */
    protected $_templateAvatax = 'onepica/avatax/sales/order/totals/tax.phtml';

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->setTemplate($this->_templateAvatax);

        return $this;
    }

    /**
     * @return float
     * @throws \Varien_Exception
     */
    public function getLandedCostAmount()
    {
        return $this->getSource()->getAvataxLandedCostImportDutiesAmount();
    }

    /**
     * @return float
     * @throws \Varien_Exception
     */
    public function getLandedCostBaseAmount()
    {
        return $this->getSource()->getBaseAvataxLandedCostImportDutiesAmount();
    }

    /**
     * @return float
     * @throws \Varien_Exception
     */
    public function getSourceTaxAmount()
    {
        return $this->getSource()->getTaxAmount() - $this->getLandedCostAmount();
    }

    /**
     * @return float
     * @throws \Varien_Exception
     */
    public function getSourceBaseTaxAmount()
    {
        return $this->getSource()->getBaseTaxAmount() - $this->getLandedCostBaseAmount();
    }
}
