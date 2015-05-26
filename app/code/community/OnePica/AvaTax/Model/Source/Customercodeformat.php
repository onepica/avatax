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
 * Customer code format source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_Customercodeformat
{
    /**
     * Legacy format
     */
    const LEGACY = 0; //depricated, included for stores who are upgrading so the code format doesn't change unexpectedly

    /**
     * Customer id format
     */
    const CUST_ID = 1; //recommended

    /**
     * Customer email format
     */
    const CUST_EMAIL = 2;

    /**
     * Gets the list of cache methods for the admin config dropdown
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::CUST_ID,
                'label' => 'customer_id'
            ),
            array(
                'value' => self::CUST_EMAIL,
                'label' => 'customer_email'
            ),
            array(
                'value' => self::LEGACY,
                'label' => 'customer_name (customer_id)'
            )
        );
    }
}
