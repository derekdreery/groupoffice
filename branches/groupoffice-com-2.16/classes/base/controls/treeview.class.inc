<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.28 $ $Date: 2006/06/26 11:42:54 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

/**
 * Create a hierarchical structured view of various items.
 * The treeview object needs to be stored in the session and placed inside a form
 * that uses POST data to work.
 * 
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
 
 class treeview
 {
 	/**
	* Internal ID of the treeview. Used to identify POST variables that are sent by 
	* the treeview
	*
	* @var     String
	* @access  private
	*/	
 	var $id;
 	
 	/**
	* The nodes that represent the root of the tree.
	*
	* @var     Array
	* @access  private
	*/	
 	var $rootNodes = array();
 	
 	/**
	* Associative array with the Node ID as index that references a node object.
	*
	* @var     Array
	* @access  private
	*/	
 	var $nodes = array();
 	
  /**
	* The node state is an array of node ID's that are set to true or false.
	* True means the node is open.
	*
	* @var     Array
	* @access  private
	*/	
 	var $nodeState = null;
 		
 	
 	/**
	* Will expand all nodes if set to true by expandAll();
	*
	* @var     bool
	* @access  private
	*/	
	
 	var $expandAll = false;

 	/**
	* Will collapse all nodes if set to true by collapseAll();
	*
	* @var     bool
	* @access  private
	*/	
 	
 	var $collapseAll = false;
 	
 	
 	
 	
   /**
   * Constructor: Initializes treeview
   * 
   * @access public
   * 
   * @return void
   */
 	function treeview($id)
 	{
 		$this->id=$id;
 		$_SESSION[$this->id]['nodestate'] = isset($_SESSION[$this->id]['nodestate']) ? $_SESSION[$this->id]['nodestate'] : array();
 		$this->nodeState = &$_SESSION[$this->id]['nodestate'];
 		
 		if(isset($_POST[$this->id]['collapseId']) && $_POST[$this->id]['collapseId'] != '')
 		{
 			$this->setClosed(smart_stripslashes($_POST[$this->id]['collapseId']));
 		}elseif(isset($_POST[$this->id]['expandId']) && $_POST[$this->id]['expandId'] != '')
 		{
 			$this->setOpen(smart_stripslashes($_POST[$this->id]['expandId']));
 		}
 	}
 	
 	 /**
   * Reset's the treeview exept for the node state so opened nodes will stay open.
   * 
   * @access public
   * @return void
   */
 	function reset()
 	{
 		$this->rootNodes = array();;
		$this->nodes = array();
 		$this->treeview();
 	}
 	
 	function expandAll()
 	{
 		$this->expandAll = true;
 	}
 	
 	function collapseAll()
 	{
  		$this->collapseAll = true;
  		$this->nodeState=array();
 	}
 	
 	 	/**
   * Add's a node that has no parent to the treeview
   * 
   * @param treenode $treenode A node object
   * @access public
   * @return void
   */
 	function addRootNode($treenode)
 	{
 		$treenode->levelPadding=0;
 		$this->rootNodes[] = $treenode;
 	}
 	
 	/**
	* Indicates if the treeview was active
  * @access public
  * @return bool
  */
 	function isSubmitted()
 	{
 		return (isset($_POST[$this->id]['submitted']) && $_POST[$this->id]['submitted'] == 'true');
 	}
 	
 	 /**
   * Builds the treeview in HTML format.
   * 
   * @param string $formName The name of the form the treeview is in.
   * @access public
   * @return string Treeview HTML
   */
 	function getTreeview($formName='0')
 	{ 	
 		global $GO_CONFIG;
 		
 		$input = new input('hidden', $this->id.'[expandId]', '', false);
 		$tree = $input->get_html();
 		
 		$input = new input('hidden', $this->id.'[collapseId]', '', false); 		
 		$tree .= $input->get_html();
 		
 		$input = new input('hidden', $this->id.'[submitted]', 'false', false); 		
 		$tree .= $input->get_html();
 		
 		for($i=0;$i<count($this->rootNodes);$i++)
 		{
 			$tree .= $this->rootNodes[$i]->getNode();
 		}
 		
 		$tree .= '<script type="text/javascript">
 		 function expand_'.$this->id.'(nodeId){ 		 
 		 	document.forms["'.$formName.'"].elements["'.$this->id.'[submitted]"].value="true";
 			document.forms["'.$formName.'"].elements["'.$this->id.'[expandId]"].value=nodeId;
 		 	document.forms["'.$formName.'"].submit();
		}'.
		 'function collapse_'.$this->id.'(nodeId){		 
		 	document.forms["'.$formName.'"].elements["'.$this->id.'[submitted]"].value="true";
 		 	document.forms["'.$formName.'"].elements["'.$this->id.'[collapseId]"].value=nodeId;
 		 	document.forms["'.$formName.'"].submit();
		}'.
 		'</script>';
 		return $tree;
 	}
 	
 	function get_expand_handler($nodeId)
 	{
 		return 'expand_'.$this->id.'(\''.$nodeId.'\');';
 	}

 	function get_collapse_handler($nodeId)
 	{
 		return 'collapse_'.$this->id.'(\''.$nodeId.'\');';
 	} 
   /**
   * Sets a node in state opened
   * 
   * @param string $nodeId The node ID
   * @param string $recurse If this is true it will also set all parent nodes open.
   * @access public
   * @return bool True if open
   */	
 	function setOpen($nodeId)
 	{
 		$this->nodeState[$nodeId] = true;
 		$this->nodes[$nodeId]->open=true;
 	}
 	
   /**
   * Sets a node in state closed
   * 
   * @param string $nodeId The node ID
   * @access public
   * @return bool True if open
   */		
 	function setClosed($nodeId)
 	{
 		$this->nodes[$nodeId]->open=false;
 		$this->nodeState[$nodeId] = false;
 	}
 	
 	/**
   * Tells if a node is open or not
   * 
   * @param string $nodeId The node ID
   * @access public
   * @return bool True if open
   */
 	function nodeIsOpen($nodeId)
 	{
 		if($this->expandAll)
 		{
 			$this->setOpen($nodeId);
 		}
 		return (isset($this->nodeState[$nodeId])  &&  $this->nodeState[$nodeId] == true);
 	}
 }


/**
 * Create a hierarchical structured view of various items.
 * The treeview object needs to be stored in the session and placed inside a form
 * that uses POST data to work.
 * 
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
 
class treenode
{
 	/**
	* Internal ID of the treenode
	*
	* @var     String
	* @access  private
	*/	
	var $id;
	
 	/**
	* The HTML representation of the treenode in closed state
	*
	* @var     String
	* @access  private
	*/		
	var $htmlClosed = '';

 	/**
	* The HTML representation of the treenode in opened state
	*
	* @var     String
	* @access  private
	*/		
	var $htmlOpened = '';
	
	/**
	* The childnodes of this treenode in an array
	*
	* @var     array
	* @access  private
	*/		
	var $childnodes = array();
	
	
	
	/**
	* The innerHTML code for the hyperlink used to close this treenode
	*
	* @var     string
	* @access  private
	*/		
	var $openedNodeImage = '-';	

	/**
	* The innerHTML code for the hyperlink used to open this treenode
	*
	* @var     string
	* @access  private
	*/		
	var $closedNodeImage = '+';
	
	/**
	* The HTML code used to replace the open/close hyperlink when there are no children
	*
	* @var     string
	* @access  private
	*/		
	var $noChildsImage = '&nbsp;';
	
	/**
	* The number of pixels to move a treenode when we go to a deeper level in the tree
	*
	* @var     string
	* @access  private
	*/		
	var $levelPadding = 20;

	/**
	* Indicates whether this note is open or not
	*
	* @var     bool
	* @access  private
	*/			
	var $open = false;
	
	/**
	* The ID of the parent node. If not set then the parent is the root
	*
	* @var     Object
	* @access  private
	*/			
	var $parentNode;
	
	/**
	* If the childnodes are not loaded into the node then we should draw a plus sign
	*
	* @var     bool
	* @access  public
	*/		
	var $childnodesNotLoaded = false;	
	
	var $treeview;

   /**
   * Constructor: Initializes treenode
   * 
   * @access public
   * 
   * @return void
   */
  function treenode(&$treeview, $id, $htmlClosed=null, $htmlOpened=null, $open=false, $alwaysOpen=false)
  {
  	global $GO_THEME;
  	
  	$this->id = $id;  	 	
  	
  	$this->open = $alwaysOpen ? true : $open;  	
  	$this->alwaysOpen = $alwaysOpen;  	  	
  	$this->htmlClosed = $htmlClosed;  	
  	$this->htmlOpened = isset($htmlOpened) ? $htmlOpened : $htmlClosed;  	
  	
  	if(!isset($this->htmlClosed))
  	{	
  		$this->alwaysOpen=true;
  		$this->open=true;
  	}
  	
  	$this->openedNodeImage = '<img src="'.$GO_THEME->images['min_node'].'" style="border:0px;margin-right:5px;" align="absmiddle" />';
  	$this->closedNodeImage = '<img src="'.$GO_THEME->images['plus_node'].'" style="border:0px;margin-right:5px;" align="absmiddle" />';
  	$this->noChildsImage = '<img src="'.$GO_THEME->images['blank'].'" style="border:0px;margin-right:5px;" align="absmiddle" />';
  	
  	$this->treeview = &$treeview;
  	$this->treeview->nodes[$id] = &$this;
    	
	  if($treeview->expandAll)
		{
			$treeview->setOpen($this->id);
		}elseif($treeview->collapseAll)
		{
			$treeview->setClosed($this->id);
		}elseif(isset($treeview->nodeState[$this->id]))
  	{
  		$this->open = $treeview->nodeIsOpen($this->id);
  	}elseif($this->open)
  	{
  		//treeview is not yet aware of this node's state
  		$treeview->setOpen($this->id);
  	}else
  	{
  		//treeview is not yet aware of this node's state
  		$treeview->setClosed($this->id);  	
  	}
  }
  
  function addNode($treenode)
  {
  	if(!isset($this->htmlClosed))
  	{	
  		$treenode->levelPadding=0;
  	}
  	$treenode->parentNode=&$this;
  	$this->childnodes[] = $treenode;
  }
  
  function getNode()
  {  
  	$node = '<div id="tn_'.$this->id.'" style="padding-left: '.$this->levelPadding.'px;display:block;white-space:nowrap">';
  	if($this->treeview->nodeIsOpen($this->id))
  	{
  		if(!$this->alwaysOpen && isset($this->htmlOpened) && (count($this->childnodes)))
	  	{
	  		$node .= '<a style="cursor:default;" href="javascript:collapse_'.$this->treeview->id.'(\''.addslashes($this->id).'\');">'.$this->openedNodeImage.'</a>';
	  	}else
	  	{
	  		$node .= $this->noChildsImage;
	  	}
	  	$node .= $this->htmlOpened;
	  	
	  	if(count($this->childnodes))
	  	{  	
		  	for($i=0;$i<count($this->childnodes);$i++)
		  	{
		  		$node .= $this->childnodes[$i]->getNode();
		  	}	  	
	  	}
  	}else
  	{
	  	if(count($this->childnodes) || $this->childnodesNotLoaded)
	  	{
	  		$node .= '<a style="cursor:default;" href="javascript:expand_'.$this->treeview->id.'(\''.addslashes($this->id).'\');">'.$this->closedNodeImage.'</a>';
	  	}else
	  	{
	  		$node .= $this->noChildsImage;
	  	}  	
	  	$node .= $this->htmlClosed;
	}
  	return $node.'</div>';
  }
}
?>
