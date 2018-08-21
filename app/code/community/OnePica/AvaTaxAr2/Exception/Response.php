<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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

class OnePica_AvaTaxAr2_Exception_Response extends Mage_Core_Exception
{
    /**
     * @var string
     */
    protected $_responceCode = null;

    /**
     * OnePica_AvaTaxAr2_Exception_Response constructor.
     * @param $response
     * @param Throwable|null $previous
     */
    public function __construct($response, Throwable $previous = null)
    {
        $message = '';

        if (self::isResponseError($response)) {
            $prefix = $this->_getHelper()->__('AvaTax Document Management. ');
            if (is_object($response)) {
                if (isset($response->error->details[0]) && is_object($response->error->details[0])) {
                    $message = $response->error->details[0]->message . ": " . $response->error->details[0]->description;
                } else {
                    $message = $response->error->message;
                }

                $message = $prefix . $message;

                $this->_responceCode = $response->error->code;
            } elseif (is_string($response) && !empty($response)) {
                $message = $prefix . $response;
            } else {
                $message = $prefix . $this->_getHelper()->__('Unexpected exception occurred.');
            }
        }

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return null|string
     */
    public function getResponseCode()
    {
        return $this->_responceCode;
    }

    /**
     * Is Response Contains Errors
     *
     * @param $response
     * @return bool
     */
    public static function isResponseError($response)
    {
        // is_string used because we and http client adapter return exception message as response,
        // we have methods in API that returns string as success value but we do not use such methods currently
        return !isset($response) || (is_object($response) && isset($response->error)) || is_string($response);
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avataxar2');
    }

}
