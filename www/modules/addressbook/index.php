<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.64 $ $Date: 2006/11/28 12:35:40 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
      
 * @package Addressbook
 * @category Addressbook
 */
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');
require_once($GO_LANGUAGE->get_language_file('addressbook'));

load_basic_controls();
load_control('datatable');


$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? htmlspecialchars($_REQUEST['link_back']) : $_SERVER['REQUEST_URI'];
$post_action = isset($_REQUEST['post_action']) ? $_REQUEST['post_action'] : 'search';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
//load contact management class
require_once($GO_MODULES->class_path."addressbook.class.inc");
$ab = new addressbook();

$ab_settings = $ab->get_settings($GO_SECURITY->user_id);
$addressbook_id = isset($_REQUEST['addressbook_id']) ? smart_addslashes($_REQUEST['addressbook_id']) : $ab_settings['addressbook_id'];
$addressbook = $ab->get_addressbook($addressbook_id);
$addressbook_id = $addressbook['id'];

if($ab_settings['addressbook_id'] != $addressbook_id)
{
	$ab_settings['addressbook_id'] = $addressbook_id;
	$ab->update_settings($ab_settings);
}


if($post_action == 'search')
{
	$GO_HEADER['body_arguments'] = 'onload="document.forms[0].query.focus()" onkeypress="executeOnEnter(event, \'_search()\');"';
}

//Create table for search results already so we can add the delete handler to the button menu.
$datatable = new datatable('addressbook_table');
$datatable->set_attribute('width','100%');
$GO_HEADER['head'] = $datatable->get_header();

require_once($GO_THEME->theme_path."header.inc");



$menu = new button_menu();

$menu->add_button('ab_search', $contacts_search, $_SERVER['PHP_SELF'].'?post_action=search&addressbook_id='.$addressbook_id);
$menu->add_button('ab_browse', $contacts_contacts, $_SERVER['PHP_SELF'].'?post_action=browse&addressbook_id='.$addressbook_id);
$menu->add_button('ab_companies', $ab_companies, $_SERVER['PHP_SELF'].'?post_action=companies&addressbook_id='.$addressbook_id);
$menu->add_button('users', $contacts_members, $_SERVER['PHP_SELF'].'?post_action=members&addressbook_id='.$addressbook_id);
$menu->add_button('add_contact', $ab_new_contact, 'contact.php?addressbook_id='.$addressbook_id.'&return_to='.urlencode($link_back));
$menu->add_button('ab_add_company', $ab_new_company, 'company.php?addressbook_id='.$addressbook_id.'&return_to='.urlencode($link_back));
$menu->add_button('ab_addressbooks', $strAdministrate, 'admin.php?return_to='.urlencode($link_back));


if ($post_action != 'members' && $post_action != 'addressbooks')
{
	$menu->add_button('delete_big',$cmdDelete, "javascript:document.addressbook_form.task.value='".$task."';".$datatable->get_delete_handler());
	//$menu->add_button('cut',$ab_move, "javascript:move_items();");
	
}

$form = new form('addressbook_form');
$form->add_html_element(new input('hidden', 'task', $task,false));
$form->add_html_element(new input('hidden', 'post_action', $post_action));
$form->add_html_element(new input('hidden', 'addressbook_id', $addressbook_id));
$form->add_html_element(new input('hidden', 'move_addressbook_id', 0, false));





switch($post_action)
{
	case 'members':
		require_once('members.inc');
		break;

	case 'browse':
		
		$edit_file='edit_contacts.php';
		$ids_array='contacts_table';
		require_once('classes/contacts_list.class.inc');
		
		$menu->add_button('enter_data_big',$ab_edit_selected, "javascript:edit_items();");
		$form->add_html_element($menu);

		$datatable = new contacts_list('contacts_table', $addressbook_id, false, true, '0', $link_back);
		
		$ids_array='contacts_table';
		if ($task == 'move_to_addressbook')
		{
			$move_ab = $ab->get_addressbook($_POST['move_addressbook_id']);

			if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $move_ab['acl_write']) &&
			$GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
			{
				foreach($con_list->selected as $contact_id)
				{
					$contact_id =str_replace('2:', '', $contact_id);
					$ab->move_contact_to_addressbook($contact_id, $_POST['move_addressbook_id']);
				}
			} else
			{
				$p = new html_element('p', $strAccessDenied);
				$p->set_attribute('class','Error');
				$this->add_outerhtml_element($p);
			}
		}

		$link = new hyperlink("javascript:popup('select_addressbook.php?callback=select_addressbook', '300','400');",$addressbook['name']);
		$link->set_attribute('style','margin:3px;');
		$link->set_attribute('class','normal');

		$p = new html_element('p', $ab_addressbook.': '.$link->get_html());
		$p->set_attribute('style','margin-bottom:2px;margin-top:2px;');
		$form->add_html_element($p);

		$form->add_html_element($con_list);
		break;

	case 'companies':

		$edit_file='edit_companies.php';
		$ids_array='companies_table';
		
		$menu->add_button('enter_data_big',$ab_edit_selected, "javascript:edit_items();");
		$form->add_html_element($menu);
		
		require_once('classes/companies_list.class.inc');
		$datatable = new companies_list('companies_table', $addressbook_id, false, true, '0', $link_back);

		if ($task == 'move_to_addressbook')
		{
			$move_ab = $ab->get_addressbook($_POST['move_addressbook_id']);

			if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $move_ab['acl_write']) &&
			$GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
			{
				foreach($com_list->selected as $company_id)
				{
					$company_id =str_replace('3:', '', $company_id);
					$ab->move_company_to_addressbook($company_id, $_POST['move_addressbook_id']);
				}
			} else
			{
				$p = new html_element('p', $strAccessDenied);
				$p->set_attribute('class','Error');
				$this->add_outerhtml_element($p);
			}
		}

		$link = new hyperlink("javascript:popup('select_addressbook.php?callback=select_addressbook', '300','400');",$addressbook['name']);
		$link->set_attribute('style','margin:3px;');
		$link->set_attribute('class','normal');

		$p = new html_element('p', $ab_addressbook.': '.$link->get_html());
		$p->set_attribute('style','margin-bottom:2px;margin-top:2px;');
		$form->add_html_element($p);

		//$form->add_html_element($com_list);
		break;

	default:
		$ids_array='addressbook_table';
		
		
		
		require_once('search.inc');
		break;
}


$form->add_html_element($datatable);
echo $form->get_html();
?>
<script type="text/javascript">
function select_addressbook(addressbook_id)
{
	document.forms[0].addressbook_id.value=addressbook_id;
	document.forms[0].submit();
}

function select_search_addressbook(addressbook_id)
{
	document.forms[0].search_addressbook_id.value=addressbook_id;
	document.forms[0].submit();
}

function move_to_addressbook(addressbook_id)
{
	document.forms[0].task.value="move_to_addressbook";
	document.forms[0].move_addressbook_id.value=addressbook_id;
	document.forms[0].submit();
}

function move_items()
{
	var count = <?php echo $datatable->get_count_selected_handler(); ?>;
	if(count==0)
	{
		alert("<?php echo htmlentities($strNoItemSelected); ?>");
	}else
	{
		popup('select_addressbook.php?callback=move_to_addressbook','300','400');
	}
}

function edit_items()
{
	var count = <?php echo $datatable->get_count_selected_handler(); ?>;
	if(count==0)
	{
		alert("<?php echo htmlentities($strNoItemSelected); ?>");
	}else
	{
		document.forms[0].action='<?php echo $edit_file; ?>?id_array=<?php echo $ids_array; ?>';
		document.forms[0].submit();
	}
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
