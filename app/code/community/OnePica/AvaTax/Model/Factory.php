<?php

/**
 * The abstract base AvaTax model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Factory extends Mage_Core_Model_Factory
{
    /** @var null|OnePica_AvaTax_Model_Service_Configuration */
    protected $_serviceConfig = null;
    /** @var null|OnePica_AvaTax_Model_Service_Abstract */
    protected $_instance      = null;

    /**
     * OnePica_AvaTax_Model_Factory constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setServiceConfig($this->getSingleton('avatax/service_configuration'));
    }

    /**
     * @return null|OnePica_AvaTax_Model_Service_Configuration
     */
    public function getServiceConfig()
    {
        return $this->_serviceConfig;
    }

    /**
     * @param null|OnePica_AvaTax_Model_Service_Configuration $serviceConfig
     */
    public function setServiceConfig($serviceConfig)
    {
        $this->_serviceConfig = $serviceConfig;
    }

    /**
     * @return Mage_Core_Model_Abstract|null|OnePica_AvaTax_Model_Service_Abstract
     */
    public function getServiceInstance()
    {
        if (is_null($this->_instance)) {
            try {
                $this->_instance = $this->getSingleton('avatax/service_' . strtolower($this->getServiceConfig()->getActiveService()));
                $this->_instance->setConfig($this->getServiceConfig());
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $this->_instance;
    }


}