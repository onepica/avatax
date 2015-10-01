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
 * Class OnePica_AvaTax16_Config
 */
class OnePica_AvaTax16_Config
{
    /**
     * Accept header
     */
    const ACCEPT_HEADER = 'application/json; document-version=1';

    /**
     * Content type header
     */
    const CONTENT_TYPE_HEADER = 'application/json';

    /**
     * Default user agent
     */
    const USER_AGENT_DEFAULT = 'AvaTax16 agent';

    /**
     * Base url
     *
     * @var string
     */
    protected $_baseUrl;

    /**
     * Account id
     *
     * @var string
     */
    protected $_accountId;

    /**
     * Company code
     *
     * @var string
     */
    protected $_companyCode;

    /**
     * Authorization header
     *
     * @var string
     */
    protected $_authorizationHeader;

    /**
     * User agent
     *
     * @var string
     */
    protected $_userAgent;

    /**
     * Construct
     */
    public function __construct()
    {
        // init default values
        $this->setUserAgent(self::USER_AGENT_DEFAULT);
    }

    /**
     * Set base url
     *
     * @param string $value
     * @return OnePica_AvaTax16_Config
     */
    public function setBaseUrl($value)
    {
        $this->_baseUrl = $value;
    }

    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * Set user agent
     *
     * @param string $value
     * @return OnePica_AvaTax16_Config
     */
    public function setUserAgent($value)
    {
        $this->_userAgent = $value;
    }

    /**
     * Get user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->_userAgent;
    }

    /**
     * Set account id
     *
     * @param string $value
     * @return OnePica_AvaTax16_Config
     */
    public function setAccountId($value)
    {
        $this->_accountId = $value;
    }

    /**
     * Get account id
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->_accountId;
    }

    /**
     * Set company code
     *
     * @param string $value
     * @return OnePica_AvaTax16_Config
     */
    public function setCompanyCode($value)
    {
        $this->_companyCode = $value;
    }

    /**
     * Get company code
     *
     * @return string
     */
    public function getCompanyCode()
    {
        return $this->_companyCode;
    }

    /**
     * Set authorization header
     *
     * @param string $value
     * @return OnePica_AvaTax16_Config
     */
    public function setAuthorizationHeader($value)
    {
        $this->_authorizationHeader = $value;
    }

    /**
     * Get authorization header
     *
     * @return string
     */
    public function getAuthorizationHeader()
    {
        return $this->_authorizationHeader;
    }

    /**
     * Get accept header
     *
     * @return string
     */
    public function getAcceptHeader()
    {
        return self::ACCEPT_HEADER;
    }

    /**
     * Get accept header
     *
     * @return string
     */
    public function getContentTypeHeader()
    {
        return self::CONTENT_TYPE_HEADER;
    }

    /**
     * Get if config values are available for requests
     *
     * @return bool
     */
    public function isValid()
    {
        if ($this->getBaseUrl()
            && $this->getAccountId()
            && $this->getCompanyCode()
            && $this->getAuthorizationHeader()
            && $this->getAcceptHeader()
            && $this->getContentTypeHeader()
            && $this->getUserAgent()
        ) {
            return true;
        } else {
            return false;
        }
    }
}
