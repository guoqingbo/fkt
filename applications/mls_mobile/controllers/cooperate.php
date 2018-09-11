<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 房客源合作控制器
 * @package     mls
 * @subpackage  Controllers
 * @category    Controllers
 * @author      fisher
 */
class Cooperate extends MY_Controller
{

  /**
   * 经纪人id
   *
   * @access private
   * @var int
   */
  private $_broker_id = 0;

  /**
   * 当前页码
   *
   * @access private
   * @var string
   */
  private $_current_page = 1;

  /**
   * 每页条目数
   *
   * @access private
   * @var int
   */
  private $_limit = 20;

  /**
   * 偏移
   *
   * @access private
   * @var int
   */
  private $_offset = 0;

  /**
   * 条目总数
   *
   * @access private
   * @var int
   */
  private $_total_count = 0;

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();

    $this->load->model('cooperate_model');
    $this->_broker_id = $this->user_arr['broker_id'];
    $this->load->model('push_func_model');
    $this->load->library('Verify');
    $this->load->model('agency_model');
    $this->load->model('broker_info_model');
    $this->load->model('operate_log_model');
  }

  /*
     * 申请合作页面
    * @param void
    * @return void
    */
  public function apply_cooperate()
  {
    //房源类型
    $tbl = strip_tags($this->input->get('tbl', TRUE));
    $tbl = $tbl == 0 ? 'sell' : 'rent';
    //房源编号
    $rowid = intval($this->input->get('rowid', TRUE));

    //被申请的客源编号
    $customer_id = intval($this->input->get('customer_id', TRUE));

    //甲方经纪人帐号
    $broker_a_id = intval($this->input->get('broker_id', TRUE));

    //申请人经纪人帐号
    $broker_b_id = intval($this->user_arr['broker_id']);

    //合作类型
    $apply_type = intval($this->input->get('apply_type', TRUE));

    $apply_type = $apply_type > 0 ? $apply_type : 1;

    $cooperate_info = array();
    /***
     * $cooperate_info['houseinfo'] = array();
     * $cooperate_info['brokerinfo_a'] = array();
     * $cooperate_info['brokerinfo_b'] = array();**/

    if ($rowid > 0 && $broker_a_id > 0 && in_array($tbl, array('sell', 'rent'))) {
      $house = $this->cooperate_model->get_cooperate_house($tbl, $rowid);
      //获得房源详情中的合作悬赏
      if (isset($house['cooperate_reward']) && !empty($house['cooperate_reward'])) {
        $cooperate_reward = $house['cooperate_reward'];
      } else {
        $cooperate_reward = 0;
      }

      //根据经纪人，获得所属公司名称
      $company_name = '';
      if (isset($broker_a_id) && !empty($broker_a_id)) {
        $a_broker_data = $this->broker_info_model->get_one_by(array('broker_id' => intval($broker_a_id)));
        if (is_full_array($a_broker_data)) {
          $company_where_cond = array(
            'id' => $a_broker_data['company_id'],
            'company_id' => 0
          );
          $company_data = $this->agency_model->get_one_by($company_where_cond);
          if (is_full_array($company_data)) {
            $company_name = $company_data['name'];
          }
        }
      }

      //初始佣金比例
      if ($tbl == 'sell') {
        $this->load->model('sell_house_share_ratio_model');
        $init_ratio = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
      } else if ($tbl == 'rent') {
        $this->load->model('rent_house_share_ratio_model');
        $init_ratio = $this->rent_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
      }
      $unit = $house['tbl'] == 'sell' ? '万' : '元/月';
      $cooperate_info['house'] = array(
        'rowid' => $rowid, 'tbl' => ($tbl == 'sell' ? 0 : 1),
        'block_name' => $house['blockname'], 'room_hall' => $house['room'] . '室' . $house['hall'] . '厅',
        'price' => strip_end_0($house['price']) . $unit, 'area' => strip_end_0($house['buildarea']),
        'buyer_ratio' => strip_end_0($init_ratio['buyer_ratio']), 'seller_ratio' => strip_end_0($init_ratio['seller_ratio']),
        'a_ratio' => strip_end_0($init_ratio['a_ratio']), 'b_ratio' => strip_end_0($init_ratio['b_ratio']),
        'cooperate_reward' => $cooperate_reward,
        'reward_type' => $house['reward_type']
      );
      if ($house['reward_type'] != 2 && $tbl == 'sell') {
        //获取出售信息基本配置资料
        $this->load->model('house_config_model');
        $config = $this->house_config_model->get_config();
        $this->load->model('sell_house_model');
        $commission_ratio = $this->sell_house_model->get_commission_ratio_id($house['commission_ratio']);
        $cooperate_info['house']['commission_ratio_arr'] = $this->sell_house_model->get_commission_ratio($config['commission_ratio'][$commission_ratio]);
      }
      $brokerinfo_a = $this->cooperate_model->get_cooperate_broker($broker_a_id);
      //经纪人信用积分模块
      $this->load->model('api_broker_sincere_model');

      //甲方信息
      $appraise_avg_info_a = $this->api_broker_sincere_model->get_appraise_and_avg($broker_a_id);
      //平均好评率
      $trust_appraise_avg = $this->api_broker_sincere_model->good_avg_rate($broker_a_id);
      $trust_appraise_count = $this->api_broker_sincere_model->get_trust_appraise_count($broker_a_id);
      $trust_level_a = $this->api_broker_sincere_model->get_level_id_by_trust($brokerinfo_a['trust']);
      //没有好评率时
      if ($trust_appraise_count['good_rate'] == '') {
        $trust_appraise_count['good_rate'] = '--';
        $good_rate_hot = 0;//低
      } else {
        $good_rate_hot = $trust_appraise_avg['good_rate_avg_high'] < 0 ? 0 : 1;
      }
      //合作成功率平均值
      $this->load->model('cooperate_suc_ratio_base_model');
      $avg_cop_suc_ratio = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
      $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_a_id);
      if ($cop_succ_ratio_info['cop_succ_ratio'] == '') {
        $cop_succ_ratio_info['cop_succ_ratio'] = '--';
        $cop_suc_ratio_hot = 0;//低
      } else {
        $cop_suc_ratio_hot = $cop_succ_ratio_info['cop_succ_ratio'] >= $avg_cop_suc_ratio ? 1 : 0;
      }
      $cooperate_info['cooperate_brokerinfo'] = array(
        'broker_id' => $brokerinfo_a['broker_id'],
        'broker_name' => $brokerinfo_a['truename'],
        'phone' => $brokerinfo_a['phone'],
        'agency_name' => $brokerinfo_a['agency_name'],
        'photo' => $brokerinfo_a['photo'],
        'good_rate' => strip_end_0($trust_appraise_count['good_rate']) . '%',
        'good_rate_hot' => $good_rate_hot,
        'cop_suc_ratio' => strip_end_0($cop_succ_ratio_info['cop_succ_ratio']) . '%',
        'cop_suc_ratio_hot' => $cop_suc_ratio_hot,
        'trust_level' => $trust_level_a, 'infomation' => $appraise_avg_info_a['infomation']['score'],
        'infomation_hot' => $appraise_avg_info_a['infomation']['rate'] >= 0 ? 1 : 0,
        'attitude' => $appraise_avg_info_a['attitude']['score'],
        'attitude_hot' => $appraise_avg_info_a['attitude']['rate'] >= 0 ? 1 : 0,
        'business' => $appraise_avg_info_a['business']['score'],
        'business_hot' => $appraise_avg_info_a['business']['rate'] >= 0 ? 1 : 0,
        'company_name' => $company_name
      );
      $brokerinfo_b = $this->cooperate_model->get_cooperate_broker($broker_b_id);
      //操作加密字符串
      $secret_param = array('tbl' => $tbl, 'rowid' => $rowid, 'customer_id' => $customer_id,
        'broker_a_id' => $broker_a_id, 'broker_b_id' => $broker_b_id, 'apply_type' => $apply_type);
      $cooperate_info['secret_key'] = $this->verify->user_enrypt($secret_param);
      /**
       * //乙方信息
       * $data['appraise_avg_info_b'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_b_id);
       * $data['trust_info_b'] = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_b_id);**/
      $cooperate_info['our_brokerinfo'] = array(
        'broker_id' => $brokerinfo_b['broker_id'],
        'broker_name' => $brokerinfo_b['truename'],
        'phone' => $brokerinfo_b['phone'],
        'agency_name' => $brokerinfo_b['agency_name'],
      );
      $this->result(1, '查询成功', $cooperate_info);
    } else {
      $this->result(0, '查询失败');
    }
  }

  //提交合作信息
  public function add_cooperation_info()
  {
    $data_arr = array();
    $devicetype = $this->input->post('api_key', TRUE);
    $deviceid = $this->input->post('deviceid', TRUE);
    $data_arr['apply_type'] = $this->input->post('apply_type', TRUE);
    $data_arr['customer_id'] = $this->input->post('customer_id', TRUE);
    if ($data_arr['customer_id'] == '') {
      $data_arr['customer_id'] = 0;
    }
    $data_arr['tbl'] = $this->input->post('tbl', TRUE);
    $data_arr['tbl'] = $data_arr['tbl'] == 0 ? 'sell' : 'rent';
    $data_arr['rowid'] = $this->input->post('rowid', TRUE);

    //乙方
    $brokerid_b = $this->user_arr['broker_id'];
    //甲方
    $brokerid_a = $this->input->post('broker_id', TRUE);
    $this->load->model('api_broker_model');
    $brokerinfo_a = serialize($this->cooperate_model->get_cooperate_broker($brokerid_a));
    $brokerinfo_b = serialize($this->cooperate_model->get_cooperate_broker($brokerid_b));
    $brokerinfo_a_arr = unserialize($brokerinfo_a);
    $brokerinfo_b_arr = unserialize($brokerinfo_b);
    $data_arr['agentid_a'] = $brokerinfo_a_arr['agency_id'];
    $data_arr['brokerid_a'] = $brokerinfo_a_arr['broker_id'];
    $data_arr['broker_name_a'] = $brokerinfo_a_arr['truename'];
    $data_arr['phone_a'] = $brokerinfo_a_arr['phone'];
    $data_arr['agentid_b'] = $brokerinfo_b_arr['agency_id'];
    $data_arr['brokerid_b'] = $brokerinfo_b_arr['broker_id'];
    $data_arr['broker_name_b'] = $brokerinfo_b_arr['truename'];
    $data_arr['phone_b'] = $brokerinfo_b_arr['phone'];
    $house_info = $this->cooperate_model->get_cooperate_house($data_arr['tbl'], $data_arr['rowid']);
    //出售房源进入合作流程，添加该房源的奖励类别和奖励金额到合作表
    if ('sell' == $data_arr['tbl']) {
      if (isset($house_info['reward_type']) && !empty($house_info['reward_type'])) {
        $data_arr['reward_type'] = intval($house_info['reward_type']);//奖励类别
      }
      if (isset($house_info['cooperate_reward']) && !empty($house_info['cooperate_reward'])) {
        $data_arr['reward_money'] = intval($house_info['cooperate_reward']);//奖励金额
      }
    }
    if ($house_info['reward_type'] != 2 && $data_arr['tbl'] == 'sell') {
      //获取出售信息基本配置资料
      $this->load->model('house_config_model');
      $config = $this->house_config_model->get_config();
      $this->load->model('sell_house_model');
      $commission_ratio = $this->sell_house_model->get_commission_ratio_id($house_info['commission_ratio']);
      $house_info['commission_ratio_arr'] = $this->sell_house_model->get_commission_ratio($config['commission_ratio'][$commission_ratio]);
    }
    $data_arr['house'] = serialize($house_info);
    $data_arr['block_name'] = $house_info['blockname'];//小区名称

    $data_arr['broker_a'] = $brokerinfo_a;
    $data_arr['broker_b'] = $brokerinfo_b;
    $cop_secret_key = $this->input->post('secret_key', TRUE); //加密字符串
    //操作加密字符串
    $secret_param = array('tbl' => $data_arr['tbl'], 'rowid' => $data_arr['rowid'], 'customer_id' => $data_arr['customer_id'],
      'broker_a_id' => $data_arr['brokerid_a'], 'broker_b_id' => $data_arr['brokerid_b'], 'apply_type' => $data_arr['apply_type']);
    $secret_key = $this->verify->user_enrypt($secret_param);
    if ($cop_secret_key == $secret_key && $data_arr['brokerid_a'] > 0
      && $data_arr['brokerid_b'] > 0 && $data_arr['rowid'] > 0
    ) {
      //不能和自己合作
      if ($data_arr['brokerid_a'] == $data_arr['brokerid_b']) {
        $result = array('is_ok' => 0, 'msg' => '不能与自己合作');
        $this->result($result['is_ok'], $result['msg']);
        exit;
      }

      //判断是否已经申请过合作信息
      $cooperate_num = $this->cooperate_model->get_valid_cooperate_num($data_arr['tbl'], $data_arr['brokerid_a'], $data_arr['brokerid_b'], $data_arr['rowid']);
      if ($cooperate_num >= 1) {
        $result = array('is_ok' => 0, 'msg' => '你已经申请过该合作，无法再次合作');
        $this->result($result['is_ok'], $result['msg']);
        exit;
      }

      //添加合作信息
      $result_add = $this->cooperate_model->add_cooperate($data_arr);

      //返回提交结果
      if (is_array($result_add) && !empty($result_add)) {
        //操作日志
        $add_log_text = '';
        //获得经纪人所在的公司门店名
        $this->load->model('api_broker_model');
        $brokerinfo_a = $this->api_broker_model->get_baseinfo_by_broker_id(intval($data_arr['brokerid_a']));
        //接收方所属门店名
        $agency_name_a = $brokerinfo_a['agency_name'];
        $this->load->model('agency_model');
        $agency_a = $this->agency_model->get_by_id(intval($brokerinfo_a['company_id']));
        //接收方所属公司名
        $company_name_a = $agency_a['name'];
        //房源申请
        if ('1' == $data_arr['apply_type']) {
          if ('sell' == $data_arr['tbl']) {
            $add_log_text .= '出售房源：' . 'CS' . $data_arr['rowid'] . ' ' . $data_arr['block_name'] . ' ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
          } else if ('rent' == $data_arr['tbl']) {
            $add_log_text .= '出租房源：' . 'CZ' . $data_arr['rowid'] . ' ' . $data_arr['block_name'] . ' ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
          }
          //客源申请
        } else if ('2' == $data_arr['apply_type']) {
          if ('sell' == $data_arr['tbl']) {
            $this->load->model('buy_customer_model');
            $this->buy_customer_model->set_id(intval($data_arr['customer_id']));
            $customer_info = $this->buy_customer_model->get_info_by_id();
            $add_log_text .= '求购客源：' . 'QG' . $data_arr['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '万元 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
          } else if ('rent' == $data_arr['tbl']) {
            $this->load->model('rent_customer_model');
            $this->rent_customer_model->set_id(intval($data_arr['customer_id']));
            $customer_info = $this->rent_customer_model->get_info_by_id();
            $add_log_text .= '求租客源：' . 'QZ' . $data_arr['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '元/月 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
          }
        }

        $add_log_param = array();
        $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['agency_id'];
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $this->user_arr['truename'];
        $add_log_param['type'] = 17;
        $add_log_param['text'] = $add_log_text;
        if ($devicetype == 'android') {
          $add_log_param['from_system'] = 2;
        } else {
          $add_log_param['from_system'] = 3;
        }
        $add_log_param['device_id'] = $deviceid;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = '127.0.0.1';
        $add_log_param['from_host_name'] = '127.0.0.1';
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();
        $this->operate_log_model->add_operate_log($add_log_param);

        $result = array('is_ok' => 1, 'msg' => '您的合作信息已经成功提交！'
          . '请耐心等待甲方经纪人确认或者你可以直接联系对方确认！');

        $cid = !empty($result_add) ? intval($result_add['cid']) : 0;
        $order_sn = $result_add['order_sn'];
        $brokerid_a = $data_arr['brokerid_a'];
        $broker_name_a = $data_arr['broker_name_a'];
        $fromer = $data_arr['broker_name_b'];
        $msg_type = '1-1-1';
        $url_a = '/cooperate/accept_order_list/?cid=' . $cid;
        $n_params['block_name'] = $data_arr['block_name'];
        $params['name'] = $fromer;
        if ($data_arr['apply_type'] == 1) {
          $params['type'] = "f";
          $params['id'] = $data_arr['rowid'];
        } elseif ($data_arr['apply_type'] == 2) {
          $params['id'] = $data_arr['customer_id'];
          $tbl .= '_customer';
        }
        $params['id'] = format_info_id($params['id'], $tbl);

        //发送站内信通知甲方接收到合作申请
        $this->load->model('message_base_model');
        $msg_id = $this->message_base_model->add_message($msg_type, $brokerid_a, $broker_name_a, $url_a, $n_params);
        //发送推送消息
        $this->push_func_model->send(1, 1, 1, $brokerid_b, $brokerid_a, array('msg_id' => $msg_id),
          array('broker_name' => $this->user_arr['truename']));
      } else {
        $result = array('is_ok' => 0, 'msg' => '合作申请提交失败');
      }
    } else {
      $result = array('is_ok' => 0, 'msg' => '数据异常，无法申请合作！');
    }

    $this->result($result['is_ok'], $result['msg']);
  }

  //申请客源合作选择房源弹框
  public function apply_customer_cooperate_window()
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //客源编号
    $customer_id = $post_param['customer_id'];
    $kind = $post_param['kind'];
    $broker_id = intval($this->user_arr['broker_id']); //经纪人帐号
    $kind = $kind == 1 ? 'buy_customer' : 'rent_customer';
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;// 获取当前页数
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);
    //房源列表
    $house_list = array();
    $house_num = '';
    $customer_info = array();
    if ($kind == 'buy_customer' && $customer_id > 0) {
      //客源信息
      $this->load->model('sell_house_model');
      $this->load->model('buy_customer_model');
      $this->buy_customer_model->set_id($customer_id);
      $customer_info = $this->buy_customer_model->get_info_by_id();

      //根据客源编号获取符合条件的房源
      if (is_array($customer_info) && !empty($customer_info)) {
        $dist_id1 = intval($customer_info['dist_id1']);
        $dist_id2 = intval($customer_info['dist_id2']);
        $dist_id3 = intval($customer_info['dist_id3']);
        $price_min = floatval($customer_info['price_min']);
        $price_max = floatval($customer_info['price_max']);
        $area_min = floatval($customer_info['area_min']);
        $area_max = floatval($customer_info['area_max']);
        $property_type = intval($customer_info['property_type']);

        $cond_distid = '';
        $cond_distid .= $dist_id1 > 0 ? $dist_id1 : '';
        $cond_distid .= $dist_id2 > 0 ? "," . $dist_id2 : '';
        $cond_distid .= $dist_id3 > 0 ? "," . $dist_id3 : '';

        if ($dist_id2 > 0 || $dist_id3 > 0) {
          $cond_distid = " AND district_id IN (" . $cond_distid . ") ";
        } else {
          $cond_distid = " AND district_id = '" . $cond_distid . "' ";
        }

        //获取房源
        $house_list = array();
        $this->load->model('rent_house_model');
        $cond_where = "broker_id = '" . $broker_id . "'" . $cond_distid;

        //面积条件
        if ($area_min > 0 || $area_max > 0) {
          $cond_where .= " AND buildarea >= '" . $area_min . "' AND "
            . "buildarea <= '" . $area_max . "'";
        }

        //价格条件
        if ($price_min > 0 || $price_max > 0) {
          $cond_where .= " AND price >= '" . $price_min . "' AND "
            . "price <= '" . $price_max . "'";
        }

        $cond_where .= " AND sell_type = '" . $property_type . "' AND isshare = '1' AND status != 5";
        $house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
        $house_list = $this->sell_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);

      }
      //房源类型
      $data['tbl'] = 'sell';
    } else if ($kind == 'rent_customer' && $customer_id > 0) {

      $this->load->model('rent_customer_model');
      $this->load->model('rent_house_model');
      $this->rent_customer_model->set_id($customer_id);
      $customer_info = $this->rent_customer_model->get_info_by_id();

      //根据客源编号获取符合条件的房源
      if (is_array($customer_info) && !empty($customer_info)) {
        $dist_id1 = intval($customer_info['dist_id1']);
        $dist_id2 = intval($customer_info['dist_id2']);
        $dist_id3 = intval($customer_info['dist_id3']);
        $price_min = floatval($customer_info['price_min']);
        $price_max = floatval($customer_info['price_max']);
        $area_min = floatval($customer_info['area_min']);
        $area_max = floatval($customer_info['area_max']);
        $property_type = intval($customer_info['property_type']);

        $cond_distid = '';
        $cond_distid .= $dist_id1 > 0 ? $dist_id1 : '';
        $cond_distid .= $dist_id2 > 0 ? "," . $dist_id2 : '';
        $cond_distid .= $dist_id3 > 0 ? "," . $dist_id3 : '';

        if ($dist_id2 > 0 || $dist_id3 > 0) {
          $cond_distid = " AND district_id IN (" . $cond_distid . ") ";
        } else {
          $cond_distid = " AND district_id = '" . $cond_distid . "' ";
        }

        //获取房源
        $house_list = array();
        $this->load->model('rent_house_model');
        $cond_where = "broker_id = '" . $broker_id . "'" . $cond_distid;

        //面积条件
        if ($area_min > 0 || $area_max > 0) {
          $cond_where .= " AND buildarea >= '" . $area_min . "' AND "
            . "buildarea <= '" . $area_max . "'";
        }

        //价格条件
        if ($price_min > 0 || $price_max > 0) {
          $cond_where .= " AND price >= '" . $price_min . "' AND "
            . "price <= '" . $price_max . "'";
        }

        $cond_where .= " AND sell_type = '" . $property_type . "' AND isshare = '1' AND status != 5";
        $house_num = $this->rent_house_model->get_housenum_by_cond($cond_where);
        $house_list = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);
      }
      //房源类型
      $data['tbl'] = 'rent';
    }

    //区属板块信息
    $this->load->model('district_model');
    $district_arr = $this->district_model->get_district();
    $district_num = count($district_arr);
    for ($i = 0; $i < $district_num; $i++) {
      $temp_dist_arr[$district_arr[$i]['id']] = $district_arr[$i];
    }
    $district_arr = $temp_dist_arr;

    //板块数据
    $arr_street = $this->district_model->get_street();
    $street_num = count($arr_street);
    $temp_street_arr = array();
    for ($i = 0; $i < $street_num; $i++) {
      $temp_street_arr[$arr_street[$i]['id']] = $arr_street[$i];
    }
    //加载求购客户MODEL
    $this->load->model('buy_customer_model');
    $data['buy_config'] = $this->buy_customer_model->get_base_conf();
    //获取出售信息基本配置资料
    $this->load->model('house_config_model');
    $data['house_config'] = $this->house_config_model->get_config();
    //客源房源信息
    $data['customer_info'] = $customer_info;
    $data['house_list'] = $house_list;
    $data['house_num'] = $house_num;
    $data['district_arr'] = $district_arr;
    $data['street_arr'] = $temp_street_arr;
    $data['customer_kind'] = $kind;
    $unit = $kind == 'buy_customer' ? '万' : '元/月';
    //区属板块一
    $district_street = array();
    $district_street_1 = '';
    if ($customer_info['dist_id1'] > 0
      && isset($district_arr[$customer_info['dist_id1']]['district'])
    ) {
      $district_1 = $district_arr[$customer_info['dist_id1']]['district'];
      if ($customer_info['street_id1'] > 0
        && isset($temp_street_arr[$customer_info['street_id1']]['streetname'])
      ) {
        $street_1 = $temp_street_arr[$customer_info['street_id1']]['streetname'];
      }
      $district_street_1 = $district_1 . '-' . $street_1;
      $district_street[] = $district_street_1;
    }
    //区属板块二
    $district_street_2 = '';
    if ($customer_info['dist_id2'] > 0
      && isset($district_arr[$customer_info['dist_id2']]['district'])
    ) {
      $district_2 = $district_arr[$customer_info['dist_id2']]['district'];
      if ($customer_info['street_id2'] > 0
        && isset($temp_street_arr[$customer_info['street_id2']]['streetname'])
      ) {
        $street_2 = $temp_street_arr[$customer_info['street_id2']]['streetname'];
      }
      $district_street_2 = $district_2 . '-' . $street_2;
      $district_street[] = $district_street_2;
    }
    //区属板块二
    $district_street_3 = '';
    if ($customer_info['dist_id3'] > 0
      && isset($district_arr[$customer_info['dist_id3']]['district'])
    ) {
      $district_3 = $district_arr[$customer_info['dist_id3']]['district'];
      if ($customer_info['street_id3'] > 0
        && isset($temp_street_arr[$customer_info['street_id3']]['streetname'])
      ) {
        $street_3 = $temp_street_arr[$customer_info['street_id3']]['streetname'];
      }
      $district_street_3 = $district_3 . '-' . $street_3;
      $district_street[] = $district_street_3;
    }
    $new_house_list = array();
    if (is_full_array($house_list)) {
      foreach ($house_list as $v) {
        if ($kind == 'buy_customer') {
          $entrust = $v['entrust'] == 1 ? 1 : 2;
        } else {
          $entrust = $v['rententrust'] == 3 ? 1 : 2;
        }
        $new_house_list[] = array(
          'rowid' => $v['id'],
          'block_name' => $v['block_name'],
          'property_type' => $data['house_config']['sell_type']
          [$v['sell_type']],
          'district_street' => $district_arr[$v['district_id']]['district']
            . '-' . $temp_street_arr[$v['street_id']]['streetname'],
          'fitment' => $data['house_config']['fitment'][$v['fitment']],
          'room_hall' => $v['room'] . '室' . $v['hall'] . '厅',
          'area' => strip_end_0($v['buildarea']), 'price' => strip_end_0($v['price']) . $unit,
          'nature' => $v['nature'], 'keys' => $v['keys'],
          'entrust' => $entrust,
          'is_share' => $kind == 'rent_customer' ? $v['is_share'] : $v['isshare'],
          'pic' => $v['pic'],
          'floor_info' => $v['floor'] . '/' . $v['totalfloor'],
        );
      }
    }
    $result = array(
      'customer_info' => array(
        'broker_name' => $customer_info['broker_name'],
        'room' => $customer_info['room_min'] . '-'
          . $customer_info['room_max'] . '室',
        'area' => strip_end_0($customer_info['area_min']) . '-' . strip_end_0($customer_info['area_max']),
        'price' => strip_end_0($customer_info['price_min']) . '-' . strip_end_0($customer_info['price_max']) . $unit,
        'property_type' => $data['buy_config']['property_type'][$customer_info['property_type']],
        'district_street' => $district_street,
      ),
      'house_num' => $house_num,
      'house_list' => $new_house_list
    );
    $this->result(1, '查询成功', $result);
  }

  /**
   * 发起的合作申请
   *
   * @access  public
   * @return  void
   */
  public function send_order_list()
  {
    $this->_order_list('send');
  }

  /**
   * 收到的合作申请
   *
   * @access  public
   * @return  void
   */
  public function accept_order_list()
  {
    $this->_order_list('accept');
  }

  /**
   * 发起的合作申请/收到的合作申请
   *
   * @access  private
   * @param  string $type 类型("send":发起的;"accept":收到的)
   * @return  void
   */
  private function _order_list($type = 'send')
  {
    //模板使用数据
    $data = array();

    //条件数组
    $cond_where = array();
    $cond_where_in = array();

    //默认排序字段
    $order_key = 'dateline';
    //默认降序排序
    $order_by = 'DESC';
    //后缀
    if ($type == 'send') {
      $primary_postfix = "_b";    //主
      $secondary_postfix = "_a";  //次（合伙人）
    } else {
      $primary_postfix = "_a";
      $secondary_postfix = "_b";
    }
    $cond_where_in['brokerid' . $primary_postfix] = $this->user_arr['broker_id'];
    /**
     * $company_id = $this->user_arr['company_id'];
     * //获取所有分公司数组
     * $this->load->model('api_broker_model');
     * $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
     * //$data['agency_list'] = $agencys;
     *
     * //所有分公司id数组
     * $agency_ids = array();
     * foreach($agencys as $k =>$v)
     * {
     * $agency_ids[] = $v['agency_id'];
     * }
     * //权限条件
     * if(is_full_array($agency_ids))
     * {
     * $cond_where_in['agentid'.$primary_postfix] = $agency_ids;
     * }
     ***/
    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where = array_merge($cond_where, $cond_where_ext);

    //条件-我方门店

    $agentid_w = isset($post_param['agentid_w']) ? intval($post_param['agentid_w']) : 0;
    if ($agentid_w) {
      $cond_where['agentid' . $primary_postfix] = $agentid_w;

      //获取经纪人列表数组
      //$this->load->model('api_broker_model');
      //$brokers = $this->api_broker_model->get_brokers_agency_id($agentid_w);
      //$data['brokers'] = $brokers;
    }

    //条件-我方经纪人
    $brokerid_w = isset($post_param['brokerid_w']) ? intval($post_param['brokerid_w']) : 0;
    if ($brokerid_w) {
      $cond_where['brokerid' . $primary_postfix] = $brokerid_w;
    }

    //条件-合作经纪人姓名
    $broker_name = isset($post_param['broker_name']) ? trim($post_param['broker_name']) : "";
    if ($broker_name) {
      $cond_where['broker_name' . $secondary_postfix] = $broker_name;
    }

    //条件-手机
    $phone = isset($post_param['phone']) ? trim($post_param['phone']) : "";
    if ($phone) {
      $cond_where['phone' . $secondary_postfix] = $phone;
    }

    //条件-门店
    $agentid = isset($post_param['agentid']) ? trim($post_param['agentid']) : "";
    if ($agentid) {
      $cond_where['agentid' . $secondary_postfix] = $agentid;
    }
    //条件-门店
    $cond_where_like = array();
    $block_name = isset($post_param['block_name']) ? trim($post_param['block_name']) : '';
    if (!empty($block_name)) {
      $cond_where_like['block_name'] = $block_name;
    }
    //搜索条件中的待处理申请(1)、待评价合作(2)、合作生效(3)、交易成功(4)数量（随搜索条件变化）
    //搜索条件中的待处理申请(1)、待评价合作(2)、合作生效(3)、交易成功(4)数量（随搜索条件变化
    if ($type == 'send') {
      $wait_do_count = 'wait_do_b';
    } else {
      $wait_do_count = 'wait_do_a';
    }
    $data['estas_num']['all'] = $this->cooperate_model->get_cooperate_statistics_by_cond('all', $cond_where, $cond_where_in, $cond_where_like);
    $data['estas_num']['wait_process'] = $this->cooperate_model->get_cooperate_statistics_by_cond($wait_do_count, $cond_where, $cond_where_in, $cond_where_like);
    $data['estas_num']['wait_appraise'] = $this->cooperate_model->get_cooperate_statistics_by_cond('wait_appraise', $cond_where, $cond_where_in, $cond_where_like, $primary_postfix);
    $data['estas_num']['cooperate_valid'] = $this->cooperate_model->get_cooperate_statistics_by_cond('cop_effect', $cond_where, $cond_where_in, $cond_where_like);

    //条件-待处理申请(1)、待评价合作(2)、合作生效(3)、交易成功(4)
    $estas = isset($post_param['estas']) ? intval($post_param['estas']) : 0;
    switch ($estas) {
      case '1':
        $cond_where_in['esta'] = array(1, 2, 3);
        if ($type == 'send') {
          $cond_where_in['esta'] = array(3);
        } else {
          $cond_where_in['esta'] = array(1, 2);
        }
        break;
      case '2':
        $cond_where_in['esta'] = array(4);
        break;
      case '3':
        $cond_where_in['esta'] = array(6, 7, 8, 9, 11);
        $cond_where_in['step'] = array(3, 4);
        $cond_where_in['appraise' . $primary_postfix] = 0;
        break;
      case '4':
        $cond_where_in['esta'] = array(7);
        break;
    }

    //排序-房源
    $order_key_rowid = isset($post_param['order_key_rowid']) ? intval($post_param['order_key_rowid']) : 0;
    if ($order_key_rowid) {
      $order_key = "rowid";
    }
    //符合条件的总行数
    $this->cooperate_model->set_cond_where_in($cond_where_in);//设置where in条件
    $this->cooperate_model->set_cond_where_like($cond_where_like);//设置where in条件
    $this->_total_count =
      $this->cooperate_model->get_cooperate_num_by_cond($cond_where);

    //获取列表内容
    $this->cooperate_model->set_cond_where_in($cond_where_in);//设置where in条件
    $this->cooperate_model->set_cond_where_like($cond_where_like);//设置where in条件
    $list = $this->cooperate_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_key, $order_by);
    $new_list = array();
    if ($list) {
      //加载出售基本配置MODEL
      $this->load->model('house_config_model');

      //获取出售信息基本配置资料
      $config = $this->house_config_model->get_config();

      //状态数组
      $base_conf = $this->cooperate_model->get_base_conf();
      $esta_conf = $base_conf['esta'];
      $ids = array();

      foreach ($list as $key => $val) {
        $ids[] = $val['id'];
        $agency_ids[] = $val['agentid' . $secondary_postfix];
      }
      //查询房源信息
      $house_list = $this->cooperate_model->get_house_att_by_cid($ids);
      //查询合作信息
      $this->cooperate_model->set_select_fields(array());
      //出售和出租房源列表获取最新信息-
      $this->load->model('sell_house_model');
      $this->load->model('rent_house_model');
      foreach ($list as $key => $val) {
        $house_info = $house_list[$val['id']];
        $unit = $house_info['tbl'] == 'sell' ? '万' : '元/月';
        if ($house_info['tbl'] == 'sell') {
          $this->sell_house_model->set_id($house_info['rowid']);
          $new_house_info = $this->sell_house_model->get_info_by_id();
        } else {
          $this->rent_house_model->set_id($house_info['rowid']);
          $new_house_info = $this->rent_house_model->get_info_by_id();
        }
        if ($new_house_info['sell_type'] == 1 || $new_house_info['sell_type'] == 4) {
          $floor_info_rate = $new_house_info['floor'] / $new_house_info['totalfloor'];
          if ($floor_info_rate < 0.4) {
            $floor_info = '低';
          } else if ($floor_info_rate >= 0.4 && $floor_info_rate <= 0.7) {
            $floor_info = '中';
          } else {
            $floor_info = '高';
          }
        } else {
          $floor_info = '低';
        }
        //初始化合作双方
        $broker_a_ratio = '-';
        $broker_b_ratio = '-';
        $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($val['id']);
        //合作经纪人的基本信息
        $partner_info = unserialize($cooperate_info['broker' . $secondary_postfix]);
        if (!empty($cooperate_info['ratio'])) {
          $cooperate_ratio = unserialize($cooperate_info['ratio']);
          $broker_a_ratio = $cooperate_ratio['a_ratio'];
          $broker_b_ratio = $cooperate_ratio['b_ratio'];
        } else {
          if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'sell') {
            $this->load->model('sell_house_share_ratio_model');
            $cooperate_ratio = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_info['rowid']);
          } else if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'rent') {
            $this->load->model('rent_house_share_ratio_model');
            $cooperate_ratio = $this->rent_house_share_ratio_model->get_house_ratio_by_rowid($house_info['rowid']);
          }
          if ($cooperate_ratio) {
            $broker_a_ratio = strip_end_0($cooperate_ratio['a_ratio']);
            $broker_b_ratio = strip_end_0($cooperate_ratio['b_ratio']);
          }
        }
        //判断是否评论
        $appraise = 0;
        if ((in_array($val['esta'], array(7, 8, 9)) || (($val['esta'] == 6 || $val['esta'] == 10
              || $val['esta'] == 11) && $val['step'] >= 3)
          && ($val['brokerid' . $primary_postfix] == $this->user_arr['broker_id']))
        ) {
          $appraise = !$val['appraise' . $primary_postfix] ? 1 : 2;
        }
        //判断是否确认佣金
        $status = $val['step'] >= 3 ? 1 : 0;
        $this->load->model('api_broker_sincere_model');
        $trust = $this->api_broker_sincere_model->get_trust_level_by_broker_id(
          $val['brokerid' . $secondary_postfix]);
        $comfirm_deal = 0;
        //确认成交那一步
        if ($val['esta'] == 4) {
          //如果是房源
          if ($val['rowid'] && $val['customer_id'] == 0) {
            $comfirm_deal = $this->user_arr['broker_id']
            == $val['brokerid_a'] ? 1 : 0;
          } else //客源匹配
          {
            $comfirm_deal = $this->user_arr['broker_id']
            == $val['brokerid_b'] ? 1 : 0;
          }
        }

        if (isset($house_info['cooperate_reward']) && !empty($house_info['cooperate_reward'])) {
          $cooperate_reward = $house_info['cooperate_reward'];
        } else {
          $cooperate_reward = 0;
        }
        $new_list[$key] = array(
          'c_id' => $val['id'], 'esta' => $val['esta'],
          'order_sn' => $val['order_sn'],
          'esta_str' => $esta_conf[$val['esta']],
          'comfirm_deal' => $comfirm_deal,
          'broker_id' => $val['brokerid' . $secondary_postfix],
          'broker_name' => $val['broker_name' . $secondary_postfix],
          'agency_name' => $partner_info['agency_name'],
          'block_name' => $house_info['blockname'],
          'fitment' => $config['fitment'][$house_info['fitment']],
          'room_hall' => $house_info['room'] . '室' . $house_info['hall'] . '厅',
          'price' => strip_end_0($house_info['price']) . $unit,
          'area' => strip_end_0($house_info['buildarea']),
          'broker_a_ratio' => 50 . '%',
          'broker_b_ratio' => 50 . '%',
          'trust_level' => $trust['level_id'], 'status' => $status,
          'appraise' => $appraise, 'photo_url' => $partner_info['photo'],
          'cooperate_reward' => $cooperate_reward,
          'reward_type' => $val['reward_type'],
          'reward_money' => $val['reward_money'],
          'video_id' => empty($new_house_info['video_id']) ? '' : $new_house_info['video_id'],
          'video_pic' => empty($new_house_info['video_pic']) ? '' : $new_house_info['video_pic'],
          'districtname' => $house_info['districtname'],
          'streetname' => $house_info['streetname'],
          'photo' => $house_info['photo'],
          'floor_info' => $floor_info . '楼层/' . $new_house_info['totalfloor'],
        );
        if ($house_info['reward_type'] != 2 && $house_info['tbl'] == 'sell') {
          //获取出售信息基本配置资料
          $this->load->model('sell_house_model');
          $commission_ratio = $this->sell_house_model->get_commission_ratio_id($house_info['commission_ratio']);
          $commission_ratio_arr = $this->sell_house_model->get_commission_ratio($config['commission_ratio'][$commission_ratio]);
          $new_list[$key]['commission_ratio_arr'] = $commission_ratio_arr;
        }
      }
    }
    $data['list'] = $new_list;
    $this->result(1, '查询成功', $data);
  }

  //我接收的合作详情页
  public function my_accept_order()
  {
    $c_id = $this->input->get('c_id', TRUE);
    $new_cooperate_info = array();
    $log_arr = array();
    if ($c_id > 0) {
      $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);
      if (is_array($cooperate_info) && !empty($cooperate_info)) {
        //经纪人基础信息和信用积分模块
        $this->load->model('api_broker_base_model');
        $this->load->model('api_broker_sincere_model');
        //加载出售基本配置MODEL
        $this->load->model('house_config_model');
        //获取出售信息基本配置资料
        $config = $this->house_config_model->get_config();
        //合同房源信息
        $cooperate_info['house'] = !empty($cooperate_info['house']) ?
          unserialize($cooperate_info['house']) : array();
        //出售和出租房源列表获取最新信息-
        $this->load->model('sell_house_model');
        $this->load->model('rent_house_model');
        if ($cooperate_info['house']['tbl'] == 'sell') {
          $this->sell_house_model->set_id($cooperate_info['house']['rowid']);
          $new_house_info = $this->sell_house_model->get_info_by_id();
        } else {
          $this->rent_house_model->set_id($cooperate_info['house']['rowid']);
          $new_house_info = $this->rent_house_model->get_info_by_id();
        }
        if ($new_house_info['sell_type'] == 1 || $new_house_info['sell_type'] == 4) {
          $floor_info_rate = $new_house_info['floor'] / $new_house_info['totalfloor'];
          if ($floor_info_rate < 0.4) {
            $floor_info = '低';
          } else if ($floor_info_rate >= 0.4 && $floor_info_rate <= 0.7) {
            $floor_info = '中';
          } else {
            $floor_info = '高';
          }
        } else {
          $floor_info = '低';
        }
        $unit = $cooperate_info['tbl'] == 'sell' ? '万' : '元/月';
        $new_cooperate_info['house'] = array(
          'rowid' => $cooperate_info['house']['rowid'],
          'tbl' => $cooperate_info['tbl'] == 'sell' ? 0 : 1,
          'block_name' => $cooperate_info['house']['blockname'],
          'room_hall' => $cooperate_info['house']['room'] . '室'
            . $cooperate_info['house']['hall'] . '厅',
          'price' => strip_end_0($cooperate_info['house']['price']) . $unit,
          'area' => strip_end_0($cooperate_info['house']['buildarea']),
          'total_price' => strip_end_0($cooperate_info['price']) . $unit,
          'fitment' => $config['fitment'][$cooperate_info['house']['fitment']],
          'video_id' => empty($new_house_info['video_id']) ? '' : $new_house_info['video_id'],
          'video_pic' => empty($new_house_info['video_pic']) ? '' : $new_house_info['video_pic'],
          'districtname' => $cooperate_info['house']['districtname'],
          'streetname' => $cooperate_info['house']['streetname'],
          'photo' => $cooperate_info['house']['photo'],
          'floor_info' => $floor_info . '楼层/' . $new_house_info['totalfloor'],
        );

        //获得房源详情中的合作悬赏
        if (isset($cooperate_info['house']['cooperate_reward']) && !empty($cooperate_info['house']['cooperate_reward'])) {
          $new_cooperate_info['house']['cooperate_reward'] = $cooperate_info['house']['cooperate_reward'];
        } else {
          $new_cooperate_info['house']['cooperate_reward'] = 0;
        }

        $new_cooperate_info['step'] = $cooperate_info['step'];
        $new_cooperate_info['esta'] = $cooperate_info['esta'];
        $new_cooperate_info['reward_type'] = $cooperate_info['reward_type'];
        $new_cooperate_info['reward_money'] = $cooperate_info['reward_money'];

        if ($cooperate_info['house']['reward_type'] != 2 && $cooperate_info['house']['tbl'] == 'sell') {
          //获取出售信息基本配置资料
          $this->load->model('sell_house_model');
          $commission_ratio = $this->sell_house_model->get_commission_ratio_id($cooperate_info['house']['commission_ratio']);
          $new_cooperate_info['house']['commission_ratio_arr'] = $this->sell_house_model->get_commission_ratio($config['commission_ratio'][$commission_ratio]);
        }

        //合同甲方经济人信息
        $cooperate_info['brokerinfo_a'] = !empty($cooperate_info['broker_a']) ?
          unserialize($cooperate_info['broker_a']) : array();
        $broker_a_id = intval($cooperate_info['brokerinfo_a']['broker_id']);
        $new_cooperate_info['our_brokerinfo']['broker_id'] = $broker_a_id;
        $broker_a_now = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_a_id);
        //获取门店所属公司名
        $company_name = '';
        if (isset($broker_a_now['company_id']) && !empty($broker_a_now['company_id'])) {
          $company_where_cond = array(
            'id' => $broker_a_now['company_id'],
            'company_id' => 0
          );
          $company_data = $this->agency_model->get_one_by($company_where_cond);
          if (is_full_array($company_data)) {
            $company_name = $company_data['name'];
          }
        }

        $new_cooperate_info['our_brokerinfo']['broker_name'] = $broker_a_now['truename'];
        $new_cooperate_info['our_brokerinfo']['phone'] = $broker_a_now['phone'];
        $new_cooperate_info['our_brokerinfo']['agency_name'] = $broker_a_now['agency_name'];
        $new_cooperate_info['our_brokerinfo']['company_name'] = $company_name;
        /***
         * //甲方经纪人积分、信用值信息
         * $appraise_avg_info_a = $this->api_broker_sincere_model->get_appraise_and_avg($broker_a_id);
         *
         * $trust_level_a = $this->api_broker_sincere_model->get_level_id_by_trust($broker_a_now['trust']);
         * $new_cooperate_info['brokerinfo_a']['cop_suc_ratio'] = $broker_a_now['cop_suc_ratio'];
         * $new_cooperate_info['brokerinfo_a']['trust_level'] = $trust_level_a;
         * $new_cooperate_info['brokerinfo_a']['infomation'] = $appraise_avg_info_a['infomation']['score'];
         * $new_cooperate_info['brokerinfo_a']['attitude'] = $appraise_avg_info_a['attitude']['score'];
         * $new_cooperate_info['brokerinfo_a']['business'] = $appraise_avg_info_a['business']['score'];**/
        //合同乙方经纪人信息
        $new_cooperate_info['cooperate_brokerinfo'] = !empty($cooperate_info['broker_b']) ?
          unserialize($cooperate_info['broker_b']) : array();
        $new_cooperate_info['cooperate_brokerinfo']['broker_name'] = $new_cooperate_info['cooperate_brokerinfo']['truename'];
        //根据甲方经纪人，获得所属公司名称
        $company_name = '';
        $a_broker_id = $new_cooperate_info['cooperate_brokerinfo']['broker_id'];
        if (isset($a_broker_id) && !empty($a_broker_id)) {
          $a_broker_data = $this->broker_info_model->get_one_by(array('broker_id' => intval($a_broker_id)));
          if (is_full_array($a_broker_data)) {
            $company_where_cond = array(
              'id' => $a_broker_data['company_id'],
              'company_id' => 0
            );
            $company_data = $this->agency_model->get_one_by($company_where_cond);
            if (is_full_array($company_data)) {
              $company_name = $company_data['name'];
            }
          }
        }
        $new_cooperate_info['cooperate_brokerinfo']['company_name'] = $company_name;

        unset($new_cooperate_info['cooperate_brokerinfo']['truename']);
        //已方经纪人积分、信用值信息
        $broker_b_id = intval($new_cooperate_info['cooperate_brokerinfo']['broker_id']);
        $appraise_avg_info_b = $this->api_broker_sincere_model->get_appraise_and_avg($broker_b_id);
        $trust_appraise_count = $this->api_broker_sincere_model->get_trust_appraise_count($broker_b_id);
        $broker_b_now = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_b_id);
        $trust_level_b = $this->api_broker_sincere_model->get_level_id_by_trust($broker_b_now['trust']);
        //平均好评率
        $trust_appraise_avg = $this->api_broker_sincere_model->good_avg_rate($broker_b_id);
        //没有好评率时
        if ($trust_appraise_count['good_rate'] == '') {
          $trust_appraise_count['good_rate'] = '--';
          $good_rate_hot = 0;//低
        } else {
          $good_rate_hot = $trust_appraise_avg['good_rate_avg_high'] < 0 ? 0 : 1;
        }
        //合作成功率平均值
        $this->load->model('cooperate_suc_ratio_base_model');
        $avg_cop_suc_ratio = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
        $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_b_id);
        if ($cop_succ_ratio_info['cop_succ_ratio'] == '') {
          $cop_succ_ratio_info['cop_succ_ratio'] = '--';
          $cop_suc_ratio_hot = 0;//低
        } else {
          $cop_suc_ratio_hot = $cop_succ_ratio_info['cop_succ_ratio'] >= $avg_cop_suc_ratio ? 1 : 0;
        }
        $new_cooperate_info['cooperate_brokerinfo']['good_rate'] = strip_end_0($trust_appraise_count['good_rate']) . '%';
        $new_cooperate_info['cooperate_brokerinfo']['good_rate_hot'] = $good_rate_hot;
        $new_cooperate_info['cooperate_brokerinfo']['cop_suc_ratio'] = strip_end_0($cop_succ_ratio_info['cop_succ_ratio']) . '%';
        $new_cooperate_info['cooperate_brokerinfo']['cop_suc_ratio_hot'] = $cop_suc_ratio_hot;
        $new_cooperate_info['cooperate_brokerinfo']['trust_level'] = $trust_level_b;
        $new_cooperate_info['cooperate_brokerinfo']['infomation'] = $appraise_avg_info_b['infomation']['score'];
        $new_cooperate_info['cooperate_brokerinfo']['infomation_hot'] = $appraise_avg_info_b['infomation']['rate'] >= 0 ? 1 : 0;
        $new_cooperate_info['cooperate_brokerinfo']['attitude'] = $appraise_avg_info_b['attitude']['score'];
        $new_cooperate_info['cooperate_brokerinfo']['attitude_hot'] = $appraise_avg_info_b['attitude']['rate'] >= 0 ? 1 : 0;
        $new_cooperate_info['cooperate_brokerinfo']['business'] = $appraise_avg_info_b['business']['score'];
        $new_cooperate_info['cooperate_brokerinfo']['business_hot'] = $appraise_avg_info_b['business']['rate'] >= 0 ? 1 : 0;
        //合同取消原因信息
        $cooperate_info['cancel_reason'] = !empty($cooperate_info['cancel_reason']) ?
          unserialize($cooperate_info['cancel_reason']) : array();
        //合同拒绝原因信息
        $cooperate_info['refuse_reason'] = !empty($cooperate_info['refuse_reason']) ?
          unserialize($cooperate_info['refuse_reason']) : array();
        //计算佣金分配
        if ($new_cooperate_info['step'] < 2) {
          $rowid = $cooperate_info['rowid'];
          if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'sell') {
            $this->load->model('sell_house_share_ratio_model');
            $ratio = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
            //$ratio['master_a'] = '买方';
            //$ratio['master_b'] = '卖方';
          } else if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'rent') {
            $this->load->model('rent_house_share_ratio_model');
            $ratio = $this->rent_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
            //$ratio['master_a'] = '求租方';
            //$ratio['master_b'] = '出租方';
          }
          if (!empty($ratio)) {
            $new_cooperate_info['house']['seller_ratio'] = strip_end_0($ratio['seller_ratio']);
            $new_cooperate_info['house']['buyer_ratio'] = strip_end_0($ratio['buyer_ratio']);
            $new_cooperate_info['house']['a_ratio'] = strip_end_0($ratio['a_ratio']);
            $new_cooperate_info['house']['b_ratio'] = strip_end_0($ratio['b_ratio']);
          }
        } else if ($cooperate_info['step'] >= 2) {
          //合同佣金分配信息
          $cooperate_info['ratio'] = !empty($cooperate_info['ratio']) ?
            unserialize($cooperate_info['ratio']) : array();
          if (!empty($cooperate_info)) {
            $new_cooperate_info['house']['seller_ratio'] = strip_end_0($cooperate_info['ratio']['seller_ratio']);
            $new_cooperate_info['house']['buyer_ratio'] = strip_end_0($cooperate_info['ratio']['buyer_ratio']);
            $new_cooperate_info['house']['a_ratio'] = strip_end_0($cooperate_info['ratio']['a_ratio']);
            $new_cooperate_info['house']['b_ratio'] = strip_end_0($cooperate_info['ratio']['b_ratio']);
          }
        }
        //操作日志
        $log_arr = $this->cooperate_model->get_cooperation_log_by_cid($c_id);
        $log_num = count($log_arr);
        $temp_log = array();

        for ($i = 0; $i < $log_num; $i++) {
          $step = $log_arr[$i]['step'];
          $esta = $log_arr[$i]['esta'];
          $temp_log[$step][$esta] = $log_arr[$i];
        }
        $log_arr = $temp_log;
        $cooperate_info['log_record'] = $log_arr;
        $cooperate_info['config'] = $this->cooperate_model->get_base_conf();
        unset($new_cooperate_info['cooperate_brokerinfo']['credit']);
        unset($new_cooperate_info['cooperate_brokerinfo']['trust']);
        unset($new_cooperate_info['cooperate_brokerinfo']['agency_id']);

        $new_cooperate_info['status'] = array(
          'order_sn' => $cooperate_info['order_sn'],
          'step' => $this->cooperate_model->get_cooperate_step($cooperate_info, 'accept')
        );

        //判断是否评论
        $appraise = 0;
        if ((in_array($cooperate_info['esta'], array(7, 8, 9))
            || (($cooperate_info['esta'] == 6 || $cooperate_info['esta'] == 10
                || $cooperate_info['esta'] == 11) && $cooperate_info['step'] >= 3))
          && ($cooperate_info['brokerid_a'] == $this->user_arr['broker_id'])
        ) {
          $appraise = !$cooperate_info['appraise_a'] ? 1 : 2;
        }
        $new_cooperate_info['appraise'] = $appraise;
        //操作加密字符串
        $secret_param = array('cid' => $c_id, 'step' => $cooperate_info['step'], 'esta' => $new_cooperate_info['esta']);
        $new_cooperate_info['secret_key'] = $this->verify->user_enrypt($secret_param);
        $comfirm_deal = 0;
        //确认成交那一步
        if ($cooperate_info['esta'] == 4) {
          //如果是房源
          if ($cooperate_info['rowid'] && $cooperate_info['customer_id'] == 0) {
            $comfirm_deal = $this->user_arr['broker_id']
            == $cooperate_info['brokerid_a'] ? 1 : 0;
          } else //客源匹配
          {
            $comfirm_deal = $this->user_arr['broker_id']
            == $cooperate_info['brokerid_b'] ? 1 : 0;
          }
        }
        $new_cooperate_info['comfirm_deal'] = $comfirm_deal;
        $new_cooperate_info['creattime'] = date('Y-m-d', $cooperate_info['creattime']);;
        $this->result(1, '查询成功', $new_cooperate_info);
      } else {
        $this->result(0, '查询失败');
      }
    } else {
      $this->result(0, '查询失败');
    }
  }

  //我发送的合同申请
  public function my_send_order()
  {
    $c_id = $this->input->get('c_id', TRUE);
    $new_cooperate_info = array();
    $log_arr = array();
    if ($c_id > 0) {
      $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);
      if (is_array($cooperate_info) && !empty($cooperate_info)) {
        //经纪人基础信息和信用积分模块
        $this->load->model('api_broker_base_model');
        $this->load->model('api_broker_sincere_model');
        //加载出售基本配置MODEL
        $this->load->model('house_config_model');
        //获取出售信息基本配置资料
        $config = $this->house_config_model->get_config();
        //合同房源信息
        $cooperate_info['house'] = !empty($cooperate_info['house']) ?
          unserialize($cooperate_info['house']) : array();
        $unit = $cooperate_info['tbl'] == 'sell' ? '万' : '元/月';
        //出售和出租房源列表获取最新信息-
        $this->load->model('sell_house_model');
        $this->load->model('rent_house_model');
        if ($cooperate_info['house']['tbl'] == 'sell') {
          $this->sell_house_model->set_id($cooperate_info['house']['rowid']);
          $new_house_info = $this->sell_house_model->get_info_by_id();
        } else {
          $this->rent_house_model->set_id($cooperate_info['house']['rowid']);
          $new_house_info = $this->rent_house_model->get_info_by_id();
        }
        if ($new_house_info['sell_type'] == 1 || $new_house_info['sell_type'] == 4) {
          $floor_info_rate = $new_house_info['floor'] / $new_house_info['totalfloor'];
          if ($floor_info_rate < 0.4) {
            $floor_info = '低';
          } else if ($floor_info_rate >= 0.4 && $floor_info_rate <= 0.7) {
            $floor_info = '中';
          } else {
            $floor_info = '高';
          }
        } else {
          $floor_info = '低';
        }
        $new_cooperate_info['house'] = array(
          'rowid' => $cooperate_info['house']['rowid'],
          'tbl' => $cooperate_info['tbl'] == 'sell' ? 0 : 1,
          'block_name' => $cooperate_info['house']['blockname'],
          'room_hall' => $cooperate_info['house']['room'] . '室'
            . $cooperate_info['house']['hall'] . '厅',
          'price' => strip_end_0($cooperate_info['house']['price']) . $unit,
          'area' => strip_end_0($cooperate_info['house']['buildarea']),
          'total_price' => strip_end_0($cooperate_info['price']) . $unit,
          'fitment' => $config['fitment'][$cooperate_info['house']['fitment']],
          'video_id' => empty($new_house_info['video_id']) ? '' : $new_house_info['video_id'],
          'video_pic' => empty($new_house_info['video_pic']) ? '' : $new_house_info['video_pic'],
          'districtname' => $cooperate_info['house']['districtname'],
          'streetname' => $cooperate_info['house']['streetname'],
          'photo' => $cooperate_info['house']['photo'],
          'floor_info' => $floor_info . '楼层/' . $new_house_info['totalfloor'],
        );

        //获得房源详情中的合作悬赏
        if (isset($cooperate_info['house']['cooperate_reward']) && !empty($cooperate_info['house']['cooperate_reward'])) {
          $new_cooperate_info['house']['cooperate_reward'] = $cooperate_info['house']['cooperate_reward'];
        } else {
          $new_cooperate_info['house']['cooperate_reward'] = 0;
        }

        $new_cooperate_info['step'] = $cooperate_info['step'];
        $new_cooperate_info['esta'] = $cooperate_info['esta'];
        $new_cooperate_info['reward_type'] = $cooperate_info['reward_type'];
        $new_cooperate_info['reward_money'] = $cooperate_info['reward_money'];

        if ($cooperate_info['house']['reward_type'] != 2 && $cooperate_info['house']['tbl'] == 'sell') {
          //获取出售信息基本配置资料
          $this->load->model('sell_house_model');
          $commission_ratio = $this->sell_house_model->get_commission_ratio_id($cooperate_info['house']['commission_ratio']);
          $new_cooperate_info['house']['commission_ratio_arr'] = $this->sell_house_model->get_commission_ratio($config['commission_ratio'][$commission_ratio]);
        }

        //合同甲方经济人信息
        $new_cooperate_info['cooperate_brokerinfo'] = !empty($cooperate_info['broker_a']) ?
          unserialize($cooperate_info['broker_a']) : array();
        $new_cooperate_info['cooperate_brokerinfo']['broker_name'] = $new_cooperate_info['cooperate_brokerinfo']['truename'];
        //根据甲方经纪人，获得所属公司名称
        $company_name = '';
        $a_broker_id = $new_cooperate_info['cooperate_brokerinfo']['broker_id'];
        if (isset($a_broker_id) && !empty($a_broker_id)) {
          $a_broker_data = $this->broker_info_model->get_one_by(array('broker_id' => intval($a_broker_id)));
          if (is_full_array($a_broker_data)) {
            $company_where_cond = array(
              'id' => $a_broker_data['company_id'],
              'company_id' => 0
            );
            $company_data = $this->agency_model->get_one_by($company_where_cond);
            if (is_full_array($company_data)) {
              $company_name = $company_data['name'];
            }
          }
        }
        $new_cooperate_info['cooperate_brokerinfo']['company_name'] = $company_name;

        unset($new_cooperate_info['cooperate_brokerinfo']['truename']);
        $broker_a_id = intval($new_cooperate_info['cooperate_brokerinfo']['broker_id']);

        //甲方经纪人积分、信用值信息
        $appraise_avg_info_a = $this->api_broker_sincere_model->get_appraise_and_avg($broker_a_id);
        $broker_a_now = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_a_id);
        $trust_level_a = $this->api_broker_sincere_model->get_level_id_by_trust($broker_a_now['trust']);
        $trust_appraise_count = $this->api_broker_sincere_model->get_trust_appraise_count($broker_a_id);
        //平均好评率
        $trust_appraise_avg = $this->api_broker_sincere_model->good_avg_rate($broker_a_id);
        //没有好评率时
        if ($trust_appraise_count['good_rate'] == '') {
          $trust_appraise_count['good_rate'] = '--';
          $good_rate_hot = 0;//低
        } else {
          $good_rate_hot = $trust_appraise_avg['good_rate_avg_high'] < 0 ? 0 : 1;
        }
        //合作成功率平均值
        $this->load->model('cooperate_suc_ratio_base_model');
        $avg_cop_suc_ratio = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
        $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_a_id);
        if ($cop_succ_ratio_info['cop_succ_ratio'] == '') {
          $cop_succ_ratio_info['cop_succ_ratio'] = '--';
          $cop_suc_ratio_hot = 0;//低
        } else {
          $cop_suc_ratio_hot = $cop_succ_ratio_info['cop_succ_ratio'] >= $avg_cop_suc_ratio ? 1 : 0;
        }
        $new_cooperate_info['cooperate_brokerinfo']['good_rate'] = strip_end_0($trust_appraise_count['good_rate']) . '%';
        $new_cooperate_info['cooperate_brokerinfo']['good_rate_hot'] = $good_rate_hot;
        $new_cooperate_info['cooperate_brokerinfo']['cop_suc_ratio'] = strip_end_0($cop_succ_ratio_info['cop_succ_ratio']) . '%';
        $new_cooperate_info['cooperate_brokerinfo']['cop_suc_ratio_hot'] = $cop_suc_ratio_hot;
        $new_cooperate_info['cooperate_brokerinfo']['trust_level'] = $trust_level_a;
        $new_cooperate_info['cooperate_brokerinfo']['infomation'] = $appraise_avg_info_a['infomation']['score'];
        $new_cooperate_info['cooperate_brokerinfo']['infomation_hot'] = $appraise_avg_info_a['infomation']['rate'] >= 0 ? 1 : 0;
        $new_cooperate_info['cooperate_brokerinfo']['attitude'] = $appraise_avg_info_a['attitude']['score'];
        $new_cooperate_info['cooperate_brokerinfo']['attitude_hot'] = $appraise_avg_info_a['attitude']['rate'] >= 0 ? 1 : 0;
        $new_cooperate_info['cooperate_brokerinfo']['business'] = $appraise_avg_info_a['business']['score'];
        $new_cooperate_info['cooperate_brokerinfo']['business_hot'] = $appraise_avg_info_a['business']['rate'] >= 0 ? 1 : 0;
        //合同乙方经纪人信息
        $cooperate_info['brokerinfo_b'] = !empty($cooperate_info['broker_b']) ?
          unserialize($cooperate_info['broker_b']) : array();
        $broker_b_id = intval($cooperate_info['brokerinfo_b']['broker_id']);
        $new_cooperate_info['our_brokerinfo']['broker_id'] = $broker_b_id;
        $broker_b_now = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_b_id);
        //获取门店所属公司名
        $company_name = '';
        if (isset($broker_a_now['company_id']) && !empty($broker_a_now['company_id'])) {
          $company_where_cond = array(
            'id' => $broker_a_now['company_id'],
            'company_id' => 0
          );
          $company_data = $this->agency_model->get_one_by($company_where_cond);
          if (is_full_array($company_data)) {
            $company_name = $company_data['name'];
          }
        }

        $new_cooperate_info['our_brokerinfo']['broker_name'] = $broker_b_now['truename'];
        $new_cooperate_info['our_brokerinfo']['phone'] = $broker_b_now['phone'];
        $new_cooperate_info['our_brokerinfo']['agency_name'] = $broker_b_now['agency_name'];
        $new_cooperate_info['our_brokerinfo']['company_name'] = $company_name;

        /**
         * //已方经纪人积分、信用值信息
         * $cooperate_info['appraise_avg_info_b'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_b_id);
         *
         * $cooperate_info['trust_level_b'] = $this->api_broker_sincere_model-> ($cooperate_info['broker_b_now']['trust']);
         **/
        //合同取消原因信息
        $cooperate_info['cancel_reason'] = !empty($cooperate_info['cancel_reason']) ?
          unserialize($cooperate_info['cancel_reason']) : array();
        //合同拒绝原因信息
        $cooperate_info['refuse_reason'] = !empty($cooperate_info['refuse_reason']) ?
          unserialize($cooperate_info['refuse_reason']) : array();
        //计算佣金分配
        if ($new_cooperate_info['step'] < 2) {
          $rowid = $cooperate_info['rowid'];
          if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'sell') {
            $this->load->model('sell_house_share_ratio_model');
            $ratio = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
            //$ratio['master_a'] = '买方';
            //$ratio['master_b'] = '卖方';
          } else if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'rent') {
            $this->load->model('rent_house_share_ratio_model');
            $ratio = $this->rent_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
            //$ratio['master_a'] = '求租方';
            //$ratio['master_b'] = '出租方';
          }
          if (!empty($ratio)) {
            $new_cooperate_info['house']['seller_ratio'] = strip_end_0($ratio['seller_ratio']);
            $new_cooperate_info['house']['buyer_ratio'] = strip_end_0($ratio['buyer_ratio']);
            $new_cooperate_info['house']['a_ratio'] = strip_end_0($ratio['a_ratio']);
            $new_cooperate_info['house']['b_ratio'] = strip_end_0($ratio['b_ratio']);
          }
        } else if ($cooperate_info['step'] >= 2) {
          //合同佣金分配信息
          $cooperate_info['ratio'] = !empty($cooperate_info['ratio']) ?
            unserialize($cooperate_info['ratio']) : array();
          if (!empty($cooperate_info)) {
            $new_cooperate_info['house']['seller_ratio'] = strip_end_0($cooperate_info['ratio']['seller_ratio']);
            $new_cooperate_info['house']['buyer_ratio'] = strip_end_0($cooperate_info['ratio']['buyer_ratio']);
            $new_cooperate_info['house']['a_ratio'] = strip_end_0($cooperate_info['ratio']['a_ratio']);
            $new_cooperate_info['house']['b_ratio'] = strip_end_0($cooperate_info['ratio']['b_ratio']);
          }
        }
        //操作日志
        $log_arr = $this->cooperate_model->get_cooperation_log_by_cid($c_id);
        $log_num = count($log_arr);
        $temp_log = array();
        for ($i = 0; $i < $log_num; $i++) {
          $step = $log_arr[$i]['step'];
          $esta = $log_arr[$i]['esta'];
          $temp_log[$step][$esta] = $log_arr[$i];
        }

        $log_arr = $temp_log;

        $cooperate_info['log_record'] = $log_arr;

        //配置文件
        $cooperate_info['config'] = $this->cooperate_model->get_base_conf();
        unset($new_cooperate_info['cooperate_brokerinfo']['credit']);
        unset($new_cooperate_info['cooperate_brokerinfo']['trust']);
        unset($new_cooperate_info['cooperate_brokerinfo']['agency_id']);
        $new_cooperate_info['status'] = array(
          'order_sn' => $cooperate_info['order_sn'],
          'step' => $this->cooperate_model->get_cooperate_step($cooperate_info, 'send')
        );
        //判断是否评论
        $appraise = 0;
        if ((in_array($cooperate_info['esta'], array(7, 8, 9))
            || (($cooperate_info['esta'] == 6 || $cooperate_info['esta'] == 10
                || $cooperate_info['esta'] == 11) && $cooperate_info['step'] >= 3))
          && ($cooperate_info['brokerid_b'] == $this->user_arr['broker_id'])
        ) {
          $appraise = !$cooperate_info['appraise_b'] ? 1 : 2;
        }
        $new_cooperate_info['appraise'] = $appraise;
        //操作加密字符串
        $secret_param = array('cid' => $c_id, 'step' => $cooperate_info['step'], 'esta' => $new_cooperate_info['esta']);
        $new_cooperate_info['secret_key'] = $this->verify->user_enrypt($secret_param);
        $comfirm_deal = 0;
        //确认成交那一步
        if ($cooperate_info['esta'] == 4) {
          //如果是房源
          if ($cooperate_info['rowid'] && $cooperate_info['customer_id'] == 0) {
            $comfirm_deal = $this->user_arr['broker_id']
            == $cooperate_info['brokerid_a'] ? 1 : 0;
          } else //客源匹配
          {
            $comfirm_deal = $this->user_arr['broker_id']
            == $cooperate_info['brokerid_b'] ? 1 : 0;
          }
        }
        $new_cooperate_info['comfirm_deal'] = $comfirm_deal;
        $new_cooperate_info['creattime'] = date('Y-m-d', $cooperate_info['creattime']);
        $this->result(1, '查询成功', $new_cooperate_info);
      } else {
        $this->result(0, '查询失败');
      }
    } else {
      $this->result(0, '查询失败');
    }
  }


  /*
     * 甲方经纪人接受合作
     * @return json
     */
  public function accept_cooperation()
  {
    //合同ID
    $c_id = $this->input->get('c_id', TRUE);
    $cop_step = $this->input->get('step', TRUE); //乙方经纪人编号
    $old_esta = $this->input->get('old_esta', TRUE); //合作状态
    $cop_secret_key = $this->input->get('secret_key', TRUE); //加密字符串
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);

    $brokerid_a = $this->user_arr['broker_id']; //操作经纪人编号
    $broker_name_a = $this->user_arr['truename']; //操作经纪人姓名

    //根据表单参数重新拼接加密字符串
    $secret_param = array('cid' => $c_id, 'step' => $cop_step, 'esta' => $old_esta);
    $secret_key = $this->verify->user_enrypt($secret_param);
    $result = array('is_ok' => 0, 'msg' => '参数异常，无法接受合作！');

    //更改合同状态、步骤、更新时间，更改下步操作人
    if ($c_id > 0 && $secret_key == $cop_secret_key) {
      /*判断当前合作状态是否已经变化*/
      $cooperate_info = array();

      //获取合作当前状态的基本信息
      $cooperate_info = $this->cooperate_model->get_cooperate_baseinfo_by_cid($c_id);

      if (!empty($cooperate_info) && $cooperate_info['esta'] == $old_esta) {
        //更新合作状态
        $up_num = $this->cooperate_model->accept_cooperation($c_id, $brokerid_a);

        if ($up_num > 0) {
          //操作日志
          $add_log_text = '';
          //获得经纪人所在的公司门店名
          $this->load->model('api_broker_model');
          $brokerinfo_a = $this->api_broker_model->get_baseinfo_by_broker_id(intval($cooperate_info['brokerid_a']));
          //接收方所属门店名
          $agency_name_a = $brokerinfo_a['agency_name'];
          $this->load->model('agency_model');
          $agency_a = $this->agency_model->get_by_id(intval($brokerinfo_a['company_id']));
          //接收方所属公司名
          $company_name_a = $agency_a['name'];
          //房源申请
          if ('1' == $cooperate_info['apply_type']) {
            if ('sell' == $cooperate_info['tbl']) {
              $add_log_text .= '出售房源：' . 'CS' . $cooperate_info['rowid'] . ' ' . $cooperate_info['block_name'] . ' ' . $cooperate_info['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            } else if ('rent' == $cooperate_info['tbl']) {
              $add_log_text .= '出租房源：' . 'CZ' . $cooperate_info['rowid'] . ' ' . $cooperate_info['block_name'] . ' ' . $cooperate_info['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            }
            //客源申请
          } else if ('2' == $cooperate_info['apply_type']) {
            if ('sell' == $cooperate_info['tbl']) {
              $this->load->model('buy_customer_model');
              $this->buy_customer_model->set_id(intval($cooperate_info['customer_id']));
              $customer_info = $this->buy_customer_model->get_info_by_id();
              $add_log_text .= '求购客源：' . 'QG' . $cooperate_info['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '万元 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            } else if ('rent' == $cooperate_info['tbl']) {
              $this->load->model('rent_customer_model');
              $this->rent_customer_model->set_id(intval($cooperate_info['customer_id']));
              $customer_info = $this->rent_customer_model->get_info_by_id();
              $add_log_text .= '求租客源：' . 'QZ' . $cooperate_info['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '元/月 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            }
          }

          $add_log_param = array();
          $add_log_param['company_id'] = $this->user_arr['company_id'];
          $add_log_param['agency_id'] = $this->user_arr['agency_id'];
          $add_log_param['broker_id'] = $this->user_arr['broker_id'];
          $add_log_param['broker_name'] = $this->user_arr['truename'];
          $add_log_param['type'] = 19;
          $add_log_param['text'] = $add_log_text;
          if ($devicetype == 'android') {
            $add_log_param['from_system'] = 2;
          } else {
            $add_log_param['from_system'] = 3;
          }
          $add_log_param['device_id'] = $deviceid;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();
          $this->operate_log_model->add_operate_log($add_log_param);

          //发送站内信通知已方经纪人
          $order_sn = $cooperate_info['order_sn'];
          $broker_id = $cooperate_info['brokerid_b'];
          $broker_name = $cooperate_info['broker_name_b'];
          //$fromer = $broker_name_a;
          $url_msg = '/cooperate/send_order_list/?cid=' . $c_id;
          $params['block_name'] = $cooperate_info['block_name'];

          //添加记录表中合作记录[统计合作成功率用]
          $this->load->model('cooperate_suc_ratio_base_model');
          $this->cooperate_suc_ratio_base_model->add_cooperate_record($c_id, $brokerid_a, $broker_id);

          //发送站内信
          $result = array('is_ok' => 1, 'msg' => '合作生效！');
          $this->load->model('message_base_model');
          $url_a = '/cooperate/accept_order_list/?cid=' . $c_id;
          $url_b = '/cooperate/send_order_list/?cid=' . $c_id;
          $params['name'] = $broker_name;
          $this->message_base_model->add_message('1-2-1', $brokerid_a, $broker_name_a, $url_a, $params);
          $params['name'] = $broker_name_a;
          $msg_id = $this->message_base_model->add_message('1-2', $broker_id, $broker_name, $url_b, $params);
          //增加积分
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $brokerid_a));
          $credit_result = $this->api_broker_credit_model->accept_cooperate($cooperate_info);
          //判断积分是否增加成功
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $result['msg'] .= '+' . $credit_result['score'] . '积分';
          }
          //增加等级分值
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $brokerid_a));
          $level_result = $this->api_broker_level_model->accept_cooperate($cooperate_info);
          //判断成长值是否增加成功
          if (is_full_array($level_result) && $level_result['status'] == 1) {
            $result['msg'] .= '+' . $level_result['score'] . '成长值';
          }
          //发送推送消息
          $this->push_func_model->send(1, 1, 2, $brokerid_a, $broker_id, array('msg_id' => $msg_id), array('block_name' => $cooperate_info['block_name']));
        }
      } else {
        $cop_config = $this->cooperate_model->get_base_conf();
        $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
        $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
        $msg .= ",重新进入合作详情页后再操作。";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }
    $this->result($result['is_ok'], $result['msg']);
  }


  /*
     * 拒绝合作
     */
  public function refuse_to_cooperation()
  {
    $broker_id = $this->user_arr['broker_id']; //经纪人编号
    $c_id = $this->input->get('c_id', TRUE); //合同ID
    $step = $this->input->get('step', TRUE); //合同步骤
    $old_esta = $this->input->get('old_esta', TRUE);
    $cop_secret_key = $this->input->get('secret_key', TRUE); //加密字符串
    $refuse_type = $this->input->get('refuse_type', TRUE); //拒绝类型
    $refuse_reason = $this->input->get('refuse_reason', TRUE); //拒绝原因
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);

    //根据表单参数重新拼接加密字符串
    $secret_param = array('cid' => $c_id, 'step' => $step, 'esta' => $old_esta);
    $secret_key = $this->verify->user_enrypt($secret_param);
    $result = array('is_ok' => 0, 'msg' => '参数异常，无法接受合作！');

    if ($c_id > 0 && $secret_key == $cop_secret_key) {
      //获取合作当前状态的基本信息
      $cooperate_info = array();
      $this->cooperate_model->set_select_fields(array('customer_id', 'rowid', 'broker_name_a', 'rowid', 'apply_type', 'tbl', 'brokerid_a', 'order_sn', 'brokerid_b', 'broker_name_b', 'step', 'esta', 'who_do', 'block_name'));
      $cooperate_info = $this->cooperate_model->get_cooperate_baseinfo_by_cid($c_id);
      $tbl = $cooperate_info['tbl'];
      //状态变化时提醒用户无法提交
      if (!empty($cooperate_info) && $old_esta == $cooperate_info['esta']) {
        $refuse_arr = array('step' => $step, 'broker_id' => $broker_id, 'type' => $refuse_type, 'reason' => $refuse_reason);
        //更改合同状态、步骤、更新时间，更改下步操作人
        $up_num = $this->cooperate_model->refuse_to_cooperation($c_id, $broker_id, $step, $refuse_arr);

        //更新合同步骤日志
        if ($up_num > 0) {
          //操作日志
          $add_log_text = '';
          //获得经纪人所在的公司门店名
          $this->load->model('api_broker_model');
          $brokerinfo_a = $this->api_broker_model->get_baseinfo_by_broker_id(intval($cooperate_info['brokerid_a']));
          //接收方所属门店名
          $agency_name_a = $brokerinfo_a['agency_name'];
          $this->load->model('agency_model');
          $agency_a = $this->agency_model->get_by_id(intval($brokerinfo_a['company_id']));
          //接收方所属公司名
          $company_name_a = $agency_a['name'];
          //房源申请
          if ('1' == $cooperate_info['apply_type']) {
            if ('sell' == $cooperate_info['tbl']) {
              $add_log_text .= '出售房源：' . 'CS' . $cooperate_info['rowid'] . ' ' . $cooperate_info['block_name'] . ' ' . $cooperate_info['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            } else if ('rent' == $cooperate_info['tbl']) {
              $add_log_text .= '出租房源：' . 'CZ' . $cooperate_info['rowid'] . ' ' . $cooperate_info['block_name'] . ' ' . $cooperate_info['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            }
            //客源申请
          } else if ('2' == $cooperate_info['apply_type']) {
            if ('sell' == $cooperate_info['tbl']) {
              $this->load->model('buy_customer_model');
              $this->buy_customer_model->set_id(intval($cooperate_info['customer_id']));
              $customer_info = $this->buy_customer_model->get_info_by_id();
              $add_log_text .= '求购客源：' . 'QG' . $cooperate_info['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '万元 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            } else if ('rent' == $cooperate_info['tbl']) {
              $this->load->model('rent_customer_model');
              $this->rent_customer_model->set_id(intval($cooperate_info['customer_id']));
              $customer_info = $this->rent_customer_model->get_info_by_id();
              $add_log_text .= '求租客源：' . 'QZ' . $cooperate_info['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '元/月 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            }
          }

          $add_log_param = array();
          $add_log_param['company_id'] = $this->user_arr['company_id'];
          $add_log_param['agency_id'] = $this->user_arr['agency_id'];
          $add_log_param['broker_id'] = $this->user_arr['broker_id'];
          $add_log_param['broker_name'] = $this->user_arr['truename'];
          $add_log_param['type'] = 20;
          $add_log_param['text'] = $add_log_text;
          if ($devicetype == 'android') {
            $add_log_param['from_system'] = 2;
          } else {
            $add_log_param['from_system'] = 3;
          }
          $add_log_param['device_id'] = $deviceid;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();
          $this->operate_log_model->add_operate_log($add_log_param);

          if ($step == 1) {
            //发送给甲方站内信
            $order_sn = strip_tags($cooperate_info['order_sn']);
            $broker_id = intval($cooperate_info['brokerid_b']);
            $broker_name = strip_tags($cooperate_info['broker_name_b']);
            $fromer = strip_tags($this->user_arr['truename']);
            $url_msg = '/cooperate/send_order_list/?cid=' . $c_id;
            //$params['block_name'] = $cooperate_info['block_name'];
            $params['name'] = $fromer;
            $params['type'] = "f";
            $params['id'] = format_info_id($cooperate_info['rowid'], $tbl);

            $this->load->model('message_base_model');
            $msg_id = $this->message_base_model->add_message("1-6-2", $broker_id, $broker_name, $url_msg, $params);

            $result = array('is_ok' => 1, 'msg' => '很遗憾，本次合作未达成！'
              . '您可以再次与乙方经纪人沟通确认合作细节。达成的合作将由平台担保，保证您的佣金收益，祝您开单顺利！');
            //发送推送消息
            $this->push_func_model->send(1, 1, 3, $this->user_arr['broker_id'], $broker_id, array('msg_id' => $msg_id), array('block_name' => $cooperate_info['block_name']));
          } else if ($step == 2) {
            //发送站内信
            $order_sn = strip_tags($cooperate_info['order_sn']);
            $broker_id = intval($cooperate_info['brokerid_a']);
            $broker_name = strip_tags($cooperate_info['broker_name_a']);
            $fromer = strip_tags($this->user_arr['truename']);
            $url_msg = '/cooperate/accept_order_list/?cid=' . $c_id;
            //$n_params['block_name'] = $cooperate_info['block_name'];
            $params['name'] = $fromer;
            $params['type'] = "f";
            $params['id'] = format_info_id($cooperate_info['rowid'], $tbl);

            $this->load->model('message_base_model');
            $msg_id = $this->message_base_model->add_message("1-6-2", $broker_id, $broker_name, $url_msg, $params);

            $result = array('is_ok' => 1, 'msg' => '很遗憾，本次合作未达成！'
              . '您可以再次与甲方经纪人沟通确认合作细节。达成的合作将由平台担保，保证您的佣金收益，祝您开单顺利！');
            //发送推送消息
            $this->push_func_model->send(1, 1, 5, $this->user_arr['broker_id'], $broker_id, array('msg_id' => $msg_id), array('block_name' => $cooperate_info['block_name']));
          }
        }
      } else {
        $cop_config = $this->cooperate_model->get_base_conf();
        $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
        $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
        $msg .= ",重新进入合作详情页后再操作。";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }
    $this->result($result['is_ok'], $result['msg']);
  }


  /*
     * 取消合作
     */
  public function cancle_cooperation()
  {
    $c_id = $this->input->get('c_id', TRUE); //合同ID
    $step = $this->input->get('step', TRUE); //取消合同原因类别
    $old_esta = $this->input->get('old_esta', TRUE); //合作状态
    $cancle_type = $this->input->get('cancle_type', TRUE); //取消合同原因类别
    $cancle_reason = $this->input->get('cancle_reason', TRUE); //取消合同原因
    $cop_secret_key = $this->input->get('secret_key', TRUE); //加密字符串
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);

    //经纪人信息
    $broker_id = $this->user_arr['broker_id']; //经纪人编号
    $broker_name = $this->user_arr['truename']; //经纪人姓名

    //根据表单参数重新拼接加密字符串
    $secret_param = array('cid' => $c_id, 'step' => $step, 'esta' => $old_esta);
    $secret_key = $this->verify->user_enrypt($secret_param);
    $result = array('is_ok' => 0, 'msg' => '参数异常，取消合作失败！');

    if ($c_id > 0 && $secret_key == $cop_secret_key) {
      //获取合作当前状态的基本信息
      $cooperate_info = array();
      $cooperate_info = $this->cooperate_model->get_cooperate_baseinfo_by_cid($c_id);

      if (!empty($cooperate_info) && $old_esta == $cooperate_info['esta']) {
        //更改合同状态、步骤、更新时间，更改下步操作人
        $cancle_arr = array('step' => $step, 'broker_id' => $broker_id, 'type' => $cancle_type, 'reason' => $cancle_reason);
        $up_num = $this->cooperate_model->cancle_cooperation($c_id, $broker_id, $cancle_arr);

        //更新合同步骤日志
        if ($up_num > 0) {
          //操作日志
          $add_log_text = '';
          //获得经纪人所在的公司门店名
          $this->load->model('api_broker_model');
          $brokerinfo_a = $this->api_broker_model->get_baseinfo_by_broker_id(intval($cooperate_info['brokerid_a']));
          //接收方所属门店名
          $agency_name_a = $brokerinfo_a['agency_name'];
          $this->load->model('agency_model');
          $agency_a = $this->agency_model->get_by_id(intval($brokerinfo_a['company_id']));
          //接收方所属公司名
          $company_name_a = $agency_a['name'];
          //房源申请
          if ('1' == $cooperate_info['apply_type']) {
            if ('sell' == $cooperate_info['tbl']) {
              $add_log_text .= '出售房源：' . 'CS' . $cooperate_info['rowid'] . ' ' . $cooperate_info['block_name'] . ' ' . $cooperate_info['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            } else if ('rent' == $cooperate_info['tbl']) {
              $add_log_text .= '出租房源：' . 'CZ' . $cooperate_info['rowid'] . ' ' . $cooperate_info['block_name'] . ' ' . $cooperate_info['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            }
            //客源申请
          } else if ('2' == $cooperate_info['apply_type']) {
            if ('sell' == $cooperate_info['tbl']) {
              $this->load->model('buy_customer_model');
              $this->buy_customer_model->set_id(intval($cooperate_info['customer_id']));
              $customer_info = $this->buy_customer_model->get_info_by_id();
              $add_log_text .= '求购客源：' . 'QG' . $cooperate_info['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '万元 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            } else if ('rent' == $cooperate_info['tbl']) {
              $this->load->model('rent_customer_model');
              $this->rent_customer_model->set_id(intval($cooperate_info['customer_id']));
              $customer_info = $this->rent_customer_model->get_info_by_id();
              $add_log_text .= '求租客源：' . 'QZ' . $cooperate_info['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '元/月 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            }
          }

          $add_log_param = array();
          $add_log_param['company_id'] = $this->user_arr['company_id'];
          $add_log_param['agency_id'] = $this->user_arr['agency_id'];
          $add_log_param['broker_id'] = $this->user_arr['broker_id'];
          $add_log_param['broker_name'] = $this->user_arr['truename'];
          $add_log_param['type'] = 23;
          $add_log_param['text'] = $add_log_text;
          if ($devicetype == 'android') {
            $add_log_param['from_system'] = 2;
          } else {
            $add_log_param['from_system'] = 3;
          }
          $add_log_param['device_id'] = $deviceid;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();
          $this->operate_log_model->add_operate_log($add_log_param);

          //站内信类
          $this->load->model('message_base_model');
          $result = array('is_ok' => 1, 'msg' => '合作已取消！');

          $broker_id_a = intval($cooperate_info['brokerid_a']);
          $broker_name_a = strip_tags($cooperate_info['broker_name_a']);
          $broker_id_b = intval($cooperate_info['brokerid_b']);
          $broker_name_b = strip_tags($cooperate_info['broker_name_b']);
          $order_sn = $cooperate_info['order_sn'];
          $n_params['block_name'] = $cooperate_info['block_name'];

          if ($cancle_type == 4) {
            $n_params['reason'] = strip_tags($cancle_reason);
          } else {
            //配置文件
            $cop_config = $this->cooperate_model->get_base_conf();
            $n_params['reason'] = !empty($cop_config['cancel_reason'][$cancle_type]) ?
              $cop_config['cancel_reason'][$cancle_type] : "";
          }

          //接受合作后的取消会更新合作记录数据状态
          if ($step >= 2) {
            //更新合作记录数据
            $esta = 6;
            //更新合作记录数据状态[统计合作成功率用]
            $this->load->model('cooperate_suc_ratio_base_model');
            $ret_up = $this->cooperate_suc_ratio_base_model->update_cooperate_record($c_id, $broker_id_a, $broker_id_b, $esta);

            if ($ret_up) {
              //更新经纪人合作成功数据
              $this->cooperate_suc_ratio_base_model->update_broker_succ_raito($broker_id_a);
              $this->cooperate_suc_ratio_base_model->update_broker_succ_raito($broker_id_b);
            }

            //第三步骤取消合作扣除相应的信用值
            if ($step == 3) {
              $this->load->model('api_broker_sincere_model');
              $score = $this->api_broker_sincere_model->update_trust($broker_id, 'cancel_cooperate_signature');
              $house_info = $this->cooperate_model->get_house_att_by_cid($c_id, 0);
              $this->api_broker_sincere_model->punish(0, $broker_id, 'cancel_cooperate', $score, $order_sn, $house_info[$c_id]);
              //发送站内信通知操作人扣除信用值
              $msg_type_self = '1-14-4';
              $broker_id_self = $broker_id;
              $broker_name_self = $broker_name;
              $fromer_self = '';
              if ($cooperate_info['apply_type'] == 1) {
                $params['type'] = "f";
                $params['id'] = $cooperate_info['rowid'];
              } elseif ($cooperate_info['apply_type'] == 2) {
                $params['id'] = $cooperate_info['customer_id'];
                $tbl .= '_customer';
              }
              $params['pf'] = 1;
              $params['id'] = format_info_id($params['id'], $tbl);
              $url_self = '/my_growing_punish/index/';
              $this->message_base_model->add_message($msg_type_self, $broker_id_self, $broker_name_self, $url_self, $params);
            }
          }

          //发送站内信
          $broker_id_msg = $broker_id == $broker_id_a ? $broker_id_b : $broker_id_a;
          $url_msg = $broker_id == $broker_id_a ?
            '/cooperate/send_order_list/?cid=' . $c_id : '/cooperate/accept_order_list/?cid=' . $c_id;
          $broker_name_msg = $broker_id == $broker_id_a ? $broker_name_b : $broker_name_a;
          $fromer = $broker_name;
          $msg_type = '1-7-1';
          $params['name'] = $fromer;
          if ($cooperate_info['apply_type'] == 1) {
            $params['type'] = "f";
            $params['id'] = $cooperate_info['rowid'];
          } elseif ($cooperate_info['apply_type'] == 2) {
            $params['id'] = $cooperate_info['customer_id'];
            $tbl .= '_customer';
          }
          $params['id'] = format_info_id($params['id'], $tbl);
          //发送给对方的站内信
          $msg_id = $this->message_base_model->add_message($msg_type, $broker_id_msg, $broker_name_msg, $url_msg, $params);

          $this->push_func_model->send(1, 1, 9, $this->user_arr['broker_id'], $broker_id_msg, array('msg_id' => $msg_id), array('block_name' => $cooperate_info['block_name']));
        }
      } else {
        $cop_config = $this->cooperate_model->get_base_conf();
        $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
        $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
        $msg .= ",重新进入合作详情页后再操作。";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }
    $this->result($result['is_ok'], $result['msg']);
  }


  /*
     * 提交佣金分配方案
     */
  public function sub_allocation_scheme()
  {
    $c_id = $this->input->get('c_id', TRUE); //合同ID
    $cop_step = $this->input->get('step', TRUE); //合作步骤
    $old_esta = $this->input->get('old_esta', TRUE); //合作状态
    $cop_secret_key = $this->input->get('secret_key', TRUE); //加密字符串

    //经纪人信息
    $brokerid_a = $this->user_arr['broker_id']; //甲方经纪人编号
    $broker_name_a = $this->user_arr['truename']; //甲方经纪人姓名

    //根据表单参数重新拼接加密字符串
    $secret_param = array('cid' => $c_id, 'step' => $cop_step, 'esta' => $old_esta);
    $secret_key = $this->verify->user_enrypt($secret_param);
    $result = array('is_ok' => 0, 'msg' => '参数异常，提交佣金方案失败！');

    $up_num = 0;
    if ($c_id > 0 && $secret_key == $cop_secret_key) {
      $commission_arr = array();
      $commission_arr['seller_ratio'] = floatval($this->input->get('seller_ratio', TRUE));
      $commission_arr['buyer_ratio'] = floatval($this->input->get('buyer_ratio', TRUE));
      $commission_arr['a_ratio'] = floatval($this->input->get('a_ratio', TRUE));
      $commission_arr['b_ratio'] = floatval($this->input->get('b_ratio', TRUE));

      //获取合作当前状态的基本信息
      $cooperate_info = array();
      $cooperate_info = $this->cooperate_model->get_cooperate_baseinfo_by_cid($c_id);

      if (!empty($cooperate_info) && $cooperate_info['esta'] == $old_esta) {
        $up_num = $this->cooperate_model->sub_allocation_scheme($c_id, $brokerid_a, $commission_arr);

        //更新合同步骤日志
        if ($up_num > 0) {
          $result = array('is_ok' => 1, 'msg' => '您的佣金分配已经成功提交！'
            . '请耐心等待乙方经纪人确认或者您也可以直接联系对方确认！');

          //发送站内信
          $order_sn = $cooperate_info['order_sn'];
          $broker_id = $cooperate_info['brokerid_b'];
          $broker_name = $cooperate_info['broker_name_b'];
          $fromer = $broker_name_a;
          $url_msg = '/cooperate/send_order_list/?cid=' . $c_id;

          /*$this->load->model('message_base_model');
                    $msg_id = $this->message_base_model->pub_message(13 , $broker_id , $broker_name , $fromer , $order_sn , $url_msg);
                    $this->push_func_model->send(1, 1, 4, $this->user_arr['broker_id'], $broker_id, array('msg_id' => $msg_id));*/
        }
      } else {
        $cop_config = $this->cooperate_model->get_base_conf();
        $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
        $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
        $msg .= ",重新进入合作详情页后再操作。";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }
    $this->result($result['is_ok'], $result['msg']);
  }


  /*
     * 乙方确认佣金分配方案
     */
  public function confirm_allocation_scheme()
  {
    $c_id = $this->input->get('c_id', TRUE); //合同ID
    $step = $this->input->get('step', TRUE); //合作步骤
    $old_esta = $this->input->get('old_esta', TRUE); //合作状态
    $cop_secret_key = $this->input->get('secret_key', TRUE); //加密字符串

    //经纪人信息
    $brokerid = $this->user_arr['broker_id']; //经纪人编号
    $broker_name = $this->user_arr['truename']; //经纪人姓名

    //根据表单参数重新拼接加密字符串
    $secret_param = array('cid' => $c_id, 'step' => $step, 'esta' => $old_esta);
    $secret_key = $this->verify->user_enrypt($secret_param);
    $result = array('is_ok' => 0, 'msg' => '参数异常，确认佣金方案失败！');
    $up_num = 0;

    if ($c_id > 0 && $secret_key == $cop_secret_key) {
      /*判断当前合作状态（步骤为2，状态为已提交佣金方案，操作人为乙方才可以进行操作，防止双方步骤混乱）*/
      $cooperate_info = array();

      //获取合作当前状态的基本信息
      $cooperate_info = $this->cooperate_model->get_cooperate_baseinfo_by_cid($c_id);

      if (!empty($cooperate_info) && $cooperate_info['esta'] == $old_esta) {
        $up_num = $this->cooperate_model->confirm_allocation_scheme($c_id, $brokerid);

        //更新合同步骤日志
        if ($up_num > 0) {
          $result = array('is_ok' => 1,
            'msg' => '合作生效！本次合作信息已被系统记录，买卖达成后，将作为您收取佣金的凭证。祝您顺利开单！');

          //发送站内信、添加积分
          $order_sn = strip_tags($cooperate_info['order_sn']);
          $broker_id_a = intval($cooperate_info['brokerid_a']);
          $broker_name_a = strip_tags($cooperate_info['broker_name_a']);
          $broker_id_b = intval($cooperate_info['brokerid_b']);
          $broker_name_b = strip_tags($cooperate_info['broker_name_b']);
          $fromer = '';
          $url_a = '/cooperate/accept_order_list/?cid=' . $c_id;
          $url_b = '/cooperate/send_order_list/?cid=' . $c_id;
          $n_params['block_name'] = $cooperate_info['block_name'];
          //发送站内信
          /*$this->load->model('message_base_model');
                    $this->message_base_model->pub_message('4a',$broker_id_a,$broker_name_a,$fromer,$order_sn,$url_a,$n_params);
                    $msg_id = $this->message_base_model->pub_message('4b',$broker_id_b,$broker_name_b,$fromer,$order_sn,$url_b,$n_params);
                    $this->push_func_model->send(1, 1, 6, $this->user_arr['broker_id'], $broker_id_a, array('msg_id' => $msg_id));*/
        }
      } else {
        $cop_config = $this->cooperate_model->get_base_conf();
        $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
        $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
        $msg .= ",重新进入合作详情页后再操作。";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }
    $this->result($result['is_ok'], $result['msg']);
  }


  /*
     * 合作流程提交合作结果
     */
  public function sub_cooperate_result()
  {
    $c_id = $this->input->get('c_id', TRUE); //合同ID
    $step = $this->input->get('step', TRUE); //合作步骤
    $esta = $this->input->get('esta', TRUE); //提交的合作状态
    $old_esta = $this->input->get('old_esta', TRUE); //当前合作状态
    $total_price = $this->input->get('total_price', TRUE); //成交总价
    $cop_secret_key = $this->input->get('secret_key', TRUE); //加密字符串
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);

    //经纪人信息
    $broker_id_a = $this->user_arr['broker_id']; //甲方经纪人编号
    $broker_name_a = $this->user_arr['truename']; //甲方经纪人姓名

    $secret_param = array('cid' => $c_id, 'step' => $step, 'esta' => $old_esta);
    $secret_key = $this->verify->user_enrypt($secret_param);
    $result = array('is_ok' => 0, 'msg' => '参数异常，合作结果提交失败！');

    $up_num = 0;

    if ($c_id > 0 && $secret_key == $cop_secret_key) {
      /*判断当前合作状态（当步骤为3，步骤为3，状态为确认合作生效才可以进行操作，防止双方步骤混乱）*/
      $cooperate_info = array();

      //获取合作当前状态的基本信息
      $cooperate_info = $this->cooperate_model->get_cooperate_baseinfo_by_cid($c_id);
      $rowid = $cooperate_info['rowid'];
      $customer_id = $cooperate_info['customer_id'];
      $tbl = strip_tags($cooperate_info['tbl']);
      if (!empty($cooperate_info) && $cooperate_info['esta'] == $old_esta) {
        if ($esta == 7) {
          //操作日志
          $add_log_text = '';
          //获得经纪人所在的公司门店名
          $this->load->model('api_broker_model');
          $brokerinfo_a = $this->api_broker_model->get_baseinfo_by_broker_id(intval($cooperate_info['brokerid_a']));
          //接收方所属门店名
          $agency_name_a = $brokerinfo_a['agency_name'];
          $this->load->model('agency_model');
          $agency_a = $this->agency_model->get_by_id(intval($brokerinfo_a['company_id']));
          //接收方所属公司名
          $company_name_a = $agency_a['name'];
          //房源申请
          if ('1' == $cooperate_info['apply_type']) {
            if ('sell' == $cooperate_info['tbl']) {
              $add_log_text .= '出售房源：' . 'CS' . $cooperate_info['rowid'] . ' ' . $cooperate_info['block_name'] . ' ' . $cooperate_info['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            } else if ('rent' == $cooperate_info['tbl']) {
              $add_log_text .= '出租房源：' . 'CZ' . $cooperate_info['rowid'] . ' ' . $cooperate_info['block_name'] . ' ' . $cooperate_info['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            }
            //客源申请
          } else if ('2' == $cooperate_info['apply_type']) {
            if ('sell' == $cooperate_info['tbl']) {
              $this->load->model('buy_customer_model');
              $this->buy_customer_model->set_id(intval($cooperate_info['customer_id']));
              $customer_info = $this->buy_customer_model->get_info_by_id();
              $add_log_text .= '求购客源：' . 'QG' . $cooperate_info['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '万元 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            } else if ('rent' == $cooperate_info['tbl']) {
              $this->load->model('rent_customer_model');
              $this->rent_customer_model->set_id(intval($cooperate_info['customer_id']));
              $customer_info = $this->rent_customer_model->get_info_by_id();
              $add_log_text .= '求租客源：' . 'QZ' . $cooperate_info['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '元/月 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            }
          }

          $add_log_param = array();
          $add_log_param['company_id'] = $this->user_arr['company_id'];
          $add_log_param['agency_id'] = $this->user_arr['agency_id'];
          $add_log_param['broker_id'] = $this->user_arr['broker_id'];
          $add_log_param['broker_name'] = $this->user_arr['truename'];
          $add_log_param['type'] = 21;
          $add_log_param['text'] = $add_log_text;
          if ($devicetype == 'android') {
            $add_log_param['from_system'] = 2;
          } else {
            $add_log_param['from_system'] = 3;
          }
          $add_log_param['device_id'] = $deviceid;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();
          $this->operate_log_model->add_operate_log($add_log_param);

          //房源成交，提交交易总额
          $up_num = $this->cooperate_model->sub_total_price($c_id, $broker_id_a, $esta, $total_price);
          $message_type = "1-5";
          $show_msg = "恭喜您顺利开单！分佣之后别忘了给您的合作方评价哦！";

          if ($up_num > 0) {
            $house_id = intval($cooperate_info['rowid']);
            $tbl = strip_tags($cooperate_info['tbl']);
            $stop_reason = 'cop_deal_house';

            //房源成交通知终止与该房源相关的其它进行中的合作
            $this->load->model('cooperate_model');
            $this->cooperate_model->stop_cooperate($house_id, $tbl, $stop_reason);

            //更新房源为已成交状态
            if ($tbl == 'sell') {
              $this->load->model('sell_house_model');
              $this->sell_house_model->set_id($house_id);
              $this->sell_house_model->deal_house();
            } else if ($tbl == 'rent') {
              $this->load->model('rent_house_model');
              $this->rent_house_model->set_id($house_id);
              $this->rent_house_model->deal_house();
            }
          }
        } else if ($esta == 8) {
          //操作日志
          $add_log_text = '';
          //获得经纪人所在的公司门店名
          $this->load->model('api_broker_model');
          $brokerinfo_a = $this->api_broker_model->get_baseinfo_by_broker_id(intval($cooperate_info['brokerid_a']));
          //接收方所属门店名
          $agency_name_a = $brokerinfo_a['agency_name'];
          $this->load->model('agency_model');
          $agency_a = $this->agency_model->get_by_id(intval($brokerinfo_a['company_id']));
          //接收方所属公司名
          $company_name_a = $agency_a['name'];
          //房源申请
          if ('1' == $cooperate_info['apply_type']) {
            if ('sell' == $cooperate_info['tbl']) {
              $add_log_text .= '出售房源：' . 'CS' . $cooperate_info['rowid'] . ' ' . $cooperate_info['block_name'] . ' ' . $cooperate_info['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            } else if ('rent' == $cooperate_info['tbl']) {
              $add_log_text .= '出租房源：' . 'CZ' . $cooperate_info['rowid'] . ' ' . $cooperate_info['block_name'] . ' ' . $cooperate_info['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            }
            //客源申请
          } else if ('2' == $cooperate_info['apply_type']) {
            if ('sell' == $cooperate_info['tbl']) {
              $this->load->model('buy_customer_model');
              $this->buy_customer_model->set_id(intval($cooperate_info['customer_id']));
              $customer_info = $this->buy_customer_model->get_info_by_id();
              $add_log_text .= '求购客源：' . 'QG' . $cooperate_info['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '万元 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            } else if ('rent' == $cooperate_info['tbl']) {
              $this->load->model('rent_customer_model');
              $this->rent_customer_model->set_id(intval($cooperate_info['customer_id']));
              $customer_info = $this->rent_customer_model->get_info_by_id();
              $add_log_text .= '求租客源：' . 'QZ' . $cooperate_info['customer_id'] . ' ' . intval($customer_info['area_min']) . '-' . intval($customer_info['area_max']) . '平方米 ' . intval($customer_info['price_min']) . '-' . intval($customer_info['price_max']) . '元/月 ' . $data_arr['broker_name_a'] . ' ' . $company_name_a . ' ' . $agency_name_a;
            }
          }

          $add_log_param = array();
          $add_log_param['company_id'] = $this->user_arr['company_id'];
          $add_log_param['agency_id'] = $this->user_arr['agency_id'];
          $add_log_param['broker_id'] = $this->user_arr['broker_id'];
          $add_log_param['broker_name'] = $this->user_arr['truename'];
          $add_log_param['type'] = 22;
          $add_log_param['text'] = $add_log_text;
          if ($devicetype == 'android') {
            $add_log_param['from_system'] = 2;
          } else {
            $add_log_param['from_system'] = 3;
          }
          $add_log_param['device_id'] = $deviceid;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();
          $this->operate_log_model->add_operate_log($add_log_param);

          $up_num = $this->cooperate_model->sub_cooperate_result($c_id, $broker_id_a, $esta);
          $message_type = "1-11";
          $show_msg = "很遗憾本次合作交易未成功，合作中心机会还很多，祝您顺利开单！别忘了给您的合作方评价哦！";
        }

        //更新合同步骤日志
        if ($up_num > 0) {
          $result = array('is_ok' => 1, 'msg' => $show_msg);

          $order_sn = strip_tags($cooperate_info['order_sn']);
          $broker_id_a = intval($cooperate_info['brokerid_a']);
          $broker_name_a = strip_tags($cooperate_info['broker_name_a']);
          $broker_id_b = intval($cooperate_info['brokerid_b']);
          $broker_name_b = strip_tags($cooperate_info['broker_name_b']);
          $params['block_name'] = $cooperate_info['block_name'];
          $params['name'] = $broker_name_b;
          if ($cooperate_info['apply_type'] == 1) {
            $params['type'] = "f";
            $param['id'] = $rowid;
          } elseif ($cooperate_info['apply_type'] == 2) {
            $param['id'] = $customer_id;
            $tbl .= '_customer';
          }
          $params['id'] = format_info_id($param['id'], $tbl);
          //更新合作记录数据状态[统计合作成功率用]
          $this->load->model('cooperate_suc_ratio_base_model');
          //更新合作记录数据
          $ret_up = $this->cooperate_suc_ratio_base_model->update_cooperate_record($c_id, $broker_id_a, $broker_id_b, $esta);

          if ($ret_up) {
            //更新经纪人合作成功数据
            $this->cooperate_suc_ratio_base_model->update_broker_succ_raito($broker_id_a);
            $this->cooperate_suc_ratio_base_model->update_broker_succ_raito($broker_id_b);
          }

          //发送站内信
          $url_a = '/cooperate/accept_order_list/?cid=' . $c_id;
          $url_b = '/cooperate/send_order_list/?cid=' . $c_id;

          //消息模块MODEL
          $this->load->model('message_base_model');
          //发送甲方消息
          $msg_a = $this->message_base_model->add_message($message_type, $broker_id_a, $broker_name_a, $url_a, $params);
          //发送乙方消息
          $msg_b = $this->message_base_model->add_message($message_type, $broker_id_b, $broker_name_b, $url_b, $params);
          $msg_id = $this->user_arr['broker_id'] == $broker_id_a ? $msg_a : $msg_b;
          $broker_id_msg = $this->user_arr['broker_id'] == $cooperate_info['brokerid_a']
            ? $cooperate_info['brokerid_b'] : $cooperate_info['brokerid_a'];
          if ($esta == 7) {
            $this->push_func_model->send(1, 1, 10, $this->user_arr['broker_id'], $broker_id_msg, array('msg_id' => $msg_id), array('block_name' => $cooperate_info['block_name']));
          } else if ($esta == 8) {
            $this->push_func_model->send(1, 1, 8, $this->user_arr['broker_id'], $broker_id_msg, array('msg_id' => $msg_id), array('block_name' => $cooperate_info['block_name']));
          }
        }
      } else {
        $cop_config = $this->cooperate_model->get_base_conf();
        $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
        $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
        $msg .= ",重新进入合作详情页后再操作。";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }

    $this->result($result['is_ok'], $result['msg']);
  }

  //我接收方人的评价
  public function my_appraise_accept()
  {
    $this->_my_appraise('accept');
  }


  //我发起人的评价
  public function my_appraise_send()
  {
    $this->_my_appraise('send');
  }

  //我的评价
  private function _my_appraise($type)
  {
    $c_id = $this->input->get('c_id');
    //合作源和合作房源不能为空
    if ($type == '' || intval($c_id) == '') {
      $result = array('is_ok' => 1, 'msg' => '参数不合法');
      $this->result(0, '参数不合法');
      die();
    }
    //合作的房源信息
    $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);
    if (is_array($cooperate_info) && !empty($cooperate_info)) {
      //评价房源是否到了可评价阶段
      $cooperate_info_esta = $cooperate_info['esta'];
      //交易成功 - 成交失败 - 成交逾期失效 - 已成交终止合作 方可评价
      if ($cooperate_info_esta == 7 || $cooperate_info_esta == 8 ||
        $cooperate_info_esta == 9 || ($cooperate_info['step'] >= 3
          && $cooperate_info_esta == 11)
      ) //合作状态是否到这阶段了
      {
        //合同房源信息
        $cooperate_info['house'] = !empty($cooperate_info['house']) ?
          unserialize($cooperate_info['house']) : array();
        //合同甲方经济人信息
        $cooperate_info['brokerinfo_a'] = !empty($cooperate_info['broker_a']) ?
          unserialize($cooperate_info['broker_a']) : array();
        //合同乙方经纪人信息
        $cooperate_info['brokerinfo_b'] = !empty($cooperate_info['broker_b']) ?
          unserialize($cooperate_info['broker_b']) : array();

        if ($type == 'accept') //接收方
        {
          //被评价人
          $partner_id = $cooperate_info['brokerinfo_b']['broker_id'];
          //评价人
          $appraise_broker_id = $cooperate_info['brokerinfo_a']['broker_id'];
        } else if ($type == 'send') //发起方
        {
          //被评价人
          $partner_id = $cooperate_info['brokerinfo_a']['broker_id'];
          //评价人
          $appraise_broker_id = $cooperate_info['brokerinfo_b']['broker_id'];
        }
        //评价人和被价人之间的关系是否合法
        if ($appraise_broker_id != $this->user_arr['broker_id']) //模拟非法数据不合法
        {
          $this->result(0, '不允许评论其它经纪人的房源');
          die();
        }
        //判断此房源是否已经评价过了
        $this->load->model('api_broker_sincere_model');
        $appriase_broker_count = $this->api_broker_sincere_model->
        is_exist_transaction($appraise_broker_id,
          $cooperate_info['order_sn']);
        //判断是否已经评价过了
        if ($appriase_broker_count !== 0) //已经评论过
        {
          $this->result(0, '请不要重复评论');
          die();
        }
        //获取合作配置信息
        $cooperate_conf = $this->cooperate_model->get_base_conf();
        //读取合作方的基本信息
        $this->load->model('api_broker_model');
        $partner_info = $this->api_broker_model->get_baseinfo_by_broker_id($partner_id);
        //交易编号
        $data['order_sn'] = $cooperate_info['order_sn'];
        $data['tbl'] = $cooperate_info['tbl'];
        $unit = $cooperate_info['tbl'] == 'sell' ? '万' : '元/月';
        $appraise = array(
          'order_sn' => $cooperate_info['order_sn'],
          'district' => $cooperate_info['house']['districtname'],
          'room_hall' => $cooperate_info['house']['room'] . '室'
            . $cooperate_info['house']['hall'] . '厅',
          'area' => $cooperate_info['house']['buildarea'],
          'price' => $cooperate_info['house']['price'] . $unit,
          'broker_name' => $partner_info['truename'],
          'agency_name' => $partner_info['agency_name'],
          'esta_str' => $cooperate_conf['esta'][$cooperate_info_esta],
        );
        $this->result(1, '查询成功', $appraise);
      } else {

        $this->result(0, '查询失败');
        die();
      }
    }
  }

  //提交评论
  public function my_appraise_submit()
  {
    //要是你当前经纪人参与的房源 --不允许重复评价
    $type = ltrim($this->input->post('type', TRUE));
    $c_id = ltrim($this->input->post('c_id', TRUE));
    $trust_type = ltrim($this->input->post('trust_type', TRUE));
    $infomation = ltrim($this->input->post('infomation', TRUE));
    $attritude = ltrim($this->input->post('attritude', TRUE));
    $business = ltrim($this->input->post('business', TRUE));
    $content = ltrim($this->input->post('content', TRUE));

    //返回结果
    $data = array('result' => '', 'reason' => '');

    //判断参数合法性
    $para_status = true;
    if ($type == '' || intval($c_id) == '' || $trust_type == ''
      || $infomation == '' || $attritude == ''
      || $business == '' || $content == ''
    ) {
      $para_status = false;
    } else if (!in_array($trust_type, array(1, 2, 3))) //整体好评范围
    {
      $para_status = false;
    } else if (!in_array($infomation, array(1, 2, 3, 4, 5))) //信息真实度
    {
      $para_status = false;
    } else if (!in_array($attritude, array(1, 2, 3, 4, 5)))//态度满意度
    {
      $para_status = false;
    } else if (!in_array($business, array(1, 2, 3, 4, 5)))//业务专业度
    {
      $para_status = false;
    }

    if (!$para_status) {
      $this->result(0, '参数不合法');
      return;
    }
    $type = $type == 1 ? 'accept' : 'send';
    if ($trust_type == 1) {
      $trust_type = 'good';
    } else if ($trust_type == 2) {
      $trust_type = 'medium';
    } else if ($trust_type == 3) {
      $trust_type = 'bad';
    }
    //合作的房源信息
    $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);
    if (is_array($cooperate_info) && !empty($cooperate_info)) {
      $n_params['block_name'] = $cooperate_info['block_name'];
      //评价房源是否到了可评价阶段
      $cooperate_info_esta = $cooperate_info['esta'];
      //交易成功 - 成交失败 - 成交逾期失效 - 已成交终止合作 方可评价
      if ((in_array($cooperate_info_esta, array(7, 8, 9)) || (($cooperate_info_esta == 6
            || $cooperate_info_esta == 10 || $cooperate_info_esta == 11)
          && $cooperate_info['step'] >= 3))
      ) //合作状态是否到这阶段了
      {
        //合同房源信息
        $cooperate_info['house'] = !empty($cooperate_info['house']) ?
          unserialize($cooperate_info['house']) : array();
        //合同甲方经济人信息
        $cooperate_info['brokerinfo_a'] = !empty($cooperate_info['broker_a']) ?
          unserialize($cooperate_info['broker_a']) : array();
        //合同乙方经纪人信息
        $cooperate_info['brokerinfo_b'] = !empty($cooperate_info['broker_b']) ?
          unserialize($cooperate_info['broker_b']) : array();
        if ($type == 'accept') //接收方
        {
          //被评价人
          $partner_id = $cooperate_info['brokerinfo_b']['broker_id'];
          //评价人
          $appraise_broker_id = $cooperate_info['brokerinfo_a']['broker_id'];
        } else if ($type == 'send') //发起方
        {
          //被评价人
          $partner_id = $cooperate_info['brokerinfo_a']['broker_id'];
          //评价人
          $appraise_broker_id = $cooperate_info['brokerinfo_b']['broker_id'];
        }
        $this->load->model('api_broker_model');
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($partner_id);
        $partner_name = $brokerinfo['truename'];//被评价人姓名

        //评价人和被价人之间的关系是否合法
        if ($appraise_broker_id != $this->user_arr['broker_id']) //模拟非法数据不合法
        {
          $this->result(0, '不允许评论其它经纪人的房源');
          return;
        }
        //判断此房源是否已经评价过了
        $this->load->model('api_broker_sincere_model');
        $appriase_broker_count = $this->api_broker_sincere_model->
        is_exist_transaction($appraise_broker_id,
          $cooperate_info['order_sn']);
        //判断是否已经评价过了
        if ($appriase_broker_count !== 0) //已经评论过
        {
          $this->result(0, '请不要重复评论');
          return;
        }

        $cooperate_info['house']['tbl'] = $cooperate_info['tbl'];
        $trade_type = 0;
        if ($cooperate_info_esta == 7) //交易成功
        {
          $trade_type = 1;
        } else if ($cooperate_info_esta == 8)//交易失败
        {
          $trade_type = 2;
        } else if ($cooperate_info_esta == 9) //交易逾期
        {
          $trade_type = 3;
        } else if ($cooperate_info_esta == 11
          && $cooperate_info['step'] >= 3
        )//已成交终止合同
        {
          $trade_type = 4;
        } else if ($cooperate_info_esta == 6
          && $cooperate_info['step'] >= 3
        ) //合作生效后取消合作
        {
          $trade_type = 5;
        } else if ($cooperate_info_esta == 10
          && $cooperate_info['step'] >= 3
        ) //合作生效后冻结
        {
          $trade_type = 6;
        }
        if ($trade_type == 0) {
          $this->result(0, '参数不合法');
          return;
        }
        $info = array(
          'broker_id' => $appraise_broker_id, /**经纪人编号-评价人*/
          'trade_type' => $trade_type,/*交易的状态*/
          'transaction_id' => $cooperate_info['order_sn'], /**交易编号*/
          'house_info' => $cooperate_info['house'], /**房源详情*/
          'trust_type' => $trust_type, /*整体评价* good=好评 medium=中评 bad=差评*/
          'infomation' => $infomation, /*信息真实度 1-5分之间*/
          'attitude' => $attritude, /*合作满意度 1-5分之间*/
          'business' => $business, /*业务专业度 1-5分之间*/
          'content' => $content, /*评价内容*/
          'partner_id' => $partner_id /*合作方的经纪人编号-被评价人*/
        );
        $this->load->model('sincere_appraise_cooperate_model');
        $insert_id = $this->sincere_appraise_cooperate_model->appraise($info);
        if ($insert_id) {
          $appraise_set = ($type == 'accept' ? array('appraise_a' => $insert_id)
            : array('appraise_b' => $insert_id));
          $this->cooperate_model->update_info_by_cond($appraise_set, 'id = ' . $c_id);
          $params['name'] = '';
          $rowid = $cooperate_info['rowid']; //房源编号
          $tbl = $cooperate_info['tbl']; // 房源类型sell/rent

          $apply_type = $cooperate_info['apply_type'];//房客源
          if ($apply_type == 1) {
            $params['type'] = 'f';
          } else {
            $tbl .= '_customer';
          }
          $params['id'] = format_info_id($rowid, $tbl);

          $this->load->model('message_base_model');
          $this->message_base_model->add_message('2-17', $partner_id, $partner_name, '/my_evaluate/', $params);
          //增加积分
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
          $credit_result = $this->api_broker_credit_model->cooperate_appraise($cooperate_info);
          $data['result'] = 1;
          $data['reason'] = '评论成功！';
          //判断积分是否增加成功
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $data['reason'] .= '+' . $credit_result['score'] . '积分';
          }
          //增加等级分值
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
          $level_result = $this->api_broker_level_model->cooperate_appraise($cooperate_info);
          //判断成长值是否增加成功
          if (is_full_array($level_result) && $level_result['status'] == 1) {
            $data['reason'] .= '+' . $level_result['score'] . '成长值';
          }
        } else {
          $data['result'] = 0;
          $data['reason'] = '评论失败';
        }
      } else {
        $data['result'] = 0;
        $data['reason'] = '参数不合法';
      }
    } else {
      $data['result'] = 0;
      $data['reason'] = '参数不合法';
    }
    $this->result($data['result'], $data['reason']);
  }

  /**
   *
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = array();
    //状态
    $esta = isset($form_param['esta']) ? intval($form_param['esta']) : 0;
    if ($esta) {
      $cond_where['esta'] = $esta;
    }
    $order_sn = isset($form_param['order_sn']) ? trim($form_param['order_sn']) : "";
    //交易编号
    if ($order_sn) {
      $cond_where['order_sn'] = $order_sn;
    }
    $rowid = isset($form_param['rowid']) ? trim($form_param['rowid']) : 0;
    //房源编号
    if ($rowid) {
      $cond_where['rowid'] = $rowid;
    }

    if (isset($form_param['start_time']) && $form_param['start_time']) {
      $start_time = strtotime($form_param['start_time'] . " 00:00");
      $cond_where['dateline >='] = $start_time;
    }

    if (isset($form_param['end_time']) && $form_param['end_time']) {
      $end_time = strtotime($form_param['end_time'] . " 23:59");
      $cond_where['dateline <='] = $end_time;
    }

    if (isset($start_time) && isset($end_time) && $start_time > $end_time) {
      $this->jump(MLS_URL . '/cooperate/cooperate_query/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    return $cond_where;
  }

  /**
   * 初始化分页参数
   *
   * @access public
   * @param  int $current_page
   * @param  int $page_size
   * @return void
   */
  private function _init_pagination($current_page = 1, $page_size = 0)
  {
    /** 当前页 */
    $this->_current_page = ($current_page && is_numeric($current_page)) ?
      intval($current_page) : 1;

    /** 每页多少项 */
    $this->_limit = ($page_size && is_numeric($page_size)) ?
      intval($page_size) : $this->_limit;

    /** 偏移量 */
    $this->_offset = ($this->_current_page - 1) * $this->_limit;

    if ($this->_offset < 0) {
      redirect(base_url());
    }
  }

  //添加合同举报信息
  public function add_report()
  {
    $data = array();
    $this->load->model('cooperate_model');
    $ct_id = $this->input->post('c_id', TRUE);//合同id

    $report_type = $this->input->post('report_type', TRUE);//举报类型

    $cooperate_style = $this->input->post('cooperate_style', TRUE);

    //合同方式1发起2收到
    $report_text = $this->input->post('report_text', TRUE);

    //举报的具内容
    $report_time = time();//举报时间
    $cooperate_type = $this->input->post('cooperate_type', TRUE);//合同类型1.生效2.成功
    $broker_info = $this->user_arr;
    $brokerinfo_id = $broker_info['broker_id'];//举报人的id
    $brokerinfo_name = $broker_info['truename'];//举报人的姓名
    //图片名称
    $im_name = '';
    $img_naa = '';
    //加载图片地址
    $fileurl = $this->input->post('img_url', TRUE);
    if ($fileurl) {
      $img_na = json_decode($fileurl);
      $img_naa = implode(',', $img_na);
      $img_num = count($img_na);

      for ($i = 1; $i <= $img_num; $i++) {
        $im_name .= "证据";
        $im_name .= $i;
        $im_name .= ',';
      }
    }


    $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($ct_id);

    $cooperate_no = $cooperate_info['order_sn'];//合同编号
    $house_type = $cooperate_info['tbl'];//房源类型
    $type = $cooperate_info['apply_type'];

    //很据合同的id获取相关信息
    $where = "id ='$ct_id'";
    $select_fields = array('house', 'broker_a', 'broker_b');
    $this->cooperate_model->set_select_fields($select_fields);
    $return_info = $this->cooperate_model->get_att_list_by_cond($where);

    $house_info = $return_info['0']['house'];   //房源信息
    $broker_a_info = unserialize($return_info['0']['broker_a']);    //甲方经纪人信息、
    $broker_b_info = unserialize($return_info['0']['broker_b']);    //已方经纪人信息
    $brokered_id = '';//被举报人的id
    $brokered_name = '';//被举报人的name

    if ($brokerinfo_id != $broker_a_info['broker_id']) {
      $brokered_id = $broker_a_info['broker_id'];
      $brokered_name = $broker_a_info['truename'];
    } else {
      $brokered_id = $broker_b_info['broker_id'];
      $brokered_name = $broker_b_info['truename'];
    }

    $insert_data = array(
      'broker_id' => $brokerinfo_id,
      'broker_name' => $brokerinfo_name,
      'brokered_id' => $brokered_id,
      'brokered_name' => $brokered_name,
      'house_info' => $house_info,
      'house_type' => $house_type,
      'type' => $type,
      'cooperate_id' => $ct_id,
      'cooperate_no' => $cooperate_no,
      'cooperate_style' => $cooperate_style,
      'cooperate_type' => $cooperate_type,
      'report_type' => $report_type,
      'report_text' => $report_text,
      'photo_url' => $img_naa,
      'photo_name' => $im_name,
      'report_time' => $report_time,
      'status' => 1
    );

    //加载合同举报model
    $this->load->model('cooperate_report_model');
    $cooperate_where = "broker_id ='$brokerinfo_id'";
    $cooperate_where .= " AND cooperate_id ='$ct_id' ";
    $cooperate_where .= " AND cooperate_type ='$cooperate_type'";
    $cooperate_where .= " AND report_type ='$report_type'";
    $cooperate_num = $this->cooperate_report_model->count_by($cooperate_where);
    $inster_id = '';
    if ($cooperate_num == 0) {
      $inster_id = $this->cooperate_report_model->insert($insert_data);
    }
    $data['cooperate_num'] = $cooperate_num;
    if ($cooperate_num > 0 && $inster_id == 0) {
      $this->result('0', '举报失败该类型已经举报过了');
    }
    if ($inster_id > 0 && $cooperate_num == 0) {
      $this->result('1', '举报成功');
    }
    if ($inster_id == 0 && $cooperate_num == 0) {
      $this->result('0', '举报失败', $data);
    }
  }

  /**
   * 合作协议
   */
  public function protocol()
  {
    //结果状态
    $get_status = true;
    //来源  1: 合作申请 2:合作详情
    $from = $this->input->get('from', TRUE);
    //经纪人基础信息和信用积分模块
    $this->load->model('api_broker_base_model');
    if ($from == 1) {
      //1出售 2出租 3求购 4求租
      $apply_type = $this->input->get('apply_type', TRUE);
      $broker_id = $this->input->get('broker_id', TRUE);
      $rowid = $this->input->get('rowid', TRUE);
      if ($apply_type == 1 || $apply_type == 3) {
        $tbl = 'sell';
        $unit = '万';
      } else {
        $tbl = 'rent';
        $unit = '元/月';
      }
      $house_info = $this->cooperate_model->get_cooperate_house($tbl, $rowid);
      $house_info['price'] = strip_end_0($house_info['price']) . $unit;
      $house_info['buildarea'] = strip_end_0($house_info['buildarea']) . '平方米';
      //经纪人信息
      $broker_now = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_id);
      $company_a = $this->api_broker_base_model->get_by_agency_id($broker_now['company_id']);
      $broker_a_house = $broker_now['truename'] . '&nbsp;' . $broker_now['phone']
        . '&nbsp;(' . $broker_now['agency_name'] . ')'
        . '&nbsp;(' . $company_a['name'] . ')';
      $company_b = $this->api_broker_base_model->get_by_agency_id($this->user_arr['company_id']);
      $broker_b_house = $this->user_arr['truename'] . '&nbsp;' . $this->user_arr['phone']
        . '&nbsp;(' . $this->user_arr['agency_name'] . ')'
        . '&nbsp;(' . $company_b['name'] . ')';
      if ($apply_type == 1 || $apply_type == 2) {
        $house_side = $broker_a_house;
        $customer_side = $broker_b_house;
      } else {
        $house_side = $broker_b_house;
        $customer_side = $broker_a_house;
      }
      $create_time = date('Y-m-d');
    } else {
      $c_id = $this->input->get('c_id', TRUE);
      if ($c_id > 0) {
        $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);
        if (is_array($cooperate_info) && !empty($cooperate_info)) {
          //合同甲方经济人信息
          $brokerinfo_a = !empty($cooperate_info['broker_a']) ?
            unserialize($cooperate_info['broker_a']) : array();
          $broker_a_id = intval($brokerinfo_a['broker_id']);
          $broker_a_now = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_a_id);
          $company_a = $this->api_broker_base_model->get_by_agency_id($broker_a_now['company_id']);
          $broker_a_house = $brokerinfo_a['truename'] . '&nbsp;' . $brokerinfo_a['phone']
            . '&nbsp;(' . $brokerinfo_a['agency_name'] . ')'
            . '&nbsp;(' . $company_a['name'] . ')';
          //合同乙方经济人信息
          $brokerinfo_b = !empty($cooperate_info['broker_b']) ?
            unserialize($cooperate_info['broker_b']) : array();
          $broker_b_id = intval($brokerinfo_b['broker_id']);
          $broker_b_now = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_b_id);
          $company_b = $this->api_broker_base_model->get_by_agency_id($broker_b_now['company_id']);
          $broker_b_house = $brokerinfo_b['truename'] . '&nbsp;' . $brokerinfo_b['phone']
            . '&nbsp;(' . $brokerinfo_b['agency_name'] . ')'
            . '&nbsp;(' . $company_b['name'] . ')';
          $house_info = !empty($cooperate_info['house']) ?
            unserialize($cooperate_info['house']) : array();
          $unit = $cooperate_info['tbl'] == 'sell' ? '万' : '元/月';
          $house_info['price'] = strip_end_0($house_info['price']) . $unit;
          $house_info['buildarea'] = strip_end_0($house_info['buildarea']) . '平方米';
          //判断房源方和客源方
          if ($cooperate_info['customer_id'] == 0) {
            $house_side = $broker_a_house;
            $customer_side = $broker_b_house;
          } else {
            $house_side = $broker_b_house;
            $customer_side = $broker_a_house;
          }
          $create_time = date('Y-m-d', $cooperate_info['creattime']);
        } else {
          $get_status = false;
        }
      } else {
        $get_status = false;
      }
    }
    if ($get_status) {
      $house_detail = '楼盘名称：' . $house_info['blockname'] . ' 区属：' . $house_info['districtname']
        . ' 面积：' . $house_info['buildarea'] . ' 价格：' . $house_info['price']
        . ' 户型：' . $house_info['room'] . '室';
      //获取出售信息基本配置资料
      $this->load->model('house_config_model');
      $config = $this->house_config_model->get_config();
      $this->load->model('sell_house_model');
      $commission_ratio = $this->sell_house_model->get_commission_ratio_id($house_info['commission_ratio']);
      $commission_ratio_arr = $this->sell_house_model->get_commission_ratio($config['commission_ratio'][$commission_ratio]);
      $data_view = array(
        'house_side' => $house_side, 'customer_side' => $customer_side,
        'house_detail' => $house_detail, 'create_time' => $create_time,
        'commission_ratio_house' => $commission_ratio_arr['house'],
        'commission_ratio_customer' => $commission_ratio_arr['customer'],
      );
      $protocol_html = file_get_contents(dirname(__FILE__) . '/../views/cooperate/protocol.php');
      foreach ($data_view as $k => $v) {
        $protocol_html = str_replace('{' . $k . '}', $v, $protocol_html);
      }
      $protocol_html_arr = array('protocol' => $protocol_html);
      $this->result(1, '查询成功', $protocol_html_arr);
    } else {
      $this->result(0, '参数不合法');
    }
  }
}

/* End of file cooperate.php */
/* Location: ./application/mls/controllers/cooperate.php */
