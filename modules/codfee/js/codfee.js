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
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2014 idnovate.com
*  @license   See above
*/

$(document).ready(function() {
	$('.ajax_cart_block_remove_link').click(function() {
		updateCarrierSelectionAndGift();
	});
	$('.cart_quantity_button').click(function() {
		updateCarrierSelectionAndGift();
	});
	$('.cart_quantity_delete').click(function() {
		updateCarrierSelectionAndGift();
	});
	$('#cgv').click(function() {
		setTimeout(function() {
			updateCarrierSelectionAndGift();
		}, 2000);
	});
	$('#submitAccount').click(function() {
		setTimeout(function() {
			updateCarrierSelectionAndGift();
		}, 2000);
	});
	$('#SubmitLogin').click(function() {
		setTimeout(function() {
			updateCarrierSelectionAndGift();
		}, 2000);
	});
	$('#id_country').change(function() {
		setTimeout(function() {
			updateCarrierSelectionAndGift();
		}, 2500);
	});
	$("#login_form").submit(function() {
		setTimeout(function() {
			updateCarrierSelectionAndGift();
		}, 2000);
	});
	$("#new_account_form").submit(function() {
		setTimeout(function() {
			updateCarrierSelectionAndGift();
		}, 2000);
	});
});
