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
 * Log type source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_Logtype
{
    /**
     * Gets the list of type for the admin config dropdown
     *
     * @return array
     * @throws OnePica_AvaTax_Exception
     */
    public function toOptionArray()
    {
        return $this->_getLogTypeModel()->toArray();
    }

    /**
     * Get log types array
     *
     * @return array
     */
    public function getLogTypes()
    {
        return $this->_getLogTypeModel()->getLogTypes();
    }

    /**
     * Get LogType source model
     *
     * @return false|OnePica_AvaTax_Model_Source_Avatax16_Logtype|OnePica_AvaTax_Model_Source_Avatax_Logtype
     * @throws \OnePica_AvaTax_Exception
     */
    protected function _getLogTypeModel()
    {
        $activeService = $this->_getConfigHelper()->getActiveService(Mage::app()->getStore());
        if (!$activeService) {
            throw new OnePica_AvaTax_Exception('Service source model is not defined.');
        }

        $model = Mage::getModel('avatax/source_' . $activeService . '_logtype');

        if (!$model) {
            throw new OnePica_AvaTax_Exception('Could not found source model ' . $activeService);
        }

        return $model;
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
