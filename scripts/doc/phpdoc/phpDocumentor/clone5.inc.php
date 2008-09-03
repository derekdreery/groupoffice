<?php
/**
 * Object clone method
 *
 * phpDocumentor :: automatic documentation generator
 * 
 * PHP versions 4 and 5
 *
 * Copyright (c) 2001-2006 Gregory Beaver
 * 
 * LICENSE:
 * 
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @category  ToolsAndUtilities
 * @package   phpDocumentor
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2001-2006 Gregory Beaver
 * @license   http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version   CVS: $Id: clone5.inc.php,v 1.3 2007/09/30 02:08:08 ashnazg Exp $
 * @filesource
 * @link      http://www.phpdoc.org
 * @link      http://pear.php.net/PhpDocumentor
 * @since     1.0rc1
 * @todo      CS cleanup - change package to PhpDocumentor
 */
/**
 * Clone an object in PHP 5
 *
 * @param object $obj the object to be cloned
 *
 * @return object the new clone
 * @ignore
 * @todo CS cleanup - rename function to PhpDocumentor_clone
 */
function phpDocumentor_clone($obj)
{
    return clone $obj;
}

?>
