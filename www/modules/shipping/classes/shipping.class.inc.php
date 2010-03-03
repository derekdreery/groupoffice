<?php


$propack_reference='col_23';
$jcb_supplier_reference='col_67';
$customer_contact_name='col_68';
$customer_address='col_69';


/**
 * @copyright Intermesh 2006
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.91 $ $Date: 2006/12/05 11:37:30 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 *
 * link_type:
 *
 * 2002
 * 2003 container
 * 2004 shipment
 * 2005 invoice
 * 2006 purchase order
 */

class shipping extends db {

	function total_rows()
	{
		$this->query("SELECT FOUND_ROWS() as count;");
		$this->next_record();
		return $this->f('count');
	}


	function search_companies($user_id, $query, $field = 'name', $addressbook_id = 0, $start=0, $offset=0, $require_email=false, $sort_index='name', $sort_order='ASC', $type='destination') {
		global $GO_MODULES;

		$query = str_replace('*', '%', $query);

		$ab = new addressbook();


		if(isset($GO_MODULES->modules['custom_fields']))
		{
			$sql = "SELECT ab_companies.*, cf_3.* FROM ab_companies ".
 			"LEFT JOIN cf_3 ON cf_3.link_id=ab_companies.link_id ";
		}else {
			$sql = "SELECT ab_companies.* FROM ab_companies ";
		}

		if ($addressbook_id > 0) {
			$sql .= "WHERE ab_companies.addressbook_id='$addressbook_id' AND ";
		} else {

			$user_ab = $ab->get_user_addressbook_ids($user_id);
			if(count($user_ab) > 1)
			{
				$sql .= "WHERE ab_companies.addressbook_id IN (".implode(",",$user_ab).") AND ";
			}elseif(count($user_ab)==1)
			{
				$sql .= "WHERE ab_companies.addressbook_id=".$user_ab[0]." AND ";
			}else
			{
				return false;
			}
		}

		if ($field == '') {
			$fields_sql = "SHOW FIELDS FROM ab_companies";
			$this->query($fields_sql);
			while ($this->next_record()) {
				if (eregi('varchar', $this->f('Type'))) {
					if (isset ($first)) {
						$sql .= ' OR ';
					} else {
						$first = true;
						$sql .= '(';
					}
					$sql .= "ab_companies.".$this->f('Field')." LIKE '$query'";
				}
			}
			if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
			{
				$fields_sql = "SHOW FIELDS FROM cf_3";
				$this->query($fields_sql);
				while ($this->next_record()) {
					if (eregi('varchar', $this->f('Type')) || $this->f('Field')=='id') {
						if (isset ($first)) {
							$sql .= ' OR ';
						} else {
							$first = true;
							$sql .= '(';
						}
						$sql .= "cf_3.".$this->f('Field')." LIKE '$query'";
					}
				}

			}
			$sql .= ')';
		} else {
			$sql .= "$field LIKE '$query' ";
		}

		if($require_email)
		{
			$sql .= " AND ab_companies.email != '' ";
		}

		switch($type)
		{
			case 'supplier':
				$sql .= " AND col_76='1' ";
				break;

			case 'propack_supplier':
				$sql .= " AND col_77='1' ";
				break;


			case 'destination':
				$sql .= " AND col_75='1' ";
				break;
			default:
				$sql .= " AND col_72='1' ";
				break;
		}


		$sql .= "ORDER BY $sort_index $sort_order";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset > 0 )
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
			return $count;
		}else
		{
			return $count;
		}
	}



	function show_user_name($user_id)
	{
		global $GO_USERS;
		$user = $GO_USERS->get_user($user_id);

		return format_name($user['last_name'], $user['first_name'], $user['middle_name']);
	}


	function get_next_invoice_id($invoice)
	{
		if($invoice['status_id']==4)
		{
			$prefix='PC';
			$id = $this->nextid('soap_credit_note');

		}else		{
			$prefix='PS';
			$id = $this->nextid('soap_invoice');
		}
		$prefix.=date('Y');

		$lenght = 5;
		$order_id_lenght=strlen($id);

		for($i=$order_id_lenght;$i<=$lenght;$i++)
		{
			$id = '0'.$id;
		}

		return $prefix.$id;
	}

	function prefix($id, $prefix){
		$lenght = 5;
		$order_id_lenght=strlen($id);

		for($i=$order_id_lenght;$i<=$lenght;$i++)
		{
			$id = '0'.$id;
		}
		return $prefix.$id;
	}

	function get_invoice_total_for_cost_code($invoice_id, $cost_code_id)
	{
		$sql = "SELECT SUM(amount*price) FROM sh_charges WHERE invoice_id=$invoice_id AND cost_code_id=$cost_code_id";
		$this->query($sql);
		$this->next_record();
		return $this->f(0);
	}

	function get_purchase_invoice_total_for_purchase_code($purchase_invoice_id, $purchase_code_id)
	{
		$sql = "SELECT SUM(amount*price) FROM sh_pi_charges WHERE purchase_invoice_id=$purchase_invoice_id AND purchase_code_id=$purchase_code_id";
		$this->query($sql);
		$this->next_record();
		return $this->f(0);
	}

	function get_purchase_order_total_for_purchase_code($purchase_order_id, $purchase_code_id)
	{
		$sql = "SELECT SUM(amount*price) FROM sh_po_charges WHERE purchase_order_id=$purchase_order_id AND purchase_code_id=$purchase_code_id";
		$this->query($sql);
		$this->next_record();
		return $this->f(0);
	}



	function get_destinations($start, $end, $groups)
	{
		$sql = "SELECT DISTINCT j.destination
		FROM sh_jobs j
		INNER JOIN sh_packages p ON p.job_id = j.id
		INNER JOIN sh_containers c ON p.container_id = c.id
		INNER JOIN sh_shipments s ON c.shipment_id = s.id
		WHERE (s.ctime >= $start AND s.ctime <= $end)";
		if(count($groups)>0)
		{
			$sql .= " AND (";
			foreach($groups as $group)
			{
				$sql .= "j.group_id = ".$group." OR ";
			}
			$sql = substr($sql, 0, -4);
			$sql .= ")";
		}
		$this->query($sql);
		return $this->num_rows();
	}

	function get_invoiced_customers(){
		$sql = "SELECT DISTINCT customer_name FROM sh_invoices";
		return $this->query($sql);
	}


	function get_customers($start, $end, $groups)
	{
		$sql = "SELECT DISTINCT j.customer
		FROM sh_jobs j
		INNER JOIN sh_packages p ON p.job_id = j.id
		INNER JOIN sh_containers c ON p.container_id = c.id
		INNER JOIN sh_shipments s ON c.shipment_id = s.id
		WHERE (s.ctime >= $start AND s.ctime <= $end)";
		if(count($groups)>0)
		{
			$sql .= " AND (";
			foreach($groups as $group)
			{
				$sql .= "j.group_id = ".$group." OR ";
			}
			$sql = substr($sql, 0, -4);
			$sql .= ")";
		}
		$this->query($sql);
		return $this->num_rows();
	}

	function get_customers_to_be_invoiced($start, $end)
	{
		$customers = '';
		$sql = "SELECT DISTINCT j.customer
		FROM sh_jobs j
		INNER JOIN sh_packages p ON p.job_id = j.id
		INNER JOIN sh_containers c ON p.container_id = c.id
		INNER JOIN sh_shipments s ON c.shipment_id = s.id
		WHERE (s.ctime >= $start AND s.ctime <= $end)
		AND ((c.charge_load_fee = '1'
		AND c.invoice_id = 0)
		OR p.invoice_id = 0)";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_containers_to_be_invoiced($start, $end, $customer)
	{
		$containers = '';
		$sql = "SELECT DISTINCT c.*, s.ets, s.ctime AS shipment_created
		FROM sh_containers c
		INNER JOIN sh_shipments s ON c.shipment_id = s.id
		INNER JOIN sh_packages p ON p.container_id = c.id
		INNER JOIN sh_jobs j ON p.job_id = j.id
		WHERE (s.ctime >= $start AND s.ctime <= $end)
		AND ((c.charge_load_fee = '1'
		AND c.invoice_id = 0)
		OR p.invoice_id = 0)
		AND j.customer = '$customer'".
				"ORDER BY c.id ASC";
		$this->query($sql);
		/*while($this->next_record())
		 {
			$containers[] = $this->f('id').'%#'.$this->f('container_no').'%#'.$this->f('destination');
			}
			return $containers;*/
		return $this->num_rows();
	}

	function get_total_packages_cost($container_id, $customer)
	{
		$total_packages_cost = 0;
		$sql = "SELECT SUM(p.price)
		FROM sh_packages p
		INNER JOIN sh_containers c ON p.container_id = c.id
		INNER JOIN sh_jobs j ON p.job_id = j.id
		WHERE j.customer = '$customer'
		AND c.id = $container_id
		GROUP BY j.customer";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->f(0);
		}
		return false;
	}

	function get_suppliers($start, $end, $groups)
	{
		$sql = "SELECT DISTINCT j.supplier
		FROM sh_jobs j
		INNER JOIN sh_packages p ON p.job_id = j.id
		INNER JOIN sh_containers c ON p.container_id = c.id
		INNER JOIN sh_shipments s ON c.shipment_id = s.id
		WHERE (s.ctime >= $start AND s.ctime <= $end)";
		if(count($groups)>0)
		{
			$sql .= " AND (";
			foreach($groups as $group)
			{
				$sql .= "j.group_id = ".$group." OR ";
			}
			$sql = substr($sql, 0, -4);
			$sql .= ")";
		}
		$this->query($sql);
		return $this->num_rows();
	}


	function get_material_usage($start_time, $end_time, $selected_groups, $selected_customers,
	$selected_suppliers,$start=0, $offset=0)
	{
		$sql = "SELECT j.destination AS DESTINATION, ROUND(SUM(sp.weight),2) AS 'TOTAL WEIGHT kg', ROUND(SUM(softwood),2) AS 'SOFTWOOD kg' ".
			", ROUND(SUM(plywood),2) AS 'PLYWOOD kg', ".
			"ROUND(SUM(corrugated_paper),2) AS 'CORRUGATED PAPER kg', ".
			"ROUND(SUM(plastics),2) AS 'PLASTICS kg', ".
			"ROUND(SUM(metals),2) AS 'METALS kg' FROM sh_jobs j ".
			"INNER JOIN sh_packages p ON j.id=p.job_id ".
			"INNER JOIN sh_standard_packages sp ON sp.id=p.standard_package_id ".
			"INNER JOIN sh_containers c on c.id=p.container_id ".
			"INNER JOIN sh_shipments s ON s.id=c.shipment_id ".
			"WHERE s.ctime>$start_time AND s.ctime<$end_time";

		if(count($selected_groups)>0)
		{
			$sql .= " AND j.group_id IN (".implode(',',$selected_groups).")";
		}

		if(count($selected_customers)>0)
		{
			$sql .= " AND j.customer IN (\"".implode('","',$selected_customers)."\")";
		}

		if(count($selected_suppliers)>0)
		{
			$sql .= " AND j.supplier IN (\"".implode('","',$selected_suppliers)."\")";
		}

		$sql .= " GROUP BY destination ORDER BY destination ASC";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;

	}

	function get_material_usage_totals($start_time, $end_time, $selected_groups, $selected_customers,
	$selected_suppliers)
	{
		$sql = "SELECT ROUND(SUM(sp.weight),2) AS 'TOTAL WEIGHT kg', ROUND(SUM(softwood),2) AS 'SOFTWOOD kg' ".
			", ROUND(SUM(plywood),2) AS 'PLYWOOD kg', ".
			"ROUND(SUM(corrugated_paper),2) AS 'CORRUGATED PAPER kg', ".
			"ROUND(SUM(plastics),2) AS 'PLASTICS kg', ".
			"ROUND(SUM(metals),2) AS 'METALS kg' FROM sh_jobs j ".
			"INNER JOIN sh_packages p ON j.id=p.job_id ".
			"INNER JOIN sh_standard_packages sp ON sp.id=p.standard_package_id ".
			"INNER JOIN sh_containers c on c.id=p.container_id ".
			"INNER JOIN sh_shipments s ON s.id=c.shipment_id ".
			"WHERE s.ctime>$start_time AND s.ctime<$end_time";

		if(count($selected_groups)>0)
		{
			$sql .= " AND j.group_id IN (".implode(',',$selected_groups).")";
		}

		if(count($selected_customers)>0)
		{
			$sql .= " AND j.customer IN (\"".implode('","',$selected_customers)."\")";
		}

		if(count($selected_suppliers)>0)
		{
			$sql .= " AND j.supplier IN (\"".implode('","',$selected_suppliers)."\")";
		}

		$this->query($sql);
		$this->next_record(MYSQL_ASSOC);

		return $this->record;

	}


	/**
	 * Searches all packages
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function search_packages($selected_groups,
	$selected_destinations,
	$selected_customers,
	$selected_suppliers,
	$part_number,
	$start_time,
	$end_time,
	$start=0, $offset=0,
	$sortfield='id', $sortorder='ASC')
	{
		$sql = "SELECT DISTINCT sh_jobs.id AS job_id, sh_packages.*, ".
		"sh_jobs.destination, sh_jobs.supplier, sh_jobs.customer, sh_containers.container_no, ".
		"sh_containers.shipment_id, sh_containers.ctime, ".
		"sh_shipments.ets, sh_shipments.id AS shipment_id ";

		$sql .= "FROM sh_packages ".
		"INNER JOIN sh_jobs ON sh_jobs.id=sh_packages.job_id ".
		"INNER JOIN sh_containers ON sh_containers.id=sh_packages.container_id ".
		"INNER JOIN sh_shipments ON sh_containers.shipment_id=sh_shipments.id ";

		if(!empty($part_number))
		{
			$sql .= "INNER JOIN sh_package_items ON sh_package_items.package_id=sh_packages.id ".
			"INNER JOIN sh_job_items ON sh_package_items.job_item_id=sh_job_items.id ".
			"INNER JOIN sh_parts ON sh_parts.id=sh_job_items.part_id ";
		}

		/*if(count($selected_package_types))
		 {
			$sql .= "INNER JOIN sh_standard_packages ON sh_standard_packages.id=sh_packages.standard_package_id ".
			"INNER JOIN sh_package_types ON sh_standard_packages.package_type_id=sh_package_types.id ";
			}*/

		$sql .= "WHERE ";

		if(count($selected_groups)>0)
		{
			$sql .= "sh_packages.group_id IN (".implode(',',$selected_groups).") AND ";
		}

		if(count($selected_destinations)>0)
		{
			$sql .= "sh_jobs.destination IN (\"".implode('","',$selected_destinations)."\") AND ";
		}

		if(count($selected_customers)>0)
		{
			$sql .= "sh_jobs.customer IN (\"".implode('","',$selected_customers)."\") AND ";
		}

		if(count($selected_suppliers)>0)
		{
			$sql .= "sh_jobs.supplier IN (\"".implode('","',$selected_suppliers)."\") AND ";
		}

		/*	if(count($selected_package_types)>0)
		 {
			$sql .= "sh_package_types.id IN (".implode(',',$selected_package_types).") AND ";
			}*/

		if(!empty($part_number))
		{
			$sql .= "sh_parts.part_no LIKE '%$part_number%' AND ";

		}


		$sql .= "sh_shipments.ctime>$start_time AND sh_shipments.ctime<$end_time ".
		"ORDER BY $sortfield $sortorder";

		//echo $sql;

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}




	function get_sales_manifest($invoice_id)
	{
		$sql = "SELECT DISTINCT sh_jobs.id AS job_id, sh_jobs.jcb_po_no, sh_packages.*, ".
		"sh_jobs.destination, sh_jobs.supplier, sh_jobs.customer, sh_containers.container_no, ".
		"sh_containers.shipment_id, sh_containers.ctime, ".
		"sh_shipments.ets, sh_shipments.id AS shipment_id ".
		"FROM sh_packages ".
		"LEFT JOIN sh_jobs ON sh_jobs.id=sh_packages.job_id ".
		"LEFT JOIN sh_containers ON sh_containers.id=sh_packages.container_id ".
		"LEFT JOIN sh_shipments ON sh_containers.shipment_id=sh_shipments.id ".
		"WHERE sh_packages.invoice_id=$invoice_id ORDER BY container_id ASC, sh_jobs.supplier ASC , sh_packages.package_no";

		$this->query($sql);
		return $this->num_rows();
	}






	function get_container_weight($container_id)
	{
		$shipping = new shipping();
		$this->get_container_packages($container_id);
		$weight=0;
		while($this->next_record())
		{
			$weight += $shipping->get_package_weight($this->f('id'),true);
		}
		return $weight;
	}




	function get_package_weight($package_id, $return_manual_weight=true)
	{

		$package=$this->get_package($package_id);

		//always return manual weight if it's entered. Discussed in email at
		//24-07-2007
		if($return_manual_weight && $package['weight']>0)
		{
			return $package['weight'];
		}
		if($package['standard_package_id']>0)
		{
			$standard_package=$this->get_standard_package($package['standard_package_id']);
		}

		if(!isset($standard_package['weight']) || $standard_package['weight']==0)
		{
			if($return_manual_weight)
			{
				return $package['weight'];
			}else {
				return 0;
			}
		}

		//check for empty weight
		$sql = "SELECT * FROM sh_parts ".
		"INNER JOIN sh_job_items ON sh_parts.id=sh_job_items.part_id ".
		"INNER JOIN sh_package_items ON sh_job_items.id=sh_package_items.job_item_id ".
		"WHERE sh_parts.weight=0 AND sh_package_items.package_id=$package_id";
		$this->query($sql);
		if($this->next_record())
		{
			//$package = $this->get_package($package_id);
			if($return_manual_weight)
			{
				return $package['weight'];
			}else {
				return 0;
			}
		}else {
			return $this->calc_package_weight($package_id);
		}
	}
	function calc_package_weight($package_id)
	{

		$sql = "SELECT SUM(sh_package_items.amount*sh_parts.weight) FROM sh_package_items ".
		"INNER JOIN sh_job_items ON sh_job_items.id=sh_package_items.job_item_id ".
		"INNER JOIN sh_parts ON sh_parts.id=sh_job_items.part_id ".
		"WHERE sh_package_items.package_id=$package_id";

		$this->query($sql);
		$this->next_record();
		$sum = $this->f(0);

		$package=$this->get_package($package_id);
		if($package['standard_package_id']>0)
		{
			$standard_package=$this->get_standard_package($package['standard_package_id']);
			$sum += $standard_package['weight'];
		}
		return $sum;

	}


	function get_container_destination($container_id)
	{
		$sql = "SELECT DISTINCT destination FROM sh_jobs INNER JOIN sh_packages ON sh_jobs.id=sh_packages.job_id WHERE sh_packages.container_id=$container_id";
		$this->query($sql);
		$destination = '';
		while($this->next_record())
		{
			if(!isset($first))
			{
				$first=true;
			}else {
				$destination .= ',';
			}
			$destination .= $this->f('destination');
		}
		return $destination;
	}

	function get_invoice_destination($invoice_id)
	{
		$sql = "SELECT DISTINCT destination FROM sh_jobs ".
			"INNER JOIN sh_packages ON sh_jobs.id=sh_packages.job_id ".
			"INNER JOIN sh_containers ON sh_containers.id=sh_packages.container_id ".
			"INNER JOIN sh_charges ON sh_containers.id=sh_charges.container_id ".
			"WHERE sh_charges.invoice_id=$invoice_id";
		$this->query($sql);
		$destination = '';
		while($this->next_record())
		{
			if(!isset($first))
			{
				$first=true;
			}else {
				$destination .= ',';
			}
			$destination .= $this->f('destination');
		}
		return $destination;
	}



	function get_package_parts($package_id)
	{
		$sql = "SELECT DISTINCT SUM(sh_package_items.amount) AS amount, ".
		"part_no, sh_parts.name,sh_parts.weight,serial_no FROM sh_package_items ".
		"INNER JOIN sh_job_items ON sh_job_items.id=sh_package_items.job_item_id ".
		"INNER JOIN sh_parts ON sh_parts.id=sh_job_items.part_id ".
		"WHERE sh_package_items.package_id=$package_id GROUP BY sh_package_items.id";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_package_part_numbers($package_id)
	{
		$sql = "SELECT DISTINCT part_no, sh_parts.description FROM sh_package_items ".
		"INNER JOIN sh_job_items ON sh_job_items.id=sh_package_items.job_item_id ".
		"INNER JOIN sh_parts ON sh_parts.id=sh_job_items.part_id ".
		"WHERE sh_package_items.package_id=$package_id";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_container_parts($container_id)
	{
		$sql = "SELECT sh_parts.*, sh_packages.* FROM sh_parts ".
		"INNER JOIN sh_job_items ON sh_job_items.part_id=sh_parts.id ".
		"INNER JOIN sh_package_items ON sh_package_items.job_item_id=sh_job_items.id ".
		"INNER JOIN sh_packages ON sh_packages.id=sh_package_items.package_id ".
		"WHERE sh_packages.container_id=$container_id ORDER BY package_no ASC";

		$this->query($sql);
		return $this->num_rows();

	}

	function get_part_categories($parent_id)
	{
		$sql = "SELECT * FROM sh_part_categories ".
		"WHERE parent_id=$parent_id ORDER BY sort_order ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_part_category($part_category_id)
	{
		$sql = "SELECT * FROM sh_part_categories WHERE id=$part_category_id";

		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	function get_part_category_by_name($part_category_name)
	{
		$sql = "SELECT * FROM sh_part_categories WHERE name='$part_category_name'";

		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	function add_part_category($part_category)
	{
		$part_category['id'] = $this->nextid('sh_part_categories');
		if($this->insert_row('sh_part_categories', $part_category))
		{
			return $part_category['id'];
		}
		return false;
	}

	function update_part_category($part_category)
	{
		return $this->update_row('sh_part_categories', 'id', $part_category);
	}

	function delete_part_category($part_category_id)
	{
		$sh = new shipping();
		$this->get_parts($part_category_id);
		while($this->next_record())
		{
			$sh->delete_part($this->f('id'));
		}
		return $this->query("DELETE FROM sh_part_categories WHERE id=$part_category_id");
	}

	function get_parts($category_id=-1, $start=0, $offset=0, $sort_index='name', $sort_order='ASC')
	{
		$sql = "SELECT sh_parts.*, sh_part_types.name AS part_type FROM sh_parts ".
		"LEFT JOIN sh_part_types ON sh_part_types.id=sh_parts.part_type_id ";


		if($category_id>-1)
		{
			$sql .= "WHERE category_id=$category_id ";
		}
		$sql .= "ORDER BY $sort_index $sort_order";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .=" LIMIT $start,$offset";
			$this->query($sql);
		}

		return $count;
	}

	function search_parts($keyword, $groups=array())
	{
		$sql = "SELECT sh_parts.*, sh_part_types.name AS part_type FROM sh_parts ".
		"LEFT JOIN sh_part_types ON sh_part_types.id=sh_parts.part_type_id ";
		if($group_count = count($groups))
		{

			$sql .= "INNER JOIN sh_parts_groups ON sh_parts_groups.part_id=sh_parts.id ";
		}

		$sql .= "WHERE (sh_parts.name LIKE '%$keyword%' OR part_no LIKE '%$keyword%')";

		if($group_count = count($groups))
		{
			if($group_count>1)
			{
				$sql .= " AND sh_parts_groups.group_id IN (".implode(',', $groups).")";
			}else
			{
				$sql .= " AND sh_parts_groups.group_id=".$groups[0];
			}
		}

		$sql .= " ORDER BY sh_parts.name ASC LIMIT 0,20";

		$this->query($sql);

		return $this->num_rows();
	}

	function get_part_types()
	{
		$sql = "SELECT * FROM sh_part_types";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_package_types()
	{
		$sql = "SELECT * FROM sh_package_types";
		$this->query($sql);
		return $this->num_rows();
	}



	function get_part($part_id)
	{
		$sql = "SELECT * FROM sh_parts WHERE id=$part_id";



		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	function get_part_by_part_no($part_no)
	{
		$sql = "SELECT * FROM sh_parts WHERE part_no='$part_no'";

		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	function add_part($part)
	{
		$part['id'] = $this->nextid('sh_parts');
		if($this->insert_row('sh_parts', $part))
		{
			return $part['id'];
		}
		return false;
	}



	function update_part($part)
	{
		return $this->update_row('sh_parts', 'id', $part);
	}

	function delete_part($part_id)
	{
		global $GO_CONFIG;

		if(file_exists($GO_CONFIG->local_path.'shipping/parts/'.$part_id.'.jpg'))
		{
			unlink($GO_CONFIG->local_url.'shipping/parts/'.$part_id.'.jpg');
		}
		return $this->query("DELETE FROM sh_parts WHERE id=$part_id");
	}



	function format_package_number($job_id, $package_id)
	{
		if(strlen($package_id)==1)
		{
			$package_id = '0'.$package_id;
		}
		return $job_id.'/'.$package_id;
	}





	function increase_job_item_packed($job_item_id, $amount)
	{
		$sql = "UPDATE sh_job_items SET packed=packed+$amount WHERE id=$job_item_id";
		return $this->query($sql);
	}

	function increase_job_items($job_id, $amount)
	{
		$sql = "UPDATE sh_jobs SET items=items+$amount WHERE id=$job_id";
		return $this->query($sql);
	}

	function increase_job_packed($job_id, $amount)
	{
		$sql = "UPDATE sh_jobs SET packed=packed+$amount WHERE id=$job_id";
		return $this->query($sql);
	}

	function sync_items($job_id)
	{

		$job_packed=0;
		$job_amount=0;
		$sh = new shipping();

		$this->get_job_items($job_id);
		while($this->next_record())
		{
			$job_amount+=$this->f('amount');

			$sql="SELECT SUM(amount) FROM sh_package_items WHERE job_item_id=".$this->f('id');
			$sh->query($sql);
			$packed=0;
			if($sh->next_record())
			{
				$packed=$sh->f(0);
			}

			$job_packed+=$packed;
			if($packed!=$this->f('packed'))
			{
				$ji['id']=$this->f('id');
				$ji['packed']=$packed;
				$sh->update_job_item($ji);
			}
		}



		$sql = "UPDATE sh_jobs SET packed=".$job_packed.", items=".$job_amount." WHERE id=$job_id";
		$this->query($sql);

	}



	function get_unpacked_job_items($job_id)
	{
		$sql = "SELECT sh_job_items.*,sh_parts.part_no, ".
		"sh_job_items.serial_no FROM sh_job_items ".
		"INNER JOIN sh_parts ON sh_parts.id=sh_job_items.part_id ".
		"WHERE amount>packed AND job_id=$job_id";
		$this->query($sql);
		return $this->num_rows();
	}


	function get_package_items($package_id, $start=0, $offset=0)
	{
		$sql = "SELECT sh_package_items.*, sh_job_items.description,sh_parts.part_no, ".
		"sh_job_items.serial_no, sh_parts.weight FROM sh_package_items INNER JOIN sh_job_items ON ".
		"sh_job_items.id=sh_package_items.job_item_id ".
		"INNER JOIN sh_parts ON sh_parts.id=sh_job_items.part_id ".
		"WHERE package_id='$package_id' ".
		"ORDER BY sh_job_items.id ASC";
		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}

	function add_package_item($item)
	{
		$item['id'] = $this->nextid('sh_package_items');
		if($this->insert_row('sh_package_items', $item))
		{
			return $item['id'];
		}
		return false;
	}

	function get_packages_by_job_item_id($job_item_id)
	{
		$sql = "select * from sh_package_items where job_item_id='$job_item_id'";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_job_item_package_numbers($job_item_id)
	{
		$sql = "select distinct package_id, package_no from sh_package_items inner join sh_packages on sh_package_items.package_id=sh_packages.id where job_item_id='$job_item_id' order by sh_package_items.id ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function delete_package_item($item_id)
	{
		return $this->query("DELETE FROM sh_package_items WHERE id='$item_id'");
	}

	function search_products($query)
	{
		global $GO_MODULES, $GO_SECURITY;

		$fields[]='name';
		//$fields[]='description';
		$fields[]='part_no';

		/*require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
		 $cf = new custom_fields();
		 $cf2 = new custom_fields();



		 $cf->get_authorized_categories(101,$GO_SECURITY->user_id);
		 while($cf->next_record())
		 {
		 $cf2->get_fields($cf->f('id'));

		 while($cf2->next_record())
		 {
		 $fields[]='cf_101.col_'.$cf2->f('id');
		 }
		 }*/



		$sql = "SELECT * FROM sh_parts ".
		"WHERE (";

		$sql .= implode(' LIKE \''.$query.'\' OR ', $fields);

		$sql .= " LIKE '$query')";

		$this->query($sql);
		return $this->num_rows();
	}




	function add_job_item($item)
	{
		$item['id'] = $this->nextid('sh_job_items');
		if($this->insert_row('sh_job_items', $item))
		{
			return $item['id'];
		}
		return false;
	}

	function update_job_item($item)
	{
		return $this->update_row('sh_job_items', 'id', $item);
	}


	function get_job_item($item_id)
	{
		$this->query("SELECT * FROM sh_job_items WHERE id='$item_id'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	function delete_job_item($item_id)
	{
		return $this->query("DELETE FROM sh_job_items WHERE id='$item_id'");
	}


	function get_job_items($job_id, $start=0, $offset=0)
	{
		$sql = "SELECT sh_job_items.*, sh_parts.part_no, sh_parts.weight FROM sh_job_items INNER JOIN sh_parts ON sh_job_items.part_id=sh_parts.id WHERE job_id='$job_id' ".
		"ORDER BY (amount/packed) ASC, sh_job_items.id DESC";
		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}





	/**
	 * Add a job
	 *
	 * @param Array $job Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_job($job)
	{
		$job['id']=$this->nextid('sh_jobs');
		$job['ctime']=$job['mtime']=get_gmt_time();
		$job['link_id']=$GLOBALS['GO_LINKS']->get_link_id();
		if($this->insert_row('sh_jobs', $job))
		{
			return $job['id'];
		}
		return false;
	}

	/**
	 * Update a job
	 *
	 * @param Array $job Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_job($job)
	{
		$job['mtime']=get_gmt_time();
		return $this->update_row('sh_jobs', 'id', $job);
	}


	/**
	 * Delete a job
	 *
	 * @param Int $job_id ID of the job
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_job($job_id)
	{
		$this->query("DELETE FROM sh_job_items WHERE job_id='$job_id'");
		return $this->query("DELETE FROM sh_jobs WHERE id=$job_id");
	}


	/**
	 * Gets a job record
	 *
	 * @param Int $job_id ID of the job
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_job($job_id)
	{
		$this->query("SELECT * FROM sh_jobs WHERE id=$job_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a job record by the name field
	 *
	 * @param String $name Name of the job
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_job_by_name($name)
	{
		$this->query("SELECT * FROM sh_jobs WHERE job_name='$name'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all jobs
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_jobs($start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	{
		$sql = "SELECT * FROM sh_jobs ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}

	/**
	 * Gets all active jobs
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_active_jobs($start=0, $offset=0, $sortfield='id', $sortorder='ASC', $groups=array(), $completed, $archived)
	{
		$sql = "SELECT * FROM sh_jobs WHERE (packed<items OR items=0)";

		if($group_count = count($groups))
		{
			if($group_count>1)
			{
				$sql .= " AND group_id IN (".implode(',', $groups).")";
			}else
			{
				$sql .= " AND group_id=".$groups[0];
			}
		}

		if(!$completed)
		{
			$sql .= " AND completed='0'";
		}
		if(!$archived)
		{
			$sql .= " AND archived='0'";
		}

		$sql .= " ORDER BY $sortfield $sortorder";

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";

			$sql = substr_replace($sql, 'SELECT SQL_CALC_FOUND_ROWS',0,6);
			$this->query($sql);

			$db = new db();
			$db->query("SELECT FOUND_ROWS() as count");
			$db->next_record();
			$count = $db->f('count');
		}else
		{
			$this->query($sql);
			$count = $this->num_rows();

		}
		return $count;
	}


	/**
	 * Gets all jobs that have packages
	 *
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_jobs_with_packages($start=0, $offset=0, $sortfield='id', $sortorder='ASC', $groups=array(), $completed=false, $archived=false)
	{
		$sql = "SELECT * FROM sh_jobs WHERE packed>0";


		if($group_count = count($groups))
		{
			if($group_count>1)
			{
				$sql .= " AND group_id IN (".implode(',', $groups).")";
			}else
			{
				$sql .= " AND group_id=".$groups[0];
			}
		}

		if(!$completed)
		{
			$sql .= " AND completed='0'";
		}
		if(!$archived)
		{
			$sql .= " AND archived='0'";
		}

		$sql .= " ORDER BY $sortfield $sortorder";

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";

			$sql = substr_replace($sql, 'SELECT SQL_CALC_FOUND_ROWS',0,6);
			$this->query($sql);

			$db = new db();
			$db->query("SELECT FOUND_ROWS() as count");
			$db->next_record();
			$count = $db->f('count');
		}else
		{
			$this->query($sql);
			$count = $this->num_rows();

		}
		return $count;
	}

	/**
	 * Add a package
	 *
	 * @param Array $package Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_package($package)
	{
		$package['id']=$this->nextid('sh_packages');
		$package['link_id']=$GLOBALS['GO_LINKS']->get_link_id();
		$package['ctime']=$package['mtime']=get_gmt_time();


		$job_number = $package['job_id'];
		while(strlen($job_number)<3)
		{
			$job_number = '0'.$job_number;
		}

		$sql = "SELECT * FROM sh_packages WHERE job_id='".$package['job_id']."'";
		$this->query($sql);

		$package_number=$this->num_rows()+1;
		while(strlen($package_number)<3)
		{
			$package_number = '0'.$package_number;
		}

		$package['package_no']=$job_number.'/'.$package_number;



		if($this->insert_row('sh_packages', $package))
		{
			return $package['id'];
		}
		return false;
	}

	/**
	 * Update a package
	 *
	 * @param Array $package Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_package($package)
	{

		$package['mtime']=get_gmt_time();

		return $this->update_row('sh_packages', 'id', $package);
	}


	/**
	 * Delete a package
	 *
	 * @param Int $package_id ID of the package
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_package($package_id)
	{
		$this->query("DELETE FROM sh_package_items WHERE package_id='$package_id'");
		return $this->query("DELETE FROM sh_packages WHERE id=$package_id");
	}


	/**
	 * Gets a package record
	 *
	 * @param Int $package_id ID of the package
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_package($package_id)
	{
		$this->query("SELECT * FROM sh_packages WHERE id=$package_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a package record by the name field
	 *
	 * @param String $name Name of the package
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_package_by_name($name)
	{
		$this->query("SELECT * FROM sh_packages WHERE package_name='$name'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all packages
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_packages($start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	{
		$sql = "SELECT sh_packages.*, sh_jobs.destination, sh_containers.container_no, sh_containers.shipment_id FROM sh_packages ".
		"INNER JOIN sh_jobs ON sh_jobs.id=sh_packages.job_id ".
		"LEFT JOIN sh_containers ON sh_containers.id=sh_packages.container_id ".
		"ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}

	/**
	 * Gets all packages
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_job_packages($job_id=0, $start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	{
		$sql = "SELECT sh_packages.*, sh_jobs.destination, sh_containers.container_no, sh_containers.shipment_id FROM sh_packages ".
		"INNER JOIN sh_jobs ON sh_jobs.id=sh_packages.job_id ".
		"LEFT JOIN sh_containers ON sh_containers.id=sh_packages.container_id ";

		if ($job_id>0) {
			$sql .= "WHERE job_id=$job_id ";
		}

		$sql .= "ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_job_container($job_id)
	{
		$sql = "SELECT sh_packages.*, sh_jobs.destination, sh_containers.container_no, sh_containers.shipment_id FROM sh_packages ".
		"INNER JOIN sh_jobs ON sh_jobs.id=sh_packages.job_id ".
		"INNER JOIN sh_containers ON sh_containers.id=sh_packages.container_id ".
		"WHERE job_id=$job_id";

		$this->query($sql);
		$count = $this->num_rows();

		return $count;
	}

	function get_uncontainerised_destinations($group_id)
	{
		$sql = "SELECT DISTINCT sh_jobs.destination FROM sh_packages INNER JOIN sh_jobs ON sh_packages.job_id=sh_jobs.id WHERE container_id=0";
		$this->query($sql);
		return $this->num_rows();
	}

	/**
	 * Gets all packages related to a container
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_container_packages($container_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC', $group_id=0, $customer='', $destination='')
	{
		$sql = "SELECT sh_packages.*, c.container_no, sh_jobs.destination, sh_jobs.supplier, sh_jobs.customer, sh_jobs.jcb_po_no ".
			"FROM sh_packages INNER JOIN sh_jobs ON sh_packages.job_id=sh_jobs.id ".
			"LEFT JOIN sh_containers c ON c.id=sh_packages.container_id ".
			"WHERE container_id='$container_id'";

		if($group_id>0)
		{
			$sql .= " AND sh_packages.group_id=$group_id";
		}

		if(!empty($customer))
		{
			$sql .= " AND sh_jobs.customer='$customer'";
		}

		if(!empty($destination))
		{
			$sql .= " AND sh_jobs.destination='$destination'";
		}
		$sql .= " ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}

		go_log(LOG_DEBUG, $sql);

		return $count;
	}

	/*function get_packages_to_be_contained($group_id=0, $start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	 {
		$sql = "SELECT sh_packages.*, sh_jobs.destination, sh_jobs.supplier, sh_jobs.customer ".
		"FROM sh_packages INNER JOIN sh_jobs ON sh_packages.job_id=sh_jobs.id ".
		"WHERE container_id='$container_id' AND sh_packages.group_id=$group_id ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
		$sql .= " LIMIT $start,$offset";
		$this->query($sql);
		}
		return $count;

		}*/

	function get_packages_to_be_invoiced($container_id, $customer)
	{
		$sql = "SELECT sh_packages.* FROM sh_packages INNER JOIN sh_jobs ON sh_packages.job_id=sh_jobs.id WHERE container_id=$container_id AND invoice_id=0 AND sh_jobs.customer='$customer'";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_cost_codes()
	{
		$sql = "SELECT * FROM sh_cost_codes ORDER BY code ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_cost_code($cost_code_id)
	{
		$sql = "SELECT code FROM sh_cost_codes WHERE id=$cost_code_id";
		$this->query($sql);
		$this->next_record();
		return $this->f(0);
	}

	function get_purchase_codes()
	{
		$sql = "SELECT * FROM sh_purchase_codes ORDER BY code ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_purchase_code($purchase_code_id)
	{
		$sql = "SELECT code FROM sh_purchase_codes WHERE id=$purchase_code_id";
		$this->query($sql);
		$this->next_record();
		return $this->f(0);
	}

	/**
	 * Gets all packages related to an invoice
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_invoice_packages($invoice_id, $start=0, $offset=0, $sortfield='sh_packages.id', $sortorder='ASC')
	{
		$sql = "SELECT * FROM sh_packages INNER JOIN sh_containers ON sh_containers.id=sh_packages.container_id WHERE sh_containers.invoice_id='$invoice_id' ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_invoice_net($invoice_id)
	{
		$sql = "SELECT SUM(price) FROM sh_packages INNER JOIN sh_containers ON sh_containers.id=sh_packages.container_id WHERE sh_containers.invoice_id='$invoice_id'";
		$this->query($sql);
		$this->next_record();
		return $this->f(0);
	}

	function get_container_net($container_id)
	{
		$sql = "SELECT SUM(price) FROM sh_packages WHERE container_id='$container_id'";
		$this->query($sql);
		$this->next_record();
		return $this->f(0);
	}


	function format_purchase_order_number($record){

		$no = $record['id'];
		while(strlen($no)<6){
			$no = '0'.$no;
		}

		return 'PO'.date('Y', $record['btime']).$no;
	}



	/**
	 * Add a standard_package
	 *
	 * @param Array $standard_package Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_standard_package($standard_package)
	{
		$standard_package['id']=$this->nextid('sh_standard_packages');


		if($this->insert_row('sh_standard_packages', $standard_package))
		{
			return $standard_package['id'];
		}
		return false;
	}

	/**
	 * Update a standard_package
	 *
	 * @param Array $standard_package Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_standard_package($standard_package)
	{

		return $this->update_row('sh_standard_packages', 'id', $standard_package);
	}


	/**
	 * Delete a standard_package
	 *
	 * @param Int $standard_package_id ID of the standard_package
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_standard_package($standard_package_id)
	{
		return $this->query("DELETE FROM sh_standard_packages WHERE id=$standard_package_id");
	}


	/**
	 * Gets a standard_package record
	 *
	 * @param Int $standard_package_id ID of the standard_package
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_standard_package($standard_package_id)
	{
		$this->query("SELECT sh_standard_packages.*, sh_package_types.name AS package_type FROM sh_standard_packages INNER JOIN sh_package_types ON sh_package_types.id=sh_standard_packages.package_type_id WHERE sh_standard_packages.id=$standard_package_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a standard_package record by the name field
	 *
	 * @param String $name Name of the standard_package
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_standard_package_by_name($name)
	{
		$this->query("SELECT * FROM sh_standard_packages WHERE standard_package_name='$name'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all standard_packages
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_standard_packages($start=0, $offset=0, $sortfield='id', $sortorder='ASC', $groups=array(), $params=array())
	{
		$sql = "SELECT sh_standard_packages.*, sh_package_types.name AS package_name ".
		"FROM sh_standard_packages LEFT JOIN sh_package_types ON ".
		"sh_package_types.id=sh_standard_packages.package_type_id";
		$where=false;

		if($group_count = count($groups))
		{
			$where=true;
			$sql .= " INNER JOIN sh_standard_packages_groups ON sh_standard_packages_groups.standard_package_id=sh_standard_packages.id";

			if($group_count>1)
			{
				$sql .= " WHERE sh_standard_packages_groups.group_id IN (".implode(',', $groups).")";
			}else
			{
				$sql .= " WHERE sh_standard_packages_groups.group_id=".$groups[0];
			}
		}

		if(count($params))
		{
			$sql .= $where ? ' AND ' : ' WHERE ';
			$sql .= " ".implode(' AND ', $params);
		}

		$sql .= " ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}



	/**
	 * Add a container
	 *
	 * @param Array $container Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_container($container)
	{
		$container['id']=$this->nextid('sh_containers');
		$container['link_id']=$GLOBALS['GO_LINKS']->get_link_id();
		$container['ctime']=$container['mtime']=get_gmt_time();

		if($this->insert_row('sh_containers', $container))
		{
			return $container['id'];
		}
		return false;
	}

	/**
	 * Update a container
	 *
	 * @param Array $container Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_container($container)
	{
		$container['mtime']=get_gmt_time();
		return $this->update_row('sh_containers', 'id', $container);
	}


	/**
	 * Delete a container
	 *
	 * @param Int $container_id ID of the container
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_container($container_id)
	{
		$sql = "UPDATE sh_packages SET container_id=0 WHERE container_id=$container_id";
		$this->query($sql);

		return $this->query("DELETE FROM sh_containers WHERE id=$container_id");
	}


	/**
	 * Gets a container record
	 *
	 * @param Int $container_id ID of the container
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_container($container_id)
	{
		$this->query("SELECT * FROM sh_containers WHERE id=$container_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a container record by the name field
	 *
	 * @param String $name Name of the container
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_container_by_name($name)
	{
		$this->query("SELECT * FROM sh_containers WHERE container_name='$name'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all containers
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_containers($start=0, $offset=0, $sortfield='id', $sortorder='ASC', $groups=array(), $query='')
	{
		$sql = "SELECT * FROM sh_containers";

		if($group_count = count($groups))
		{
			if($group_count>1)
			{
				$sql .= " WHERE group_id IN (".implode(',', $groups).")";
			}else
			{
				$sql .= " WHERE group_id=".$groups[0];
			}
			$where=true;
		}

		if(!empty($query))
		{
			if(isset($where))
			{
				$sql .= ' AND ';
			}else
			{
				$sql .= ' WHERE ';
			}

			$sql .= $query;
		}

		$sql .= " ORDER BY $sortfield $sortorder";

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";

			$sql = substr_replace($sql, 'SELECT SQL_CALC_FOUND_ROWS',0,6);
			$this->query($sql);

			$db = new db();
			$db->query("SELECT FOUND_ROWS() as count");
			$db->next_record();
			$count = $db->f('count');
		}else
		{
			$this->query($sql);
			$count = $this->num_rows();

		}
		return $count;
	}

	function get_container_status($container_id)
	{
		$sql = "SELECT count(*) FROM sh_packages WHERE container_id=$container_id";
		$this->query($sql);
		$this->next_record();

		$status['count']=$this->f(0);


		$sql = "SELECT count(*) FROM sh_packages WHERE container_id=$container_id AND invoice_id>0";
		$this->query($sql);
		$this->next_record();

		$status['invoiced']=$this->f(0);

		return $status;

	}


	/**
	 * Gets all containers
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_shipment_containers($shipment_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC', $group_id=0)
	{
		$sql = "SELECT * FROM sh_containers WHERE shipment_id='$shipment_id'";

		if($group_id>0)
		{
			$sql .= " AND group_id=$group_id";
		}
		$sql .= " ORDER BY $sortfield $sortorder";


		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}


	/**
	 * Gets all containers
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_invoice_containers($invoice_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	{
		$sql = "SELECT * FROM sh_containers WHERE invoice_id='$invoice_id' ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_shipment_standard_packages($shipment_id)
	{
		if($this->get_shipment_containers($shipment_id))
		{
			while($this->next_record())
			{
				$containers[]=$this->f('id');
			}
			$sql = "SELECT count(sh_standard_packages.id) AS count, sh_package_types.name,sh_packages.container_id,sh_standard_packages.package_type_id, sh_standard_packages.short_description  ".
			" FROM sh_packages INNER JOIN sh_standard_packages ON ".
			"sh_standard_packages.id=sh_packages.standard_package_id ".
			"INNER JOIN sh_package_types ON ".
			"sh_package_types.id=sh_standard_packages.package_type_id WHERE ".
			"sh_packages.container_id IN (".implode(',', $containers).") GROUP BY sh_standard_packages.package_type_id";

			$this->query($sql);

			return $this->num_rows();
		}
		return false;
	}

	function get_container_package_type_weight_and_volume($container_id, $package_type_id)
	{
		$sh=new shipping();
		$sql = "SELECT sh_packages.* FROM sh_packages ".
		"INNER JOIN sh_standard_packages ON ".
		"sh_standard_packages.id=sh_packages.standard_package_id ".
		"INNER JOIN sh_package_types ON ".
		"sh_package_types.id=sh_standard_packages.package_type_id ".
		"WHERE container_id=$container_id AND package_type_id=$package_type_id";

		$volume=0;

		$this->query($sql);
		$weight=0;
		while($this->next_record())
		{
			$volume+=($this->f('length')*$this->f('width')*$this->f('height')/1000000);
			$weight+=$sh->get_package_weight($this->f('id'));
		}
		return array($weight, $volume);
	}

	function get_shipment_custom_packages($shipment_id)
	{
		if($this->get_shipment_containers($shipment_id))
		{
			while($this->next_record())
			{
				$containers[]=$this->f('id');
			}
			$sql = "SELECT weight, length, height,".
			" width ".
			" FROM sh_packages WHERE ".
			"sh_packages.container_id IN (".implode(',', $containers).") AND sh_packages.standard_package_id=0";

			$this->query($sql);

			return $this->num_rows();
		}
		return false;
	}



	/**
	 * Add a shipment
	 *
	 * @param Array $shipment Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_shipment($shipment)
	{
		$shipment['id']=$this->nextid('sh_shipments');
		$shipment['link_id']=$GLOBALS['GO_LINKS']->get_link_id();
		$shipment['ctime']=$shipment['mtime']=get_gmt_time();


		if($this->insert_row('sh_shipments', $shipment))
		{
			return $shipment['id'];
		}
		return false;
	}

	/**
	 * Update a shipment
	 *
	 * @param Array $shipment Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_shipment($shipment)
	{

		$shipment['mtime']=get_gmt_time();

		return $this->update_row('sh_shipments', 'id', $shipment);
	}


	/**
	 * Delete a shipment
	 *
	 * @param Int $shipment_id ID of the shipment
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_shipment($shipment_id)
	{
		$sql = "UDPATE sh_containers SET shipment_id=0 WHERE shipment_id=$shipment_id";
		$this->query($sql);
		return $this->query("DELETE FROM sh_shipments WHERE id=$shipment_id");
	}


	/**
	 * Gets a shipment record
	 *
	 * @param Int $shipment_id ID of the shipment
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_shipment($shipment_id)
	{
		$this->query("SELECT * FROM sh_shipments WHERE id=$shipment_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a shipment record by the name field
	 *
	 * @param String $name Name of the shipment
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_shipment_by_name($name)
	{
		$this->query("SELECT * FROM sh_shipments WHERE shipment_name='$name'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all shipments
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_shipments($start=0, $offset=0, $sortfield='id', $sortorder='ASC', $groups=array())
	{
		$sql = "SELECT * FROM sh_shipments";

		if($group_count = count($groups))
		{
			if($group_count>1)
			{
				$sql .= " WHERE group_id IN (".implode(',', $groups).")";
			}else
			{
				$sql .= " WHERE group_id=".$groups[0];
			}
		}

		$sql .= " ORDER BY $sortfield $sortorder";

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";

			$sql = substr_replace($sql, 'SELECT SQL_CALC_FOUND_ROWS',0,6);
			$this->query($sql);

			$db = new db();
			$db->query("SELECT FOUND_ROWS() as count");
			$db->next_record();
			$count = $db->f('count');
		}else
		{
			$this->query($sql);
			$count = $this->num_rows();

		}
		return $count;
	}


	function __on_search($user_id, $last_sync_time)
	{

		global $GO_LANGUAGE,$GO_MODULES;

		require($GO_LANGUAGE->get_language_file('shipping'));
		$db = new db();

		$shipping2 = new shipping();


		$search = new search();

		$sql = "SELECT * FROM sh_jobs WHERE mtime>$last_sync_time";
		$this->query($sql);

		while($this->next_record())
		{
			$cache['table']='sh_jobs';
			$cache['id']=$this->f('id');
			$cache['user_id']=$user_id;
			$cache['name'] = addslashes($this->f('order_no'));
			$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=2001;
			$cache['description']=addslashes($this->f('description'));
			$cache['url']=$GO_MODULES->modules['shipping']['url'].'job.php?job_id='.$this->f('id');
			$cache['type']=addslashes($sh_job);
			$cache['keywords']=addslashes(record_to_keywords($this->record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_read']=$GO_MODULES->modules['shipping']['acl_read'];
			$cache['acl_write']=$GO_MODULES->modules['shipping']['acl_write'];

			$shipping2->get_job_items($this->f('id'));
			while($shipping2->next_record())
			{
				$cache['keywords'] .= addslashes(','.$shipping2->f('serial_no').','.$shipping2->f('part_no'));
			}

			if($search->get_search_result($user_id, $this->f('link_id')))
			{
				$db->update_row('se_cache',array('user_id','link_id'), $cache);
			}else {
				$db->insert_row('se_cache',$cache);
			}
		}

		$sql = "SELECT * FROM sh_packages WHERE mtime>$last_sync_time";
		$this->query($sql);

		while($this->next_record())
		{
			$cache['table']='sh_packages';
			$cache['id']=$this->f('id');
			$cache['user_id']=$user_id;
			$cache['name'] = addslashes($this->f('package_no'));
			$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=2002;
			$cache['description']=addslashes($this->f('description'));
			$cache['url']=$GO_MODULES->modules['shipping']['url'].'package.php?package_id='.$this->f('id');
			$cache['type']=addslashes($sh_package);
			$cache['keywords']=addslashes(record_to_keywords($this->record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');

			$shipping2->get_package_items($this->f('id'));
			while($shipping2->next_record())
			{
				$cache['keywords'] .= addslashes(','.$shipping2->f('serial_no').','.$shipping2->f('part_no'));
			}
			if($search->get_search_result($user_id, $this->f('link_id')))
			{
				$db->update_row('se_cache',array('user_id','link_id'), $cache);
			}else {
				$db->insert_row('se_cache',$cache);
			}
		}

		$sql = "SELECT * FROM sh_containers WHERE mtime>$last_sync_time";
		$this->query($sql);

		while($this->next_record())
		{
			$cache['table']='sh_containers';
			$cache['id']=$this->f('id');
			$cache['user_id']=$user_id;
			$cache['name'] = addslashes($this->f('container_no'));
			$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=2003;
			$cache['description']=addslashes($this->f('description'));
			$cache['url']=$GO_MODULES->modules['shipping']['url'].'container.php?container_id='.$this->f('id');
			$cache['type']=addslashes($sh_container);
			$cache['keywords']=addslashes(record_to_keywords($this->record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');

			if($search->get_search_result($user_id, $this->f('link_id')))
			{
				$db->update_row('se_cache',array('user_id','link_id'), $cache);
			}else {
				$db->insert_row('se_cache',$cache);
			}
		}

		$sql = "SELECT * FROM sh_shipments WHERE mtime>$last_sync_time";
		$this->query($sql);

		while($this->next_record())
		{
			$cache['table']='sh_shipments';
			$cache['id']=$this->f('id');
			$cache['user_id']=$user_id;
			$cache['name'] = $this->f('id');
			$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=2004;
			$cache['description']=addslashes($this->f('description'));
			$cache['url']=$GO_MODULES->modules['shipping']['url'].'shipment.php?shipment_id='.$this->f('id');
			$cache['type']=addslashes($sh_shipment);
			$cache['keywords']=addslashes(record_to_keywords($this->record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			if($search->get_search_result($user_id, $this->f('link_id')))
			{
				$db->update_row('se_cache',array('user_id','link_id'), $cache);
			}else {
				$db->insert_row('se_cache',$cache);
			}
		}
	}



	/**
	 * Add a invoice
	 *
	 * @param Array $invoice Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_invoice($invoice)
	{
		global $GO_CONFIG;

		$invoice['id']=$this->nextid('sh_invoices');

		$invoice['ctime']=$invoice['mtime']=get_gmt_time();
		$invoice['text_order_id']=$this->get_next_invoice_id($invoice);
		$invoice['vat_rate'] = $GO_CONFIG->get_setting('soap_vat');


		if($this->insert_row('sh_invoices', $invoice))
		{
			return $invoice['id'];
		}
		return false;
	}

	/**
	 * Update a invoice
	 *
	 * @param Array $invoice Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_invoice($invoice)
	{

		$invoice['mtime']=get_gmt_time();

		return $this->update_row('sh_invoices', 'id', $invoice);
	}


	/**
	 * Delete a invoice
	 *
	 * @param Int $invoice_id ID of the invoice
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_invoice($invoice_id)
	{
		$sql = "UPDATE sh_packages SET invoice_id=0 WHERE invoice_id=$invoice_id";
		$this->query($sql);





		$shipping = new shipping();
		$this->get_invoice_containers($invoice_id);
		while($this->next_record())
		{
			$status = $shipping->get_container_status($this->f('id'));

			$container['id']=$this->f('id');
			$container['package_count']=$status['count'];
			$container['invoiced']=$status['invoiced'];

			$shipping->update_container($container);
		}

		$sql = "UPDATE sh_containers SET invoice_id=0 WHERE invoice_id=$invoice_id";
		$this->query($sql);



		return $this->query("DELETE FROM sh_invoices WHERE id=$invoice_id");
	}


	/**
	 * Gets a invoice record
	 *
	 * @param Int $invoice_id ID of the invoice
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_invoice($invoice_id)
	{
		$this->query("SELECT * FROM sh_invoices WHERE id=$invoice_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a invoice record by the name field
	 *
	 * @param String $name Name of the invoice
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_invoice_by_name($name)
	{
		$this->query("SELECT * FROM sh_invoices WHERE invoice_name='$name'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all invoices
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_invoices($start=0, $offset=0, $sortfield='id', $sortorder='ASC', $params=array(), $groups=array(), $statuses=array())
	{
		$sql = "SELECT i.*,(i.total*(i.vat_rate/100)) AS vat, g.name AS `group`, s.name AS status, cc.code AS cost_code FROM sh_invoices i ".
			"INNER JOIN groups g ON g.id=i.group_id ".
			"LEFT JOIN sh_cost_codes cc ON cc.id=i.cost_code_id ".
			"INNER JOIN sh_invoice_statuses s ON s.id=i.status_id";

		if(count($params))
		{
			$sql .= " WHERE ".implode(' AND ', $params);
		}

		if(count($groups))
		{
			$sql .= " AND group_id IN (".implode(',', $groups).")";
		}

		if(count($statuses))
		{
			$sql .= " AND status_id IN (".implode(',', $statuses).")";
		}

		$sql .=	" ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;


		return $pos;
	}

	function get_invoices_total_for_cost_code($cost_code_id, $params=array(), $groups=array(), $statuses=array())
	{

		$sql = "SELECT SUM(amount*price) AS total FROM sh_charges c ".
			"INNER JOIN sh_invoices i ON c.invoice_id=i.id ".
			"INNER JOIN groups g ON g.id=i.group_id ".
			"INNER JOIN sh_invoice_statuses s ON s.id=i.status_id ".
			"WHERE c.cost_code_id=$cost_code_id";

		if(count($params))
		{
			$sql .= " AND ".implode(' AND ', $params);
		}

		if(count($groups))
		{
			$sql .= " AND group_id IN (".implode(',', $groups).")";
		}

		$credits=false;
		$new_statuses=array();
		foreach($statuses as $status_id)
		{
			if($status_id==4)
			{
				$credits=true;
			}else
			{
				$new_statuses[]=$status_id;
			}
		}



		$pos = 0;
		if(count($new_statuses))
		{
			$sql1 = $sql." AND status_id IN (".implode(',', $new_statuses).")";
			$this->query($sql1);
			$this->next_record();
			$pos = $this->f('total');
		}

		if($credits)
		{
			$sql2 = $sql." AND i.status_id=4";
			$this->query($sql2);
			$this->next_record();
			$pos -= $this->f('total');
		}

		return $pos;

	}

	function get_invoices_total($params=array(), $groups=array(), $statuses=array())
	{
		$sql = "SELECT SUM(total) AS total, SUM(total*vat_rate/100) AS vat FROM sh_invoices i ".
			"INNER JOIN groups g ON g.id=i.group_id ";
			//"LEFT JOIN sh_cost_codes cc ON cc.id=i.cost_code_id ".
			//"INNER JOIN sh_invoice_statuses s ON s.id=i.status_id";

		if(count($params))
		{
			$sql .= " WHERE ".implode(' AND ', $params);
		}

		if($group_count = count($groups))
		{
			$sql .= " AND group_id IN (".implode(',', $groups).")";

		}

		$credits=false;
		$new_statuses=array();
		foreach($statuses as $status_id)
		{
			if($status_id==4)
			{
				$credits=true;
			}else
			{
				$new_statuses[]=$status_id;
			}
		}



		$pos = array('total'=>0, 'vat'=>0);
		if(count($new_statuses))
		{
			$sql1 = $sql." AND status_id IN (".implode(',', $new_statuses).")";
			$this->query($sql1);
			$this->next_record();
			$pos= $this->record;
		}

		if($credits)
		{
			$sql2 = $sql." AND i.status_id=4";
			$this->query($sql2);
			$this->next_record();
			$pos['total'] -= $this->f('total');
			$pos['vat'] -= $this->f('vat');
		}

		return $pos;
	}


	function get_purchase_invoices_total($params=array(), $groups=array(), $statuses=array())
	{
		$sql = "SELECT SUM(total) AS total, SUM(vat) AS vat FROM sh_purchase_invoices i ".
			"INNER JOIN groups g ON g.id=i.group_id ";
			//"LEFT JOIN sh_cost_codes cc ON cc.id=i.cost_code_id ".
			//"INNER JOIN sh_pi_statuses s ON s.id=i.status_id";

		if(count($params))
		{
			$sql .= " WHERE ".implode(' AND ', $params);
		}

		if($group_count = count($groups))
		{
			$sql .= " AND group_id IN (".implode(',', $groups).")";

		}

		$credits=false;
		$new_statuses=array();
		foreach($statuses as $status_id)
		{
			if($status_id==4)
			{
				$credits=true;
			}else
			{
				$new_statuses[]=$status_id;
			}
		}



		$pos_total = 0;
		$pos_vat = 0;
		if(count($new_statuses))
		{
			$sql1 = $sql." AND status_id IN (".implode(',', $new_statuses).")";
			$this->query($sql1);
			$this->next_record();
			$pos_total = $this->f('total');
			$pos_vat= $this->f('vat');
		}

		if($credits)
		{
			$sql2 = $sql." AND i.status_id=4";
			$this->query($sql2);
			$this->next_record();
			$pos_total -= $this->f('total');
			$pos_vat -= $this->f('vat');
		}

		return array('total'=>$pos_total, 'vat'=>$pos_vat);
	}





	function get_sales_links($start, $end, $start, $offset, $sort_index='name', $sort_order='ASC', $groups=array())
	{
		global $GO_CONFIG, $GO_SECURITY, $GO_LINKS;
		require_once($GO_CONFIG->class_path.'/base/search.class.inc');
		$search = new search();
		$search->update_search_cache($GO_SECURITY->user_id);

		$all_links=array();

		$sql = "SELECT link_id FROM sh_invoices WHERE itime<$end AND itime>$start";
		if($group_count = count($groups))
		{
			if($group_count>0)
			{
				$sql .= " AND (group_id IN (".implode(',', $groups)."))";
			}
		}
		$this->query($sql);
		while($this->next_record())
		{
			$links = $GO_LINKS->get_links($this->f('link_id'));

			foreach($links as $link)
			{
				if(!in_array($link['link_id'], $all_links))
				{
					$all_links[]=$link['link_id'];
				}
			}

		}

		if(count($all_links))
		{

			$sql = "SELECT * FROM se_cache WHERE link_id IN (".implode(',', $all_links).") ORDER BY $sort_index $sort_order";

			$this->query($sql);
			$count =$this->num_rows();

			if($offset>0)
			{
				$sql .= " LIMIT $start,$offset";
				$this->query($sql);
			}

		}else
		{
			$count=0;
		}


		return $count;
	}


	function get_invoices_to_be_paid()
	{
		$sql = "SELECT i.*, g.name AS `group` FROM sh_invoices i ".
			"LEFT JOIN groups g ON g.id=i.group_id WHERE status_id=2 ORDER BY itime ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_invoice_total($invoice_id)
	{
		$sql="SELECT SUM(price*amount) FROM sh_charges WHERE invoice_id=$invoice_id";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->f(0);
		}
		return false;

	}

	function sync_invoice_total($invoice_id)
	{
		$invoice['id']=$invoice_id;
		$invoice['total']=$this->get_invoice_total($invoice_id);
		$this->update_invoice($invoice);
	}


	/**
	 * Add a charge
	 *
	 * @param Array $charge Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_charge($charge)
	{
		$charge['id']=$this->nextid('sh_charges');


		if($this->insert_row('sh_charges', $charge))
		{
			return $charge['id'];
		}
		return false;
	}

	/**
	 * Update a charge
	 *
	 * @param Array $charge Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_charge($charge)
	{

		return $this->update_row('sh_charges', 'id', $charge);
	}


	/**
	 * Delete a charge
	 *
	 * @param Int $charge_id ID of the charge
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_charge($charge_id)
	{

		$charge = $this->get_charge($charge_id);

		if($charge['container_id']>0)
		{
			$up_container['id']=$charge['container_id'];

			$invoice = $this->get_invoice($charge['invoice_id']);

			if(eregi('packages',$charge['description']))
			{
				$this->query("UPDATE sh_packages SET invoice_id=0 WHERE container_id=".$charge['container_id']." AND invoice_id=".$charge['invoice_id']);

				$status = $this->get_container_status($charge['container_id']);

				$up_container['package_count']=$status['count'];
				$up_container['invoiced']=$status['invoiced'];
				$this->update_container($up_container);
			}else
			{
				$up_container['invoice_id']=0;
				$this->update_container($up_container);
			}
		}
		return $this->query("DELETE FROM sh_charges WHERE id=$charge_id");
	}


	/**
	 * Gets a charge record
	 *
	 * @param Int $charge_id ID of the charge
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_charge($charge_id)
	{
		$this->query("SELECT * FROM sh_charges WHERE id=$charge_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a charge record by the name field
	 *
	 * @param String $name Name of the charge
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_charge_by_name($name)
	{
		$this->query("SELECT * FROM sh_charges WHERE charge_name='$name'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all charges
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_charges($invoice_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	{
		$sql = "SELECT c.*, cc.code AS cost_code FROM sh_charges c LEFT JOIN sh_cost_codes cc ON cc.id=c.cost_code_id WHERE invoice_id=$invoice_id ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}

	/*
	 function get_manifest($container_id, $price, $columns, $export=false, $font_size='7px', $heading_size='11px')
	 {
		$max_rows=$export ? 0 : 30;

		if($export)
		{
		load_control('csv');

		}

		$container= $this->get_container($container_id);

		$shipping = new shipping();
		$table = new table();
		$table->set_attribute('class','manifest_table');




		$heading_row2 = new table_row();

		$package_properties_header_colspan=0;
		$part_properties_header_colspan=0;
		$package_totals_header_colspan=0;
		$container_totals_header_colspan1=0;
		$container_totals_header_colspan2=0;

		if(in_array('PACKAGE_NUMBER', $columns))
		{
		$package_properties_header_colspan++;
		$package_totals_header_colspan++;
		$container_totals_header_colspan1++;
		$heading_row2->add_cell(new table_heading('PACKAGE<br />NUMBER'));
		}
		if(in_array('PACKAGE_DESCRIPTION', $columns))
		{
		$package_properties_header_colspan++;
		$package_totals_header_colspan++;
		$container_totals_header_colspan1++;
		$heading_row2->add_cell(new table_heading('PACKAGE<br />DESCRIPTION'));
		}
		if(in_array('LENGTH', $columns))
		{
		$package_properties_header_colspan++;
		$package_totals_header_colspan++;
		$container_totals_header_colspan1++;
		$heading_row2->add_cell(new table_heading('LENGTH<br />(CMS)'));
		}
		if(in_array('WIDTH', $columns))
		{
		$package_properties_header_colspan++;
		$package_totals_header_colspan++;
		$container_totals_header_colspan1++;
		$heading_row2->add_cell(new table_heading('WIDTH<br />(CMS)'));
		}
		if(in_array('DEPTH', $columns))
		{
		$package_properties_header_colspan++;
		$package_totals_header_colspan++;
		$container_totals_header_colspan1++;

		$heading_row2->add_cell(new table_heading('DEPTH<br />(CMS)'));
		}
		if(in_array('VOLUME', $columns))
		{
		$package_properties_header_colspan++;
		$package_totals_header_colspan++;
		$heading_row2->add_cell(new table_heading('VOLUME<br />(M3)'));
		}
		if(in_array('TARE', $columns))
		{
		$package_properties_header_colspan++;
		$package_totals_header_colspan++;
		$heading_row2->add_cell(new table_heading('TARE<br />(KGS)'));
		}
		if(in_array('PRICE', $columns))
		{
		$package_properties_header_colspan++;
		$package_totals_header_colspan++;
		$heading_row2->add_cell(new table_heading('PRICE<br />GBP'));
		}

		//part properties

		if(in_array('PART_NUMBER', $columns))
		{
		$package_totals_header_colspan++;
		$part_properties_header_colspan++;
		$heading_row2->add_cell(new table_heading('PART<br />NUMBER'));
		}
		if(in_array('SERIAL_NUMBER', $columns))
		{
		$package_totals_header_colspan++;
		$part_properties_header_colspan++;
		$heading_row2->add_cell(new table_heading('SERIAL<br />NUMBER'));
		}
		if(in_array('PART_DESCRIPTION', $columns))
		{
		$package_totals_header_colspan++;
		$part_properties_header_colspan++;
		$heading_row2->add_cell(new table_heading('PART<br />DESCRIPTION'));
		}
		if(in_array('QTY_PACKED', $columns))
		{
		$part_properties_header_colspan++;
		$heading_row2->add_cell(new table_heading('QTY<br />PACKED'));
		}
		if(in_array('PART_WT_EA', $columns))
		{
		$part_properties_header_colspan++;
		$heading_row2->add_cell(new table_heading('PART WT<br />EA (KGS)'));
		}
		if(in_array('TOTAL_WT_PARTS', $columns))
		{
		$part_properties_header_colspan++;
		$heading_row2->add_cell(new table_heading('TOTAL WT<br />PARTS (KGS)'));
		}
		if(in_array('GROSS_WT', $columns))
		{
		$part_properties_header_colspan++;
		$th=new table_heading('GROSS WT<br />(KGS)');
		$th->set_attribute('style','border-right:1px solid black;');
		$heading_row2->add_cell($th);
		}




		$heading_row1 = new table_row();

		$th = new table_heading('PACKAGE PROPERTIES');
		$th->set_attribute('colspan',$package_properties_header_colspan);
		$heading_row1->add_cell($th);

		$th = new table_heading('PACKAGE CONTENTS');
		$th->set_attribute('colspan',$part_properties_header_colspan);
		$heading_row1->add_cell($th);




		$_heading_row1=clone($heading_row1);
		$_heading_row2=clone($heading_row2);

		$table->add_row($_heading_row1);
		$table->add_row($_heading_row2);



		$container_price=0;

		$shipping2 = new shipping();

		//$total_tare_weight=0;
		$container_net_weight=0;
		$container_gross_weight=0;
		$container_volume=0;
		$container_price=0;
		$container_qty=0;
		$rowcount=0;
		$package_count = $shipping->get_container_packages($container_id,0,0,'supplier ASC, sh_packages.package_no');


		$supplier_container_net_weight=0;
		$supplier_container_gross_weight=0;
		$supplier_container_volume=0;
		$supplier_container_price=0;
		$supplier_container_qty=0;



		$supplier_package_counts=Array();
		$supplier_package_count=0;

		while($shipping->next_record())
		{
		//new page for new customer
		if(isset($supplier) && $shipping->f('supplier')!=$supplier)
		{
		//START OF CONTAINER TOTALS

		$supplier_container_tare_weight = $supplier_container_gross_weight-$supplier_container_net_weight;

		$row = new table_row();
		$row->set_attribute('style','border:0px;');

		$cell = new table_cell('<br />SUPPLIER TOTALS:');
		$cell->set_attribute('colspan',$container_totals_header_colspan1);
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		if(in_array('VOLUME', $columns))
		{
		$cell = new table_cell('<br />'.format_number($supplier_container_volume,3));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('TARE', $columns))
		{
		$cell = new table_cell('<br />'.format_number($supplier_container_tare_weight,3));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('PRICE', $columns))
		{
		$cell = new table_cell('<br />'.format_number($supplier_container_price));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}


		if(in_array('PART_NUMBER', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('SERIAL_NUMBER', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('PART_DESCRIPTION', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('QTY_PACKED', $columns))
		{

		$cell = new table_cell('<br />'.format_number($supplier_container_qty));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('PART_WT_EA', $columns))
		{
		$cell = new table_cell('<br />');
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('TOTAL_WT_PARTS', $columns))
		{
		$cell = new table_cell('<br />'.format_number($supplier_container_net_weight,3));
		$cell->set_attribute('style','text-align:right;border:0px;k');
		$row->add_cell($cell);
		}
		if(in_array('GROSS_WT', $columns))
		{
		$cell = new table_cell('<br />'.format_number($supplier_container_gross_weight,3));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		$table->add_row($row);

		//END OF CONTAINER TOTALS

		$_heading_row1=clone($heading_row1);
		$_heading_row2=clone($heading_row2);

		$supplier_package_counts[$supplier]=$supplier_package_count;

		if(!$export)
		{
		$pages[]=str_replace('%supplier_package_count%', '%supplier_package_count_'.$supplier.'%', $header).
		$table->get_html();

		}else
		{

		$csv = new csv();
		$csv->add_record(array(""));
		$csv->import_table($table);

		$pages[]="\"\"\n\"\"\n".str_replace('%supplier_package_count%', '%supplier_package_count_'.$supplier.'%', $header).
		$csv->get_csv();
		}
		$table->rows=array();

		$table->add_row($_heading_row1);
		$table->add_row($_heading_row2);

		$rowcount=0;//$count;

		$supplier_package_count=0;
		$supplier_container_net_weight=0;
		$supplier_container_gross_weight=0;
		$supplier_container_volume=0;
		$supplier_container_price=0;
		$supplier_container_qty=0;


		}


		$supplier_package_count++;

		$destination = $shipping->f('destination');
		$supplier = $shipping->f('supplier');
		$customer = $shipping->f('customer');



		//create header



		$toptable = new table();
		$toptable->set_attribute('style','width:100%;');



		$row = new table_row();
		if($price)
		{
		$cell = new table_cell('PROPACK SERVICES, EXPORT PACKING DIVISION - BILLING MANIFEST');
		}else {
		$cell = new table_cell('PROPACK SERVICES, EXPORT PACKING DIVISION - CONTAINER MANIFEST');
		}

		$cell->set_attribute('colspan','5');
		$cell->set_attribute('style','font-weight:bold;');
		$row->add_cell($cell);


		$cell = new table_cell('%page%');
		$cell->set_attribute('style','font-weight:bold;text-align:right');
		$row->add_cell($cell);
		$toptable->add_row($row);

		$row = new table_row();
		$cell = new table_cell('DESTINATION:');
		$row->add_cell($cell);
		$cell = new table_cell($destination);
		$row->add_cell($cell);

		$cell = new table_cell('SUPPLIER:');
		$row->add_cell($cell);
		$cell = new table_cell($supplier);
		$row->add_cell($cell);


		$cell = new table_cell('CUSTOMER:');
		$row->add_cell($cell);
		$cell = new table_cell($customer);
		$row->add_cell($cell);


		$toptable->add_row($row);

		$row = new table_row();
		$cell = new table_cell('DATE:');
		$row->add_cell($cell);
		$cell = new table_cell(date($_SESSION['GO_SESSION']['date_format']));
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$toptable->add_row($row);

		$row = new table_row();
		$cell = new table_cell('MANIFEST NO.:');
		$cell->set_attribute('style','width:140px;');
		$row->add_cell($cell);
		$cell = new table_cell($container['container_no']);
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$toptable->add_row($row);

		$row = new table_row();
		$cell = new table_cell('TOTAL PACKAGES:');
		$row->add_cell($cell);
		$cell = new table_cell('%supplier_package_count%');
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$toptable->add_row($row);

		if($export)
		{
		$csv =  new csv();
		$csv->import_table($toptable);
		$header = $csv->get_csv();
		}else
		{
		$header = $toptable->get_html();
		}


		//end header


		$count = $shipping2->get_package_parts($shipping->f('id'));

		$package_total_qty=0;
		$package_net_weight=0;

		$package_rows=array();
		while($shipping2->next_record())
		{

		$row = new table_row();

		if(in_array('PACKAGE_NUMBER', $columns))
		{
		$cell = new table_cell();
		$row->add_cell($cell);
		}
		if(in_array('PACKAGE_DESCRIPTION', $columns))
		{
		$cell = new table_cell();
		$row->add_cell($cell);
		}
		if(in_array('LENGTH', $columns))
		{
		$cell = new table_cell();
		$row->add_cell($cell);
		}
		if(in_array('WIDTH', $columns))
		{
		$cell = new table_cell();
		$row->add_cell($cell);
		}
		if(in_array('DEPTH', $columns))
		{
		$cell = new table_cell();
		$row->add_cell($cell);
		}
		if(in_array('VOLUME', $columns))
		{
		$cell = new table_cell();
		$row->add_cell($cell);
		}
		if(in_array('TARE', $columns))
		{
		$cell = new table_cell();
		$row->add_cell($cell);
		}
		if(in_array('PRICE', $columns))
		{
		$cell = new table_cell();
		$row->add_cell($cell);
		}


		if(in_array('PART_NUMBER', $columns))
		{
		$cell = new table_cell($shipping2->f('part_no'));
		$row->add_cell($cell);
		}
		if(in_array('SERIAL_NUMBER', $columns))
		{
		$cell = new table_cell($shipping2->f('serial_no'));
		$row->add_cell($cell);
		}
		if(in_array('PACKAGE_DESCRIPTION', $columns))
		{
		$cell = new table_cell($shipping2->f('name'));
		$row->add_cell($cell);
		}

		if(in_array('QTY_PACKED', $columns))
		{
		$cell=new table_cell($shipping2->f('amount'));
		$cell->set_attribute('style','text-align:right');
		$row->add_cell($cell);
		}
		if(in_array('PART_WT_EA', $columns))
		{
		$cell=new table_cell(format_number($shipping2->f('weight'),3));
		$cell->set_attribute('style','text-align:right');
		$row->add_cell($cell);
		}
		if(in_array('TOTAL_WT_PARTS', $columns))
		{
		$cell=new table_cell(format_number($shipping2->f('amount')*$shipping2->f('weight'),3));
		$cell->set_attribute('style','text-align:right');
		$row->add_cell($cell);
		}

		if(in_array('GROSS_WT', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','border-right:1px solid black;');
		$row->add_cell($cell);
		}

		$package_net_weight+=$shipping2->f('amount')*$shipping2->f('weight');
		$package_total_qty+=$shipping2->f('amount');
		$container_qty+=$shipping2->f('amount');
		$supplier_container_qty+=$shipping2->f('amount');




		//$table->add_row($row);
		$package_rows[]=$row;
		}

		//return manual weight if set
		$package_gross_weight=$shipping2->get_package_weight($shipping->f('id'),true);

		if($shipping->f('standard_package_id')>0 && $standard_package=$shipping2->get_standard_package($shipping->f('standard_package_id')))
		{
		//$tare_weight=$standard_package['weight'];
		$package_type=$standard_package['package_type'];
		}else{
		$package_type='CUSTOM PACKAGE';
		//$tare_weight=0;
		}

		$package_tare_weight = $package_gross_weight-$package_net_weight;

		$volume = $shipping->f('width')*$shipping->f('length')*$shipping->f('height')/1000000;

		$container_volume+=$volume;
		$container_net_weight+=$package_net_weight;
		$container_gross_weight+=$package_gross_weight;

		$supplier_container_volume+=$volume;
		$supplier_container_net_weight+=$package_net_weight;
		$supplier_container_gross_weight+=$package_gross_weight;

		$rowcount++;
		$row = new table_row();

		if(in_array('PACKAGE_NUMBER', $columns))
		{
		$cell = new table_cell($shipping->f('package_no'));
		$cell->set_attribute('style','border-top:1px solid black;');
		$row->add_cell($cell);
		}
		if(in_array('PACKAGE_DESCRIPTION', $columns))
		{
		$cell = new table_cell($package_type);
		$cell->set_attribute('style','border-top:1px solid black;');
		$row->add_cell($cell);
		}

		if(in_array('LENGTH', $columns))
		{
		$cell = new table_cell($shipping->f('length'));
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}
		if(in_array('WIDTH', $columns))
		{
		$cell = new table_cell($shipping->f('width'));
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}
		if(in_array('DEPTH', $columns))
		{
		$cell = new table_cell($shipping->f('height'));
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}
		if(in_array('VOLUME', $columns))
		{


		$cell = new table_cell(format_number($volume,3));
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}
		//$tare_weight = $shipping2->get_package_weight($shipping->f('id'));
		//$standard_package = $shipping2->get_standard_package($shipping->f('standard_package_id'));

		if(in_array('TARE', $columns))
		{
		$cell = new table_cell(format_number($package_tare_weight,3));
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}




		if(in_array('PRICE', $columns))
		{
		$cell = new table_cell(format_number($shipping->f('price'),2));
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);

		$container_price+=$shipping->f('price');
		$supplier_container_price+=$shipping->f('price');
		}

		if(in_array('PART_NUMBER', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}
		if(in_array('SERIAL_NUMBER', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}
		if(in_array('PART_DESCRIPTION', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}



		if(in_array('QTY_PACKED', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}
		if(in_array('PART_WT_EA', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}
		if(in_array('TOTAL_WT_PARTS', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}
		if(in_array('GROSS_WT', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border-top:1px solid black');
		$row->add_cell($cell);
		}


		//if($max_rows>0 && ($rowcount+$count+1)>$max_rows)
		if($max_rows>0 && (count($table->rows)+$count)>=$max_rows)
		{


		$_heading_row1=clone($heading_row1);
		$_heading_row2=clone($heading_row2);

		//todo count is not complete
		$pages[]=str_replace('%supplier_package_count%', '%supplier_package_count_'.$supplier.'%', $header).
		$table->get_html();

		$table->rows=array();
		$table->add_row($_heading_row1);
		$table->add_row($_heading_row2);

		//$rowcount=$count;
		}

		$table->add_row($row);



		foreach($package_rows as $row)
		{

		if($max_rows>0 && (count($table->rows)+1)>=$max_rows)
		{
		$continue_row = new table_row();
		$cell = new table_cell('Package continues on next page');
		$cell->set_attribute('colspan', count($heading_row2->cells));
		$cell->set_attribute('style', 'border:1px solid black');
		$continue_row->add_cell($cell);
		$table->add_row($continue_row);

		$_heading_row1=clone($heading_row1);
		$_heading_row2=clone($heading_row2);

		$pages[]=str_replace('%supplier_package_count%', '%supplier_package_count_'.$supplier.'%', $header).
		$table->get_html();
		$table->rows=array();

		$table->add_row($_heading_row1);
		$table->add_row($_heading_row2);

		}


		$table->add_row($row);
		}








		$row = new table_row();

		$cell = new table_cell('PACKAGE TOTALS:');
		$cell->set_attribute('style','text-align:right;border:0px;border-top:1px solid black;');
		$cell->set_attribute('colspan',$package_totals_header_colspan);
		$row->add_cell($cell);
		if(in_array('QTY_PACKED', $columns))
		{
		$cell = new table_cell(format_number($package_total_qty));
		$cell->set_attribute('style','text-align:right;border:0px;border-top:1px solid black;');
		$row->add_cell($cell);
		}
		if(in_array('PART_WT_EA', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;border-top:1px solid black;');
		$row->add_cell($cell);
		}
		if(in_array('TOTAL_WT_PARTS', $columns))
		{
		$cell = new table_cell(format_number($package_net_weight,3));
		$cell->set_attribute('style','text-align:right;border:0px;border-top:1px solid black;');
		$row->add_cell($cell);
		}


		if(in_array('GROSS_WT', $columns))
		{


		$cell = new table_cell(format_number($package_gross_weight,3));
		$cell->set_attribute('style','text-align:right;border:0px;border-top:1px solid black;border-right:0');
		$row->add_cell($cell);
		}
		$table->add_row($row);
		}




		if($price && !empty($container['charge_load_fee']))
		{
		$container_price+=130;
		$row = new table_row();
		$cell = new table_cell('CONTAINER LOAD FEE:');
		$cell->set_attribute('colspan',$container_totals_header_colspan1);
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);


		if(in_array('VOLUME', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('TARE', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('PRICE', $columns))
		{
		$cell = new table_cell(format_number(130));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}


		if(in_array('PART_NUMBER', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('SERIAL_NUMBER', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('PART_DESCRIPTION', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('QTY_PACKED', $columns))
		{

		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('PART_WT_EA', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('TOTAL_WT_PARTS', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;k');
		$row->add_cell($cell);
		}
		if(in_array('GROSS_WT', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		$table->add_row($row);
		}




		//START OF CONTAINER TOTALS

		$supplier_container_tare_weight = $supplier_container_gross_weight-$supplier_container_net_weight;

		$row = new table_row();
		$row->set_attribute('style','border:0px;');

		$cell = new table_cell('<br />SUPPLIER TOTALS:');
		$cell->set_attribute('colspan',$container_totals_header_colspan1);
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		if(in_array('VOLUME', $columns))
		{
		$cell = new table_cell('<br />'.format_number($supplier_container_volume,3));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('TARE', $columns))
		{
		$cell = new table_cell('<br />'.format_number($supplier_container_tare_weight,3));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('PRICE', $columns))
		{
		$cell = new table_cell('<br />'.format_number($supplier_container_price));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}


		if(in_array('PART_NUMBER', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('SERIAL_NUMBER', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('PART_DESCRIPTION', $columns))
		{
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('QTY_PACKED', $columns))
		{

		$cell = new table_cell('<br />'.format_number($supplier_container_qty));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('PART_WT_EA', $columns))
		{
		$cell = new table_cell('<br />');
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		if(in_array('TOTAL_WT_PARTS', $columns))
		{
		$cell = new table_cell('<br />'.format_number($supplier_container_net_weight,3));
		$cell->set_attribute('style','text-align:right;border:0px;k');
		$row->add_cell($cell);
		}
		if(in_array('GROSS_WT', $columns))
		{
		$cell = new table_cell('<br />'.format_number($supplier_container_gross_weight,3));
		$cell->set_attribute('style','text-align:right;border:0px;');
		$row->add_cell($cell);
		}
		$table->add_row($row);

		//END OF CONTAINER TOTALS







		//START OF CONTAINER TOTALS

		$totals_table = new table();
		$totals_table->set_attribute('class','manifest_table');




		$row = new table_row();
		$row->set_attribute('style','border:1px solid black;');

		$cell = new table_heading('NUMBER OF<br />PACKAGES');
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);

		$colspan=1;

		if(in_array('VOLUME', $columns))
		{
		$cell = new table_heading('VOLUME<br />(M3)');
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		$colspan++;
		}
		if(in_array('TARE', $columns))
		{
		$cell = new table_heading('TARE<br />(KGS)');
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		$colspan++;
		}
		if(in_array('PRICE', $columns))
		{
		$cell = new table_heading('PRICE<br />(GBP)');
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		$colspan++;
		}

		if(in_array('QTY_PACKED', $columns))
		{

		$cell = new table_heading('QTY<br />PACKED');
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		$colspan++;
		}

		if(in_array('TOTAL_WT_PARTS', $columns))
		{
		$cell = new table_heading('TOTAL WT<br />PARTS (KGS)');
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		$colspan++;
		}
		if(in_array('GROSS_WT', $columns))
		{
		$cell = new table_heading('GROSS WT<BR />(KGS)');
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		$colspan++;
		}


		$headrow = new table_row();
		$headrow->set_attribute('style','border:1px solid black;');

		$headcell = new table_heading('OVERALL CONTAINER TOTALS');
		$headcell->set_attribute('colspan',$colspan);
		$headcell->set_attribute('style','text-align:center;border:1px solid black;');
		$headrow->add_cell($headcell);
		$totals_table->add_row($headrow);


		$totals_table->add_row($row);


		//END HEADERS




		$container_tare_weight = $container_gross_weight-$container_net_weight;

		$row = new table_row();
		$row->set_attribute('style','border:1px solid black;');

		$cell = new table_cell($package_count);
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);

		if(in_array('VOLUME', $columns))
		{
		$cell = new table_cell(format_number($container_volume,3));
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		}
		if(in_array('TARE', $columns))
		{
		$cell = new table_cell(format_number($container_tare_weight,3));
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		}
		if(in_array('PRICE', $columns))
		{
		$cell = new table_cell(format_number($container_price));
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		}

		if(in_array('QTY_PACKED', $columns))
		{

		$cell = new table_cell(format_number($container_qty));
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		}

		if(in_array('TOTAL_WT_PARTS', $columns))
		{
		$cell = new table_cell(format_number($container_net_weight,3));
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		}
		if(in_array('GROSS_WT', $columns))
		{
		$cell = new table_cell(format_number($container_gross_weight,3));
		$cell->set_attribute('style','text-align:center;border:1px solid black;');
		$row->add_cell($cell);
		}
		$totals_table->add_row($row);

		//END OF CONTAINER TOTALS

		$supplier_package_counts[$supplier]=$supplier_package_count;


		if($export)
		{


		$csv = new csv();
		$csv->import_table($table);

		$csv->add_record(array(''));

		$csv->import_table($totals_table);

		$pages[]="\"\"\n\"\"\n".str_replace('%supplier_package_count%', '%supplier_package_count_'.$supplier.'%', $header).
		$csv->get_csv();





		$csv = '';

		$page_count = count($pages);
		for($i=0;$i<$page_count;$i++)
		{
		$page = str_replace('%page%', '', $pages[$i]);

		foreach($supplier_package_counts as $supplier=>$count)
		{
		$page = str_replace('%supplier_package_count_'.$supplier.'%', $count, $page);
		}

		$csv .= $page;
		}

		return $csv;


		}else {



		$pages[]=str_replace('%supplier_package_count%', '%supplier_package_count_'.$supplier.'%', $header).
		$table->get_html().$totals_table->get_html();



		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
		<html>
		<head>
		<title>MANIFEST</title>
		<style type="text/css">
		body{
		font-family: helvetica;
		font-size:'.$font_size.';
		}
		h1, h2{
		margin-bottom:1px;
		margin-top:5px;
		font-size:'.$heading_size.'
		}



		.manifest_table{
		font-family: helvetica, helvetica, sans-serif;
		font-size: '.$font_size.';
		color: #000;
		border: 0px solid black;
		border-collapse: collapse;
		cursor: default;
		width:100%;
		margin-top:5px;
		}

		.manifest_table th, .manifest_table th td {
		background-color: #f1f1f1;
		font-size: '.$font_size.';
		padding: 2px;
		padding-right:7px;
		margin: 0px;
		font-weight: bold;
		white-space: nowrap;
		text-align: center;

		border: 1px solid #000;
		}
		.manifest_table td {
		border-right: 1px solid #000;
		border-left: 1px solid #000;
		border-top:0px;
		border-bottom:0px;
		margin: 0px;
		padding: 2px;
		padding-right: 7px;
		white-space: nowrap;
		}
		</style>
		</head>
		<body>';





		$page_count = count($pages);
		for($i=0;$i<$page_count;$i++)
		{
		$page = str_replace('%page%', '<br />Page '.($i+1).' of '.$page_count, $pages[$i]);

		foreach($supplier_package_counts as $supplier=>$count)
		{
		$page = str_replace('%supplier_package_count_'.$supplier.'%', $count, $page);
		}

		$html .= $page;
		if($i!=($page_count-1))
		{
		//$html .= '<div align="right"></div>';
		$html .= '<div style="page-break-after:always;text-align:right"></div>';
		}
		}


		$html .='</body></html>';
		return $html;
		}
		}
		*/

	function is_group($group_id)
	{
		$sql = "SELECT * FROM sh_groups WHERE group_id=$group_id";
		$this->query($sql);
		return $this->next_record();
	}

	function add_group($group_id)
	{
		$sql = "INSERT INTO sh_groups VALUES ($group_id)";
		return $this->query($sql);
	}
	function delete_group($group_id)
	{
		$sql = "DELETE FROM sh_parts_groups WHERE group_id=$group_id";
		$this->query($sql);

		$sql = "DELETE FROM sh_standard_packages_groups WHERE group_id=$group_id";
		$this->query($sql);

		$sql = "DELETE FROM sh_groups WHERE group_id=$group_id";
		return $this->query($sql);
	}

	function get_groups($user_id=0)
	{
		$sql = "SELECT * FROM sh_groups INNER JOIN groups ON sh_groups.group_id=groups.id ";

		if($user_id > 0)
		{
			$sql .= "INNER JOIN users_groups ON groups.id=users_groups.group_id ".
			"AND users_groups.user_id='$user_id' ";
		}

		$sql .= "ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}



	function part_is_in_group($part_id, $group_id)
	{
		$sql = "SELECT * FROM sh_parts_groups WHERE part_id=$part_id AND group_id=$group_id";
		$this->query($sql);
		return $this->next_record();
	}

	function add_part_to_group($part_id, $group_id)
	{
		$sql = "INSERT INTO sh_parts_groups (part_id, group_id) VALUES ($part_id,$group_id)";
		return $this->query($sql);
	}

	function delete_part_from_group($part_id, $group_id)
	{
		$sql = "DELETE FROM sh_parts_groups WHERE part_id=$part_id AND group_id=$group_id";
		return $this->query($sql);
	}

	function delete_parts_from_group($part_id)
	{
		$sql = "DELETE FROM sh_parts_groups WHERE part_id=$part_id";
		return $this->query($sql);
	}

	function standard_package_is_in_group($standard_package_id, $group_id)
	{
		$sql = "SELECT * FROM sh_standard_packages_groups WHERE standard_package_id=$standard_package_id AND group_id=$group_id";
		$this->query($sql);
		return $this->next_record();
	}

	function add_standard_package_to_group($standard_package_id, $group_id)
	{
		$sql = "INSERT INTO sh_standard_packages_groups (standard_package_id, group_id) VALUES ($standard_package_id,$group_id)";
		return $this->query($sql);
	}

	function delete_standard_package_from_group($standard_package_id, $group_id)
	{
		$sql = "DELETE FROM sh_standard_packages_groups WHERE standard_package_id=$standard_package_id AND group_id=$group_id";
		return $this->query($sql);
	}

	function delete_standard_packages_from_group($standard_package_id)
	{
		$sql = "DELETE FROM sh_standard_packages_groups WHERE standard_package_id=$standard_package_id";
		return $this->query($sql);
	}



	function get_invoice_statuses()
	{
		$sql = "SELECT * FROM sh_invoice_statuses ORDER BY id ASC";
		$this->query($sql);
		return $this->num_rows();
	}


	function get_purchase_invoice_statuses()
	{
		$sql = "SELECT * FROM sh_pi_statuses ORDER BY id ASC";
		$this->query($sql);
		return $this->num_rows();
	}








	/**
	 * Add a purchase_order
	 *
	 * @param Array $purchase_order Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_purchase_order($purchase_order)
	{
		$purchase_order['id']=$this->nextid('sh_purchase_orders');

		$purchase_order['ctime']=$purchase_order['mtime']=get_gmt_time();


		if($this->insert_row('sh_purchase_orders', $purchase_order))
		{
			return $purchase_order['id'];
		}
		return false;
	}

	/**
	 * Update a purchase_order
	 *
	 * @param Array $purchase_order Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_purchase_order($purchase_order)
	{

		$purchase_order['mtime']=get_gmt_time();

		return $this->update_row('sh_purchase_orders', 'id', $purchase_order);
	}


	/**
	 * Delete a purchase_order
	 *
	 * @param Int $purchase_order_id ID of the purchase_order
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_purchase_order($purchase_order_id)
	{
		$this->query("DELETE FROM sh_po_charges WHERE purchase_order_id=$purchase_order_id");
		return $this->query("DELETE FROM sh_purchase_orders WHERE id=$purchase_order_id");
	}


	/**
	 * Gets a purchase_order record
	 *
	 * @param Int $purchase_order_id ID of the purchase_order
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_purchase_order($purchase_order_id)
	{
		$this->query("SELECT * FROM sh_purchase_orders WHERE id=$purchase_order_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a purchase_order record by the name field
	 *
	 * @param String $name Name of the purchase_order
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_purchase_order_by_name($name)
	{
		$this->query("SELECT * FROM sh_purchase_orders WHERE purchase_order_name='$name'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all purchase_orders
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_purchase_orders($start=0, $offset=0, $sortfield='id', $sortorder='ASC', $params=array(), $groups=array())
	{
		$sql = "SELECT po.*, pc.code AS cost_code FROM sh_purchase_orders po ".
			"LEFT JOIN sh_purchase_codes pc ON pc.id=po.purchase_code_id ";

		if(count($params))
		{
			$sql .= " WHERE ".implode(' AND ', $params);
			$group_where = " AND";
		}
		else
		{
			$group_where = " WHERE";
		}
		if($group_count = count($groups))
		{
			if($group_count>0)
			{
				$sql .= $group_where." (group_id IN (".implode(',', $groups)."))";
			}
		}

		$sql .= " ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_standard_package_usage($start=0, $offset=0, $sortfield='id', $sortorder='ASC', $params=array(), $groups=array())
	{
		$sql = "SELECT s.id, s.name, s.stock, COUNT(p.standard_package_id) AS sp_usage FROM sh_packages p LEFT JOIN sh_standard_packages s ON s.id = p.standard_package_id";

		if(count($params))
		{
			$sql .= " WHERE ".implode(' AND ', $params);
		}

		if($group_count = count($groups))
		{
			if($group_count>0)
			{
				$sql .= " AND (group_id IN (".implode(',', $groups)."))";
			}
		}

		$sql .= " GROUP BY standard_package_id ORDER BY $sortfield $sortorder";
		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}




	/**
	 * Add a po_charge
	 *
	 * @param Array $po_charge Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_po_charge($po_charge)
	{
		$po_charge['id']=$this->nextid('sh_po_charges');


		if($this->insert_row('sh_po_charges', $po_charge))
		{
			return $po_charge['id'];
		}
		return false;
	}

	/**
	 * Update a po_charge
	 *
	 * @param Array $po_charge Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_po_charge($po_charge)
	{

		return $this->update_row('sh_po_charges', 'id', $po_charge);
	}


	/**
	 * Delete a po_charge
	 *
	 * @param Int $po_charge_id ID of the po_charge
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_po_charge($po_charge_id)
	{
		return $this->query("DELETE FROM sh_po_charges WHERE id=$po_charge_id");
	}


	/**
	 * Gets a po_charge record
	 *
	 * @param Int $po_charge_id ID of the po_charge
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_po_charge($po_charge_id)
	{
		$this->query("SELECT * FROM sh_po_charges WHERE id=$po_charge_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	/*
	 * Gets all charges
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_po_charges($purchase_order_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	{
		$sql = "SELECT poc.*, pc.code AS cost_code FROM sh_po_charges poc LEFT JOIN sh_purchase_codes pc ON pc.id=poc.purchase_code_id WHERE purchase_order_id=$purchase_order_id ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}



	function get_purchase_order_totals($purchase_order_id)
	{
		global $GO_CONFIG;
		$vat = $GO_CONFIG->get_setting('soap_vat')/100;

		$sql="SELECT SUM(price*amount) AS total, SUM(price*amount*$vat) AS vat, SUM(amount) AS items, SUM(delivered_amount) AS delivered FROM sh_po_charges WHERE purchase_order_id=$purchase_order_id";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


/*	function get_purchase_order_vat($purchase_order_id)
	{
		$vat = $GO_CONFIG->get_setting('soap_vat')/100;

		$sql="SELECT SUM(price*amount*$vat) FROM sh_po_charges WHERE purchase_order_id=$purchase_order_id AND vat_applicable='1'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->f(0);
		}
		return false;
	}*/


	function sync_purchase_order_total($purchase_order_id)
	{
		$totals = $this->get_purchase_order_totals($purchase_order_id);
		$purchase_order['id']=$purchase_order_id;
		$purchase_order['total']=$totals['total'];
		$purchase_order['items']=$totals['items'];
		$purchase_order['delivered']=$totals['delivered'];
		$this->update_purchase_order($purchase_order);
	}

	function decrease_stock($id)
	{
		$sql = "UPDATE sh_standard_packages SET stock=stock-1 WHERE id='$id'";
		$this->query($sql);
	}




	/**
	 * Add a material
	 *
	 * @param Array $material Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_material($material)
	{
		$material['id']=$this->nextid('sh_materials');


		if($this->insert_row('sh_materials', $material))
		{
			return $material['id'];
		}
		return false;
	}

	/**
	 * Update a material
	 *
	 * @param Array $material Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_material($material)
	{

		return $this->update_row('sh_materials', 'id', $material);
	}


	/**
	 * Delete a material
	 *
	 * @param Int $material_id ID of the material
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_material($material_id)
	{
		return $this->query("DELETE FROM sh_materials WHERE id=$material_id");
	}


	/**
	 * Gets a material record
	 *
	 * @param Int $material_id ID of the material
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_material($material_id)
	{
		$this->query("SELECT * FROM sh_materials WHERE id=$material_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a material record by the name field
	 *
	 * @param String $name Name of the material
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_material_by_name($name)
	{
		$this->query("SELECT * FROM sh_materials WHERE material_name='$name'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all materials
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_materials($start=0, $offset=0, $sortfield='id', $sortorder='ASC', $params=array())
	{
		$sql = "SELECT * FROM sh_materials";

		if(count($params))
		{
			$sql .= " WHERE ".implode(' AND ', $params);
		}

		$sql .=  " ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_material_suppliers()
	{
		$sql = "SELECT DISTINCT supplier FROM sh_materials";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_standard_package_suppliers()
	{
		$sql = "SELECT DISTINCT c.id, c.name FROM ab_companies c INNER JOIN sh_standard_packages p ON p.supplier_company_id=c.id ORDER BY c.name ASC";
		$this->query($sql);
		return $this->num_rows();
	}


	/**
	 * Add a purchase_invoice
	 *
	 * @param Array $purchase_invoice Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_purchase_invoice($purchase_invoice)
	{
		$purchase_invoice['id']=$this->nextid('sh_purchase_invoices');

		$purchase_invoice['ctime']=$purchase_invoice['mtime']=get_gmt_time();


		if($this->insert_row('sh_purchase_invoices', $purchase_invoice))
		{
			return $purchase_invoice['id'];
		}
		return false;
	}

	/**
	 * Update a purchase_invoice
	 *
	 * @param Array $purchase_invoice Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_purchase_invoice($purchase_invoice)
	{

		$purchase_invoice['mtime']=get_gmt_time();

		return $this->update_row('sh_purchase_invoices', 'id', $purchase_invoice);
	}


	/**
	 * Delete a purchase_invoice
	 *
	 * @param Int $purchase_invoice_id ID of the purchase_invoice
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_purchase_invoice($purchase_invoice_id)
	{
		return $this->query("DELETE FROM sh_purchase_invoices WHERE id=$purchase_invoice_id");
	}


	/**
	 * Gets a purchase_invoice record
	 *
	 * @param Int $purchase_invoice_id ID of the purchase_invoice
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_purchase_invoice($purchase_invoice_id)
	{
		$this->query("SELECT * FROM sh_purchase_invoices WHERE id=$purchase_invoice_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a purchase_invoice record by the name field
	 *
	 * @param String $name Name of the purchase_invoice
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_purchase_invoice_by_name($name)
	{
		$this->query("SELECT * FROM sh_purchase_invoices WHERE purchase_invoice_name='$name'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets all purchase_invoices
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_purchase_invoices($start=0, $offset=0, $sortfield='id', $sortorder='ASC', $params=array(), $groups=array(), $statuses=array())
	{
		$sql = "SELECT i.*, c.code AS purchase_code, s.name AS status_name FROM sh_purchase_invoices i ".
		"INNER JOIN sh_purchase_codes c ON c.id=i.purchase_code_id ".
		"INNER JOIN sh_pi_statuses s ON s.id=i.status_id ";


		if(count($params))
		{
			$sql .= " WHERE ".implode(' AND ', $params);
			$group_where = " AND";
		}
		else
		{
			$group_where = " WHERE";
		}
		if($group_count = count($groups))
		{
			if($group_count>0)
			{
				$sql .= $group_where." (i.group_id IN (".implode(',', $groups)."))";
			}
		}

		if(count($statuses))
		{
			$sql .= " AND status_id IN (".implode(',', $statuses).")";
		}

		$sql .= " ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}


	/*function get_purchase_invoices_total($params=array(), $groups=array())
	{
		$sql = "SELECT SUM(total) AS total, SUM(vat) AS vat FROM sh_purchase_invoices";


		if(count($params))
		{
			$sql .= " WHERE ".implode(' AND ', $params);
			$group_where = " AND";
		}
		else
		{
			$group_where = " WHERE";
		}
		if($group_count = count($groups))
		{
			if($group_count>0)
			{
				$sql .= $group_where." (group_id IN (".implode(',', $groups)."))";
			}
		}

		$this->query($sql);
		$this->next_record();

		return $this->record;
	}*/




	function get_purchase_invoices_total_for_cost_code($purchase_code_id, $params, $groups, $statuses){
		$sql = "SELECT SUM(amount*price) AS total FROM sh_pi_charges c ".
			"INNER JOIN sh_purchase_invoices i ON c.purchase_invoice_id=i.id ".
			"WHERE c.purchase_code_id=$purchase_code_id";

		if(count($params))
		{
			$sql .= " AND ".implode(' AND ', $params);
		}
		if($group_count = count($groups))
		{
			if($group_count>0)
			{
				$sql .= " AND (group_id IN (".implode(',', $groups)."))";
			}
		}

		$credits=false;
		$new_statuses=array();
		foreach($statuses as $status_id)
		{
			if($status_id==4)
			{
				$credits=true;
			}else
			{
				$new_statuses[]=$status_id;
			}
		}



		$pos_total = 0;

		if(count($new_statuses))
		{
			$sql1 = $sql." AND status_id IN (".implode(',', $new_statuses).")";
			$this->query($sql1);
			$this->next_record();
			$pos_total = $this->f('total');
		}

		if($credits)
		{
			$sql2 = $sql." AND i.status_id=4";
			$this->query($sql2);
			$this->next_record();
			$pos_total -= $this->f('total');
		}

		return $pos_total;
	}


















	/**
	 * Add a pi_charge
	 *
	 * @param Array $pi_charge Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_pi_charge($pi_charge)
	{
		$pi_charge['id']=$this->nextid('sh_pi_charges');


		if($this->insert_row('sh_pi_charges', $pi_charge))
		{
			return $pi_charge['id'];
		}
		return false;
	}

	/**
	 * Update a pi_charge
	 *
	 * @param Array $pi_charge Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_pi_charge($pi_charge)
	{

		return $this->update_row('sh_pi_charges', 'id', $pi_charge);
	}


	/**
	 * Delete a pi_charge
	 *
	 * @param Int $pi_charge_id ID of the pi_charge
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_pi_charge($pi_charge_id)
	{
		return $this->query("DELETE FROM sh_pi_charges WHERE id=$pi_charge_id");
	}


	/**
	 * Gets a pi_charge record
	 *
	 * @param Int $pi_charge_id ID of the pi_charge
	 *
	 * @access public
	 * @return Array record properties
	 */

	function get_pi_charge($pi_charge_id)
	{
		$this->query("SELECT * FROM sh_pi_charges WHERE id=$pi_charge_id");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	/*
	 * Gets all charges
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_pi_charges($purchase_invoice_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	{
		$sql = "SELECT poc.*, pc.code AS cost_code FROM sh_pi_charges poc LEFT JOIN sh_purchase_codes pc ON pc.id=poc.purchase_code_id WHERE purchase_invoice_id=$purchase_invoice_id ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}



	function get_purchase_invoice_total($purchase_invoice_id)
	{
		$sql="SELECT SUM(price*amount) FROM sh_pi_charges WHERE purchase_invoice_id=$purchase_invoice_id";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->f(0);
		}
		return false;
	}

	function sync_purchase_invoice_total($purchase_invoice_id)
	{
		$purchase_invoice['id']=$purchase_invoice_id;
		$purchase_invoice['total']=$this->get_purchase_invoice_total($purchase_invoice_id);
		$this->update_purchase_invoice($purchase_invoice);
	}






}
