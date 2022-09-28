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
*  @copyright 2020 idnovate
*  @license   See above
*/

function updateTotalsWithFee() {
    var js_checkout_summary = '';
    if (typeof $('#js-checkout-summary') !== 'undefined') {
        var js_checkout_summary = '#js-checkout-summary ';
    }
    var total_ori_html = $(js_checkout_summary + '.cart-summary-line.cart-total').last().html();
    if (typeof $('.cart-summary-line.cart-total_2').last().html() === 'undefined') {
        if ($(js_checkout_summary + '.cart-summary-totals div').length == 2) {
            if($(js_checkout_summary + '.cart-summary-totals div').last().prev().find('span.value').text() != '') {
                var total_ori_without_taxes_html = $(js_checkout_summary + '.cart-summary-totals div').last().prev().html();
            }
        } else {
            var total_ori_without_taxes_html = $(js_checkout_summary + '.cart-summary-totals .cart-summary-line.cart-total').last().prevAll('.cart-summary-line.cart-total').html();
        }

    } else {
        var total_ori_without_taxes_html = $('.cart-summary-line.cart-total_2').last().html();
    }
    if (typeof $('.cart-summary-line.cart-total').next().html() === 'undefined') {
        var taxes_ori_html = $(js_checkout_summary + '.cart-summary-line.cart-total').prev().html();
        if ($('#cart-subtotal-tax')) {
            var taxes_ori_html = $('#cart-subtotal-tax').find('span.value').html();
        }
    } else {
        var taxes_ori_html = $(js_checkout_summary + '.cart-summary-line.cart-total').next().html();
    }
    $("input[name='payment-option']").click(function() {
    //$(document).on('change', 'input[name="payment-option"]', function(e) {
        var codfee_id = $('#pay-with-' + this.id + '-form').find('input[name=codfee_id]').val();
        if (codfee_id != null && typeof codfee_id !== 'undefined') {
            var conditions_checkbox = $("input[name='cgv']");
            if ($("input[name='cgv']").size() == 0) {
                conditions_checkbox = $("input[name='conditions_to_approve[terms-and-conditions]']");
            }
            if (conditions_checkbox.size() == 0 || $("input[name='conditions_to_approve[terms-and-conditions]']").is(':checked')) {
                //document.querySelector('#payment-confirmation article').style.display = 'none';
                document.querySelector('#payment-confirmation button').removeAttribute('disabled');
            }
        }
        if (codfee_id != null && typeof codfee_id !== 'undefined' && $("input[name='codfee_type_" + codfee_id + "']").val() != '3') {
            /* Order fees
            $(this).on('change', function() { return false; });
            $(this).off('change');
            $('.cart-payment-discount').remove();
            setTimeout(function() { 
                $('.order-confirmation-table .cart-order-summary-codfee').prev().remove();
            }, 1000);
            Order fees */
            
            /*
            $('.js-additional-information.definition-list.additional-information, .js-payment-option-form').each(function() {
                $(this).css('display', 'none');
            });
            $('#' + this.id + '-additional-information').css('display', 'block');
            */
            $('#cart-subtotal-codfee').remove();
            /*$('#cart-subtotalsum-codfee').remove();*/
            if ($("input[name='codfee_free_flag_" + codfee_id + "']").val() == '1') {
                $("input[name='codfee_fee_" + codfee_id + "']").val($("input[name='codfee_free_txt_" + codfee_id + "']").val());
                $("input[name='codfee_fee_wt_" + codfee_id + "']").val($("input[name='codfee_free_txt_" + codfee_id + "']").val());
            }
            if ($("input[name='codfee_price_display_method_cartsummary_" + codfee_id + "']").val() == '1') {
                $('<div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-codfee">' +
                    '<span class="label cart-summary-codfee-label">' + $("input[name='codfee_text_" + codfee_id + "']").val() + '</span>' +
                    '<span class="value">' + $("input[name='codfee_fee_" + codfee_id + "']").val() + '</span>' +
                    '</div>').insertAfter(js_checkout_summary + '#cart-subtotal-shipping');
            } else {
                $('<div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-codfee">' +
                    '<span class="label cart-summary-codfee-label">' + $("input[name='codfee_text_" + codfee_id + "']").val() + '</span>' +
                    '<span class="value">' + $("input[name='codfee_fee_wt_" + codfee_id + "']").val() + '</span>' +
                    '</div>').insertAfter(js_checkout_summary + '#cart-subtotal-shipping');
            }
            /*$('<div class="cart-summary-line cart-summary-subtotals" id="cart-subtotalsum-codfee">' +
                '<span class="label">' + $("input[name='codfee_text_" + codfee_id + "']").val() + '</span>' +
                '<span class="value">' + $("input[name='codfee_fee_" + codfee_id + "']").val() + '</span>' +
                '</div>').insertBefore($('.cart-summary-line.cart-total').prev());*/
            if (typeof $('.cart-summary-line.cart-total_2').last().html() === 'undefined') {
                $(js_checkout_summary + '.cart-summary-totals .cart-summary-line.cart-total').last().prevAll('.cart-summary-line.cart-total').find('span.value').html($("input[name='codfee_total_without_taxes_" + codfee_id + "']").val());
            } else {
                $('.cart-summary-line.cart-total_2').find('span.value').html($("input[name='codfee_total_without_taxes_" + codfee_id + "']").val());
            }
            if ($("input[name='codfee_price_display_method_cartsummary_" + codfee_id + "']").val() == '1') {
                $(js_checkout_summary + '.cart-summary-line.cart-total').last().find('span.value').html($("input[name='codfee_total_with_taxes_" + codfee_id + "']").val());
                $(js_checkout_summary + '.cart-summary-line.cart-total').last().find('strong.value').html($("input[name='codfee_total_with_taxes_" + codfee_id + "']").val());
            } else {
                if ($(js_checkout_summary + '.cart-summary-totals div').length == 2) {
                    if($(js_checkout_summary + '.cart-summary-totals div').last().prev().find('span.value').text() != '') {
                        $(js_checkout_summary + '.cart-summary-totals div').last().prev().find('span.value').html($("input[name='codfee_total_without_taxes_" + codfee_id + "']").val());
                    }
                    if($(js_checkout_summary + '.cart-summary-totals div').last().find('span.value').text() != '') {
                        $(js_checkout_summary + '.cart-summary-totals div').last().find('span.value').html($("input[name='codfee_total_with_taxes_" + codfee_id + "']").val());
                    }
                } else if ($(js_checkout_summary + '.cart-summary-totals div').length == 3) {
                    if($(js_checkout_summary + '.cart-summary-totals div').last().prev().find('span.value').text() != '') {
                        $(js_checkout_summary + '.cart-summary-totals div').last().prev().find('span.value').html($("input[name='codfee_total_without_taxes_" + codfee_id + "']").val());
                    }
                    if($(js_checkout_summary + '.cart-summary-totals div').last().find('span.value').text() != '') {
                        $(js_checkout_summary + '.cart-summary-totals div').last().find('span.value').html($("input[name='codfee_total_with_taxes_" + codfee_id + "']").val());
                    }
                } else {
                    $(js_checkout_summary + '.cart-summary-line.cart-total').last().find('span.value').html($("input[name='codfee_total_without_taxes_" + codfee_id + "']").val());
                    $(js_checkout_summary + '.cart-summary-line.cart-total').last().find('strong.value').html($("input[name='codfee_total_without_taxes_" + codfee_id + "']").val());
                }
            }
            if ($("input[name='codfee_tax_enabled_" + codfee_id + "']").val() == '1' && $("input[name='codfee_tax_display_" + codfee_id + "']").val() == '1') {
                if ($(js_checkout_summary + '.cart-summary-line.cart-total').next().find('span.value').text() != "") {
                    $(js_checkout_summary + '.cart-summary-line.cart-total').next().find('span.value').html($("input[name='codfee_taxes_" + codfee_id + "']").val());
                }
                if (typeof $('.cart-summary-line.cart-total_2').last().html() !== 'undefined') {
                    $(js_checkout_summary + '.cart-summary-line.cart-total').prev().find('span.value').html($("input[name='codfee_taxes_" + codfee_id + "']").val());
                }
                if ($('#cart-subtotal-tax').size() > 0) {
                    $('.cart-summary-codfee-label').removeClass('label');
                    $('#cart-subtotal-tax').find('span.value').html($("input[name='codfee_taxes_" + codfee_id + "']").val());
                }
            }
        } else if ($(this).attr('data-module-name') != 'paypalwithfee' && $(this).is(':checked')) {
                /*
                $('#' + this.id + '-additional-information').css('display', 'none');
                */
                $('#cart-subtotal-codfee').remove();
                /*$('#cart-subtotalsum-codfee').remove();*/
                $(js_checkout_summary + '.cart-summary-line.cart-total').last().html(total_ori_html);
                if (typeof $(js_checkout_summary + '.cart-summary-line.cart-total_2').last().html() === 'undefined') {
                    if ($(js_checkout_summary + '.cart-summary-totals div').length == 2) {
                        $(js_checkout_summary + '.cart-summary-totals div').last().prev().html(total_ori_without_taxes_html);
                    } else {
                        $(js_checkout_summary + '.cart-summary-totals .cart-summary-line.cart-total').last().prevAll('.cart-summary-line.cart-total').html(total_ori_without_taxes_html);
                    }
                } else {
                    $(js_checkout_summary + '.cart-summary-line.cart-total_2').first().html(total_ori_without_taxes_html);
                }
                if (typeof $(js_checkout_summary + '.cart-summary-line.cart-total').next().html() === 'undefined') {
                    $(js_checkout_summary + '.cart-summary-line.cart-total').prev().html(taxes_ori_html);
                    if ($('#cart-subtotal-tax').size() > 0) {
                        $('#cart-subtotal-tax').find('span.value').html(taxes_ori_html);
                    }
                } else {
                    $(js_checkout_summary + '.cart-summary-line.cart-total').next().html(taxes_ori_html);
                }
        } else {
            /*
            $('#' + this.id + '-additional-information').css('display', 'none');
            */
            $(js_checkout_summary + '#cart-subtotal-codfee').remove();
            /*$('#cart-subtotalsum-codfee').remove();*/
            //$(js_checkout_summary + '.cart-summary-line.cart-total').first().html(total_ori_html);
            $(js_checkout_summary + '.cart-summary-line.cart-total_2').first().html(total_ori_without_taxes_html);
            if ($(js_checkout_summary + '.cart-summary-totals div').length == 2) {
                $(js_checkout_summary + '.cart-summary-totals div').last().prev().html(total_ori_without_taxes_html);
            }
            if (typeof $(js_checkout_summary + '.cart-summary-line.cart-total').next().html() === 'undefined') {
                $(js_checkout_summary + '.cart-summary-line.cart-total').prev().html(taxes_ori_html);
            } else {
                $(js_checkout_summary + '.cart-summary-line.cart-total').next().html(taxes_ori_html);
            }
            $(js_checkout_summary + '.cart-summary-line.cart-total').last().html(total_ori_html);
        }
    });
}

function updateOrderSummaryWithFee() {
    var ps176 = false;
    var ps1761 = false;
    if (typeof $('.order-confirmation-table .order-confirmation-total').html() !== 'undefined') {
        ps1761 = true;
        var table_totals = $('.order-confirmation-table div');
        var total_ori_html = table_totals.last().html();
        var taxes_ori_html = table_totals.last().prev().prev().html();
    } else if (typeof $('.order-confirmation-table .taxes').html() === 'undefined') {
        var total_ori_html = $('.order-confirmation-table table tr td').last().html();
        var taxes_ori_html = $('.order-confirmation-table table tr').last().prev().last().html();
    } else {
        ps176 = true;
        var total_ori_html = $('.order-confirmation-table table tr').last().prev().last().html();
        var taxes_ori_html = $('.order-confirmation-table .taxes').html();
    }
    $("input[name='payment-option']").click(function() {
        var codfee_id = $('#pay-with-' + this.id + '-form').find('input[name=codfee_id]').val();
        if (codfee_id != null && $("input[name='codfee_type_" + codfee_id + "']").val() != '3') {
            $('tr.cart-order-summary-codfee').remove();
            if ($("input[name='codfee_free_flag_" + codfee_id + "']").val() == '1') {
                $("input[name='codfee_fee_" + codfee_id + "']").val($("input[name='codfee_free_txt_" + codfee_id + "']").val());
                $("input[name='codfee_fee_wt_" + codfee_id + "']").val($("input[name='codfee_free_txt_" + codfee_id + "']").val());
            }
            if ($("input[name='codfee_price_display_method_cartsummary_" + codfee_id + "']").val() == '1') {
                $('<tr class="cart-order-summary-codfee">' +
                    '<td>' + $("input[name='codfee_text_" + codfee_id + "']").val() + '</td>' +
                    '<td>' + $("input[name='codfee_fee_" + codfee_id + "']").val() + '</td>' +
                    '</tr>').insertBefore($('.order-confirmation-table table tr').last().prev());
                if (ps1761) {
                    $('<div class="col-8 cart-order-summary-codfee">' +
                        '<label>' + $("input[name='codfee_text_" + codfee_id + "']").val() + '</label></div>' +
                        '<div class="col-4 cart-order-summary-codfee"><span class="price price-normal">' + $("input[name='codfee_fee_" + codfee_id + "']").val() + '</span></div>' +
                        '').insertBefore(table_totals.last().prev().prev().prev());
                }
            } else {
                $('<tr class="cart-order-summary-codfee">' +
                    '<td>' + $("input[name='codfee_text_" + codfee_id + "']").val() + '</td>' +
                    '<td>' + $("input[name='codfee_fee_wt_" + codfee_id + "']").val() + '</td>' +
                    '</tr>').insertBefore($('.order-confirmation-table table tr').last().prev());
            }
            if (ps176) {
                $('.order-confirmation-table table tr').last().prev().find('td').last().html($("input[name='codfee_total_with_taxes_" + codfee_id + "']").val());
                if ($("input[name='codfee_tax_enabled_" + codfee_id + "']").val() == '1' && $("input[name='codfee_tax_display_" + codfee_id + "']").val() == '1') {
                    $('.order-confirmation-table .taxes').find('td span').last().html($("input[name='codfee_taxes_" + codfee_id + "']").val());
                }
            } else if (ps1761) {
                $('.order-confirmation-table div span').last().html($("input[name='codfee_total_with_taxes_" + codfee_id + "']").val());
                if ($("input[name='codfee_tax_enabled_" + codfee_id + "']").val() == '1' && $("input[name='codfee_tax_display_" + codfee_id + "']").val() == '1') {
                    table_totals.last().prev().prev().html($("input[name='codfee_taxes_" + codfee_id + "']").val());
                }
            } else {
                $('.order-confirmation-table table tr td').last().html($("input[name='codfee_total_with_taxes_" + codfee_id + "']").val());
                if ($("input[name='codfee_tax_enabled_" + codfee_id + "']").val() == '1' && $("input[name='codfee_tax_display_" + codfee_id + "']").val() == '1') {
                    $('.order-confirmation-table table tr').last().prev().find('td').last().html($("input[name='codfee_taxes_" + codfee_id + "']").val());
                }
            }
        } else {
            $('tr.cart-order-summary-codfee, div.cart-order-summary-codfee').remove();
            if (ps176) {
                $('.order-confirmation-table table tr').last().prev().last().html(total_ori_html);
                $('.order-confirmation-table .taxes').html(taxes_ori_html);
            } else if (ps1761) {
                table_totals.last().html(total_ori_html);
                table_totals.last().prev().prev().html(taxes_ori_html);
            } else {
                $('.order-confirmation-table table tr td').last().html(total_ori_html);
                $('.order-confirmation-table table tr').last().prev().last().html(taxes_ori_html);
            }
        }
    });
}

$(document).ready(function() {
    /*
    $("input[name='payment-option']").each(function() {
        if ($(this).attr('data-module-name') === 'codfee') {
            $(this).on('change', function() {return false;});
        }
    });
    */
    updateTotalsWithFee();
    updateOrderSummaryWithFee();
    if ($('input[data-module-name=codfee]:checked').attr('checked') === 'checked') {
        $('input[data-module-name=codfee]').click();
    }
    if (typeof prestashop !== 'undefined') {
        prestashop.on('updatedCart', function() {
            if ($('input[data-module-name=codfee]:checked').length > 0) {
                window.location.replace(window.location.href + '?codfee');
            }
        });
        if (window.location.href.indexOf("?codfee") !== -1) {
            $('input[data-module-name=codfee]').click();
        }
    }
});
