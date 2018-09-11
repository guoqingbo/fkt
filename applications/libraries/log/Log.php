<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author user
 */
class Log
{
  public static function record($msg, $content, $filename)
  {
    $msg = '[' . date("Y-m-d H:i:s") . ']' . ' [message]' . $msg . ' [data]' . serialize($content) . "\r\n";
    //$msg = '['.  date("Y-m-d H:i:s").']'.' '.iconv('GBK','UTF-8',urldecode($content))."\r\n";
    $filename = PUBLIC_LIBRARIES_PATH . 'log/' . $filename . '_' . date("Y-m-d") . '.log';
    @file_put_contents($filename, $msg, FILE_APPEND);
  }
}
