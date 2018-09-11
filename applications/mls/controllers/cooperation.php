<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 城市控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class Cooperation extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'sh';


  /**
   * 录入经纪人id
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
    //加载区属模型类
    $this->load->model('district_model');
    //加载楼盘模型类
    $this->load->model('community_model');
    //表单验证
    $this->load->library('form_validation');
    //加载客户MODEL
    $this->load->model('cooperation_model');
    // $this->load->model('sell_house_model');
    //加载房源标题模板类
    $this->load->model('house_title_template_model');
    $this->load->model('rent_house_model');
    $this->load->model('broker_model');
    $this->load->model('house_collect_model');
    $this->load->model('sell_model');
    $this->load->model('api_broker_model');
    $this->load->library('Verify');
    $this->load->model('agency_model');
    $this->load->model('api_broker_credit_model');
    $this->load->model('api_broker_level_model');
    $this->load->model('operate_log_model');

    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
    }

  }


  /**
   * 出售房源列表页
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function index($page = 1)
  {
    //遗留 判断是否登录
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //模板使用数据
    $data = array();
    //页面菜单
    $data['user_menu'] = $this->user_menu;

    //三级功能菜单
    $data['user_func_menu'] = $this->user_func_menu;

    $data['broker_id'] = $broker_id;
    $data['truename'] = $broker_info['truename'];
    $data['group_id'] = $broker_info['group_id'];

    $agency_id = $broker_info['agency_id'];//经纪人门店编号
    $data['agency_id'] = $agency_id;
    $data['agency_name'] = $broker_info['agency_name'];//获取经纪人所对应门店的名称
    $data['phone'] = $broker_info['phone'];//获取经纪人对应的联系方式

    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $data['company_id'] = $company_id;
    //获取当前经纪人在官网注册时的公司和门店名
    $this->load->model('broker_info_model');
    $register_info = $this->broker_info_model->get_register_info_by_brokerid(intval($broker_info['id']));
    $data['register_info'] = $register_info;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $blockname = $this->input->post('blockname', true);
    //默认状态为有效
    if (!isset($post_param['status'])) {
      $post_param['status'] = 1;
    }
    $data['post_param'] = $post_param;
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);

    $this->_init_pagination($page);

    //查询房源条件
    $cond_where = "status > 0 ";

    //合作状态是2
    $cond_where .= " AND isshare = 2";
    if (!isset($post_param['post_broker_id'])) {
      $post_param['post_broker_id'] = $this->user_arr['broker_id'];
    }
    if (!isset($post_param['post_agency_id'])) {
      $post_param['post_agency_id'] = $this->user_arr['agency_id'];
    }

    //根据权限role_id获得当前经纪人的角色，判断店长
    $role_level = intval($this->user_arr['role_level']);
    $role_id = $this->user_arr['role_id'];
    $this->load->model('permission_company_group_model');
    $role_data = $this->permission_company_group_model->get_one_by(array('id' => $role_id));
    if (is_full_array($role_data)) {
      $system_group_id = intval($role_data['system_group_id']);
    }
    $data['role_level'] = $role_level;

    //店长
    if (is_int($role_level) && $role_level == 6) {
      $dist_street = $this->agency_model->get_by_id($agency_id);
      $agency_name = $dist_street['name'];
      $data['agency_list'] = $agency_name;
      if ($post_param['post_agency_id']) {  //根据门店编号获取经纪人列表数组
        $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($post_param['post_agency_id']);
      }
      //店长以上的获取全部分公司信息
    } else {
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_cooperation');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $this->user_arr['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $this->user_arr['agency_id'];
      }
      $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";

      $this->load->model('agency_model');
      $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

      if ($post_param['post_agency_id']) {
        $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($post_param['post_agency_id']);
      }
    }
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;
    //设置默认排序字段
    if ('1' == $house_list_order_field) {
      $default_order = 13;
    } else if ('2' == $house_list_order_field) {
      $default_order = 7;
    } else {
      $default_order = 0;
    }
    $roomorder = (isset($post_param['orderby_id']) && $post_param['orderby_id'] != '') ? intval($post_param['orderby_id']) : $default_order;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count =
      $this->cooperation_model->get_count_by_cond($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $data['pages'] = $pages;

    //获取列表内容
    $list = $this->cooperation_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    //房源id数组
    $house_id_arr = array();
    //提醒加亮房源id
    //$remind_house_id = array();
    //一段时间内跟进方式无‘堪房’，房源id
    $follow_no_kanfang_house_id = array();

    $this->load->model('api_broker_model');
    $brokeridstr = '';
    //$remind_house_id = $follow_yes_kanfang_house_id2 = $yellow_house_id2 = array();
    if ($list) {
      foreach ($list as $key => $val) {
        $house_id_arr[] = $val['id'];
        $brokeridstr .= $val['broker_id'] . ',';
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $list[$key]['telno'] = $brokerinfo['phone'];
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
        // 最新跟进时间
        $list[$key]['genjintime'] = $val['updatetime'] > 0 ? date('Y-m-d H:i', $val['updatetime']) : '';
      }
    }
    $data['list'] = $list;

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $config_data = $this->house_config_model->get_config();
    if (isset($config_data['status']) && !empty($config_data['status'])) {
      foreach ($config_data['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $config_data['status'][$k] = '暂不售';
        }
      }
    }
    $data['config'] = $config_data;

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }
    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );

    //加载分页类
    $this->load->library('page_list', $params);

    //调用分页函数
    $data['page_list'] = $this->page_list->show('jump');


    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/house_manage.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js');
    //加载发布页面模板
    $this->view('cooperation/index', $data);
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


  /**
   * 出售列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';

    //房源编号
    if (isset($form_param['house_id']) && $form_param['house_id'] > 0) {
      $house_id = intval($form_param['house_id']);
      $cond_where .= " AND id = '" . $house_id . "'";
      return $cond_where;
    }

    //板块 ，区属
    $street = isset($form_param['street']) ? intval($form_param['street']) : 0;
    $district = isset($form_param['district']) ? intval($form_param['district']) : 0;
    if ($street) {
      $cond_where .= " AND street_id = '" . $street . "'";
    } elseif ($district) {
      $cond_where .= " AND district_id = '" . $district . "'";
    }

    //楼盘ID
    if (!empty($form_param['block_name'])) {
      $cond_where .= " AND block_name like '%" . $form_param['block_name'] . "%'";
    }

    //独家代理
    if (!empty($form_param['entrust']) && $form_param['entrust'] > 0) {
      $cond_where .= $form_param['entrust'] == 1 ? " AND entrust = '1'" : " AND entrust != '1'";
    }

    //时间范
    if (!empty($form_param['searchtime'])) {
      $searchtime = intval($form_param['searchtime']);
      $now_time = time();
      switch ($searchtime) {
        case '1':
          $creattime = $now_time - 86400 * 30;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '2':
          $creattime = $now_time - 86400 * 90;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '3':
          $creattime = $now_time - 86400 * 180;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '4':
          $creattime = $now_time - 86400 * 360;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '5':
          $creattime = $now_time - 86400 * 360;
          $cond_where .= " AND createtime <  '" . $creattime . "' ";
          break;

        default :
          $creattime = $now_time - 86400 * 180;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
      }
    }

    //经纪人
    if (!empty($form_param['post_broker_id']) && $form_param['post_broker_id'] != '') {
      $broker_id = intval($form_param['post_broker_id']);
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    if (!empty($form_param['post_agency_id']) && $form_param['post_agency_id'] != '') {
      $agency_id = intval($form_param['post_agency_id']);
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    if (!empty($form_param['post_company_id']) && $form_param['post_company_id'] != '') {
      $company_id = intval($form_param['post_company_id']);
      $cond_where .= " AND company_id = '" . $company_id . "'";
    }
    return $cond_where;
  }

  /**
   * 获取排序参数
   *
   * @access private
   * @param  int $order_val
   * @return void
   */
  private function _get_orderby_arr($order_val)
  {
    $arr_order = array();

    switch ($order_val) {
      case 1:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 2:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'ASC';
        break;
      case 3:
        $arr_order['order_key'] = 'buildyear';
        $arr_order['order_by'] = 'DESC';
        break;
      case 4:
        $arr_order['order_key'] = 'buildyear';
        $arr_order['order_by'] = 'ASC';
        break;
      case 5:
        $arr_order['order_key'] = 'buildarea';
        $arr_order['order_by'] = 'ASC';
        break;
      case 6:
        $arr_order['order_key'] = 'buildarea';
        $arr_order['order_by'] = 'DESC';
        break;
      case 7:
        $arr_order['order_key'] = 'price';
        $arr_order['order_by'] = 'ASC';
        break;
      case 8:
        $arr_order['order_key'] = 'price';
        $arr_order['order_by'] = 'DESC';
        break;
      case 9:
        $arr_order['order_key'] = 'avgprice';
        $arr_order['order_by'] = 'ASC';
        break;
      case 10:
        $arr_order['order_key'] = 'avgprice';
        $arr_order['order_by'] = 'DESC';
        break;
      case 11:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'ASC';
        break;
      case 12:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 13:
        $arr_order['order_key'] = 'createtime';
        $arr_order['order_by'] = 'DESC';
        break;
      default:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
    }

    return $arr_order;
  }


  //确认合作或拒绝的方法
  public function change_is_share()
  {
    $house_id = $this->input->get('house_id');
    $type = $this->input->get('type');
    $where = array(
      'id' => $house_id,
    );
    $data = array('isshare' => $type);
    if ($data['isshare'] == 0) {
      $data['isshare_friend'] = 0;
    }
    if ('1' == $type) {
      $data['set_share_time'] = time();
    } else {

    }
    $rel = $this->cooperation_model->change_isshare_status($where, $data);
    //添加跟进信息
    $this->load->model('follow_model');
    $needarr = array();
    $needarr['broker_id'] = $this->user_arr['broker_id'];
    $needarr['house_id'] = $house_id;
    $needarr['agency_id'] = $this->user_arr['agency_id'];//门店ID
    $needarr['company_id'] = $this->user_arr['company_id'];//总公司id
    $needarr['type'] = 1;

    //操作日志
    $add_log_param = array();
    $add_log_param['company_id'] = $this->user_arr['company_id'];
    $add_log_param['agency_id'] = $this->user_arr['agency_id'];
    $add_log_param['broker_id'] = $this->user_arr['broker_id'];
    $add_log_param['broker_name'] = $this->user_arr['truename'];
    $add_log_param['type'] = 33;
    //$add_log_param['text'] = '修改基本设置';
    $add_log_param['from_system'] = 1;
    $add_log_param['from_ip'] = get_ip();
    $add_log_param['mac_ip'] = '127.0.0.1';
    $add_log_param['from_host_name'] = '127.0.0.1';
    $add_log_param['hardware_num'] = '测试硬件序列号';
    $add_log_param['time'] = time();

    if ($rel && $type == 1) {
      $add_log_param['text'] = '通过房源编号为' . $house_id . '的合作审核';
      $this->operate_log_model->add_operate_log($add_log_param);

      /*通过增加积分开始*/
      $house_info = $this->cooperation_model->get_info_by_id($house_id, 'sell_house');
      $this->api_broker_credit_model->set_broker_param(array('broker_id' => $house_info['broker_id']));
      $this->api_broker_credit_model->publish_cooperate_house(array('id' => $house_id), 1);
      /*通过增加积分结束*/
      /*通过增加等级分值开始*/
      $this->api_broker_level_model->set_broker_param(array('broker_id' => $house_info['broker_id']));
      $this->api_broker_level_model->publish_cooperate_house(array('id' => $house_id), 1);
      /*通过增加等级分值结束*/
      $needarr['text'] = "是否合作:店长审核中>>是";
      $this->follow_model->house_save($needarr);
      echo json_encode($data['result'] = 'ok');
      exit;
    } else if ($rel && $type == 3) {
      $needarr['text'] = "是否合作:店长审核中>>资料审核中";
      $this->follow_model->house_save($needarr);
      echo json_encode($data['result'] = 'ok');
      exit;
    } else {
      $add_log_param['text'] = '拒绝房源编号为' . $house_id . '的合作审核';
      $this->operate_log_model->add_operate_log($add_log_param);

      $needarr['text'] = "是否合作:店长审核中>>否";
      $this->follow_model->house_save($needarr);
      echo json_encode($data['result'] = 'fail');
      exit;
    }
  }

  //确认合作全部通过或拒绝
  public function change_all_share()
  {
    $this->load->model('sell_house_model');
    $ids = $this->input->get('ids');
    $all_id_arr = explode('|', $ids);
    //根据房源id，分离房源奖励类型
    $reward_type_1_id = array();
    $reward_type_2_id = array();

    if (is_full_array($all_id_arr)) {
      foreach ($all_id_arr as $k => $v) {
        $this->sell_house_model->set_search_fields(array('reward_type'));
        $this->sell_house_model->set_id(intval($v));
        $house_details = $this->sell_house_model->get_info_by_id();
        if ('1' == $house_details['reward_type']) {
          $reward_type_1_id[] = intval($v);
        } else if ('2' == $house_details['reward_type']) {
          $reward_type_2_id[] = intval($v);
        } else {
          $reward_type_1_id[] = intval($v);
        }
      }
    }

    $type = $this->input->get('type');
    //拒绝
    if ('0' == $type) {
      foreach ($all_id_arr as $key => $val) {
        $where = array(
          'id' => $val,
        );
        $data = array('isshare' => 0, 'isshare_friend' => 0);
        $rel = $this->cooperation_model->change_isshare_status($where, $data);
      }
    } else if ('1' == $type) {//通过
      foreach ($reward_type_1_id as $key => $val) {
        $where = array(
          'id' => $val,
        );
        $data = array('isshare' => 1);
        $rel = $this->cooperation_model->change_isshare_status($where, $data);
        if ($rel) {
          /*通过增加积分开始*/
          $house_info = $this->cooperation_model->get_info_by_id($val, 'sell_house');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $house_info['broker_id']));
          $this->api_broker_credit_model->publish_cooperate_house(array('id' => $val), 1);
          /*通过增加积分结束*/
          /*通过增加等级分值开始*/
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $house_info['broker_id']));
          $this->api_broker_level_model->publish_cooperate_house(array('id' => $val), 1);
          /*通过增加等级分值结束*/
        }
      }

      foreach ($reward_type_2_id as $key => $val) {
        $where = array(
          'id' => $val,
        );
        $data = array('isshare' => 3);
        $rel = $this->cooperation_model->change_isshare_status($where, $data);
      }
    }

    if ($rel && $type == 1) {
      echo json_encode($data['result'] = 'ok');
      exit;
    } else {
      echo json_encode($data['result'] = 'fail');
      exit;
    }
  }
}

/* End of file sell.php */
/* Location: ./application/mls/controllers/sell.php */
