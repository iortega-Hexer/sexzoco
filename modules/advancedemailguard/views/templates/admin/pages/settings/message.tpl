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
    <h5 class="card-header">{l s='Message validation' mod='advancedemailguard'}</h5>
    <form action="{$url|escape:'html':'UTF-8'}" method="post">
        <input type="hidden" name="_action" value="settings.message">
        <div class="card-body">
            <div class="form-group">
                <label for="ADVEG_FORBIDDEN_TEXTS">
                    {l s='Forbidden texts' mod='advancedemailguard'}
                </label>
                <select name="ADVEG_FORBIDDEN_TEXTS[]" id="ADVEG_FORBIDDEN_TEXTS" class="form-control select2-texts"
                    data-placeholder="{l s='Example' mod='advancedemailguard'}: {l s='sign up free today' mod='advancedemailguard'}, {l s='claim your reward now' mod='advancedemailguard'} ..." multiple>
                    {if $config.ADVEG_FORBIDDEN_TEXTS}
                        {foreach $config.ADVEG_FORBIDDEN_TEXTS as $text}
                            <option value="{$text|escape:'html':'UTF-8'}" selected>{$text|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    {/if}
                </select>
                <small class="form-text text-muted">
                    {l s='Deny the usage of messages (or comments) that contain any of the specified texts.' mod='advancedemailguard'}<br>
                    {$trans.sepByEnter|escape:'html':'UTF-8'}
                    {l s='At least 3 characters per value is required.' mod='advancedemailguard'}
                </small>
            </div>

            {include file='../../_partials/save.tpl'}
        </div>
    </form>
</div>