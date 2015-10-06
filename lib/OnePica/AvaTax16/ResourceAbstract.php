<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category  OnePica
 * @package   OnePica_AvaTax
 * @copyright Copyright (c) 2015 One Pica, Inc. (http://www.onepica.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract class OnePica_AvaTax16_ResourceAbstract
 */
abstract class OnePica_AvaTax16_ResourceAbstract
{
    /**
     * Config
     *
     * @var OnePica_AvaTax16_Config
     */
    protected $_config;

    /**
     * Construct
     *
     * @param OnePica_AvaTax16_Config $config
     */
    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * Get config
     *
     * @return OnePica_AvaTax16_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Get Curl Object with headers from config
     *
     * @return OnePica_AvaTax16_IO_Curl
     */
    protected function _getCurlObjectWithHeaders()
    {
        $curl = new OnePica_AvaTax16_IO_Curl();
        $config = $this->getConfig();
        $curl->setHeader('Authorization', $config->getAuthorizationHeader());
        $curl->setHeader('Accept', $config->getAcceptHeader());
        $curl->setHeader('Content-Type', $config->getContentTypeHeader());
        $curl->setHeader('User-Agent', $config->getUserAgent());
        return $curl;
    }

    /**
     * Set Error Data To Response If Exists
     *
     * @param OnePica_AvaTax16_Document_Part $response
     * @param OnePica_AvaTax16_IO_Curl $curl
     * @return $this
     */
    protected function _setErrorDataToResponseIfExists($response, $curl)
    {
        if ($curl->getError()) {
            $response->setHasError(true);
            $errors = array();
            $responseData = $curl->getResponse();
            if ($responseData instanceof stdClass) {
                if (isset($responseData->errors)) {
                    $errors = (array) $responseData->errors;
                }
                if (isset($responseData->message)) {
                    $errors['message'] = $responseData->message;
                }
            } else {
                $errors['message'] = $responseData;
            }
            $response->setErrors($errors);
        }
    }
}
