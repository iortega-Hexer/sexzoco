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
<a class="list-group-item list-group-item-action font-weight-bold text-left text-md-center{if !$tab} active{/if}"
    data-toggle="list" href="#list-settings">
    <i class="material-icons-outlined text-primary">settings</i>
    <span class="d-inline-block d-md-block px-1 pt-md-1">{l s='Settings' mod='advancedemailguard'}</span>
</a>

<a class="list-group-item list-group-item-action font-weight-bold text-left text-md-center{if $tab === 'forms'} active{/if}"
    data-toggle="list" href="#list-forms">
    <i class="material-icons-outlined text-primary">assignment_turned_in</i>
    <span class="d-inline-block d-md-block px-1 pt-md-1">{l s='Forms' mod='advancedemailguard'}</span>
</a>

<a class="list-group-item list-group-item-action font-weight-bold text-left text-md-center{if $tab === 'logs'} active{/if}"
    data-toggle="list" href="#list-logs">
    <i class="material-icons-outlined text-primary">policy</i>
    <span class="d-inline-block d-md-block px-1 pt-md-1">{l s='Validations' mod='advancedemailguard'}</span>
</a>