<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cooperate_lol extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $city = $this->input->get('city');
    $this->set_city($city);
    $this->load->model('project_cooperate_lol_base_model');
    $this->load->model('cooperate_chushen_base_model');
    $this->load->model('cooperate_base_model');
    $this->load->model('agency_base_model');
    $this->load->model('city_base_model');
  }

  //把活动的合作审核资料转入新的合作审核表内
  public function index()
  {
    $city = $this->input->get('city');
    $city_id = $this->city_base_model->get_city_by_spell($city);
    $list = $this->project_cooperate_lol_base_model->get_cooperate_success_by_city($city_id['id']);
    $i = 0;
    $j = 0;
    foreach ($list as $key => $val) {
      $result = $this->cooperate_chushen_base_model->update_cooperate_apply($val['c_id'], 1);
      $i++;
      $info[0] = $this->project_cooperate_lol_base_model->get_cooperate_success_applay_list(array('order_sn' => $val['order_sn']), '', '', 'id', 'ASC');
      foreach ($info[0] as $key => $val) {
        $cooperate_info = $this->cooperate_base_model->get_cooperate_by_cid($val['c_id']);
        if (is_full_array($cooperate_info)) {
          $params['c_id'] = $val['c_id'];
          $params['order_sn'] = $cooperate_info['order_sn'];
          $params['apply_type'] = $cooperate_info['apply_type'];
          //甲方经纪人信息
          $params['brokerid_a'] = $cooperate_info['brokerid_a'];//经纪人id
          $params['phone_a'] = $cooperate_info['phone_a'];//经纪人电话
          $params['broker_name_a'] = $cooperate_info['broker_name_a'];///经纪人名字
          $params['agencyid_a'] = $cooperate_info['agentid_a'];//门店id
          $agency_a = $this->agency_base_model->get_by_id($cooperate_info['agentid_a']);
          $params['agency_name_a'] = $agency_a['name'];//门店名称
          $params['agency_type_a'] = $agency_a['agency_type'];//门店类型
          $params['companyid_a'] = $agency_a['company_id'];//公司id
          $company_a = $this->agency_base_model->get_by_id($agency_a['company_id']);
          $params['company_name_a'] = $company_a['name'];//公司名称

          //乙方经纪人信息
          $params['brokerid_b'] = $cooperate_info['brokerid_b'];//经纪人id
          $params['phone_b'] = $cooperate_info['phone_b'];//经纪人电话
          $params['broker_name_b'] = $cooperate_info['broker_name_b'];///经纪人名字
          $params['agencyid_b'] = $cooperate_info['agentid_b'];//门店id
          $agency_b = $this->agency_base_model->get_by_id($cooperate_info['agentid_b']);
          $params['agency_name_b'] = $agency_b['name'];//门店名称
          $params['agency_type_b'] = $agency_b['agency_type'];//门店类型
          $params['companyid_b'] = $agency_b['company_id'];//公司id
          $company_b = $this->agency_base_model->get_by_id($agency_b['company_id']);
          $params['company_name_b'] = $company_b['name'];//公司名称

          $params['seller_idcard'] = $val['seller_idcard'];
          $params['seller_owner'] = $val['seller_owner'];
          $params['seller_telno'] = $val['seller_telno'];
          $params['buyer_owner'] = $val['buyer_owner'];
          $params['buyer_idcard'] = $val['buyer_idcard'];
          $params['buyer_telno'] = $val['buyer_telno'];
          $params['pic'] = $val['pic'];

          $params['status'] = $val['status'];
          $params['create_time'] = $val['create_time'];//经纪人id
          $this->cooperate_chushen_base_model->add_cooperate_chushen($params);
          $j++;
        }
      }
    }
    echo "更新" . $i . "条 添加" . $j . "条";
  }

}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

