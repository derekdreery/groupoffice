<?php

/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id AutomaticInvoice.php 2012-09-06 14:29:45 mdhart $
 * @author Michael de Hart <mdhart@intermesh.nl> 
 * @package GO.servermanager.models
 */
/**
 * This objec tis responsible for creating invoices in the billing module based
 * on specifications in the servermanagers Installations
 *
 * @package GO.servermanager.models
 * @copyright Copyright Intermesh
 * @version $Id AutomaticInvoice.php 2012-09-06 14:29:45 mdhart $ 
 * @author Michael de Hart <mdhart@intermesh.nl> 
 * 
 * @property integer $id
 * @property boolean $enable_invoicing
 * @property double $discount_price
 * @property string $discount_description
 * @property double $discount_percentage
 * @property integer $invoice_timespan amount of months to pass before next invoice
 * @property integer $next_invoice_time unixtimestamp when next invoice needs to be created
 * @property integer $trial_days amount of days how long the customer can try out new users or modules
 * @property string $customer_name invoicing name
 * @property string $customer_address invoicing address
 * @property string $customer_address_no customers housenumber
 * @property string $customer_zip invoiceing zip
 * @property string $customer_state invoicing state
 * @property string $customer_country invoicing country
 * @property string $customer_vat invoice VAT
 * @property string $customer_city The customers city
 * @property integer $installation_id foreingkey of installation
 */
class GO_ServerManager_Model_AutomaticInvoice extends GO_Base_Db_ActiveRecord{
	
	const BILLING_HOST = 'http://trunk.loc/'; //where do we post our invoiceing data to
	const BILLING_USERNAME = 'admin';
	const BILLING_PASSWORD = 'admin';
	const BILLING_BOOK_ID = 2; //default order book id = 2
	
	public function tableName()
	{
		return 'sm_automatic_invoices';
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function init()
	{
		$this->columns['next_invoice_time']['gotype'] = 'unixtimestamp';
	}
	
	public function beforeSave()
	{
		if($this->isNew) //set next_invoice time of an new object to end of trial period
			$this->next_invoice_time = strtotime('today') + $this->getTrailTimeInSeconds();
		return true;
	}
	
	public function relations()
	{
		return array(
				'installation'=>array('type'=>self::BELONGS_TO, 'model'=>'GO_Servermanager_Model_Installation', 'field'=>'installation_id'),
		);
	}
	
	/**
	 * See if there is login information to connect to a groupoffice server where the
	 * Billing module is installed and see if remote invoicing is possible
	 * @return boolean true is remote invoicing if possible on the provided information 
	 */
	public static function canConnect()
	{
		$host = self::BILLING_HOST; // GO::config()->get_setting('servermanager_invoice_host');
		$username = self::BILLING_USERNAME; //GO::config()->get_setting('servermanager_invoice_username');
		$password = self::BILLING_PASSWORD; //GO::config()->get_setting('servermanager_invoice_password');
		if(!isset($host) || !isset($username) || !isset($password))
			return false;
		
		//connection with login
		$c = new GO_Base_Util_HttpClient();
		$c->groupofficeLogin($host, $username, $password);
		$response = $c->request($host.'?r=billing/order/remoteAutoInvoice', array(
				'data'=>json_encode(array('test'=>true))
		));
		
		$result = json_decode($response,true);
		return $result['success'];
	}
	
	/**
	 * get the cost for the amount of users in the installation based in staffel prices
	 * @return double price for amount of users
	 */
	protected function _getUserPrice()
	{
		$params = GO_Base_Db_FindParams::newInstance()->order('max_users','ASC');
		$staffelprices = GO_ServerManager_Model_UserPrice::model()->find($params);
		$uprice = 0;
		foreach($staffelprices as $price)
		{
			if($price->max_users <= $this->installation->currentusage->count_users)
				$uprice = $price->price_per_month;
			else
				break;
		}
		return $uprice;
	}
	
	/**
	 * This method is responsabgle for creating an new invoice in the billin module
	 * We send JSONdata to antoher server using CURL
	 * @return boolean true if an invoice was created successfull
	 */
	public function createOrderData()
	{
			$userCountPrice = $this->_getUserPrice();

			//create jsondata to send
			$order = array();
			$order['book_id'] = self::BILLING_BOOK_ID;
			//$order['btime'] = time(); //behaves strange in billing
			$order['customer_address'] = $this->customer_address;
			$order['customer_address_no'] = $this->customer_address_no;
			$order['customer_city'] = $this->customer_city;
			$order['customer_contact_name'] = $this->customer_name;
			$order['customer_contact_to'] = $this->customer_name;
			$order['customer_country'] = $this->customer_country;
			$order['customer_email'] = $this->installation->admin_email;
			$order['customer_vat_no'] = $this->customer_vat;
			$order['customer_state'] = $this->customer_state;
			$order['customer_zip'] = $this->customer_zip;
			$order['customer_name'] = $this->customer_name;
			$order['reference'] = 'GroupOffice hosted server';
			$order['items'] = array();
			$order['items'][] = array('description'=>'Hosted Groupoffice ('.$this->installation->currentusage->count_users.' users)', 'unit_price'=>$userCountPrice, 'amount'=>$this->invoice_timespan);
			foreach($this->installation->modules as $module)
			{
				if($module->name == 'billing') //TODO: Don't hardcode, look into table for prices
					$order['items'][] = array('description'=>'Billing module', 'unit_price'=>20, 'amount'=>$this->invoice_timespan);
			}
			if(!empty($this->discount_price) && $this->discount_price > 0) //add discount to order
				$order['items'][] = array('description'=>$this->discount_description, 'unit_price'=>0-$this->discount_price, 'amount'=>$this->invoice_timespan);

			return $order;
	}
	
	/**
	 * This will post a new order to a billing module
	 * @return boolean true when every went well
	 * @throws Exception when we cant connect to billing or when we cant save new invoice time
	 */
	public function sendOrder()
	{
		//if we can connect to the billing module
		if(self::canConnect())
		{
			$orderData = $this->createOrderData();

		  //send the data to billing module using curl
			$c = new GO_Base_Util_HttpClient();
			$c->groupofficeLogin(self::BILLING_HOST, self::BILLING_USERNAME, self::BILLING_PASSWORD);
			$response = $c->request(self::BILLING_HOST.'?r=billing/order/remoteAutoInvoice', array(
					'data'=>json_encode($orderData)
			));
			//return the response status curl returns (true if invoice was created)
			$result = json_decode($response,true);
			var_dump($result);
			if($result['success'])
			{
				$this->next_invoice_time = $this->calcNextInvoiceTime();
				if(!$this->save())
					throw new Exception('Could not save last invoice time');
				return true;
			}
			else
				return false;
		} else
			throw new Exception('Could not connect to the billing host');
	}
	
	/**
	 * send a partial invoice for new users/modules when trial periode is over 
	 */
	public function sendPartInvoice()
	{
		//TODO
	}
	
	/**
	 * Add the invoice timespan to the next_invoice_time
	 * @return int unixtimestamp with next_invoice_time
	 */
	protected function calcNextInvoiceTime()
	{
		return GO_Base_Util_Date::date_add($this->next_invoice_time, 0, $this->invoice_timespan);
		//$timestring = "+".$this->invoice_timespan." month";
		//return strtotime($timestring,$this->next_invoice_time);
	}
	
	public function getTrailTimeInSeconds()
	{
		//days * hours * minutes * seconds
		return ($this->installation->trial_days * 24 * 60 * 60);
	}
	
	/**
	 * A new order should be created when:
	 * - next_invoice_time has been passed
	 */
	public function shouldCreateOrder()
	{
		return time() >= $this->next_invoice_time;
	}
}
?>