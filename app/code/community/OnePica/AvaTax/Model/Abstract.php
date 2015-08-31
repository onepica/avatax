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
 * The abstract base AvaTax model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Abstract extends Varien_Object
{
    /**
     * The module helper
     *
     * @var OnePica_AvaTax_Helper_Data
     */
    protected $_helper = null;

    /**
     * Logs a debug message
     *
     * @param string $type
     * @param string $request the request string
     * @param string $result the result string
     * @param int $storeId id of the store the call is make for
     * @param mixed $additional any other info
     * @return $this
     */
    protected function _log($type, $request, $result, $storeId = null, $additional = null)
    {
        if ($result->getResultCode() == SeverityLevel::$Success) {
            switch (Mage::helper('avatax')->getLogMode($storeId)) {
                case OnePica_AvaTax_Model_Source_Logmode::ERRORS:
                    return $this;
                    break;
                case OnePica_AvaTax_Model_Source_Logmode::NORMAL:
                    $additional = null;
                    break;
            }
        }

        if (in_array($type, Mage::helper('avatax')->getLogType($storeId))) {
            Mage::getModel('avatax_records/log')
                ->setStoreId($storeId)
                ->setLevel($result->getResultCode())
                ->setType($type)
                ->setRequest(print_r($request, true))
                ->setResult(print_r($result, true))
                ->setAdditional($additional)
                ->save();
        }
        return $this;
    }

    /**
     * Returns the AvaTax session.
     *
     * @return OnePica_AvaTax_Model_Session
     */
    public function getSession ()
    {
        return Mage::getSingleton('avatax/session');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    public function getHelper ()
    {
        if (!$this->_helper) {
            $this->_helper = Mage::helper('avatax');
        }
        return $this->_helper;
    }

    //@startSkipCommitHooks
    /**
     * Alias to the helper translate method.
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        return call_user_func_array(array($this->getHelper(), '__'), $args);
    }
    //@finishSkipCommitHooks
}
