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


class OnePica_AvaTax_Model_Records_Log extends Mage_Core_Model_Abstract
{

	const LOG_LEVEL_SUCCESS = 'Success';
	const LOG_LEVEL_ERROR	= 'Error';

	public function _construct()
	{
		parent::_construct();
		$this->_init('avatax_records/log');
	}
	
	public function setAdditional($value=null) {
		if($value) {
			$value = str_replace(Mage::getStoreConfig('tax/avatax/license'), '[MASKED::LICENSE_KEY]', print_r($value, true));
		}
		$this->setData('additional', $value);
		return $this;
	}

	public function getTypeOptions() {
		$storeId = Mage::app()->getStore()->getId();
		$types = Mage::helper('avatax')->getLogType($storeId);
		foreach ($types as $key => $value)
		{
			$result[$value] = $value;
		}
		return $result;
    }

	public function getLevelOptions() {
		return array(
			self::LOG_LEVEL_SUCCESS		=> self::LOG_LEVEL_SUCCESS,
			self::LOG_LEVEL_ERROR		=> self::LOG_LEVEL_ERROR
  	 	);
	}
}