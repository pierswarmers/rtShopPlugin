<?php
/*
 * This file is part of the reditype package.
 * (c) digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopNumberToolkits
 *
 * @package    steerCms
 * @subpackage steerCmsPluginTools
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopNumberToolkit
{
  /**
   *
   * @param integer String length
   * @return string Random alpha/numeric
   */
  static public function getReferenceNumber($length=10) {
    $newstring="";
    if($length>0) {
      while(strlen($newstring)<$length) {
        $randnum = mt_rand(0,61);
        if ($randnum < 10) {
          $newstring.=chr($randnum+48);
        }
        elseif ($randnum < 36) {
          $newstring.=chr($randnum+55);
        }
        else {
          $newstring.=chr($randnum+61);
        }
      }
    }
    return strtoupper($newstring);
  }
}
