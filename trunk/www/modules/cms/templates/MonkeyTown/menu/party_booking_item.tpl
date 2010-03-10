{if $item.type eq 'party'}
	{if $item.option_values.bookable eq 'on'}
		<input type="radio" name="party" value="{$item.name}">{$item.name} {$item.option_values.price}<br/>
	{/if}
{/if}