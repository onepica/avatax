<?php
namespace Avalara\AvaTaxRestV2;
/**
 * Base AvaTaxClient object that handles connectivity to the AvaTax v2 API server.
 * This class is overridden by the descendant AvaTaxClient which implements all the API methods.
 */
class AvaTaxClientBase
{
    /**  @var \Varien_Http_Client The client to use to connect to AvaTax $client */
    private $client;

    /** @var array The authentication credentials to use to connect to AvaTax $auth */
    private $auth;

    /** @var string The application name as reported to AvaTax $appName */
    private $appName;

    /** @var string The application version as reported to AvaTax $appVersion */
    private $appVersion;

    /** @var string The machine name as reported to AvaTax $machineName */
    private $machineName;

    /** @var string The root URL of the AvaTax environment to contact $environment */
    private $environment;

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
        $env = 'https://rest.avatax.com';
        if ($environment == "sandbox") {
            $env = 'https://sandbox-rest.avatax.com';
        } else {
            if ((substr($environment, 0, 8) == 'https://') || (substr($environment, 0, 7) == 'http://')) {
                $env = $environment;
            }
        }

        // Configure the HTTP client
        $this->client = new \Varien_Http_Client($env, $params);
    }

    /**
     * Configure this client to use the specified username/password security settings
     *
     * @param  string $username The username for your AvaTax user account
     * @param  string $password The password for your AvaTax user account
     * @return AvaTaxClientBase
     */
    public function withSecurity($username, $password)
    {
        $this->auth = array($username, $password);

        return $this;
    }

    /**
     * Configure this client to use Account ID / License Key security
     *
     * @param  int    $accountId  The account ID for your AvaTax account
     * @param  string $licenseKey The private license key for your AvaTax account
     * @return AvaTaxClientBase
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
     * @return AvaTaxClientBase
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
        // Contact the server
        try {
            $this->client->setHeaders('Accept', "application/json");
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

            /** @var \Zend_Uri $uri */
            $uri = $this->client->getUri();
            $uri->setPath($apiUriPath);
            $uri->setQuery($params['query']);

            $this->client->setConfig($params);
            if ($method == 'POST' && isset($params['body'])) {
                $this->client->setRawData($params['body'], 'application/json');
            }

            $response = $this->client->request($method);
            $body = $response->getBody();

            return json_decode($body);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
