<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 特殊化采集（赶集）
 * 2016.6.30
 * cc
 */
class Collect_solve extends CI_Controller
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
  public function collect_url($city = '')
  {
    if ($city != '') {
      $res = $this->collect_solve_model->collect_city_byspell($city);
      $result[0] = $this->collect_solve_model->collect_url($res['id']);//传城市id
    } else {
      $result[0] = $this->collect_solve_model->collect_url();//传城市id
    }

    //遍历城市，同时采集数据

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

    echo $result;
  }
}
