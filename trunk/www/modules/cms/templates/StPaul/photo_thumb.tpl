{if $index mod 2}
	{assign var="thumb_class" value="thumb-even"}
{else}
	{assign var="thumb_class" value="thumb-odd"}
{/if}

<div class="{$thumb_class}">
<a title="{$file.name}" class="thumb_link" href="{$go_url}modules/files/download.php?path={$file.relpath}" rel="shadowbox[Album];player=img">
	{assign var="escaped_path" value="`$file.path`"|escape:url}
	<img src="{phpthumb_url params="src=$escaped_path&zc=1&w=120&h=120"}" alt="{$file.name}" />
</a>
</div>
