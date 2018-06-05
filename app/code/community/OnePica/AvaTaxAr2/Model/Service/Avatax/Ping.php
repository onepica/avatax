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
 * The AvaTax Ping model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Service_Avatax_Ping extends OnePica_AvaTaxAr2_Model_Service_Avatax_Abstract
{
    /**
     * Tries to ping AvaTax service with provided credentials
     *
     * @param int $storeId
     * @return bool|array
     */
    public function ping($storeId = null)
    {
        /** @var OnePica_AvaTaxAr2_Model_Service_AvaTax_Config $config */
        $config = Mage::getModel('avataxar2/service_avatax_config');
        $client = $config->getClient();

        $result = null;
        $pingResult = null;
        $message = null;

        try {
            $pingResult = $client->Ping();
        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        $result = new Varien_Object();
        $result->setActualResult($pingResult);
        $result->setMessage($message);

        if (!isset($pingResult) || !is_object($pingResult)) {
            $result->setResultCode(SeverityLevel::C_EXCEPTION);
        }

        if(!$pingResult->authenticated) {
            $result->setResultCode(SeverityLevel::C_EXCEPTION);
            $result->setMessage('REST V2 is not Authorized');
        }

        $this->_log(
            OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype::PING,
            new stdClass(),
            $result,
            $storeId,
            $config->getParams()
        );

        return ($result->getResultCode() == SeverityLevel::C_SUCCESS) ? true : $result->getMessage();
    }

}