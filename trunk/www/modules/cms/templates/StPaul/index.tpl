{include file="header.tpl"}

<div class="background-container">
	<div class="afbeeldingen-container">
		{items root_path="achtergrondfotos" wrap_div="false" item_template="achtergronditem.tpl"}
	</div>
</div>


<div class="logo">
	<a href="{cms_href path="home/Home"}">
		{assign var="escaped_path" value="`$images_path`home/logo.gif"|escape:url}
		<img src="{phpthumb_url params="src=$escaped_path&h=111&w=272"}" style="float:left;width:272px;height:111px;margin:0px 15px;" />
	</a>
</div>

{if $file.type=='home'}
	<div class="home-container">
		<div class="homebuttons-bg">
			<div class="homebuttons-container">
				{items level="0" expand_levels="0" wrap_div="false" item_template="menu/homebutton.tpl"}
			</div>
		</div>
	</div>
{else}
	<div class="main-container">
	<div class="schaduw-container"></div>
	<div class="buttons-container">
		<div class="buttons">
			{items level="0" expand_levels="0" wrap_div="false" item_template="menu/button.tpl" active_item_template="menu/active_button.tpl"}
		</div>
	</div>

	<div class="menu-container">
	{items level="1" expand_levels="0" wrap_div="false" item_template="menu/item.tpl" active_item_template="menu/active_item.tpl"}
	</div>

	{if $file.type==''}
		{include file="pages/default.tpl"}
	{else}
		{include file="pages/`$file.type`.tpl"}
	{/if}

	</div>
{/if}



{include file="footer.tpl"}