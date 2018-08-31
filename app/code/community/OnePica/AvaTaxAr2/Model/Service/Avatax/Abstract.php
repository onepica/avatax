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

use Avalara\AvaTaxRestV2\SeverityLevel;

/**
 * Avatax service abstract model
 *
 * @method getService() OnePica_AvaTax_Model_Service_Avatax
 * @method $this setLandedCostMode(string $mode)
 * @method string getLandedCostMode()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTaxAr2_Model_Service_Avatax_Abstract extends Varien_Object
{
    /** @var null|string $_include */
    protected $_include = null;

    /** @var null|string $_filter */
    protected $_filter = null;

    /** @var null|string $_top */
    protected $_top = null;

    /** @var null|string $_skip */
    protected $_skip = null;

    /** @var null|string $_orderBy */
    protected $_orderBy = null;

    /**
     * Logs a debug message
     *
     * @param string          $type
     * @param string          $request    the request string
     * @param string          $result     the result string
     * @param int             $storeId    id of the store the call is make for
     * @param mixed           $additional any other info
     * @param \TaxServiceSoap $connection for logging soap request/response
     * @param Varien_Object   $quoteData
     * @return $this
     * @throws \Varien_Exception
     */
    protected function _log(
        $type,
        $request,
        $result,
        $storeId = null,
        $additional = null,
        $connection = null,
        $quoteData = null
    ) {
//        if ($result->getResultCode() == SeverityLevel::C_SUCCESS) {
//            switch ($this->_getHelper()->getLogMode($storeId)) {
//                case OnePica_AvaTax_Model_Source_Logmode::ERRORS:
//                    return $this;
//                    break;
//                case OnePica_AvaTax_Model_Source_Logmode::NORMAL:
//                    $additional = null;
//                    break;
//            }
//        }

        $soapRequest = null;
        $soapRequestHeaders = null;
        $soapResponse = null;
        $soapResponseHeaders = null;

        if ($connection) {
            $soapRequest = $connection->__getLastRequest();
            $soapRequestHeaders = $connection->__getLastRequestHeaders();
            $soapResponse = $connection->__getLastResponse();
            $soapResponseHeaders = $connection->__getLastResponseHeaders();
        }

        $quoteId = $quoteData ? $quoteData->getQuoteId() : null;
        $quoteAddressId = $quoteData ? $quoteData->getQuoteAddressId() : null;

        if (in_array($type, $this->_getAvataxHelper()->getLogType($storeId))) {
            Mage::getModel('avatax_records/log')
                ->setStoreId($storeId)
                ->setLevel($result->getResultCode())
                ->setType($type)
                ->setRequest(print_r($request, true))
                ->setResult(print_r($result, true))
                ->setAdditional($additional)
                ->setSoapRequest($soapRequest)
                ->setSoapRequestHeaders($soapRequestHeaders)
                ->setSoapResult($soapResponse)
                ->setSoapResultHeaders($soapResponseHeaders)
                ->setQuoteId($quoteId)
                ->setQuoteAddressId($quoteAddressId)
                ->save();
        }

        return $this;
    }

    /**
     * @return \OnePica_AvaTax_Helper_Data
     */
    protected function _getAvataxHelper(){
        return Mage::helper('avatax');
    }

    /**
     * Returns the AvaTax session.
     *
     * @return OnePica_AvaTax_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('avatax/session');
    }

    /**
     * Sets the company code on the request
     *
     * @param int|null $storeId
     * @return $this
     * @throws \Varien_Exception
     */
    protected function _setCompanyCode($storeId = null)
    {
        $config = Mage::getSingleton('avatax/service_avatax_config');
        $this->_request->setCompanyCode($config->getCompanyCode($storeId));

        return $this;
    }

    /**
     * @param \stdClass|\Avalara\AvaTaxRestV2\FetchResult $response
     * @return \Varien_Data_Collection
     * @throws \Mage_Core_Exception
     * @throws \Exception
     */
    public function processResponse($response, $throwException = true)
    {
        $this->validateResponse($response, $throwException);

        $items = array();

        if (is_object($response) && isset($response->value)) {
            $items = (array)$response->value;
        }

        $itemsCollection = new Varien_Data_Collection();

        foreach ($items as $item) {
            $collectionItem = new Varien_Object();
            $collectionItem->setData((array)$item);

            $itemsCollection->addItem($collectionItem);
        }

        return $itemsCollection;
    }

    public function validateResponse($response, $throwError = true)
    {
        $exception = null;
        if(OnePica_AvaTaxAr2_Exception_Response::isResponseError($response)) {

            $exception = new \OnePica_AvaTaxAr2_Exception_Response($response);
            if ($throwError) {
                Mage::throwException($exception->getMessage());

                //throw $exception;
            }
        }

        return $exception;
    }

    /**
     * @return null|string
     */
    public function getInclude()
    {
        return $this->_include;
    }

    /**
     * @param null|string $include
     */
    public function setInclude($include)
    {
        $this->_include = $include;
    }

    /**
     * @return null|string
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * @param null|string $filter
     */
    public function setFilter($filter)
    {
        $this->_filter = $filter;
    }

    /**
     * @return null|string
     */
    public function getTop()
    {
        return $this->_top;
    }

    /**
     * @param null|string $top
     */
    public function setTop($top)
    {
        $this->_top = $top;
    }

    /**
     * @return null|string
     */
    public function getSkip()
    {
        return $this->_skip;
    }

    /**
     * @param null|string $skip
     */
    public function setSkip($skip)
    {
        $this->_skip = $skip;
    }

    /**
     * @return null|string
     */
    public function getOrderBy()
    {
        return $this->_orderBy;
    }

    /**
     * @param null|string $orderBy
     */
    public function setOrderBy($orderBy)
    {
        $this->_orderBy = $orderBy;
    }
}
