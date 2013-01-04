<?php
/*
 * Command line test

export HOME=/home/groupoffice/users/admin
sudo -u www-data /usr/bin/gpg --no-use-agent --display-charset utf-8 --utf8-strings --no-tty --status-fd 5 --always-trust -a  -e -r admin@intermesh.dev -u admin@intermesh.dev
 */

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
	GPGSTDERR => array( 'pipe', 'w' )  // stderr
	//STATUS_FD => array( 'pipe', 'w' ),
	//PASSPHRASE_FD => array( 'pipe', 'r' )
	);

	var $pipes;

	public function __construct($home=null)
	{
		global $GO_CONFIG;
		
		if(isset($GO_CONFIG->cmd_gpg))
		{
			$this->gpg = $GO_CONFIG->cmd_gpg;
		}
		
		if(!isset($home) && isset($_SESSION['GO_SESSION']['username']))
		{
			$home = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/.gnupg';
		}

		if(isset($home))
		{
			$this->set_home($home);
			File::mkdir($home);
			chmod($home, 0700);
		}
	}

	public function set_home($home)
	{
		$this->home = $home;
		putenv("HOME=".$home);
	}
	
	public function is_pgp_data($data)
	{
		return preg_match('/-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----/s', $data);
	}

	public function is_public_key($data)
	{
		return preg_match('/-----BEGIN PGP PUBLIC KEY BLOCK-----.*-----END PGP PUBLIC KEY BLOCK-----/s', $data);
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
		
		// the text to decrypt from another platforms can has a bad sequence
		// this line removes the bad date and converts to line returns
		$data = preg_replace("/\x0D\x0D\x0A/s", "\n", $data);

		//commented out the following code because of this error:
		/*
		 * gpg: invalid radix64 character 3A skipped gpg: invalid radix64 character 2E skipped gpg: invalid radix64 character 2E skipped gpg: invalid radix64 character 28 skipped gpg: invalid radix64 character 29 skipped gpg: invalid radix64 character 3A skipped gpg: invalid radix64 character 2D skipped gpg: invalid radix64 character 3C skipped gpg: [don't know]: invalid packet (ctb=55) 
		 */		
		// we generate an array and add a new line after the PGP header
//		$data = explode("\n", $data);
//		if (count($data) > 1) $data[1] .= "\n";
//		$data = implode("\n", $data);

		$data = $passphrase."\n".$data;

		$command = '-d --passphrase-fd '.GPGSTDIN.' --yes --batch';
		$this->run_cmd($command, $unencrypted, $data);

		return $unencrypted;
	}
	
	public function decode_file($file, $outfile, $passphrase){

		global $GO_CONFIG;

		//go_debug($data);
		if(file_exists($outfile))
		{
			unlink($outfile);
		}	

		$command = '-o \''.$outfile.'\' --batch --yes --passphrase-fd '.GPGSTDIN.' -d \''.$file.'\' ';
		$this->run_cmd($command, $unencrypted, $passphrase."\n");
		
		if(!file_exists($outfile))
		{
			throw new Exception($this->error);
		}

		return true;
	}

	public function encode($data, $recipient, $user=null){
		$command = '--always-trust -a -e --yes --batch --armor';
		
		go_debug($data);

		if(!is_array($recipient))
		{
			$recipient = array($recipient);
		}
		$command .= ' -r '.escapeshellcmd(implode(' -r ', $recipient));

		if(!empty($user))
		{
			$command .= ' -u '.escapeshellcmd($user);
		}
		$this->run_cmd($command, $encrypted, $data);

		if(preg_match('/-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----/s',$encrypted))
		{
			return str_replace("\r", '', $encrypted);
		}else
		{
			throw new Exception($this->error);
		}
	}
	
	public function encode_file($file, $recipient, $user=null){
		
		if(file_exists($file.'.gpg'))
		{
			unlink($file.'.gpg');
		}
		
		$command = '--always-trust -e --yes --batch';
		
		if(!is_array($recipient))
		{
			$recipient = array($recipient);
		}
		$command .= ' -r '.escapeshellcmd(implode(' -r ', $recipient));

		if(!empty($user))
		{
			$command .= ' -u '.escapeshellcmd($user);
		}
		
		$command .= ' '.escapeshellarg($file);
		$this->run_cmd($command, $encrypted);


		if(!file_exists($file.'.gpg'))
		{
			throw new Exception($this->error);
		}
		return $file.'.gpg';
	}

	public function export($fingerprint)
	{
		$this->run_cmd('--armor --export '.escapeshellarg($fingerprint), $key);

		return $key;
	}

	public function sign_key($private_fpr, $public_fpr,$passphrase)
	{
		$cmd = '--default-key '.$private_fpr.' --sign-key '.$public_fpr;
		$this->run_cmd($cmd, $output, $errorcode,null,$passphrase);
	}

	function import($data)
	{
		$this->run_cmd('--armor --status-fd '.GPGSTDOUT.' --import', $output, $data);
		//go_debug($this->error);
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

	public function list_private_keys(){

		$this->run_cmd('-K --fingerprint', $output);

		return $this->parse_keys_output($output);
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

		global $GO_CONFIG;

		$data='';
		$data.="Key-Type: DSA\n";
		$data.="Key-Length: 1024\n";
		$data.="Subkey-Type: ELG-E\n";
		$data.="Subkey-Length: " . $keylength . "\n";
		$data.="Name-Real: " . $name . "\n";
		$data.="Name-Comment: " . $comment . "\n";
		$data.="Name-Email: " . $email . "\n";
		$data.="Expire-Date: ". $expiredate ."\n";
		$data.="Passphrase: " . $passphrase . "\n";
		$data.="%commit\n";
		$data.="%echo done with success\n";		

		$tmpfile = $GO_CONFIG->tmpdir.md5($email).'-gen-key.batch';

		file_put_contents($tmpfile, $data);

		$complete_cmd = 'cat '.$tmpfile.' | '.$this->gpg.' --homedir '.$this->home.' --status-fd 1 --gen-key --batch --armor  2>&1 &';
		proc_close(proc_open ($complete_cmd, array(), $foo));
		return true;

		/*
		// initialize the output
		$contents = '';

		// execute the GPG command
		if ( $this->run_cmd('--batch --status-fd 1 --gen-key',
			$contents, $data) ) {
			$matches = false;

			go_debug('Key gen command finished');

			if ( preg_match('/\[GNUPG:\]\sKEY_CREATED\s(\w+)\s(\w+)/', $contents, $matches) )
				return $matches[2];
			else
				return true;
		} else
		{
			go_debug('Key gen failed');
			return false;
		}*/

		//return empty($errorcode);
	}

	

	private function run_cmd($cmd, &$output=null, $input=null)
	{
		global $GO_CONFIG;

		$this->error = '';

		//$complete_cmd = $this->gpg.' --no-use-agent --display-charset utf-8 --utf8-strings --no-tty';
		//$complete_cmd = $this->gpg.' --display-charset utf-8 --utf8-strings';

		$complete_cmd = $this->gpg.' --homedir '.$this->home;
		//$complete_cmd = $this->gpg;

		$complete_cmd .= ' '.$cmd;

		go_debug('CMD: '.$complete_cmd);

		putenv("LANG=en_US.UTF-8");
		
		$p = proc_open($complete_cmd,$this->fd, $this->pipes);

		if(is_resource($p)){
			// writes the input
			if (!empty($input)) fwrite($this->pipes[GPGSTDIN], $input);
			fclose($this->pipes[GPGSTDIN]);

			// reads the output
			while (!feof($this->pipes[GPGSTDOUT])) {
				$data = fread($this->pipes[GPGSTDOUT], 1024);
				if (strlen($data) == 0) break;
				$output .= $data;
			}
			fclose($this->pipes[GPGSTDOUT]);

			// reads the error message
			$result = '';
			while (!feof($this->pipes[GPGSTDERR])) {
				$data = fread($this->pipes[GPGSTDERR], 1024);
				if (strlen($data) == 0) break;
				$result .= $data;
			}
			fclose($this->pipes[GPGSTDERR]);

			// close the process
			$status = proc_close($p);

			// returns the contents
			$this->error = $result;

			go_debug($output);

			go_debug($result);

			return ($status == 0);
		} else {
			$this->error = 'Unable to fork the command';
			return false;
		}

		return $ret;
	}
}