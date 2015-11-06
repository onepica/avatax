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
 * Use type source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @todo       Remove this class if we will not use those properties
 */
class OnePica_AvaTax_Model_Source_Avatax16_Usetype
{
    /**
     * Federal Government
     */
    const FEDERAL_GOVERNMENT = 'A';

    /**
     * State/Local Govt.
     */
    const STATE_LOCAL_GOVT = 'B';

    /**
     * Tribal Government
     */
    const TRIBAL_GOVERNMENT = 'C';

    /**
     * Foreign Diplomat
     */
    const FOREIGN_DIPLOMAT = 'D';

    /**
     * Charitable Organization
     */
    const CHARITABLE_ORGANIZATION = 'E';

    /**
     * Religious/Education
     */
    const RELIGIOUS_EDUCATION = 'F';

    /**
     * Other
     */
    const OTHER = 'L';

    /**
     * Local Government
     */
    const LOCAL_GOVERNMENT = 'N';

    /**
     * Non-resident (Canada)
     */
    const NON_RESIDENT = 'R';

    /**
     * Gets the list of type for the admin config dropdown
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '',
                'label' => ''
            ),
            array(
                'value' => self::FEDERAL_GOVERNMENT,
                'label' => Mage::helper('avatax')->__('A - Federal Government')
            ),
            array(
                'value' => self::STATE_LOCAL_GOVT,
                'label' => Mage::helper('avatax')->__('B - State/Local Govt.')
            ),
            array(
                'value' => self::TRIBAL_GOVERNMENT,
                'label' => Mage::helper('avatax')->__('C - Tribal Government')
            ),
            array(
                'value' => self::FOREIGN_DIPLOMAT,
                'label' => Mage::helper('avatax')->__('D - Foreign Diplomat')
            ),
            array(
                'value' => self::CHARITABLE_ORGANIZATION,
                'label' => Mage::helper('avatax')->__('E - Charitable Organization')
            ),
            array(
                'value' => self::RELIGIOUS_EDUCATION,
                'label' => Mage::helper('avatax')->__('F - Religious/Education')
            ),
            array(
                'value' => self::OTHER,
                'label' => Mage::helper('avatax')->__('L - Other')
            ),
            array(
                'value' => self::LOCAL_GOVERNMENT,
                'label' => Mage::helper('avatax')->__('N - Local Government')
            ),
            array(
                'value' => self::NON_RESIDENT,
                'label' => Mage::helper('avatax')->__('R - Non-resident (Canada)')
            )
        );
    }
}
