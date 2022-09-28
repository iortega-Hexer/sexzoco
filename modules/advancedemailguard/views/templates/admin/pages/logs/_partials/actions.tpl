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
<div class="d-inline-block dropdown js-dropdown-stop">
    <a href="#" data-toggle="dropdown"
        class="btn btn-sm {if $logs.filtered}btn-primary{else}btn-light border{/if} dropdown-toggle"
        data-toggle2="tooltip" title="{$trans.filters|escape:'html':'UTF-8'}">
        <i class="material-icons-outlined">filter_list</i></a>
    <div class="dropdown-menu dropdown-lg dropdown-menu-right p-2">
        {include file='./filters.tpl' type=$logs.type forms=$logs.forms filters=$logs.filters}
    </div>
</div>
<a href="{$url|escape:'html':'UTF-8'}&tab=logs&type={$logs.type|escape:'html':'UTF-8'}" class="btn btn-sm btn-light border ml-1"
    data-toggle="tooltip" title="{$trans.reset|escape:'html':'UTF-8'}">
    <i class="material-icons-outlined">settings_backup_restore</i>
</a>
<a href="{$url|escape:'html':'UTF-8'}&tab=logs" class="btn btn-sm btn-light border ml-1"
    data-toggle="tooltip" title="{l s='Back to summary' mod='advancedemailguard'}">
    <i class="material-icons-outlined">west</i>
</a>