<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Exportfktdata extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $city = $this->input->get('city', true);
    //设置成熟参数
    $this->set_city($city);
    $this->load->model('exportfktdata_model');
    //$this->load->model('newhouse_sync_account_model');
  }

  /**
   * Index Page for this controller.
   */
  public function index()
  {
    $this->result(1, 'Entrust API for MLS.');
  }

  //公司
  public function company()
  {
    $city = $this->input->get('city', true);
    $where = array('company_id' => 0);
    $result = $this->exportfktdata_model->select_company($where, $database = 'db_city');
    foreach ($result as $val) {
      $xffxdata = array(
        'kcp_name' => $val['name'],
        'city' => $city,
        'username' => $val['telno'],
        'realname' => $val['linkman'],
        'password' => md5(''),
        'status' => $val['status'],
        'create_time' => time(),
        'update_time' => time(),
        'isdel' => 0,
        'company_id' => $val['id']
      );
      /*$this->newhouse_sync_account_model->addcompany($xffxdata);*/

      $url = MLS_JOB_URL . '/fktdata/addcompany';
      $this->load->library('Curl');
      Curl::fktdata($url, $xffxdata);
    }
  }

  //门店
  public function store()
  {
    $city = $this->input->get('city', true);
    $where = array('company_id !=' => 0);
    $result = $this->exportfktdata_model->select_store($where, $database = 'db_city');
    foreach ($result as $val) {
      $xffxdata = array(
        'area_id' => $val['dist_id'],
        'city' => $city,
        'storeName' => $val['name'],
        'address' => $val['address'],
        'com_id' => $val['company_id'],
        'status' => $val['status'],
        'special' => 0,
        'create_time' => time(),
        'update_time' => time(),
        'isdel' => 0,
        'store_id' => $val['id']
      );
      $url = MLS_JOB_URL . '/fktdata/addstore';
      $this->load->library('Curl');
      echo Curl::fktdata($url, $xffxdata);
      echo "<br />";
    }
  }

  //经纪人
  public function agency()
  {
    $city = $this->input->get('city', true);
    $result = $this->exportfktdata_model->select_broker_info($database = 'db_city');
    foreach ($result as $val) {
      if (isset($val['agency_id']) && isset($val['company_id'])) {
        $xffxdata = array(
          'ag_id' => $val['broker_id'],
          'ks_id' => $val['agency_id'],
          'kcp_id' => $val['company_id'],
          'ag_name' => $val['truename'],
          'ag_phone' => $val['phone'],
          'city' => $city,
          'ag_dist' => $val['area_id'],
          'pic' => $val['photo'],
          'sex' => 0,
          'ag_status' => $val['group_id'] == 1 ? 1 : 4,
          'addtime' => time(),
          'updatetime' => time()
        );
        $url = MLS_JOB_URL . '/fktdata/addagency';
        $this->load->library('Curl');
        Curl::fktdata($url, $xffxdata);
      } else {
        echo $val['broker_id'];
        echo '<br />';
      }
    }
  }

}
