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

        /*
         * we need to set empty collection because of validation exception when something is wrong
         * so _allCustomerCertificates is not set and we try to load collection from service again and again
         */
        $this->_allCustomerCertificates = new Varien_Data_Collection();

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

        $this->_allCustomerCertificates = $this->processResponse($response, false);

        return $this->_allCustomerCertificates;
    }

    /**
     * Retrieve certificate preview image
     *
     * @param string                               $id
     * @param  null|bool|int|Mage_Core_Model_Store $store
     * @return string
     * @throws \Mage_Core_Exception
     */
    public function getCertificateImage($id, $store = null)
    {
        $client = $this->_getServiceConfig()->getClient($store);

        $response = $client->downloadCertificateImage(
            $this->_getServiceCompany()->getCurrentCompanyId($store), $id, 1, 'Jpeg'
        );

        return $response;
    }

    /**
     * Retrieve certificate preview image
     *
     * @param string                               $id
     * @param  null|bool|int|Mage_Core_Model_Store $store
     * @return string
     * @throws \Mage_Core_Exception
     */
    public function getCertificatePdf($id, $store = null)
    {
        $client = $this->_getServiceConfig()->getClient($store);

        $response = $client->downloadCertificateImage(
            $this->_getServiceCompany()->getCurrentCompanyId($store), $id, null, null
        );

        return $response;
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

        $this->_certificates = $this->processResponse($response);

        return $this->_certificates;
    }

    /**
     * @param  int                                 $id
     * @param  string                              $customerId
     * @param  null|bool|int|Mage_Core_Model_Store $store
     * @return bool
     * @throws \Mage_Core_Exception
     */
    public function deleteCertificate($id, $customerId, $store = null)
    {
        $client = $this->_getServiceConfig()->getClient($store);

        $customerCodesInCertificate = $this->getCustomerCodesFromCertificate($id, $store);
        $customerCodesToDelete = array($customerId);

        $this->unlinkCustomersFromCertificate($id, $customerCodesToDelete, $store);

        $customersLeft = array_diff($customerCodesInCertificate, $customerCodesToDelete);

        if (!$customersLeft) {
            $client->deleteCertificate($this->_getServiceCompany()->getCurrentCompanyId($store), $id);
        }

        return true;
    }

    /**
     * @param  int                                 $id
     * @param  array                               $customerIds
     * @param  null|bool|int|Mage_Core_Model_Store $store
     * @return \Varien_Data_Collection
     * @throws \Mage_Core_Exception
     */
    public function unlinkCustomersFromCertificate($id, $customerIds, $store = null)
    {
        $client = $this->_getServiceConfig()->getClient($store);

        $model = new Avalara\AvaTaxRestV2\LinkCustomersModel;
        $model->customers = $customerIds;
        $response = $client->unlinkCustomersFromCertificate(
            $this->_getServiceCompany()->getCurrentCompanyId($store), $id, $model
        );

        return $this->processResponse($response);
    }

    /**
     * @param  int                                 $id
     * @param  null|bool|int|Mage_Core_Model_Store $store
     * @return \Varien_Data_Collection
     * @throws \Mage_Core_Exception
     */
    public function getCustomersFromCertificate($id, $store = null)
    {
        $client = $this->_getServiceConfig()->getClient($store);

        $response = $client->listCustomersForCertificate(
            $this->_getServiceCompany()->getCurrentCompanyId($store), $id, $this->getInclude()
        );

        return $this->processResponse($response);
    }

    /**
     * @param  int                                 $id
     * @param  null|bool|int|Mage_Core_Model_Store $store
     * @return array
     * @throws \Mage_Core_Exception
     */
    public function getCustomerCodesFromCertificate($id, $store = null)
    {
        $customerCodes = array();

        /** @var \Varien_Object $customer */
        foreach ($this->getCustomersFromCertificate($id, $store)->getItems() as $customer) {
            $customerCodes[] = $customer->getData('customerCode');
        }

        return $customerCodes;
    }

    /**
     * @param string                              $customerCode
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @param bool                                $throwError
     * @return \Varien_Data_Collection
     * @throws \Mage_Core_Exception
     */
    public function getCustomer($customerCode, $store = null, $throwError = true)
    {
        $client = $this->_getServiceConfig()->getClient($store);

        $response = $client->getCustomer($this->_getServiceCompany()->getCurrentCompanyId($store), $customerCode, null);

        $exception = $this->validateResponse($response, $throwError);

        return $exception ? $exception : Mage::getModel('avataxar2/service_avatax_model_customer', (array)$response);
    }

    /**
     * @param      $customer \OnePica_AvaTaxAr2_Model_Service_Avatax_Model_Customer
     * @param null $store
     * @return Varien_Object
     * @throws Mage_Core_Exception
     */
    public function createCustomer($customer, $store = null)
    {
        $avaCustomer = $customer->toAvalaraCustomerModel();

        $client = $this->_getServiceConfig()->getClient($store);

        $response = $client->createCustomers(
            $this->_getServiceCompany()->getCurrentCompanyId($store), array($avaCustomer)
        );

        $this->validateResponse($response);

        return new \Varien_Object((array)$response);
    }

    /**
     * @param      $customer \OnePica_AvaTaxAr2_Model_Service_Avatax_Model_Customer
     * @param null $store
     * @return Varien_Object
     * @throws Mage_Core_Exception
     */
    public function updateCustomer($customer, $store = null)
    {
        $avaCustomer = $customer->toAvalaraCustomerModel();

        $client = $this->_getServiceConfig()->getClient($store);

        $response = $client->updateCustomer(
            $this->_getServiceCompany()->getCurrentCompanyId($store), $avaCustomer->customerCode, $avaCustomer
        );

        $this->validateResponse($response);

        return new \Varien_Object((array)$response);
    }

    /**
     * @param null $store
     * @return \Varien_Object
     * @throws \Mage_Core_Exception
     */
    public function getCompanyInfo($store = null)
    {
        $client = $this->_getServiceConfig()->getClient($store);

        $response = $client->getCompany($this->_getServiceCompany()->getCurrentCompanyId($store), null);

        $this->validateResponse($response);

        return new \Varien_Object((array)$response);
    }

    /**
     * @param string $companyId
     * @param string $customerCode
     * @param string $email
     * @param null   $store
     * @return \Varien_Object
     */
    public function sendCertExpressInvite($companyId, $customerCode, $email, $store = null)
    {
        $client = $this->_getServiceConfig()->getClient($store);

        $letterInfo = array(
            'recipient'        => $email,
            'coverLetterTitle' => 'STANDARD_REQUEST',
            'deliveryMethod'   => 'Email'
        );

        $response = $client->sendCoverLetter($companyId, $customerCode, 'certexpressinvites', $letterInfo);

        $this->validateResponse($response);

        return new \Varien_Object((array)$response);
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
