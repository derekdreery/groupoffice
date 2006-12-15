//this file contains additions for HTMLarea required by Group-Office

// Returns the next upper parent element (relative to current cursor-position) matching nodeName 
HTMLArea.prototype.getParentElementByName = function(nodeName) 
{ 
	var el = null; 
	var ancestors = this.getAllAncestors(); 
	for ( var j = 0; j < ancestors.length; j++) 
	{ 
		el =  (ancestors[j] && ancestors[j].nodeName.toLowerCase() == nodeName.toLowerCase()) ? ancestors[j] : null; 
		
		if (el) break; 
	} 
	return el;
}

HTMLArea.prototype.removeFormat = function(sel) { 

	// make one line 
	sel = sel.replace(/\r\n/g, ''); 
	sel = sel.replace(/\n/g, ''); 
	sel = sel.replace(/\r/g, ''); 
	sel = sel.replace(/\&nbsp\;/g,''); 

	// keep tags, strip attributes 
	sel = sel.replace(/ class=[^\s|>]*/gi,''); 
	sel = sel.replace(/ style=\"[^>]*\"/gi,''); 

	//clean up tags 
	sel = sel.replace(/<b [^>]*>/gi,'<b>'); 
	sel = sel.replace(/<i [^>]*>/gi,'<i>'); 
	sel = sel.replace(/<li [^>]*>/gi,'<li>'); 
	sel = sel.replace(/<ul [^>]*>/gi,'<ul>'); 

	// replace outdated tags 
	sel = sel.replace(/<b>/gi,'<strong>'); 
	sel = sel.replace(/<\/b>/gi,'</strong>'); 
	sel = sel.replace(/<i>/gi,'<em>'); 
	sel = sel.replace(/<\/i>/gi,'</em>'); 

	// kill unwanted tags 
	sel = sel.replace(/<\?xml:[^>]*>/g, ''); // Word xml 
	sel = sel.replace(/<\/?st1:[^>]*>/g,''); // Word SmartTags 
	sel = sel.replace(/<\/?[a-z]\:[^>]*>/g,''); // All other funny Word non-HTML stuff 
	sel = sel.replace(/<\/?font[^>]*>/gi,''); // Disable if you want to keep font formatting 
	sel = sel.replace(/<\/?span[^>]*>/gi,''); 
	sel = sel.replace(/<\/?div[^>]*>/gi,''); 

	//remove empty tags 
	sel = sel.replace(/<strong><\/strong>/gi,''); 
	sel = sel.replace(/<P[^>]*><\/P>/gi,''); 

	return sel;
} 