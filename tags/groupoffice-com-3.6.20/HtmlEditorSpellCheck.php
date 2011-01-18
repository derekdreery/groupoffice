<?PHP
/** 
 * RPM Solutions UK Ltd
 * Using aspell and pspell to enbale spell checking.
 * 
 * @Code By RPM Solutions UK Ltd
 * @author Shaun Forsyth <shaun@rpm-solutions.co.uk>
 */

require_once('Group-Office.php');

header('Content-Type: text/html; charset=UTF-8');

class spellchecker {
	var $plink;
	
	function spellchecker(){

		if(!function_exists('pspell_new'))
			die('The spell check function requires the PHP pspell extension. Please ask your system administrator to install it with the right dictionaries.');
		
		$this->plink = pspell_new($_REQUEST['lang'],"","","",PSPELL_FAST);
	}
	
	function checkText($text){
		$words = $this->_getWords($text);
		$checkspelling = $this->checkWords($words);
		if (!empty($checkspelling)){
			return $checkspelling;	
		}else{
			return false;
		}
	}
	
	function _getWords($text){
		//Add some space to the html
		$text = str_replace('<',' <',$text);
		//Remove Signature if found
		//this was a custom hack I had already applied to fix 
		//signature changing problems, which turned out to be a entitiy problem, not location
		$text = preg_replace('/<div id=(")?EmailSignature(")?.*?<\/div>/si','',$text);
		//Assume there might be HTML so remove it;
		$text = strip_tags($text);
		//Decode HTML Entities
		$text = html_entity_decode($text,ENT_QUOTES);
		//Remove any email addresses (this could be stronger!)
		$text = preg_replace('/\w+@[a-zA-Z0-9-.]+\.(com|edu|gov|mil|net|org|biz|info|name|museum|us|ca|uk)/si',' ',$text);
		//Remove any web address (this could be stronger)
		$text = preg_replace('/(https?:\/\/)?\w+\.[a-zA-Z0-9-_.]+\.(co\.uk|com|edu|gov|mil|net|org|biz|info|name|museum|us|ca|uk)[a-zA-Z0-9-_.\/]*?(\s|$)/si',' ',$text);
		//Remove numbers
		$text = preg_replace('/[0-9.,]+/sm',' ',$text);
		//Replace any characters which should be splitters
		$text = preg_replace('/['.preg_quote('!"\'#$%&()*+,-.:;<=>?@[]^_{|}§©«®±¶·¸»¼½¾\\¿×÷¤/','/').']/si',' ',$text);
		//Fix MultiSpace
		$text = preg_replace('/\s+/',' ',$text);
		
		//print $text;
		
		return array_unique(explode(' ',$text));
	}
	
	function checkWords($words){
		if (is_array($words) and !empty($words)){
			$badwords = array();
			foreach ($words as $word){
				if (!pspell_check($this->plink,$word)){
					$badwords[$word] = array_slice(pspell_suggest($this->plink,$word),0,21);	
				}
			}
			
			return $badwords;
		}else{
			return array();
		}
	}
}

$check = new spellchecker();

$mispeltwords = $check->checkText($_REQUEST['tocheck']);

//print_r($mispeltwords);

if (is_array($mispeltwords) && !empty($mispeltwords)){
	$data['errorcount'] = count($mispeltwords);
	$data['text'] = replaceMisspeltWords($mispeltwords,$_REQUEST['tocheck']);
}else{
	$data['errorcount'] = '0';
	$data['text'] = $_REQUEST['tocheck'];
}

print json_encode($data);

function replaceMisspeltWords($mispeltwords,$text){
	$tokens = preg_split('/(<|>)/',$text,NULL,PREG_SPLIT_DELIM_CAPTURE);
	$inhtml = false;
	foreach ($tokens as $key => $token){
		if ($token == '<'){
			$inhtml = true;
			continue;
		}elseif($token == '>'){
			$inhtml = false;
			continue;
		}else{
			if (!$inhtml){
				foreach ($mispeltwords as $word => $sugestions){
					//not sure how to fix this in one go so will use another regex to add another space between repeat words
					$tokens[$key] = preg_replace('/(\b(\w+)(\b\s)*\2\b)/','\2\3\3\2',$tokens[$key]);
					$tokens[$key] = preg_replace('/(^|[._,\'"-]|&lt;|\s)'.preg_quote($word).'(\s|[._,@\'"-]|&gt;|$)/i','\1'.inlinespellsystem($word,$sugestions,'\2').'\2',$tokens[$key]);
				}
			}
		}
	}
	
	return implode('',$tokens);
}

function inlinespellsystem($word,$sugestions,$endDelem){
	return '<span class="spelling" ieAfterObject="'.htmlentities($endDelem).'">'.$word.'<ul>'.wraparray('<li>','</li>',$sugestions).'</ul></span>';
}

function wraparray($before,$after,$sugestions){
	$out = '';
	if (is_array($sugestions) && !empty($sugestions)){
		foreach ($sugestions as $sugestion){
			$out .= $before.String::to_utf8($sugestion).$after;
		}
	}else{
		$out .= $before.'No Sugestions'.$after;	
	}
	
	return $out;
}
?>