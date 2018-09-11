<?php

header("Content-type: text/html; charset=utf-8");

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Get_district extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $dist_data = array();
//        echo 123;
    $city = $_GET['city'];
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    //加载区属板块模型类
    $this->load->model('get_district_model');
    $type = $_GET['type'];

    $geo = $_GET['geo'] === 'true' || $_GET['geo'] === true ? true : false;


    //区属
    if ($type == 'dist') {
      $district = $this->get_district_model->get_district();

//      $dist_data['dist']['-1']['id'] = '-1';
//      $dist_data['dist']['-1']['district'] = '不限';
      foreach ($district as $key => $val) {
        $dist_data['dist'][$val['id']]['id'] = $val['id'];
        $dist_data['dist'][$val['id']]['district'] = $val['district'];
        if ($geo) {
          $dist_data['dist'][$val['id']]['b_map_x'] = $val['b_map_x'];
          $dist_data['dist'][$val['id']]['b_map_y'] = $val['b_map_y'];
        }
      }
    }
    //板块
    if ($type == 'block') {
      $street = $this->get_district_model->get_street();
//      $dist_data['block']['-1']['id'] = '-1';
//      $dist_data['block']['-1']['streetname'] = '不限';
      foreach ($street as $key => $val) {
        $dist_data['block'][$val['id']]['id'] = $val['id'];
        $dist_data['block'][$val['id']]['streetname'] = $val['streetname'];
        if ($geo) {
          $dist_data['block'][$val['id']]['b_map_x'] = $val['b_map_x'];
          $dist_data['block'][$val['id']]['b_map_y'] = $val['b_map_y'];
        }
      }
    }

    //板块
    if ($type == 'all') {
//      $dist_data['dist']['-1']['id'] = '-1';
//      $dist_data['dist']['-1']['district'] = '不限';

      $district = $this->get_district_model->get_district();
      $street = $this->get_district_model->get_street();
//      foreach ($street as $key => $val) {
//        $st = array();
//        $st['id'] = $val['id'];
//        $st['streetname'] = $val['streetname'];
//        $st['b_map_x'] = $val['b_map_x'];
//        $st['b_map_y'] = $val['b_map_y'];
//        foreach ($district as $keyd => $vald) {
//          if ($val['dist_id'] == $vald['id']) {
//            if (!is_array($vald['street'])) {
//              $district[$keyd]['street'] = array();
//              $dist_data[$keyd]['street'] = array();
//              $dist_data[$keyd]['id'] = $district[$keyd]['id'];
//              $dist_data[$keyd]['district'] = $district[$keyd]['district'];
//              $dist_data[$keyd]['b_map_x'] = $district[$keyd]['b_map_x'];
//              $dist_data[$keyd]['b_map_y'] = $district[$keyd]['b_map_y'];
//            }
//            array_push($dist_data[$keyd]['street'], $st);
//            break;
//          }
//        }
//      }

      foreach ($district as $key => $val) {
        $dist_data['dist'][$val['id']]['id'] = $val['id'];
        $dist_data['dist'][$val['id']]['district'] = $val['district'];
        if ($geo) {
          $dist_data['dist'][$val['id']]['b_map_x'] = $val['b_map_x'];
          $dist_data['dist'][$val['id']]['b_map_y'] = $val['b_map_y'];
        }
//          $street = $this->get_district_model->get_street();

//        $dist_data['dist'][$val['id']]['block']['-1']['streetname'] = '不限';
        foreach ($street as $key => $val) {
          if ($dist_data['dist'][$val['dist_id']]) {
            $dist_data['dist'][$val['dist_id']]['block'][$val['id']]['streetname'] = $val['streetname'];
            if ($geo) {
              $dist_data['dist'][$val['dist_id']]['block'][$val['id']]['b_map_x'] = $val['b_map_x'];
              $dist_data['dist'][$val['dist_id']]['block'][$val['id']]['b_map_y'] = $val['b_map_y'];
            }
          }
        }
      }


    }

    $data['data'] = $dist_data;

    echo $this->result(true, '', $data);
  }

  public function xffx()
  {

    $dist_data = array();

    $city = $_GET['city'];
    //设置成熟参数
    $this->set_city($city);
    //加载区属板块模型类
    $this->load->model('get_district_xf_model');
    $type = $_GET['type'];
    //区属
    if ($type == 'dist') {
      $district = $this->get_district_xf_model->get_district();
      foreach ($district as $key => $val) {
        $dist_data['dist'][$val['id']] = iconv('UTF-8', 'UTF-8', $val['district']);
      }
    }
    //板块
    if ($type == 'block') {
      $street = $this->get_district_xf_model->get_street();
      foreach ($street as $key => $val) {
        $dist_data['block'][$val['id']] = iconv('UTF-8', 'UTF-8', $val['streetname']);
      }
    }

    $data['data'] = $dist_data;

    echo $this->result(true, '', $data);
  }

}
