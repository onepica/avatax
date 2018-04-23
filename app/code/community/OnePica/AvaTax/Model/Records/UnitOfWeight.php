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
 * Unit Of Weight model
 *
 * @method int getId()
 * @method int getStoreId()
 * @method $this setStoreId(int $storeId)
 * @method string getAvalaraCode()
 * @method $this setAvalaraCode(string $code)
 * @method string getZendCode()
 * @method $this setZendCode(string $code)
 * @method string getDescription()
 * @method $this setDescription(string $description)
 * @method string getCountryCodes()
 * @method $this setCountryCodes(string $countryCodes)
 * @method OnePica_AvaTax_Model_Records_Mysql4_UnitOfWeight getResource()
 * @method OnePica_AvaTax_Model_Records_Mysql4_Log_Collection getCollection()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_UnitOfWeight extends Mage_Core_Model_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('avatax_records/unitofweight');
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
