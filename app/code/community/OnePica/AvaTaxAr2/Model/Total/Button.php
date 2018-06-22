<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * Class OnePica_AvaTaxAr2_Model_Total_Button
 */
class OnePica_AvaTaxAr2_Model_Total_Button extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_code = 'certcapture_button';

    /**
     * Fetch (Retrieve data as array)
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return \OnePica_AvaTaxAr2_Model_Total_Button
     * @throws \Mage_Core_Exception
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if ($this->_getConfigHelper()->isEnabled()) {
            $address->addTotal(array('code' => $this->getCode()));
        }

        return $this;
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avataxar2/config');
    }
}
