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
     * Initializes the AvaTax SDK with connection settings found in the Admin config.
     *
     * @param int $storeId
     * @return OnePica_AvaTax_Model_Config
     */
    public function init($storeId)
    {
        if (is_null($this->_config)) {
            $this->_config = new ATConfig(
                self::CONFIG_KEY,
                array(
                    'url'     => Mage::helper('avatax/config')->getServiceUrl($storeId),
                    'account' => Mage::helper('avatax/config')->getServiceAccountId($storeId),
                    'license' => Mage::helper('avatax/config')->getServiceKey($storeId),
                    'trace'   => (Mage::helper('avatax')
                        ->getLogMode($storeId) == OnePica_AvaTax_Model_Source_Logmode::DEBUG) ? true : false,
                    'client'  => $this->getClientName()
                )
            );
        }

        return $this;
    }

    /**
     * Generates client name to pass with communications
     *
     * Parts:
     * - MyERP: the ERP that this connector is for (not always applicable)
     * - Majver: version info for the ERP (not always applicable)
     * - MinVer: version info for the ERP (not always applicable)
     * - MyConnector: Name of the OEM�s connector AND the name of the OEM (company)  *required*
     * - Majver: OEM�s connector version *required*
     * - MinVer: OEM�s connector version *required*
     *
     * @example Magento,1.4,.0.1,OP_AvaTax by One Pica,2,0.1
     * @return string
     */
    public function getClientName()
    {
        $mageVersion = Mage::getVersion();
        $mageVerParts = explode('.', $mageVersion, 2);

        $opVersion = Mage::getResourceModel('core/resource')->getDbVersion('avatax_records_setup');
        $opVerParts = explode('.', $opVersion, 2);

        $part = array();
        $part[] = self::CONFIG_KEY;
        $part[] = $mageVerParts[0];
        $part[] = $mageVerParts[1];
        $part[] = self::APP_NAME;
        $part[] = $opVerParts[0];
        $part[] = $opVerParts[1];
        return implode(',', $part);
    }

    /**
     * Returns the AvaTax Address soap connection client.
     *
     * @return AddressServiceSoap
     */
    public function getAddressConnection()
    {
        if (is_null($this->_addressConnection)) {
            $this->_addressConnection = new AddressServiceSoap(self::CONFIG_KEY);
        }
        return $this->_addressConnection;
    }

    /**
     * Returns the AvaTax Address soap connection client.
     *
     * @return AddressServiceSoap
     */
    public function getTaxConnection()
    {
        if (is_null($this->_taxConnection)) {
            $this->_taxConnection = new TaxServiceSoap(self::CONFIG_KEY);
        }
        return $this->_taxConnection;
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
}
