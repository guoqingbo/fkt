<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class ent_grab extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('grab_model');
    $this->load->model('customer_demand_model');
  }

  public function grab()
  {
    $isajax = $this->input->post('isajax', true);
    $id = $this->input->post('id', true);
    $tab = $this->input->post('type', true);
    if ($isajax) {
      $broker_info = $this->user_arr;
      if ($broker_info['group_id'] != 2) {
        $result = array('result' => 101, 'msg' => '该经纪人尚未认证！');
      } else {
        if ($id) {
          $info = $this->grab_model->get_list_by($id, $tab);
          if ($info['grab_times'] < 10 && $info['grab_times'] >= 0) {
            $type = $this->_get_type($tab);
            $broker_times = $this->grab_model->get_broker_times($broker_info['broker_id'], $type);
            if ($broker_times < 5) {
              $is_grab = $this->grab_model->is_grab($broker_info['broker_id'], $id, $type);
              if (empty($is_grab)) {
                $insert_array = array('broker_id' => $broker_info['broker_id'],
                  'ent_id' => $id,
                  'type' => $type,
                  'createtime' => time()
                );
                $insert_id = $this->grab_model->grap($insert_array, $info['grab_times'], $tab);
                if ($insert_id) {
                  //增加等级分值
                  $this->load->model('api_broker_level_model');
                  $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_info['broker_id']));
                  $this->api_broker_level_model->grab($info, $tab);
                  if ($info['district_id']) {
                    //意向区属
                    $district = $this->customer_demand_model->get_all_district();
                    $info['district'] = $district[$info['district_id']]['district'];
                    unset($info['district_id']);
                  }
                  if ($info['comt_id']) {
                    $this->load->model('community_base_model');
                    $this->load->model('district_model');
                    $community_info = $this->community_base_model->find_cmt($info['comt_id']);
                    //获取区属
                    $district = $this->district_model->get_distname_by_id($community_info[0]['dist_id']);
                    //获取板块
                    $street = $this->district_model->get_streetname_by_id($community_info[0]['streetid']);
                    $info['district'] = $district;
                    $info['street'] = $street;
                  }
                  $result = array('result' => 200, 'msg' => '抢拍成功！', 'data' => $info);
                } else {
                  $result = array('result' => 104, 'msg' => '抢拍失败！');
                }
              } else {
                $result = array('result' => 104, 'msg' => '您已经抢拍过了！');
              }
            } else {
              $result = array('result' => 103, 'msg' => '您今天已经抢过5次了哦，请明天再来！');
            }
          } else {
            $result = array('result' => 102, 'msg' => '已经被抢光了，请您下次赶早！');
          }
        } else {
          $result = array('result' => 100, 'msg' => '委托编号非法！');
        }
      }
    } else {
      $result = array('result' => 100, 'msg' => '非法操作');
    }
    echo json_encode($result);
  }

  private function _get_type($tab)
  {
    if ($tab == "ent_sell") {
      $type = 1;
    } elseif ($tab == "ent_rent") {
      $type = 2;
    } elseif ($tab == "seek_sell") {
      $type = 3;
    } elseif ($tab == "seek_rent") {
      $type = 4;
    }
    return $type;
  }
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

