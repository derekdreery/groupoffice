<?php
class updateclient extends db{

	var $remote_sid;
	var $remote_host='intermesh.group-office.com';
	var $remote_url = 'https://intermesh.group-office.com/groupoffice/modules/updateserver/';
	var $remote_path='/groupoffice/modules/updateserver/';
	var $remote_port=443;
	var $username;
	var $password;
	var $host;
	var $status;
	var $packages;

	function updateclient($host='',$username='', $password='')
	{
		global $GO_CONFIG;
		$this->db();

		$this->username=$username;
		$this->password=$password;
		$this->host=$host;
		
		
	}

	function create_cred_node(&$doc)
	{
		$credNode = $doc->createElement('cred');

		$usernameNode = $doc->createElement('username');
		$usernameNode = $credNode->appendChild($usernameNode);
		$value = $doc->createTextNode($this->username);
		$value = $usernameNode->appendChild($value);

		$passwordNode = $doc->createElement('password');
		$passwordNode = $credNode->appendChild($passwordNode);
		$value = $doc->createTextNode($this->password);
		$value = $passwordNode->appendChild($value);

		return $credNode;
	}

	function format_request($data)
	{
		return "POST ".$this->remote_path."check.php HTTP/1.1\r\n".
		"Host: ".$this->remote_host."\r\n".
		"User-Agent: Group-Office Update Client\r\n".
		"Content-Type: text/xml; charset=UTF-8\r\n".
		"Content-Length: ".strlen($data)."\r\n".
		"Connection: close\r\n\r\n".$data;
	}

	function get_xml_from_reponse($response)
	{
		if(!$xmlstart = strpos($response, '<?xml'))
		{
			return false;
		}
		return substr($response, $xmlstart);
	}

	function send_request($data)
	{
		$response='';

		if(!isset($mysocket)){
			$mysocket = fsockopen('ssl://'.$this->remote_host, $this->remote_port, $errno, $errstr, "10");
		}
		if(!isset($mysocket)){
			return false;
		}

		fputs($mysocket,$this->format_request($data));
		while(!feof($mysocket)) {
			$response .= fgets($mysocket,1024);
		}
		fclose($mysocket);

		return $response;
	}

	function add_package($package)
	{
		$package['id']=$this->nextid('uc_packages');
		if($this->insert_row('uc_packages', $package))
		{
			return $package['id'];
		}
		return false;
	}
	function update_package($package)
	{
		return $this->update_row('uc_packages', 'id', $package);
	}

	function delete_package($package_id)
	{
		return $this->query("DELETE FROM uc_packages WHERE id=$package_id");
	}

	function get_package($package_id)
	{
		$this->query("SELECT * FROM uc_packages WHERE id=$package_id");
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function get_package_by_name($name)
	{
		$this->query("SELECT * FROM uc_packages WHERE package_name='$name'");
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function check()
	{
		// create a new XML document
		$doc = new DomDocument('1.0');

		// create root node
		$root = $doc->createElement('request');
		$root = $doc->appendChild($root);

		$credNode = $root->appendChild($this->create_cred_node(&$doc));

		$hostNode = $doc->createElement('host');
		$hostNode = $root->appendChild($hostNode);
		$value = $doc->createTextNode($this->host);
		$value = $hostNode->appendChild($value);

		$response = $this->send_request($doc->saveXML());

		$doc = new DOMDocument('1.0');
		$xml = $this->get_xml_from_reponse($response);
		if(!$xml || !$doc->loadXML($xml))
		{
			return false;
		}else {

			$this->packages=array();

			$responseNodes = $doc->getElementsByTagname('response');
			$responseNode = $responseNodes->item(0);

			for($i=0;$i<$responseNode->childNodes->length;$i++)
			{
				switch($responseNode->childNodes->item($i)->nodeName)
				{
					case 'sid':
						$this->remote_sid=$responseNode->childNodes->item($i)->nodeValue;
						break;

					case 'status':
						$this->status=$responseNode->childNodes->item($i)->nodeValue;
						break;

					default:
						$package=array();
						$licenseNode = $responseNode->childNodes->item($i);
						$license_id = $licenseNode->getAttribute('id');

						$packageNodes = $licenseNode->childNodes;
						for($n=0;$n<$packageNodes->length;$n++)
						{
							$packageNode = $packageNodes->item($n);

							for($a=0;$a<$packageNode->childNodes->length;$a++)
							{
								$package[$packageNode->childNodes->item($a)->nodeName]=$packageNode->childNodes->item($a)->nodeValue;
							}
							$package['license_id']=$license_id;

							$local_package = $this->get_package_by_name(addslashes($package['package_name']));
							if(!$local_package)
							{
								$local_package['name']=addslashes($package['name']);
								$local_package['package_name']=addslashes($package['package_name']);
								$local_package['date']=0;
								$local_package['version']='';
								$local_package['id']=$this->add_package($local_package);
							}
							$package['local_package_id']=$local_package['id'];

							$package['local_date']=$local_package['date'];
													
							$this->packages[]=$package;
							
						}
					break;
				}
			}
			return true;
		}		
	}
}