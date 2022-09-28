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
{foreach $logs as $type => $data}
    <div class="card mb-3">
        <div class="card-header border-bottom-0 d-flex align-items-center">
            <h5 class="m-0 d-flex">
                <a href="{$url|escape:'html':'UTF-8'}&tab=logs&type={$type|escape:'html':'UTF-8'}" class="text-dark">
                {if $type === 'email'}
                    {l s='Email' mod='advancedemailguard'}
                {elseif $type === 'message'}
                    {l s='Message' mod='advancedemailguard'}
                {else}
                    {l s='reCAPTCHA' mod='advancedemailguard'}
                {/if}
                </a>
                <span class="badge badge-light border ml-2"
                    data-toggle="tooltip" title="{l s='Total validations' mod='advancedemailguard'}">{$data.count|escape:'html':'UTF-8'}</span>
            </h5>
            <div class="ml-auto my-n2">
                <a href="#" class="btn btn-light btn-sm border js-logs-delete"
                    data-toggle="tooltip" title="{$trans.deleteAll|escape:'html':'UTF-8'}">
                    <i class="material-icons-outlined">delete</i>
                </a>
                <form action="{$url|escape:'html':'UTF-8'}&tab=logs" method="post" class="d-none">
                    <input type="hidden" name="_action" value="logs.delete">
                    <input type="hidden" name="_type" value="{$type|escape:'html':'UTF-8'}">
                    <button type="submit"></button>
                </form>
                <a href="{$url|escape:'html':'UTF-8'}&tab=logs&type={$type|escape:'html':'UTF-8'}" class="btn btn-light btn-sm border ml-1"
                    data-toggle="tooltip" title="{l s='See all validations' mod='advancedemailguard'}">
                     <i class="material-icons-outlined">reorder</i>
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0">
                <thead>
                    <tr>
                        <th>{l s='Form' mod='advancedemailguard'}</th>
                        <th>{l s='Latest' mod='advancedemailguard'}</th>
                        <th colspan="3" class="text-right">{l s='Summary' mod='advancedemailguard'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $data.forms as $formType => $form}
                        <tr>
                            <td>
                                <a href="{$url|escape:'html':'UTF-8'}&tab=logs&type={$type|escape:'html':'UTF-8'}&form={$formType|escape:'html':'UTF-8'}" class="text-secondary">
                                    <i class="material-icons-outlined" style="color: {$form.display.color|escape:'html':'UTF-8'}">{$form.display.icon|escape:'html':'UTF-8'}</i><span class="ml-1 text-dark">{$form.display.name|escape:'html':'UTF-8'}</span>
                                </a>
                            </td>
                            <td>
                                {if $form.latest}
                                    {date($lang.date_format_full, strtotime($form.latest))|escape:'html':'UTF-8'}
                                {else}
                                    <span class="text-muted">&mdash;</span>
                                {/if}
                            </td>
                            <td style="width: 1px;">
                                    <a href="{$url|escape:'html':'UTF-8'}&tab=logs&type={$type|escape:'html':'UTF-8'}&form={$formType|escape:'html':'UTF-8'}&success=1"
                                        class="text-nowrap btn btn-block btn-sm btn-light border{if $form.passed === 0} disabled{/if}"
                                        data-toggle="tooltip" title="{l s='See all passed validations' mod='advancedemailguard'}">
                                        <i class="material-icons-outlined md-18" style="color: #0fb9b1;">verified_user</i>
                                        <span class="ml-1 font-weight-bold">{$form.passed|escape:'html':'UTF-8'}</span>
                                    </a>
                            </td>
                            <td style="width: 1px;">
                                    <a href="{$url|escape:'html':'UTF-8'}&tab=logs&type={$type|escape:'html':'UTF-8'}&form={$formType|escape:'html':'UTF-8'}&success=0"
                                        class="text-nowrap btn btn-block btn-sm btn-light border{if $form.failed === 0} disabled{/if}"
                                        data-toggle="tooltip" title="{l s='See all failed validations' mod='advancedemailguard'}">
                                        <i class="material-icons-outlined md-18" style="color: #ff6b81;">block</i>
                                        <span class="ml-1 font-weight-bold">{$form.failed|escape:'html':'UTF-8'}</span>
                                    </a>
                            </td>
                            <td style="width: 1px;">
                                <a href="{$url|escape:'html':'UTF-8'}&tab=logs&type={$type|escape:'html':'UTF-8'}&form={$formType|escape:'html':'UTF-8'}"
                                    class="text-nowrap btn btn-block btn-sm btn-light border{if $form.total === 0} disabled{/if}"
                                    data-toggle="tooltip" title="{l s='See all validations' mod='advancedemailguard'}">
                                    <i class="material-icons-outlined md-18 text-muted">info</i>
                                    <span class="ml-1 font-weight-bold">{$form.total|escape:'html':'UTF-8'}</span>
                                </a>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/foreach}