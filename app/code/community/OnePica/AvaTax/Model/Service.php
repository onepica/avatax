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
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */

class OnePica_AvaTax_Model_Service extends Mage_Core_Model_Factory
{
    /** @var null|OnePica_AvaTax_Model_Service_Configuration */
    protected $_serviceConfig = null;

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
     *
     * @return $this
     */
    public function setServiceConfig($serviceConfig)
    {
        $this->_serviceConfig = $serviceConfig;
        return $this;
    }

    /**
     * Get factory method
     *
     * @return Mage_Core_Model_Abstract|null|OnePica_AvaTax_Model_Service_Abstract
     */
    public function factory()
    {
        try {
            $model = $this->getModel('avatax/service_' . strtolower($this->getServiceConfig()->getActiveService()));
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $model;
    }
}
