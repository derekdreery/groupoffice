<div id="selected-news-panel">
	{items filter_by_get_category_id="true" filter_by_get_year="true" random="true" root_path="Nieuws" item_template="pages/nieuws_item.tpl" wrap_div="false"}
</div>

<div id="filter-panel">
	<div id="filter-category-panel">
		<div class="filter-title"><a href="{update_get_params baseurl="`$file.href`" filter_category_id=""}#the-anchor">Categorieën</a></div>
		{child_categories category_name="Root" item_template="menu/category_item.tpl" wrap_div="false"}
	</div>
	<div id="filter-year-panel">
		<div class="filter-title"><a href="{update_get_params baseurl="`$file.href`" filter_year=""}#the-anchor">Jaren</a></div>
		{sort_years root_path="Nieuws" item_template="menu/year_item.tpl" wrap_div="false"}
	</div>
</div>