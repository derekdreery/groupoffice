

function expand_all()
{
	document.forms[0].form_action.value = 'expand_all';
	document.forms[0].submit();
}

function collapse_all()
{
	document.forms[0].form_action.value = 'collapse_all';
	document.forms[0].submit();
}

function move_mail()
{
	document.forms[0].form_action.value = 'move';
	document.forms[0].submit();
}



function set_flag()
{
	document.forms[0].form_action.value = 'set_flag';
	document.forms[0].submit();
}

function open_folder(account_id, mailbox)
{
		parent.messages.location='messages.php?account_id='+account_id+'&mailbox='+escape(mailbox);
}
function expand_folder(folder_id)
{
	document.forms[0].expand_id.value=folder_id;
	document.forms[0].submit();
}
function open_account(account_id)
{
	parent.update_toolbar(false);
	document.location = 'treeview.php?account_id='+account_id+'&mailbox=INBOX';
	parent.message.location.href='about:blank';
	parent.messages.location.href='messages.php?account_id='+account_id+'&mailbox=INBOX';
}

function update_toolbar(enabled)
{
	var messageButtons = get_object('messageButtons');
	var accountButtons = get_object('accountButtons');
	if(enabled)
	{
		messageButtons.style.display='';
		accountButtons.style.display='none';
	}else
	{
		messageButtons.style.display='none';
		accountButtons.style.display='';
	}
	toggle_message_frame(enabled);
}

function toggle_message_frame(enabled)
{
	var message = get_object('message');
	var messages = get_object('messages');
	
	if(enabled)
	{
		if(message.style.height!='100%')
		{
			var fsButton = get_object('fsButton');
			fsButton.style.display='';
			messages.style.height='50%';
			message.style.height='50%';
		}
	}else
	{		
		message.style.height='0';
		messages.style.height='100%';
	}
}

function close_message_frame()
{	
	var message = get_object('message');
	if(message.style.height=='100%')
	{
		toggle_navigation(true);
	}else
	{
		message.src='blank.html';
		var messagestd = get_object('messages');
		messagestd.style.height='100%';		
		messagestd.style.height='0';
		var fsButton = get_object('fsButton');
		fsButton.style.display='none';
		update_toolbar(false);
		messages.table_clear_selection('email_form', 'messages_table');
	}
}

function close_message_frame_for_delete()
{
	var message = get_object('message');
	message.src='blank.html';
	
	var tv = get_object('treeview');
	var tvFrame = get_object('treeviewFrame');
	tvFrame.style.width='100%';
	tv.style.width='25%';
	var messagestd = get_object('messages');
	messagestd.style.height='100%';		
	messagestd.style.height='0';
	var fsButton = get_object('fsButton');
	fsButton.style.display='none';
	update_toolbar(false);
}

function toggle_next_button(visible)
{
	var next_button =get_object('next_button');
	if(visible)
	{
		next_button.style.display='';
	}else
	{
		next_button.style.display='none';
	}
}

function toggle_previous_button(visible)
{
	var previous_button =get_object('previous_button');
	if(visible)
	{
		previous_button.style.display='';
	}else
	{
		previous_button.style.display='none';
	}
}

function toggle_navigation(display)
{
	var fsButton = get_object('fsButton');
	fsButton.style.display='';
	
	var tv = get_object('treeview');
	var tvFrame = get_object('treeviewFrame');
	
	if(display)
	{
		tvFrame.style.width='100%';
		tv.style.width='25%';
	}else
	{
		tvFrame.style.width='0';
		tv.style.width='0';
	}
	
	var messagesTD = get_object('messagesTD');
	var messages = get_object('messages');
	var message = get_object('message');
		

	if(display)
	{
		messagesTD.style.width='75%';
		message.style.height='50%';
		messages.style.height='50%';
	}else
	{
		messages.style.height='0';
		message.style.height='100%';	
		messagesTD.style.width='100%';
	}
}
