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


class OnePica_AvaTax_Model_Records_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() {
        $this->_init('avatax_records/log', 'log_id');
    }
    
	/**
	 * Sets various dates before the model is saved.
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return OnePica_AvaTax_Model_Mysql4_Log
	 */
    protected function _beforeSave(Mage_Core_Model_Abstract $object) {
        $object->setCreatedAt(gmdate('Y-m-d H:i:s'));
        if(!$object->getLevel()) {
        	$object->setLevel('Unknown');
        }
        return $this;
    }
}
