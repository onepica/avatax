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
 * Log model
 *
 * @method int getStoreId()
 * @method $this setStoreId(int $storeId)
 * @method OnePica_AvaTax_Model_Records_Mysql4_Log getResource()
 * @method OnePica_AvaTax_Model_Records_Mysql4_Log_Collection getCollection()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_Log extends Mage_Core_Model_Abstract
{
    /**
     * Success log level
     */
    const LOG_LEVEL_SUCCESS = 'Success';

    /**
     * Error log level
     */
    const LOG_LEVEL_ERROR = 'Error';

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('avatax_records/log');
    }

    /**
     * Set additional data
     *
     * @param string|null $value
     * @return $this
     */
    public function setAdditional($value = null)
    {
        if ($value) {
            $value = str_replace(
                $this->_getConfigHelper()->getServiceKey($this->getStoreId()),
                '[MASKED::LICENSE_KEY]',
                print_r($value, true)
            );
        }
        $this->setData('additional', $value);

        return $this;
    }

    /**
     * Get level options
     *
     * @return array
     */
    public function getLevelOptions()
    {
        return array(
            self::LOG_LEVEL_SUCCESS => self::LOG_LEVEL_SUCCESS,
            self::LOG_LEVEL_ERROR   => self::LOG_LEVEL_ERROR
        );
    }

    /**
     * Delete logs for given interval
     *
     * @param int $days
     * @return int
     */
    public function deleteLogsByInterval($days)
    {
        return $this->getResource()->deleteLogsByInterval($days);
    }

    /**
     * Get config helper
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }
}
