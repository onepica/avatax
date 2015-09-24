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
 * Class OnePica_AvaTax16_Document_Part
 */
class OnePica_AvaTax16_Document_Part
{
    /**
     * Required properties
     *
     * @var array
     */
    protected $_requiredProperties = array();

    /**
     * Properties get and set methods
     */
    public function __call($name, $arguments) {
        $action = substr($name, 0, 3);
        switch ($action) {
            case 'get':
                $property = '_' . lcfirst(substr($name, 3));
                if (property_exists($this,$property)) {
                    return $this->{$property};
                } else {
                    $trace = debug_backtrace();
                    $errorMessage = 'Undefined method  ' . $name . ' in ' . $trace[0]['file']
                                  . ' on line ' . $trace[0]['line'];
                    trigger_error($errorMessage, E_USER_ERROR);
                    return null;
                }
                break;
            case 'set':
                $property = '_' . lcfirst(substr($name, 3));
                if (property_exists($this,$property)) {
                    $this->{$property} = $arguments[0];
                } else {
                    $trace = debug_backtrace();
                    $errorMessage = 'Undefined method  ' . $name . ' in ' . $trace[0]['file']
                                  . ' on line ' . $trace[0]['line'];
                    trigger_error($errorMessage, E_USER_ERROR);
                    return null;
                }
                break;
            default :
                $trace = debug_backtrace();
                $errorMessage = 'Undefined method  ' . $name . ' in ' . $trace[0]['file']
                    . ' on line ' . $trace[0]['line'];
                trigger_error($errorMessage, E_USER_ERROR);
                return null;
        }
    }

    /**
     * Checks if document part is valid
     */
    public function isValid() {
        foreach ($this as $key => $value) {
            if (in_array($key, $this->_requiredProperties) && !$value) {
                return false;
            }
        }
        return true;
    }
}
