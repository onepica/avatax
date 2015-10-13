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
 * Class OnePica_AvaTax_Model_Calculator
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */

abstract class OnePica_AvaTax_Model_Calculator
{
    /**
     * Service
     *
     * @var mixed
     */
    protected $_service;

    /**
     * Construct
     *
     * @todo implement logic to init correct service
     */
    public function __construct()
    {
        // init service
        $this->_service = new OnePica_AvaTax_Model_Calculator_Service_Avatax();
    }
}

