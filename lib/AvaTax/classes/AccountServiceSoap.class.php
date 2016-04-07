<?php
/**
 * AccountServiceSoap.class.php
 */

/**
 * Proxy interface for the Avalara Accounts Web Service.
 *
 * AccountServiceSoap reads its configuration values from static variables defined
 * in ATConfig.class.php. This file must be properly configured with your security credentials.
 *
 * <p>
 * <b>Example:</b>
 * <pre>
 *  $accountService = new AccountServiceSoap();
 * </pre>
 * @author    Avalara
 * @copyright © 2004 - 2016 Avalara, Inc.  All rights reserved.
 * @package   Address
 */

class AccountServiceSoap extends AvalaraSoapClient
{
    static protected $classmap = array(
        							'UserFetch' => 'UserFetch',
                                    'BaseRequest' => 'BaseRequest',
                                    'ValidateRequest' => 'ValidateRequest',
                                    'BaseAddress' => 'BaseAddress',
                                    'URL' => 'URL',
                                    'UserName' => 'UserName',
                                    'Password' => 'Password',
                                    'BaseResult' => 'BaseResult',
                                    'SeverityLevel' => 'SeverityLevel',
                                    'Message' => 'Message',
                                    'Profile' => 'Profile',
                                    'Ping' => 'Ping',
                                    'PingResult' => 'PingResult',
                                    'IsAuthorized' => 'IsAuthorized',
                                    'IsAuthorizedResult' => 'IsAuthorizedResult');

    /**
     * Construct a proxy for Avalara's Address Web Service using the default URL as coded in the class or programatically set.
     *
     * <b>Example:</b>
     * <pre>
     *  $port = new AccountServiceSoap();
     *  $port->ping();
     * </pre>
     *
     * @see AvalaraSoapClient
     * @see TaxServiceSoap
     */

    public function __construct($configurationName = 'Default')
    {
        $config = new ATConfig($configurationName);
        $this->client = new DynamicSoapClient   (
            $config->accountWSDL,
            array
            (
                'location' => $config->url.$config->accountService,
                'trace' => $config->trace,
                'classmap' => AccountServiceSoap::$classmap
            ),
            $config
        );
    }

    public function ping($message = '')
    {
        return $this->client->Ping(array('Message' => $message))->PingResult;
    }

    public function isAuthorized($operations)
    {
        return $this->client->IsAuthorized(array('Operations' => $operations))->IsAuthorizedResult;
    }

	public function CompanyFetch($validateRequest)
    {
        return $this->client->CompanyFetch(array('FetchRequest' => $validateRequest))->CompanyFetchResult;
    }
}
