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
 * Buyer type source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @todo       Remove this class if we will not use those properties
 */
class OnePica_AvaTax_Model_Source_Avatax16_Buyertype
{
    /**
     * Resale
     */
    const RESALE = 'G';

    /**
     * Agricultural Production
     */
    const AGRICULTURAL_PRODUCTION = 'H';

    /**
     * Industrial Prod/Mfg.
     */
    const INDUSTRIAL_PROD_MFG = 'I';

    /**
     * Direct Pay Permit
     */
    const DIRECT_PAY_PERMIT = 'J';

    /**
     * Direct Mail
     */
    const DIRECT_MAIL = 'K';

    /**
     * Other
     */
    const OTHER = 'L';

    /**
     * Commercial Aquaculture (Canada)
     */
    const COMMERCIAL_AQUACULTURE = 'P';

    /**
     * Commercial Fishery (Canada)
     */
    const COMMERCIAL_FISHERY = 'Q';

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
                'value' => self::RESALE,
                'label' => Mage::helper('avatax')->__('G - Resale')
            ),
            array(
                'value' => self::AGRICULTURAL_PRODUCTION,
                'label' => Mage::helper('avatax')->__('H - Agricultural Production')
            ),
            array(
                'value' => self::INDUSTRIAL_PROD_MFG,
                'label' => Mage::helper('avatax')->__('I - Industrial Prod/Mfg.')
            ),
            array(
                'value' => self::DIRECT_PAY_PERMIT,
                'label' => Mage::helper('avatax')->__('J - Direct Pay Permit')
            ),
            array(
                'value' => self::DIRECT_MAIL,
                'label' => Mage::helper('avatax')->__('K - Direct Mail')
            ),
            array(
                'value' => self::OTHER,
                'label' => Mage::helper('avatax')->__('L - Other')
            ),
            array(
                'value' => self::COMMERCIAL_AQUACULTURE,
                'label' => Mage::helper('avatax')->__('P - Commercial Aquaculture (Canada)')
            ),
            array(
                'value' => self::COMMERCIAL_FISHERY,
                'label' => Mage::helper('avatax')->__('Q - Commercial Fishery (Canada)')
            )
        );
    }
}
