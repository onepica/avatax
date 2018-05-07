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
 * Avatax service tax model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax_Tax extends OnePica_AvaTax_Model_Service_Avatax_Abstract
{
    /**
     * Sends a request to the Avatax server
     *
     * @param int $storeId
     * @param Varien_Object|null $quoteData
     * @return mixed
     */
    protected function _send($storeId, $quoteData = null)
    {
        /** @var OnePica_AvaTax_Model_Config $config */
        $config = $this->getServiceConfig();
        $connection = $config->getTaxConnection();
        $result = null;
        $message = null;

        try {
            $result = $connection->getTax($this->_request);
        } catch (Exception $exception) {
            $message = new Message();
            $message->setSummary($exception->getMessage());
        }

        if (!isset($result) || !is_object($result) || !$result->getResultCode()) {
            $actualResult = $result;
            $result = new Varien_Object();
            $result->setResultCode(SeverityLevel::$Exception)
                ->setActualResult($actualResult)
                ->setMessages(array($message));
        }

        $this->_log(
            OnePica_AvaTax_Model_Source_Avatax_Logtype::GET_TAX,
            $this->_request,
            $result,
            $storeId,
            $config->getParams(),
            $connection,
            $quoteData
        );

        return $result;
    }

    /**
     * @param Varien_Object $data
     *
     * @return $this
     */
    protected function _newLinePrepareProduct($data)
    {
        return $this;
    }

    /**
     * @param Varien_Object $data
     *
     * @return $this
     */
    protected function _newLineMakeAdditionalProcessingForLine($data)
    {
        return $this;
    }
}
