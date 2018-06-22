<?php

/**
 * Class OnePica_AvaTaxAr2_Block_ActionButton
 */
class OnePica_AvaTaxAr2_Block_ActionButton extends Mage_Core_Block_Template
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
     * @return string
     */
    public function getToken()
    {
        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjdXN0b21lcl9udW1iZXIiOiIxMDAiLCJjbGllbnRfaWQiOiI3MTAxMSIsImVjb21tIjp0cnVlLCJzdWIiOjEwMzM4MywiaXNzIjoiaHR0cDovL2FwaS5jZXJ0Y2FwdHVyZS5jb20vdjIvYXV0aC9nZXQtdG9rZW4iLCJpYXQiOjE1Mjk1NDI3NzAsImV4cCI6MTUyOTU0NjM3MCwibmJmIjoxNTI5NTQyNzcwLCJqdGkiOiJTbWdXeFlnMHM3Vktwem40In0.ix8-neBixHq3NGFbDtlKMEtnxe6SHYbGpU6stZU2-bk';
    }

    /**
     * @return string
     */
    public function getShipZone()
    {
        return 'Washington';
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avataxar2/config');
    }
}
