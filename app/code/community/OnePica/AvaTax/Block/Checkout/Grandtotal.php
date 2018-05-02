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
 * Grand Total and Landed Cost DAP Renderer
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Checkout_Grandtotal extends Mage_Checkout_Block_Total_Default
{
    /**
     * Render block HTML
     *
     * @return string
     * @throws \Varien_Exception
     */
    protected function _toHtml()
    {
        $DAPMessage = $this->getTotal()->getLandedCostMessage();
        if ($DAPMessage) {
            return parent::_toHtml() . $this->_getDAPHtml($DAPMessage);
        }

        return parent::_toHtml();
    }

    /**
     * Get DAP HTML
     *
     * @param string $message
     * @return string
     * @todo refactor to use template file
     */
    protected function _getDAPHtml($message)
    {
        $dapMessage = $message;
        $contentHtml = sprintf('<tr><td colspan="%s" style="%s font-size: 12px; text-align: right">%s</td></tr>',
            $this->getColspan() + 1,
            $this->getTotal()->getStyle(),
            $dapMessage
        );

        return $contentHtml;
    }
}
