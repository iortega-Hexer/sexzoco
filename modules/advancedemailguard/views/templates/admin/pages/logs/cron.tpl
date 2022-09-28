{**
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
 *}
<div class="modal fade" id="logsDeleteCron">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">
                {l s='Delete logged validations' mod='advancedemailguard'}
                {if $isDemo}<small>{include file='./../../_partials/demo.badge.tpl'}</small>{/if}
            </h5>
            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form action="#">
                <p>{l s='Use the following link in your scheduled tasks (CRON tasks) to periodically delete the logged validations.' mod='advancedemailguard'}</p>
                <div class="form-group form-inline">
                    <span class="mr-2 small">{l s='Delete logs older than' mod='advancedemailguard'}</span>
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control text-monospace" name="days" min="0" value="7" max="100">
                        <div class="input-group-append">
                            <span class="input-group-text">{$trans.days|escape:'html':'UTF-8'}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm text-monospace" name="link" readonly>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-sm btn-light border js-copy-to-clipboard"
                                data-content="" data-toggle="tooltip"
                                title="{$trans.copyToClipboard|escape:'html':'UTF-8'}">
                                <i class="material-icons-outlined md-16">filter_none</i></button>
                        </div>
                    </div>
                </div>
                <p>{l s='You can use the Linux CURL command to programmatically visit the link.' mod='advancedemailguard'}</p>
            </form>
            </div>
        </div>
    </div>
</div>