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
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


require_once(Mage::getModuleDir('', 'OnePica_AvaTax').DS.'lib'.DS.'functions.php');

/**
 * The AvaTax Config Model, which registers config settings with the AvaTax SDK. 
 */
class OnePica_AvaTax_Model_Config extends Varien_Object
{
	const CONFIG_KEY = 'Magento';
	const APP_NAME = 'OP_AvaTax by One Pica';
	
	/**
	 * Values for the admin config action options
	 */
	const ACTION_DISABLE 			= 0;
	const ACTION_CALC				= 1;
	const ACTION_CALC_SUBMIT		= 2;
	const ACTION_CALC_SUBMIT_COMMIT	= 3;
	
	/**
	 * Values for the admin config action options
	 */
	const REGIONFILTER_OFF			= 0;
	const REGIONFILTER_TAX			= 1;
	const REGIONFILTER_ALL			= 2;
	
	/**
	 * Number of times a queue item will try to send
	 */
	const QUEUE_ATTEMPT_MAX			= 5;

	/**
	 * The AvaTax ATConfig object.
	 *
	 * @var ATConfig
	 */
	protected $_config = null;

	/**
	 * The AvaTax TaxServiceSoap object.
	 *
	 * @var TaxServiceSoap
	 */
	protected $_taxConnection;

	/**
	 * The AvaTax AddressServiceSoap object.
	 *
	 * @var AddressServiceSoap
	 */
	protected $_addressConnection;

	/**
	 * Constructor.  Loads all required AvaTax classes.
	 *
	 * @return null
	 */
	protected function _construct ()
	{
		$helper = Mage::helper('avatax');

		// AvaTax functions
		require_once $helper->getLibPath() . DS . 'functions.php';

		// AvaTax classes
		$helper->loadClasses(array(			
			'TaxRequest',
			'PostTaxRequest',
			'PostTaxResult',
			'CommitTaxRequest',
			'CommitTaxResult',
			'CancelTaxRequest',
			'CancelTaxResult',
			'Enum',
			'CancelCode',			
			'ATConfig',
			'ATObject',
			'DynamicSoapClient',
			'AvalaraSoapClient',
			'AddressServiceSoap',
			'Address',
			'Enum',
			'TextCase',
			'Message',
			'SeverityLevel',
			'ValidateRequest',
			'ValidateResult',
			'ValidAddress',
			'TaxServiceSoap',
			'GetTaxRequest',
			'DocumentType',
			'DetailLevel',
			'Line',
			'ServiceMode',
			'GetTaxResult',
			'TaxLine',
			'TaxDetail',
			'PingResult',
			'TaxOverride',
			'TaxOverrideType'
		));	
	}

	/**
	 * Initializes the AvaTax SDK with connection settings found in the Admin config.
	 *
	 * @param int $storeId
	 * @return OnePica_AvaTax_Model_Config
	 */
	public function init ($storeId)
	{
		if (!$this->_config) {
			$this->_config = new ATConfig(self::CONFIG_KEY, array(
				'url'       => $this->getConfig('url', $storeId),
				'account'   => $this->getConfig('account', $storeId),
				'license'   => $this->getConfig('license', $storeId),
				'trace'     => (Mage::helper('avatax')->getLogMode($storeId) == OnePica_AvaTax_Model_Source_Logmode::DEBUG) ? true : false,
				'client'	=> $this->getClientName()
			));
		}		
		return $this;
	}

	/**
	 * Generates client name to pass with communications
	 * 
	 * Parts:
	 * - MyERP: the ERP that this connector is for (not always applicable)
	 * - Majver: version info for the ERP (not always applicable)
	 * - MinVer: version info for the ERP (not always applicable)
	 * - MyConnector: Name of the OEM�s connector AND the name of the OEM (company)  *required*
	 * - Majver: OEM�s connector version *required*
	 * - MinVer: OEM�s connector version *required*
	 * 
	 * @example Magento,1.4,.0.1,OP_AvaTax by One Pica,2,0.1
	 * @return string
	 */
	public function getClientName() {
		$mageVersion = Mage::getVersion();
		$mageVerParts = explode('.', $mageVersion, 2);
		
		$opVersion = Mage::getResourceModel('core/resource')->getDbVersion('avatax_records_setup');
		$opVerParts = explode('.', $opVersion, 2);
		
		$part = array();
		$part[] = self::CONFIG_KEY;
		$part[] = $mageVerParts[0];
		$part[] = $mageVerParts[1];
		$part[] = self::APP_NAME;
		$part[] = $opVerParts[0];
		$part[] = $opVerParts[1];
		return implode(',', $part);
	}

	/**
	 * Returns the AvaTax Address soap connection client.
	 *
	 * @return AddressServiceSoap
	 */
	public function getAddressConnection ()
	{
		if (!$this->_addressConnection) {
			$this->_addressConnection = new AddressServiceSoap(self::CONFIG_KEY);
		}
		return $this->_addressConnection;
	}

	/**
	 * Returns the AvaTax Address soap connection client.
	 *
	 * @return AddressServiceSoap
	 */
	public function getTaxConnection ()
	{
		if (!$this->_taxConnection) {
			$this->_taxConnection = new TaxServiceSoap(self::CONFIG_KEY);
		}
		return $this->_taxConnection;
	}

	/**
	 * Returns a parameter from the AvaTax ATConfig object.
	 *
	 * @return string
	 */
	public function getParam ($param)
	{
		return $this->_avataxConfig->$param;
	}

	/**
	 * Returns data from the admin system config.
	 *
	 * @param string $path
	 * @param int $store
	 * @return string
	 */
	public function getConfig ($path, $store=null)
	{
		return Mage::getStoreConfig('tax/avatax/' . $path, $store);
	}

	/**
	 * Returns true if the admin is configured to normalize addresses.
	 *
	 * @return boolean
	 */
	public function normalizeAddress ()
	{
		$storeId = Mage::app()->getStore()->getId();
		return $this->getConfig('normalize_address', $storeId);
	}

	/**
	 * Returns the company code to use from the AvaTax dashboard
	 *
	 * @return string
	 */
	public function getCompanyCode ($store=null)
	{
		return $this->getConfig('company_code', $store);
	}
}
