{if $item.name=="Contact"}
	<div class="contact-menuitem"><a href="{$item.href}">{$item.name}</a></div>
{else}
	<div class="menuitem"><div class="menuitem2"><a href="{$item.href}">{$item.name}</a></div></div>
{/if}

