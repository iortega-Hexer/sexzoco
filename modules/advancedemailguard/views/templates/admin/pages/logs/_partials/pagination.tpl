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
<div class="d-flex">
    <form action="{$url|escape:'html':'UTF-8'}&tab=logs&{$logs.urlParams|escape:'html':'UTF-8'}"
        method="post">
        <input type="hidden" name="_action" value="logs.page.step">
        <div class="input-group">
            <div class="select2-control">
                <select name="perPage" class="form-control select2" data-width="70px" onchange="this.form.submit()">
                    {foreach array(20, 50, 100, 300, 1000) as $option}
                        <option value="{$option|escape:'html':'UTF-8'}"{if $logs.perPage == $option} selected{/if}>
                            {$option|escape:'html':'UTF-8'}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="input-group-append">
                <span class="input-group-text">{$trans.perPage|escape:'html':'UTF-8'}</span>
            </div>
        </div>
    </form>
    {if $logs.count > $logs.perPage}
        <div class="ml-auto">
            <ul class="pagination mb-0">
                {if $logs.paginator->getPrevUrl()}
                    <li class="page-item">
                        <a class="page-link" href="{$logs.paginator->getPrevUrl()|escape:'html':'UTF-8'}">
                            <i class="material-icons-outlined md-16">arrow_back</i></a>
                    </li>
                {/if}

                {foreach $logs.paginator->getPages() as $page}
                    {if $page.url}
                        <li class="page-item{if $page.isCurrent} active{/if}">
                            <a class="page-link" href="{$page.url|escape:'html':'UTF-8'}">{$page.num|escape:'html':'UTF-8'}</a>
                        </li>
                    {else}
                        <li class="page-item disabled">
                            <span class="page-link">{$page.num|escape:'html':'UTF-8'}</span>
                        </li>
                    {/if}
                {/foreach}

                {if $logs.paginator->getNextUrl()}
                    <li class="page-item">
                        <a class="page-link" href="{$logs.paginator->getNextUrl()|escape:'html':'UTF-8'}">
                            <i class="material-icons-outlined md-16">arrow_forward</i></a>
                    </li>
                {/if}
            </ul>
        </div>
    {/if}
</div>
