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
class OnePica_AvaTax_Model_Service_Avatax_Config extends OnePica_AvaTax_Model_Service_Abstract_Config
{
    /**
     * The AvaTax TaxServiceSoap object.
     *
     * @var TaxServiceSoap
     */
    protected $_taxConnection = null;

    /**
     * The AvaTax AddressServiceSoap object.
     *
     * @var AddressServiceSoap
     */
    protected $_addressConnection = null;

    /**
     * The AvaTax AccountServiceSoap object.
     *
     * @var AccountServiceSoap
     */
    protected $_accountService = null;

    /**
     * Initializes the AvaTax SDK with connection settings found in the Admin config.
     *
     * @param int $storeId
     * @return OnePica_AvaTax_Model_Config
     */
    public function init($storeId)
    {
        if (null === $this->_config) {
            $cfgValues = array(
                'url'     => Mage::helper('avatax/config')->getServiceUrl($storeId),
                'account' => Mage::helper('avatax/config')->getServiceAccountId($storeId),
                'license' => Mage::helper('avatax/config')->getServiceKey($storeId),
                'trace'   => (Mage::helper('avatax')
                                  ->getLogMode($storeId) == OnePica_AvaTax_Model_Source_Logmode::DEBUG) ? true : false,
                'client'  => $this->getClientName()
            );

            if ($this->_getLandedCostHelper()->isLandedCostEnabled($storeId)) {
                $taxServiceKey = 'taxService';
                $cfgDefault = new ATConfig('Default');
                $cfgValues[$taxServiceKey] = $cfgDefault->taxService . '/v2';
                $cfgValues['isLandedCostEnabled'] = true;
            }

            $this->_config = new ATConfig(self::CONFIG_KEY, $cfgValues);
        }

        return $this;
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
     * Returns the AvaTax Address soap connection client.
     *
     * @return AddressServiceSoap
     */
    public function getAddressConnection()
    {
        if (null === $this->_addressConnection) {
            $this->_addressConnection = new AddressServiceSoap(self::CONFIG_KEY);
        }

        return $this->_addressConnection;
    }

    /**
     * Returns the AvaTax Address soap connection client.
     *
     * @return TaxServiceSoap
     */
    public function getTaxConnection()
    {
        if (null === $this->_taxConnection) {
            $this->_taxConnection = new TaxServiceSoap(self::CONFIG_KEY);
        }

        return $this->_taxConnection;
    }

    /**
     * Returns the AvaTax Address soap connection client.
     *
     * @return AccountServiceSoap
     */
    public function getAccountConnection()
    {
        if (null === $this->_accountService) {
            $this->_accountService = new AccountServiceSoap(self::CONFIG_KEY);
        }

        return $this->_accountService;
    }

    /**
     * Returns true if the admin is configured to normalize addresses.
     *
     * @return boolean
     */
    public function normalizeAddress()
    {
        return Mage::helper('avatax/config')->getNormalizeAddress(Mage::app()->getStore());
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

    /**
     * Returns the company code to use from the AvaTax dashboard
     *
     * @param       $storeId
     * @param array $params
     * @return array
     */
    public function getAccountCompanies($storeId, $params = array())
    {
        if ($params) {
            $cfgValues = array(
                'url'     => $params['url'],
                'account' => $params['account'],
                'license' => $params['license'],
                'trace'   => false,
                'client'  => $this->getClientName()
            );

            $this->_config = new ATConfig(self::CONFIG_KEY, $cfgValues);
        } elseif ($this->_config === null) {
            $this->init($storeId);
        }

        $companies = $this->getAccountConnection()->CompanyFetch('')->getValidCompanies();

        return (array)$companies;
    }

    /**
     * Returns the detail level config
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getDetailLevel($store = null)
    {
        return Mage::helper('avatax/config')->getDetailLevel($store);
    }
}
