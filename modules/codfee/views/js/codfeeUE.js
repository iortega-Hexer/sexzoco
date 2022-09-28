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
*  @version   2.0.1
*  @copyright 2016 idnovate
*  @license   See above
*/

$(document).ready(function() {
	$(document).on('change', 'input.delivery_option_radio', function(){
		var key = $(this).data('key');
		var id_address = parseInt($(this).data('id_address'));
		if (orderProcess == 'order' && key && id_address) {
			updateExtraCarrier(key, id_address);
		} else if(orderProcess == 'order-opc' && typeof updateCarrierSelectionAndGift !== 'undefined') {
			$("html").css('opacity','0.3');
			updateCarrierSelectionAndGift();
			location.reload();
		}
	});
	$(document).on('click', 'a.payment_module_adv', function(e, i){
        if ($('p.payment_selected').find('img').attr('src').indexOf('codfee') != -1) {
            $("input[type='hidden'][name='codfee_base']").val($('#total_price').text());
            $('#total_price').html('<span class="price">' + $("input[type='hidden'][name='codfee_included']").val() + '</span><br />' + $("input[type='hidden'][name='codfee_total']").val());
            //$('tr.cart_total_tax').before('<tr class="cart_total_codfee"><td colspan="3" class="text-right">' + $("input[type='hidden'][name='codfee_name']").val() + '</td><td colspan="2" class="price" id="total_codfee">' + $("input[type='hidden'][name='codfee_fee']").val() + '</td></tr>');
        } else {
            $('#total_price').text($("input[type='hidden'][name='codfee_base']").val());
        }
	});
});

function updateCarrierSelectionAndGift()
{
	var recyclablePackage = 0;
	var gift = 0;
	var giftMessage = '';

	var delivery_option_radio = $('.delivery_option_radio');
	var delivery_option_params = '&';
	$.each(delivery_option_radio, function(i) {
		if ($(this).prop('checked'))
			delivery_option_params += $(delivery_option_radio[i]).attr('name') + '=' + $(delivery_option_radio[i]).val() + '&';
	});
	if (delivery_option_params == '&')
		delivery_option_params = '&delivery_option=&';

	if ($('input#recyclable:checked').length)
		recyclablePackage = 1;
	if ($('input#gift:checked').length)
	{
		gift = 1;
		giftMessage = encodeURIComponent($('#gift_message').val());
	}

	$('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		async: false,
		cache: false,
		dataType : "json",
		data: 'ajax=true&method=updateCarrierAndGetPayments' + delivery_option_params + 'recyclable=' + recyclablePackage + '&gift=' + gift + '&gift_message=' + giftMessage + '&token=' + static_token ,
		success: function(jsonData)
		{
			if (jsonData.hasError)
			{
				var errors = '';
				for(var error in jsonData.errors)
					//IE6 bug fix
					if(error !== 'indexOf')
						errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
	            if (!!$.prototype.fancybox)
	                $.fancybox.open([
	                    {
	                        type: 'inline',
	                        autoScale: true,
	                        minHeight: 30,
	                        content: '<p class="fancybox-error">' + errors + '</p>'
	                    }
	                ], {
	                    padding: 0
	                });
	            else
	                alert(errors);
			}
			else
			{
				updateCartSummary(jsonData.summary);
				updatePaymentMethods(jsonData);
				updateHookShoppingCart(jsonData.summary.HOOK_SHOPPING_CART);
				updateHookShoppingCartExtra(jsonData.summary.HOOK_SHOPPING_CART_EXTRA);
				updateCarrierList(jsonData.carrier_data);
				$('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
				refreshDeliveryOptions();
				if (typeof bindUniform !=='undefined')
					bindUniform();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus !== 'abort')
				alert("TECHNICAL ERROR: unable to save carrier \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			$('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
		}
	});
}