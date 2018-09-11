<?php

class Android_Push
{
  private $_get_url = 'http://mobile.house365.com:8080/apn/notification_api.do';

  private $_app = 'fkt';

  private $_type = 1;

  private $_title = '';

  function __construct()
  {
    $this->config =& load_class('Config', 'core');
    $this->_title = $this->config->item('title');
  }

  function sendAndroidBroadcast($array)
  {
    $post_data['action'] = 'send';
    $post_data['broadcast'] = 'Y';
    $post_data['app'] = $this->_app;
    $post_data['title'] = $this->_title;
    $post_data['message'] = $array['alert'];
    $post_data['devicetype'] = 'android';
    $post_data['uri'] = json_encode($array['field']);
    foreach ($post_data as $key => $value) {
      $values[] = "$key=" . urlencode($value);
    }
    $data_string = implode("&", $values);//提交的数据格式是a=1&b=2
    return $this->curl_get_contents($this->_get_url . '?' . $data_string);
  }

  function sendAndriodUnicast($array)
  {
    $post_data['action'] = 'send';
    $post_data['broadcast'] = 'N';
    $post_data['username'] = $array['device_tokens'] . '|' . $this->_type . '|' . $this->_app;
    $post_data['title'] = $this->_title;
    $post_data['message'] = $array['alert'];
    $post_data['devicetype'] = 'android';
    $post_data['uri'] = json_encode($array['field']);
    foreach ($post_data as $key => $value) {
      $values[] = "$key=" . urlencode($value);
    }
    $data_string = implode("&", $values);//提交的数据格式是a=1&b=2
    return $this->curl_get_contents($this->_get_url . '?' . $data_string);
  }

  function sendAndriodListcast()
  {

  }

  public function curl_get_contents($t_url)
  {
    // 初始化一个cURL会话
    $curl = curl_init($t_url);
    // 不显示header信息
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // 自动设置Referer
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    // 执行一个curl会话
    $tmp = curl_exec($curl);
    // 关闭curl会话
    curl_close($curl);
    return $tmp;
  }
}
