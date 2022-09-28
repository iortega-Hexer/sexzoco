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
    <div class="card-header d-flex align-items-center">
        <h5 class="m-0">
            {l s='Logged validations' mod='advancedemailguard'}
        </h5>
        <div class="ml-auto my-n2">
            <a href="{$url|escape:'html':'UTF-8'}&tab=logs" class="btn btn-sm btn-light border"
                title="{$trans.refresh|escape:'html':'UTF-8'}" data-toggle="tooltip"><i class="material-icons-outlined">refresh</i></a>
        </div>
    </div>
    <div class="card-body">
        <div class="row no-gutters">
            <div class="col-md-auto">
                <form action="{$url|escape:'html':'UTF-8'}&tab=logs" method="post" class="form-inline">
                    <input type="hidden" name="_action" value="logs.settings">
                    <select name="ADVEG_LOGS_MODE" id="ADVEG_LOGS_MODE" class="custom-select">
                        <option value="all"{if $config.ADVEG_LOGS_MODE === 'all'} selected{/if}>{l s='Log all validations' mod='advancedemailguard'}</option>
                        <option value="failed"{if $config.ADVEG_LOGS_MODE === 'failed'} selected{/if}>{l s='Log only failed validations' mod='advancedemailguard'}</option>
                        <option value="none"{if $config.ADVEG_LOGS_MODE === 'none'} selected{/if}>{l s='Disable logs' mod='advancedemailguard'}</option>
                    </select>
                    <button type="submit" class="btn btn-outline-secondary ml-sm-2 mt-2 mt-sm-0 px-4"{if $isDemo} disabled{/if}>
                        <i class="material-icons-outlined md-18 mr-1">done_outline</i>
                        {$trans.save|escape:'html':'UTF-8'}</button>
                    {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                </form>
            </div>
            <div class="col-md-auto ml-auto mt-2 mt-md-0">
                <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#logsDeleteCron">
                    <i class="material-icons-outlined md-22 mr-1">schedule</i>
                    {l s='Delete logs task' mod='advancedemailguard'} (CRON)</button>
            </div>
        </div>
    </div>
</div>

{include file='./cron.tpl'}