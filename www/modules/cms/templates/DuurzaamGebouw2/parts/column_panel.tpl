{if $smarty.session.GO_SESSION.DG2.column_used eq false}
	{if $item.option_values.column_enabled neq ''}
		<div class="column-panel">
			<a href="{$item.href}"><div class="column-header">
				<img class="column-header-image" src="{thumbnail_url path="`$item.option_values.column_thumbnail`" zc=1 w=962 h=298"}" />
				<div class="column-header-text-panel">
					<div class="column-header-text1">
						{$item.option_values.column_thumbnail_title1}
					</div>
					<div class="column-header-text2">
						{$item.option_values.column_thumbnail_title2}
					</div>
				</div>
			</div>
			<div class="column-content">
				<div class="column-content-title">{$folder.name}</div>
				<div class="column-content-main">{$item.option_values.column_text}</div>
				<div class="column-content-link"><a href="{$item.href}">Lees verder</a></div>
			</div>
			<a href="{$item.href}"><div class="column-bottom">
				{$item.option_values.column_block_text}
			</div></a>
		</div>
		{php}$_SESSION['GO_SESSION']['DG2']['column_used']=true;{/php}
	{/if}
{/if}