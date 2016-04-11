<?php
/**
 * ATConfig.class.php
 */

/**
 * Contains various service configuration parameters as class static variables.
 *
 * {@link AddressServiceSoap} and {@link TaxServiceSoap} read this file during initialization.
 *
 * @author    Avalara
 * @copyright © 2004 - 2016 Avalara, Inc.  All rights reserved.
 * @package   Base
 */

class ATConfig
{
    private static $Configurations = array();
    private $_ivars;

    public function __construct($name, $values = null)
    {
        if($values)
        {
            ATConfig::$Configurations[$name] = $values;
        }
        $this->_ivars = ATConfig::$Configurations[$name];
    }

    public function __get($n)
    {
        if($n == '_ivars') { return parent::__get($n); }
        if(isset($this->_ivars[$n]))
        {
            return $this->_ivars[$n];
        }
        else if(isset(ATConfig::$Configurations['Default'][$n])) // read missing values from default
        {
            return ATConfig::$Configurations['Default'][$n];
        }
        else
        {
            return null;
        }
    }
}
/* Specify configurations by name here.  You can specify as many as you like */


$__wsdldir = dirname(__FILE__)."/wsdl";

/* This is the default configuration - it is used if no other configuration is specified */
new ATConfig('Default', array(
    'url'       => 'no url specified',
    'accountService' => '/Account/AccountSvc.asmx',
    'addressService' => '/Address/AddressSvc.asmx',
    'taxService' => '/Tax/TaxSvc.asmx',
	'batchService'=> '/Batch/BatchSvc.asmx',
	'avacert2Service'=> '/AvaCert2/AvaCert2Svc.asmx',
    'accountWSDL' => 'file://'.$__wsdldir.'/Account.wsdl',
    'addressWSDL' => 'file://'.$__wsdldir.'/Address.wsdl',
    'taxWSDL'  => 'file://'.$__wsdldir.'/Tax.wsdl',
	'batchWSDL'  => 'file://'.$__wsdldir.'/BatchSvc.wsdl',
	'avacert2WSDL'  => 'file://'.$__wsdldir.'/AvaCert2Svc.wsdl',
    'account'   => '<Your Production Account Here>',
    'license'   => '<Your Production License Key Here>',
    'adapter'   => 'avatax4php,15.5.1.0',
    'client'    => 'AvalaraPHPInterface,1.0',
	'name'    => '15.5.1.0',
    'trace'     => true) // change to false for production
);
