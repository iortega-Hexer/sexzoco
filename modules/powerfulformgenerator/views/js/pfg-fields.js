/**
 * @package   Powerful Form Generator
 * @author    Cyril Nicodème <contact@prestaddons.net>
 * @copyright Copyright (C) June 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
 * @since     2014-04-15
 * @version   2.7.9
 * @license   Nicodème Cyril
 */

var slugify = function(str) {
  str = str.replace(/^\s+|\s+$/g, ''); // trim
  str = str.toLowerCase();

  // remove accents, swap ñ for n, etc
  var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
  var to   = "aaaaaeeeeeiiiiooooouuuunc______";
  for (var i=0, l=from.length ; i<l ; i++) {
    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
  }

  str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
    .replace(/\s+/g, '_') // collapse whitespace and replace by -
    .replace(/-+/g, '_'); // collapse dashes

  return str;
};

jQuery(function ($) {
	/**
	 * Display textareas for the messages sent to the admins and senders,
	 * depending on the choice of the user.
	 */
	$('#pfg-field-select-types').on('change', function () {
		var val = $(this).val();

		$('.field-select-items').hide();
		if (val === 'checkbox' || val === 'radio' || val === 'select' || val === 'file' || val === 'multicheckbox' || val === 'hidden' || val === 'static') {
			if (val === 'multicheckbox') val = 'select';

			$('.field-select-items').parent().find('.field-' + val).show();
			$('.pfg-fields-values').prop('disabled', false);
		} else {
			$('.pfg-fields-values').prop('disabled', true);
		}

	}).trigger('change');

	$('.label-field').on('change', function () {
		var $this = $(this),
			nameInput = $('#pfg_fields_form #name');
		if (nameInput.val() === '') {
			nameInput.val(slugify($this.val()));
		}
	});
});
