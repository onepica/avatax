<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * Class OnePica_AvaTaxAr2_Block_Popup_Form
 */
class OnePica_AvaTaxAr2_Block_Popup_Form extends Mage_Core_Block_Template
{
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

}
