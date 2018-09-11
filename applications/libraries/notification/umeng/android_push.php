<?php
require_once(dirname(__FILE__) . '/' . '/android/AndroidBroadcast.php');
//require_once(dirname(__FILE__) . '/' . '/android/AndroidFilecast.php');
//require_once(dirname(__FILE__) . '/' . '/android/AndroidGroupcast.php');
require_once(dirname(__FILE__) . '/' . '/android/AndroidUnicast.php');
//require_once(dirname(__FILE__) . '/' . '/android/AndroidCustomizedcast.php');
//require_once(dirname(__FILE__) . '/' . '/ios/IOSBroadcast.php');
//require_once(dirname(__FILE__) . '/' . '/ios/IOSFilecast.php');
//require_once(dirname(__FILE__) . '/' . '/ios/IOSGroupcast.php');
//require_once(dirname(__FILE__) . '/' . '/ios/IOSUnicast.php');
//require_once(dirname(__FILE__) . '/' . '/ios/IOSCustomizedcast.php');

class Android_Push
{
  protected $appkey = NULL;
  protected $appMasterSecret = NULL;
  protected $timestamp = NULL;
  protected $validation_token = NULL;

  function __construct()
  {
    //$this->appkey = '552f720efd98c5e1e5001a97';
    //$this->appMasterSecret = 'desw9gi86fihdtgngojdxwnsbqwlgrny';
    $this->timestamp = strval(time());
  }

  function set_app($type = '')
  {
    if ($type) {
      $this->appkey = '5641c61367e58edc70001d9d';
      $this->appMasterSecret = 'egwdjbtlox0h73bbfblkco9nt0kixmiw';
    } else {
      $this->appkey = '552f720efd98c5e1e5001a97';
      $this->appMasterSecret = 'desw9gi86fihdtgngojdxwnsbqwlgrny';
    }
  }

  function sendAndroidBroadcast($array)
  {
    try {
      $brocast = new AndroidBroadcast();
      $brocast->setAppMasterSecret($this->appMasterSecret);
      $brocast->setPredefinedKeyValue("appkey", $this->appkey);
      $brocast->setPredefinedKeyValue("timestamp", $this->timestamp);
      if ($array['type']) {
        $brocast->setPredefinedKeyValue("ticker", "消息通知");
        $brocast->setPredefinedKeyValue("title", "消息通知");
      } else {
        $brocast->setPredefinedKeyValue("ticker", "消息通知");
        $brocast->setPredefinedKeyValue("title", "消息通知");
      }
      $brocast->setPredefinedKeyValue("text", $array['alert']);
      $brocast->setPredefinedKeyValue("after_open", "go_app");
      // Set 'production_mode' to 'false' if it's a test device.
      // For how to register a test device, please see the developer doc.
      $brocast->setPredefinedKeyValue("production_mode", "true");
      // [optional]Set extra fields
      $brocast->setExtraField('uri', json_encode($array['field']));
      return $brocast->send();
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  function sendAndroidUnicast($array)
  {
    try {
      $unicast = new AndroidUnicast();
      $unicast->setAppMasterSecret($this->appMasterSecret);
      $unicast->setPredefinedKeyValue("appkey", $this->appkey);
      $unicast->setPredefinedKeyValue("timestamp", $this->timestamp);
      // Set your device tokens here
      $unicast->setPredefinedKeyValue("device_tokens", $array['device_tokens']);
      if ($array['type']) {
        $unicast->setPredefinedKeyValue("ticker", "消息通知");
        $unicast->setPredefinedKeyValue("title", "消息通知");
      } else {
        $unicast->setPredefinedKeyValue("ticker", "消息通知");
        $unicast->setPredefinedKeyValue("title", "消息通知");
      }
      $unicast->setPredefinedKeyValue("text", $array['alert']);
      $unicast->setPredefinedKeyValue("after_open", "go_app");
      // Set 'production_mode' to 'false' if it's a test device.
      // For how to register a test device, please see the developer doc.
      $unicast->setPredefinedKeyValue("production_mode", "true");
      // Set extra fields
      $unicast->setExtraField('uri', json_encode($array['field']));
      $return = $unicast->send();
      //print("Send Success ".$return);
      return $return;
    } catch (Exception $e) {
      $return = $e->getMessage();
      //print("Sent Fail ".$return);
      return $return;
    }
  }
}
