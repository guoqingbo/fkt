<?php
/**
 * require_once(dirname(__FILE__) . '/' . '/android/AndroidBroadcast.php');
 * require_once(dirname(__FILE__) . '/' . '/android/AndroidFilecast.php');
 * require_once(dirname(__FILE__) . '/' . '/android/AndroidGroupcast.php');
 * require_once(dirname(__FILE__) . '/' . '/android/AndroidUnicast.php');
 * require_once(dirname(__FILE__) . '/' . '/android/AndroidCustomizedcast.php');**/
require_once(dirname(__FILE__) . '/' . '/ios/IOSBroadcast.php');
//require_once(dirname(__FILE__) . '/' . '/ios/IOSFilecast.php');
//require_once(dirname(__FILE__) . '/' . '/ios/IOSGroupcast.php');
require_once(dirname(__FILE__) . '/' . '/ios/IOSUnicast.php');

//require_once(dirname(__FILE__) . '/' . '/ios/IOSCustomizedcast.php');

class Ios_Push
{
  protected $appkey = NULL;
  protected $appMasterSecret = NULL;
  protected $timestamp = NULL;
  protected $validation_token = NULL;

  function __construct()
  {
    //$this->appkey = '552f717dfd98c52235000b1c';
    //$this->appMasterSecret = 'nskriqtixzhm3pvidl0zzdq7j6kjusac';
    $this->timestamp = strval(time());
  }

  function set_app($type = '')
  {
    if ($type) {
      $this->appkey = '5641c61367e58edc70001d9d';
      $this->appMasterSecret = 'egwdjbtlox0h73bbfblkco9nt0kixmiw';
    } else {
      $this->appkey = '552f717dfd98c52235000b1c';
      $this->appMasterSecret = 'nskriqtixzhm3pvidl0zzdq7j6kjusac';
    }
  }

  function sendIOSBroadcast($array)
  {
    try {
      $brocast = new IOSBroadcast();
      $brocast->setAppMasterSecret($this->appMasterSecret);
      $brocast->setPredefinedKeyValue("appkey", $this->appkey);
      $brocast->setPredefinedKeyValue("timestamp", $this->timestamp);
      $brocast->setPredefinedKeyValue("alert", $array['alert']);
      $brocast->setPredefinedKeyValue("badge", $array['badge']);
      $brocast->setPredefinedKeyValue("sound", "chime");
      $brocast->setPredefinedKeyValue("production_mode", "true");
      $brocast->setCustomizedField('uri', json_encode($array['field']));
      $result = $brocast->send();
      return $result;
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  function sendIOSUnicast($array)
  {
    try {
      $unicast = new IOSUnicast();
      $unicast->setAppMasterSecret($this->appMasterSecret);
      $unicast->setPredefinedKeyValue("appkey", $this->appkey);
      $unicast->setPredefinedKeyValue("timestamp", $this->timestamp);
      // Set your device tokens here
      $unicast->setPredefinedKeyValue("device_tokens", $array['device_tokens']);
      $unicast->setPredefinedKeyValue("alert", $array['alert']);
      $unicast->setPredefinedKeyValue("badge", $array['badge']);
      $unicast->setPredefinedKeyValue("sound", "chime");
      // Set 'production_mode' to 'true' if your app is under production mode
      $unicast->setPredefinedKeyValue("production_mode", "true");
      $unicast->setCustomizedField('uri', json_encode($array['field']));
      $result = $unicast->send();
      return $result;
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
}
