<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.


After calling this function you can set the following parameters:
name: The name of the control to validate (required)
input: The string to validate this is the name of the control with an $ sign(optional)
expression: Regular expression matching (optional)
min_length: Minimum length of the input (optional)
max_length: Maximum lentgh of the input (optional)
required: indicates if the input is required (optional)
error_required: error string when input is not given but required bla bla bla (optional, will use default value)
match1 and match2 can be set to give an error when they do not match such as with passwords.
error_min_length: bla bla bla (optional, will use default value)
error max_length: bla bla bla (optional, will use default value)
error_expression: bla bla bla (optional, will use default value)
error_match: bla bla bla (optional, will use default value)

after setting these vars call function validate_input().
The function will output errors to the page you are calling it from.
*/

class validate
{
        var $validated;
        var $error;
        var $name;
        var $input;
        var $expression;
        var $min_length;
        var $max_length;
        var $required;
        var $match1;
        var $match2;
        var $error_match;
        var $error_required;
        var $error_min_length;
        var $error_max_length;
        var $error_expression;

        function validate()
        {
                $this->validated = true;
                $this->min_length = "";
                $this->max_length = "";
                $this->required = false;
                $this->expression = "";
                $this->error=false;
        }

        function validate_input()
        {
			$this->input = trim($this->input);
                if ($this->name == "")
                {
                        echo "DEBUG: name in validate class not set.<br />";
                }
                if ($this->required == true && $this->input == "")
                {
                        if (isset($this->error[$this->name]))
                        {
                                $this->error[$this->name] .=  $this->error_required."<br />";
                        }else
                        {
                                $this->error[$this->name] =  $this->error_required."<br />";
                        }
                        $this->validated = false;
                }else
                {
                        if ($this->min_length != "" && $this->input != "")
                        {
                                if (strlen($this->input) < $this->min_length)
                                {
                                        if (isset($this->error[$this->name]))
                                        {
                                                $this->error[$this->name] .= $this->error_min_length." ".$this->min_length."<br />";
                                        }else
                                        {
                                                $this->error[$this->name] = $this->error_min_length." ".$this->min_length."<br />";
                                        }
                                        $this->validated = false;
                                }
                        }
                }

                if ($this->max_length != "")
                {
                        if (strlen($this->input) > $this->max_length)
                        {
                                if (isset($this->error[$this->name]))
                                {
                                        $this->error[$this->name] .= $this->error_max_length." ".$this->max_length."<br />";
                                }else
                                {
                                        $this->error[$this->name] = $this->error_max_length." ".$this->max_length."<br />";
                                }
                                $this->validated = false;
                        }
                }

                if ($this->expression != "" && !isset($this->error[$this->name]) && $this->input != '')
                {
                        if (!eregi($this->expression, $this->input))
                        {
                                if (isset($this->error[$this->name]))
                                {
                                        $this->error[$this->name] .= $this->error_expression."<br />";
                                }else
                                {
                                        $this->error[$this->name] = $this->error_expression."<br />";
                                }
                                $this->validated = false;
                        }
                }

                if ($this->match1 != "" && $this->match2 != "" && $this->match1 != $this->match2)
                {
                        if (isset($this->error[$this->name]))
                        {
                                $this->error[$this->name] .= $this->error_match."<br />";
                        }else
                        {
                                $this->error[$this->name] = $this->error_match."<br />";
                        }
                        $this->validated = false;

                }
                $this->min_length = "";
                $this->max_length = "";
                $this->required = false;
                $this->expression = "";
                return true;
        }
}
?>
