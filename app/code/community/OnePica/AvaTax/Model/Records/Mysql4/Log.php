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
 * Log resource model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init('avatax_records/log', 'log_id');
    }

    /**
     * Sets various dates before the model is saved.
     *
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setCreatedAt($this->_getDateModel()->gmtDate('Y-m-d H:i:s'));
        if ($object->getLevel() != OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_SUCCESS) {
            $object->setLevel(OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_ERROR);
        }
        return $this;
    }

    /**
     * Delete logs for given interval
     *
     * @param int $days
     * @return int
     */
    public function deleteLogsByInterval($days)
    {
        return $this->_getWriteAdapter()->delete(
            $this->getTable('avatax_records/log'),
            $this->_getWriteAdapter()->quoteInto('created_at < DATE_SUB(UTC_DATE(), INTERVAL ? DAY)', $days)
        );
    }

    /**
     * Get core date model
     *
     * @return \Mage_Core_Model_Date
     */
    protected function _getDateModel()
    {
        return Mage::getSingleton('core/date');
    }
}
