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
 *
 *
 * Class OnePica_AvaTaxAr2_Model_Service_Avatax_Log_Interpreter
 */
class OnePica_AvaTaxAr2_Model_Service_Avatax_Log_Interpreter extends Varien_Object
{
    /**
     * OnePica_AvaTaxAr2_Model_Service_Avatax_Log_Interpreter constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param \Avalara\AvaTaxRestV2\AvaTaxClient         $client
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     * @return $this
     */
    public function interpret(\Avalara\AvaTaxRestV2\AvaTaxClient $client, $store)
    {
        try {
            if ($this->getHelperAvaTax()->getLogMode($store)) {
                $httpClient = $client->getClient();
                $logType = $this->interpretTransactionType($httpClient);

                if (in_array($logType, $this->getHelperAvaTax()->getLogType($store))) {
                    $storeId = Mage::app()->getStore($store)->getId();

                    $lastJsonModelEncoded = $client->getLastJsonModelEncoded();
                    $lastJsonModelDecoded = $client->getLastJsonModelDecoded();

                    $lastRequest = $httpClient->getLastRequest();
                    $lastRequest = isset($lastRequest) ? $lastRequest : $httpClient->getUri(true);

                    $lastResponse = $httpClient->getLastResponse();
                    $lastResponseAsString =  $lastResponse instanceof \Zend_Http_Response ? $lastResponse->asString() : null;
                    $lastResponseAsString = $lastResponseAsString . PHP_EOL . PHP_EOL . "Routing time : " . $client->getLastRoutingTime();

                    $level = 'Error';
                    if ($lastResponse instanceof \Zend_Http_Response) {
                        $level = $lastResponse->isSuccessful() ? 'Success' : 'Error';
                    }

                    $log = Mage::getModel('avatax_records/log')
                               ->setStoreId($storeId)
                               ->setType($logType)
                               ->setLevel($level)
                               ->setRequest(print_r($lastJsonModelEncoded, true))
                               ->setSoapRequest($lastRequest)
                               ->setResult(print_r($lastJsonModelDecoded, true))
                               ->setSoapResult($lastResponseAsString);
                    $log->save();
                }
            }
        } catch (Exception $ex) {
            Mage::logException($ex);
        }

        return $this;
    }

    /**
     * @param Zend_Http_Client $httpClient
     * @return string
     */
    protected function interpretTransactionType(Zend_Http_Client $httpClient)
    {
        $uri = $httpClient->getUri(true);

        switch ($uri) {
            /* AvaTaxClient::ping */
            case $this->_checkRegex($uri, '/\/utilities\/ping/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_PING;
                break;
            /* AvaTaxClient::listCertificatesForCustomer */
            case $this->_checkRegex($uri, '/\/companies\/[^\/]*\/customers\/[^\/]*\/certificates/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_CUSTOMER_CERTIFICATE;
                break;
            /* AvaTaxClient::createCertExpressInvitation */
            case $this->_checkRegex($uri, '/\/companies\/[^\/]*\/customers\/[^\/]*\/certexpressinvites/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_CUSTOMER_CERT_EXPRESS_INVITE;
                break;
            /* AvaTaxClient::getCustomer | AvaTaxClient::updateCustomer | AvaTaxClient::deleteCustomer */
            case $this->_checkRegex($uri, '/\/companies\/[^\/]*\/customers\/[^\/]+/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_CUSTOMER;
                break;
            /* AvaTaxClient::queryCustomers | AvaTaxClient::createCustomers */
            case $this->_checkRegex($uri, '/\/companies\/[^\/]*\/customers/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_CUSTOMERS;
                break;
            /* AvaTaxClient::unlinkCustomersFromCertificate */
            case $this->_checkRegex($uri, '/\/companies\/[^\/]*\/certificates\/[^\/]*\/customers\/unlink/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_CUSTOMER_CERTIFICATE_UNLINK;
                break;
            /* AvaTaxClient::listCustomersForCertificate */
            case $this->_checkRegex($uri, '/\/companies\/[^\/]*\/certificates\/[^\/]*\/customers/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_COMPANY_CERTIFICATE_CUSTOMERS;
                break;
            /* AvaTaxClient::downloadCertificateImage | AvaTaxClient::uploadCertificateImage */
            case $this->_checkRegex($uri, '/\/companies\/[^\/]*\/certificates\/[^\/]*\/attachment/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_COMPANY_CERTIFICATE_ATTACHMENT;
                break;
            /* AvaTaxClient::getCertificate | AvaTaxClient::updateCertificate | AvaTaxClient::deleteCertificate */
            case $this->_checkRegex($uri, '/\/companies\/[^\/]*\/certificates\/[^\/]+/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_COMPANY_CERTIFICATE;
                break;
            /* AvaTaxClient::queryCertificates | AvaTaxClient::createCertificates */
            case $this->_checkRegex($uri, '/\/companies\/[^\/]*\/certificates/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_COMPANY_CERTIFICATES;
                break;
            /* AvaTaxClient::queryCompany */
            case $this->_checkRegex($uri, '/\/companies\/[^\/]+/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_COMPANY;
                break;
            /* AvaTaxClient::queryCompanies */
            case $this->_checkRegex($uri, '/\/companies\?.+/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_COMPANIES;
                break;
            default:
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::REST_COMMON;
                break;
        }

        return $result;
    }

    /**
     * @param $stringToCheck
     * @param $regexRule
     * @return bool
     */
    protected function _checkRegex($stringToCheck, $regexRule)
    {
        return $this->getHelper()->checkRegex($stringToCheck, $regexRule);
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper('avataxar2');
    }

    /**
     * @return \OnePica_AvaTax_Helper_Data
     */
    protected function getHelperAvaTax()
    {
        return Mage::helper('avatax');
    }
}
