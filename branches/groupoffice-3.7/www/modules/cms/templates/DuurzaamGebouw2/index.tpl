{include file="header.tpl"}

<div id="content-panel">
	<div id="content-panel-top"></div>
	<div id="content-panel-mid">
		{$file.content}
	</div>
	<div id="content-panel-bottom"></div>
</div>

<div id="big-panel">
{if $file.type==''}
	{include file="pages/standaard_pagina.tpl"}
{else}
	{include file="pages/`$file.type`.tpl"}
{/if}
</div><!-- big-panel -->

{include file="footer.tpl"}
