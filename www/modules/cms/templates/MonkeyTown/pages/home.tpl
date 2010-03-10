<div id="wrapper1">
<img src="{$template_url}images/logo.gif" class="logo"/>

<img src="{$template_url}images/animals.gif" class="animals"/>
<div id="kop" >
<span id="title">

{if $folder.name eq 'Home'}
	{assign var='title' value='Monkey Town'}
{else}
  {assign var='title' value=$folder.name}
{/if}

<h1>{$title}</h1>
</span>
</div>

<div id="menuindex"></div>
<div id="content">
<div id="mainindex">
<table><tbody><tr><td valign="top">
<ul class="sitelist">
{items level="0" expand_levels="0" item_template="menu/branch_item.tpl" }
</ul>
</td></tr></tbody></table>
</div>
</div>
<div id="footer">
</div>

</div>
</div>
<img src="images/mainfooter.gif" style="margin-left:4px;" />
<!-- Start of StatCounter Code -->

<script type="text/javascript" language="javascript">

var sc_project=2389572;

var sc_invisible=0;

var sc_partition=22;

var sc_security="1f7049e9";

</script>

