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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Service Url source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_Url
{
    /**
     * Gets the list of service urls for the admin config dropdown
     *
     * @return array
     * @throws OnePica_AvaTax_Exception
     */
    public function toOptionArray()
    {
        $activeService = Mage::helper('avatax/config')->getActiveService(Mage::app()->getStore());
        if (!$activeService) {
            throw new OnePica_AvaTax_Exception('Service source model is not defined.');
        }
        $model = Mage::getModel('avatax/source_' . $activeService . '_url');
        if (!$model) {
            throw new OnePica_AvaTax_Exception('Could not found source model ' . $activeService);
        }
        return $model->toArray();
    }
}
