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

    /**
     * Get factory method
     *
     * @param $service
     * @param array $options
     * @return Mage_Core_Model_Abstract|null|OnePica_AvaTax_Model_Service_Abstract
     * @throws OnePica_SocialPost_Model_Api_Exception
     */
    public function factory($service, array $options = array())
    {
        if (!$service) {
            throw new OnePica_SocialPost_Model_Api_Exception("Not defined service name.");;
        }
        $model = Mage::getModel('avatax/service_' . $service, $options);
        if (!$model) {
            throw new OnePica_SocialPost_Model_Api_Exception("Could not found service Service model '$service'.");
        }
        return $model;
    }
}
