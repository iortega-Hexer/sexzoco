/**
 * Cash On Delivery With Fee
 *
 * NOTICE OF LICENSE
 *
 * This product is licensed for one customer to use on one installation (test stores and multishop included).
 * Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
 * whole or in part. Any other use of this module constitues a violation of the user agreement.
 *
 * DISCLAIMER
 *
 * NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
 * ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
 * WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
 * PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
 * IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
 *
 *  @author    idnovate
 *  @copyright 2017 idnovate
 *  @license   See above
 */

jQuery('document').ready(function() {
    $("input:radio[name=filter_by_product], input:radio[name=filter_by_customer]").click(function() {
        toggleFields($(this).attr('name'));
    });
    toggleFields('filter_by_product');
    toggleFields('filter_by_customer');
    $("select[name=type]").change(function() {
        toggleFields($(this).attr('name'));
    });
    toggleFields('type');
    $("input:radio[name=filter_by_product]").change(function() {
        var products_el = $("select[name='products[]']");
        if (products_el.children('option').length === 0) {
            if ($(this).val() === '1') {
                $.ajax({
                    type: 'POST',
                    url: AdminCodfeeAjaxController,
                    async: true,
                    cache: false,
                    dataType: 'json',
                    data: 'entity=products',
                    success: function (jsonData) {
                        products_el.empty().multiselect('refresh');
                        jQuery.each( jsonData, function( key, value ) {
                            products_el.append($('<option></option>').attr('value', value.id_product).text(value.name));
                        });
                        products_el.multiselect('refresh');
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest);
                        if (textStatus !== 'abort') {
                            alert("TECHNICAL ERROR: AdminCodfeeAjaxController \n\nDetails:\nError thrown: " + errorThrown + "\n" + 'Text status: ' + textStatus);
                        }
                    }
                });
            }
        }
    });
    $("input:radio[name=filter_by_manufacturer]").change(function() {
        var manufacturers_el = $("select[name='manufacturers[]']");
        if (manufacturers_el.children('option').length === 0) {
            if ($(this).val() === '1') {
                $.ajax({
                    type: 'POST',
                    url: AdminCodfeeAjaxController,
                    async: true,
                    cache: false,
                    dataType: 'json',
                    data: 'entity=manufacturers',
                    success: function (jsonData) {
                        manufacturers_el.empty().multiselect('refresh');
                        jQuery.each( jsonData, function( key, value ) {
                            manufacturers_el.append($('<option></option>').attr('value', value.id_manufacturer).text(value.name));
                        });
                        manufacturers_el.multiselect('refresh');
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest);
                        if (textStatus !== 'abort') {
                            alert("TECHNICAL ERROR: AdminCodfeeAjaxController \n\nDetails:\nError thrown: " + errorThrown + "\n" + 'Text status: ' + textStatus);
                        }
                    }
                });
            }
        }
    });
    $("input:radio[name=filter_by_supplier]").change(function() {
        var suppliers_el = $("select[name='suppliers[]']");
        if (suppliers_el.children('option').length === 0) {
            if ($(this).val() === '1') {
                $.ajax({
                    type: 'POST',
                    url: AdminCodfeeAjaxController,
                    async: true,
                    cache: false,
                    dataType: 'json',
                    data: 'entity=suppliers',
                    success: function (jsonData) {
                        suppliers_el.empty().multiselect('refresh');
                        jQuery.each( jsonData, function( key, value ) {
                            suppliers_el.append($('<option></option>').attr('value', value.id_supplier).text(value.name));
                        });
                        suppliers_el.multiselect('refresh');
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest);
                        if (textStatus !== 'abort') {
                            alert("TECHNICAL ERROR: AdminCodfeeAjaxController \n\nDetails:\nError thrown: " + errorThrown + "\n" + 'Text status: ' + textStatus);
                        }
                    }
                });
            }
        }
    });
    $("input:radio[name=filter_by_customer]").change(function() {
        var customers_el = $("select[name='customers[]']");
        if (customers_el.children('option').length === 0) {
            if ($(this).val() === '1') {
                $.ajax({
                    type: 'POST',
                    url: AdminCodfeeAjaxController,
                    async: true,
                    cache: false,
                    dataType: 'json',
                    data: 'entity=customers',
                    success: function (jsonData) {
                        customers_el.empty().multiselect('refresh');
                        jQuery.each( jsonData, function( key, value ) {
                            console.log(value);
                            customers_el.append($('<option></option>').attr('value', value.id_customer).text(value.email));
                        });
                        customers_el.multiselect('refresh');
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest);
                        if (textStatus !== 'abort') {
                            alert("TECHNICAL ERROR: AdminCodfeeAjaxController \n\nDetails:\nError thrown: " + errorThrown + "\n" + 'Text status: ' + textStatus);
                        }
                    }
                });
            }
        }
    });
});

function toggleFields(fieldName)
{
    if ($('#'+fieldName) != null && $('#'+fieldName+'_on').is(':checked')) {
        $('.form-group').each(function() {
            if ($(this).find('.toggle_'+fieldName).length > 0) {
                if (!$(this).hasClass('translatable-field')) {
                    $(this).slideDown('slow');
                }
                if (id_language) {
                    if ($(this).hasClass('lang-'+id_language)) {
                        $(this).slideDown('slow');
                    }
                } else {
                    if ($(this).hasClass('lang-1')) {
                        $(this).slideDown('slow');
                    }
                }
            }
        });
        if (fieldName === 'filter_store') {
            $('.tree-panel-heading-controls').closest('.panel').closest('.form-group').slideDown('slow');
        }
    } else if ($('#'+fieldName).val() === '0' || $('#'+fieldName).val() === '1' || $('#'+fieldName).val() === '2') {
        $('.form-group').each(function() {
            if ($(this).find('.toggle_'+fieldName).length > 0) {
                if (!$(this).hasClass('translatable-field')) {
                    $(this).slideDown('slow');
                }
                if (id_language) {
                    if ($(this).hasClass('lang-'+id_language)) {
                        $(this).slideDown('slow');
                    }
                } else {
                    if ($(this).hasClass('lang-1')) {
                        $(this).slideDown('slow');
                    }
                }
            }
        });
        if ($('#'+fieldName).val() === '0') {
            $("select[name='amount_calc']").parent().parent().slideUp();
            $("input[name='percentage']").parent().parent().parent().slideUp();
            $("input[name='min']").parent().parent().parent().slideUp();
            $("input[name='max']").parent().parent().parent().slideUp();
        } else {
            $("select[name='amount_calc']").parent().parent().slideDown('slow');
            $("input[name='percentage']").parent().parent().parent().slideDown('slow');
            $("input[name='min']").parent().parent().parent().slideDown('slow');
            $("input[name='max']").parent().parent().parent().slideDown('slow');
        }
        $("input[name='round']").parent().parent().parent().show();
        $("input[name='free_on_freeshipping']").parent().parent().parent().show();
    } else {
        $("input[name='round']").parent().parent().parent().hide();
        $("input[name='free_on_freeshipping']").parent().parent().parent().hide();
        $('.form-group').each(function() {
            if ($(this).find('.toggle_'+fieldName).length > 0) {
                $(this).slideUp();
            }
        });
        if (fieldName === 'filter_store') {
            $('.tree-panel-heading-controls').closest('.panel').closest('.form-group').slideUp();
        }
    }
}
