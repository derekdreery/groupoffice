<?php

namespace Sabre\VObject;

use
    ArrayObject;

/**
 * VObject Parameter
 *
 * This class represents a parameter. A parameter is always tied to a property.
 * In the case of:
 *   DTSTART;VALUE=DATE:20101108
 * VALUE=DATE would be the parameter name and value.
 *
 * @copyright Copyright (C) 2007-2013 fruux GmbH (https://fruux.com/).
 * @author Evert Pot (http://evertpot.com/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class Parameter extends Node {

    /**
     * Parameter name
     *
     * @var string
     */
    public $name;

    /**
     * vCard 2.1 allows parameters to be encoded without a name.
     *
     * We can deduce the parameter name based on it's value.
     *
     * @var bool
     */
    public $noName = false;

    /**
     * Parameter value
     *
     * @var string
     */
    protected $value;

    /**
     * Sets up the object.
     *
     * It's recommended to use the create:: factory method instead.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct(Document $root, $name, $value = null) {

        $this->name = strtoupper($name);
        $this->root = $root;
        if (is_null($name)) {
            $this->noName = true;
            $this->name = static::guessParameterNameByValue($value);
        }
        $this->setValue($value);
    }

    /**
     * Try to guess property name by value, can be used for vCard 2.1 nameless parameters.
     *
     * Figuring out what the name should have been. Note that a ton of
     * these are rather silly in 2013 and would probably rarely be
     * used, but we like to be complete.
     *
     * @param string $value
     * @return string
     */
    public static function guessParameterNameByValue($value) {
        switch(strtoupper($value)) {

            // Encodings
            case '7-BIT' :
            case 'QUOTED-PRINTABLE' :
            case 'BASE64' :
                $name = 'ENCODING';
                break;

            // Common types
            case 'WORK' :
            case 'HOME' :
            case 'PREF' :

                // Delivery Label Type
            case 'DOM' :
            case 'INTL' :
            case 'POSTAL' :
            case 'PARCEL' :

                // Telephone types
            case 'VOICE' :
            case 'FAX' :
            case 'MSG' :
            case 'CELL' :
            case 'PAGER' :
            case 'BBS' :
            case 'MODEM' :
            case 'CAR' :
            case 'ISDN' :
            case 'VIDEO' :

                // EMAIL types (lol)
            case 'AOL' :
            case 'APPLELINK' :
            case 'ATTMAIL' :
            case 'CIS' :
            case 'EWORLD' :
            case 'INTERNET' :
            case 'IBMMAIL' :
            case 'MCIMAIL' :
            case 'POWERSHARE' :
            case 'PRODIGY' :
            case 'TLX' :
            case 'X400' :

                // Photo / Logo format types
            case 'GIF' :
            case 'CGM' :
            case 'WMF' :
            case 'BMP' :
            case 'DIB' :
            case 'PICT' :
            case 'TIFF' :
            case 'PDF ':
            case 'PS' :
            case 'JPEG' :
            case 'MPEG' :
            case 'MPEG2' :
            case 'AVI' :
            case 'QTIME' :

                // Sound Digital Audio Type
            case 'WAVE' :
            case 'PCM' :
            case 'AIFF' :

                // Key types
            case 'X509' :
            case 'PGP' :
                $name = 'TYPE';
                break;

            // Value types
            case 'INLINE' :
            case 'URL' :
            case 'CONTENT-ID' :
            case 'CID' :
                $name = 'VALUE';
                break;

            default:
                $name = '';
        }

        return $name;
    }

    /**
     * Updates the current value.
     *
     * This may be either a single, or multiple strings in an array.
     *
     * @param string|array $value
     * @return void
     */
    public function setValue($value) {

        $this->value = $value;

    }

    /**
     * Returns the current value
     *
     * This method will always return a string, or null. If there were multiple
     * values, it will automatically concatinate them (separated by comma).
     *
     * @return string|null
     */
    public function getValue() {

        if (is_array($this->value)) {
            return implode(',' , $this->value);
        } else {
            return $this->value;
        }

    }

    /**
     * Sets multiple values for this parameter.
     *
     * @param array $value
     * @return void
     */
    public function setParts(array $value) {

        $this->value = $value;

    }

    /**
     * Returns all values for this parameter.
     *
     * If there were no values, an empty array will be returned.
     *
     * @return array
     */
    public function getParts() {

        if (is_array($this->value)) {
            return $this->value;
        } elseif (is_null($this->value)) {
            return array();
        } else {
            return array($this->value);
        }

    }

    /**
     * Adds a value to this parameter
     *
     * If the argument is specified as an array, all items will be added to the
     * parameter value list.
     *
     * @param string|array $part
     * @return void
     */
    public function addValue($part) {

        if (is_null($this->value)) {
            $this->value = $part;
        } else {
            $this->value = array_merge((array)$this->value, (array)$part);
        }

    }

    /**
     * Checks if this parameter contains the specified value.
     *
     * This is a case-insensitive match. It makes sense to call this for for
     * instance the TYPE parameter, to see if it contains a keyword such as
     * 'WORK' or 'FAX'.
     *
     * @param string $value
     * @return bool
     */
    public function has($value) {

        return in_array(
            strtolower($value),
            array_map('strtolower', (array)$this->value)
        );

    }

    /**
     * Turns the object back into a serialized blob.
     *
     * @return string
     */
    public function serialize() {

        $value = $this->getParts();

        if (count($value)===0) {
            return $this->name;
        }

        return ($this->noName?:$this->name . '=') . array_reduce($value, function($out, $item) {

            if (!is_null($out)) $out.=',';

            // If there's no special characters in the string, we'll use the simple
            // format
            if (!preg_match('#(?: [\n":;\^,] )#x', $item)) {
                return $out.$item;
            } else {
                // Enclosing in double-quotes, and using RFC6868 for encoding any
                // special characters
                $out.='"' . strtr($item, array(
                    '^'  => '^^',
                    "\n" => '^n',
                    '"'  => '^\'',
                )) . '"';
                return $out;
            }

        });

    }

    /**
     * This method returns an array, with the representation as it should be
     * encoded in json. This is used to create jCard or jCal documents.
     *
     * @return array
     */
    public function jsonSerialize() {

        return $this->value;

    }

    /**
     * Called when this object is being cast to a string
     *
     * @return string
     */
    public function __toString() {

        return $this->getValue();

    }

    /**
     * Returns the iterator for this object
     *
     * @return ElementList
     */
    public function getIterator() {

        if (!is_null($this->iterator))
            return $this->iterator;

        return $this->iterator = new ArrayObject((array)$this->value);

    }

}
