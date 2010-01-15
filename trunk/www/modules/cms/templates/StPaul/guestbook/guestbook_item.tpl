{if "01" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='januari'}
{elseif "02" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='februari'}
{elseif "03" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='maart'}
{elseif "04" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='april'}
{elseif "05" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='mei'}
{elseif "06" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='juni'}
{elseif "07" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='juli'}
{elseif "08" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='augustus'}
{elseif "09" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='september'}
{elseif "10" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='oktober'}
{elseif "11" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='november'}
{elseif "12" eq $item.mtime|date_format:"%m"}
	{assign var='maand' value='december'}
{/if}

<div class="guestbook-message">
	<div class="guestbook-title">
		<div class="guestbook-title-name">
			{$item.name}
		</div>
		<div class="guestbook-title-time">
			{$item.mtime|date_format:"%d"} {$maand} {$item.mtime|date_format:"%Y"} - {$item.mtime|date_format:"%H:%M:%S  "}
		</div>
	</div>
	<div class="guestbook-content">
	...................................................................................................
	{$item.content}
	<br />
	...................................................................................................
	<br /><br />
	</div>
</div>