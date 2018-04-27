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
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $importDutiesAmount = $this->getTotal()->getLandedCostImportDutiesAmount();
        if ($importDutiesAmount) {
            return parent::_toHtml() . $this->_getImportDutiesHtml($importDutiesAmount);
        }

        return parent::_toHtml();
    }

    /**
     * Get DDP HTML
     *
     * @param float $importDutiesAmount
     * @return string
     * @todo refactor to use template file
     */
    protected function _getImportDutiesHtml($importDutiesAmount)
    {
        $importDutiesAmount = $this->helper('checkout')->formatPrice($importDutiesAmount);
        $contentHtml = sprintf(
            '<tr><td colspan="%s" style="%s" class="a-right">%s</td><td style="%s" class="a-right">%s</td></tr>',
            $this->getColspan(),
            $this->getTotal()->getStyle(),
            $this->__('Customs Duty and Import Tax'),
            $this->getTotal()->getStyle(),
            $importDutiesAmount
        );

        return $contentHtml;
    }
}
