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
    <h5 class="card-header">
        {l s='Google reCAPTCHA' mod='advancedemailguard'}
    </h5>
    <form action="{$url|escape:'html':'UTF-8'}" method="post">
        <input type="hidden" name="_action" value="settings.recaptcha">
        <div class="card-body">
            <div class="form-group">
                <label for="ADVEG_REC_TYPE">
                    {l s='reCAPTCHA type' mod='advancedemailguard'}
                </label>
                <div class="row gutters-sm js-radio-toggle">
                    <div class="col-md custom-control-card custom-radio d-flex align-items-stretch">
                        <input type="radio" id="ADVEG_REC_TYPE_V2_CBX" name="ADVEG_REC_TYPE" value="v2_cbx"
                            class="custom-control-input" data-target="#recV2CbxKeys"
                            {if $config.ADVEG_REC_TYPE === 'v2_cbx'} checked{/if}>
                        <label class="custom-control-label w-100" for="ADVEG_REC_TYPE_V2_CBX">
                            v2 &mdash; {l s='Checkbox' mod='advancedemailguard'}
                            <i class="material-icons-outlined md-16 text-muted" data-toggle="tooltip"
                                data-template="<div class='tooltip' role='tooltip'><div class='arrow'></div><div class='tooltip-inner' style='max-width: 300px;'></div></div>"
                                title="{l s='After clicking the checkbox the widget will prompt suspicious users to identify certain objects from a random set of images.' mod='advancedemailguard'}">help_outline</i>
                            <div class="mt-1">
                                <img src="{$basePath|escape:'html':'UTF-8'}views/img/recaptcha.checkbox.gif" style="max-height: 70px;"
                                    alt="{l s='Checkbox' mod='advancedemailguard'}" class="img-fluid">
                            </div>
                            <small class="form-text text-muted">{l s='Displays a checkbox on the enabled forms.' mod='advancedemailguard'}</small>
                        </label>
                    </div>

                    <div class="col-md custom-control-card custom-radio d-flex align-items-stretch">
                        <input type="radio" id="ADVEG_REC_TYPE_V2_INV" name="ADVEG_REC_TYPE" value="v2_inv"
                            class="custom-control-input" data-target="#recV2InvKeys"
                            {if $config.ADVEG_REC_TYPE === 'v2_inv'} checked{/if}>
                        <label class="custom-control-label w-100" for="ADVEG_REC_TYPE_V2_INV">
                            v2 &mdash; {l s='Invisible' mod='advancedemailguard'}
                            <i class="material-icons-outlined md-16 text-muted" data-toggle="tooltip"
                                data-template="<div class='tooltip' role='tooltip'><div class='arrow'></div><div class='tooltip-inner' style='max-width: 300px;'></div></div>"
                                title="{l s='After submitting the form the widget will prompt suspicious users to identify certain objects from a random set of images.' mod='advancedemailguard'}">help_outline</i>
                            <div class="mt-1">
                                <img src="{$basePath|escape:'html':'UTF-8'}views/img/recaptcha.badge.png"
                                    alt="{l s='Invisible' mod='advancedemailguard'}" class="img-fluid">
                            </div>
                            <small class="form-text text-muted">{l s='Displays a fixed or inline badge on the enabled forms.' mod='advancedemailguard'}</small>
                        </label>
                    </div>

                    <div class="col-md custom-control-card custom-radio d-flex align-items-stretch">
                        <input type="radio" id="ADVEG_REC_TYPE_V3" name="ADVEG_REC_TYPE" value="v3"
                            class="custom-control-input"
                            data-target="#recV3Keys" {if $config.ADVEG_REC_TYPE === 'v3'} checked{/if}>
                        <label class="custom-control-label w-100" for="ADVEG_REC_TYPE_V3">
                            v3
                            <i class="material-icons-outlined md-16 text-muted" data-toggle="tooltip"
                                data-template="<div class='tooltip' role='tooltip'><div class='arrow'></div><div class='tooltip-inner' style='max-width: 300px;'></div></div>"
                                title="{l s='After submitting the form the widget will generate a score based on the user\'s interaction with your shop. Scores below the threshold setting will be considered invalid. No additional user interaction is required.' mod='advancedemailguard'}">help_outline</i>
                            <div class="mt-1">
                                <img src="{$basePath|escape:'html':'UTF-8'}views/img/recaptcha.badge.png"
                                    alt="{l s='reCAPTCHA v3' mod='advancedemailguard'}" class="img-fluid">
                            </div>
                            <small class="form-text text-muted">{l s='Displays a fixed or inline badge on the footer of the page.' mod='advancedemailguard'}</small>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="recV2CbxKeys"{if $config.ADVEG_REC_TYPE !== 'v2_cbx'} style="display: none;"{/if}>
                    <div class="row gutters-sm">
                        <div class="col-sm-6">
                            <label for="ADVEG_REC_V2_CBX_KEY">
                                {l s='Site key' mod='advancedemailguard'}
                            </label>
                            {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                            <input type="text" class="form-control text-monospace" name="ADVEG_REC_V2_CBX_KEY" id="ADVEG_REC_V2_CBX_KEY"
                                value="{$config.ADVEG_REC_V2_CBX_KEY|escape:'html':'UTF-8'}"
                                {if $config.ADVEG_REC_TYPE === 'v2_cbx'} required{/if}{if $isDemo} disabled{/if}>
                        </div>

                        <div class="col-sm-6">
                            <label for="ADVEG_REC_V2_CBX_SECRET">
                                {l s='Secret key' mod='advancedemailguard'}
                            </label>
                            {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                            <input type="text" class="form-control text-monospace" name="ADVEG_REC_V2_CBX_SECRET" id="ADVEG_REC_V2_CBX_SECRET"
                                value="{$config.ADVEG_REC_V2_CBX_SECRET|escape:'html':'UTF-8'}"
                                {if $config.ADVEG_REC_TYPE === 'v2_cbx'} required{/if}{if $isDemo} disabled{/if}>
                        </div>
                    </div>
                </div>

                <div id="recV2InvKeys"{if $config.ADVEG_REC_TYPE !== 'v2_inv'} style="display: none;"{/if}>
                    <div class="row gutters-sm">
                        <div class="col-sm-6">
                            <label for="ADVEG_REC_V2_INV_KEY">
                                {l s='Site key' mod='advancedemailguard'}
                            </label>
                            {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                            <input type="text" class="form-control text-monospace" name="ADVEG_REC_V2_INV_KEY" id="ADVEG_REC_V2_INV_KEY"
                                value="{$config.ADVEG_REC_V2_INV_KEY|escape:'html':'UTF-8'}"
                                {if $config.ADVEG_REC_TYPE === 'v2_inv'} required{/if}{if $isDemo} disabled{/if}>
                        </div>

                        <div class="col-sm-6">
                            <label for="ADVEG_REC_V2_INV_SECRET">
                                {l s='Secret key' mod='advancedemailguard'}
                            </label>
                            {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                            <input type="text" class="form-control text-monospace" name="ADVEG_REC_V2_INV_SECRET" id="ADVEG_REC_V2_INV_SECRET"
                                value="{$config.ADVEG_REC_V2_INV_SECRET|escape:'html':'UTF-8'}"
                                {if $config.ADVEG_REC_TYPE === 'v2_inv'} required{/if}{if $isDemo} disabled{/if}>
                        </div>
                    </div>
                </div>

                <div id="recV3Keys"{if $config.ADVEG_REC_TYPE !== 'v3'} style="display: none;"{/if}>
                    <div class="row gutters-sm">
                        <div class="col-sm-6">
                            <label for="ADVEG_REC_V3_KEY">
                                {l s='Site key' mod='advancedemailguard'}
                            </label>
                            {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                            <input type="text" class="form-control text-monospace" name="ADVEG_REC_V3_KEY" id="ADVEG_REC_V3_KEY"
                                value="{$config.ADVEG_REC_V3_KEY|escape:'html':'UTF-8'}"
                                {if $config.ADVEG_REC_TYPE === 'v3'} required{/if}{if $isDemo} disabled{/if}>
                        </div>

                        <div class="col-sm-6">
                            <label for="ADVEG_REC_V3_SECRET">
                                {l s='Secret key' mod='advancedemailguard'}
                            </label>
                            {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                            <input type="text" class="form-control text-monospace" name="ADVEG_REC_V3_SECRET" id="ADVEG_REC_V3_SECRET"
                                value="{$config.ADVEG_REC_V3_SECRET|escape:'html':'UTF-8'}"
                                {if $config.ADVEG_REC_TYPE === 'v3'} required{/if}{if $isDemo} disabled{/if}>
                        </div>
                    </div>
                </div>

                <small class="form-text text-muted">
                    <a href="https://www.google.com/recaptcha/admin" target="_blank">
                        <i class="material-icons-outlined md-18 mr-1">open_in_new</i>{l s='Get the reCAPTCHA site and secret keys' mod='advancedemailguard'}
                    </a>
                </small>
            </div>

            <div class="form-group">
                <label>
                    {l s='reCAPTCHA widget language' mod='advancedemailguard'}
                </label>
                <div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="ADVEG_REC_LANGUAGE_SHOP" name="ADVEG_REC_LANGUAGE"
                            value="shop" class="custom-control-input"{if $config.ADVEG_REC_LANGUAGE === 'shop'} checked{/if}>
                        <label class="custom-control-label" for="ADVEG_REC_LANGUAGE_SHOP">{l s='Use shop language' mod='advancedemailguard'}</label>
                    </div>

                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="ADVEG_REC_LANGUAGE_BROWSER" name="ADVEG_REC_LANGUAGE"
                            value="browser" class="custom-control-input"{if $config.ADVEG_REC_LANGUAGE === 'browser'} checked{/if}>
                        <label class="custom-control-label" for="ADVEG_REC_LANGUAGE_BROWSER">{l s='Use browser language' mod='advancedemailguard'}</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>
                    {l s='reCAPTCHA widget theme' mod='advancedemailguard'}
                </label>
                <div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="ADVEG_REC_THEME_LIGHT" name="ADVEG_REC_THEME"
                            value="light" class="custom-control-input"{if $config.ADVEG_REC_THEME === 'light'} checked{/if}>
                        <label class="custom-control-label" for="ADVEG_REC_THEME_LIGHT">{l s='Light theme' mod='advancedemailguard'}</label>
                    </div>

                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="ADVEG_REC_THEME_DARK" name="ADVEG_REC_THEME"
                            value="dark" class="custom-control-input"{if $config.ADVEG_REC_THEME === 'dark'} checked{/if}>
                        <label class="custom-control-label" for="ADVEG_REC_THEME_DARK">{l s='Dark theme' mod='advancedemailguard'}</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>
                    {l s='reCAPTCHA badge position' mod='advancedemailguard'}
                </label>
                <div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="ADVEG_REC_POSITION_BOTTOM_RIGHT" name="ADVEG_REC_POSITION"
                            value="bottomright" class="custom-control-input"{if $config.ADVEG_REC_POSITION === 'bottomright'} checked{/if}>
                        <label class="custom-control-label" for="ADVEG_REC_POSITION_BOTTOM_RIGHT">{l s='Bottom right' mod='advancedemailguard'}</label>
                    </div>

                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="ADVEG_REC_POSITION_BOTTOM_LEFT" name="ADVEG_REC_POSITION"
                            value="bottomleft" class="custom-control-input"{if $config.ADVEG_REC_POSITION === 'bottomleft'} checked{/if}>
                        <label class="custom-control-label" for="ADVEG_REC_POSITION_BOTTOM_LEFT">{l s='Bottom left' mod='advancedemailguard'}</label>
                    </div>

                        <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="ADVEG_REC_POSITION_BOTTOM_INLINE" name="ADVEG_REC_POSITION"
                            value="inline" class="custom-control-input"{if $config.ADVEG_REC_POSITION === 'inline'} checked{/if}>
                        <label class="custom-control-label" for="ADVEG_REC_POSITION_BOTTOM_INLINE">{l s='Inline' mod='advancedemailguard'}</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>
                    {l s='reCAPTCHA v3 score threshold' mod='advancedemailguard'}
                </label>
                {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                <div class="js-range-update-value"{if $isDemo} style="opacity: .6;"{/if}>
                    <div class="row gutters-sm">
                        <div class="col">
                            <input type="range" class="custom-range js-recaptcha-score" min="0.0" max="1.0" step="0.1"
                                id="ADVEG_REC_SCORE_THRESHOLD" name="ADVEG_REC_SCORE_THRESHOLD"
                                value="{$config.ADVEG_REC_SCORE_THRESHOLD|escape:'html':'UTF-8'}"{if $isDemo} disabled{/if}>
                        </div>
                        <div class="col-auto">
                            <div class="js-range-value badge badge-lg badge-primary text-monospace">
                                {$config.ADVEG_REC_SCORE_THRESHOLD|escape:'html':'UTF-8'}</div>
                        </div>
                    </div>
                </div>
                <small class="form-text text-muted">
                    {l s='The minimum reCAPTCHA score required to pass validation.' mod='advancedemailguard'}<br>
                </small>
                <small class="form-text text-muted">
                    <span class="text-danger"><i class="material-icons-outlined md-22">sentiment_dissatisfied</i> 0.0</span> &mdash; {l s='Most likely a bot' mod='advancedemailguard'}
                    <span class="text-success"><i class="material-icons-outlined md-22">sentiment_satisfied</i> 9.0</span> &mdash; {l s='Very good interaction' mod='advancedemailguard'}
                    <span class="text-muted"><i class="material-icons-outlined md-22">sentiment_neutral</i> 1.0</span> &mdash; {l s='Not recommended' mod='advancedemailguard'}
                </small>
                <small class="form-text text-muted">
                    <b>{l s='Note:' mod='advancedemailguard'}</b> {l s='We highly recommend that you test your forms before deciding on a score threshold. You can view the score results in the "Validations" section.' mod='advancedemailguard'}<br>
                    <a href="https://developers.google.com/recaptcha/docs/v3#interpreting_the_score" target="_blank">
                        <i class="material-icons-outlined md-18 mr-1">open_in_new</i>{l s='Learn more about the reCAPTCHA v3 score' mod='advancedemailguard'}</a>
                </small>

                <div class="modal fade js-recaptcha-score-warning" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="material-icons-outlined text-warning">warning_amber</i>
                                    {$trans.warning|escape:'html':'UTF-8'}
                                </h5>
                            </div>
                            <div class="modal-body">
                                {l s='Several users have reported that the 1.0 score is too hard to reach even in normal activity. Continue if you know what you are doing.' mod='advancedemailguard'}
                                <a href="https://github.com/google/recaptcha/issues/235" target="_blank">
                                    <i class="material-icons-outlined md-18 mr-1">open_in_new</i>{l s='See reported issue' mod='advancedemailguard'}</a>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary js-cancel">{$trans.cancel|escape:'html':'UTF-8'}</button>
                                <button type="button" class="btn btn-warning" data-dismiss="modal">{$trans.continue|escape:'html':'UTF-8'}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <input type="checkbox" name="ADVEG_REC_LEGAL_LINKS" id="ADVEG_REC_LEGAL_LINKS" class="switch"
                    {if $config.ADVEG_REC_LEGAL_LINKS} checked{/if}{if $isDemo} disabled{/if}>
                <label class="ml-1">
                    {l s='Hide the reCAPTCHA widget' mod='advancedemailguard'}
                </label>
                {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                <small class="form-text text-muted">
                    {l s='Hide the reCAPTCHA widget and display the legal links in the footer instead.' mod='advancedemailguard'}<br>
                    {l s='This option is not available for the checkbox version of reCAPTCHA v2 since user interaction with the widget is required.' mod='advancedemailguard'}<br>
                    <a href="{$basePath|escape:'html':'UTF-8'}views/img/legallinks.png" target="_blank">
                        <i class="material-icons-outlined md-18 mr-1">open_in_new</i>{l s='See an example' mod='advancedemailguard'}</a>
                </small>
            </div>

            <div class="form-group">
                <input type="checkbox" name="ADVEG_REC_LOAD_ON_DEMAND" id="ADVEG_REC_LOAD_ON_DEMAND" class="switch"
                    {if $config.ADVEG_REC_LOAD_ON_DEMAND} checked{/if}{if $isDemo} disabled{/if}>
                <label class="ml-1">
                    {l s='Load reCAPTCHA when needed' mod='advancedemailguard'}
                </label>
                {if $isDemo}{include file='./../../_partials/demo.badge.tpl'}{/if}
                <small class="form-text text-muted">
                    {l s='Load the reCAPTCHA library only when it is needed. Disable this option if you are having issues.' mod='advancedemailguard'}<br>
                    {l s='This option will not work if you have a form (newsletter or login for example) that needs protection and is present on every page.' mod='advancedemailguard'}
                </small>
            </div>

            {include file='../../_partials/save.tpl'}
        </div>
    </form>
</div>