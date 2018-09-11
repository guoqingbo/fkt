<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 特殊化采集（赶集）
 * 2016.6.30
 * cc
 */
class Collect_solve extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('collect_solve_model');
  }

  /**
   * 读取列表链接
   * 2016.9.1
   * cc
   */
  public function collect_url_list($city = '')
  {
    if ($city != '') {
      $res = $this->collect_solve_model->collect_city_byspell($city);
      $result = $this->collect_solve_model->collect_url_list($res['id']);
    } else {
      $result = $this->collect_solve_model->collect_url_list();
    }
    echo json_encode($result);
  }

  /**
   * 采集房源列表入库
   * 2016.9.1
   * cc
   */
  public function collect_list()
  {
    $datas = $_POST;
    $con = $datas['con'];
    $data = json_decode($datas['data'], true);
    $result = $this->collect_solve_model->collect_list($data, $con);
  }

  /**
   * 读取采集列表
   * 2016.6.30
   * cc
   */
  public function collect_url()
  {
    //遍历城市，同时采集数据
    $result[0] = $this->collect_solve_model->collect_url();//传城市id
    /*$res = $this->collect_solve_model->collect_city();
    foreach ($res as $key=>$val) {
        $result[$key] = $this->collect_solve_model->collect_url($val['id']);//传城市id
    }*/
    echo json_encode($result);
  }

  /**
   * 采集数据入库
   * 2016.6.30
   * cc
   */
  public function collect_info()
  {
    $datas = $_POST;
    $con = $datas['con'];
    $data = json_decode($datas['data'], true);
    $result = $this->collect_solve_model->collect_info($data, $con);//传城市id
  }

  //读取代理接口数据
  public function set_proxy()
  {
    $num = file_get_contents("./source/cache/proxy.txt");

    echo $num . '<br />';

    $url = "http://api.ip.data5u.com/dynamic/get.html?order=b0e8d57e7e8cbca84642e564c7b4e926&ttl=1";
    $proxystr = '';

    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $proxystr = curl_exec($ch);
    if (curl_errno($ch)) {
      echo 'Errno   ' . curl_error($ch);
    }
    curl_close($ch); // 关闭CURL会话

    if ($proxystr != '') {
      $proxys = explode(',', $proxystr);

      echo '@@proxy infomation@@<br />';
      echo $proxystr;

      $proxyfile = fopen("./source/cache/proxy_" . $num . ".txt", "w") or die("Unable to open file!");
      fwrite($proxyfile, $proxys[0]);
      fclose($proxyfile);
    } else {
      echo '没有获取到！';
    }

    $num = $num + 1;
    $num = $num > 9 ? 0 : $num;

    $proxyfile = fopen("./source/cache/proxy.txt", "w") or die("Unable to open file!");
    fwrite($proxyfile, $num);
    fclose($proxyfile);
  }

  //读取代理数据
  public function get_proxy()
  {
    $num = rand(0, 9);

    $proxystr = file_get_contents("./source/cache/proxy_" . $num . ".txt");

    echo $proxystr;
  }
}
