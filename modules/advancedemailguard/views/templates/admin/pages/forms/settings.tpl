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
    <h5 class="card-header border-bottom-0">{l s='Settings' mod='advancedemailguard'}</h5>
    <form action="{$url|escape:'html':'UTF-8'}&tab=forms" method="post">
        <input type="hidden" name="_action" value="forms.settings">
        <div class="table-responsive">
            <table class="table table-striped table-hover border-bottom m-0">
                <thead>
                    <tr>
                        <th>{l s='Form' mod='advancedemailguard'}</th>
                        <th class="text-center">{l s='reCAPTCHA validation' mod='advancedemailguard'}</th>
                        <th class="text-center">{l s='Email validation' mod='advancedemailguard'}</th>
                        <th class="text-center">{l s='Message validation' mod='advancedemailguard'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $forms as $name => $form}
                        <tr>
                            <td class="align-middle">
                                <i class="material-icons-outlined" style="color: {$form.display.color|escape:'html':'UTF-8'}">{$form.display.icon|escape:'html':'UTF-8'}</i>
                                <span class="ml-1">{$form.display.name|escape:'html':'UTF-8'}</span>
                                {if $form.display.custom}<span class="badge border border-primary text-primary ml-1">{l s='Custom' mod='advancedemailguard'}</span>{/if}
                                <small class="form-text text-muted">
                                    {$form.display.desc|escape:'html':'UTF-8'}
                                </small>
                            </td>
                            <td class="text-center align-middle">
                                <div class="dropdown js-dropdown-stop d-flex justify-content-center justify-content-center">
                                    <input type="checkbox" class="switch"
                                        name="ADVEG_{$name|upper|escape:'html':'UTF-8'}_OPTS[recaptchaValidation]"
                                        {if $form.settings.recaptchaValidation} checked{/if}>
                                    <button type="button" data-toggle="dropdown"
                                        class="btn btn-sm btn-light border text-secondary ml-2 dropdown-toggle dropdown-no-caret" data-toggle2="tooltip"
                                        title="{l s='reCAPTCHA widget settings' mod='advancedemailguard'}"
                                        {if $config.ADVEG_REC_TYPE === 'v3' || ($config.ADVEG_REC_TYPE === 'v2_inv' && $config.ADVEG_REC_POSITION !== 'inline')} style="display: none;"{/if}>
                                        <i class="material-icons-outlined md-18 px-1">settings</i></button>
                                    <div class="dropdown-menu dropdown-lg dropdown-menu-right p-2">
                                        <div class="form-group mb-1"{if $config.ADVEG_REC_TYPE !== 'v2_cbx'} style="display: none;"{/if}>
                                            <label for="ADVEG_{$name|upper|escape:'html':'UTF-8'}_RECAPTCHA_SIZE" class="small">
                                                {l s='Checkbox size' mod='advancedemailguard'}</label>
                                            <select name="ADVEG_{$name|upper|escape:'html':'UTF-8'}_OPTS[recaptchaSize]"
                                                id="ADVEG_{$name|upper|escape:'html':'UTF-8'}_RECAPTCHA_SIZE" class="custom-select custom-select-sm">
                                                <option value="normal"{if $form.settings.recaptchaSize === 'normal'} selected{/if}>{l s='Normal' mod='advancedemailguard'}</option>
                                                <option value="compact"{if $form.settings.recaptchaSize === 'compact'} selected{/if}>{l s='Compact' mod='advancedemailguard'}</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label for="ADVEG_{$name|upper|escape:'html':'UTF-8'}_RECAPTCHA_ALIGN" class="small">
                                                {if $config.ADVEG_REC_TYPE === 'v2_cbx'}
                                                    {l s='Checkbox alignment' mod='advancedemailguard'}
                                                {else}
                                                    {l s='Inline badge alignment' mod='advancedemailguard'}
                                                {/if}
                                            </label>
                                            <select name="ADVEG_{$name|upper|escape:'html':'UTF-8'}_OPTS[recaptchaAlign]"
                                                id="ADVEG_{$name|upper|escape:'html':'UTF-8'}_RECAPTCHA_ALIGN" class="custom-select custom-select-sm js-select-toggle"
                                                data-target="#ADVEG_{$name|upper|escape:'html':'UTF-8'}_RECAPTCHA_OFFSET" data-toggle-on="offset">
                                                <option value="left"{if $form.settings.recaptchaAlign === 'left'} selected{/if}>{l s='Left' mod='advancedemailguard'}</option>
                                                <option value="center"{if $form.settings.recaptchaAlign === 'center'} selected{/if}>{l s='Center' mod='advancedemailguard'}</option>
                                                <option value="right"{if $form.settings.recaptchaAlign === 'right'} selected{/if}>{l s='Right' mod='advancedemailguard'}</option>
                                                <option value="offset"{if $form.settings.recaptchaAlign === 'offset'} selected{/if}>{l s='Offset' mod='advancedemailguard'}</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-0 mt-1" id="ADVEG_{$name|upper|escape:'html':'UTF-8'}_RECAPTCHA_OFFSET"
                                            {if $form.settings.recaptchaAlign !== 'offset'} style="display: none;"{/if}>
                                            <label for="ADVEG_{$name|upper|escape:'html':'UTF-8'}_RECAPTCHA_OFFSET" class="small">
                                                {l s='Offset by' mod='advancedemailguard'}
                                            </label>
                                            <select name="ADVEG_{$name|upper|escape:'html':'UTF-8'}_OPTS[recaptchaOffset]"
                                                id="ADVEG_{$name|upper|escape:'html':'UTF-8'}_RECAPTCHA_OFFSET" class="custom-select custom-select-sm">
                                                <option value="1"{if $form.settings.recaptchaOffset === 1} selected{/if}>{l s='1 column' mod='advancedemailguard'}</option>
                                                <option value="2"{if $form.settings.recaptchaOffset === 2} selected{/if}>{l s='2 column' mod='advancedemailguard'}</option>
                                                <option value="3"{if $form.settings.recaptchaOffset === 3} selected{/if}>{l s='3 columns' mod='advancedemailguard'}</option>
                                                <option value="4"{if $form.settings.recaptchaOffset === 4} selected{/if}>{l s='4 columns' mod='advancedemailguard'}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                {if $form.isEmailForm}
                                    <input type="checkbox" class="switch"
                                        name="ADVEG_{$name|upper|escape:'html':'UTF-8'}_OPTS[emailValidation]"
                                        {if $form.settings.emailValidation} checked{/if}>
                                {elseif $name === 'login' || $name === 'reset_password'}
                                    <div data-toggle="tooltip" title="{l s='Not required since the email address is already verified in your shop\'s database.' mod='advancedemailguard'}">
                                        <input type="checkbox" class="switch" disabled>
                                    </div>
                                {else}
                                    <div data-toggle="tooltip" title="{l s='Not available due to the absence of an email field.' mod='advancedemailguard'}">
                                        <input type="checkbox" class="switch" disabled>
                                    </div>
                                {/if}
                            </td>
                            <td class="text-center align-middle">
                                {if $form.isMessageForm}
                                    <input type="checkbox" class="switch"
                                        name="ADVEG_{$name|upper|escape:'html':'UTF-8'}_OPTS[messageValidation]"
                                        {if $form.settings.messageValidation} checked{/if}>
                                {else}
                                    <div data-toggle="tooltip" title="{l s='Not available due to the absence of a message/comment field.' mod='advancedemailguard'}">
                                        <input type="checkbox" class="switch" disabled>
                                    </div>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>

        <div class="card-body pt-3">
            {include file='../../_partials/save.tpl' disabledForDemo=$isDemo}
        </div>
    </form>
</div>