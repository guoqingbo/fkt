<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 经纪人基本信息接口
 * @package         applications
 * @author          fisher
 * @copyright       Copyright (c) 2006 - 2015
 * @version         1.0
 */
class Cooperate extends MY_Controller
{
  public function config_district()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $this->load->model('get_district_model');
    $list = $this->get_district_model->get_district('is_show = 1');
    $data = array();
    foreach ($list as $key => $val) {
      $data[$key]['id'] = $val['id'];
      $data[$key]['district'] = $val['district'];
      $data[$key]['b_map_x'] = $val['b_map_x'];
      $data[$key]['b_map_y'] = $val['b_map_y'];
    }
    unset($list);
    $this->result(1, '查询成功', $data);
  }

  public function config_district_cj()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $this->load->model('get_district_model');
    $list = $this->get_district_model->get_district_cj('is_show = 1', $city);
    $data = array();
    foreach ($list as $key => $val) {
      $data[$key]['id'] = $val['id'];
      $data[$key]['district'] = $val['district'];
      $data[$key]['b_map_x'] = $val['b_map_x'];
      $data[$key]['b_map_y'] = $val['b_map_y'];
    }
    unset($list);
    $this->result(1, '查询成功', $data);
  }

  public function config_street()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $this->load->model('get_district_model');
    $list = $this->get_district_model->get_street('is_show = 1');
    $data = array();
    foreach ($list as $key => $val) {
      $data[$key]['id'] = $val['id'];
      $data[$key]['streetname'] = $val['streetname'];
      $data[$key]['district_id'] = $val['dist_id'];
      $data[$key]['b_map_x'] = $val['b_map_x'];
      $data[$key]['b_map_y'] = $val['b_map_y'];

    }
    unset($list);
    $this->result(1, '查询成功', $data);
  }

  public function config_street_cj()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $this->load->model('get_district_model');
    $list = $this->get_district_model->get_street_cj('is_show = 1', $city);
    $data = array();
    foreach ($list as $key => $val) {
      $data[$key]['id'] = $val['id'];
      $data[$key]['streetname'] = $val['streetname'];
      $data[$key]['district_id'] = $val['dist_id'];
      $data[$key]['b_map_x'] = $val['b_map_x'];
      $data[$key]['b_map_y'] = $val['b_map_y'];

    }
    unset($list);
    $this->result(1, '查询成功', $data);
  }

  //小区名称模糊搜索
  public function block_search_admin()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $blockname = $this->input->get('blockname');
    $this->load->model('community_base_model');

    $blockname = trim(urldecode($blockname));
    $list = $this->community_base_model->auto_cmtname($blockname);
    $data = array();
    if (is_array($list) && !empty($list)) {
      foreach ($list as $key => $val) {
        $data[$key]['id'] = $val['id'];
        $data[$key]['cmt_name'] = $val['cmt_name'];
        $data[$key]['dist_id'] = $val['dist_id'];
        $data[$key]['districtname'] = $val['districtname'];
        $data[$key]['streetid'] = $val['streetid'];
        $data[$key]['streetname'] = $val['streetname'];
        $data[$key]['address'] = $val['address'];
        $data[$key]['name_spell_s'] = $val['name_spell_s'];
        $data[$key]['name_spell'] = $val['name_spell'];
        $data[$key]['b_map_x'] = $val['b_map_x'];
        $data[$key]['b_map_y'] = $val['b_map_y'];
        $data[$key]['label'] = $val['cmt_name'];
      }
      unset($list);
    }
    $this->result(1, '查询成功', $data);
  }

  //小区名称模糊搜索
  public function block_search()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $blockname = $this->input->get('blockname');
    $this->load->model('community_base_model');

    $blockname = trim(urldecode($blockname));
    $list = $this->community_base_model->auto_cmtname($blockname);
    $data = array();
    if (is_array($list) && !empty($list)) {
      foreach ($list as $key => $val) {
        $data[$key]['cmt_id'] = $val['id'];
        $data[$key]['cmt_name'] = $val['cmt_name'];
        $data[$key]['dist_id'] = $val['dist_id'];
        $data[$key]['districtname'] = $val['districtname'];
        $data[$key]['streetid'] = $val['streetid'];
        $data[$key]['streetname'] = $val['streetname'];
        $data[$key]['address'] = $val['address'];
        $data[$key]['name_spell_s'] = $val['name_spell_s'];
        $data[$key]['name_spell'] = $val['name_spell'];
        $data[$key]['b_map_x'] = $val['b_map_x'];
        $data[$key]['b_map_y'] = $val['b_map_y'];
      }
      unset($list);
    }
    $this->result(1, '查询成功', $data);
  }
}
