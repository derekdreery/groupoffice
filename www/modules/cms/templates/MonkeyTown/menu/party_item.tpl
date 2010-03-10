{if $item.type eq 'party'}
	<div class="party">
	<h3>{$item.name}</h3>
	<b>{$item.option_values.price}</b><br/>
	{$item.content}
	</div>
{/if}