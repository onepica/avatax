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
/*global jQuery*/

jQuery.noConflict();

jQuery(document).ready(function () {
    // Landed Cost show multiselects
    jQuery(".avatax-lc-carrier-method").each(function (i, select) {
        var blockId = this.id + "_carrier_methods";
        var rowId = "row_" + this.id + "_" + this.value;

        jQuery("#" + blockId + " #" + rowId).show();
    });

    // Landed Cost show multiselect on select change
    jQuery('.avatax-lc-carrier-method').on('change', '', function (e) {
        var changedElement = this;
        var id = changedElement.id;
        var value = changedElement.value;
        var blockId = id + "_carrier_methods";
        var rowId = "row_" + id + "_" + value;

        jQuery("#" + blockId + " tr").each(function (i, select) {
            jQuery(this).hide();
        });

        jQuery("#" + blockId + " #" + rowId).show();
    });

    // Landed Cost prepare values in all multiselects on multiselect change
    jQuery(".avatax-lc-carrier-method-multiselect").on("change", "", function (e) {
        var changedElement = this;
        var id = changedElement.id;
        var selected = jQuery("#" + id + " option:selected");
        var parentId = jQuery(changedElement).closest("tbody").attr("id");
        jQuery(selected).each(function (i, select) {
            var neighborhoods = jQuery("#" + parentId + ' select option[value="' + select.value + '"]').not("#" + id + ' option[value="' + select.value + '"]');

            neighborhoods.each(function () {
                jQuery(this).prop('selected', false);
            });

            jQuery("#" + id + ' option[value="' + select.value + '"]').prop("selected", true);
        });
    });

    // Landed Cost DDP/DAP
    jQuery("#" + landedCost.idDDP).on("change", "", function (event) {
        landedCost.changeDdpDap(event);
    });

    jQuery("#" + landedCost.idDAP).on("change", "", function (event) {
        landedCost.changeDdpDap(event);
    });
});

var landedCost = {
    idDAP: "tax_avatax_landed_cost_landed_cost_dap_countries",
    idDDP: "tax_avatax_landed_cost_landed_cost_ddp_countries",

    changeDdpDap: function (event) {
        var target = event.target;
        var opposite = '';

        switch (target.id) {
            case this.idDAP:
                opposite = jQuery("#" + landedCost.idDDP)[0];
                break;
            case this.idDDP:
                opposite = jQuery("#" + landedCost.idDAP)[0];
                break;
            default:
                break;
        }

        var selectedTargetValues = this.collectValues(target.selectedOptions);

        jQuery(opposite.selectedOptions).each(function (i, option) {
            if (jQuery.inArray(option.value, selectedTargetValues) !== -1) {
                jQuery(option).prop('selected', false);
            }
        });
    },

    collectValues: function (options) {
        var values = [];
        jQuery(options).each(function (i, option) {
            values.push(option.value);
        });

        return values;
    }
};
