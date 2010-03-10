<div id="wrapper3">
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

<div id="menu">
<div id="menupos">
{items level="1" expand_levels="0" wrap_div="" item_template="menu/menu_item.tpl" }
{items root_path="Home" expand_levels="0" wrap_div="" item_template="menu/home_item.tpl" }
</div>
</div>

<div id="content">

<div class="content">

<span class="title" >
{$file.name}</span><br/><br/>

{$file.content}
{files template="photo_thumb.tpl"}

<div style="text-align:center;z-index:100;">
<script type="text/javascript"><!--

google_ad_client = "pub-2876290956390340";

google_ad_width = 468;

google_ad_height = 60;

google_ad_format = "468x60_as";

google_ad_type = "text";

google_ad_channel = "";

google_color_border = "9ae2b4";

google_color_bg = "9ae2b4";

google_color_link = "0000FF";

google_color_text = "000000";

google_color_url = "008000";

//-->

</script>

<script type="text/javascript"

  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">

</script>
</div></div>
<div id="footer">
</div>
</div>
</div>
<img src="../images/mainfooter.gif" style="margin-left:4px;"/>
<a name="s" id="s"></a>
<!-- Start of StatCounter Code -->

<script type="text/javascript" language="javascript">

var sc_project=2389572;

var sc_invisible=0;

var sc_partition=22;

var sc_security="1f7049e9";

</script>
