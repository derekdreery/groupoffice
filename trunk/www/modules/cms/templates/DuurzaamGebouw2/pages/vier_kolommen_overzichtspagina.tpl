<div id="four-columns-panel">
	{php}$_SESSION['GO_SESSION']['DG2']['column_used'] = false;{/php}
	{items root_path="MENU1/Diensten" random="true" wrap_div="false" item_template="parts/column_panel.tpl"}
	{php}$_SESSION['GO_SESSION']['DG2']['column_used'] = false;{/php}
	{items root_path="Projecten" random="true" wrap_div="false" item_template="parts/column_panel.tpl"}
	{php}$_SESSION['GO_SESSION']['DG2']['column_used'] = false;{/php}
	{items root_path="Nieuws" random="true" wrap_div="false" item_template="parts/column_panel.tpl"}
	{php}$_SESSION['GO_SESSION']['DG2']['column_used'] = false;{/php}
	{items root_path="Footer/In de pers" random="true" wrap_div="false" item_template="parts/column_panel.tpl"}
</div>

{items max_items="1" random="true" filter_by_get_year="true" filter_by_get_category_id="true" root_path="Projecten/Overzichten" item_template="parts/partner.tpl" wrap_div="false"}

{include file="parts/tweets.tpl"}