{if $item.type eq 'news'}
	<a href="{$item.href}" class="link"><BR>{$item.name}</a>
	<small>({$item.option_values.date})</small><br/>
	{assign var="content" value=$item.content|strip_tags:false}
	{$content|truncate:135:'...':false:false}
	<br/>
	<a href="{$item.href}">{$item.option_values.read_on}</a><br/>
{/if}