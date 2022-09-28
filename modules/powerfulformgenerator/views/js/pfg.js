/**
 * @package   Powerful Form Generator
 * @author    Cyril Nicodème <contact@prestaddons.net>
 * @copyright Copyright (C) June 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
 * @since     2014-04-15
 * @version   2.7.9
 * @license   Nicodème Cyril
 */

jQuery(function ($) {
    /**
     * Display or hide the message box for the senders based on the preferences of the user
     */
    $('#action_sender').on('change', function () {
        if ($(this).val() === 'message') {
            $('.message_senders').closest('.form-group').parent().closest('.form-group').show();
        } else {
            $('.message_senders').closest('.form-group').parent().closest('.form-group').hide();
        }
    }).trigger('change');

    /**
     * Display or hide the message box for the admins based on the preferences of the user
     */
    $('#action_admin').on('change', function () {
        if ($(this).val() === 'message') {
            $('.message_admins').closest('.form-group').parent().closest('.form-group').show();
        } else {
            $('.message_admins').closest('.form-group').parent().closest('.form-group').hide();
        }
    }).trigger('change');

    /**
     * Display or hide the redirect url box based on the access allowed.
     */
    $('#is_only_connected').on('change', function () {
        if ($(this).val() === '1') {
            $('.redirect_url').closest('.form-group').parent().closest('.form-group').show();
        } else {
            $('.redirect_url').closest('.form-group').parent().closest('.form-group').hide();
        }
    }).trigger('change');
});
