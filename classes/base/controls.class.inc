<?php
/**
* @copyright Intermesh 2003
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.36 $ $Date: 2006/03/06 08:48:30 $
*
*  This program is free software; you can redistribute it and/or modify it
*  under the terms of the GNU General Public License as published by the
*  Free Software Foundation; either version 2 of the License, or (at your
*  option) any later version.
*/

require_once($GO_CONFIG->class_path."base/controls/tooltip.class.inc"); 
require_once($GO_CONFIG->class_path."base/controls/html_element.class.inc");
require_once($GO_CONFIG->class_path."base/controls/table.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/overlib.class.inc" );//deprecated use tooltip
require_once($GO_CONFIG->class_path."base/controls/select.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/color_selector.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/date_picker.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/checkbox.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/radio_list.class.inc" );//deprecated use radiogroup
require_once($GO_CONFIG->class_path."base/controls/acl.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/statusbar.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/dropbox.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/htmlarea.class.inc" );//deprecated use htmleditor
require_once($GO_CONFIG->class_path."base/controls/tabtable.class.inc" );//deprecated use tabstrip
require_once($GO_CONFIG->class_path."base/controls/datagrid.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/dbrecord.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/treeview.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/image.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/hyperlink.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/input.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/button.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/textarea.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/tabstrip.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/form.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/object_select.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/button_menu.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/radiogroup.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/links_list.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/autocomplete_select.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/dynamic_tabstrip.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/select_users.class.inc" );
require_once($GO_CONFIG->class_path."base/controls/htmleditor.class.inc" );

function html2html_element($html)
{

    if (preg_match("/<(.*?)>(.*?)<(.*?)>/", $html, $matches)) {

        $tag = explode(" ", trim($matches[1]));

        $return_element = new html_element($tag[0], $matches[2]);

        array_shift($tag);

        foreach ($tag as $value) {

            $tmp = explode("=", $value);

            $tmp[1] = preg_replace("/[\"']/", "", $tmp[1]);

            $return_element->set_attribute(trim($tmp[0]), trim($tmp[1]));

        }

        return $return_element;

    } else {

        return false;

    }

}

?>
