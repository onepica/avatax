<?php

/**
 * Class OnePica_AvaTaxAr2_Block_Head_Script
 */
class OnePica_AvaTaxAr2_Block_Head_Script extends Mage_Core_Block_Template
{
    /**
     * @return bool
     * @throws \Mage_Core_Exception
     */
    public function isEnabled()
    {
        return $this->_getConfigHelper()->isEnabled();
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avataxar2/config');
    }
}
