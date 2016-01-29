<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * Class OnePica_AvaTax_Model_Action_Ping
 */
class OnePica_AvaTax_Model_Action_Ping extends OnePica_AvaTax_Model_Action_Abstract
{
    /**
     * Tries to ping AvaTax service with provided credentials
     *
     * @param int|null $storeId
     * @return bool|array
     */
    public function ping($storeId)
    {
        $storeId = Mage::app()->getStore($storeId)->getStoreId();
        $this->setStoreId($storeId);

        return $this->_getService()->ping($storeId);
    }
}
