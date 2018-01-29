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
 * Sql export adapter
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Export_Adapter_Json extends OnePica_AvaTax_Model_Export_Adapter_Abstract
{
    /**
     * Columns to export
     *
     * @var array
     */
    protected $_columns = null;

    /**
     * Get content
     *
     * @return string
     * @throws OnePica_AvaTax_Exception
     */
    public function getContent()
    {
        if (!$this->getCollection()) {
            throw new OnePica_AvaTax_Exception('Collection should be set before export process');
        }

        $content = $this->_getExportArray();

        return json_encode($content);
    }

    /**
     *
     */
    protected function _getExportArray()
    {
        $exportArray = array();
        foreach ($this->getCollection() as $item) {
            $exportArray[] = $item->getData();
        }
        return $exportArray;
    }
}
