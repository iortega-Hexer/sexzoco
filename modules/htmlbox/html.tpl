{if $htmlbox_ssl==1}
    {if $is_https_htmlbox==1}
        {if $page_name!='index'}
            {if $htmlbox_home==1}
                {* disable *}
            {else}
                {$htmlboxbody}
            {/if}
        {else}
            {$htmlboxbody}
        {/if}
    {/if}
{else}
    {if $page_name!='index'}
        {if $htmlbox_home==1}
                {* disable *}
        {else}
            {$htmlboxbody}
        {/if}
     {else}
        {$htmlboxbody}
     {/if}
{/if}