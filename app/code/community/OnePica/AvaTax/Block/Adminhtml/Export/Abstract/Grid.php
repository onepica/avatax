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
 * Avatax admin abstract grid
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Block_Adminhtml_Export_Abstract_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor: sets grid id and sort order
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('export_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);

        $url = Mage::helper('avatax')->getDocumentationUrl();
        Mage::helper('adminhtml')->setPageHelpUrl($url);
    }

    /**
     * Mass adds columns based on passed in array
     *
     * @param array $columns array(columnName => dataType)
     * @return $this
     * @throws Exception
     */
    protected function _addColumns($columns)
    {
        foreach ($columns as $name => $type) {
            if (is_array($type)) {
                $this->addColumn(
                    $name, array(
                        'header'  => Mage::helper('avatax')->__(ucwords(str_replace('_', ' ', $name))),
                        'index'   => $name,
                        'type'    => 'options',
                        'options' => $type
                    )
                );
            } else {
                $this->addColumn(
                    $name, array(
                        'header' => Mage::helper('avatax')->__(ucwords(str_replace('_', ' ', $name))),
                        'index'  => $name,
                        'type'   => $type
                    )
                );
            }
        }
        return $this;
    }
}
