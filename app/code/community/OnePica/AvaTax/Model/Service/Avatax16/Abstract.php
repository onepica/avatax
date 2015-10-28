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
 * Avatax 16 service abstract model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Service_Avatax16_Abstract extends OnePica_AvaTax_Model_Service_Abstract_Tools
{

    /**
     * Logs a debug message
     *
     * @param string $type
     * @param string $request the request string
     * @param string $result the result string
     * @param int $storeId id of the store the call is make for
     * @param mixed $additional any other info
     * @return $this
     */
    protected function _log($type, $request, $result, $storeId = null, $additional = null)
    {
        if ($result->getHasError() === false) {
            switch ($this->_getHelper()->getLogMode($storeId)) {
                case OnePica_AvaTax_Model_Source_Logmode::ERRORS:
                    return $this;
                    break;
                case OnePica_AvaTax_Model_Source_Logmode::NORMAL:
                    $additional = null;
                    break;
            }
        }
        $level = $result->getHasError() ? OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_ERROR
            : OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_SUCCESS;

        $requestLog = ($request instanceof OnePica_AvaTax16_Document_Part) ? $request->toArray() : $request;
        $resultLog = ($result instanceof OnePica_AvaTax16_Document_Part) ? $result->toArray() : $result;

        if (in_array($type, $this->_getHelper()->getLogType($storeId))) {
            Mage::getModel('avatax_records/log')
                ->setStoreId($storeId)
                ->setLevel($level)
                ->setType($type)
                ->setRequest(print_r($requestLog, true))
                ->setResult(print_r($resultLog, true))
                ->setAdditional($additional)
                ->save();
        }
        return $this;
    }
}
