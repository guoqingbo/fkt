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
  private $_limit = 15;

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
    $this->load->model('cooperate_chushen_model');
    $this->_broker_id = $this->user_arr['broker_id'];
    $this->load->model('push_func_model');
    $this->load->model('operate_log_model');
    $this->load->library('Verify');
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


  /*
     * 发起合作申请
     * @param string $tbl (sell/rent)
     * @param int $rowid
     * @param int $broker_a_id
     * @param int $broker_b_id
     */
  public function index()
  {
    $this->apply_cooperate();
  }


  /*
     * 根据合作ID获取单条合作详情
     * @param int $cid 合同编号
     * @param array 合同详情
     */
  public function detail($cid)
  {
    //需要判断经纪人的门店信息与该合同信息是否一致
    $cid = intval($cid);
    $cooperate_arr = array();

    if ($cid > 0) {
      $cooperate_arr = $this->cooperate_model->get_cooperate_by_cid_agencyid($cid, $this->user_arr['agency_id']);
    }

    return $cooperate_arr;
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

    //房源编号
    $rowid = intval($this->input->get('rowid', TRUE));

    //被申请的客源编号
    $customer_id = intval($this->input->get('customer_id', TRUE));

    //甲方经纪人帐号
    $broker_a_id = intval($this->input->get('broker_a_id', TRUE));

    //申请人经纪人帐号
    $broker_b_id = intval($this->user_arr['broker_id']);

    //合作类型
    $apply_type = intval($this->input->get('apply_type', TRUE));


    $apply_type = $apply_type > 0 ? $apply_type : 1;

    $cooperate_info = array();//房源信息
    $cooperate_info['houseinfo'] = array();
    //接收方
    $cooperate_info['brokerinfo_a'] = array();
    //发送方
    $cooperate_info['brokerinfo_b'] = array();
    //房源方
    $house_broker_info = array();
    //客源方
    $customer_broker_info = array();

    if ($rowid > 0 && $broker_a_id > 0 && in_array($tbl, array('sell', 'rent'))) {
      $cooperate_info['houseinfo'] = $this->cooperate_model->get_cooperate_house($tbl, $rowid);
      $cooperate_info['brokerinfo_a'] = $this->cooperate_model->get_cooperate_broker($broker_a_id);
      $cooperate_info['brokerinfo_b'] = $this->cooperate_model->get_cooperate_broker($broker_b_id);

      //对于分公司查找总公司
      if ($cooperate_info['brokerinfo_a']['company_id'] !== 0) {
        $company_id_a = $cooperate_info['brokerinfo_a']['company_id'];
        //调用公司模型
        $this->load->model('agency_model');
        $agency_a = $this->agency_model->get_by_id($company_id_a);
        $cooperate_info['company_name_a'] = $agency_a['name'];
      }
      if ($cooperate_info['brokerinfo_b']['company_id'] !== 0) {
        $company_id_b = $cooperate_info['brokerinfo_b']['company_id'];

        //调用公司模型
        $this->load->model('agency_model');
        $agency_b = $this->agency_model->get_by_id($company_id_b);
        $cooperate_info['company_name_b'] = $agency_b['name'];
      }
      //客源申请
      if (2 == $apply_type) {
        $house_broker_info = $cooperate_info['brokerinfo_b'];
        $customer_broker_info = $cooperate_info['brokerinfo_a'];
        $house_company_name = $cooperate_info['company_name_b'];
        $customer_company_name = $cooperate_info['company_name_a'];
      } else {
        //房源申请
        $house_broker_info = $cooperate_info['brokerinfo_a'];
        $customer_broker_info = $cooperate_info['brokerinfo_b'];
        $house_company_name = $cooperate_info['company_name_a'];
        $customer_company_name = $cooperate_info['company_name_b'];
      }
      //经纪人信用积分模块
      $this->load->model('api_broker_sincere_model');

      //接受方信息
      $data['appraise_avg_info_a'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_a_id);
      $data['trust_info_a'] = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_a_id);

      //发送方信息
      $data['appraise_avg_info_b'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_b_id);
      $data['trust_info_b'] = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_b_id);


//甲方经纪人好评率信息
      $count_info_b = $this->api_broker_sincere_model->get_trust_appraise_count($broker_a_id);
      $house_broker_info['good_rate'] = $count_info_b['good_rate'];
//乙方好评率信息,home
      $count_info_a = $this->api_broker_sincere_model->get_trust_appraise_count($broker_b_id);
      $customer_broker_info['good_rate'] = $count_info_a['good_rate'];


      //客源申请
      if (2 == $apply_type) {
        $trust_info_house = $data['trust_info_b'];
        $trust_info_customer = $data['trust_info_a'];
        $appraise_avg_info_house = $data['appraise_avg_info_b'];
        $appraise_avg_info_customer = $data['appraise_avg_info_a'];
      } else {
        //房源申请
        $trust_info_house = $data['trust_info_a'];
        $trust_info_customer = $data['trust_info_b'];
        $appraise_avg_info_house = $data['appraise_avg_info_a'];
        $appraise_avg_info_customer = $data['appraise_avg_info_b'];
      }
      $data['trust_info_house'] = $trust_info_house;
      $data['trust_info_customer'] = $trust_info_customer;
      $data['appraise_avg_info_house'] = $appraise_avg_info_house;
      $data['appraise_avg_info_customer'] = $appraise_avg_info_customer;

      //初始佣金比例
      if ($tbl == 'sell') {
        $this->load->model('sell_house_share_ratio_model');
        $cooperate_info['init_ratio'] = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
        $cooperate_info['master_a'] = '买方';
        $cooperate_info['master_b'] = '卖方';
      } else if ($tbl == 'rent') {
        $this->load->model('rent_house_share_ratio_model');
        $cooperate_info['init_ratio'] = $this->rent_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
        $cooperate_info['master_a'] = '承租方';
        $cooperate_info['master_b'] = '租赁方';
      }
      if ($cooperate_info['houseinfo']['reward_type'] != 2 && $tbl == 'sell') {
        //获取出售信息基本配置资料
        $this->load->model('house_config_model');
        $config = $this->house_config_model->get_config();
        $this->load->model('sell_house_model');
        $commission_ratio = $this->sell_house_model->get_commission_ratio_id($cooperate_info['houseinfo']['commission_ratio']);
        $cooperate_info['houseinfo']['commission_ratio_arr'] = $this->sell_house_model->get_commission_ratio($config['commission_ratio'][$commission_ratio]);
      }
    }

    $data['house_broker_info'] = $house_broker_info;
    $data['customer_broker_info'] = $customer_broker_info;
    $data['house_company_name'] = $house_company_name;
    $data['customer_company_name'] = $customer_company_name;
    $data['cooperate_info'] = $cooperate_info;
    $data['apply_type'] = $apply_type;
    $data['customer_id'] = $customer_id;
    $data['tbl'] = $tbl;
    $data['title'] = '合作申请';

    //操作加密字符串
    $secret_param = array('tbl' => $tbl, 'rowid' => $rowid, 'customer_id' => $customer_id,
      'broker_a_id' => $broker_a_id, 'broker_b_id' => $broker_b_id, 'apply_type' => $apply_type);
    $data['secret_key'] = $this->verify->user_enrypt($secret_param);

    //房源配置文件
    $this->load->model('house_config_model');

    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/cooperate_common.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');

    $this->view("cooperate/cooperate_apply", $data);
  }


  //提交合作信息
  public function add_cooperation_info()
  {
    $data_arr = array();
    $data_arr['tbl'] = $this->input->post('tbl', TRUE);
    $data_arr['rowid'] = $this->input->post('rowid', TRUE);
    $data_arr['customer_id'] = $this->input->post('customer_id', TRUE);
    $data_arr['agentid_a'] = $this->input->post('agentid_a', TRUE);
    $data_arr['brokerid_a'] = $this->input->post('brokerid_a', TRUE);
    $data_arr['broker_name_a'] = $this->input->post('broker_name_a', TRUE);
    $data_arr['phone_a'] = $this->input->post('phone_a', TRUE);
    $data_arr['agentid_b'] = $this->input->post('agentid_b', TRUE);
    $data_arr['brokerid_b'] = $this->input->post('brokerid_b', TRUE);
    $data_arr['phone_b'] = $this->input->post('phone_b', TRUE);
    $data_arr['house'] = $this->input->post('house', TRUE);

    $data_arr['block_name'] = strip_tags($this->input->post('block_name', TRUE));//小区名称
    $data_arr['broker_a'] = $this->input->post('broker_a', TRUE);
    $data_arr['broker_b'] = $this->input->post('broker_b', TRUE);
    $data_arr['broker_name_b'] = $this->input->post('broker_name_b', TRUE);
    $data_arr['apply_type'] = $this->input->post('apply_type', TRUE);
    $cop_secret_key = $this->input->post('secret_key', TRUE); //加密字符串

    $tbl = $data_arr['tbl'];
    //操作加密字符串
    $secret_param = array('tbl' => $data_arr['tbl'], 'rowid' => $data_arr['rowid'], 'customer_id' => $data_arr['customer_id'],
      'broker_a_id' => $data_arr['brokerid_a'], 'broker_b_id' => $data_arr['brokerid_b'], 'apply_type' => $data_arr['apply_type']);
    $secret_key = $this->verify->user_enrypt($secret_param);

    if ($cop_secret_key == $secret_key && $data_arr['brokerid_a'] > 0 &&
      $data_arr['brokerid_b'] > 0 && $data_arr['rowid'] > 0
    ) {
      //不能和自己合作
      if ($data_arr['brokerid_a'] == $data_arr['brokerid_b']) {
        $result = array('is_ok' => 0, 'msg' => '不能与自己合作');
        echo json_encode($result);
        exit;
      }

      //判断是否已经申请过合作信息
      $cooperate_num =
        $this->cooperate_model->get_valid_cooperate_num($data_arr['tbl'], $data_arr['brokerid_a'], $data_arr['brokerid_b'], $data_arr['rowid']);
      if ($cooperate_num >= 1) {
        $result = array('is_ok' => 0, 'msg' => '你已经申请过该合作，无法再次合作！');
        echo json_encode($result);
        exit;
      }

      //出售房源进入合作流程，房源中的奖励方式、奖金额添加到合作表
      if ('sell' == $tbl) {
        $this->load->model('sell_house_model');
        $this->sell_house_model->set_search_fields(array('reward_type', 'cooperate_reward'));
        $this->sell_house_model->set_id(intval($data_arr['rowid']));
        $sell_house_data = $this->sell_house_model->get_info_by_id();
        if (isset($sell_house_data['reward_type']) && !empty($sell_house_data['reward_type'])) {
          $data_arr['reward_type'] = intval($sell_house_data['reward_type']);
        }
        if (isset($sell_house_data['cooperate_reward']) && !empty($sell_house_data['cooperate_reward'])) {
          $data_arr['cooperate_reward'] = intval($sell_house_data['cooperate_reward']);
        }
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
        $add_log_param['from_system'] = 1;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = '127.0.0.1';
        $add_log_param['from_host_name'] = '127.0.0.1';
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();
        $this->operate_log_model->add_operate_log($add_log_param);

        $result = array('is_ok' => 1, 'msg' => '您的合作信息已经成功提交！'
          . '<br>请耐心等待甲方经纪人确认或者你可以直接联系对方确认！'
          . '<a href="/cooperate/send_order_list/">查看我的申请</a>');

        $cid = !empty($result_add) ? intval($result_add['cid']) : 0;
        $order_sn = $result_add['order_sn'];
        $brokerid_a = $data_arr['brokerid_a'];
        $broker_name_a = $data_arr['broker_name_a'];
        $fromer = $data_arr['broker_name_b'];
        $msg_type = '1-1-1';
        $url_a = '/cooperate/accept_order_list/?cid=' . $cid;
        $params['block_name'] = $data_arr['block_name'];
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
        //33
        $msg_id = $this->message_base_model->add_message($msg_type, $brokerid_a, $broker_name_a, $url_a, $params);
        //发送推送消息
        $this->push_func_model->send(1, 1, 1, $data_arr['brokerid_b'], $brokerid_a, array('msg_id' => $msg_id), array('broker_name' => $data_arr['broker_name_b']));
      } else {
        $result = array('is_ok' => 0, 'msg' => '合作申请提交失败！');
      }
    } else {
      $result = array('is_ok' => 0, 'msg' => '数据异常，无法申请合作！');
    }

    echo json_encode($result);
  }


  //申请客源合作选择房源弹框
  public function apply_customer_cooperate_window()
  {
    //客源编号
    $customer_id = $this->input->get('customer_id', TRUE);
    $kind = $this->input->get('kind', TRUE);
    $broker_id = intval($this->user_arr['broker_id']); //经纪人帐号

    //房源列表
    $house_list = array();
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
        $cond_distid .= $dist_id1 > 0 ? $dist_id1 : '0';
        $cond_distid .= $dist_id1 > 0 && $dist_id2 > 0 ? "," . $dist_id2 : '';
        $cond_distid .= $dist_id1 > 0 && $dist_id3 > 0 ? "," . $dist_id3 : '';

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

        $cond_where .= " AND sell_type = '" . $property_type . "' AND isshare = '1' AND status = 1 AND status != 5";
        $house_list = $this->sell_house_model->get_list_by_cond($cond_where, 0, 100);
      }

      $data['tbl'] = 'sell';//房源类型
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
        $cond_distid .= $dist_id1 > 0 ? $dist_id1 : '0';
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

        $cond_where .= " AND sell_type = '" . $property_type . "' AND isshare = '1' AND status = 1 AND status != 5 ";

        $house_list = $this->rent_house_model->get_list_by_cond($cond_where, 0, 100);
      }

      $data['tbl'] = 'rent';//房源类型
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

    //客源房源信息
    $data['customer_info'] = $customer_info;
    $data['house_list'] = $house_list;
    $data['district_arr'] = $district_arr;
    $data['street_arr'] = $temp_street_arr;
    $data['customer_kind'] = $kind;

    //获取出售信息基本配置资料
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();

    $data['title'] = '合作申请详情';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');

    $this->view("cooperate/apply_customer_cooperate_window", $data);
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
    $data['user_menu'] = $this->user_menu;

    //其它页面通过合作编号搜索合作
    $cid = (int)$this->input->get('cid', TRUE);

    //条件数组
    $cond_where = array();
    $cond_where_in = array();

    //默认排序字段
    $order_key = 'dateline';

    //默认降序排序
    $order_by = 'DESC';

    $data['fun_type'] = $type;

    //后缀
    if ($type == 'send') {
      $primary_postfix = "_b";    //主
      $secondary_postfix = "_a";  //次（合伙人）
      $form_action = 'send_order_list/';
    } else {
      $primary_postfix = "_a";
      $secondary_postfix = "_b";
      $form_action = 'accept_order_list/';
    }
    $data['user_arr'] = $this->user_arr;
    $data['primary_postfix'] = $primary_postfix;
    $data['secondary_postfix'] = $secondary_postfix;
    $data['form_action'] = $form_action;
    //权限条件($func_area  1 本人  2 门店  3公司)
    $func_area = $this->user_func_permission['area'];
    $data['func_area'] = $func_area;

    $cond_where['brokerid' . $primary_postfix] = $this->_broker_id;//只能看到本人的

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $get_param = array();
    if ($cid > 0) {
      $get_param['cid'] = $cid;
    }
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param, $get_param);
    $cond_where = array_merge($cond_where, $cond_where_ext);


    //条件-我方门店
    $agentid_w = isset($post_param['agentid_w']) ? intval($post_param['agentid_w']) : 0;
    if ($agentid_w) {
      $cond_where['agentid' . $primary_postfix] = $agentid_w;

      //获取经纪人列表数组
      $this->load->model('api_broker_model');
      $brokers = $this->api_broker_model->get_brokers_agency_id($agentid_w);
      $data['brokers'] = $brokers;
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
    $phone = isset($post_param['phone']) ? trim($post_param['phone']) : '';
    if ($phone) {
      $cond_where['phone' . $secondary_postfix] = $phone;
    }

    //条件-门店
    $agentid = isset($post_param['agentid']) ? trim($post_param['agentid']) : '';
    if ($agentid) {
      $cond_where['agentid' . $secondary_postfix] = $agentid;
    }

    //条件-门店
    $cond_where_like = array();
    $block_name = isset($post_param['block_name']) ? trim($post_param['block_name']) : '';
    if (!empty($block_name)) {
      $cond_where_like['block_name'] = $block_name;
    }

    //搜索条件中的待处理申请(1)、待评价合作(2)、合作生效(3)、交易成功(4)数量（随搜索条件变化
    if ($type == 'send') {
      $wait_do_count = 'wait_do_b';
    } else {
      $wait_do_count = 'wait_do_a';
    }

    $data['estas_num'] = $this->cooperate_model->get_cooperate_statistics_by_cond('all', $cond_where, $cond_where_in, $cond_where_like);
    $data['estas_num1'] = $this->cooperate_model->get_cooperate_statistics_by_cond($wait_do_count, $cond_where, $cond_where_in, $cond_where_like);
    $data['estas_num2'] = $this->cooperate_model->get_cooperate_statistics_by_cond('wait_appraise', $cond_where, $cond_where_in, $cond_where_like, $primary_postfix);
    $data['estas_num3'] = $this->cooperate_model->get_cooperate_statistics_by_cond('cop_effect', $cond_where, $cond_where_in, $cond_where_like);
    $data['estas_num4'] = $this->cooperate_model->get_cooperate_statistics_by_cond('cop_success', $cond_where, $cond_where_in, $cond_where_like);

    //条件-待处理申请(1)、待评价合作(2)、合作生效(3)、交易成功(4)
    $estas = isset($post_param['estas']) ? strip_tags($post_param['estas']) : 'all';
    $data['estas'] = $estas;
    $data['type'] = $type;

    switch ($estas) {
      case 'wait_do':
        $cond_where_in['esta'] = array(1, 2, 3);
        break;
      case 'wait_do_a':
        $cond_where_in['esta'] = array(1, 2);
        break;
      case 'wait_do_b':
        $cond_where_in['esta'] = array(3);
        break;
      case 'wait_appraise':
        $cond_where_in['esta'] = array(6, 7, 8, 9, 11);
        $cond_where_in['step'] = array(3, 4);
        $cond_where_in['appraise' . $primary_postfix] = 0;
        break;
      case 'cop_effect':
        $cond_where_in['esta'] = array(4);
        break;
      case 'cop_success':
        $cond_where_in['esta'] = array(7);
        break;
    }

    //排序-房源
    $order_key_rowid = isset($post_param['order_key_rowid']) ? intval($post_param['order_key_rowid']) : 0;
    $data['order_key_rowid'] = $order_key_rowid;
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

    if (!empty($list) && is_array($list)) {
      $ids = array();
      foreach ($list as $key => $val) {
        $ids[] = $val['id'];
      }

      $house_list = $this->cooperate_model->get_house_att_by_cid($ids);
      //echo '<pre>';print_r($house_list);die;
      if (!empty($house_list) && is_array($house_list)) {
        foreach ($list as $key => $val) {
          $list[$key]['house'] = !empty($house_list[$val['id']]) ? $house_list[$val['id']] : array();
          //echo '<pre>';print_r($list[$key]['house']);die;
        }
      }


      foreach ($list as $key => $val) {
        if ($val['esta'] == 7) {
          if ($val['apply_type'] == 1) { //判断房源方
            $house_broker_id = $val['brokerid_a'];
          } else {
            $house_broker_id = $val['brokerid_b'];
          }

          if ($house_broker_id == $this->user_arr['broker_id']) {
            if ($val['is_apply'] == 1) {
              $status = $this->cooperate_chushen_model->get_cooperate_chushen_status($val['id']);
              $list[$key]['status'] = $status['status'];
            }
          } else {
            unset($list[$key]['is_apply']);//如果不是房源方就不需要提交申请
          }
        }
      }
    }
    $data['list'] = $list;


    //加载出售基本配置MODEL
    $this->load->model('house_config_model');

    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //状态数组
    $base_conf = $this->cooperate_model->get_base_conf();
    $esta_conf = $base_conf['esta'];
    $data['esta_conf'] = $esta_conf;

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );

    //加载分页类
    $this->load->library('page_list', $params);

    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '合作申请--我发起的合作申请';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css'
      . ',mls/css/v1.0/myStyle.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'mls/js/v1.0/cooperate_common.js,mls/js/v1.0/broker_common.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/house.js,mls/js/v1.0/backspace.js,mls/js/v1.0/disk.js');

    $this->view("cooperate/order_list", $data);
  }


  //我接收的合作详情页
  public function my_accept_order($c_id)
  {
    $c_id = intval($c_id);
    $cooperate_info = array();
    $log_arr = array();
    $data['brokerid'] = $this->user_arr['broker_id']; //经纪人编号

    if ($c_id > 0) {
      $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);
      //echo '<pre>';print_r($cooperate_info['house']);die;
      if (is_array($cooperate_info) && !empty($cooperate_info)) {
        $this->load->model('api_broker_permission_model');
        //经纪人基础信息和信用积分模块
        $this->load->model('api_broker_base_model');
        $this->load->model('api_broker_sincere_model');

        //合同房源信息
        $cooperate_info['houseinfo'] = !empty($cooperate_info['house']) ?
          unserialize($cooperate_info['house']) : array();
        //echo '<pre>';print_r($cooperate_info['houseinfo']);die;
        //合同甲方经济人信息
        $cooperate_info['brokerinfo_a'] = !empty($cooperate_info['broker_a']) ?
          unserialize($cooperate_info['broker_a']) : array();
        $broker_a_id = intval($cooperate_info['brokerinfo_a']['broker_id']);

        //甲方经纪人积分、信用值信息
        $cooperate_info['appraise_avg_info_a'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_a_id);
        $cooperate_info['broker_a_now'] = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_a_id);
        $cooperate_info['trust_level_a'] = $this->api_broker_sincere_model->get_level_by_trust($cooperate_info['broker_a_now']['trust']);

        //合同乙方经纪人信息
        $cooperate_info['brokerinfo_b'] = !empty($cooperate_info['broker_b']) ?
          unserialize($cooperate_info['broker_b']) : array();
        //已方经纪人积分、信用值信息
        $broker_b_id = intval($cooperate_info['brokerinfo_b']['broker_id']);
        $cooperate_info['appraise_avg_info_b'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_b_id);
        $cooperate_info['broker_b_now'] = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_b_id);
        $cooperate_info['trust_level_b'] = $this->api_broker_sincere_model->get_level_by_trust($cooperate_info['broker_b_now']['trust']);
        //甲方经纪人好评率信息
        $count_info_b = $this->api_broker_sincere_model->get_trust_appraise_count($broker_b_id);
        $cooperate_info['broker_b_now']['good_rate'] = $count_info_b['good_rate'];
        //乙方好评率信息,home
        $count_info_a = $this->api_broker_sincere_model->get_trust_appraise_count($broker_a_id);
        $cooperate_info['broker_a_now']['good_rate'] = $count_info_a['good_rate'];

        //对于分公司查找总公司
        if ($cooperate_info['brokerinfo_a']['company_id'] !== 0) {
          $company_id_a = $cooperate_info['brokerinfo_a']['company_id'];
          //调用公司模型
          $this->load->model('agency_model');
          $agency_a = $this->agency_model->get_by_id($company_id_a);
          $cooperate_info['company_name_a'] = $agency_a['name'];
        }
        if ($cooperate_info['brokerinfo_b']['company_id'] !== 0) {
          $company_id_b = $cooperate_info['brokerinfo_b']['company_id'];

          //调用公司模型
          $this->load->model('agency_model');
          $agency_b = $this->agency_model->get_by_id($company_id_b);
          $cooperate_info['company_name_b'] = $agency_b['name'];
        }

        //合同取消原因信息
        $cooperate_info['cancel_reason'] = !empty($cooperate_info['cancel_reason']) ?
          unserialize($cooperate_info['cancel_reason']) : array();
        //合同拒绝原因信息
        $cooperate_info['refuse_reason'] = !empty($cooperate_info['refuse_reason']) ?
          unserialize($cooperate_info['refuse_reason']) : array();
        //合同佣金分配信息
        $cooperate_info['ratio'] = !empty($cooperate_info['ratio']) ?
          unserialize($cooperate_info['ratio']) : array();

        $rowid = $cooperate_info['rowid'];

        if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'sell') {
          if ($cooperate_info['step'] < 2) {
            $this->load->model('sell_house_share_ratio_model');
            $cooperate_info['init_ratio'] = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
          }

          //加载MODEL
          $this->load->model('sell_house_model');
          $this->sell_house_model->set_search_fields(array('broker_id'));
          $this->sell_house_model->set_id($rowid);
          $owner_arr = $this->sell_house_model->get_info_by_id();
          $data['house_owner'] = $owner_arr['broker_id'];

          $cooperate_info['master_a'] = '买方';
          $cooperate_info['master_b'] = '卖方';
        } else if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'rent') {
          if ($cooperate_info['step'] < 2) {
            $this->load->model('rent_house_share_ratio_model');
            $cooperate_info['init_ratio'] = $this->rent_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
          }

          //加载MODEL
          $this->load->model('rent_house_model');
          $this->rent_house_model->set_search_fields(array('broker_id'));
          $this->rent_house_model->set_id($rowid);
          $owner_arr = $this->rent_house_model->get_info_by_id();
          $data['house_owner'] = $owner_arr['broker_id'];

          $cooperate_info['master_a'] = '承租方';
          $cooperate_info['master_b'] = '租赁方';
        }
      }
      //获取出售信息基本配置资料
      $this->load->model('house_config_model');
      $data['config'] = $this->house_config_model->get_config();
      if ($cooperate_info['houseinfo']['reward_type'] != 2 && $cooperate_info['houseinfo']['tbl'] == 'sell') {
        //获取出售信息基本配置资料
        $this->load->model('sell_house_model');
        $commission_ratio = $this->sell_house_model->get_commission_ratio_id($cooperate_info['houseinfo']['commission_ratio']);
        $cooperate_info['houseinfo']['commission_ratio_arr'] = $this->sell_house_model->get_commission_ratio($data['config']['commission_ratio'][$commission_ratio]);
      }
      //客源申请
      if (2 == $cooperate_info['apply_type']) {
        $house_broker_info = $cooperate_info['brokerinfo_b'];
        $customer_broker_info = $cooperate_info['brokerinfo_a'];
        $trust_info_house = $cooperate_info['trust_level_b'];
        $trust_info_customer = $cooperate_info['trust_level_a'];
        $appraise_avg_info_house = $cooperate_info['appraise_avg_info_b'];
        $appraise_avg_info_customer = $cooperate_info['appraise_avg_info_a'];
        $broker_house_now = $cooperate_info['broker_b_now'];
        $broker_customer_now = $cooperate_info['broker_a_now'];
        $house_company_name = $cooperate_info['company_name_b'];
        $customer_company_name = $cooperate_info['company_name_a'];
      } else {
        //房源申请
        $house_broker_info = $cooperate_info['brokerinfo_a'];
        $customer_broker_info = $cooperate_info['brokerinfo_b'];
        $trust_info_house = $cooperate_info['trust_level_a'];
        $trust_info_customer = $cooperate_info['trust_level_b'];
        $appraise_avg_info_house = $cooperate_info['appraise_avg_info_a'];
        $appraise_avg_info_customer = $cooperate_info['appraise_avg_info_b'];
        $broker_house_now = $cooperate_info['broker_a_now'];
        $broker_customer_now = $cooperate_info['broker_b_now'];
        $house_company_name = $cooperate_info['company_name_a'];
        $customer_company_name = $cooperate_info['company_name_b'];
      }
      $data['house_broker_info'] = $house_broker_info;
      $data['customer_broker_info'] = $customer_broker_info;
      $data['trust_info_house'] = $trust_info_house;
      $data['trust_info_customer'] = $trust_info_customer;
      $data['appraise_avg_info_house'] = $appraise_avg_info_house;
      $data['appraise_avg_info_customer'] = $appraise_avg_info_customer;
      $data['broker_house_now'] = $broker_house_now;
      $data['broker_customer_now'] = $broker_customer_now;
      $data['house_company_name'] = $house_company_name;
      $data['customer_company_name'] = $customer_company_name;

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
    }

    $cooperate_info['log_record'] = $log_arr;

    //配置文件
    $cooperate_info['config'] = $this->cooperate_model->get_base_conf();

    //合作信息
    $data['cooperate_info'] = $cooperate_info;
    $cooperate_esta = intval($cooperate_info['esta']);
    $data['ct_id'] = $c_id;

    $data['title'] = '合作申请详情';

    //操作加密字符串
    $secret_param = array('cid' => $c_id, 'step' => $cooperate_info['step'], 'esta' => $cooperate_esta);
    $data['secret_key'] = $this->verify->user_enrypt($secret_param);

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/myStyle.css'
      . ',mls/css/v1.0/guest_disk.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/cooperate_common.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js,'
      . 'common/js/jquery.form.js,common/js/jquery.validate.min.js');
    switch ($cooperate_esta) {
      case '1':
        $template_name = "my_accept_order"; //我收到的合作页面
        break;
      case '2':
        $template_name = "my_accept_order_fenyong"; //佣金分成页面
        break;
      case '3':
        $template_name = "my_accept_order_wait_confirmed";  //待确认佣金分成
        break;
      case '4':
        $template_name = "order_sub_result";    //合作生效页面
        break;
      case '5':
        $template_name = "my_accept_order_failure"; //合作失败页面
        break;
      case '6':
        $template_name = "my_accept_order_cancled";//取消合作页面
        break;
      case '7':
        $template_name = "my_accept_order_sucess";  //合作成功页面
        break;
      case '8':
      case '9':
      case '10':
      case '11':
        $template_name = "my_accept_order_failure"; //合作逾期页面
        break;
      default :
        $template_name = "my_accept_order";
    }

    $data['effect'] = 1;

    $this->view("cooperate/" . $template_name, $data);
  }


  //我发送的合同申请
  public function my_send_order($c_id)
  {
    $c_id = intval($c_id);

    $data['brokerid'] = $this->user_arr['broker_id']; //经纪人编号
    $cooperate_info = array();
    $log_arr = array();
    $data['ct_id'] = $c_id;

    if ($c_id > 0) {
      $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);
      if (is_array($cooperate_info) && !empty($cooperate_info)) {
        $this->load->model('api_broker_permission_model');
        //经纪人基础信息和信用积分模块
        $this->load->model('api_broker_base_model');
        $this->load->model('api_broker_sincere_model');

        //合同房源信息
        $cooperate_info['houseinfo'] = !empty($cooperate_info['house']) ?
          unserialize($cooperate_info['house']) : array();
        //合同甲方经济人信息
        $cooperate_info['brokerinfo_a'] = !empty($cooperate_info['broker_a']) ?
          unserialize($cooperate_info['broker_a']) : array();
        $broker_a_id = intval($cooperate_info['brokerinfo_a']['broker_id']);

        //甲方经纪人积分、信用值信息
        $cooperate_info['appraise_avg_info_a'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_a_id);
        $cooperate_info['broker_a_now'] = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_a_id);
        $cooperate_info['trust_level_a'] = $this->api_broker_sincere_model->get_level_by_trust($cooperate_info['broker_a_now']['trust']);

        //合同乙方经纪人信息
        $cooperate_info['brokerinfo_b'] = !empty($cooperate_info['broker_b']) ?
          unserialize($cooperate_info['broker_b']) : array();

        //已方经纪人积分、信用值信息
        $broker_b_id = intval($cooperate_info['brokerinfo_b']['broker_id']);
        $cooperate_info['appraise_avg_info_b'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_b_id);
        $cooperate_info['broker_b_now'] = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_b_id);
        $cooperate_info['trust_level_b'] = $this->api_broker_sincere_model->get_level_by_trust($cooperate_info['broker_b_now']['trust']);

        $cooperate_info['brokerinfo_a'] = $this->cooperate_model->get_cooperate_broker($broker_a_id);

        $cooperate_info['brokerinfo_b'] = $this->cooperate_model->get_cooperate_broker($broker_b_id);


//甲方经纪人好评率信息
        $count_info_b = $this->api_broker_sincere_model->get_trust_appraise_count($broker_b_id);
        $cooperate_info['broker_b_now']['good_rate'] = $count_info_b['good_rate'];
//乙方好评率信息,home
        $count_info_a = $this->api_broker_sincere_model->get_trust_appraise_count($broker_a_id);
        $cooperate_info['broker_a_now']['good_rate'] = $count_info_a['good_rate'];

        //对于分公司查找总公司
        if ($cooperate_info['brokerinfo_a']['company_id'] !== 0) {
          $company_id_a = $cooperate_info['brokerinfo_a']['company_id'];
          //调用公司模型
          $this->load->model('agency_model');
          $agency_a = $this->agency_model->get_by_id($company_id_a);
          $cooperate_info['company_name_a'] = $agency_a['name'];
          //echo '<pre>';print_r($cooperate_info['company_name']);die;
        }
        if ($cooperate_info['brokerinfo_b']['company_id'] !== 0) {
          $company_id_b = $cooperate_info['brokerinfo_b']['company_id'];

          //调用公司模型
          $this->load->model('agency_model');
          $agency_b = $this->agency_model->get_by_id($company_id_b);
          $cooperate_info['company_name_b'] = $agency_b['name'];
        }

        //合同取消原因信息
        $cooperate_info['cancel_reason'] = !empty($cooperate_info['cancel_reason']) ?
          unserialize($cooperate_info['cancel_reason']) : array();
        //合同拒绝原因信息
        $cooperate_info['refuse_reason'] = !empty($cooperate_info['refuse_reason']) ?
          unserialize($cooperate_info['refuse_reason']) : array();
        //合同佣金分配信息
        $cooperate_info['ratio'] = !empty($cooperate_info['ratio']) ?
          unserialize($cooperate_info['ratio']) : array();

        $rowid = $cooperate_info['rowid'];
        if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'sell') {
          if ($cooperate_info['step'] < 2) {
            $this->load->model('sell_house_share_ratio_model');
            $cooperate_info['init_ratio'] = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
          }

          //加载MODEL
          $this->load->model('sell_house_model');
          $this->sell_house_model->set_search_fields(array('broker_id'));
          $this->sell_house_model->set_id($rowid);
          $owner_arr = $this->sell_house_model->get_info_by_id();
          $data['house_owner'] = $owner_arr['broker_id'];

          $cooperate_info['master_a'] = '买方';
          $cooperate_info['master_b'] = '卖方';
        } else if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'rent') {
          if ($cooperate_info['step'] < 2) {
            $this->load->model('rent_house_share_ratio_model');
            $cooperate_info['init_ratio'] = $this->rent_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
          }

          //加载MODEL
          $this->load->model('rent_house_model');
          $this->rent_house_model->set_search_fields(array('broker_id'));
          $this->rent_house_model->set_id($rowid);
          $owner_arr = $this->rent_house_model->get_info_by_id();
          $data['house_owner'] = $owner_arr['broker_id'];

          $cooperate_info['master_a'] = '承租方';
          $cooperate_info['master_b'] = '租赁方';
        }
      }
      //获取出售信息基本配置资料
      $this->load->model('house_config_model');
      $data['config'] = $this->house_config_model->get_config();
      if ($cooperate_info['houseinfo']['reward_type'] != 2 && $cooperate_info['houseinfo']['tbl'] == 'sell') {
        //获取出售信息基本配置资料
        $this->load->model('sell_house_model');
        $commission_ratio = $this->sell_house_model->get_commission_ratio_id($cooperate_info['houseinfo']['commission_ratio']);
        $cooperate_info['houseinfo']['commission_ratio_arr'] = $this->sell_house_model->get_commission_ratio($data['config']['commission_ratio'][$commission_ratio]);
      }
      //如果是申请客源
      if (2 == $cooperate_info['apply_type']) {
        $house_broker_info = $cooperate_info['brokerinfo_b'];
        $customer_broker_info = $cooperate_info['brokerinfo_a'];
        $trust_info_house = $cooperate_info['trust_level_b'];
        $trust_info_customer = $cooperate_info['trust_level_a'];
        $appraise_avg_info_house = $cooperate_info['appraise_avg_info_b'];
        $appraise_avg_info_customer = $cooperate_info['appraise_avg_info_a'];
        $broker_house_now = $cooperate_info['broker_b_now'];
        $broker_customer_now = $cooperate_info['broker_a_now'];
        $house_company_name = $cooperate_info['company_name_b'];
        $customer_company_name = $cooperate_info['company_name_a'];
      } else {
        //申请房源
        $house_broker_info = $cooperate_info['brokerinfo_a'];
        $customer_broker_info = $cooperate_info['brokerinfo_b'];
        $trust_info_house = $cooperate_info['trust_level_a'];
        $trust_info_customer = $cooperate_info['trust_level_b'];
        $appraise_avg_info_house = $cooperate_info['appraise_avg_info_a'];
        $appraise_avg_info_customer = $cooperate_info['appraise_avg_info_b'];
        $broker_house_now = $cooperate_info['broker_a_now'];
        $broker_customer_now = $cooperate_info['broker_b_now'];
        $house_company_name = $cooperate_info['company_name_a'];
        $customer_company_name = $cooperate_info['company_name_b'];
      }
      $data['house_broker_info'] = $house_broker_info;
      $data['customer_broker_info'] = $customer_broker_info;
      $data['trust_info_house'] = $trust_info_house;
      $data['trust_info_customer'] = $trust_info_customer;
      $data['appraise_avg_info_house'] = $appraise_avg_info_house;
      $data['appraise_avg_info_customer'] = $appraise_avg_info_customer;
      $data['broker_house_now'] = $broker_house_now;
      $data['broker_customer_now'] = $broker_customer_now;
      $data['house_company_name'] = $house_company_name;
      $data['customer_company_name'] = $customer_company_name;

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
    }

    $cooperate_info['log_record'] = $log_arr;

    //配置文件
    $cooperate_info['config'] = $this->cooperate_model->get_base_conf();
    $data['my_id'] = $c_id;

    //合作信息
    $data['cooperate_info'] = $cooperate_info;
    $cooperate_esta = !empty($cooperate_info['esta']) ? intval($cooperate_info['esta']) : 0;

    $data['title'] = '合作申请详情';

    //操作加密通行证字符串
    $secret_param = array('cid' => $c_id, 'step' => $cooperate_info['step'], 'esta' => $cooperate_esta);
    $data['secret_key'] = $this->verify->user_enrypt($secret_param);

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/myStyle.css'
      . ',mls/css/v1.0/guest_disk.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/cooperate_common.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate.js,mls/js/v1.0/backspace.js,'
      . 'common/js/jquery.form.js,common/js/jquery.validate.min.js');
    switch ($cooperate_esta) {
      case '1':
        $template_name = "my_send_order";//我发起的合作页面
        break;
      case '2':
        $template_name = "my_send_order_fenyong";//佣金分成页面
        break;
      case '3':
        $template_name = "my_send_order_wait_confirmed";//待确认佣金分成
        break;
      case '4':
        $template_name = "order_sub_result";//合同生效页面
        break;
      case '5':
        $template_name = "my_send_order_failure";//合作失败页面
        break;
      case '6':
        $template_name = "my_send_order_cancled";//取消合作页面
        break;
      case '7':
        $template_name = "my_send_order_sucess";//合作成功页面
        break;
      case '8':
      case '9':
      case '10':
      case '11':
        $template_name = "my_send_order_failure";//合作逾期页面
        break;
      default :
        $template_name = "my_accept_order";
    }
    $data['effect'] = 2;
    $this->view("cooperate/" . $template_name, $data);
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
      $rowid = $cooperate_info['rowid'];
      $tbl = $cooperate_info['tbl'];
      $apply_type = $cooperate_info['apply_type'];
      if ($apply_type == 1) {
        $params['type'] = 'f';
      } else {
        $tbl .= '_customer';
      }
      $params['id'] = format_info_id($rowid, $tbl);
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
          $add_log_param['from_system'] = 1;
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
          // $fromer = $broker_name_a;
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
          //判断积分是否增加成功
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
        $msg .= ",刷新合作列表页后再操作。<a href='javascript:void(0)' onclick='search_form.submit();return false;'>刷新>></a>";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }

    echo json_encode($result);
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

    //根据表单参数重新拼接加密字符串
    $secret_param = array('cid' => $c_id, 'step' => $step, 'esta' => $old_esta);
    $secret_key = $this->verify->user_enrypt($secret_param);
    $result = array('is_ok' => 0, 'msg' => '参数异常，无法接受合作！');

    if ($c_id > 0 && $secret_key == $cop_secret_key) {
      //获取合作当前状态的基本信息
      $cooperate_info = array();
      $this->cooperate_model->set_select_fields(array('order_sn', 'brokerid_a', 'broker_name_a',
        'rowid', 'brokerid_b', 'broker_name_b', 'step', 'esta', 'who_do', 'block_name', 'tbl', 'apply_type'));
      $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);
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
          $add_log_param['from_system'] = 1;
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
            $params['name'] = $fromer;
            $params['type'] = "f";
            $params['id'] = format_info_id($cooperate_info['rowid'], $tbl);
            $this->load->model('message_base_model');
            //33
            //给乙方发送消息
            $msg_id = $this->message_base_model->add_message("1-6-2", $broker_id, $broker_name, $url_msg, $params);

            $result = array('is_ok' => 1, 'msg' => '很遗憾，本次合作未达成！<br>'
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
            $params['name'] = $fromer;
            $params['type'] = "f";
            $params['id'] = format_info_id($cooperate_info['rowid'], $tbl);
            $this->load->model('message_base_model');
            //33
            $msg_id = $this->message_base_model->add_message("1-6-2", $broker_id, $broker_name, $url_msg, $params);

            $result = array('is_ok' => 1, 'msg' => '很遗憾，本次合作未达成！<br>'
              . '您可以再次与甲方经纪人沟通确认合作细节。达成的合作将由平台担保，保证您的佣金收益，祝您开单顺利！');
            //发送推送消息
            $this->push_func_model->send(1, 1, 5, $this->user_arr['broker_id'], $broker_id, array('msg_id' => $msg_id), array('block_name' => $cooperate_info['block_name']));
          }
        }
      } else {
        $cop_config = $this->cooperate_model->get_base_conf();
        $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
        $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
        $msg .= ",刷新合作列表页后再操作。<a href='javascript:void(0)' onclick='search_form.submit();return false;'>刷新>></a>";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }

    echo json_encode($result);
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
      $tbl = $cooperate_info['tbl'];
      if (!empty($cooperate_info) && $old_esta == $cooperate_info['esta']) {
        //更改合同状态、步骤、更新时间，更改下步操作人
        $cancle_arr = array('step' => $step, 'broker_id' => $broker_id, 'type' => $cancle_type, 'reason' => $cancle_reason);
        $up_num = $this->cooperate_model->cancle_cooperation($c_id, $broker_id, $cancle_arr);
        $tbl = $cooperate_info['tbl'];
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
          $add_log_param['from_system'] = 1;
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
          $params['block_name'] = $cooperate_info['block_name'];

          if ($cancle_type == 4) {
            $params['reason'] = strip_tags($cancle_reason);
          } else {
            //配置文件
            $cop_config = $this->cooperate_model->get_base_conf();
            $params['reason'] = !empty($cop_config['cancel_reason'][$cancle_type]) ?
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
              //33
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
          //33
          $msg_id = $this->message_base_model->add_message($msg_type, $broker_id_msg, $broker_name_msg, $url_msg, $params);
          $this->push_func_model->send(1, 1, 9, $this->user_arr['broker_id'], $broker_id_msg, array('msg_id' => $msg_id), array('block_name' => $cooperate_info['block_name']));
        }
      } else {
        $cop_config = $this->cooperate_model->get_base_conf();
        $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
        $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
        $msg .= ",刷新合作列表页后再操作。<a href='javascript:void(0)' onclick='search_form.submit();return false;'>刷新>></a>";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }

    echo json_encode($result);
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

      if ($commission_arr['seller_ratio'] > 0 && $commission_arr['buyer_ratio'] > 0 &&
        $commission_arr['a_ratio'] > 0 && $commission_arr['b_ratio'] > 0
      ) {
        //获取合作当前状态的基本信息
        $cooperate_info = array();
        $cooperate_info = $this->cooperate_model->get_cooperate_baseinfo_by_cid($c_id);

        if (!empty($cooperate_info) && $cooperate_info['esta'] == $old_esta) {
          $up_num = $this->cooperate_model->sub_allocation_scheme($c_id, $brokerid_a, $commission_arr);

          //更新合同步骤日志
          if ($up_num > 0) {
            $result = array('is_ok' => 1, 'msg' => '您的佣金分配已经成功提交！ <br>'
              . '请耐心等待乙方经纪人确认或者您也可以直接联系对方确认！');

            //发送站内信
            $order_sn = $cooperate_info['order_sn'];
            $broker_id = $cooperate_info['brokerid_b'];
            $broker_name = $cooperate_info['broker_name_b'];
            $fromer = $broker_name_a;
            $url_msg = '/cooperate/send_order_list/?cid=' . $c_id;

            $this->load->model('message_base_model');
            //佣金 去除
            // $msg_id = $this->message_base_model->pub_message(13 , $broker_id , $broker_name , $fromer , $order_sn , $url_msg);
            // $this->push_func_model->send(1, 1, 4, $this->user_arr['broker_id'], $broker_id, array('msg_id' => $msg_id));
          }
        } else {
          $cop_config = $this->cooperate_model->get_base_conf();
          $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
          $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
          $msg .= ",刷新合作列表页后再操作。<a href='javascript:void(0)' onclick='search_form.submit();return false;'>刷新>></a>";
          $result = array('is_ok' => 0, 'msg' => $msg);
        }
      } else {
        $result = array('is_ok' => 0, 'msg' => '佣金比例必须填写！');
      }
    }
    echo json_encode($result);
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
          $params['block_name'] = $cooperate_info['block_name'];
          //发送站内信
          $this->load->model('message_base_model');
          //佣金 去除
          // $this->message_base_model->pub_message('4a',$broker_id_a,$broker_name_a,$fromer,$order_sn,$url_a,$params);
          // $msg_id = $this->message_base_model->pub_message('4b',$broker_id_b,$broker_name_b,$fromer,$order_sn,$url_b,$params);
          //  $this->push_func_model->send(1, 1, 6, $this->user_arr['broker_id'], $broker_id_a, array('msg_id' => $msg_id));
        }
      } else {
        $cop_config = $this->cooperate_model->get_base_conf();
        $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
        $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
        $msg .= ",刷新合作列表页后再操作。<a href='javascript:void(0)' onclick='search_form.submit();return false;'>刷新>></a>";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }

    echo json_encode($result);
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
          $add_log_param['from_system'] = 1;
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
          $add_log_param['from_system'] = 1;
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
          //33
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
        $msg .= ",刷新合作列表页后再操作。<a href='javascript:void(0)' onclick='search_form.submit();return false;'>刷新>></a>";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }

    echo json_encode($result);
  }


  //我接收方人的评价
  public function my_appraise_accept($c_id)
  {
    $this->_my_appraise('accept', $c_id);
  }


  //我发起人的评价
  public function my_appraise_send($c_id)
  {
    $this->_my_appraise('send', $c_id);
  }


  //我的评价
  private function _my_appraise($type, $c_id)
  {
    //合作源和合作房源不能为空
    if ($type == '' || intval($c_id) == 0) {
      return false;
    }

    //合作的房源信息
    $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);

    if (is_array($cooperate_info) && !empty($cooperate_info)) {
      //评价房源是否到了可评价阶段
      $cooperate_info_esta = $cooperate_info['esta'];
      //交易成功 - 成交失败 - 成交逾期失效 - 已成交终止合作 方可评价
      if ($cooperate_info_esta == 7 || $cooperate_info_esta == 8 ||
        $cooperate_info_esta == 9 || ($cooperate_info['step'] >= 3
          && ($cooperate_info_esta == 6 || $cooperate_info_esta == 11 || $cooperate_info_esta == 10))
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
          echo '不允许评论其它经纪人的房源';
          return;
        }

        //判断此房源是否已经评价过了
        $this->load->model('api_broker_sincere_model');
        $appriase_broker_count =
          $this->api_broker_sincere_model->is_exist_transaction($appraise_broker_id, $cooperate_info['order_sn']);

        //判断是否已经评价过了
        if ($appriase_broker_count !== 0) //已经评论过
        {
          echo '<script type="text/javascript">window.parent.open_appraise_openwin();</script>';
          return;
        }
        //房源配置信息
        $this->load->model('house_config_model');
        $house_config = $this->house_config_model->get_config();
        $cooperate_info['house']['forward_str'] = $house_config['forward']
        [$cooperate_info['house']['forward']];
        $cooperate_info['house']['fitment_str'] = $house_config['fitment']
        [$cooperate_info['house']['fitment']];
        //房源信息
        $data['house'] = $cooperate_info['house'];
        //获取合作配置信息
        $cooperate_conf = $this->cooperate_model->get_base_conf();
        $data['esta'] = $cooperate_info_esta;
        $data['esta_description'] = $cooperate_conf['esta'][$data['esta']];
        //交易编号
        $data['order_sn'] = $cooperate_info['order_sn'];
        $data['tbl'] = $cooperate_info['tbl'];
        //读取合作方的基本信息
        $this->load->model('api_broker_model');
        $partner_info = $this->api_broker_model->get_baseinfo_by_broker_id($partner_id);
        //获取合作方的信用
        $this->load->model('api_broker_sincere_model');
        $partner_level = $this->api_broker_sincere_model->
        get_trust_level_by_broker_id($partner_id);
        //获取合作方的细节评分
        $partner_appraise_avg = $this->api_broker_sincere_model->
        get_appraise_and_avg($partner_id);
        $data['type'] = $type;
        $data['c_id'] = $c_id;
        $data['partner_info'] = $partner_info;
        $data['partner_level'] = $partner_level;
        $data['partner_appraise_avg'] = $partner_appraise_avg;
      } else {
        return;
      }
    }
    //页面标题
    $data['page_title'] = '评价对象';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/tipe_num.js,'
      . 'mls/js/v1.0/stars.js,mls/js/v1.0/radio_checkbox_mod.js');

    $this->view("cooperate/my_appraise", $data);
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
    } else if (!in_array($trust_type,
      array('good', 'medium', 'bad'))
    ) //整体好评范围
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
      $data['result'] = 0;
      $data['reason'] = '参数不合法1';
      echo json_encode($data);
      return;
    }

    //合作的房源信息
    $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);
    if (is_array($cooperate_info) && !empty($cooperate_info)) {
      $params['block_name'] = $cooperate_info['block_name'];
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
          $data['result'] = 0;
          $data['reason'] = '参数不合法';
          echo json_encode($data);
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
          $data['result'] = 0;
          $data['reason'] = '请不要重复评论';
          echo json_encode($data);
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
          $data['result'] = 0;
          $data['reason'] = '参数不合法';
          echo json_encode($data);
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
          $this->load->model('message_base_model'); //$this->message_base_model->pub_message('33',$partner_id,$partner_name,'',$cooperate_info['order_sn'],'/my_evaluate/',$params);
          //33
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
          //判断积分是否增加成功
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
        echo json_encode($data);
        return;
      }
    } else {
      $data['result'] = 0;
      $data['reason'] = '参数不合法';
    }
    echo json_encode($data);
  }


  /**
   *
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param, $get_param = array())
  {
    $cond_where = array();

    //合作编号
    $cid = isset($get_param['cid']) ? intval($get_param['cid']) : 0;
    if ($cid > 0) {
      $cond_where['id'] = $cid;
    }

    //状态
    $esta = isset($form_param['esta']) ? intval($form_param['esta']) : 0;
    if ($esta) {
      $cond_where['esta'] = $esta;
    }

    //交易编号
    $order_sn = isset($form_param['order_sn']) ? trim($form_param['order_sn']) : '';
    if ($order_sn) {
      $cond_where['order_sn'] = $order_sn;
    }

    //开始时间
    if (isset($form_param['start_time']) && $form_param['start_time']) {
      $start_time = strtotime($form_param['start_time'] . " 00:00");
      $cond_where['dateline >='] = $start_time;
    }

    //结束时间
    if (isset($form_param['end_time']) && $form_param['end_time']) {
      $end_time = strtotime($form_param['end_time'] . " 23:59");
      $cond_where['dateline <='] = $end_time;
    }

    return $cond_where;
  }


  //生效举报页面加载
  public function accept_report($ct_id, $cooper_type)
  {
    $cooper_type = intval($cooper_type);
    $ct_id = intval($ct_id);
    $view_name = '';

    if ($cooper_type == 1) {
      $view_name = 'order_sub_result_report';//收到生效举报页面
    }

    if ($cooper_type == 2) {
      $view_name = 'order_sub_send_report';//发起生效举报页面
    }

    if ($cooper_type == 3) {
      $view_name = 'my_accept_order_wait_confirmed_report';//收到的待分佣金举报
    }

    $data = array();
    $data['ct_id'] = intval($ct_id);

    //页面标题
    $data['page_title'] = '我要举报';
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/guest_disk.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadimg.js,'
      . 'mls/js/v1.0/cooperate_common.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification.js,mls/js/v1.0/backspace.js');

    //加载合同页面模板
    $this->view('cooperate/' . $view_name, $data);
  }


  //成功举报页面加载
  public function report($ct_id, $cooper_type)
  {
    $cooper_type = intval($cooper_type);
    $view_name = '';

    if ($cooper_type == 1) {
      $view_name = 'received_result_success_report';//收到的成功举报界面
    }

    if ($cooper_type == 2) {
      $view_name = 'send_result_success_report';//发起的成功举报界面
    }

    $data = array();
    $data['ct_id'] = intval($ct_id);

    //页面标题
    $data['page_title'] = '我要举报';
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadimg.js,'
      . 'mls/js/v1.0/cooperate_common.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification.js,mls/js/v1.0/backspace.js');
    //加载合同页面模板
    $this->view('cooperate/' . $view_name, $data);
  }


  //添加合同举报信息
  public function add_report()
  {
    $ct_id = $this->input->post('ct_id', TRUE);//合同id
    $report_type = $this->input->post('report_type', TRUE);//举报类型
    $cooperate_style = $this->input->post('cooperate_style', TRUE);//合同方式1发起2收到
    $report_text = $this->input->post('report_text', TRUE);//举报的具内容
    $report_time = time();//举报时间
    $cooperate_type = $this->input->post('cooperate_type', TRUE);//合同类型1.生效2.成功
    $broker_info = $this->user_arr;
    $brokerinfo_id = $broker_info['broker_id'];//举报人的id
    $brokerinfo_name = $broker_info['truename'];//举报人的姓名

    //图片名称
    $im_name = '';
    $fileurl = '';
    //加载图片地址
    $file = $this->input->post('img_name1', TRUE);
    if ($file) {
      $fileurl = implode(',', $file);
      $img_na = explode(',', $fileurl);
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
    $type = $cooperate_info['apply_type'];//类型1客源2房源

    //很据合同的id获取相关信息
    $where = "id = '$ct_id' ";
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
      'cooperate_type' => $cooperate_type,
      'cooperate_style' => $cooperate_style,
      'report_type' => $report_type,
      'report_text' => $report_text,
      'photo_url' => $fileurl,
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
    $return_report = array();
    $inster_id = '';
    if ($cooperate_num == 0) {
      $inster_id = $this->cooperate_report_model->insert($insert_data);
    }
    $return_report['cooperate_num'] = $cooperate_num;
    $return_report['insert_id'] = $inster_id;
    echo json_encode($return_report);
  }

  //根据合作id获取合作状态和步骤
  public function get_step_esta_by_id()
  {
    $result = array();
    $id = intval($this->input->get('ct_id'));
    if ($id > 0) {
      $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($id);
      if (isset($cooperate_info['step']) && isset($cooperate_info['esta']) && !empty($cooperate_info['step']) && !empty($cooperate_info['esta'])) {
        $result['step'] = $cooperate_info['step'];
        $result['esta'] = $cooperate_info['esta'];
        echo json_encode($result);
      }
    }
    exit;
  }

  //根据合作id打开合作初审资料弹窗
  function chushen($c_id = "")
  {
    $data['cooperate_info'] = $this->cooperate_model->get_cooperate_by_cid($c_id);
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/xcc.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/verification03.js');
    $this->view('cooperate/cooperate_chushen_pop', $data);
  }

  //提交初审资料
  public function add_apply()
  {
    $this->load->model('cooperate_chushen_model');
    $this->load->model('agency_model');
    $params = $this->input->post(NULL, TRUE);
    $params['pic'] = implode(',', $params['p_filename']);
    unset($params['p_filename']);
    if ($params['c_id'] && $params['seller_owner'] && $params['seller_idcard'] && $params['seller_telno'] &&
      $params['buyer_owner'] && $params['buyer_idcard'] && $params['buyer_telno'] && $params['pic']
    ) {
      if (preg_match('/[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u', $params['buyer_owner']) && preg_match('/[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u', $params['seller_owner'])) {
        if (preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $params['buyer_idcard']) && preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $params['seller_idcard'])) {
          if (preg_match('/(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/', $params['buyer_telno']) && preg_match('/(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/', $params['seller_telno'])) {
            //检查是否重复提交
            $result = $this->cooperate_chushen_model->check_chushen_is_apply($params['c_id']);
            if (is_full_array($result)) {
              $data = array('result' => 101, 'msg' => '正在审核中，不要重复提交');
            } else {
              $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($params['c_id']);
              if (is_full_array($cooperate_info) && $cooperate_info['is_apply'] == 0 && $cooperate_info['esta'] == 7) {
                $params['order_sn'] = $cooperate_info['order_sn'];
                $params['apply_type'] = $cooperate_info['apply_type'];
                //甲方经纪人信息
                $params['brokerid_a'] = $cooperate_info['brokerid_a'];//经纪人id
                $params['phone_a'] = $cooperate_info['phone_a'];//经纪人电话
                $params['broker_name_a'] = $cooperate_info['broker_name_a'];///经纪人名字
                $params['agencyid_a'] = $cooperate_info['agentid_a'];//门店id
                $agency_a = $this->agency_model->get_by_id($cooperate_info['agentid_a']);
                $params['agency_name_a'] = $agency_a['name'];//门店名称
                $params['agency_type_a'] = $agency_a['agency_type'];//门店类型
                $params['companyid_a'] = $agency_a['company_id'];//公司id
                $company_a = $this->agency_model->get_by_id($agency_a['company_id']);
                $params['company_name_a'] = $company_a['name'];//公司名称

                //乙方经纪人信息
                $params['brokerid_b'] = $cooperate_info['brokerid_b'];//经纪人id
                $params['phone_b'] = $cooperate_info['phone_b'];//经纪人电话
                $params['broker_name_b'] = $cooperate_info['broker_name_b'];///经纪人名字
                $params['agencyid_b'] = $cooperate_info['agentid_b'];//门店id
                $agency_b = $this->agency_model->get_by_id($cooperate_info['agentid_b']);
                $params['agency_name_b'] = $agency_b['name'];//门店名称
                $params['agency_type_b'] = $agency_b['agency_type'];//门店类型
                $params['companyid_b'] = $agency_b['company_id'];//公司id
                $company_b = $this->agency_model->get_by_id($agency_b['company_id']);
                $params['company_name_b'] = $company_b['name'];//公司名称

                $params['create_time'] = time();//经纪人id
                if ($this->cooperate_chushen_model->add_cooperate_chushen($params)) {
                  $data = array('result' => 200, 'msg' => '提交成功');
                } else {
                  $data = array('result' => 101, 'msg' => '提交失败');
                }
              } else {
                $data = array('result' => 101, 'msg' => '提交失败');
              }
            }
          } else {
            $data = array('result' => 101, 'msg' => '请输入正确的电话号码');
          }
        } else {
          $data = array('result' => 101, 'msg' => '请输入正确的身份证号码');
        }
      } else {
        $data = array('result' => 101, 'msg' => '请输入正确的姓名');
      }
    } else {
      $data = array('result' => 100, 'msg' => '请填完完整的数据');
    }
    echo json_encode($data);
  }
}

/* End of file cooperate.php */
/* Location: ./application/mls/controllers/cooperate.php */
