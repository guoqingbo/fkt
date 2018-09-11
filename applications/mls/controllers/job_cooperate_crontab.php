<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 合作先关功能定时任务计划
 *
 * @author xz
 */
class Job_cooperate_crontab extends MY_Controller
{

  /**
   * 构造函数
   */
  public function __construct()
  {
    parent::__construct();
  }

  /*
   * 合作流程-规定时间内仍然未接受合作，提醒甲方经纪人接受已申请的合作
   * @param strnig $city 城市拼音缩写
   * @param float $limit_hours 规定的小时数
   * @return void
   */
  public function send_accepet_message_to_broker_a($limit_hours = 48)
  {
    $job_referer_url = MLS_URL . '/job_cooperate_crontab/send_accepet_message_to_broker_a/48/';
    job_start($job_referer_url);

    $city = $this->input->get('city');
    $city = strip_tags(trim($city));
    if ($city == '') {
      exit('缺少城市参数无法执行！');
    }
    $this->config->set_item('login_city', $city);

    //加载合作MODEL
    $this->load->model('cooperate_model');

    $limit_hours = floatval($limit_hours);  //规定小时数
    $unaccpet_num = 0;  //未接收的合作个数
    $message_type = "1-1-2";  //通知甲方接受合作信息

    //查询已申请超过48小时以上，并且甲方经纪人仍然没有接受的合作信息
    $limit_time = time() - $limit_hours * 3600;
    $cond_where = "esta = 1 AND dateline <= '" . $limit_time . "'";
    $unaccpet_num = $this->cooperate_model->get_cooperate_num_by_cond($cond_where);

    if ($unaccpet_num > 0) {
      $per_num = 30;
      $unconfirm_info = array();
      $this->cooperate_model->set_select_fields(array('id', 'order_sn', 'esta', 'brokerid_a', 'broker_name_a', 'phone_a'));

      //消息模块MODEL
      $this->load->model('message_base_model');

      //循环合作信息，给甲方经纪人发送系统站内信
      for ($i = 0; $i < $unaccpet_num; $i++) {
        $offset = $per_num * $i;
        $unconfirm_info = $this->cooperate_model->get_list_by_cond($cond_where, $offset, $per_num);

        //循环发送站内信通知甲方经纪人接受合作
        foreach ($unconfirm_info as $key => $value) {
          $order_sn = strip_tags($value['order_sn']);
          $tbl = strip_tags($value['tbl']);
          $broker_id_a = intval($value['brokerid_a']);
          $phone_a = strip_tags($value['phone_a']);
          $broker_name_a = strip_tags($value['broker_name_a']);
          $fromer_self = '';
          $url_a = '/cooperate/accept_order_list/?cid=' . $value['id'];
          if ($value['apply_type'] == 1) {
            $params['type'] = "f";
            $params['name'] = $fromer_self;
            $params['id'] = $value['rowid'];
          } else if ($value['apply_type'] == 2) {
            $params['name'] = $fromer_self;
            $params['id'] = $value['customer_id'];
            $tbl .= '_customer';
          }
          $params['id'] = format_info_id($params['id'], $tbl);
          //发送甲方消息
          $this->message_base_model->add_message($message_type, $broker_id_a, $broker_name_a, $url_a, $params);
          $this->load->library('Sms_codi', array('city' => $city, 'jid' => '2', 'template' => 'send_accepet_message_to_broker_a'), 'sms');
          $return = $this->sms->send($phone_a);
          $result['status'] = $return['success'] ? 1 : 0;
          $result['msg'] = $return['success'] ? '短信发送成功' : $return['errorMessage'];
        }
        sleep(1);
      }
    }

    job_end($job_referer_url);
    echo 'over';
  }


  /*
   * 合作流程提-把规定时间内仍未接受的合作更改为合作失败状态，并发送站内信通知
   * @param strnig $city 城市拼音缩写
   * @param float $limit_hours 规定的小时数
   * @return void
   */
  public function update_cooperate_to_failed_unaccepet($limit_hours = 72)
  {
    $job_referer_url = MLS_URL . '/job_cooperate_crontab/update_cooperate_to_failed_unaccepet/72/';
    job_start($job_referer_url);

    $city = $this->input->get('city');
    $city = strip_tags(trim($city));

    if ($city == '') {
      exit('缺少城市参数无法执行！');
    }

    $this->config->set_item('login_city', $city);

    //加载合作MODEL
    $this->load->model('cooperate_model');

    $limit_hours = floatval($limit_hours);  //规定小时数
    $unaccpet_num = 0;  //未接收的合作个数
    $message_type_a = '1-14-1';  //通知甲方信息类型
    $message_type_b = '1-6-1';  //通知乙方信息类型

    //查询已申请超过72小时以上，并且甲方经纪人仍然没有接受的合作信息
    $limit_time = time() - $limit_hours * 3600;
    $cond_where = "esta = 1 AND dateline <= '" . $limit_time . "'";
    $unaccpet_num = $this->cooperate_model->get_cooperate_num_by_cond($cond_where);

    //循环合作信息，更改合作状态为合作失败
    if ($unaccpet_num > 0) {
      $per_num = 30;
      $unaccepet_info = array();
      $this->load->model('api_broker_sincere_model');
      $this->cooperate_model->set_select_fields(array('id', 'order_sn', 'esta', 'tbl', 'customer_id', 'apply_type',
        'rowid', 'brokerid_a', 'broker_name_a', 'brokerid_b', 'broker_name_b'));

      //消息模块MODEL
      $this->load->model('message_base_model');

      //循环合作信息，给甲方经纪人发送系统站内信
      for ($i = 0; $i < $unaccpet_num; $i++) {
        $offset = $per_num * $i;
        $unaccepet_info = $this->cooperate_model->get_list_by_cond($cond_where, $offset, $per_num);

        //循环发送站内信通知经纪人\拒绝合作\扣除甲方合作满意度数值
        foreach ($unaccepet_info as $key => $value) {
          $cid = intval($value['id']);
          $order_sn = strip_tags($value['order_sn']);
          $broker_id_a = intval($value['brokerid_a']);
          $broker_name_a = strip_tags($value['broker_name_a']);
          $broker_id_b = intval($value['brokerid_b']);
          $broker_name_b = strip_tags($value['broker_name_b']);
          $fromer = '';
          $url_a = '/my_growing_punish/index/';
          $url_b = '/cooperate/send_order_list/';

          //合作失败[此步骤默认甲方经纪人拒绝]
          $step = 1;
          $refuse_reason = array('step' => $step, 'broker_id' => $broker_id_a, 'type' => 4, 'reason' => '甲方未及时处理');
          $this->cooperate_model->refuse_to_cooperation($cid, $broker_id_a, $step, $refuse_reason);

          //扣除甲方合作满意度并通过站内信通知
          $score = $this->api_broker_sincere_model->update_attitude($broker_id_a, 'whether_accept_cooperate');

          //添加处罚记录
          if (!empty($score)) {
            //获取合作房源信息
            $temp_houseinfo = $this->cooperate_model->get_house_att_by_cid($cid, 0);
            $cooperate_houseinfo = !empty($temp_houseinfo) ? $temp_houseinfo[$cid] : array();
            $this->api_broker_sincere_model->punish(0, $broker_id_a, 'whether_accept_cooperate',
              $score, $order_sn, $cooperate_houseinfo);
          }

          $tbl = $value['tbl'];
          $params['name'] = $broker_name_a;
          $params['type'] = 'f';
          $params['id'] = $value['rowid'];
          $params['id'] = format_info_id($params['id'], $tbl);
          $params['pf'] = 0.1;
          //发送双方消息
          //33
          $this->message_base_model->add_message($message_type_a, $broker_id_a, $broker_name_a, $url_a, $params);
          $this->message_base_model->add_message($message_type_b, $broker_id_b, $broker_name_b, $url_b, $params);
        }

        sleep(1);
      }
    }

    job_end($job_referer_url);
    echo 'over';
  }


  /*
   * 合作流程-规定时间内仍然未确认佣金分配，提醒乙方经纪人确认佣金分配
   * @param strnig $city 城市拼音缩写
   * @param float $limit_hours 规定的小时数
   * @return void
   */
  public function send_confirm_commission_message_to_broker_b($limit_hours = 48)
  {
    $job_referer_url = MLS_URL . '/job_cooperate_crontab/send_confirm_commission_message_to_broker_b/48/';
    job_start($job_referer_url);

    $city = $this->input->get('city');
    $city = strip_tags(trim($city));
    if ($city == '') {
      exit('缺少城市参数无法执行！');
    }
    $this->config->set_item('login_city', $city);

    //加载合作MODEL
    $this->load->model('cooperate_model');

    $limit_hours = floatval($limit_hours);  //规定小时数
    $unconfirm_num = 0;  //未确认的合作个数
    $message_type = 40;  //通知乙方确认佣金分配

    //查询已申请超过48小时以上，并且经纪人仍未确认佣金分配的个数
    $limit_time = time() - $limit_hours * 3600;
    $cond_where = "esta = 3 AND dateline <= '" . $limit_time . "'";
    $unconfirm_num = $this->cooperate_model->get_cooperate_num_by_cond($cond_where);

    if ($unconfirm_num > 0) {
      $per_num = 30;
      $unconfirm_info = array();
      $this->cooperate_model->set_select_fields(array('id', 'order_sn', 'esta', 'brokerid_b', 'broker_name_b'));

      //消息模块MODEL
      $this->load->model('message_base_model');

      //循环合作信息，给甲方经纪人发送系统站内信
      for ($i = 0; $i < $unconfirm_num; $i++) {
        $offset = $per_num * $i;
        $unconfirm_info = $this->cooperate_model->get_list_by_cond($cond_where, $offset, $per_num);

        //循环发送站内信通知甲方经纪人接受合作
        foreach ($unconfirm_info as $key => $value) {
          $order_sn = strip_tags($value['order_sn']);
          $broker_id_b = intval($value['brokerid_b']);
          $broker_name_b = strip_tags($value['broker_name_b']);
          $fromer = $value['broker_name_a'];
          $url_b = '/cooperate/send_order_list/?cid=' . $value['id'];

          //发送甲方消息
          //佣金 去除
          $this->message_base_model->pub_message($message_type, $broker_id_b, $broker_name_b, $fromer, $order_sn, $url_b);
        }
        sleep(1);
      }
    }

    job_end($job_referer_url);
    echo 'over';
  }


  /*
   * 合作流程-规定时间内仍然未确认佣金分配，更改合作状态为合作失败
   * @param strnig $city 城市拼音缩写
   * @param float $limit_hours 规定的小时数
   * @return void
   */
  public function update_cooperate_to_failed_uncofirm($limit_hours = 72)
  {
    $job_referer_url = MLS_URL . '/job_cooperate_crontab/update_cooperate_to_failed_uncofirm/72/';
    job_start($job_referer_url);

    $city = $this->input->get('city');
    $city = strip_tags(trim($city));
    if ($city == '') {
      exit('缺少城市参数无法执行！');
    }
    $this->config->set_item('login_city', $city);

    //加载合作MODEL
    $this->load->model('cooperate_model');

    $limit_hours = floatval($limit_hours);  //规定小时数
    $unconfirm_num = 0;  //未接收的合作个数
    $message_type_a = '38a';  //通知甲方的消息类型
    $message_type_b = '38b';  //通知乙方的消息类型

    //查询已申请超过72小时以上，并且甲方经纪人仍然没有接受的合作信息
    $limit_time = time() - $limit_hours * 1;
    $cond_where = "esta = 3 AND dateline <= '" . $limit_time . "'";
    $unconfirm_num = $this->cooperate_model->get_cooperate_num_by_cond($cond_where);

    //循环合作信息，更改合作状态为合作失败
    if ($unconfirm_num > 0) {
      $per_num = 30;
      $unaccepet_info = array();
      $this->cooperate_model->set_select_fields(array('id', 'order_sn', 'esta',
        'brokerid_a', 'broker_name_a', 'brokerid_b', 'broker_name_b'));

      //消息模块MODEL
      $this->load->model('message_base_model');

      $this->load->model('api_broker_sincere_model');
      //循环合作信息，给甲方经纪人发送系统站内信
      for ($i = 0; $i < $unconfirm_num; $i++) {
        $offset = $per_num * $i;
        $unconfirm_info = $this->cooperate_model->get_list_by_cond($cond_where, $offset, $per_num);

        //循环发送站内信通知经纪人\拒绝合作\扣除甲方合作满意度数值
        foreach ($unconfirm_info as $key => $value) {
          $cid = intval($value['id']);
          $order_sn = strip_tags($value['order_sn']);
          $broker_id_a = intval($value['brokerid_a']);
          $broker_name_a = strip_tags($value['broker_name_a']);
          $broker_id_b = intval($value['brokerid_b']);
          $broker_name_b = strip_tags($value['broker_name_b']);
          $fromer = '';
          $url_a = '/cooperate/accept_order_list/';
          $url_b = '/my_growing_punish/index/';

          //合作失败[此步骤默认乙方拒绝]
          $step = 2;
          $refuse_reason = array('step' => $step, 'broker_id' => $broker_id_b, 'type' => 4, 'reason' => '接收方未及时处理');
          $this->cooperate_model->refuse_to_cooperation($cid, $broker_id_b, $step, $refuse_reason);

          //未及时确认佣金分配扣除乙方合作满意度并通过站内信通知
          $score = $this->api_broker_sincere_model->update_attitude($broker_id_b, 'whether_accept_brokerage');

          //添加处罚记录
          if (!empty($score)) {
            //获取合作房源信息
            $temp_houseinfo = $this->cooperate_model->get_house_att_by_cid($cid, 0);
            $cooperate_houseinfo = !empty($temp_houseinfo) ? $temp_houseinfo[$cid] : array();
            $this->api_broker_sincere_model->punish(0, $broker_id_b, 'whether_accept_brokerage', $score, $order_sn, $cooperate_houseinfo);
          }

          //发送双方消息
          //佣金 去除
           $this->message_base_model->pub_message($message_type_a , $broker_id_a , $broker_name_a , $fromer , $order_sn , $url_a);
           $this->message_base_model->pub_message($message_type_b , $broker_id_b , $broker_name_b , $fromer , $order_sn , $url_b);
        }

        sleep(1);
      }
    }

    job_end($job_referer_url);
    echo 'over';
  }


  /*
   * 合作流程-合作成功状态后，规定时间内没有提交合作结果，则更改合作为逾期失败状态
   * @param strnig $city 城市拼音缩写
   * @param float $limit_days 规定的天数
   * @return void
   */
  public function update_cooperate_to_overdue_unsub($limit_days = 90)
  {

    $job_referer_url = MLS_URL . '/job_cooperate_crontab/update_cooperate_to_overdue_unsub/90/';
    job_start($job_referer_url);


    $city = $this->input->get('city');
    $city = strip_tags(trim($city));

    if ($city == '') {
      exit('缺少城市参数无法执行！');
    }

    $this->config->set_item('login_city', $city);

    //加载合作MODEL
    $this->load->model('cooperate_model');

    $limit_days = floatval($limit_days);
    $message_type = '1-8';  //消息类型
    $unsub_num = 0;     //规定时间内未提交合作结果个数

    //查询已申请超过3个月仍然未提交合作结果，则更改合作为逾期失败状态
    $limit_time = time() - $limit_days * 86400;
    $cond_where = "esta = 4 AND dateline <= '" . $limit_time . "'";
    $unsub_num = $this->cooperate_model->get_cooperate_num_by_cond($cond_where);

    if ($unsub_num > 0) {
      $per_num = 30;
      $unsub_info = array();
      $this->cooperate_model->set_select_fields(array('id', 'order_sn', 'esta',
        'rowid', 'brokerid_a', 'broker_name_a', 'brokerid_b', 'broker_name_b'));

      //循环合作信息，给甲方经纪人发送系统站内信
      for ($i = 0; $i < $unsub_num; $i++) {
        $offset = $per_num * $i;
        $unsub_info = $this->cooperate_model->get_list_by_cond($cond_where, $offset, $per_num);

        //消息模块MODEL
        $this->load->model('message_base_model');

        //循环发送站内信通知甲方经纪人接受合作
        foreach ($unsub_info as $key => $value) {
          $cid = intval($value['id']);
          $tbl = intval($value['tbl']);
          $order_sn = strip_tags($value['order_sn']);
          $broker_id_a = intval($value['brokerid_a']);
          $broker_name_a = strip_tags($value['broker_name_a']);
          $broker_id_b = intval($value['brokerid_b']);
          $broker_name_b = strip_tags($value['broker_name_b']);
          $fromer = '';
          $url_a = '/cooperate/accept_order_list/?cid=' . $value['id'];
          $url_b = '/cooperate/send_order_list/?cid=' . $value['id'];

          //成交逾期失败
          $this->cooperate_model->overdue_failed_cooperate($cid);

          //发送双方消息
          $params['type'] = "f";
          $params['name'] = $fromer;
          $params['id'] = $value['rowid'];
          //33
          $this->message_base_model->add_message($message_type, $broker_id_a, $broker_name_a, $url_a, $params);
          //发送双方消息
          $params['type'] = "";
          $params['name'] = $fromer;
          $params['id'] = $value['rowid'];
          $this->message_base_model->add_message($message_type, $broker_id_b, $broker_name_b, $url_b, $params);
        }

        sleep(1);
      }
    }


    job_end($job_referer_url);
    echo 'over';
  }
}

/* End of file Job_cooperate_crontab.php */
/* Location: ./application/mls/controllers/Job_cooperate_crontab.php */
