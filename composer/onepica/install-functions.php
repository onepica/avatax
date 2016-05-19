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

// copy recursive
function copy_recursive($source, $dest)
{
    if (is_dir($source)) {
        $dirHandle = opendir($source);
        while ($file = readdir($dirHandle)) {
            if (!is_dir($dest)) {
                mkdir($dest);
            }

            if ($file != "." && $file != "..") {
                if (is_dir($source . "/" . $file)) {
                    if (!is_dir($dest . "/" . $file)) {
                        mkdir($dest . "/" . $file);
                    }
                    copy_recursive($source . "/" . $file, $dest . "/" . $file);
                } else {
                    copy($source . "/" . $file, $dest . "/" . $file);
                }
            }
        }
        closedir($dirHandle);
    } else {
        copy($source, $dest);
    }
}
