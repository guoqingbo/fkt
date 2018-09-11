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
        $this->result(0, '请先到个人中心认证！');
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
                  $this->result(1, '抢拍成功！');
                } else {
                  $this->result(0, '抢拍失败！');
                }
              } else {
                $this->result(0, '您已经抢拍过了！');
              }
            } else {
              $this->result(0, '您今天已经抢过5次了哦，请明天再来！');
            }
          } else {
            $this->result(0, '已经被抢光了，请您下次赶早！');
          }
        } else {
          $this->result(0, '委托编号非法！');
        }
      }
    } else {
      $this->result(0, '非法操作！');
    }
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

  //抢拍规则
  public function protocol()
  {
    $protocol_html = file_get_contents(dirname(__FILE__) . '/../views/ent_grab/grab_rule.php');
    $protocol_html_arr = array('protocol' => $protocol_html);
    $this->result(1, '查询成功', $protocol_html_arr);
  }
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

