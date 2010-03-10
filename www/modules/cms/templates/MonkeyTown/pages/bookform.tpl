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

<div id="menu">
<div id="menupos">
{items level="1" expand_levels="0" wrap_div="" item_template="menu/menu_item.tpl" }
{items root_path="Home" expand_levels="0" wrap_div="" item_template="menu/home_item.tpl" }
</div>
</div>

{assign var='branch' value=$folder.path}

<div id="content">

<div class="content">

<span class="title" >
{$file.name}</span><br/><br/>

<form name="form1" method="post" action="index.php?p=reservation" onsubmit="return validateForm(this)">
    <p><p>{$file.option_values.top_info}</p></p>

 	{$file.option_values.branch}:
	<b>{$branch}</b><p>


	{items root_path="`$branch`/data/parties" expand_levels="0" wrap_div="" item_template="menu/party_booking_item.tpl" }

  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="contentpaneopen">
    <tr>
      <td width="35%">
		{$file.option_values.name_child}*:
      </td>

      <td width="65%">
        <input type="text" name="naam" id="naam" class="inputbox">
      </td>
    </tr>
    <tr>
      <td width="35%">
		{$file.option_values.age_child}*:
      </td>
      <td width="65%">
        <input type="text" name="leeftijd" id="leeftijd" class="inputbox">
      </td>

    </tr>
    <tr>
      <td width="35%">
 		{$file.option_values.address}*:
      </td>
      <td width="65%">
        <input type="text" name="adres" class="inputbox">
      </td>
    </tr>
    <tr>
      <td width="35%" >
 		{$file.option_values.zip}:
      </td>

      <td width="65%" >
        <input type="text" name="postcode" class="inputbox">
      </td>
    </tr>
    <tr>
      <td width="35%">
 		{$file.option_values.city}*:
      </td>
      <td width="65%">
        <input type="text" name="plaats" class="inputbox">
      </td>

    </tr>
    <tr>
      <td width="35%">
  		{$file.option_values.phone}.*:
      </td>
      <td width="65%">
        <input type="text" name="tel" id="tel" class="inputbox">
      </td>
    </tr>
    <tr>
      <td width="35%" >

 		{$file.option_values.how_many}*:
      </td>
      <td width="65%" >
        <input type="text" name="hoeveel" id="hoeveel" class="inputbox">
      </td>
    </tr>
    <tr>
      <td width="35%" >
 		{$file.option_values.what_time}*:
      </td>

      <td width="65%" >
        <select name="hoelaat" class="inputbox">
          <OPTION VALUE="">{$file.option_values.select_time}</OPTION>
          <OPTION VALUE="10:00">10:00 </OPTION>
          <OPTION VALUE="10:30">10:30</OPTION>
          <OPTION VALUE="11:00">11:00</OPTION>
          <OPTION VALUE="11:30">11:30</OPTION>

          <OPTION VALUE="12:00">12:00</OPTION>
          <OPTION VALUE="12:30">12:30</OPTION>
          <OPTION VALUE="13:00">13:00</OPTION>
          <OPTION VALUE="13:30">13:30</OPTION>
          <OPTION VALUE="14:00">14:00</OPTION>
          <OPTION VALUE="14:30">14:30</OPTION>

          <OPTION VALUE="15:00">15:00</OPTION>
          <OPTION VALUE="15:30">15:30</OPTION>
          <OPTION VALUE="16:00">16:00</OPTION>
          <OPTION VALUE="16:30">16:30</OPTION>
          <OPTION VALUE="17:00">17:00</OPTION>
          <OPTION VALUE="17:30">17:30</OPTION>

        </select>
      </td>
    </tr>
    <tr>
      <td width="35%" >
 		{$file.option_values.dinner_time}*:
      </td>
      <td width="65%" >
          <select name="etenstijd" class="inputbox">
            <OPTION VALUE="">{$file.option_values.select_time}</OPTION>
						<OPTION VALUE="12:00">12:00</OPTION>

            <OPTION VALUE="12:30">12:30</OPTION>
            <OPTION VALUE="13:00">13:00</OPTION>
            <OPTION VALUE="13:30">13:30</OPTION>
            <OPTION VALUE="14:00">14:00</OPTION>
            <OPTION VALUE="14:30">14:30</OPTION>
            <OPTION VALUE="15:00">15:00</OPTION>

            <OPTION VALUE="15:30">15:30</OPTION>
            <OPTION VALUE="16:00">16:00</OPTION>
            <OPTION VALUE="16:30">16:30</OPTION>
            <OPTION VALUE="17:00">17:00</OPTION>

          </select>
      </td>
    </tr>

    <tr>
      <td width="35%" >
 		{$file.option_values.what_date}*:
      </td>
      <td width="65%" >

	    <!-- INIZIO CALENDARIO -->
	    <span id='calendario_outer1' style='font-family:Arial;'>
	    <input name='datum1' type='text' value='03/10/2010' readonly='readonly' id='calendario1' style='font-family:Arial;font-size:X-Small;'/>
	    </span>
	    <input type='button' name='calendario1_calbutton' value=' ... ' id='calendario1_calbutton' />
{literal}
	    <script language='javascript'>
	    calendario1_outer_EnableHideDropDownFlag = false;
	    calendario1_outer_VisibleDate = scrivi_data_odierna(1);
	    function calendario1_Up_SetClick(addClickTo)
	    {
	      if(addClickTo != '') document.getElementById(addClickTo).onclick = calendario1_Up_CallClick;
	      document.onmousedown = CalendarPopup_Up_LostFocus;
	      document.getElementById('calendario1').onclick = calendario1_Up_CallClick;
	    }
	    function calendario1_Up_CallClick(e)
	    {
		var monthnames = new Array('january','february','march','april','may','june','july','august','september','october','november','december');
		var daynames = new Array('m','t','w','t','f','s','s');
		var day_week_link=new Array(0,1,2,3,4,5,6);
		CalendarPopup_Up_DisplayCalendar(day_week_link,'calendario1_outer_EnableHideDropDownFlag', 'calendario1','','','calendario1_div', 'calendario1_monthYear', 'calendario1_Up_PreDisplayCalendar', 'calendario1_Up_PreMonthYear', 'style="color:Black;background-color:White;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"','style="color:Black;background-color:LightGray;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"','style="color:Gray;background-color:AntiqueWhite;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"','style="color:Black;background-color:Yellow;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"','style="color:White;background-color:Red;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"','style="color:Black;background-color:Orange;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"', 'style="color:Black;background-color:White;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"', 'style="color:Black;background-color:Green;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"', 'style="color:Black;background-color:White;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"', 'style="color:Black;background-color:White;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"', 2, monthnames, daynames, 1, 6, 5, false, false, '03/10/2010', '','calendario1_calbutton',1, false, 'calendario1_Up_PostBack', 0, 0, false, 'Annulla', false, 'Data oggi:', '', '', -1, 'calendario1_outer_VisibleDate', 'Seleziona una data', CalendarPopup_Array_calendario1_outer, '', '', '', '');
	    }
	    function calendario1_Up_PreDisplayCalendar(theDate)
	    {
	      var monthnames = new Array('january','february','march','april','may','june','july','august','september','october','november','december');
	      var daynames = new Array('m','t','w','t','f','s','s');
	      var day_week_link=new Array(0,1,2,3,4,5,6);
	      CalendarPopup_Up_DisplayCalendarByDate(day_week_link,'calendario1','','calendario1_div', 'calendario1_monthYear', 'calendario1_Up_PreDisplayCalendar', 'calendario1_Up_PreMonthYear', theDate, 'style="color:Black;background-color:White;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"','style="color:Black;background-color:LightGray;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"','style="color:Gray;background-color:AntiqueWhite;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"','style="color:Black;background-color:Green;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"','style="color:White;background-color:Red;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"','style="color:Black;background-color:Orange;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"', 'style="color:Black;background-color:White;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"', 'style="color:Black;background-color:Green;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"', 'style="color:Black;background-color:White;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"', 'style="color:Black;background-color:White;font-family:Verdana,Helvetica,Tahoma,Arial;font-size:XX-Small;"', 2, monthnames, daynames, 1, 6, 5, false, false, '03/10/2010', '', false, 'calendario1_Up_PostBack', false, 'Annulla', false, 'Data oggi:', '', '', -1, 'calendario1_outer_VisibleDate', 'Seleziona una data', CalendarPopup_Array_calendario1_outer, '', '', '', '');
	    }
	    calendario1_Up_SetClick('calendario1_calbutton');
	    function calendario1_Up_PreMonthYear(theDate)
	    {
		var monthnames = new Array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
		CalendarPopup_Up_DisplayMonthYear('calendario1_div', 'calendario1_monthYear', 'calendario1_Up_PreDisplayCalendar', 'calendario1_Up_PreMonthYear', monthnames, theDate, 'Applica', 'Annulla', '03/10/2010', '');
	    }
	    function calendario1_Up_PostBack() {
	    }
	    var CalendarPopup_Array_calendario1_outer = null;
	    </script>
{/literal}
	    <div id='calendario1_div' onmouseover='document.onmousedown = null;' onmouseout='document.onmousedown = CalendarPopup_Up_LostFocus;' style='visibility:hidden;z-index:500;position:absolute;background:#fff;' class="select-free"><!--[if lte IE 6.5]><iframe></iframe><![endif]--></div>
	    <div id='calendario1_monthYear' onmouseover='document.onmousedown = null;' onmouseout='document.onmousedown = CalendarPopup_Up_LostFocus;' style='visibility:hidden;z-index:501;position:absolute;' class="select-free"><!--[if lte IE 6.5]><iframe></iframe><![endif]--></div>

	    <!-- FINE CALENDARIO -->
	          </td>
    </tr>

    <tr>
      <td width="35%">

 		{$file.option_values.email}*:
      </td>
      <td width="65%">
        <input type="text" name="email" id="email" class="inputbox">
      </td>
    </tr>
    <tr>
      <td width="35%">
 		{$file.option_values.email_repeat}*:
      </td>

      <td width="65%">
        <input type="text" name="email2" id="email2" class="inputbox">
      </td>
    </tr>
  </table>
  <br/>
  <p>{$file.option_values.extra_info}</p>	<textarea name="overig" cols="56" rows="6" class="inputbox"></textarea><br/>
	<input type="checkbox" name="nieuwsbrief" class="inputbox" checked>{$file.option_values.receive_info}<br/>

    <input name="submit" type="submit" value="{$file.option_values.send}" class="button" >
  </form>
*{$file.option_values.obligatory_fields}<br/>
<p>{$file.option_values.bottom_info}</p><br/>
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
