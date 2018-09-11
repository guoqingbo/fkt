<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Push_func_base_model
 *
 * @author user
 */
class Push_func_base_model extends MY_Model
{

  private $_template = array(
    1 => array(
      1 => '您好，经纪人{{broker_name}}向您发起了合作申请，现在就去查看', //合作申请
      2 => '您申请的{{block_name}}合作已被对方接受', //合作被接受
      3 => '您申请的{{block_name}}合作已被对方拒绝', //合作被拒绝
      4 => '您申请（参与）的合作，对方提交了佣金分配', //对方提交佣金
      5 => '您提交的佣金分配被拒绝', //对方拒绝佣金
      6 => '您申请（参与）的合作生效', //佣金被接受
      7 => '您申请（参与）{{block_name}}的合作，对方确认成交', //对方确认成交
      8 => '您申请（参与）{{block_name}}的成交失败', //成交失败
      9 => '您申请（参与）{{block_name}}的合作被取消', //对方取消合作
      10 => '您申请（参与）{{block_name}}的合作交易成功', //交易成功
      11 => '您申请（参与）的合作逾期失败', //成交逾期
      12 => '您申请（参与）的合作被冻结', //合作冻结
      13 => '您申请（参与）的合作终止', //合作终止
    ),//合作状态变更
    2 => array(1 => '采集市场新上架了{{collect_num}}条个人房源，马上去采集，快人一步'), //采集中心
    3 => array(1 => '使用自定义'), //新房分销
    4 => array(
      1 => '开发商确认"{{cm_name}}"({{cm_phone}})为有效客户，可以安排带看({{lp_name}})', //审核通过
      2 => '{{cm_name}}({{cm_phone}})为无效客户,报备失败({{lp_name}})', //审核不通过
      3 => '{{cm_name}}({{cm_phone}})到场看房成功,请跟进客户认购({{lp_name}})', //验证号验证成功
      4 => '审核通过{{cm_name}}({{cm_phone}})已签约({{lp_name}})', //签约成功
      5 => '审核未通过{{cm_name}}({{cm_phone}})没有签约,请再次确认({{lp_name}})', //签约失败
      6 => '恭喜您完成结拥，请继续努力', //申请结拥
      7 => '{{cm_name}}({{lp_name}})结拥失败,该客户认购后因故解约,未能完成购房', //结拥失败
    ), //报备状态变更
    5 => array(1 => '使用自定义'), //合作市场
    6 => array(1 => '使用自定义'), //资讯中心
    7 => array(1 => '提交的认证已经通过'), //资格认证
    8 => array(1 => '使用自定义'), //事情提醒
    9 => array(1 => '您所在的区域有新的房源委托“{{comt_name}}{{hprice}}{{price_danwei}}”，快去抢房源吧，点击查看'),//营销中心客户委托
    10 => array(1 => '{{uname}}预约了您发布的“{{block_name}}{{price}}{{price_danwei}}”带看，请尽快查看并联系客户看房，点击查看'),//营销中心客户预约
    11 => array(1 => '您所在的区域有新的客户需求“{{district}}{{lprice}}-{{hprice}}{{price_danwei}}”，快去抢客源吧，点击查看'),//营销中心客户需求
    12 => array(
      1 => '亲爱的{{broker_name}}，您发布的合作房源{{block_name}} {{buildarea}}平 {{price}}万 已通过审核，该房源将在合作中心置顶显示。',
      2 => '对不起，您发布的合作房源{{block_name}} {{buildarea}}平 {{price}}万 未通过审核，请尝试重新提交资料审核。',
    ),//合作资料后台审核
    13 => array(1 => '{{company_name}} {{agency_name}} {{broker_name}}，已成功兑换1张大型经纪人培训会入场券，兑换时间为{{create_date}}，请在入场时出示此系统消息，凭此消息入场'),
    14 => array(1 => '使用自定义'), //广告
    15 => array(
      1 => '经纪人{{broker_name}}，请求添加您为好友。',
      2 => '经纪人{{broker_name}}，接受了您的好友请求。',
      3 => '经纪人{{broker_name}}，拒绝了您的好友请求。',
    ),//合作朋友圈申请
    16 => array(1 => '使用自定义'), //订阅
  );

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 发送推送消息
   * @param int $method_id 方式 1 为单播  2 为广播
   * @param int $module 模块
   * @param int $func 功能
   * @param int $send_broker_id 发送方  为0时是系统
   * @param int $accept_broker_ids 接收方  为0时广播
   * @param array $field 参数 用于APP跳转
   * @param string $extra 替换模板里的参数
   * @param string $alert 不使用模板设置好的信息
   */
  public function send($method_id, $module, $func, $send_broker_id,
                       $accept_broker_ids, $field = array(), $extra = array(), $alert = '')
  {
    $method = $method_id == 1 ? 'unicast' : 'broadcase';
    $param = array();//推送参数
    //先查找接收方的设备号 判断是andorid还是ios
    //提示语
    if (isset($this->_template[$module][$func])) {
      if ($alert == '') {
        $alert = $this->_template[$module][$func];
      }
      if (is_full_array($extra)) {
        foreach ($extra as $key => $val) {
          $alert = str_replace('{{' . $key . '}}', $val, $alert);
        }
      }
    }
    if ($module == '' || $func == '' || $send_broker_id === ''
      || $accept_broker_ids == ''
    ) {
      return false;
    }
    //构造基本参数
    $param['alert'] = $alert;
    $field['module'] = $module;
    $field['func'] = $func;
    $param['field'] = $field;
    $this->load->model('broker_online_app_base_model');
    $this->load->library('log/Log');
    if ($method_id == 1) //单播
    {
      $accept_brokers = $this->broker_online_app_base_model->get_by_broker_id($accept_broker_ids);
      foreach ($accept_brokers as $value) {
        if ($value['token'] == '') {
          continue;
        }
        $accept_device = $value['devicetype'];
        $accept_broker_id = $value['broker_id'];
        //插入推送结果到相应的库中
        $insert_data = array(
          'send_broker_id' => $send_broker_id,
          'accept_broker_id' => $accept_broker_id,
          'accept_devicetype' => $accept_device,
          'module' => $module, 'func' => $func,
          'field' => serialize($field),
          'alert' => $alert, 'create_time' => time(),
        );
        if ($accept_device == 'android') {
          //推送结果
          $param['device_tokens'] = $value['token'];
          $android_result = $this->android($method, $param);
          $insert_data['reason'] = $android_result;
          $insert_data['device_number'] = $value['deviceid'];
          //判断推送结果
          if (is_null(json_decode($android_result))) //执行失败
          {
            //写log日志
            Log::record('推送消息失败-' . $accept_device, $insert_data, 'push_message');
            $insert_data['status'] = 0;
          } else {
            $insert_data['status'] = 1;
          }
        } else if ($accept_device == 'iPhone') {
          //推送结果
          $param['device_tokens'] = $value['token'];
          //图标有数字
          $param['badge'] = 0;
          if (in_array($module, array(1, 4, 7, 9, 10, 11, 12))) {
            $param['badge'] = 1;
          }
          $ios_result = $this->ios($method, $param);
          $insert_data['reason'] = $ios_result;
          $insert_data['device_number'] = $value['token'];
          //判断推送结果
          if (is_null(json_decode($ios_result))) //执行失败
          {
            //写log日志
            Log::record('推送消息失败-' . $accept_device, $insert_data, 'push_message');
            $insert_data['status'] = 0;
          } else {
            $insert_data['status'] = 1;
          }
        }
        $this->load->model('push_unicast_record_base_model');
        $this->push_unicast_record_base_model->insert_unicast_result($insert_data);
      }
    } else if ($method_id == 2) //广播
    {
      $android_result = $this->android($method, $param);
      $arr_android_result = json_decode($android_result, true);
      $this->load->model('push_broadcast_record_base_model');
      //插入推送结果到相应的库中
      $insert_data = array(
        'module' => $module, 'field' => serialize($field),
        'alert' => $alert, 'create_time' => time(),
      );
      //android
      $insert_data['reason'] = $android_result;
      $insert_data['devicetype'] = 'android';
      //andriod
      if (is_full_array($arr_android_result)
        && $arr_android_result['description'] == 'send success'
      ) {
        //成功
        $insert_data['status'] = 1;
      } else //失败
      {
        //写log日志
        $insert_data['status'] = 0;
      }
      //插入记录
      $this->push_broadcast_record_base_model->insert_broadcase_result($insert_data);
      //iPhone
      /**
       * $ios_result = $this->ios($method, $param);
       * $insert_data['reason'] = $ios_result;
       * $insert_data['devicetype'] = 'iPhone';**/
    }
  }

  public function send_fang($method_id, $accept_member_uids, $field = array(), $alert)
  {
    $method = $method_id == 1 ? 'unicast' : 'broadcase';
    $param = array();//推送参数
    //先查找接收方的设备号 判断是andorid还是ios
    //提示语
    /*if (isset($this->_template[$module][$func]))
    {
        if ($alert == '')
        {
            $alert = $this->_template[$module][$func];
        }
        if (is_full_array($extra))
        {
            foreach ($extra as $key => $val)
            {
                $alert = str_replace('{{'.$key.'}}', $val, $alert);
            }
        }
    }*/
    if ($alert == '' || $accept_member_uids == '') {
      return false;
    }
    //构造基本参数
    $param['alert'] = $alert;
    //$field['module'] = $module;
    //$field['func'] = $func;
    $param['field'] = $field;
    $param['type'] = 1;
    $this->load->model('broker_online_app_base_model');
    $this->load->library('log/Log');
    if ($method_id == 1) //单播
    {
      $accept_members = $this->broker_online_app_base_model->get_by_member_uid($accept_member_uids);
      foreach ($accept_members as $value) {
        if ($value['token'] == '') {
          continue;
        }
        $accept_device = $value['devicetype'];
        $accept_member_uid = $value['uid'];
        //插入推送结果到相应的库中
        $insert_data = array();
        $insert_data = array(
          'send_broker_id' => 0,
          'accept_broker_id' => $accept_member_uid,
          'accept_devicetype' => $accept_device,
          'module' => 0, 'func' => 0,
          'field' => serialize($field),
          'alert' => $alert, 'create_time' => time(),
        );
        if ($accept_device == 'android') {
          //推送结果
          $param['device_tokens'] = $value['token'];
          $android_result = $this->android($method, $param, 1);
          $insert_data['reason'] = $android_result;
          $insert_data['device_number'] = $value['deviceid'];
          //判断推送结果
          if (is_null(json_decode($android_result))) //执行失败
          {
            //写log日志
            //Log::record('推送消息失败-' . $accept_device, $insert_data, 'push_message');
            $insert_data['status'] = 0;
          } else {
            $insert_data['status'] = 1;
          }
        } else if ($accept_device == 'iPhone') {
          //推送结果
          $param['device_tokens'] = $value['token'];
          $ios_result = $this->ios($method, $param, 1);
          $insert_data['reason'] = $ios_result;
          $insert_data['device_number'] = $value['token'];
          //判断推送结果
          if (is_null(json_decode($ios_result))) //执行失败
          {
            //写log日志
            //Log::record('推送消息失败-' . $accept_device, $insert_data, 'push_message');
            $insert_data['status'] = 0;
          } else {
            $insert_data['status'] = 1;
          }
        }
        $this->load->model('push_unicast_record_base_model');
        $this->push_unicast_record_base_model->insert_unicast_result($insert_data);
      }
    } else if ($method_id == 2) //广播
    {
      $android_result = $this->android($method, $param, 1);
      $arr_android_result = json_decode($android_result, true);
      $this->load->model('push_broadcast_record_base_model');
      //插入推送结果到相应的库中
      $insert_data = array();
      /*$insert_data = array(
          'module' => $module, 'field' => serialize($field),
          'alert' => $alert, 'create_time' => time(),
      );
      //android
      $insert_data['reason'] = $android_result;
      $insert_data['devicetype'] = 'android';*/
      //andriod
      if (is_full_array($arr_android_result)
        && $arr_android_result['description'] == 'send success'
      ) {
        //成功
        $insert_data['status'] = 1;
      } else //失败
      {
        //写log日志
        $insert_data['status'] = 0;
      }
      //插入记录
      //$this->push_broadcast_record_base_model->insert_broadcase_result($insert_data);
      //iPhone
      /**
       * $ios_result = $this->ios($method, $param);
       * $insert_data['reason'] = $ios_result;
       * $insert_data['devicetype'] = 'iPhone';**/
    }

    return $insert_data;
  }

  /**
   * ios移动平台推送
   * @param string $method 推送方式 unicast 单播 listcast 列播 broadcase 广播
   * @param array $array 推送参数 $device_tokens  $alert $field
   */
  public function ios($method, $array, $type = '')
  {
    $this->load->library('notification/umeng/ios_push');
    if ($type) {
      $this->ios_push->set_app($type);
    } else {
      $this->ios_push->set_app();
    }
    if ($method == 'unicast') //单播
    {
      $json = $this->ios_push->sendIOSUnicast($array);
    } else if ($method == 'broadcase') //广播
    {
      $json = $this->ios_push->sendIOSBroadcast($array);
    }
    return $json;
  }

  /**
   * android移动平台推送
   * @param string $method 推送方式 unicast 单播 listcast 列播 broadcase 广播
   * @param array $array 推送参数 $device_tokens  $alert $field
   */
  public function android($method, $array, $type = '')
  {
    $this->load->library('notification/umeng/android_push');
    if ($type) {
      $this->android_push->set_app($type);
    } else {
      $this->android_push->set_app();
    }
    if ($method == 'unicast') //单播
    {
      $json = $this->android_push->sendAndroidUnicast($array);
    } else if ($method == 'broadcase') //广播
    {
      $json = $this->android_push->sendAndroidBroadcast($array);
    }
    return $json;
  }
}
