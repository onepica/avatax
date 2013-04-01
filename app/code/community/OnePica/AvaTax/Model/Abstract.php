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

/**
 * The abstract base AvaTax model.
 */
abstract class OnePica_AvaTax_Model_Abstract extends Varien_Object
{
	/**
	 * The module helper
	 *
	 * @var OnePica_AvaTax_Helper_Data
	 */
	protected $_helper = null;

	/**
	 * Constructor
	 *
	 * @return null
	 */
	protected function _construct ()
	{
		Mage::getSingleton('avatax/config');
	}

	/**
	 * Logs a debug message
	 *
	 * @param string $request the request string
	 * @param string $result the result string
	 * @param int $storeId id of the store the call is make for
	 * @param mixed $additional any other info
	 */
	protected function _log ($request, $result, $storeId=null, $additional=null)
	{
		if($result->getResultCode() == SeverityLevel::$Success) {
			switch(Mage::helper('avatax')->getLogMode($storeId)) {
				case OnePica_AvaTax_Model_Source_Logmode::ERRORS:
					return;
					break;
				case OnePica_AvaTax_Model_Source_Logmode::NORMAL:
					$additional = null;
					break;
			}
		}
		
		$requestType = str_replace('Request', '', get_class($request));
		$resultType = str_replace('Result', '', get_class($result));
		$type = $requestType ? $requestType : $resultType;
		if($type == 'Varien_Object') $type = 'Unknown';

		if (in_array($resultType, Mage::helper('avatax')->getLogType($storeId)))
		{
			Mage::getModel('avatax_records/log')
				->setStoreId($storeId)
				->setLevel($result->getResultCode())
				->setType($type)
				->setRequest(print_r($request, true))
				->setResult(print_r($result, true))
				->setAdditional($additional)
				->save();
		}
	}

	/**
	 * Returns the AvaTax session.
	 *
	 * @return OnePica_AvaTax_Model_Session
	 */
	public function getSession ()
	{
		return Mage::getSingleton('avatax/session');
	}

	/**
	 * Returns the AvaTax helper.
	 *
	 * @return OnePica_AvaTax_Helper_Data
	 */
	public function getHelper ()
	{
		if (!$this->_helper) {
			$this->_helper = Mage::helper('avatax');
		}
		return $this->_helper;
	}

	/**
	 * Alias to the helper translate method.
	 *
	 * @param string $message
	 * @param string var number of replacement vars
	 * @return string
	 */
	public function __ ($message)
	{
		$args = func_get_args();
		return call_user_func_array(array($this->getHelper(), '__'), $args);
	}
}