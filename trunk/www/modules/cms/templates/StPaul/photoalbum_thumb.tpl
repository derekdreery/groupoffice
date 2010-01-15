{assign var="escaped_path" value="`$file_storage_path``$item.option_values.thumb`"|escape:url}
<div class="{$item.type}-thumb">
	<div class="album-link">
		<a href="{$item.href}">{$item.name}</a>
  </div>
	<div class="album-thumb">
		<a href="{$item.href}"><img src="{phpthumb_url params="src=$escaped_path&h=170&w=199"}" style="float:left;width:199px;height:170px;margin:0px 15px;" /></a>
	</div>
</div>