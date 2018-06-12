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
 * Adminhtml Documents grid block ExemptionReasonName item renderer
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Block_Adminhtml_Customer_Documents_Grid_Renderer_ExemptionReasonName
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        return $this->getExemptionReasonName($row->getData('exemptionReason'));
    }

    /**
     * @param array|stdClass $exemptionReason
     * @return mixed|string
     */
    public static function getExemptionReasonName($exemptionReason)
    {
        if (is_array($exemptionReason) && isset($exemptionReason['name'])) {
            return $exemptionReason['name'];
        }

        if (is_object($exemptionReason) && isset($exemptionReason->name)) {
            return $exemptionReason->name;
        }

        return null;
    }
}
