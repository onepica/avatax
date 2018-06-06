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
 * Adminhtml Documents grid block status item filter
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Block_Adminhtml_Customer_Documents_Grid_Filter_Status
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected static $_statuses;

    /**
     * OnePica_AvaTaxAr2_Block_Adminhtml_Customer_Exemption_Grid_Filter_Status constructor.
     */
    public function __construct()
    {
        self::$_statuses = array(
            null                                                      => null,
            OnePica_AvaTaxAr2_Model_Records_Document::STATUS_ACTIVE   => $this->__('Active'),
            OnePica_AvaTaxAr2_Model_Records_Document::STATUS_DISABLED => $this->__('Disabled'),
        );
        parent::__construct();
    }

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $result = array();
        foreach (self::$_statuses as $code => $label) {
            $result[] = array('value' => $code, 'label' => $this->__($label));
        }

        return $result;
    }

    /**
     * @return array|null
     */
    public function getCondition()
    {
        if ($this->getValue() === null) {
            return null;
        }

        return array('eq' => $this->getValue());
    }
}
