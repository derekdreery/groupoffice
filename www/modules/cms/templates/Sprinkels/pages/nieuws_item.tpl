{if $item.option_values.nieuws_afbeelding neq ''}
	{assign var="escaped_path" value="`$item.option_values.nieuws_afbeelding`"}
	<span>
		<br />
		<div class="nieuws-afbeelding"><img src="{thumbnail_url path="`$escaped_path`" zc=1 w=233 h=174}" alt="{$item.name}" /></div>
		{$item.content|substring:0:500}
		<a class="lees-verder" href="{$item.href}"><img src="{$template_url}images/lees_verder.bmp" /></a>
	</span>
{else}
	<span>
		<br />
		{$item.content|substring:0:500}
		<a class="lees-verder" href="{$item.href}"><img src="{$template_url}images/lees_verder.bmp" /></a>
	</span>
{/if}