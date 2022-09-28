{**
* @package   Powerful Form Generator
* @author    Cyril Nicodème <contact@prestaddons.net>
* @copyright Copyright (C) June 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
* @since     2014-04-15
* @version   2.7.9
* @license   Nicodème Cyril
*}

{include file="$tpl_dir./breadcrumb.tpl"}
{if isset($confirmation)}
	{if isset($success)}
	{$success}{* HTML CONTENT *}
	{else}
	<p>{l s='Your message has been successfully sent to our team.' mod='powerfulformgenerator'}</p>
	{/if}

	<ul class="footer_links">
		<li><a href="{$base_dir|escape:'quotes':'UTF-8'}"><img class="icon" alt="" src="{$img_dir|escape:'quotes':'UTF-8'}icon/home.gif"/></a><a href="{$base_dir|escape:'quotes':'UTF-8'}">{l s='Home'  mod='powerfulformgenerator'}</a></li>
	</ul>
{else}

	{if isset($header)}
	{$header}{* HTML CONTENT *}
	{/if}

	{include file="$tpl_dir./errors.tpl"}
	<form id="pfg-form-{$form_id|escape:'quotes':'UTF-8'}" action="{$request_uri|escape:'quotes':'UTF-8'}" method="post" class="std pfg-forms" enctype="multipart/form-data">
		<h3>{$title|escape:'html':'UTF-8'}</h3>

		<fieldset>
			{foreach from=$fields item=field}
			{if $field.type == 'separator'}
				{$field.element|escape:'quotes':'UTF-8'}
			{elseif $field.type == 'legend'}
				<legend>{$field.label|escape:'htmlall':'UTF-8'}</legend>
			{elseif $field.type == 'hidden'}
				{$field.element}{* HTML CONTENT *}
			{else}
			<p class="{$field.type|escape:'quotes':'UTF-8'}{if $field.classname} {$field.classname|escape:'htmlall':'UTF-8'}{/if}">
				<label for="{$field.id|escape:'htmlall':'UTF-8'}">{$field.label|escape:'htmlall':'UTF-8'}{if $field.required} <em class="required">*</em>{/if}</label>
				{if $field.type == 'static'}
					<p class="form-control-static">{$field.value|escape:'quotes':'UTF-8'}</p>
				{else}
					{$field.element}{* HTML CONTENT *}
				{/if}
			</p>
			{/if}
			{/foreach}
			{hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
			<p class="submit">
				<input type="hidden" name="pfg_form_id" value="{$form_id|escape:'quotes':'UTF-8'}" />
				<input type="submit" name="submitMessage" value="{if $label_btn}{$label_btn|escape:'quotes':'UTF-8'}{else}{l s='Send' mod='powerfulformgenerator'}{/if}" class="button_large" />
			</p>
		</fieldset>
	</form>

	{if isset($footer)}
	{$footer}{* HTML CONTENT *}
	{/if}
{/if}
