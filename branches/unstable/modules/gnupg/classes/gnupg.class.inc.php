<?php
class gnupg{

	var $home = '';
	var $pgp="/usr/bin/gpg";
	var $error = '';

	public function __construct($home=null)
	{
		if(!isset($home))
		{
			//$home = '/home/mschering';
			global $GO_CONFIG;
			$home = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'];
		}
			
		$this->set_home($home);
	}

	public function set_home($home)
	{
		$this->home = $home;
		putenv("HOME=".$home);
	}

	public function replace_encoded($data, $passphrase)
	{
		if(strpos($data,'-----BEGIN PGP MESSAGE-----')!==false)
		{
			preg_match('/-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----/', $data, $matches);

			$encrypted = preg_replace(
							"'<br[^>]*>[\s]*'i",
							"\n",
			$matches[0]);

			
				
			$decrypted = $this->decode($encrypted, $passphrase);

			if(!$decrypted)
			{
				throw new Exception($this->error);
			}
			$data = str_replace($matches[0], $decrypted,$data);
		}
		return $data;
	}

	public function decode($data, $passphrase){

		global $GO_CONFIG;

		$this->error = '';

		$tmpfile = $GO_CONFIG->tmpdir.uniqid(time());
		$errorlog =  $GO_CONFIG->tmpdir.uniqid(time());
		file_put_contents($tmpfile, $data);

		$command = 'echo "'.$passphrase.'" | '.$this->pgp.' --no-tty --passphrase-fd 0 -d '.$tmpfile.' 2> '.$errorlog;
		$result = exec($command, $unencrypted, $errorcode);

		if(!empty($errorcode))
		{
			$this->error = file_get_contents($errorlog);
		}

		unlink($tmpfile);
		unlink($errorlog);

		return implode("\n", $unencrypted);
	}

	public function encode($data, $recipient, $user=null){
		$command = 'echo "'.$data.'" | '.$this->pgp.' -a  -e -r "'.$recipient.'"';
		if(!empty($user))
		{
			$command .= '-u "'.$user.'"';
		}
		$result = exec($command, $encrypted, $errorcode);
		$message = implode("\n", $encrypted);

		if(ereg("-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----",$message))
		{
			return $message;
		}else
		{
			return false;
		}
	}
	
	public function export($key)
	{
		$cmd = 'gpg --armor --export test@intermeshdev.nl > publictest.asc';
	}
}
?>