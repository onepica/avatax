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

/**
 * The AvaTax Ping model
 * @class OnePica_AvaTax_Model_Service_Avatax16_Ping
 *
 * @method getService() OnePica_AvaTax_Model_Service_Avatax16
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax16_Ping extends OnePica_AvaTax_Model_Service_Avatax16_Abstract
{
    /**
     * Tries to ping AvaTax service with provided credentials
     *
     * @param int $storeId
     * @return bool|array
     */
    public function ping($storeId = null)
    {
        /** @var OnePica_AvaTax_Model_Service_Avatax16_Config $config */
        $config = $this->getService()->getServiceConfig();
        $connection = $config->getTaxConnection();
        $result = null;
        $message = array();
        try {
            /** @var OnePica_AvaTax16_AddressResolution_PingResponse $result */
            $result = $connection->ping();
        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        if (!isset($result) || !is_object($result) || !$result->getHasError()) {
            $actualResult = $result;
            $result = new Varien_Object();
            $result->setHasError($result->getHasError());
            $result->setActualResult($actualResult);
            $result->setMessage($message);
        }
        $this->_log(
            OnePica_AvaTax_Model_Source_Avatax_Logtype::PING,
            new stdClass(),
            $result,
            $storeId,
            $config->getParams()
        );
        if ($result->getHasError()) {
            if (is_array($result->getErrors())) {
                foreach ($result->getErrors() as $message) {
                    $message[] = $this->__($message->getSummary());
                }
                $message = implode(' ', $message);
            }
        }
        return (!$result->getHasError()) ? true : $message;
    }
}
