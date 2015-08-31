<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * The functions below are copied from AvaTax.php.  That file cannot be included because
 * it defines the function __autoload(), which Magento already uses.
 */

function EnsureIsArray( $obj ) 
{
    if( is_object($obj)) 
	{
        $item[0] = $obj;
    } 
	else 
	{
        $item = (array)$obj;
    }
    return $item;
}

/**
* Takes xml as a string and returns it nicely indented
*
* @param string $xml The xml to beautify
* @param boolean $html_output If the xml should be formatted for display on an html page
* @return string The beautified xml
*/
function xml_pretty_printer($xml, $html_output=FALSE)
{
    $xml_obj = new SimpleXMLElement($xml);
    $xml_lines = explode("n", $xml_obj->asXML());
    $indent_level = 0;
    
    $new_xml_lines = array();
    foreach ($xml_lines as $xml_line) {
        if (preg_match('#(<[a-z0-9:-]+((s+[a-z0-9:-]+="[^"]+")*)?>.*<s*/s*[^>]+>)|(<[a-z0-9:-]+((s+[a-z0-9:-]+="[^"]+")*)?s*/s*>)#i', $xml_line)) {
            $new_line = str_pad('', $indent_level*4) . $xml_line;
            $new_xml_lines[] = $new_line;
        } elseif (preg_match('#<[a-z0-9:-]+((s+[a-z0-9:-]+="[^"]+")*)?>#i', $xml_line)) {
            $new_line = str_pad('', $indent_level*4) . $xml_line;
            $indent_level++;
            $new_xml_lines[] = $new_line;
        } elseif (preg_match('#<s*/s*[^>/]+>#i', $xml_line)) {
            $indent_level--;
            if (trim($new_xml_lines[sizeof($new_xml_lines)-1]) == trim(str_replace("/", "", $xml_line))) {
                $new_xml_lines[sizeof($new_xml_lines)-1] .= $xml_line;
            } else {
                $new_line = str_pad('', $indent_level*4) . $xml_line;
                $new_xml_lines[] = $new_line;
            }
        } else {
            $new_line = str_pad('', $indent_level*4) . $xml_line;
            $new_xml_lines[] = $new_line;
        }
    }
    
    $xml = join("n", $new_xml_lines);
    return ($html_output) ? '<pre>' . htmlentities($xml) . '</pre>' : $xml;
}
