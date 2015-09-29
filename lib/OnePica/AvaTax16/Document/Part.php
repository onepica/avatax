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
     * Excluded properties (will be ignored during toArray function)
     *
     * @var array
     */
    protected $_excludedProperties = array();

    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $_propertyComplexTypes = array();

    /**
     * Properties get and set methods
     */
    public function __call($name, $arguments)
    {
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
     *
     * @return bool
     */
    public function isValid()
    {
        foreach ($this as $key => $value) {
            if (in_array($key, $this->_requiredProperties) && !$value) {
                return false;
            }
        }
        return true;
    }

    /**
     * Convert object data to array
     *
     * @return array
     */
    public function toArray()
    {
        if (!$this->isValid()) {
            throw new Exception("Not valid data in " . get_class($this));
        }
        $result = array();
        foreach ($this as $key => $value) {
            if (in_array($key, $this->_excludedProperties)
                || in_array($key, array('_requiredProperties', '_excludedProperties'))
                || !$value) {
                // skip property
                continue;
            }
            $name = substr($key, 1);
            $result[$name] = $this->_proceedToArrayItem($value);
        }
        return $result;
    }

    /**
     * Convert object data to array
     *
     * @param OnePica_AvaTax16_Document_Part|array|string $item
     * @return array|string
     */
    protected function _proceedToArrayItem($item)
    {
        $result = null;
        $itemType = ($item instanceof OnePica_AvaTax16_Document_Part) ? 'documentPart' :
                ((is_array($item)) ? 'array' : 'simple');

        switch ($itemType) {
            case 'documentPart':
                $result = $item->toArray();
                break;
            case 'array':
                foreach ($item as $key => $value) {
                    $result[$key] = $this->_proceedToArrayItem($value);
                }
                break;
            case 'simple':
                $result = $item;
                break;
        }

        return $result;
    }

    /**
     * Fill data from object
     *
     * @param StdClass|array $data
     * @return $this
     */
    public function fillData($data)
    {
        foreach ($data as $key => $value) {
            $propName = '_' . $key;
            $method = 'set' . ucfirst($key);
            if (isset($this->_propertyComplexTypes[$propName])) {
                $propertyType = $this->_propertyComplexTypes[$propName]['type'];
                if (isset($this->_propertyComplexTypes[$propName]['isArrayOf'])) {
                    $items = null;
                    foreach ($value as $itemKey => $itemData) {
                        $item = new $propertyType();
                        $item->fillData($itemData);
                        $items[$itemKey] = $item;
                    }
                    $this->$method($items);
                } else {
                    $item = new $propertyType();
                    $item->fillData($value);
                    $this->$method($item);
                }

            } else {
                $this->$method($value);
            }
        }
        return $this;
    }
}
