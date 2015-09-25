<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category  OnePica
 * @package   OnePica_AvaTax
 * @copyright Copyright (c) 2015 One Pica, Inc. (http://www.onepica.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class OnePica_AvaTax16_Document_Part_Locations
 */
class OnePica_AvaTax16_Document_Part_Locations extends OnePica_AvaTax16_Document_Part
{
    /**
     * Location types
     *
     * @var array
     */
    private $_types = array('ShipFrom', 'ShipTo', 'POS', 'POM', 'POO', 'BillingLocation', 'CallPlaced',
        'CallReceived', 'ServiceRendered', 'POA', 'FirstUse');

    /**
     * Locations
     *
     * @var array
     */
    private $_locations;

    /**
     * Set Location
     *
     * @param string $type
     * @param OnePica_AvaTax16_Document_Part_Location $location
     * @return $this
     */
    public function setLocation($type, OnePica_AvaTax16_Document_Part_Location $location)
    {
        if (in_array($type, $this->_types)) {
            $this->_locations[$type] = $location;
        }
        return $this;
    }

    /**
     * Convert object data to array
     *
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this->_locations as $key => $value) {
            $result[$key] = $this->_proceedToArrayItem($value);
        }
        return $result;
    }
}
