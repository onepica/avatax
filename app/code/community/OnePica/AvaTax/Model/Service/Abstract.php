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
 * abstract class OnePica_AvaTax_Model_Service_Abstract
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */

abstract class OnePica_AvaTax_Model_Service_Abstract
{
    /**
     * Get rates from Service
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return string
     */
    abstract public function getRates($item);

    /**
     * Get rates data
     *
     * @return array
     */
    abstract public function getRatesData();
}
