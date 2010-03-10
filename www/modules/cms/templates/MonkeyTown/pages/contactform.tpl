<div id="wrapper2">
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

<p><p>{$file.option_values.top_text}</p></p>

<form name="form1" method="post" action='{items root_path="`$folder.path`/data" expand_levels="0" wrap_div="" item_template="menu/contact_return.tpl" max_items="1"}' onsubmit="return validateForm(this)">
 	{$file.option_values.branch}:
	<b>{$folder.path}</b><p>

  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="contentpaneopen">
    <tr>
      <td width="35%">
  		{$file.option_values.your_name}*:
      </td>
      <td width="65%">
        <input type="text" name="naam" class="inputbox">
      </td>
    </tr>
    <tr>
      <td width="35%">
 		{$file.option_values.email}*:
 	  </td>

      <td width="65%">
        <input type="text" name="email" class="inputbox">
      </td>
    </tr>
    <tr>
      <td width="35%">
   		{$file.option_values.phone}:
      </td>
      <td width="65%">
        <input type="text" name="tel" class="inputbox">
      </td>

    </tr>
  </table>
  <br/>
  {$file.option_values.your_message}<br/>
  <textarea name="overig" cols="56" rows="6" class="inputbox"></textarea><br/>
  <input name="submit" type="submit" value="{$file.option_values.send}" class="button">
  </form>

*{$file.option_values.obligatory_fields}<br/>

<br/>
<br/>
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
