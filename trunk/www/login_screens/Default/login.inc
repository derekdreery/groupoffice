<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="/community/www/login_screens/Default/login.css" />
<link rel="stylesheet" type="text/css" href="/community/www/ext/resources/css/ext-all.css" />
<script language="javascript" type="text/javascript" src="/community/www/javascript/common.js"></script>
<link rel="shortcut icon" href="/community/www/lib/favicon.ico" />
<title>Group-Office</title>
<script type="text/javascript">
if (self != top)
{
	parent.location=document.location;
}
</script></head>
<body dir="ltr" onload="document.login_form.username.focus();" onkeypress="executeOnEnter(event, 'document.login_form.submit();')">

<form name="login_form" id="login_form" method="post" action="/community/www/index.php" target="_parent"><input type="hidden" name="load_frames" value="true" class="x-form-text x-form-field" />
<input type="hidden" name="task" value="login" class="x-form-text x-form-field" />


<script type="text/javascript" language="javascript">

function set_language(dropbox)
{
	document.location='/community/www/index.php?SET_LANGUAGE='+dropbox.value;
}
</script>

<div style="width:500px;position:absolute;top:50%;left:50%;margin-top:-300px;margin-left:-250;text-align:center">
<div class="x-box-blue" style="width:100%;">
    <div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>

    <div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc">        
    
    	<div style="text-align:left;margin-bottom:20px;"><img src="login_screens/Default/GOCOM.gif" /></div>
    
   		<table cellspacing="2" cellpadding="1" style="vertical-align:middle;"><tbody>
   		<tr>
   			<td colspan="2">
			<?php
			if($GO_CONFIG->allow_registration)
			{
			
			echo '<h2>'.$login_no_account.'</h2>'.
				'<p>'.$login_no_account_text.'</p>';
			
			
			$href = 'register.php';
			if (isset($_REQUEST['return_to']))
			{
				$href = add_params_to_url($href, 'return_to='.urlencode($_REQUEST['return_to']));
			}
			$hyperlink = new hyperlink($href, sprintf($login_register, $GO_CONFIG->title));
			echo '<br />'.$hyperlink->get_html().'<br /><br />';
			
			echo '<h2>'.sprintf($login_to, $GO_CONFIG->title).'</h2>';
			
			
			}else {
			echo '<h2>'.sprintf($login_to, $GO_CONFIG->title).'</h2>';
			}
			?>
			</tr>
			<tr>
				<td><?php echo $login_language; ?>:</td>
				<td>
				<?php
				$select = new select('SET_LANGUAGE', $GO_LANGUAGE->language['code']);
				$select->set_attribute('onchange', 'javascript:set_language(this);');
				$languages = $GO_LANGUAGE->get_languages();
				while($language = array_shift($languages))
				{
					$select->add_value($language['code'], $language['description']);
				}
				echo $select->get_html();
				?>
			
			</td>
			</tr>
			<tr>
				<td style="whitespace:nowrap;">
				<?php echo $strUsername; ?>:</td>
				<td>
				<input type="text" name="username" value="" class="x-form-text x-form-field" style="width:200px" />
				
				<?php
				if (count($auth_sources) > 1)
				{
					$cookie_key = isset($_COOKIE['GO_AUTH_SOURCE_KEY']) ? $_COOKIE['GO_AUTH_SOURCE_KEY'] : '0';
					$auth_source_key = isset($_REQUEST['auth_source_key']) ? $_REQUEST['auth_source_key'] : $cookie_key;
				
					$select = new select('auth_source_key', $auth_source_key);
					for($i=0;$i<count($auth_sources); $i++)
					{
						$select->add_value($i, $auth_sources[$i]['name']);
					}
				
					echo '@'.$select->get_html();
				
				}else
				{
					$input = new input('hidden', 'auth_source_key','0');
					echo $input->get_html();
				}				
				
				?>
				</td>
			</tr>
			<tr>
				<td style="whitespace:nowrap;">
				<?php echo $strPassword; ?>:</td>
				<td>
				<input type="password" name="password" value="" class="x-form-text x-form-field" style="width:200px" />
				<a href="lost_password.php"><?php echo $login_lost_password; ?></a>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-top:10px;">
				
				<?php
				$checkbox = new checkbox('remind', 'remind', 'true', $login_remind, false);
				echo $checkbox->get_html();
				?>
				</td>			
			</tr>
			<tr>
				<td colspan="2" style="padding-top:10px;">
				<?php
				$button = new button($cmdLogin, 'javascript:document.login_form.submit();');
				echo $button->get_html();
				?>
				</td>
			</tr>
			</tbody>
			</table>
			
			<div style="text-align:right"><img src="login_screens/Default/intermesh-klein.gif" /></div>
 
        
        
   
    </div></div></div>
    <div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>
</div>
<br />
<br />
Intermesh Group-Office Community<br />
Copyright &copy Intermesh
</div>

</form>

	
</body>
</html>



