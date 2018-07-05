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
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * The base AvaTaxAr2 Helper class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Helper_Data extends Mage_Core_Helper_Abstract
{
    const AVATAX_CUSTOMER_CODE = 'avatax_customer_code';

    const AVATAX_CUSTOMER_DOCUMENTS_FORM_CODE = 'customer_avatax_exempt';

    /**
     * Generates app name for Rest V2 requests
     *
     * @example OP_AvaTax by One Pica
     * @return string
     */
    public function getAppName()
    {
        return OnePica_AvaTax_Model_Service_Abstract_Config::APP_NAME;
    }

    /**
     * Generates app version for Rest V2 requests
     *
     * @example 3.7.0.0
     * @return string
     */
    public function getAppVersion()
    {
        $opVersion = Mage::getResourceModel('core/resource')->getDbVersion('avatax_records_setup');

        return $opVersion;
    }

    /**
     * Generates machine name for Rest V2 requests
     *
     * @example Linux,5.6.30-1
     * @return string
     */
    public function getMachineName()
    {
        $nameParams = array(PHP_OS, PHP_VERSION);

        return implode(',', $nameParams);
    }
}
