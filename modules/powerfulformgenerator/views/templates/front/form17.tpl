{**
 * @package   Powerful Form Generator
 * @author    Cyril Nicodème <contact@prestaddons.net>
 * @copyright Copyright (C) June 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
 * @since     2014-04-15
 * @version   2.7.9
 * @license   Nicodème Cyril
 *}

 {if isset($confirmation)}
    {if isset($success)}
        {$success nofilter}{* HTML CONTENT *}
    {else}
    <p class="alert alert-success">
        {l s='Your message has been successfully sent to our team.' d='Modules.powerfulformgenerator' mod='powerfulformgenerator'}
    </p>
    {/if}
{else}
    {if isset($header)}
        {$header nofilter}{* HTML CONTENT *}
    {/if}

    {if isset($errors) && $errors}
    	<div class="alert alert-danger">
    		<p>{if $errors|@count > 1}{l s='There are %d errors' sprintf=array($errors|@count) d='Modules.powerfulformgenerator' mod='powerfulformgenerator'}{else}{l s='There is %d error' sprintf=array($errors|@count) d='Modules.powerfulformgenerator' mod='powerfulformgenerator'}{/if}</p>
    		<ol>
    		{foreach from=$errors key=k item=error}
    			<li>{$error}</li>
    		{/foreach}
    		</ol>
    	</div>
    {/if}
    <form id="pfg-form-{$form_id|escape:'quotes':'UTF-8'}" action="{$request_uri nofilter}" method="post" class="pfg-forms" enctype="multipart/form-data">
        <h3>{$title|escape:'html':'UTF-8'}</h3>

        <fieldset>
          <div class="clearfix">
              {foreach from=$fields item=field}
                  {if $field.type == 'separator'}
                      {$field.element nofilter}
                  {elseif $field.type == 'legend'}
                      <div class="row" style="margin: 40px 0 20px; border-bottom: solid 1px #ebebeb">
                          <div class="col-md-9 offset-md-3">
                              <legend>{$field.label|escape:'htmlall':'UTF-8'}</legend>
                          </div>
                      </div>
                  {elseif $field.type == 'hidden'}
                      {$field.element nofilter}
                  {else}
                      <div class="form-group row {if $field.classname} {$field.classname|escape:'htmlall':'UTF-8'}{/if}">
                          <label for="{$field.id|escape:'htmlall':'UTF-8'}" class="col-md-3 form-control-label">{$field.label|escape:'htmlall':'UTF-8'}{if $field.required} <em class="required">*</em>{/if}</label>
                          <div class="col-md-9">
                              {if $field.type == 'static'}
                                  <p class="form-control-static">{$field.value|escape:'quotes':'UTF-8'}</p>
                              {else}
                                  {$field.element nofilter}{* HTML CONTENT *}
                              {/if}
                          </div>
                      </div>
                  {/if}
              {/foreach}
          </div>
          {hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
          <div class="submit row">
              <input type="hidden" name="pfg_form_id" value="{$form_id|escape:'quotes':'UTF-8'}" />
              <button type="submit" name="submitMessage" class="btn btn-primary"><span>{if $label_btn}{$label_btn|escape:'quotes':'UTF-8'}{else}{l s='Send' d='Modules.powerfulformgenerator' mod='powerfulformgenerator'}<i class="icon-chevron-right right"></i></span>{/if}</button>
          </div>
        </fieldset>
    </form>


    {if isset($footer)}
        {$footer nofilter}{* HTML CONTENT *}
    {/if}
{/if}
