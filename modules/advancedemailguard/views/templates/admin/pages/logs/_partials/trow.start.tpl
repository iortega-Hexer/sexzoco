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
<td>#{$log.id_log|escape:'html':'UTF-8'}</td>
<td>
    <i class="material-icons-outlined" style="color: {$log.display.color|escape:'html':'UTF-8'}">{$log.display.icon|escape:'html':'UTF-8'}</i>
    <span class="ml-1">{$log.display.name|escape:'html':'UTF-8'}</span>
</td>
<td class="text-center text-nowrap">
    {if $log.success}
        <i class="material-icons-outlined" style="color: #0fb9b1; cursor: default;"
            data-toggle="tooltip" title="{l s='Passed validation' mod='advancedemailguard'}">verified_user</i>
    {else}
        <i class="material-icons-outlined" style="color: #ff6b81; cursor: default;"
            data-toggle="tooltip" title="{l s='Failed validation' mod='advancedemailguard'}">block</i>
    {/if}
</td>