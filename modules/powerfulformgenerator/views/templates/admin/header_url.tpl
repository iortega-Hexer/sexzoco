{**
* @package   Powerful Form Generator
* @author    Cyril Nicodème <contact@prestaddons.net>
* @copyright Copyright (C) June 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
* @since     2014-04-15
* @version   2.7.9
* @license   Nicodème Cyril
*}
{if !$active}
({l s='Your form is not active and cannot be accessed.' mod='powerfulformgenerator'})
{else}
{if $show_url}
<div style="margin: 20px 0; padding: 10px 15px; background-color: #DFF2BF; border: 1px solid #4F8A10; color: #4F8A10">
	<p style="color: #4F8A10">
		{l s='In order to access your form from the frontend, you need to add the following url in.' mod='powerfulformgenerator'}
		<a href="{$url_cms|escape:'quotes':'UTF-8'}" title="{l s='Access to the CMS manager' mod='powerfulformgenerator'}" style="font-weight: bold; color: #4F8A10">{l s='Preferences' mod='powerfulformgenerator'} &gt; SEO &amp; URL</a>.
	</p>
	<p style="color: #4F8A10">
		{$url|escape:'quotes':'UTF-8'} {if $active } (<a href="{$url|escape:'quotes':'UTF-8'}" title="{l s='View it live' mod='powerfulformgenerator'}" target="_blank" style="font-weight: bold; color: #4F8A10">{l s='View it live' mod='powerfulformgenerator'}</a>){else}({l s='Your form is not active and cannot be accessed.' mod='powerfulformgenerator'}){/if}
	</p>
	<p style="margin-top: 20px;">
		<a href="{$fields_url|escape:'quotes':'UTF-8'}" title="{l s='Click here to manage the fields' mod='powerfulformgenerator'}" style="font-style: italic; color: #4F8A10; text-decoration: underline"><img src="{$module_dir}views/img/cog.gif" alt="{l s='Manage' mod='powerfulformgenerator'}" style="vertical-align: sub; width: 16px; height: 16px"> {l s='Click here to manage the fields' mod='powerfulformgenerator'}</a>
	</p>
</div>
{/if}
<div style="margin: 20px 0; padding: 10px 15px; background-color: #DFF2BF; border: 1px solid #4F8A10; color: #4F8A10">
	{if !$show_url}<p style="color: #4F8A10">
		<a href="{$fields_url|escape:'quotes':'UTF-8'}" title="{l s='Click here to manage the fields' mod='powerfulformgenerator'}" style="color: #2b542c; font-weight:bold"><img src="{$module_dir}views/img/cog.gif" alt="{l s='Manage' mod='powerfulformgenerator'}" style="vertical-align: sub; width: 16px; height: 16px"> {l s='Click here to manage the fields' mod='powerfulformgenerator'}</a>
	</p>{/if}
	{if $show_hook}
	<p style="color: #4F8A10">
		{l s='You can access your form by using the following HOOK anywhere you want in your .tpl file (theme)' mod='powerfulformgenerator'}:<br />
		<pre><code>{literal}{{/literal}hook h='displayPowerfulForm' mod='powerfulformgenerator' id={$id|intval}{literal}}{/literal}</code></pre>
	</p>
	{/if}
	<p style="color: #4F8A10">
		{l s='You can access your form by using the following CMS Shortcode in your' mod='powerfulformgenerator'}
		<a href="{$cms_pages_url|escape:'quotes':'UTF-8'}" title="{l s='View your CMS pages !' mod='powerfulformgenerator'}" style="color: #2b542c; font-weight:bold">{l s='CMS pages!' mod='powerfulformgenerator'}</a>
		:<br />
		<pre><code>{literal}{{/literal}powerfulform:{$id|intval}{literal}}{/literal}</code></pre>
	</p>
</div>
{/if}
