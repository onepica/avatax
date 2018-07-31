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
 * Class OnePica_AvaTaxAr2_Block_Documents_Grid_Form
 */
class OnePica_AvaTaxAr2_Block_Documents_Grid_Form extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/postForm');
    }

    /**
     * @return mixed
     */
    public function getCustomerNumberOrGenerate()
    {
        return $this->_getHelper()->getCustomerNumberOrGenerate($this->_getSession()->getCustomer());
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avataxar2');
    }

    /**
     * @return \Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
