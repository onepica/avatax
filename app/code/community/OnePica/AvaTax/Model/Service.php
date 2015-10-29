<?php

 /**
 * OnePica_AvaTax_Model_Service
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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

 /**
 * The fabric AvaTax model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */

class OnePica_AvaTax_Model_Service extends Mage_Core_Model_Factory
{
    /**
     * Path to service model
     */
    const XML_PATH_INDEX_INDEX_MODEL = 'avatax/service_';

    /**
     * Get factory method
     *
     * @param string $service
     * @param array $options
     * @return Mage_Core_Model_Abstract|null|OnePica_AvaTax_Model_Service_Abstract
     * @throws OnePica_AvaTax_Exception
     */
    public function factory($service, array $options = array())
    {
        if (!$service) {
            throw new OnePica_AvaTax_Exception('Service name is not defined.');
        }
        $model = $this->getSingleton(self::XML_PATH_INDEX_INDEX_MODEL . $service, $options);
        if (!$model) {
            throw new OnePica_AvaTax_Exception('Could not found service model ' . $service);
        }
        return $model;
    }
}
