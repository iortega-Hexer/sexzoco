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
<td>
    {if $log.browser !== 'unknown'}
        {$log.browser|escape:'html':'UTF-8'}
    {else}
        <span class="text-muted">&mdash;</span>
    {/if}
</td>
<td>
    {if $log.platform !== 'unknown'}
        {$log.platform|escape:'html':'UTF-8'}
    {else}
        <span class="text-muted">&mdash;</span>
    {/if}
</td>
<td class="text-center">
    {if $log.user_agent}
        <button type="button" class="btn btn-sm btn-light border text-secondary"
            data-toggle="popover" data-container="body" data-placement="top" data-html="true"
            data-content="<span class='text-monospace'>{$log.user_agent|escape:'html':'UTF-8'}</span>">
            <i class="material-icons-outlined md-18">person</i>
        </button>
    {else}
        <span class="text-muted">&mdash;</span>
    {/if}
</td>
<td>
    <div class="text-nowrap">
        {if $log.ip_address}
            <span>{$log.ip_address|escape:'html':'UTF-8'}</span>
            <a href="https://ipinfo.io/{$log.ip_address|escape:'html':'UTF-8'}" target="_blank"
                title="{l s='Get IP details from' mod='advancedemailguard'} ipinfo.io" data-toggle="tooltip">
                <i class="material-icons-outlined md-18">open_in_new</i></a>
        {else}
            <span class="text-muted">&mdash;</span>
        {/if}
    </div>
</td>
<td>
    <span>{date($lang.date_format_full, strtotime($log.date_add))|escape:'html':'UTF-8'}</span>
</td>
<td>
    <button type="button" class="btn btn-sm btn-light border js-log-delete"
        data-type="{$logs.type|escape:'html':'UTF-8'}" data-id="{$log.id_log|escape:'html':'UTF-8'}"
        data-toggle="tooltip" title="{$trans.delete|escape:'html':'UTF-8'}">
        <i class="material-icons-outlined md-18">delete</i>
    </button>
</td>