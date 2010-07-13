{if $item.fstype eq "folder"}
	<li class="menu-item">
				<a onmouseover="javascript:mopen('{$item.name}-div');" onmouseout="javascript:mclosetime();" href="#"><span class="button_down"><img src="{$template_url}images/button_down.bmp" /></span>{$item.name}</a>
				<div class="submenu-panel" id="{$item.name}-div" onmouseover="javascript:mcancelclosetime();" onmouseout="javascript:mclosetime();" >
					<div class="submenu-top"></div>
					<div class="submenu-mid">
						{items root_path="Hoofdmenu/`$item.name`" item_template="menu/submenu_item.tpl" }
					</div>
					<div class="submenu-bottom"></div>
				</div>
	</li>
{else}
	<li class="menu-item">
		<a href="{$item.href}"><span class="button_right"><img src="{$template_url}images/button_right.bmp" /></span>{$item.name}</a>
	</li>
{/if}