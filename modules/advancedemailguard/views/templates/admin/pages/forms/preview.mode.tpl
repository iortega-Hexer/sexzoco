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
    <h5 class="card-header">{l s='Preview mode' mod='advancedemailguard'}</h5>
    <form action="{$url|escape:'html':'UTF-8'}&tab=forms" method="post">
        <input type="hidden" name="_action" value="forms.previewMode">
         <div class="card-body">
            <div class="form-group">
                <input type="checkbox" name="ADVEG_PREVIEW_MODE" id="ADVEG_PREVIEW_MODE" class="switch"
                    data-target="#previewModeIPAddresses"{if $config.ADVEG_PREVIEW_MODE} checked{/if}>
                <label for="ADVEG_PREVIEW_MODE" class="ml-1">
                    {l s='Enable preview mode' mod='advancedemailguard'}
                </label>
                <small class="form-text text-muted">
                    {l s='Enable the module only for users with specific IP addresses.' mod='advancedemailguard'}
                    {l s='This allows you to safely test the validations before enabling them for all your users.' mod='advancedemailguard'}
                </small>
            </div>

            <div id="previewModeIPAddresses"{if !$config.ADVEG_PREVIEW_MODE} style="display: none;"{/if}>
                <div class="form-group">
                    <label for="ADVEG_PREVIEW_MODE_IPS">
                        {l s='Preview mode IP addresses' mod='advancedemailguard'}
                    </label>
                    <div class="small mb-1">
                        <a href="#" class="js-add-my-ip" data-target="#ADVEG_PREVIEW_MODE_IPS">
                            <i class="material-icons-outlined md-18">add_circle_outline</i>
                            {l s='Add my IP' mod='advancedemailguard'}</a>
                    </div>
                    <select name="ADVEG_PREVIEW_MODE_IPS[]" id="ADVEG_PREVIEW_MODE_IPS" class="form-control select2-tags" multiple>
                        {foreach $config.ADVEG_PREVIEW_MODE_IPS as $ip}
                            <option value="{$ip|escape:'html':'UTF-8'}" selected>{$ip|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    <small class="form-text text-muted">
                        {$trans.sepByComma|escape:'html':'UTF-8'}<br>
                    </small>
                </div>
            </div>

            {include file='../../_partials/save.tpl' disabledForDemo=$isDemo}
        </div>
    </form>
</div>