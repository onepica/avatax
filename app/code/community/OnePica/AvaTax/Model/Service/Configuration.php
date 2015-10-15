<?php

/**
 * The abstract base AvaTax model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */

/**
 * Quote model

 * @method string getActiveService()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Configuration extends Varien_Object
{
    const AVATAX_ACTIVE_SERVICE = 'Avatax';

    /**
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
        $configuration = array();
        $services      = Mage::helper('avatax')->getExistServices();
        foreach ($services as $service) {
            if ($service == self::AVATAX_ACTIVE_SERVICE) {
                $this->setActiveService($service);
                $configuration = Mage::getStoreConfig('tax/' . strtolower($service),
                    Mage::app()->getSafeStore()->getId());
            }
        }

        return $configuration;
    }
}