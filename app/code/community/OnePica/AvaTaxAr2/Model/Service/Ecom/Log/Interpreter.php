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
 * Class OnePica_AvaTaxAr2_Model_Service_Ecom_Log_Interpreter
 */
class OnePica_AvaTaxAr2_Model_Service_Ecom_Log_Interpreter extends Varien_Object
{
    /**
     * OnePica_AvaTaxAr2_Model_Service_Ecom_Log_Interpreter constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param \Avalara\AvaTaxRestV2\AvaTaxClient $client
     * @param                                    $store
     * @return $this
     */
    public function interpret(\Avalara\AvaTaxEcomSDK\ClientBase $client, $store)
    {
        try {
            /** @var Zend_Http_Client $httpClient */
            $httpClient = $client->getClient();
            $storeId = Mage::app()->getStore($store)->getId();
            $logType = $this->interpretTransactionType($httpClient);
            $lastJsonModelEncoded = $client->getLastJsonModelEncoded();
            $lastJsonModelDecoded = $client->getLastJsonModelDecoded();
            $lastRequest = $httpClient->getLastRequest();
            $lastResponse = $httpClient->getLastResponse()->asString();
            $lastResponse = $lastResponse . PHP_EOL . PHP_EOL . "Rounting time : " . $client->getLastRoutingTime();
            $log = Mage::getModel('avatax_records/log')
                       ->setStoreId($storeId)
                       ->setType($logType)
                       ->setLevel($httpClient->getLastResponse()->isSuccessful() ? 'Success' : 'Error')
                       ->setRequest(print_r($lastJsonModelEncoded, true))
                       ->setSoapRequest($lastRequest)
                       ->setResult(print_r($lastJsonModelDecoded, true))
                       ->setSoapResult($lastResponse);
            $log->save();
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
            case $this->_checkRegex($uri, '/\/auth\/get-token/'):
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::ECOM_PING;
                break;
            default:
                $result = OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::ECOM_COMMON;
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
}
