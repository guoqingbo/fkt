<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mass_site_broker
 * 经纪人群发网站关联
 * @author ccy
 */
class Mass_site_broker extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('mass_site_broker_model');
    $this->load->model('broker_info_model');
  }

  /**
   * 经纪人群发网站关联列表
   */
  public function index()
  {
    $data['mass_broker'] = $this->mass_site_broker_model->select_mass_broker();
    foreach ($data['mass_broker'] as $key => $value) {
      $where = array('broker_id' => $value['broker_id']);
      $broker = $this->broker_info_model->get_one_by($where);
      $data['mass_broker'][$key]['phone'] = $broker['phone'];
    }
    $data['site_list'] = $this->mass_site_broker_model->site_list();
    $this->load->view('mass_broker/index', $data);
  }

  /**
   * 修改数据
   */
  public function modify($id)
  {
    $mass_broker = $this->mass_site_broker_model->select_mass_broker($id);
    //print_r($data['mass_broker']);
    $data['mass_broker'] = $mass_broker[0];
    $this->load->view('mass_broker/modify', $data);
  }
}
