{assign var="escaped_path" value="`$file_storage_path``$item.option_values.afbeelding`"|escape:url}
<div class="{$item.name}">
	<img src="{phpthumb_url params="src=$escaped_path&h=120&w=80"}" style="float:left;width:80px;height:120px;margin:0px 15px;" />
</div>
