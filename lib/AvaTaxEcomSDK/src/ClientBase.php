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

namespace Avalara\AvaTaxEcomSDK;

/**
 * Class ClientBase
 *
 * @package Avalara\AvaTaxEcomSDK
 */
class ClientBase
{
    /**  @var \Zend_Http_Client The client to use to connect to AvaTax $client */
    private $client;

    /** @var array The authentication credentials to use to connect to AvaTax $auth */
    private $auth;

    /** @var string The application name as reported to AvaTax $appName */
    private $appName;

    /** @var string $clientId */
    private $clientId;

    /** @var string The application version as reported to AvaTax $appVersion */
    private $appVersion;

    /** @var string The machine name as reported to AvaTax $machineName */
    private $machineName;

    /** @var string The root URL of the AvaTax environment to contact $environment */
    private $environment;

    /**
     * Log layer, callback
     *
     * @var callable
     */
    public $_logsCallback;

    /**
     * Log layer, last model json encoded
     *
     * @var null|object
     */
    protected $_lastJsonModelEncoded = null;

    /**
     * Log layer, last model json decoded
     *
     * @var null|object
     */
    protected $_lastJsonModelDecoded = null;

    /**
     * @var int Last transaction routing time
     */
    private $lastRoutingTime = -1;

    /**
     * Construct a new AvaTaxClient
     *
     * @param string $appName     Specify the name of your application here.
     *                            Should not contain any semicolons.
     * @param string $appVersion  Specify the version number of your application here.
     *                            Should not contain any semicolons.
     * @param string $machineName Specify the machine name of the machine on which this code is executing here.
     *                            Should not contain any semicolons.
     * @param string $environment Indicates which server to use; acceptable values are "sandbox" or "production",
     *                            or the full URL of your AvaTax instance.
     * @param array  $params      Extra parameters to pass to the HTTP client
     *                            (http://docs.guzzlephp.org/en/latest/request-options.html) //TODO-zamoroka
     */
    public function __construct($appName, $appVersion, $machineName, $environment, $params = array())
    {
        $this->appName = $appName;
        $this->appVersion = $appVersion;
        $this->machineName = $machineName;
        $this->environment = $environment;

        // Determine startup environment
        $env = null;

        if ((substr($environment, 0, 8) == 'https://') || (substr($environment, 0, 7) == 'http://')) {
            $env = $environment;
        }

        // Configure the HTTP client
        $this->client = new \Zend_Http_Client($env, array('adapter' => 'Zend_Http_Client_Adapter_Curl'));
    }

    /**
     * Configure this client to use the specified username/password security settings
     *
     * @param  string $username The username for your AvaTax user account
     * @param  string $password The password for your AvaTax user account
     * @return ClientBase
     */
    public function withSecurity($username, $password)
    {
        $this->auth = array($username, $password);

        return $this;
    }

    /**
     * Set the client id for requests
     *
     * @param string $clientId
     * @return ClientBase
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Configure this client to use Account ID / License Key security
     *
     * @param  int    $accountId  The account ID for your AvaTax account
     * @param  string $licenseKey The private license key for your AvaTax account
     * @return ClientBase
     */
    public function withLicenseKey($accountId, $licenseKey)
    {
        $this->auth = array($accountId, $licenseKey);

        return $this;
    }

    /**
     * Configure this client to use bearer token
     *
     * @param  string $bearerToken The private bearer token for your AvaTax account
     * @return ClientBase
     */
    public function withBearerToken($bearerToken)
    {
        $this->auth = array($bearerToken);

        return $this;
    }

    /**
     * Make a single REST call to the AvaTax v2 API server
     *
     * @param string $apiUriPath The relative path of the API on the server
     * @param string $method     The HTTP verb being used in this request
     * @param array  $params     The parameters for this request, including query string and body parameters
     * @return mixed|string
     */
    protected function restCall($apiUriPath, $method, $params)
    {
        $result = '';

        try {
            // clean last json model if no body is set
            if (!isset($params['body']) || !$params['body']) {
                $this->_lastJsonModelEncoded = null;
            }

            $result = $this->_restCall($apiUriPath, $method, $params);
        } catch (\Exception $ex) {
            $result = $ex->getMessage();
        }

        call_user_func_array($this->_logsCallback, array());

        return $result;
    }

    /**
     * Make a single REST call to the AvaTax v2 API server
     *
     * @param string $apiUriPath The relative path of the API on the server
     * @param string $method     The HTTP verb being used in this request
     * @param array  $params     The parameters for this request, including query string and body parameters
     * @return mixed|string
     */
    private function _restCall($apiUriPath, $method, $params)
    {
        $result = '';

        //clean routing time
        $this->lastRoutingTime = -1;
        $startTime = microtime(true);

        // Contact the server
        try {
            $this->client->setHeaders('Accept', "application/json");
            $this->client->setHeaders('x-client-id', $this->clientId);
            $this->client->setHeaders(
                'X-Avalara-Client',
                "{$this->appName}; {$this->appVersion}; PhpRestClient; 17.5.0-67; {$this->machineName}"
            );

            // Set authentication on the parameters
            if (count($this->auth) == 2) {
                $this->client->setAuth($this->auth[0], $this->auth[1]);
            } else {
                $this->client->setHeaders('Authorization', "Bearer {$this->auth[0]}");
            }

            if (isset($params['headers']) && is_array($params['headers'])) {
                foreach ($params['headers'] as $key => $value) {
                    $this->client->setHeaders($key, $value);
                }
            }

            /** @var \Zend_Uri $uri */
            $uri = $this->client->getUri();
            $uri->setPath($apiUriPath);

            $this->client->setConfig($params);
            if ($method == 'POST' && isset($params['body'])) {
                $this->client->setRawData($params['body'], 'application/json');
            }

            $startTime = microtime(true);

            $response = $this->client->request($method);
            $body = $response->getBody();

            $result = $this->jsonDecode($body);
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }

        $endTime = microtime(true);
        $this->lastRoutingTime = $endTime - $startTime;

        return $result;
    }

    /**
     * @return \Zend_Http_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Log layer, wrapper for json_encode
     *
     * @param $value
     * @param int $options
     * @param int $depth
     * @return string
     */
    protected function jsonEncode($value, $options = 0, $depth = 512)
    {
        $this->_lastJsonModelEncoded = $value;

        return json_encode($value, $options, $depth);
    }

    /**
     * Log layer, wrapper for json_decode
     *
     * @param $json
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     */
    protected function jsonDecode($json, $assoc = false, $depth = 512, $options = 0)
    {
        $result = json_decode($json, $assoc, $depth, $options);

        $result = json_last_error() == JSON_ERROR_NONE ? $result : $json;

        $this->_lastJsonModelDecoded = $result;

        return $result;
    }

    /**
     * Log layer last model json encoded
     *
     * @return null
     */
    public function getLastJsonModelEncoded()
    {
        return $this->_lastJsonModelEncoded;
    }

    /**
     * Log layer last model json decoded
     *
     * @return null
     */
    public function getLastJsonModelDecoded()
    {
        return $this->_lastJsonModelDecoded;
    }

    /**
     * Get last routing time
     *
     * @return int
     */
    public function getLastRoutingTime()
    {
        return $this->lastRoutingTime;
    }
}
