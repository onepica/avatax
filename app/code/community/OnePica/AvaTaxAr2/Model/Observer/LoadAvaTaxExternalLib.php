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
 * Avatax Observer LoadAvaTaxExternalLib
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Observer_LoadAvaTaxExternalLib extends Mage_Core_Model_Abstract
{
    protected static $_classesRestV2 = array(
        'AvaTaxClient',
        'Avalara\AvaTaxRestV2\AvaTaxClient',
        'Avalara\AvaTaxRestV2\CustomerModel'
    );

    /**
     * Load AvaTax External Lib
     *
     * @return $this
     */
    public function loadAvaTaxExternalLib()
    {
        spl_autoload_register(array($this, 'loadLib'), false, true);

        return $this;
    }

    /**
     * This function can autoloads classes to work with Avalara API
     *
     * @param string $class
     */
    public static function loadLib($class)
    {
        if (in_array($class, self::$_classesRestV2)) {
            /** @var OnePica_AvaTaxAr2_Helper_Lib $helper */
            $helper = Mage::helper('avataxar2/lib');
            $helper->loadClasses();
        }
    }

    /**
     * @return $this
     */
    public function loadAvaTaxEcomLib()
    {
        /** @var OnePica_AvaTaxAr2_Helper_Lib $helper */
        $helper = Mage::helper('avataxar2/lib');
        spl_autoload_register(array($helper, 'loadEcomClass'), false, true);

        return $this;
    }

    /**
     * This an observer function for the event 'controller_front_init_before' and 'default'
     * It prepends our autoloader, so we can load the extra libraries.
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $this->loadAvaTaxExternalLib();
        $this->loadAvaTaxEcomLib();

        return $this;
    }
}
