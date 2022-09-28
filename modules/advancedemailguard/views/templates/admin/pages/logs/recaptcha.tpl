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
        <h5 class="m-0">{l s='reCAPTCHA' mod='advancedemailguard'}</h5>
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
                        <th class="text-center text-nowrap">
                            {l s='Response' mod='advancedemailguard'}
                            <i class="material-icons-outlined md-16 text-muted" data-toggle="tooltip" title="{l s='The reCAPTCHA API response.' mod='advancedemailguard'}">help_outline</i>
                        </th>
                        <th class="text-center text-nowrap">
                            {l s='Score' mod='advancedemailguard'}
                            <i class="material-icons-outlined md-16 text-muted" data-toggle="tooltip" title="{l s='The reCAPTCHA v3 score received.' mod='advancedemailguard'}">help_outline</i>
                        </th>
                        {include file='./_partials/thead.end.tpl'}
                    </tr>
                </thead>
                <tbody>
                    {foreach $logs.logs as $log}
                        <tr>
                            {include file='./_partials/trow.start.tpl' log=$log}
                            <td class="text-center">
                                {if $log.response}
                                    <button type="button" class="btn btn-sm btn-light border text-secondary"
                                        data-toggle="popover" data-container="body" data-placement="top" data-html="true"
                                        data-content="<pre class='text-monospace m-0' style='font-size: 100%;'>{$log.response|escape:'html':'UTF-8'}</pre>"
                                        data-template="<div class='popover' role='tooltip' style='max-width: 400px;'><div class='arrow'></div><h3 class='popover-header'></h3><div class='popover-body'></div></div>">
                                        <i class="material-icons-outlined md-18">code</i>
                                    </button>
                                {else}
                                    <span class="text-muted">&mdash;</span>
                                {/if}
                            </td>
                            <td class="text-center">
                                {if $log.score !== null}
                                    <span class="text-monospace">{number_format($log.score, 1)|escape:'html':'UTF-8'}</span>
                                {else}
                                    <span class="text-muted">&mdash;</span>
                                {/if}
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