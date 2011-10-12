{php}
	$this->assign('priority',$this->_tpl_vars['item']['priority']%3);
{/php}

{if $item.type eq 'verwijzing_bestaande_pagina'}
	{assign var="item_href" value="`$item.option_values.page_folder`/`$item.option_values.page_title`"}
	{assign var="item_name" value="`$item.option_values.page_title`"}
	{php}
		$this->assign('item_href',$_SESSION['GO_SESSION']['DG2']['page_base_url'].urlencode($this->_tpl_vars['item_href']));
	{/php}
{else}
	{assign var="item_href" value="`$item.href`"}
	{assign var="item_name" value="`$item.name`"}
{/if}

<div class="menu3-item-active">
	<div class="menu3-item-left-{$priority}"></div>
	<div class="menu3-item-mid-{$priority} menu3-item-mid-active"><a href="{$item_href}">{$item_name}</a></div>
	<div class="menu3-item-right-{$priority}"></div>
</div>