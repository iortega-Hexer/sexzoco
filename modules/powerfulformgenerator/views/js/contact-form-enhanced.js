/**
 * @package   Powerful Form Generator
 * @author    Cyril Nicodème <contact@prestaddons.net>
 * @copyright Copyright (C) June 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
 * @since     2014-04-15
 * @version   2.7.9
 * @license   Nicodème Cyril
 */

jQuery(function ($) {
    if (typeof $.uniform !== 'undefined' && typeof $.uniform.defaults !== 'undefined')
    {
        if (typeof contact_fileDefaultHtml !== 'undefined')
            $.uniform.defaults.fileDefaultHtml = contact_fileDefaultHtml;
        if (typeof contact_fileButtonHtml !== 'undefined')
            $.uniform.defaults.fileButtonHtml = contact_fileButtonHtml;
    }

    $('.pfg-forms .pfg-datepicker-elements input').each(function () {
        var $this = $(this);
        $this.datepicker($this.data());
    });
});
