{if $item.name=="Contact"}
	<div class="contact_menu_active"><a href="{$item.href}">{$item.name}</a></div>
{else}
	<div class="menu_active"><div class="menu_active2"><a href="{$item.href}">{$item.name}</a></div></div>
{/if}

{assign var="item_name" value=$item.name}
