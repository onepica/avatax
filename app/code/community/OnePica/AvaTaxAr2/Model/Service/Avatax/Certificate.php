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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use Avalara\AvaTaxRestV2\SeverityLevel;

/**
 * The AvaTax Certificate model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Service_Avatax_Certificate extends OnePica_AvaTaxAr2_Model_Service_Avatax_Abstract
{
    /** @var array $_allCustomerCertificates */
    protected $_allCustomerCertificates = array();

    /** @var array $_certificates */
    protected $_certificates = array();

    /**
     * Retrieves all customer certificates
     *
     * @param string                               $customerCode
     * @param  null|bool|int|Mage_Core_Model_Store $store
     * @return array
     * @throws \Mage_Core_Exception
     */
    public function getAllCustomerCertificates($customerCode, $store = null)
    {
        if ($this->_allCustomerCertificates) {
            return $this->_allCustomerCertificates;
        }

        $client = $this->_getServiceConfig()->getClient($store);

        $response = $client->listCertificatesForCustomer(
            $this->_getServiceCompany()->getCurrentCompanyId($store),
            $customerCode,
            $this->getInclude(),
            $this->getFilter(),
            $this->getTop(),
            $this->getSkip(),
            $this->getOrderBy()
        );

        $this->_allCustomerCertificates = $this->validateResponse($response);

        return $this->_allCustomerCertificates;
    }

    /**
     * Retrieves all  certificates
     *
     * @param  null|bool|int|Mage_Core_Model_Store $store
     * @return array
     * @throws \Mage_Core_Exception
     */
    public function getCertificates($store = null)
    {
        if ($this->_certificates) {
            return $this->_certificates;
        }

        $client = $this->_getServiceConfig()->getClient($store);

        $response = $client->queryCertificates(
            $this->_getServiceCompany()->getCurrentCompanyId($store),
            $this->getInclude(),
            $this->getFilter(),
            $this->getTop(),
            $this->getSkip(),
            $this->getOrderBy()
        );

        $this->_certificates = $this->validateResponse($response);

        return $this->_certificates;
    }

    /**
     * @return \OnePica_AvaTaxAr2_Model_Service_AvaTax_Config
     */
    protected function _getServiceConfig()
    {
        return Mage::getSingleton('avataxar2/service_avatax_config');
    }

    /**
     * @return \OnePica_AvaTaxAr2_Model_Service_Avatax_Company
     */
    protected function _getServiceCompany()
    {
        return Mage::getSingleton('avataxar2/service_avatax_company');
    }
}
