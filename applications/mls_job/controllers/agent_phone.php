<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 采集经纪人电话号码
 * 2016.3.18
 * cc
 */
class Agent_phone extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('agent_phone_model');
  }

  //采集电话
  public function index()
  {
    $result = $this->agent_phone_model->agent_phone();
    echo "<script>
        function refresh(seconds){
            setTimeout('self.location.reload()',seconds*1000);
        }
        refresh(20);//调用方法启动定时刷新，数值单位：秒。
        </script>";
  }

  //词库
  public function keywords()
  {
    $data = array();
    $i = 0;
    $j = 0;
    $url = dirname(dirname(__FILE__)) . "\all.txt";
    $handle = fopen($url, "r");
    while (!feof($handle)) {
      $i++;
      $buffer = trim(fgets($handle));
      $length = mb_strlen($buffer);
      if ($i == 1) {
        $length = $length - 1;
      }
      $data[$buffer] = $length;
    }
    echo $i . "<br>";
    fclose($handle);
    arsort($data);
    foreach ($data as $key => $val) {
      $j++;
      echo $key . "<br>";
    }
    echo $j;
  }

}
