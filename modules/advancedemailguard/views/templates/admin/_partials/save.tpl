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
<div class="pt-2">
    <div class="text-right">
        {if isset($disabledForDemo) && $disabledForDemo}
            {include file='./demo.badge.tpl'}
        {/if}
        <button type="submit" class="btn btn-outline-secondary px-4"{if isset($disabledForDemo) && $disabledForDemo} disabled{/if}>
            <i class="material-icons-outlined md-18 mr-1">done_outline</i>
            {$trans.save|escape:'html':'UTF-8'}
        </button>
    </div>
</div>
