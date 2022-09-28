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
    <h5 class="card-header">{l s='Email validation' mod='advancedemailguard'}</h5>
    <form action="{$url|escape:'html':'UTF-8'}" method="post">
        <input type="hidden" name="_action" value="settings.email">
        <div class="card-body">
            <div class="form-group">
                <input type="checkbox" name="ADVEG_CHECK_EMAIL_DISPOSABLE" id="ADVEG_CHECK_EMAIL_DISPOSABLE" class="switch"
                    {if $config.ADVEG_CHECK_EMAIL_DISPOSABLE} checked{/if}{if $isDemo} disabled{/if}>
                <label for="ADVEG_CHECK_EMAIL_DISPOSABLE" class="ml-1">
                    {l s='Check for disposable emails' mod='advancedemailguard'}
                </label>
                {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                <small class="form-text text-muted">
                    {l s='Check the given email address against a list of over 2000 disposable email providers such as mail.ru, qq.com, spam4.me and many more.' mod='advancedemailguard'}<br>
                    <a href="{$basePath|escape:'html':'UTF-8'}views/img/domains.jpg" target="_blank">
                        <i class="material-icons-outlined md-18 mr-1">open_in_new</i>{l s='See the full list of disposable email providers' mod='advancedemailguard'}
                    </a>
                </small>
            </div>

            <div class="form-group">
                <label for="ADVEG_FORBIDDEN_EMAILS">
                    {l s='Forbidden email addresses' mod='advancedemailguard'}
                </label>
                <select name="ADVEG_FORBIDDEN_EMAILS[]" id="ADVEG_FORBIDDEN_EMAILS" class="form-control select2-tags"
                    data-placeholder="{l s='Example' mod='advancedemailguard'}: spam@yahoo.com, fake@hotmail.com ..." multiple>
                    {foreach $config.ADVEG_FORBIDDEN_EMAILS as $email}
                        <option value="{$email|escape:'html':'UTF-8'}" selected>{$email|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
                <small class="form-text text-muted">
                    {l s='Deny the usage of any of the specified emails addresses.' mod='advancedemailguard'}
                    {$trans.sepByComma|escape:'html':'UTF-8'}
                </small>
            </div>

            <div class="form-group">
                <label for="ADVEG_FORBIDDEN_EMAIL_DOMAINS">
                    {l s='Forbidden email domains' mod='advancedemailguard'}
                </label>
                <select name="ADVEG_FORBIDDEN_EMAIL_DOMAINS[]" id="ADVEG_FORBIDDEN_EMAIL_DOMAINS" class="form-control select2-tags"
                    data-placeholder="{l s='Example' mod='advancedemailguard'}: spam-source.com, bad-website.com ..." multiple>
                    {foreach $config.ADVEG_FORBIDDEN_EMAIL_DOMAINS as $domain}
                        <option value="{$domain|escape:'html':'UTF-8'}" selected>{$domain|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
                <small class="form-text text-muted">
                    {l s='Deny the usage of any email address associated with the specified domains.' mod='advancedemailguard'}
                    {$trans.sepByComma|escape:'html':'UTF-8'}
                </small>
            </div>

            <div class="form-group">
                <label for="ADVEG_FORBIDDEN_EMAIL_PATTERNS">
                    {l s='Forbidden email patterns' mod='advancedemailguard'}
                </label>
                <select name="ADVEG_FORBIDDEN_EMAIL_PATTERNS[]" id="ADVEG_FORBIDDEN_EMAIL_PATTERNS" class="form-control select2-tags"
                    data-placeholder="{l s='Example' mod='advancedemailguard'}: *.xyz, spam-*.com, *fake* ..." multiple>
                    {foreach $config.ADVEG_FORBIDDEN_EMAIL_PATTERNS as $pattern}
                        <option value="{$pattern|escape:'html':'UTF-8'}" selected>{$pattern|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
                <small class="form-text text-muted">
                    {l s='Deny the usage of any email address that matches the specified patterns.' mod='advancedemailguard'}
                    {$trans.sepByComma|escape:'html':'UTF-8'}<br>
                    {l s='Use asterisks (*) to indicate wildcards in your patterns.' mod='advancedemailguard'}
                    {l s='For example *.xyz will match with any email address that ends with .xyz.' mod='advancedemailguard'}
                </small>
            </div>

            {include file='../../_partials/save.tpl'}
        </div>
    </form>
</div>