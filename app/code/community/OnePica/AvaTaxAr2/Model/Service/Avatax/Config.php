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
 * The AvaTax Config Model, which registers config settings with the AvaTax SDK
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Service_AvaTax_Config extends Varien_Object
{
    /**
     * @var \Avalara\AvaTaxRestV2\AvaTaxClient
     */
    protected $_client = null;

    /**
     * @return \Avalara\AvaTaxRestV2\AvaTaxClient|null
     */
    public function getClient()
    {
        if(null === $this->_client) {
            $this->_client = new Avalara\AvaTaxRestV2\AvaTaxClient('phpTestApp', '1.0', 'travis-ci', 'sandbox');
            $this->_client->withLicenseKey('2000226328X', 'B8B71004DD258CB1');
        }

        return $this->_client;
    }

    /**
     * Get client name to pass with communications
     *
     * @example Magento,1.4,.0.1,OP_AvaTax by One Pica,2,0.1
     * @return string
     */
    public function getClientName()
    {
        return $this->_getHelper()->getClientName();
    }

    /**
     * Returns the company code to use from the AvaTax dashboard
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCompanyCode($store = null)
    {
        return Mage::helper('avatax/config')->getCompanyCode($store);
    }
}
