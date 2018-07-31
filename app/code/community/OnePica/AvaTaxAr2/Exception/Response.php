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
            if (isset($response->error->details[0]) && is_object($response->error->details[0])) {
                $message = $response->error->details[0]->message . ": " . $response->error->details[0]->description;
            } else {
                $message = $response->error->message;
            }

            $this->_responceCode = $response->error->code;
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
        return (is_object($response) && isset($response->error));
    }

}
