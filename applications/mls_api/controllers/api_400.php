<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api_400 extends My_Controller
{
  private $_big_num_400 = '';
  private $_key = 'fang100_phone_400_key';

  public function __construct()
  {
    parent::__construct();
    $this->config =& load_class('Config', 'core');
    $this->load->model('phone_info_400_model');
    $this->load->model('city_model');
    $this->load->model('broker_phone_check_model');
    $this->_big_num_400 = $this->config->item('tel400');
  }

  //通话开始，根据小号查找真实号码
  public function get_phone_by_num()
  {
    $big_code = $this->input->post('big_code');//400大号
    $small_code = $this->input->post('small_code');//400小号
    $sub_phone = $this->input->post('main_phone');//主叫号码
    $time = $this->input->post('time');//时间戳
    $code = $this->input->post('code');//md5加密字符串，用于验证
    $data = array();
    $flag = 0;
    $true_phone = '';
    $msg = '';
    $play_voice = '';
    //参数判断
    if (!empty($big_code) && !empty($small_code) && !empty($sub_phone) && !empty($time) && !empty($code)) {
      if ($big_code == $this->_big_num_400) {
        //加密字符串
        $code_str = $big_code . $small_code . $sub_phone . $time . $this->_key;
        $code_md5 = md5($code_str);
        if ($code_md5 === $code) {
          if (intval($small_code) > 0) {
            //根据短号，查询真实号码
            $where_cond = array(
              'num_group' => intval($small_code),
              'status' => 2
            );
            $query_result = $this->phone_info_400_model->get_data_by_cond($where_cond);
            if (is_full_array($query_result[0])) {
              $city_id = intval($query_result[0]['city_id']);
              $tbl = intval($query_result[0]['tbl']);
              $row_id = intval($query_result[0]['row_id']);
              $city_data = $this->city_model->get_by_id($city_id);
              if (is_full_array($city_data)) {
                $city_spell = $city_data['spell'];
              }
              //判断短号是内部or外部使用
              if (1 == $query_result[0]['flag']) {
                $flag = 5;
                $true_phone = $query_result[0]['phone'];
                $msg = '查询成功';
                if ('99' == $true_phone) {
                  $flag = 8;
                  $msg = '手机号语音验证';
                  $play_voice = $this->_big_num_400 . 'voice.wav';
                  //经纪人手机号验证，数据操作
                  $check_where_cond = array(
                    'phone' => $sub_phone,
                    'type' => 1
                  );
                  $query_result = $this->broker_phone_check_model->get($check_where_cond);
                  if (is_full_array($query_result)) {
                    //更新操作
                    $id = $query_result[0]['id'];
                    $update_arr = array(
                      'status' => 1
                    );
                    if (intval($id) > 0) {
                      $update_result = $this->broker_phone_check_model->update($id, $update_arr);
                    }
                  } else {
                    //添加操作
                    $add_arr = array(
                      'phone' => $sub_phone,
                      'type' => 1,
                      'status' => 1
                    );
                    $this->broker_phone_check_model->add($add_arr);
                  }
                }
              } else if (2 == $query_result[0]['flag']) {
                if (isset($city_spell) && !empty($city_spell)) {
                  $this->load->model('broker_info_model');
                  $this->broker_info_model->set_city_db($city_spell);
                  //1)拨打号码是否在系统中
                  $phone_broker_data = $this->broker_info_model->get_one_by(array('phone' => $sub_phone));
                  if (is_full_array($phone_broker_data)) {
                    $phone_broker_id = $phone_broker_data['broker_id'];
                    $this->load->model('grab_model');
                    $this->grab_model->set_city_db($city_spell);
                    $grab_data = $this->grab_model->get_data_by_id_type($row_id, $tbl);
                    $broker_id_arr = array();
                    if (is_full_array($grab_data)) {
                      foreach ($grab_data as $k => $v) {
                        $broker_id_arr[] = $v['broker_id'];
                      }
                    }
                    //判断拨打者是否有权限接通（拨打者已抢到400小号所属的委托房客源）
                    if (is_full_array($broker_id_arr) && in_array($phone_broker_id, $broker_id_arr)) {
                      $flag = 5;
                      $true_phone = $query_result[0]['phone'];
                      $msg = '查询成功';
                    } else {
                      $flag = 6;
                      $msg = '没有权限拨打该400号码';
                    }
                  } else {
                    $flag = 6;
                    $msg = '没有权限拨打该400号码';
                  }
                } else {
                  $flag = 7;
                  $msg = '没有找到小号所属城市';
                }
              }
            } else {
              $flag = 4;
              $msg = '未找到对应真实电话';
            }
          } else {
            $flag = 1;
            $msg = '参数不正确';
          }
        } else {
          $flag = 3;
          $msg = 'md5验证不正确';
        }
      } else {
        $flag = 2;
        $msg = '400大号不正确';
      }
    } else {
      $flag = 1;
      $msg = '参数不正确';
    }

    $data['flag'] = $flag;
    $data['msg'] = $msg;
    $data['play_voice'] = $play_voice;
    if (5 == $flag) {
      $data['tel'] = $true_phone;
    }
    echo $this->result($flag == 5 ? true : false, $msg, $data);
    //添加接口日志
    $insert_log_arr = array(
      'big_code' => $big_code,
      'small_code' => $small_code,
      'sub_phone' => $sub_phone,
      'time' => $time,
      'code' => $code,
      'flag' => $flag,
      'msg' => $msg,
      'tel' => $true_phone,
      'now_time' => time()
    );
    $this->phone_info_400_model->insert_data_tb3($insert_log_arr);

  }

  //通话记录
  public function recoder()
  {
    $big_code = $this->input->post('big_code');//400大号
    $num_group = $this->input->post('num_group');//400小号
    $main_callphone = $this->input->post('main_callphone'); //主叫号码
    $sub_callphone = $this->input->post('sub_callphone');//被叫号码
    $begtime = $this->input->post('begtime');//通话开始时间
    $endtime = $this->input->post('endtime');//通话结束时间
    $callfee = $this->input->post('callfee');//通话费用
    $main_calltime = $this->input->post('main_calltime');//主叫通话时长
    $sub_calltime = $this->input->post('sub_calltime');//被叫通话时长
    $callResult = $this->input->post('callresult');//呼叫状态代码
    $callresult_str = $this->input->post('callresult_str');//呼叫状态描述
    $main_callcity = $this->input->post('main_callcity');//主叫号码所在城市
    $main_callprovince = $this->input->post('main_callprovince');//主叫号码所在省份
    $sub_callcity = $this->input->post('sub_callcity');//被叫号码所在城市
    $sub_callprovince = $this->input->post('sub_callprovince');//被叫号码所在省份
    $voiceid = $this->input->post('voiceid');//录音id
    $code = $this->input->post('code');//加密字符串

    $data = array();
    $flag = 5;
    $msg = '操作成功';

    //小号参数验证
    if (isset($num_group) && !empty($num_group)) {
      //话单重复验证,根据接口日志
      $log_where_cond = array(
        'big_code' => $big_code,
        'num_group' => $num_group,
        'main_callphone' => $main_callphone
      );
      $query_log_result = $this->phone_info_400_model->get_log_data_by_cond($log_where_cond);
      if (is_full_array($query_log_result[0]) && $query_log_result[0]['begtime'] == $begtime) {
        $flag = 6;
        $msg = '话单验证重复';
      } else {
        //加密字符串验证
        $code_str = $big_code . $num_group . $this->_key . $main_callphone . $sub_callphone;
        $code_md5 = md5($code_str);
        if ($code_md5 === $code) {
          //查询有无此小号
          $where_cond = array(
            'num_group' => intval($num_group),
            'status' => 2
          );
          $query_result = $this->phone_info_400_model->get_data_by_cond($where_cond);
          if (is_full_array($query_result)) {
            $insert_data = array(
              'info_id' => $query_result[0]['id'],
              'num_group' => intval($num_group),
              'main_callphone' => strval($main_callphone),
              'sub_callphone' => strval($sub_callphone),
              'begtime' => intval($begtime),
              'endtime' => intval($endtime),
              'indate' => date('Y-m-d', intval($begtime)),
              'callfee' => floatval($callfee),
              'main_calltime' => intval($main_calltime),
              'sub_calltime' => intval($sub_calltime),
              'callResult' => intval($callResult),
              'callReturn_str' => strval($callresult_str),
              'main_callcity' => strval($main_callcity),
              'main_callprovince' => strval($main_callprovince),
              'sub_callcity' => strval($sub_callcity),
              'sub_callprovince' => strval($sub_callprovince),
              'voiceid' => $voiceid,
              'num_group_city_id' => $query_result[0]['city_id']
            );

            //根据主叫号码，查询所在城市id，所属经纪人所在公司id，门店id
            $this->load->model('broker_model');
            $main_phone_broker_data = $this->broker_model->get_one_by(array('phone' => $main_callphone));
            if (is_full_array($main_phone_broker_data)) {
              $city_id = $main_phone_broker_data['city_id'];
              $city_data = $this->city_model->get_by_id($city_id);
              if (is_full_array($city_data)) {
                $city_spell = $city_data['spell'];
                $this->load->model('broker_info_model');
                $this->broker_info_model->set_city_db($city_spell);
                $main_phone_broker_data2 = $this->broker_info_model->get_one_by(array('phone' => $main_callphone));
                if (is_full_array($main_phone_broker_data2)) {
                  $insert_data['main_call_city_id'] = intval($city_id);
                  $insert_data['main_call_company_id'] = intval($main_phone_broker_data2['company_id']);
                  $insert_data['main_call_agency_id'] = intval($main_phone_broker_data2['agency_id']);
                }
              }
            }

            $insert_result = $this->phone_info_400_model->insert_data_tb2($insert_data);
            if (is_int($insert_result) && $insert_result > 0) {
//              $flag = 5;
//              $msg = '操作成功';
            } else {
              $flag = 4;
              $msg = '系统错误';
            }
          } else {
            $flag = 3;
            $msg = '查询失败';
          }
        } else {
          $flag = 2;
          $msg = 'md5校验失败';
        }
      }
    } else {
      $flag = 1;
      $msg = '小号参数不正确';
    }

    $data['flag'] = $flag;
    $data['msg'] = $msg;

    echo $this->result($flag == 5 ? true : false, $msg, $data);

    //添加接口日志
    $insert_log_arr = array(
      'big_code' => $big_code,
      'num_group' => $num_group,
      'main_callphone' => $main_callphone,
      'sub_callphone' => $sub_callphone,
      'begtime' => $begtime,
      'endtime' => $endtime,
      'main_calltime' => $main_calltime,
      'sub_calltime' => $sub_calltime,
      'callresult' => $callResult,
      'callresult_str' => $callresult_str,
      'main_callcity' => $main_callcity,
      'main_callprovince' => $main_callprovince,
      'sub_callcity' => $sub_callcity,
      'sub_callprovince' => $sub_callprovince,
      'voiceid' => $voiceid,
      'code' => $code,
      'flag' => $flag,
      'msg' => $msg,
      'now_time' => time()
    );
    $this->phone_info_400_model->insert_data_tb4($insert_log_arr);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
