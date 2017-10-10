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
class OnePica_AvaTax_Model_Export_Adapter_Order_Sql extends OnePica_AvaTax_Model_Export_Adapter_Sql
{
    protected $_columnsToExport = null;

    /**
     * Get columns to export
     *
     * @return array
     */
    protected function _getColumns()
    {
        if ($this->_columns === null) {
            $this->_columns = $this->_columnsToExport;
        }

        return $this->_columns;
    }

    /**
     * Set columns from entity
     *
     * @param array $columns
     * @return $this
     */
    public function setColumnsToExport($columns)
    {
        $this->_columnsToExport = $columns;

        return $this;
    }
}
