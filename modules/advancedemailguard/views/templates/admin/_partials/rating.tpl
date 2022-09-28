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
{if $ratingMessage}
    <div id="ratingMessage" class="rounded-0 mb-0 bg-primary text-white py-2">
        <div class="container-fluid px-md-4 d-flex align-items-center justify-content-center">
            <div>{$ratingMessage}{* This is HTML content *} :)</div>
            <div class="ml-3">
                <a href="{$psAddonsLinks.ratings|escape:'html':'UTF-8'}" target="_blank"
                    class="btn btn-light js-rating-link" style="color: #e83e8c">
                    <i class="material-icons-outlined">grade</i>
                    {l s='Sure, take me there' mod='advancedemailguard'}
                </a>
                <a href="#" class="btn btn-outline-light ml-1 js-rating-dismiss">
                    {l s='No, thanks' mod='advancedemailguard'}
                </a>
            </div>
        </div>
    </div>
{/if}