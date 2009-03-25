<?php

define( 'GPGSTDIN', 0 );
define( 'GPGSTDOUT', 1 );
define( 'GPGSTDERR', 2 );
define( 'STATUS_FD', 5 );
define( 'PASSPHRASE_FD', 7 );
define( 'CHECKPASSWORD_FD', 3 );

class gnupg{

	var $home = '';
	var $gpg="/usr/bin/gpg";
	var $error = '';

	var $fd= array(
	GPGSTDIN  => array( 'pipe', 'r' ),  // this is stdin for the child (We write to this one)
	GPGSTDOUT => array( 'pipe', 'w' ),  // child writes here (stdout)
	GPGSTDERR => array( 'pipe', 'w' ),  // stderr
	STATUS_FD => array( 'pipe', 'w' ),
	PASSPHRASE_FD => array( 'pipe', 'r' )
	);

	public function __construct($home=null)
	{
		if(!isset($home) && isset($_SESSION['GO_SESSION']['username']))
		{
			//$home = '/home/mschering';
			global $GO_CONFIG;
			$home = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'];
		}
		if(isset($home))
		{
			$this->set_home($home);
		}
	}

	public function set_home($home)
	{
		$this->home = $home;
		putenv("HOME=".$home);
	}



	public function replace_encoded($data, $passphrase, $convert_to_html=true)
	{
		$data = trim(str_replace("\r", "", $data));
		
		if(strpos($data,'-----BEGIN PGP MESSAGE-----')!==false)
		{
			preg_match('/-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----/s', $data, $matches);
			if(!isset($matches[0]))
			{
				throw new Exception('PGP message is malformated!');
			}

			$encrypted = preg_replace(
							"'<br[^>]*>[\s]*'i",
							"\n",
			$matches[0]);

			$decrypted = $this->decode($encrypted, $passphrase);

			if(!$decrypted)
			{
				throw new Exception($this->error);
			}
			if($convert_to_html)
			{
				$decrypted=String::text_to_html($decrypted);
			}
			$data = str_replace($matches[0], $decrypted,$data);
		}
		return $data;
	}

	public function decode($data, $passphrase){

		global $GO_CONFIG;
		
		debug($data);

		$command = '-d';
		$this->run_cmd($command, $unencrypted, $errorcode, $data, $passphrase);

		return $unencrypted;
	}

	public function encode($data, $recipient, $user=null){
		$command = '-a  -e';

		if(!is_array($recipient))
		{
			$recipient = array($recipient);
		}
		$command .= ' -r '.escapeshellcmd(implode(' -r ', $recipient));		
		
		if(!empty($user))
		{
			$command .= ' -u '.escapeshellcmd($user);
		}
		$this->run_cmd($command, $encrypted, $errorcode,$data);

		if(ereg("-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----",$encrypted))
		{
			return str_replace("\r", '', $encrypted);
		}else
		{
			return false;
		}
	}

	public function export($fingerprint)
	{
		$this->run_cmd('--armor --export '.$fingerprint, $key);
		
		return $key;
	}
	
	function import($data)
	{
		$this->run_cmd('--armor --import', $output, $errorval, $data);
	}

	public function list_keys(){

		$this->run_cmd('--list-keys --fingerprint', $output);


		$pubkeys = $this->parse_keys_output($output);

		$this->run_cmd('-K', $output);

		$seckeys = $this->parse_keys_output($output);

		while($seckey = array_shift($seckeys))
		{
			if(isset($pubkeys[$seckey['id']]))
			{
				$pubkeys[$seckey['id']]['type']='pub/sec';
			}else
			{
				$pubkeys[$seckey['id']]=$seckey;
			}
		}
		return $pubkeys;
	}

	private function parse_keys_output($output)
	{
		$keys = array();
		$start=false;
		$key=array();

		$output = explode("\n", $output);

		foreach($output as $line)
		{
			if($start)
			{
				if(!empty($line))
				{
					if(preg_match('/[^=]+=(.*)/',$line, $matches))
					{
						$key["fingerprint"]=preg_replace("/\s*/",'', $matches[1]);
					}elseif(preg_match('/uid\s+(.*)/',$line, $matches))
					{
						$key["uid"]=trim($matches[1]);
					}elseif(preg_match('/pub\s+(.*)\s/',$line, $matches))
					{
						$key["id"]=trim($matches[1]);
						$key['type']='pub';
					}elseif(preg_match('/sec\s+(.*)\s/',$line, $matches))
					{
						$key["id"]=trim($matches[1]);
						$key['type']='sec';
					}
				}else
				{
					if(!empty($key["id"]))
					$keys[$key["id"]]=$key;

					$key=array();
				}
			}elseif(strpos($line, '------------')!==false)
			{
				$start = true;
			}
		}

		return $keys;
	}

	public function delete_key($fingerprint){

		$cmd = '--yes --batch --delete-secret-and-public-key '.escapeshellarg($fingerprint);

		$this->run_cmd($cmd, $output);

		return empty($this->error);
	}

	public function gen_key($name, $email, $passphrase, $comment, $keylength=2048, $expiredate=0) {

		$data='';
		$data.="Key-Type: DSA\n";
		$data.="Key-Length: 2048\n";
		$data.="Subkey-Type: ELG-E\n";
		$data.="Subkey-Length: " . $keylength . "\n";
		$data.="Name-Real: " . $name . "\n";
		$data.="Name-Comment: " . $comment . "\n";
		$data.="Name-Email: " . $email . "\n";
		$data.="Expire-Date: ". $expiredate ."\n";
		$data.="Passphrase: " . $passphrase . "\n";
		$data.="%commit\n";

		$tmp = $GLOBALS['GO_CONFIG']->tmpdir.'error.log';

		$cmd = '--gen-key --batch --armor';

		$this->run_cmd($cmd, $ouput, $errorcode, $data);

		return empty($ret);
	}

	private function run_cmd($cmd, &$output=null, &$errorcode=null, $data=null, $passphrase=null)
	{
		global $GO_CONFIG;

		$this->error = '';

		$complete_cmd = $this->gpg.' --display-charset utf-8 --utf8-strings --no-tty';

		if(isset($passphrase))
		{
			$complete_cmd .= ' --command-fd '.PASSPHRASE_FD;
		}
		$complete_cmd .= ' '.$cmd;

		$p = proc_open($complete_cmd,$this->fd, $pipes);
		
		foreach($pipes as $pipe)
		{
			//stream_set_blocking($pipe,0);
		}
		
		if(!is_resource($p))
		{
			throw new Exception('Could not open proc!');
		}

		if(!empty($data))
		{
			fwrite($pipes[GPGSTDIN], $data);			
			fclose($pipes[GPGSTDIN]);			

			//echo 'Status:'.stream_get_contents($pipes[STATUS_FD])."\n\n";
				
			if(isset($passphrase))
			{
				fwrite($pipes[PASSPHRASE_FD], $passphrase."\n");
				fclose($pipes[PASSPHRASE_FD]);				
			}
			
			//echo $this->error = stream_get_contents($pipes[STATUS_FD]);
			//echo 'Status:'.stream_get_contents($pipes[STATUS_FD])."\n\n";
		}
		//echo 'Status:'.stream_get_contents($pipes[STATUS_FD])."\n\n";

		$output = stream_get_contents($pipes[GPGSTDOUT]);
		fclose($pipes[GPGSTDOUT]);
		$this->error = stream_get_contents($pipes[GPGSTDERR]);

		//echo 'Status:'.stream_get_contents($pipes[STATUS_FD])."\n\n";
		
		fclose($pipes[STATUS_FD]);				

		$ret = proc_close($p);

		if($ret>0)
		{
			throw new Exception($this->error);
		}

		return $ret;
	}
}