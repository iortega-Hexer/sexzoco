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
<div class="card mb-3">
    <div class="card-header d-flex align-items-center border-bottom-0">
        <h5 class="m-0">{l s='Message' mod='advancedemailguard'}</h5>
        <div class="ml-auto my-n2">
            {include file='./_partials/actions.tpl'}
        </div>
    </div>
    {if ! empty($logs.logs)}
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0">
                <thead>
                    <tr>
                        {include file='./_partials/thead.start.tpl'}
                        <th class="text-center">{l s='Message' mod='advancedemailguard'}</th>
                        {include file='./_partials/thead.end.tpl'}
                    </tr>
                </thead>
                <tbody>
                    {foreach $logs.logs as $log}
                        <tr>
                            {include file='./_partials/trow.start.tpl' log=$log}
                            <td>
                                <div class="text-center">
                                    <button type="button" class="btn btn-sm btn-light border text-secondary"
                                        data-toggle="modal" data-target="#viewLogMessage{$log.id_log|escape:'html':'UTF-8'}">
                                        <i class="material-icons-outlined md-18">email</i>
                                    </button>
                                </div>
                                <div class="modal fade" id="viewLogMessage{$log.id_log|escape:'html':'UTF-8'}">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    {l s='Logged message' mod='advancedemailguard'}
                                                    <span class="text-muted">#{$log.id_log|escape:'html':'UTF-8'}</span>
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body text-monospace" style="font-size: 87.5%;">{$log.message}{* This is HTML content *}</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            {include file='./_partials/trow.end.tpl' log=$log}
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    {else}
        <div class="card-body">
            <p class="m-0 text-muted"><i>{$trans.noRecords|escape:'html':'UTF-8'}</i></p>
        </div>
    {/if}
    <div class="card-body border-top">
        {include file='./_partials/pagination.tpl'}
    </div>
</div>