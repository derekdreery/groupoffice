{if $item.option_values.nieuws_afbeelding neq ''}
	{assign var="escaped_path" value="`$item.option_values.nieuws_afbeelding`"}
	<span>
		<br />
		<div class="nieuws-afbeelding"><img src="{thumbnail_url path="`$escaped_path`" zc=1 w=233 h=174}" alt="{$item.name}" /></div>
		{$item.content}
	</span>
{else}
	<span>
		<br />
		{$item.content}
	</span>
{/if}