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
{if $recaptchaType === 'v3'}
    <div id="adveg-grecaptcha" class="adveg-grecaptcha-{if $recaptchaPos === 'inline'}inline{else}fixed{/if}"></div>
{/if}
{if $recaptchaLegal !== null}
    <div id="adveg-grecaptcha-legal">
        {l s='Site protected by reCAPTCHA.' mod='advancedemailguard'}
        <a href="{$recaptchaLegal.privacy|escape:'html':'UTF-8'}" target="_blank">{l s='Privacy' mod='advancedemailguard'}</a>  -
        <a href="{$recaptchaLegal.terms|escape:'html':'UTF-8'}" target="_blank">{l s='Terms' mod='advancedemailguard'}</a>
    </div>
{/if}