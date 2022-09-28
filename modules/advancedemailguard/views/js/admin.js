/**
 * Advanced Anti Spam PrestaShop Module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    ReduxWeb
 * @copyright 2017-2022 reduxweb.net
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // Rating message.
  var $rating = $('#ratingMessage');
  if ($rating.length !== 0) {
    var dismissRating = function () {
      $rating.remove();
      $.ajax({
        url: app.url,
        method: 'GET',
        data: { _action: 'rating.dismiss' },
      });
    };

    $rating.find('.js-rating-dismiss:first').on('click', function (e) {
      e.preventDefault();
      dismissRating();
    });
    $rating.find('.js-rating-link:first').on('click', function () {
      dismissRating();
    });
  }

  // Hack to keep the dropdown open when clicked inside.
  $(document).on('click', '.js-dropdown-stop .dropdown-menu', function (e) {
    e.stopPropagation();
  });

  // Init bootstrap/jQuery plugins.
  var initPlugins = function ($parent) {
    $parent.find('[data-toggle="tooltip"],[data-toggle2="tooltip"]').tooltip();
    $parent.find('[data-toggle="popover"]').popover();

    $parent.find('input.switch').bootstrapSwitch({
      onText: app.trans.yes,
      offText: app.trans.no,
      onSwitchChange: function () {
        var target = $(this).data('target');
        if (target) {
          $(target).stop().slideToggle();
        }
      },
    });

    $parent.find('select.select2').select2({
      width: '100%',
      minimumResultsForSearch: -1,
    });

    $parent
      .find('select.select2-tags')
      .select2({
        width: '100%',
        tags: true,
        tokenSeparators: [','],
        minimumResultsForSearch: 1,
      })
      .on('select2:open', function () {
        $('.select2-container--open .select2-dropdown--below').css(
          'display',
          'none'
        );
      });

    $parent
      .find('select.select2-texts')
      .select2({
        width: '100%',
        tags: true,
        minimumResultsForSearch: 1,
      })
      .on('select2:open', function () {
        $('.select2-container--open .select2-dropdown--below').css(
          'display',
          'none'
        );
      });

    // Custom components.
    $parent.find('.js-radio-toggle .custom-radio > input').change(function () {
      var self = this;
      $(this)
        .closest('.js-radio-toggle')
        .find('.custom-radio > input')
        .each(function () {
          var $this = $(this);
          var $target = $($this.data('target'));
          var show = self === this;
          if (show) {
            $target.stop().slideDown();
          } else {
            $target.stop().slideUp();
          }
          $target.find('.form-control').prop('required', show);
        });
    });

    $parent.find('.js-select-toggle').change(function () {
      var $this = $(this);
      var $target = $($this.data('target'));
      var toggleOn = $this.data('toggle-on');
      if ($target.length === 0 || !toggleOn) {
        return;
      }

      if ($this.find('option:selected').val() === toggleOn) {
        $target.show();
      } else {
        $target.hide();
      }
    });

    $parent
      .find('.js-range-update-value .custom-range')
      .on('input', function () {
        var $this = $(this);
        var value = parseFloat($this.val()).toFixed(1);
        $this
          .closest('.js-range-update-value')
          .find('.js-range-value')
          .text(value);
      });

    $parent.find('.mark-context').each(function () {
      var $this = $(this);
      $this.find('mark').contents().unwrap();
      $this.mark($this.data('keywords'), { separateWordSearch: false });
    });

    $parent.find('.js-add-my-ip').click(function (e) {
      e.preventDefault();
      var $this = $(this);
      var $target = $($this.data('target'));
      if ($target.length === 0) {
        return;
      }

      var check = $target
        .find('option')
        .get()
        .some(function (elem) {
          return elem.value === app.ip;
        });
      if (!check) {
        var newOption = new Option(app.ip, app.ip, false, true);
        $target.append(newOption).trigger('change');
      }
    });

    // Copy to clipboard.
    $parent.find('.js-copy-to-clipboard').click(function (e) {
      e.preventDefault();
      var $this = $(this);
      var $temp = $('<input>');

      $('body').append($temp);
      $temp.val($this.data('content')).select();
      try {
        document.execCommand('copy');
      } catch (error) {}
      $temp.remove();

      $this
        .attr('title', app.trans.copiedToClipboard)
        .tooltip('dispose')
        .tooltip('show');

      setTimeout(function () {
        $this
          .attr('title', app.trans.copyToClipboard)
          .tooltip('dispose')
          .tooltip('enable');
      }, 1000);
    });
  };

  initPlugins($('body'));

  // Settings page.
  var $scoreSlider = $('.js-recaptcha-score');
  var $scoreWarning = $('.js-recaptcha-score-warning');
  $scoreWarning.find('.js-cancel').on('click', function () {
    $scoreSlider.val('0.9').trigger('input');
    $scoreWarning.modal('hide');
  });

  $scoreSlider.on('input', function () {
    var value = parseFloat($scoreSlider.val()).toFixed(1);
    if (value === '1.0' && !$scoreWarning.hasClass('show')) {
      $scoreWarning.modal('show');
    }
  });

  // Validations page.
  var $logsDelete = $('#logsDeleteCron form');
  var $logsDeleteLink = $logsDelete.find('input[name=link]');
  var $logsDeleteCopy = $logsDelete.find('.js-copy-to-clipboard');
  var $logsDeleteDays = $logsDelete.find('input[name=days]');

  $logsDelete.on('submit', function (e) {
    e.preventDefault();
  });
  $logsDeleteDays.on('change', function () {
    var link = app.deleteLogsLink + '&days=' + $logsDeleteDays.val();
    $logsDeleteLink.val(link);
    $logsDeleteCopy.data('content', link);
  });
  $logsDeleteDays.trigger('change');

  $('.js-logs-delete').click(function (e) {
    e.preventDefault();
    if (app.isDemo) {
      alert(app.trans.disabledForDemo);
      return;
    }

    var $this = $(this);
    if (!confirm(app.trans.deleteConf)) {
      return;
    }

    if ($this.next().is('form')) {
      $this.next().submit();
    }
  });

  $('.js-log-delete').click(function () {
    var $this = $(this);
    if (!confirm(app.trans.deleteConf)) {
      return;
    }
    var type = $(this).data('type');
    var id = $(this).data('id');
    $this.prop('disabled', true);
    $.ajax({
      url: app.url,
      method: 'POST',
      data: { _action: 'logs.delete', _type: type, id: id },
      complete: function () {
        $this.prop('disabled', false);
      },
      error: function () {
        alert(app.trans.serverError);
      },
      success: function () {
        $this.tooltip('hide');
        $this.on('hidden.bs.tooltip', function () {
          $this.off('hidden.bs.tooltip');
          $this.closest('tr').remove();
        });
      },
    });
  });
});
