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
 * Model to calculate grand total or an order
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Sales_Quote_Address_Total_Grand extends Mage_Sales_Model_Quote_Address_Total_Grand
{
    /**
     * Add grand total information And Landed Cost DAP message to address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Grand
     * @throws \Varien_Exception
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(
            array(
                'code'                => $this->getCode(),
                'title'               => Mage::helper('sales')->__('Grand Total'),
                'value'               => $address->getGrandTotal(),
                'landed_cost_message' => $address->getLandedCostMessage(),
                'area'                => 'footer',
            )
        );

        return $this;
    }
}
