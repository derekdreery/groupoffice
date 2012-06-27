<?php
/**
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 * @package GO.base 
 */

//require vendor lib SabreDav vobject
//require_once(GO::config()->root_path.'go/vendor/SabreDAV/lib/Sabre/VObject/includes.php');

class GO_Base_VObject_Parameter extends Sabre_VObject_Parameter {

    /**
     * Turns the object back into a serialized blob. 
     * 
     * @return string 
     */
    public function serialize() {

        $src = array(
            '\\',
            "\n",
            ';',
        );
        $out = array(
            '\\\\',
            '\n',
            '\;',
        );

        return $this->name . '=' . str_replace($src, $out, $this->value);

    }

}
