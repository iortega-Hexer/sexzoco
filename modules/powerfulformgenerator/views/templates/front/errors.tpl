{**
* @package   Powerful Form Generator
* @author    Cyril Nicodème <contact@prestaddons.net>
* @copyright Copyright (C) June 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
* @since     2014-04-15
* @version   2.7.9
* @license   Nicodème Cyril
*}

{if isset($errors) && $errors}
	<div class="alert alert-danger">
		<p>{if $errors|@count > 1}{l s='There are %d errors' sprintf=$errors|@count mod='powerfulformgenerator'}{else}{l s='There is %d error' sprintf=$errors|@count mod='powerfulformgenerator'}{/if}</p>
		<ol>
		{foreach from=$errors key=k item=error}
			<li>{$error}</li>
		{/foreach}
		</ol>
	</div>
{/if}
