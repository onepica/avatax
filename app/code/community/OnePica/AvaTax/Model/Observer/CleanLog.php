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
 * Avatax Observer CleanLog
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_CleanLog extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Observer to clean the log every so often so it does not get too big.
     *
     * @return $this
     */
    public function execute()
    {
        $days = floatval(Mage::getStoreConfig('tax/avatax/log_lifetime'));
        Mage::getModel('avatax_records/log')->deleteLogsByInterval($days);
        return $this;
    }
}
