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
 * The OnePica_AvaTaxAr2_Block_Adminhtml_Export_Log_View class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Block_Adminhtml_Export_Log_View extends OnePica_AvaTax_Block_Adminhtml_Export_Log_View
{
    /**
     * Prepare html output
     *
     * @return string
     */
    public function _toHtml()
    {
        $event = $this->getCurrentEvent();

        if (strpos($event->getType(), 'RestV2') !== false || strpos($event->getType(), 'CertAPI') !== false) {
            $this->setTemplate('onepica/avataxar2/log/view.phtml');
        }

        return parent::_toHtml();
    }
}
