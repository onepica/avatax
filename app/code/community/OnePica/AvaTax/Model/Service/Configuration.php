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
 * The abstract base AvaTax model
 *
 * @method string getActiveService()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */

class OnePica_AvaTax_Model_Service_Configuration extends Varien_Object
{

    /**
     * Class constructor
     *
     * OnePica_AvaTax_Model_Service_Configuration constructor.
     */
    public function __construct()
    {
        $this->addData($this->_getActiveServiceConfig());
    }

    /**
     * Select service and get config for them
     *
     * @return array|mixed
     */
    private function _getActiveServiceConfig()
    {
        $store = Mage::app()->getStore();
        $this->setActiveService(Mage::helper('avatax')->getActiveService($store));
        $configuration = Mage::getStoreConfig('tax/' . strtolower($this->getActiveService()),
            Mage::app()->getSafeStore()->getId());

        return $configuration;
    }
}
