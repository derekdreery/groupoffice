/* ---- Variables ---- */
var actb_timeOut = -1; // Autocomplete Timeout in ms (-1: autocomplete never time out)
var actb_lim = 4;    // Number of elements autocomplete can show (-1: no limit)
var actb_firstText = false; // should the auto complete be limited to the beginning of keyword?
var actb_mouse = true; // Enable Mouse Support

var actb_enable_multiply = 1; //Enable multiply select of values with actb_separator
var actb_separator = ","; //Any Separator string for multiply select

/* ---- Variables ---- */

/* --- Styles --- */
var actb_bgColor = '#f1f1f1';
var actb_textColor = '#000000';
var actb_hColor = '#cccccc';
var actb_fFamily = 'Arial';
var actb_fSize = '11px';
var actb_hStyle = 'text-decoration:underline;font-weight="bold"';
/* --- Styles --- */

/* ---- Constants ---- */
var actb_keywords = new Array();
var actb_display = false;
var actb_pos = 0;
var actb_total = 0;
var actb_curr = null;
var actb_rangeu = 0;
var actb_ranged = 0;
var actb_bool = new Array();
var actb_pre = 0;
var actb_toid;
var actb_tomake = false;
var actb_getpre = "";
var actb_mouse_on_list = true;
/* ---- Constants ---- */

function actb_parse(n){
//	var t = actb_curr.value.replace("'","\'");


// check for multiple select
if (actb_enable_multiply) {
var t_arr = actb_curr.value.split(actb_separator);
if (t_arr.length > 0)
//t = escape(t_arr[t_arr.length - 1]);
t = t_arr[t_arr.length - 1];
}
else
//var t = escape(actb_curr.value);
var t = actb_curr.value;

	t = t.replace('"','\"');
	t = t.replace('<','&lt;');
	t = t.replace('>','&gt;');
	
	var tobuild = '';
	var i;

	if (actb_firstText){
		var re = new RegExp("^" + t, "i");
	}else{
		var re = new RegExp(t, "i");
	}
	var p = n.search(re);
	
	for (i=0;i<p;i++){
		tobuild += n.substr(i,1);
	}
	tobuild += "<font style='"+(actb_hStyle)+"'>"
	for (i=p;i<t.length+p;i++){
		tobuild += n.substr(i,1);
	}
	tobuild += "</font>";
	for (i=t.length+p;i<n.length;i++){
		tobuild += n.substr(i,1);
	}
	return tobuild;
}
function curTop(){
	actb_toreturn = 0;
	obj = actb_curr;
	while(obj){
		actb_toreturn += obj.offsetTop;
		obj = obj.offsetParent;
	}
	return actb_toreturn;
}
function curLeft(){
	actb_toreturn = 0;
	obj = actb_curr;
	while(obj){
		actb_toreturn += obj.offsetLeft;
		obj = obj.offsetParent;
	}
	return actb_toreturn;
}
function actb_generate(actb_bool){
	hideElements('select');
	if (document.getElementById('tat_table')) document.body.removeChild(document.getElementById('tat_table'));
	a = document.createElement('table');
	a.cellSpacing='1px';
	a.cellPadding='2px';
	a.style.position='absolute';
	a.style.top = eval(curTop() + actb_curr.offsetHeight) + "px";
	a.style.left = curLeft() + "px";
	a.style.backgroundColor=actb_bgColor;
	a.id = 'tat_table';
    if (actb_mouse){
        a.onmouseover = actb_table_focus;
        a.onmouseout= actb_table_unfocus;
    }
	document.body.appendChild(a);
	var i;
	var first = true;
	var j = 1;

	var counter = 0;
	for (i=0;i<actb_keywords.length;i++){
		if (actb_bool[i]){
			counter++;
			r = a.insertRow(-1);
			if (first && !actb_tomake){
				r.style.backgroundColor = actb_hColor;
				first = false;
				actb_pos = counter;
			}else if(actb_pre == i){
				r.style.backgroundColor = actb_hColor;
				first = false;
				actb_pos = counter;
			}else{
				r.style.backgroundColor = actb_bgColor;
			}
			r.id = 'tat_tr'+(j);
			c = r.insertCell(-1);
			c.style.color = actb_textColor;
			c.style.fontFamily = actb_fFamily;
			c.style.fontSize = actb_fSize;
			
			var keyword;
			
			keyword = actb_keywords[i].replace('<','&lt;');
			keyword = keyword.replace('>','&gt;');

			c.innerHTML = actb_parse(keyword);
			c.mouseover = "alert()";
			c.id = 'tat_td'+(j);
			if (actb_mouse) c.onclick=actb_mouseclick;
			j++;
		}
		if (j - 1 == actb_lim && j < actb_total){
			r = a.insertRow(-1);
			r.style.backgroundColor = actb_bgColor;
			c = r.insertCell(-1);
			c.style.color = actb_textColor;
			c.style.fontFamily = 'arial narrow';
			c.style.fontSize = actb_fSize;
			c.align='center';
			c.innerHTML = '\\/';
			if (actb_mouse){
				c.onclick = actb_mouse_down;
			}
			break;
		}
	}
	actb_rangeu = 1;
	actb_ranged = j-1;
	actb_display = true;
	if (actb_pos <= 0) actb_pos = 1;
}
function actb_remake(){
	document.body.removeChild(document.getElementById('tat_table'));
	a = document.createElement('table');
	a.cellSpacing='1px';
	a.cellPadding='2px';
	a.style.position='absolute';
	a.style.top = eval(curTop() + actb_curr.offsetHeight) + "px";
	a.style.left = curLeft() + "px";
	a.style.backgroundColor=actb_bgColor;
	a.id = 'tat_table';
    if (actb_mouse){
        a.onmouseover = actb_table_focus;
        a.onmouseout= actb_table_unfocus;
    }
	document.body.appendChild(a);
	var i;
	var first = true;
	var j = 1;
	if (actb_rangeu > 1){
		r = a.insertRow(-1);
		r.style.backgroundColor = actb_bgColor;
		c = r.insertCell(-1);
		c.style.color = actb_textColor;
		c.style.fontFamily = 'arial narrow';
		c.style.fontSize = actb_fSize;
		c.align='center';
		c.innerHTML = '/\\';
		if (actb_mouse){
			c.onclick = actb_mouse_up;
		}
	}
	for (i=0;i<actb_keywords.length;i++){
		if (actb_bool[i]){
			if (j >= actb_rangeu && j <= actb_ranged){
				r = a.insertRow(-1);
				r.style.backgroundColor = actb_bgColor;
				r.id = 'tat_tr'+(j);
				c = r.insertCell(-1);
				c.style.color = actb_textColor;
				c.style.fontFamily = actb_fFamily;
				c.style.fontSize = actb_fSize;
				
				keyword = actb_keywords[i].replace('<','&lt;');
				keyword = keyword.replace('>','&gt;');

				c.innerHTML = actb_parse(keyword);
				c.id = 'tat_td'+(j);
				if (actb_mouse) c.onclick=actb_mouseclick;
				j++;
			}else{
				j++;
			}
		}
		if (j > actb_ranged) break;
	}
	if (j-1 < actb_total){
		r = a.insertRow(-1);
		r.style.backgroundColor = actb_bgColor;
		c = r.insertCell(-1);
		c.style.color = actb_textColor;
		c.style.fontFamily = 'arial narrow';
		c.style.fontSize = actb_fSize;
		c.align='center';
		c.innerHTML = '\\/';
		if (actb_mouse){
			c.onclick = actb_mouse_down;
		}
	}
}
function actb_goup(){
	if (!actb_display) return;
	if (actb_pos == 1) return;
	document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_bgColor;
	actb_pos--;
	if (actb_pos < actb_rangeu) actb_moveup();
	document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_hColor;
	if (actb_toid) clearTimeout(actb_toid);
	if (actb_timeOut > 0) actb_toid = setTimeout("actb_removedisp()",actb_timeOut);
}
function actb_godown(){
	if (!actb_display) return;
	if (actb_pos == actb_total) return;
	document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_bgColor;
	actb_pos++;
	if (actb_pos > actb_ranged) actb_movedown();
	document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_hColor;
	if (actb_toid) clearTimeout(actb_toid);
	if (actb_timeOut > 0) actb_toid = setTimeout("actb_removedisp()",actb_timeOut);
}
function actb_movedown(){
	actb_rangeu++;
	actb_ranged++;
	actb_remake();
}
function actb_moveup(){
	actb_rangeu--;
	actb_ranged--;
	actb_remake();
}
function actb_mclick(n){
	if (!actb_display) return;
	actb_display = 0;
	var word = '';
	var c = 0;
	for (var i=0;i<=actb_keywords.length;i++){
		if (actb_bool[i]) c++;
		if (c == n){
			word = actb_keywords[i];
			break;
		}
	}
	a = word;//actb_keywords[actb_pos-1];//document.getElementById('tat_td'+actb_pos).;
	actb_curr.value = a;
	actb_removedisp();
}

/* Mouse */
function actb_mouse_down(){
	document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_bgColor;
	actb_pos++;
	actb_movedown();
	document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_hColor;
	actb_curr.focus();
	actb_moue_on_list = 0;
	if (actb_toid) clearTimeout(actb_toid);
	if (actb_timeOut > 0) actb_toid = setTimeout("actb_removedisp()",actb_timeOut);
}
function actb_mouse_up(){
	document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_bgColor;
	actb_pos--;
	actb_moveup();
	document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_hColor;
	actb_curr.focus();
	actb_moue_on_list = 0;
	if (actb_toid) clearTimeout(actb_toid);
	if (actb_timeOut > 0) actb_toid = setTimeout("actb_removedisp()",actb_timeOut);
}
function actb_mouseclick(){
	if (!actb_display) return;
	actb_mouse_on_list = 0;
	actb_display = 0;
	//actb_curr.value = this.innerText;
	actb_removedisp();
}
function actb_table_focus(){
	actb_mouse_on_list = 1;
}
function actb_table_unfocus(){
	actb_mouse_on_list = 0;
	if (actb_toid) clearTimeout(actb_toid);
	if (actb_timeOut > 0) actb_toid = setTimeout("actb_removedisp()",actb_timeOut);
}
/* ---- */
function actb_penter(){
	if (!actb_display) return;
	actb_display = 0;
	var word = '';
	var c = 0;
	for (var i=0;i<=actb_keywords.length;i++){
		if (actb_bool[i]) c++;
		if (c == actb_pos){
			word = actb_keywords[i];
			break;
		}
	}
	a = word;//actb_keywords[actb_pos-1];//document.getElementById('tat_td'+actb_pos).;
	// check for multiply select
if (actb_enable_multiply) {
var t_last_index = actb_curr.value.lastIndexOf(actb_separator);
if (t_last_index > 0)
var t_last = actb_curr.value.substring(0,t_last_index+actb_separator.length);
else
var t_last = "";
actb_curr.value = t_last + a + actb_separator;
}
else
actb_curr.value = a;
	actb_removedisp();
}
function actb_removedisp(){

	//if (!actb_mouse_on_list) { 
		actb_display = 0;
		if (document.getElementById('tat_table')) document.body.removeChild(document.getElementById('tat_table'));
		if (actb_toid) clearTimeout(actb_toid);
	//}
	showElements('select');
}
function actb_checkkey(evt){
	a = evt.keyCode;
	if (a == 38){ // up key
		actb_goup();
	}else if(a == 40){ // down key
		actb_godown();
	}else if(a == 13){
		actb_penter();
	}
}
function actb_tocomplete(sndr,evt,arr){
	if (arr) actb_keywords = arr;
	if (evt.keyCode == 38 || evt.keyCode == 40 || evt.keyCode == 13) return;
	var i;
	if (actb_display){ 
		var word = 0;
		var c = 0;
		for (var i=0;i<=actb_keywords.length;i++){
			if (actb_bool[i]) c++;
			if (c == actb_pos){
				word = i;
				break;
			}
		}
		actb_pre = word;//actb_pos;
	}else{ actb_pre = -1};
	
	if (!sndr) var sndr = evt.srcElement;
	actb_curr = sndr;

	if (sndr.value == ''){
		actb_removedisp();
		return;
	}
	// check for multiply select
	if (actb_enable_multiply) {
	var t_arr = sndr.value.split(actb_separator);
	if (t_arr.length > 0)
	//t = escape(t_arr[t_arr.length - 1]);
	t = t_arr[t_arr.length - 1];
	}
	else
	//var t = escape(sndr.value);
	var t = sndr.value;

	t = t.replace('"','\"');
	if (actb_firstText){
		var re = new RegExp("^" + t, "i");
	}else{
		var re = new RegExp(t, "i");
	}
	
	actb_total = 0;
	actb_tomake = false;
	for (i=0;i<actb_keywords.length;i++){
		actb_bool[i] = false;
		if (re.test(actb_keywords[i])){
			actb_total++;
			actb_bool[i] = true;
			if (actb_pre == i) actb_tomake = true;
		}
	}
	if (actb_toid) clearTimeout(actb_toid);
	if (actb_timeOut > 0) actb_toid = setTimeout("actb_removedisp()",actb_timeOut);

	actb_generate(actb_bool);
}

function kH(e) {
var pK = document.all? window.event.keyCode:e.which;
return pK != 13;
}
if (document.layers) document.captureEvents(Event.KEYPRESS);

function hideElements(tagName)
{
	if(document.all)
	{
		var i;
		for (i = 0; i < document.all.tags(tagName).length; ++i)
		{
			var obj = document.all.tags(tagName)[i];
			if (!obj || !obj.offsetParent)
				continue;
				
			obj.style.visibility = "hidden";
		}
	}
}

function showElements(tagName)
{
	if(document.all)
	{
		var i;
		for (i = 0; i < document.all.tags(tagName).length; ++i)
		{
			var obj = document.all.tags(tagName)[i];
			if (!obj || !obj.offsetParent)
				continue;
			obj.style.visibility = "";
		}		
	}
}