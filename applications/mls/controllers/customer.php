<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * Customer Controller CLASS
 *
 * 客源发布、列表、详情页以及客源相关管理功能 控制器
 *
 * @package         MLS
 * @subpackage      Controllers
 * @category        Controllers
 * @author          xz
 */
class Customer extends MY_Controller
{

  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'hz';


  /**
   * 录入经纪人id
   *
   * @access private
   * @var int
   */
  private $_boker_id = 0;

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
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('customer');
    $this->load->model('buy_customer_model');
    $this->load->model('district_model');
    $this->load->model('permission_system_group_model');
    $this->load->model('operate_log_model');

    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
    }
    $this->load->model('agency_model');
    $this->load->model('api_broker_credit_model');
    $this->load->model('api_broker_level_model');
    $this->load->model('cooperation_model');
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
   * 客源管理默认首页
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function index()
  {
    //默认列表
    $this->manage(1);
  }


  /**
   * 添加客源信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function publish()
  {
    //模板使用数据
    $data = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //是否开启合作中心
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    //是否开启合作审核
    $data['check_cooperate'] = $company_basic_data['check_cooperate'];
    //新增客源是否默认私客
    $data['is_customer_private'] = $company_basic_data['is_customer_private'];

    //获取求购信息基本配置资料
    $conf_customer = $this->buy_customer_model->get_base_conf();
    //根据基本设置，是否开启合作审核，重构数据
    if ('1' == $data['check_cooperate']) {
      $conf_customer['is_share'] = array(0 => '否', 2 => '是');
    }

    //区属板块信息
    $this->load->model('district_model');
    $data['district_arr'] = $this->district_model->get_district();

    $data['conf_customer'] = $conf_customer;

    //页面标题
    $data['page_title'] = '求购信息发布';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/myStyle.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification02.js,mls/js/v1.0/radio_checkbox_mod.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js');

    //加载发布页面模板
    $this->view('customer/buy_customer_publish', $data);
  }

  /**
   * 修改权限判断
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function modify_per_check($modify_id)
  {
    $result_arr = array();
    $group_id = $this->user_arr['group_id'];
    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->buy_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->buy_customer_model->set_id($modify_id);
    $owner_arr = $this->buy_customer_model->get_info_by_id();
    //修改客源权限
    $customer_modify_per = $this->broker_permission_model->check('17', $owner_arr);
    //修改客源关联门店权限
    $agency_customer_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '12');
    if ('1' == $group_id) {
      $result_str = 'yes_per_modify';
    } else {
      if ($customer_modify_per['auth']) {
        if ($agency_customer_modify_per) {
          $result_str = 'yes_per_modify';
        } else {
          $result_str = 'no_per_modify';
        }
      } else {
        $result_str = 'no_per_modify';
      }
    }
    $result_arr['result'] = $result_str;
    echo json_encode($result_arr);
    exit;
  }

  /**
   * 修改客源信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function modify($customer_id)
  {
    //模板使用数据
    $data = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //是否开启合作中心
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    //是否开启合作审核
    $data['check_cooperate'] = $company_basic_data['check_cooperate'];

    $customer_info = array();
    $customer_id = intval($customer_id);
    //经纪人信息
    $broker_info = $this->user_arr;
    $data['group_id'] = $broker_info['group_id'];
    //列表页地址
    $url_manage = MLS_URL . '/customer/manage';

    $this->buy_customer_model->set_id($customer_id);
    $customer_info = $this->buy_customer_model->get_info_by_id();
    if (is_full_array($customer_info)) {
      //新权限
      //范围（1公司2门店3个人）
      //获得当前数据所属的经纪人id和门店id
      $owner_arr = array(
        'broker_id' => $customer_info['broker_id'],
        'agency_id' => $customer_info['agency_id'],
        'company_id' => $customer_info['company_id'],
      );
      $customer_modify_per = $this->broker_permission_model->check('17', $owner_arr);
      //修改客源关联门店权限
      $agency_customer_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '12');
      if (!$customer_modify_per['auth'] && $data['group_id'] != '1') {
        //跳转到列表页
        $this->jump($url_manage, '对不起，您没有访问权限！');
      } else {
        if (!$agency_customer_modify_per && $data['group_id'] != '1') {
          //跳转到列表页
          $this->jump($url_manage, '对不起，您没有访问权限！');
        }
      }
      $data['modify_auth'] = $customer_modify_per['auth'];
      $data['customer_info'] = $customer_info;

      //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
      $role_level = intval($this->user_arr['role_level']);
      //店长以下的经纪人不允许操作他人的私盘
      if (is_int($role_level) && $role_level > 6) {
        if ($owner_arr['broker_id'] != $broker_info['broker_id'] && $customer_info['public_type'] == '1') {
          //跳转到列表页
          $this->jump($url_manage, '对不起，您没有访问权限！');
        }
      }
    } else {
      //跳转到列表页
      $this->jump($url_manage, '无此客源信息');
    }


    if ($customer_id > 0) {
      //获取求购信息基本配置资料
      $conf_customer = $this->buy_customer_model->get_base_conf();
      //根据基本设置，是否开启合作审核，重构数据
      if ('1' == $data['check_cooperate']) {
        $conf_customer['is_share'] = array(0 => '否', 2 => '是');
      }

      //区属板块信息
      $this->load->model('district_model');

      //区属数据
      $arr_district = $this->district_model->get_district();
      $district_num = count($arr_district);

      for ($i = 0; $i < $district_num; $i++) {
        $temp_dist_arr[$arr_district[$i]['id']] = $arr_district[$i];
      }

      $data['district_arr'] = $temp_dist_arr;

      //配置文件信息
      $data['conf_customer'] = $conf_customer;
    }

    //板块数据
    $dist_id1 = intval($customer_info['dist_id1']);
    $street_id1 = intval($customer_info['street_id1']);

    //选择的区属1
    if ($dist_id1 > 0) {
      $select_info['street_info'] =
        $this->district_model->get_street_bydist($dist_id1);
      $data['select_info1'] = $select_info;
    }

    //板块数据
    $dist_id2 = intval($customer_info['dist_id2']);
    $street_id2 = intval($customer_info['street_id2']);

    //选择的区属1
    if ($dist_id2 > 0) {
      $select_info['street_info'] =
        $this->district_model->get_street_bydist($dist_id2);
      $data['select_info2'] = $select_info;
    }

    //板块数据
    $dist_id3 = intval($customer_info['dist_id3']);
    $street_id3 = intval($customer_info['street_id3']);

    //选择的区属3
    if ($dist_id3 > 0) {
      $select_info['street_info'] =
        $this->district_model->get_street_bydist($dist_id3);
      $data['select_info3'] = $select_info;
    }

    //页面标题
    $data['page_title'] = '求购信息修改页面';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/myStyle.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification02.js,mls/js/v1.0/radio_checkbox_mod.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js');

    //加载详情页面模板
    $this->view('customer/buy_customer_modify', $data);
  }


  /**
   * 求购客源列表页
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function manage($page = 1)
  {
    //经纪人信息
    $broker_info = $this->user_arr;
    //模板使用数据
    $data = array();

    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      //获取房源默认排序字段
      $customer_list_order_field = $company_basic_data['house_list_order_field'];
      //获取默认查询时间
      $buy_customer_query_time = $company_basic_data['buy_customer_query_time'];
      //获取客源跟进无堪房红色警告时间
      $buy_customer_check_time = $company_basic_data['buy_customer_check_time'];
      //两次客源跟进红色警告时间
      $customer_follow_spacing_time = $company_basic_data['customer_follow_spacing_time'];
      //是否开启合作中心
      $open_cooperate = $company_basic_data['open_cooperate'];
      //是否开启合作审核
      $check_cooperate = $company_basic_data['check_cooperate'];
      //求购客源最后跟进天数
      $buy_customer_follow_last_time1 = $company_basic_data['buy_customer_follow_last_time1'];
      $buy_customer_follow_last_time2 = $company_basic_data['buy_customer_follow_last_time2'];
      //客源列表页字段
      $buy_customer_field = $company_basic_data['buy_customer_field'];
    } else {
      $buy_customer_follow_last_time2 = $buy_customer_follow_last_time1 = $check_cooperate = $open_cooperate = $buy_customer_query_time = $buy_customer_check_time = $customer_follow_spacing_time = $customer_list_order_field = $buy_customer_field = '';
        //客源列表字段
        if ('10' == $broker_info['role_level']) {
            $buy_customer_field = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,19,20,21';
        }
    }
    $data['buy_customer_follow_last_time1'] = $buy_customer_follow_last_time1;//绿色
    $data['buy_customer_follow_last_time2'] = $buy_customer_follow_last_time2;//紫色
    $data['buy_customer_check_time'] = $buy_customer_check_time;//红色
    $data['customer_follow_spacing_time'] = $customer_follow_spacing_time;//橙色

    //客源列表字段
    if ('11' == $broker_info['role_level']) {
      $buy_customer_field = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,19,20,21';
    }
    $buy_customer_field_arr = array();
    if (!empty($buy_customer_field)) {
      $buy_customer_field_arr = explode(',', $buy_customer_field);
    }
    $data['buy_customer_field_arr'] = $buy_customer_field_arr;

    $data['open_cooperate'] = $open_cooperate;
    $data['check_cooperate'] = $check_cooperate;

    //页面菜单
    $data['user_menu'] = $this->user_menu;

    //根据查看客源权限获取门店与经济人相关信息
    $view_other_per = $this->broker_permission_model->check('26');
    $data['lists_auth'] = $view_other_per['auth'];

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //是否提交了表单数据
    $is_submit_form = false;
    if (is_full_array($post_param)) {
      $is_submit_form = true;
    }

    $broker_id = intval($broker_info['broker_id']);
    $data['broker_id'] = $broker_id;
    $data['truename'] = $broker_info['truename'];
    $data['agency_id'] = $broker_info['agency_id'];
    $data['agency_name'] = $broker_info['agency_name'];
    $data['group_id'] = $broker_info['group_id'];

    //根据门店id获取所在门店下的所有经纪人
    if (!isset($post_param['agenctcode'])) {
      $agency_id = $broker_info['agency_id'];
    } else {
      $agency_id = $broker_info['agency_id'];
    }

    //所在公司的分店信息
    $company_id = intval($broker_info['company_id']);
    $data['company_id'] = $company_id;
    //获取当前经纪人在官网注册时的公司和门店名
    $this->load->model('broker_info_model');
    $register_info = $this->broker_info_model->get_register_info_by_brokerid(intval($broker_info['id']));
    $data['register_info'] = $register_info;

    //根据数据范围，获得门店数据
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_view_customer');
    $all_access_agency_ids = '';
    if (is_full_array($access_agency_ids_data)) {
      foreach ($access_agency_ids_data as $k => $v) {
        $all_access_agency_ids .= $v['sub_agency_id'] . ',';
      }
      $all_access_agency_ids .= $broker_info['agency_id'];
      $all_access_agency_ids = trim($all_access_agency_ids, ',');
    } else {
      $all_access_agency_ids = $broker_info['agency_id'];
    }

    if (!empty($company_id)) {
      $this->load->model('agency_model');
      $data['agencys'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
      $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($agency_id);
    }

    //获取求购信息基本配置资料
    $conf_customer = $this->buy_customer_model->get_base_conf();
    $data['conf_customer'] = $conf_customer;

    //区属板块信息
    $this->load->model('district_model');
    $arr_district = $this->district_model->get_district();
    $district_num = count($arr_district);
    for ($i = 0; $i < $district_num; $i++) {
      $temp_dist_arr[$arr_district[$i]['id']] = $arr_district[$i];
    }

    $data['district_arr'] = $temp_dist_arr;
    $dist_id = intval($post_param['dist_id']);
    $street_id = intval($post_param['street_id']);
    if ($dist_id > 0) {
      $select_info['street_info'] = $this->district_model->get_street_bydist($dist_id);
      $data['select_info'] = $select_info;
    }

    //板块数据
    $arr_street = $this->district_model->get_street();
    $street_num = count($arr_street);
    for ($i = 0; $i < $street_num; $i++) {
      $temp_street_arr[$arr_street[$i]['id']] = $arr_street[$i];
    }
    $data['street_arr'] = $temp_street_arr;

    //查询条件
    //'area' : (1公司，2门店，3本人)
    if ($broker_info['role_level'] != '11') {
      $cond_where = "company_id = '" . $company_id . "' AND agency_id in (" . $all_access_agency_ids . ")";
    }

    if (!isset($post_param['agenctcode']) && $company_basic_data['rent_house_indication_range'] > 1) {
      $post_param['agenctcode'] = intval($broker_info['agency_id']);
    }

    if (!isset($post_param['broker_id']) && $company_basic_data['rent_house_indication_range'] > 2) {
      $post_param['broker_id'] = $broker_id;
    }

    //基本设置默认查询时间
    if ($post_param['create_time_range'] == 0) {
      //半年
      if ('1' == $buy_customer_query_time) {
        $post_param['create_time_range'] = 5;
      }
      //一年
      if ('2' == $buy_customer_query_time) {
        $post_param['create_time_range'] = 6;
      }
    }

    if (!$data['lists_auth']) {
      //本人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }

    if ($is_submit_form) {
      $customer_manage = array(
        'dist_id' => $post_param['dist_id'],
        'street_id' => $post_param['street_id'],
        'cmt_name' => $post_param['cmt_name'],
        'cmt_id' => $post_param['cmt_id'],
        'area_min' => $post_param['area_min'],
        'area_max' => $post_param['area_max'],
        'price_min' => $post_param['price_min'],
        'price_max' => $post_param['price_max'],
        'agenctcode' => $post_param['agenctcode'],
        'broker_id' => $post_param['broker_id'],
        'property_type' => $post_param['property_type'],
        'room' => $post_param['room'],
        'public_type' => $post_param['public_type'],
        'status' => $post_param['status'],
        'is_share' => $post_param['is_share'],
        'orderby_id' => $post_param['orderby_id'],
        'page' => $post_param['page'],
        'telno' => $post_param['telno'],
          'truename' => $post_param['truename'],
        'create_time_range' => $post_param['create_time_range']
      );
      setcookie('customer_manage', serialize($customer_manage), time() + 3600 * 24 * 7, '/');
    } else {
      $customer_manage_search = unserialize($_COOKIE['customer_manage']);
      if (is_full_array($customer_manage_search)) {
        $post_param['dist_id'] = $customer_manage_search['dist_id'];
        $post_param['street_id'] = $customer_manage_search['street_id'];
        $post_param['cmt_name'] = $customer_manage_search['cmt_name'];
        $post_param['cmt_id'] = $customer_manage_search['cmt_id'];
        $post_param['area_min'] = $customer_manage_search['area_min'];
        $post_param['area_max'] = $customer_manage_search['area_max'];
        $post_param['price_min'] = $customer_manage_search['price_min'];
        $post_param['price_max'] = $customer_manage_search['price_max'];
        $post_param['agenctcode'] = $customer_manage_search['agenctcode'];
        $post_param['broker_id'] = $customer_manage_search['broker_id'];
        $post_param['property_type'] = $customer_manage_search['property_type'];
        $post_param['room'] = $customer_manage_search['room'];
        $post_param['public_type'] = $customer_manage_search['public_type'];
        $post_param['status'] = $customer_manage_search['status'];
        $post_param['is_share'] = $customer_manage_search['is_share'];
        $post_param['orderby_id'] = $customer_manage_search['orderby_id'];
        $post_param['page'] = $customer_manage_search['page'];
        $post_param['telno'] = $customer_manage_search['telno'];
          $post_param['truename'] = $customer_manage_search['truename'];
        $post_param['create_time_range'] = $customer_manage_search['create_time_range'];
      }
    }
    $dist_id = intval($post_param['dist_id']);
    $street_id = intval($post_param['street_id']);
    if ($dist_id > 0) {
      $select_info['street_info'] = $this->district_model->get_street_bydist($dist_id);
      $data['select_info'] = $select_info;
    }

    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    //分页每页限制数(特定房源 合作 群发 采集 列表页需求)
    if ($post_param['limit_page']) {
      setcookie('limit_page', $post_param['limit_page'], time() + 3600 * 24 * 7, '/');
      $limit_page = $post_param['limit_page'];
    } elseif ($_COOKIE['limit_page']) {
      $limit_page = $_COOKIE['limit_page'];
    } else {
      $limit_page = $this->_limit;
    }
    $this->_init_pagination($page, $limit_page);
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= !empty($cond_where) ? ' AND ' . $cond_where_ext : $cond_where_ext;

    //排序字段
    //设置默认排序字段
    if ('1' == $customer_list_order_field) {
      $default_order = '7';
    } else if ('2' == $customer_list_order_field) {
      $default_order = '5';
    } else {
      $default_order = '';
    }
    $customer_order = isset($post_param['orderby_id']) ? $post_param['orderby_id'] : $default_order;
    $order_arr = $this->_get_orderby_arr($customer_order);
    if (strpos($cond_where, "status = '1'")) {
      $data['check_on'] = 'check_on';
    }
      //无公司门店时
      if ($this->user_arr['company_id'] <= 0 && $this->user_arr['agency_id'] <= 0) {
          $cond_where .= " AND broker_id = '" . $this->user_arr['broker_id'] . "'";
      }
    //符合条件的总行数
    $this->_total_count =
      $this->buy_customer_model->get_buynum_by_cond($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    if ($post_param['page'] > $pages) {
      $this->_init_pagination($pages, $limit_page);
    }
    $data['pages'] = $pages;

    //获取列表内容
    $customer_list =
      $this->buy_customer_model->get_buylist_by_cond($cond_where, $this->_offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    //客源id数组
    $customer_id_arr = array();
    $remind_customer_id = array();

    //循环获取经纪人姓名和门店信息
    if (count($customer_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();
      foreach ($customer_list as $key => $value) {
        $customer_id_arr[] = $value['id'];
        $_broker_id = intval($value['broker_id']);
        if ($_broker_id > 0 && !in_array($_broker_id, $broker_id_arr)) {
          array_push($broker_id_arr, $_broker_id);
        }
      }

      //经纪人MODEL
      $this->load->model('api_broker_model');
      $broker_num = count($broker_id_arr);
      for ($i = 0; $i < $broker_num; $i++) {
        $broker_arr = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id_arr[$i]);
        $customer_broker_info[$broker_id_arr[$i]] = $broker_arr;
      }

      $data['customer_broker_info'] = $customer_broker_info;

      //获得当前页面中需要提醒的客源
      $this->load->model('remind_model');
      $remind_where_cond = array(
        'broker_id' => $broker_id,
        'tbl' => 3,
        'status' => 0
      );

      $remind_customer = $this->remind_model->get_remind($remind_where_cond, array('row_id', $customer_id_arr));
      if (!empty($remind_customer) && is_array($remind_customer)) {
        foreach ($remind_customer as $k => $v) {
          $remind_customer_id[] = $v['row_id'];
        }
      }

      //获得当前页面中不需要红色警告的客源（有电话跟进方式）
      $buy_customer_check_day = intval($buy_customer_check_time);
      $data['buy_customer_check_day'] = $buy_customer_check_day;
      $_where_cond_red = 'id > 0 ';
      $_where_cond_red .= 'and type = 3 and follow_way = 3 ';
      $_where_in_red = array('customer_id', $customer_id_arr);
      $this->load->model('follow_model');
      $follow_yes_phone_customer_id = $this->follow_model->get_follow_customer($_where_cond_red, $_where_in_red);
      $follow_yes_phone_customer_id2 = array();
      if (is_array($follow_yes_phone_customer_id) && !empty($follow_yes_phone_customer_id)) {
        foreach ($follow_yes_phone_customer_id as $k => $v) {
          $follow_yes_phone_customer_id2[] = $v['customer_id'];
        }
      }
      //获得当前页面中需要橙色警告的客源（最近的跟进明细间隔超过基本设置的天数）
      $this->load->model('house_customer_sub_model');
      $_where_in_rellow = array('id', $customer_id_arr);
      $yellow_customer_id = $this->house_customer_sub_model->get_buy_customer_by_arrids($_where_in_rellow);
      $yellow_customer_id2 = array();
      if (is_array($yellow_customer_id) && !empty($yellow_customer_id)) {
        foreach ($yellow_customer_id as $k => $v) {
          $yellow_customer_id2[] = $v['id'];
        }
      }

      //获得当前页面中，每条房源的最后跟进日期。（绿色和紫色）
      $_where_cond_last_follow = 'id > 0 ';
      $_where_cond_last_follow .= 'and type = 3 ';
      $_where_in_last_follow = array('customer_id', $customer_id_arr);
      $this->load->model('follow_model');
      $all_follow_data = $this->follow_model->get_follow_house_order_by_date($_where_cond_last_follow, $_where_in_last_follow);
      //客源id去重
      $all_last_follow_data = array('customer_id' => array(), 'data' => array());
      if (is_full_array($all_follow_data)) {
        foreach ($all_follow_data as $k => $v) {
          if (!in_array($v['customer_id'], $all_last_follow_data['customer_id'])) {
            $all_last_follow_data['customer_id'][] = $v['customer_id'];
            $all_last_follow_data['data'][] = $v;
          }
        }
      }
      //出售房源最后跟进日期超过天数（绿色）
      $green_status = false;
      $follow_green_customer_id = array();
      if (intval($buy_customer_follow_last_time1) > 0 && is_full_array($all_last_follow_data['data'])) {
        $green_status = true;
        foreach ($all_last_follow_data['data'] as $k => $v) {
          $follow_date_time = strtotime($v['date']);
          if (time() - $follow_date_time > intval($buy_customer_follow_last_time1) * 24 * 3600 && time() - $follow_date_time < intval($buy_customer_follow_last_time2) * 24 * 3600) {
            $follow_green_customer_id[] = $v['customer_id'];
          }
        }
      }
      //出售房源最后跟进日期超过天数（紫色）
      $zi_status = false;
      $follow_zi_customer_id = array();
      if (intval($buy_customer_follow_last_time2) > 0 && is_full_array($all_last_follow_data['data'])) {
        $zi_status = true;
        foreach ($all_last_follow_data['data'] as $k => $v) {
          $follow_date_time = strtotime($v['date']);
          if (time() - $follow_date_time > intval($buy_customer_follow_last_time2) * 24 * 3600) {
            $follow_zi_customer_id[] = $v['customer_id'];
          }
        }
      }

    }

    $data['remind_customer_id'] = $remind_customer_id;
    $data['follow_red_customer_id'] = $follow_yes_phone_customer_id2;
    $data['yellow_customer_id'] = $yellow_customer_id2;
    $data['customer_list'] = $customer_list;
    $data['green_status'] = $green_status;
    $data['follow_green_customer_id'] = $follow_green_customer_id;
    $data['zi_status'] = $zi_status;
    $data['follow_zi_customer_id'] = $follow_zi_customer_id;

    //页面标题
    $data['page_title'] = '求购信息列表页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/customer_list.js,'
      . 'mls/js/v1.0/cooperate_common.js');

    //表单数据
    $data['post_param'] = $post_param;

    //底部最小化菜单
    $this->load->model('broker_info_min_log_model');
    $where_cond = array(
      'broker_id' => $broker_id
    );
    $query_result = $this->broker_info_min_log_model->get_log($where_cond);
    $buy_list_min_str = $query_result[0]['buy_customer_list'];
    $buy_list_min_arr = array();
    $buy_list_min_arr2 = array();
    if (!empty($buy_list_min_str)) {
      $buy_list_min_arr = explode(',', trim($buy_list_min_str, ','));
    }
    if (is_full_array($buy_list_min_arr)) {
      foreach ($buy_list_min_arr as $k => $v) {
        $this->buy_customer_model->set_search_fields(array('dist_id1', 'street_id1', 'price_min', 'price_max'));
        $this->buy_customer_model->set_id(intval($v));
        $info = $this->buy_customer_model->get_info_by_id();
        $name = '';
        $name = $temp_dist_arr[intval($info['dist_id1'])]['district'] . '-' . $temp_street_arr[intval($info['street_id1'])]['streetname'] . '-' . intval($info['price_min']) . '-' . intval($info['price_max']) . '万';
        $buy_list_min_arr2[] = array(
          'customer_id' => $v,
          'name' => $name
        );
      }
    }
    $data['buy_list_min_arr'] = $buy_list_min_arr2;

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post/ajax
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('5');//(特定房源 合作 群发 采集 列表页需求)

    //加载发布页面模板
    $this->view('customer/buy_customer_list', $data);
  }

  function del_search_cookie($type = '')
  {
    $result = false;

    if (!empty($type)) {
      $result = setcookie($type, '', time() - 1, '/');
    }
      $res = array();
    if ($result) {
        $res['status'] = 'success';
    } else {
        $res['status'] = 'failed';
    }
      echo json_encode($res);
    exit;
  }


  /**
   * 求购公客列表页
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function manage_pub($page = 1)
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      //是否开启合作中心
      $open_cooperate = $company_basic_data['open_cooperate'];
    } else {
      $open_cooperate = '';
    }
    //模板使用数据
    $data = array();
    $data['open_cooperate'] = $open_cooperate;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //是否提交了表单数据
    $is_submit_form = false;
    if (is_full_array($post_param)) {
      $is_submit_form = true;
    }

    //经纪人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $data['broker_id'] = $broker_id;

    //获取求购信息基本配置资料
    $conf_customer = $this->buy_customer_model->get_base_conf();
    $data['conf_customer'] = $conf_customer;

    //区属板块信息
    $this->load->model('district_model');

    //区属数据
    $arr_district = $this->district_model->get_district();
    $district_num = count($arr_district);
    for ($i = 0; $i < $district_num; $i++) {
      $temp_dist_arr[$arr_district[$i]['id']] = $arr_district[$i];
    }

    $data['district_arr'] = $temp_dist_arr;

    //板块数据
    $arr_street = $this->district_model->get_street();
    $street_num = count($arr_street);
    for ($i = 0; $i < $street_num; $i++) {
      $temp_street_arr[$arr_street[$i]['id']] = $arr_street[$i];
    }
    $data['street_arr'] = $temp_street_arr;

    //查询条件
    $cond_where = '';

    if ($is_submit_form) {
      $customer_manage_pub = array(
        'dist_id' => $post_param['dist_id'],
        'street_id' => $post_param['street_id'],
        'cmt_name' => $post_param['cmt_name'],
        'cmt_id' => $post_param['cmt_id'],
        'area_min' => $post_param['area_min'],
        'area_max' => $post_param['area_max'],
        'price_min' => $post_param['price_min'],
        'price_max' => $post_param['price_max'],
        'property_type' => $post_param['property_type'],
        'room' => $post_param['room'],
        'public_type' => $post_param['public_type'],
        'page' => $post_param['page']
      );
      setcookie('customer_manage_pub', serialize($customer_manage_pub), time() + 3600 * 24 * 7, '/');
    } else {
      $customer_manage_pub_search = unserialize($_COOKIE['customer_manage_pub']);
      if (is_full_array($customer_manage_pub_search)) {
        $post_param['dist_id'] = $customer_manage_pub_search['dist_id'];
        $post_param['street_id'] = $customer_manage_pub_search['street_id'];
        $post_param['cmt_name'] = $customer_manage_pub_search['cmt_name'];
        $post_param['cmt_id'] = $customer_manage_pub_search['cmt_id'];
        $post_param['area_min'] = $customer_manage_pub_search['area_min'];
        $post_param['area_max'] = $customer_manage_pub_search['area_max'];
        $post_param['price_min'] = $customer_manage_pub_search['price_min'];
        $post_param['price_max'] = $customer_manage_pub_search['price_max'];
        $post_param['property_type'] = $customer_manage_pub_search['property_type'];
        $post_param['room'] = $customer_manage_pub_search['room'];
        $post_param['public_type'] = $customer_manage_pub_search['public_type'];
        $post_param['page'] = $customer_manage_pub_search['page'];
      }
    }
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    //分页每页限制数(特定房源 合作 群发 采集 列表页需求)
    if ($post_param['limit_page']) {
      setcookie('limit_page', $post_param['limit_page'], time() + 3600 * 24 * 7, '/');
      $limit_page = $post_param['limit_page'];
    } elseif ($_COOKIE['limit_page']) {
      $limit_page = $_COOKIE['limit_page'];
    } else {
      $limit_page = $this->_limit;
    }
    $this->_init_pagination($page, $limit_page);

    $dist_id = intval($post_param['dist_id']);
    $street_id = intval($post_param['street_id']);

    if ($dist_id > 0) {
      $select_info['street_info'] = $this->district_model->get_street_bydist($dist_id);
      $data['select_info'] = $select_info;
    }

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;
    $pub_cond = "is_share = 1 AND status = 1";
    $cond_where .= !empty($cond_where) ? ' AND ' . $pub_cond : $pub_cond;

    //排序字段
    $customer_order = isset($post_param['customer_order']) ? $post_param['customer_order'] : array();
    $order_arr = $this->_get_orderby_arr($customer_order);

    //符合条件的总行数
    $this->_total_count = $this->buy_customer_model->get_buynum_by_cond($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    if ($post_param['page'] > $pages) {
      $this->_init_pagination($pages, $limit_page);
    }

    //获取列表内容
    $customer_list =
      $this->buy_customer_model->get_buylist_by_cond($cond_where, $this->_offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    $type = 'buy_customer';
    $this->load->model('customer_collect_model');

    //根据经纪人编号获取已收藏客源数据
    $collected_ids = array();
    $collected_ids_temp = $this->customer_collect_model->get_collect_ids_by_bid($broker_id, $type);

    $collect_num = count($collected_ids_temp);
    for ($i = 0; $i < $collect_num; $i++) {
      $collected_ids[$i] = $collected_ids_temp[$i]['customer_id'];
    }

    $data['collected_ids'] = $collected_ids;

    //循环获取经纪人姓名和门店信息
    if (count($customer_list) > 0) {
      $broker_id_arr = array();//经纪人帐号
      foreach ($customer_list as $key => $value) {
        $broker_id_gj = intval($value['broker_id']);
        if ($broker_id_gj > 0 && !in_array($broker_id_gj, $broker_id_arr)) {
          array_push($broker_id_arr, $broker_id_gj);
        }

        $cid_arr[] = $value['id'];
      }

      //检查是否已经申请过客源合作
      $this->load->model('cooperate_model');
      $data['check_coop_reulst'] = $this->cooperate_model->check_is_cooped_by_cid($cid_arr, 'sell', $broker_id);

      //经纪人MODEL
      $this->load->model('api_broker_model');
      $this->load->model('api_broker_sincere_model');
      $broker_num = count($broker_id_arr);
      //合作成功率MODEL
      $this->load->model('cooperate_suc_ratio_base_model');
      for ($i = 0; $i < $broker_num; $i++) {
        $broker_arr = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id_arr[$i]);
        $customer_broker_info[$broker_id_arr[$i]] = $broker_arr;

        //获取经纪人好评率
        $appraise_count = array();
        $appraise_count = $this->api_broker_sincere_model->get_trust_appraise_count($broker_id_arr[$i]);
        $customer_broker_info[$broker_id_arr[$i]]['good_rate'] = !empty($appraise_count) ? $appraise_count['good_rate'] : 0;

        //经济人合作成功率
        $cop_succ_ratio_info = array();
        $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id_arr[$i]);
        $customer_broker_info[$broker_id_arr[$i]]['cop_succ_ratio_info'] = !empty($cop_succ_ratio_info) ? $cop_succ_ratio_info : array();
      }

      $data['customer_broker_info'] = $customer_broker_info;
    }

    $data['customer_list'] = $customer_list;
    //表单数据
    $data['post_param'] = $post_param;

    //获取是否举报过
    $follow_house_id = array();
    $this->load->model('report_model');
    $follow_where = "type IN (2,3,4) ";
    $follow_where .= " AND broker_id = '$broker_id'";
    $follow_where .= " AND style = 3 ";
    $follow_house = $this->report_model->get_report_house_bid($follow_where);

    foreach ($follow_house as $key => $val) {
      $follow_house_id[] = $val['number'];
    }

    $follow_number = array_count_values($follow_house_id);
    $follow_house_num = array();

    foreach ($follow_number as $key => $val) {
      if ($val == 3) {
        $follow_house_num[] = $key;
      }
    }

    $data['follow_house_num'] = $follow_house_num;

    //菜单
    $data['user_menu'] = $this->user_menu;

    //三级功能菜单
    $data['user_func_menu'] = $this->user_func_menu;

    //底部最小化菜单
    $this->load->model('broker_info_min_log_model');
    $where_cond = array(
      'broker_id' => $broker_id
    );
    $query_result = $this->broker_info_min_log_model->get_log($where_cond);
    $buy_list_min_str = $query_result[0]['buy_customer_list_pub'];
    $buy_list_min_arr = array();
    $buy_list_min_arr2 = array();
    if (!empty($buy_list_min_str)) {
      $buy_list_min_arr = explode(',', trim($buy_list_min_str, ','));
    }
    if (is_full_array($buy_list_min_arr)) {
      foreach ($buy_list_min_arr as $k => $v) {
        $this->buy_customer_model->set_search_fields(array('dist_id1', 'street_id1', 'price_min', 'price_max'));
        $this->buy_customer_model->set_id(intval($v));
        $info = $this->buy_customer_model->get_info_by_id();
        $name = '';
        $name = $temp_dist_arr[intval($info['dist_id1'])]['district'] . '-' . $temp_street_arr[intval($info['street_id1'])]['streetname'] . '-' . intval($info['price_min']) . '-' . intval($info['price_max']) . '万';
        $buy_list_min_arr2[] = array(
          'customer_id' => $v,
          'name' => $name
        );
      }
    }
    $data['buy_list_min_arr'] = $buy_list_min_arr2;

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post/ajax
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('5');//(特定房源 合作 群发 采集 列表页需求)

    //页面标题
    $data['page_title'] = '求购公盘';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css'
      . ',mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'mls/js/v1.0/broker_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/customer_list.js,'
      . 'mls/js/v1.0/cooperate_common.js');

    //加载发布页面模板
    $this->view('customer/buy_customer_list_pub', $data);
  }


  //举报页面加载
  public function report($customer_id)
  {
    $data = array();
    $customer_id = intval($customer_id);//客源id
    $data['customer_id'] = $customer_id;

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
      . 'mls/js/v1.0/cooperate_common.js'
    );
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification.js,mls/js/v1.0/backspace.js');
    //加载发布页面模板
    $this->view('customer/customer_report', $data);
  }

  /**
   * 求购客源详情页面调用
   *
   * @access  public
   * @param   int $customer_id 客源编号
   * @param   int $is_public 是否公盘
   * @param   int $tab 显示的TAB
   * @return  void
   */
  public function customer_detail($customer_id, $is_public = 0, $tab = 1, $hide_btn = 0)
  {
    $this->details($customer_id, $is_public, $tab, $hide_btn);
  }

  /**
   * 求购客源保密信息页面调用
   *
   * @access  public
   * @param   int $customer_id 客源编号
   * @param   int $is_public 是否公盘
   * @param   int $tab 显示的TAB
   * @return  void
   */
  public function confidential_info($customer_id, $is_public = 0, $tab = 2, $hide_btn = 0)
  {
    $this->details($customer_id, $is_public, $tab, $hide_btn);
  }


  /**
   * 求购客源合作统计页面调用
   *
   * @access  public
   * @param   int $customer_id 客源编号
   * @param   int $is_public 是否公盘
   * @param   int $tab 显示的TAB
   * @return  void
   */
  public function cooperation($customer_id, $is_public = 0, $tab = 3, $hide_btn = 0)
  {

    $this->buy_customer_model->set_id($customer_id);
    $customer_info = $this->buy_customer_model->get_info_by_id();
    $this->details($customer_id, $is_public, $tab, $hide_btn);
  }


  /**
   * 求购客源详情页
   *
   * @access  public
   * @param   int $customer_id 客源编号
   * @param   int $is_public 是否公盘
   * @param   int $tab 显示的TAB
   * @param $tab int 是否隐藏按钮
   * @return  void
   */
  public function details($customer_id, $is_public = 0, $tab = 1, $hide_btn = 0)
  {
    $data['hide_btn'] = $hide_btn;
    //新权限 判断是否明文显示业主电话
    //获得当前数据所属的经纪人id和门店id
    $this->buy_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->buy_customer_model->set_id($customer_id);
    $owner_arr = $this->buy_customer_model->get_info_by_id();
//        $is_phone_per = $this->broker_permission_model->check('16',$owner_arr);
//        $data['is_phone_per'] = $is_phone_per['auth'];

    $this_broker_group_id = $this->user_arr['group_id'];
    $data['is_public'] = $is_public;
    if ('1' == $is_public) {
      //获取当前经济人所在公司的基本设置信息
      $company_basic_data = $this->company_basic_arr;
      if (is_full_array($company_basic_data)) {
        //是否开启合作中心
        $open_cooperate = $company_basic_data['open_cooperate'];
      } else {
        $open_cooperate = '';
      }
      $data['open_cooperate'] = $open_cooperate;

      //是否显示经纪人电话。如果当前经纪人未参与到该房源的合作，不显示。
      $this->load->model('cooperate_model');
      $where_cond = 'tbl = "sell" and customer_id = "' . $customer_id . '" and apply_type = 2';
      $this_customer_cooperate_num = $this->cooperate_model->get_cooperate_num_apply($this->user_arr['broker_id'], $where_cond);
      //是自己的房源，展示电话号码
      if ($owner_arr['broker_id'] == $this->user_arr['broker_id']) {
        $is_phone_show = true;
      } else {
        if (is_int($this_customer_cooperate_num) && $this_customer_cooperate_num > 0) {
          $is_phone_show = true;
        } else {
          $is_phone_show = false;
        }
      }
      //检测是否已经合作
      $data['check_coop_reulst'] = $this->cooperate_model->check_is_cooped_by_cid($customer_id, 'sell', $this->user_arr['broker_id']);
    } else {
      $is_phone_show = true;
    }
    $data['is_phone_show'] = $is_phone_show;

    //经纪人接口
    $this->load->model('api_broker_base_model');

    //登录经纪人信息
    $data['broker_id'] = $this->user_arr['broker_id'];
    $customer_id = intval($customer_id);
    $customer_info = array();

    if ($customer_id > 0) {
      //获取求购信息基本配置资料
      $conf_customer = $this->buy_customer_model->get_base_conf();

      //配置文件信息
      $data['conf_customer'] = $conf_customer;

      //区属板块信息
      $this->load->model('district_model');

      //区属数据
      $arr_district = $this->district_model->get_district();
      $district_num = count($arr_district);

      for ($i = 0; $i < $district_num; $i++) {
        $temp_dist_arr[$arr_district[$i]['id']] = $arr_district[$i];
      }
      $data['district_arr'] = $temp_dist_arr;

      //板块数据
      $arr_street = $this->district_model->get_street();
      $street_num = count($arr_street);
      for ($i = 0; $i < $street_num; $i++) {
        $temp_street_arr[$arr_street[$i]['id']] = $arr_street[$i];
      }
      $data['street_arr'] = $temp_street_arr;

      $this->buy_customer_model->set_search_fields(array());
      $this->buy_customer_model->set_id($customer_id);
      $customer_info = $this->buy_customer_model->get_info_by_id();
      //获取门店所属公司名
      $company_name = '';
      if (isset($customer_info['company_id']) && !empty($customer_info['company_id'])) {
        $company_where_cond = array(
          'id' => $customer_info['company_id'],
          'company_id' => 0
        );
        $company_data = $this->agency_model->get_one_by($company_where_cond);
        if (is_full_array($company_data)) {
          $company_name = $company_data['name'];
        }
      }
      $customer_info['company_name'] = $company_name;

      //新权限
      //范围（1公司2门店3个人）
      //获得当前数据所属的经纪人id和门店id
      if (0 === $is_public) {
        $owner_arr = array('agency_id' => $customer_info['agency_id'], 'broker_id' => $customer_info['broker_id'], 'company_id' => $customer_info['company_id']);
        $view_other_per = $this->broker_permission_model->check('26', $owner_arr);
          if (!$view_other_per['auth'] || $this_broker_group_id != '2') {
          $this->redirect_permission_none_iframe('js_pop_box_g');
          exit();
        }
      }

      //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
      $data['per_public_type'] = 1;
      $role_level = intval($this->user_arr['role_level']);
      //店长以下的经纪人不允许操作他人的私盘
      if (is_int($role_level) && $role_level > 6) {
        if ($owner_arr['broker_id'] != $this->user_arr['broker_id'] && $customer_info['public_type'] == '1') {
          //跳转到列表页
          $data['per_public_type'] = 0;
        }
      }

      $customer_info['telno'] = $customer_info['telno1'];
      $customer_info['telno'] .= !empty($customer_info['telno2']) ? ', ' . $customer_info['telno2'] : '';
      $customer_info['telno'] .= !empty($customer_info['telno3']) ? ', ' . $customer_info['telno3'] : '';

      //获取门店信息
      $agency_data = $this->api_broker_base_model->get_by_agency_id($customer_info['agency_id']);
      $customer_info['agency_name'] = $agency_data['name'];

      //获取委托经纪人信息
      $broker_data = $this->api_broker_base_model->get_baseinfo_by_broker_id($customer_info['broker_id']);
      $customer_info['broker_phone'] = !empty($broker_data['phone']) ? $broker_data['phone'] : '';

      $data['data_info'] = $customer_info;
      $data['pages'] = 1;

      if ($tab == 2) {
        //新权限
        //获得当前数据所属的经纪人id和门店id
        $this->buy_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'public_type'));
        $this->buy_customer_model->set_id($customer_id);
        $owner_arr = $this->buy_customer_model->get_info_by_id();
        //判断公私盘
        if ('1' == $owner_arr['public_type']) {
          $get_secret_per = $this->broker_permission_model->check('143', $owner_arr);
        } else if ('2' == $owner_arr['public_type']) {
          $get_secret_per = $this->broker_permission_model->check('141', $owner_arr);
        }
        //保密信息关联门店权限
        if ('1' == $owner_arr['public_type']) {
          $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '42');
        } else if ('2' == $owner_arr['public_type']) {
          $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '40');
        }
        $modify_secret_per = 1;
          if (!$get_secret_per['auth'] || $this_broker_group_id != '2') {
          $modify_secret_per = 0;
        } else {
              if (!$agency_secret_per || $this_broker_group_id != '2') {
            $modify_secret_per = 0;
          }
        }
        $data['modify_secret_per'] = $modify_secret_per;

        //加载客源加密信息浏览日志MODEL
        $this->load->model('customer_brower_model');
        $data['where_cond'] = array('customer_id' => $customer_id);
        $today_browertime = array(strtotime(date('Y-m-d')), strtotime(date('Y-m-d', strtotime('+1 day'))));//今天的时间戳范围
        $data['today_brower_all_num'] = $this->customer_brower_model->get_today_brower_log_num($customer_id, 0, $today_browertime);//今日查阅总次数
        //分组字段
        $group_by = 'broker_id';
        //分页开始
        $data['user_num'] = $this->customer_brower_model->get_brower_log_num($data['where_cond']);//浏览总数
        $data['group_by_num'] = $this->customer_brower_model->get_brower_log_group_num($customer_id);//分组总数
        $data['pagesize'] = 4; //设定每一页显示的记录数
        $data['pages'] = $data['group_by_num'] ? ceil($data['group_by_num'] / $data['pagesize']) : 1;  //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

        //排序字段
        $order_by_array = array('browertime', 'desc');
        //客源浏览日志数据
        $brower_list = $this->customer_brower_model->get_brower_log($data['where_cond'], $data['offset'], $data['pagesize'], $order_by_array, $group_by);
        $brower_list2 = array();

        foreach ($brower_list as $k => $v) {
          $where = array('customer_id' => $customer_id, 'broker_id' => $v['broker_id']);
          $v['brower_num'] = $this->customer_brower_model->get_brower_log_num($where);//总查阅次数
          $v['today_brower_num'] = $this->customer_brower_model->get_today_brower_log_num($customer_id, $v['broker_id'], $today_browertime);//今日查阅次数
          $first_brower = $this->customer_brower_model->get_brower_log($where, 0, 0, array('browertime', 'asc'));//初次浏览记录
          $recent_brower = $this->customer_brower_model->get_brower_log($where, 0, 0, array('browertime', 'desc'));//最近浏览记录
          $v['first_brower'] = $first_brower[0]['browertime'];
          $v['recent_brower'] = $recent_brower[0]['browertime'];
          $brower_list2[] = $v;
        }
        $data['brower_list2'] = $brower_list2;
      } else if ($tab == 3) {
        //客源访问日志信息
        $type = 'buy_customer';
        $this->load->model('view_log_model');
        $cond_where = "c_id = '" . $customer_id . "'";
        $this->_total_count = $this->view_log_model->get_view_log_num_by_cid($type, $customer_id);

        //分页开始
        $data['log_num'] = $this->view_log_model->get_view_log_num_by_cid($type, $customer_id);//浏览总数
        $data['pagesize'] = 2; //设定每一页显示的记录数
        $data['pages'] = $data['log_num'] ? ceil($data['log_num'] / $data['pagesize']) : 0;  //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

        $view_log_list = $this->view_log_model->get_view_log_list_by_cid($type, $customer_id, $data['offset'], $data['pagesize']);
        $data['view_num'] = 0;
        $data['view_people'] = 0;
        if (is_array($view_log_list) && !empty($view_log_list)) {
          //查看总人数
          $data['view_people'] = count($view_log_list);

          //查看总次数
          for ($i = 0; $i < $data['view_people']; $i++) {
            $data['view_num'] += $view_log_list[$i]['num'];
          }
        }
        $data['view_log_list'] = $view_log_list;

        //客源合作日志
        $this->load->model('cooperate_model');

        //经纪人接口
        $this->load->model('api_broker_base_model');

        //分页开始
        $data['cooperate_num'] = $this->cooperate_model->get_cooperate_num_by_cid($customer_id, 'sell');//浏览总数
        $data['pagesize'] = 2; //设定每一页显示的记录数
        $data['cooperate_pages'] = $data['cooperate_num'] ? ceil($data['cooperate_num'] / $data['pagesize']) : 0;  //计算总页数
        $data['cooperate_page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['cooperate_page'] = ($data['cooperate_page'] > $data['cooperate_pages'] && $data['cooperate_pages'] != 0) ? $data['cooperate_pages'] : $data['cooperate_page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['cooperate_page'] - 1);   //计算记录偏移量

        $cooperate_log_list = $this->cooperate_model->get_cooperate_lists_by_cid($customer_id, 'sell', $data['offset'], $data['pagesize']);
        $cooperate_log_list2 = array();
        foreach ($cooperate_log_list as $k => $v) {
          $broker_data = $this->api_broker_base_model->get_by_agency_id($v['agentid_a']);
          $v['agent_a_name'] = $broker_data['name'];
          $cooperate_log_list2[] = $v;
        }
        //合作记录
        $data['cooperate_log_list'] = $cooperate_log_list2;

        //合作基础配置文件
        $data['cooperate_conf'] = $this->cooperate_model->get_base_conf();
      }
    }

    $data['customer_id'] = $customer_id;
    $data['tab'] = $tab;

    //页面标题
    $data['page_title'] = '求购信息详情页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/details.js,'
      . 'mls/js/v1.0/backspace.js,'
      . 'mls/js/v1.0/customer_list.js');

    //加载详情页面模板
    if ($is_public == 1) {
      $type = 'buy_customer';
      $broker_id_v = intval($this->user_arr['broker_id']);
      if ($customer_id > 0 && is_array($customer_info) && !empty($customer_info)) {
        //记录访问日志
        $agency_id = $customer_info['agency_id'];
        $broker_id = $customer_info['broker_id'];
        $agency_id_v = intval($this->user_arr['agency_id']);
        $agency_name_v = strip_tags($this->user_arr['agency_name']);
        $broker_name_v = strip_tags($this->user_arr['truename']);
        $broker_telno_v = strip_tags($this->user_arr['phone']);
        $this->load->model('view_log_model');
        $this->view_log_model->add_customer_view_log($type, $customer_id, $agency_id, $broker_id,
          $agency_id_v, $agency_name_v, $broker_id_v, $broker_name_v, $broker_telno_v);
      }

      //根据经纪人编号获取已收藏客源数据
      $type = 'buy_customer';
      $this->load->model('customer_collect_model');
      $collected_ids = array();
      $collected_ids_temp = $this->customer_collect_model->get_collect_ids_by_bid($this->user_arr['broker_id'], $type);

      $collect_num = count($collected_ids_temp);
      for ($i = 0; $i < $collect_num; $i++) {
        $collected_ids[$i] = $collected_ids_temp[$i]['customer_id'];
      }

      $data['collected_ids'] = $collected_ids;
      $this->view('customer/buy_customer_info_pub', $data);
    } else if ($is_public == 3) {
      $this->view('customer/buy_customer_info_contract', $data);
    } else if ($is_public == 5) {
      $this->view('customer/buy_customer_info_match_pub', $data);
    } else {
      $this->view('customer/buy_customer_info', $data);
    }
  }

  /**
   * 求购客源保密信息访问记录分页请求
   *
   * @access  public
   * @param  int $customer_id 客源编号
   * @param  int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_brower_log($customer_id = '1')
  {
    //加载客源浏览日志MODEL
    $this->load->model('customer_brower_model');

    $data['where_cond'] = array('customer_id' => $customer_id);
    //分组字段
    $group_by = 'broker_id';
    //分页开始
    $data['user_num'] = $this->customer_brower_model->get_brower_log_num($data['where_cond']);
    $data['group_by_num'] = $this->customer_brower_model->get_brower_log_group_num($customer_id);
    $data['pagesize'] = 4; //设定每一页显示的记录数
    $data['pages'] = $data['group_by_num'] ? ceil($data['group_by_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    //排序字段
    $order_by_array = array('browertime', 'desc');
    //客源浏览日志数据
    $brower_list = $this->customer_brower_model->get_brower_log($data['where_cond'], $data['offset'], $data['pagesize'], $order_by_array, $group_by);
    $brower_list2 = array();
    //数据重构
    foreach ($brower_list as $k => $v) {
      if (!empty($v['browertime'])) {
        $where = array('customer_id' => $customer_id, 'broker_id' => $v['broker_id']);
        $today_browertime = array(strtotime(date('Y-m-d')), strtotime(date('Y-m-d', strtotime('+1 day'))));//今天的时间戳范围
        $v['browerdate'] = date('Y-m-d H:i:s', $v['browertime']);
        $v['brower_num'] = $this->customer_brower_model->get_brower_log_num($where);//总查阅次数
        $v['today_brower_num'] = $this->customer_brower_model->get_today_brower_log_num($customer_id, $v['broker_id'], $today_browertime);//今日查阅次数
        $first_brower = $this->customer_brower_model->get_brower_log($where, 0, 0, array('browertime', 'asc'));//初次浏览记录
        $recent_brower = $this->customer_brower_model->get_brower_log($where, 0, 0, array('browertime', 'desc'));//最近浏览记录
        $v['first_brower'] = date('Y-m-d H:i:s', $first_brower[0]['browertime']);
        $v['recent_brower'] = date('Y-m-d H:i:s', $recent_brower[0]['browertime']);
      }
      $brower_list2[] = $v;
    }
    echo json_encode($brower_list2);

  }

  /**
   * 求购客源详情访问记录分页请求
   *
   * @access  public
   * @param  int $customer_id 客源编号
   * @param  int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_customer_view_log($customer_id = '1')
  {
    //客源访问日志信息
    $type = 'buy_customer';
    $this->load->model('view_log_model');
    $cond_where = "c_id = '" . $customer_id . "'";
    $this->_total_count = $this->view_log_model->get_view_log_num_by_cid($type, $customer_id);

    //分页开始
    $data['log_num'] = $this->view_log_model->get_view_log_num_by_cid($type, $customer_id);//浏览总数
    $data['pagesize'] = 2; //设定每一页显示的记录数
    $data['pages'] = $data['log_num'] ? ceil($data['log_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $view_log_list = $this->view_log_model->get_view_log_list_by_cid($type, $customer_id, $data['offset'], $data['pagesize']);
    $view_log_list2 = array();
    foreach ($view_log_list as $k => $v) {
      $v['datetime'] = date('Y-m-d H:i:s', $v['datetime']);
      $view_log_list2[] = $v;
    }
    echo json_encode($view_log_list2);
  }

  /**
   * 客源申请合作分页请求
   *
   * @access  public
   * @param  int $customer_id 客源编号
   * @param  int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_cooperate_log($customer_id = '1')
  {
    //客源合作日志
    $this->load->model('cooperate_model');
    //分页开始
    $data['cooperate_num'] = $this->cooperate_model->get_cooperate_num_by_cid($customer_id, 'sell');//浏览总数
    $data['pagesize'] = 2; //设定每一页显示的记录数
    $data['cooperate_pages'] = $data['cooperate_num'] ? ceil($data['cooperate_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['cooperate_page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['cooperate_page'] = ($data['cooperate_page'] > $data['cooperate_pages'] && $data['cooperate_pages'] != 0) ? $data['cooperate_pages'] : $data['cooperate_page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['cooperate_page'] - 1);   //计算记录偏移量

    $cooperate_log_list = $this->cooperate_model->get_cooperate_lists_by_cid($customer_id, 'sell', $data['offset'], $data['pagesize']);
    $cooperate_log_list2 = array();
    //合作基础配置文件
    $cooperate_conf = $this->cooperate_model->get_base_conf();
    if (!empty($cooperate_log_list)) {
      $ids = array();
      foreach ($cooperate_log_list as $key => $val) {
        $ids[] = $val['agentid_b'];
      }

      $agency_name = $this->cooperate_model->get_agency_att_by_aid($ids);
      if (!empty($agency_name) && is_array($agency_name)) {
        foreach ($cooperate_log_list as $key => $val) {
          $cooperate_log_list[$key]['agency_name_b'] = $agency_name[$val['agentid_b']];
        }
      }
    }
    foreach ($cooperate_log_list as $k => $v) {
      $v['creattime'] = date('Y-m-d H:i:s', $v['creattime']);
      $v['esta'] = $cooperate_conf['esta'][$v['esta']];
      $cooperate_log_list2[] = $v;
    }
    echo json_encode($cooperate_log_list2);
  }


  /**
   * 求购匹配页面
   *
   * @access  public
   * @param  int $customer_id 客源编号
   * @param   int $from 页面来源，1不需要展示合作申请页面，2显示合作申请页面
   * @return  void
   */
  public function match($customer_id, $is_public = 0)
  {
    $customer_info = array();
    $customer_id = intval($customer_id);
    //列表页地址
    $url_manage = MLS_URL . '/customer/manage';
    $this->buy_customer_model->set_id($customer_id);
    $customer_info = $this->buy_customer_model->get_info_by_id();
    if (!is_full_array($customer_info)) {
      //跳转到列表页
      $this->jump($url_manage, '无此客源信息');
    }

    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //当前帐号编号
    $data['broker_id'] = $this->user_arr['broker_id'];
    if ($customer_id > 0) {
      //获取求购信息基本配置资料
      $conf_customer = $this->buy_customer_model->get_base_conf();
      //区属板块信息
      $this->load->model('district_model');

      //区属数据
      $arr_district = $this->district_model->get_district();
      //print_r($arr_district);
      $district_num = count($arr_district);
      $customer_district_arr = array($customer_info['dist_id1'],
        $customer_info['dist_id2'], $customer_info['dist_id3']);

      $select_dist_arr = array();
      $temp_dist_arr = array();
      for ($i = 0; $i < $district_num; $i++) {
        if (in_array($arr_district[$i]['id'], $customer_district_arr)) {
          $select_dist_arr[$arr_district[$i]['id']] = $arr_district[$i];
        }

        $temp_dist_arr[$arr_district[$i]['id']] = $arr_district[$i];
      }

      $data['district_select_arr'] = $select_dist_arr;
      $data['district_arr'] = $temp_dist_arr;

      $dist_id = intval($post_param['dist_id']);
      $street_id = intval($post_param['street_id']);

      if ($dist_id > 0 && $street_id > 0) {
        $select_info['street_info'] = $this->district_model->get_street_bydist($dist_id);
        $data['select_info'] = $select_info;
      }

      //板块数据
      $arr_street = $this->district_model->get_street();
      $street_num = count($arr_street);
      for ($i = 0; $i < $street_num; $i++) {
        $temp_street_arr[$arr_street[$i]['id']] = $arr_street[$i];
      }
      $data['street_arr'] = $temp_street_arr;

      //配置文件信息
      $data['conf_customer'] = $conf_customer;
      $data['is_public'] = $is_public;
      if ($is_public) {
        foreach ($data['conf_customer']['match_range'] as $key => $value) {
          if ($key == 2)
            unset($data['conf_customer']['match_range'][$key]);
        }
      }

      //经纪人信息
      $this->load->model('api_broker_model');
      $customer_broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($customer_info['broker_id']);
    }

    $data['data_info'] = $customer_info;

    //价格区间
    if (empty($post_param['price_min'])) {
      $post_param['price_min'] = $customer_info['price_min'];
    }
    if (empty($post_param['price_max'])) {
      $post_param['price_max'] = $customer_info['price_max'];
    }
    //物业类型
    if (empty($post_param['property_type'])) {
      $post_param['property_type'] = $customer_info['property_type'];
    }
    //区属
    if (empty($post_param['dist_id'])) {
      $post_param['dist_id'] = $customer_district_arr;
    }
    //面积条件
    if (empty($post_param['area_min'])) {
      $post_param['area_min'] = $customer_info['area_min'];
    }
    if (empty($post_param['area_max'])) {
      $post_param['area_max'] = $customer_info['area_max'];
    }
    //时间条件
    if (empty($post_param['match_time'])) {
      $post_param['match_time'] = 3;
    }
    //户型条件（只匹配几室）
    if (empty($post_param['room_min'])) {
      $post_param['room_min'] = $customer_info['room_min'];
    }
    if (empty($post_param['room_max'])) {
      $post_param['room_max'] = $customer_info['room_max'];
    }
    // 范围
    if (empty($post_param['match_range'])) {
      $post_param['match_range'] = 1;
    }

    $data['post_param'] = $post_param;

    $data['customer_broker_info'] = $customer_broker_info;

    //根据条件搜索房源
    $this->load->model('sell_house_model');

    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_init_pagination($page, 5);

    //表单条件查询范围
    $cond_where = $this->get_house_range($post_param['match_range']);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_house_cond_str($post_param);
    $cond_where .= $cond_where_ext;
    if ($is_public) {
      $cond_where .= ' and isshare = 1';
    }
    //TAB标签上根据条件查询个数
    $data['tab_num'] = array();
    if (is_array($conf_customer['match_range']) && !empty($conf_customer['match_range'])) {
      foreach ($conf_customer['match_range'] as $key => $value) {
        $cond_where_tab = '';
        $cond_where_tab = $this->get_house_range($key);
        if (1 == $is_public) {
          $cond_where_tab .= ' and isshare = 1';
        }
        $cond_where_tab .= $cond_where_ext;

        //符合条件的总行数
        $data['tab_num'][$key] = $this->sell_house_model->get_count_by_cond($cond_where_tab);
      }
    }

    //符合条件的总行数
    $this->_total_count = !empty($data['tab_num']) ? $data['tab_num'][$post_param['match_range']] : 0;

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $house_list =
      $this->sell_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, 'updatetime', 'DESC');
    $data['house_list'] = $house_list;
    $data['total_count'] = $this->_total_count;

    //循环获取经纪人姓名和门店信息
    if (count($house_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();
      foreach ($house_list as $key => $value) {
        $broker_id = intval($value['broker_id']);
        if ($broker_id > 0 && !in_array($broker_id, $broker_id_arr)) {
          array_push($broker_id_arr, $broker_id);
        }
      }

      //经纪人MODEL
      $this->load->model('api_broker_model');
      $broker_num = count($broker_id_arr);
      for ($i = 0; $i < $broker_num; $i++) {
        $broker_arr = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id_arr[$i]);
        $house_broker_info[$broker_id_arr[$i]] = $broker_arr;
      }

      $data['house_broker_info'] = $house_broker_info;
    }

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post/ajax
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );

    //加载分页类
    $this->load->library('page_list', $params);

    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '求购匹配';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/customer_list.js');

    //加载匹配页面模板
    $this->view('customer/buy_customer_match', $data);
  }


  /**
   * 求购详情保密信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function get_secret_info()
  {
    $customer_info = array();
    $customer_id = intval($this->input->get('customer_id'));
    //列表页地址
    $url_manage = MLS_URL . '/customer/manage';
    $this->buy_customer_model->set_id($customer_id);
    $customer_info = $this->buy_customer_model->get_info_by_id();
    if (!is_full_array($customer_info)) {
      //跳转到列表页
      $this->jump($url_manage, '无此客源信息');
    }

    //录入经纪人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $this_broker_group_id = $this->user_arr['group_id'];

    //新权限
    //获得当前数据所属的经纪人id和门店id
    $this->buy_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'public_type'));
    $this->buy_customer_model->set_id($customer_id);
    $owner_arr = $this->buy_customer_model->get_info_by_id();
    //判断公私盘
    if ('1' == $owner_arr['public_type']) {
      $get_secret_per = $this->broker_permission_model->check('142', $owner_arr);
    } else if ('2' == $owner_arr['public_type']) {
      $get_secret_per = $this->broker_permission_model->check('140', $owner_arr);
    }
    //保密信息关联门店权限
    if ('1' == $owner_arr['public_type']) {
      $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '41');
    } else if ('2' == $owner_arr['public_type']) {
      $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '39');
    }
      if (!$get_secret_per['auth'] || $this_broker_group_id != '2') {
      $this->redirect_permission_none_iframe();
      exit();
    } else {
          if (!$agency_secret_per || $this_broker_group_id != '2') {
        $this->redirect_permission_none_iframe();
        exit();
      }
    }

    if ($customer_id > 0) {
      //获取求购信息基本配置资料
      $conf_customer = $this->buy_customer_model->get_base_conf();
      //判断是否锁定，有无权限查看（锁定状态下，发布人和锁定人可以查看）
      if (!empty($customer_info) && ($customer_info['lock'] == 0 || in_array($broker_id, array($customer_info['broker_id'], $customer_info['lock'])))) {
        $customer_info['telno'] = $customer_info['telno1'];
        $customer_info['telno'] .= !empty($customer_info['telno2']) ? ', ' . $customer_info['telno2'] : '';
        $customer_info['telno'] .= !empty($customer_info['telno3']) ? ', ' . $customer_info['telno3'] : '';
        $customer_info['job_type_str'] = !empty($customer_info['job_type']) ? $conf_customer['job_type'][$customer_info['job_type']] : '无';//客源类型
        $customer_info['user_level_str'] = !empty($customer_info['user_level']) ? $conf_customer['user_level'][$customer_info['user_level']] : '无';//客源等级
        $customer_info['age_group_str'] = !empty($customer_info['age_group']) ? $conf_customer['age_group'][$customer_info['age_group']] : '无';//年龄
        $this->info_count($customer_id, 8);//记录查看保密信息的记录

        //操作日志
        $add_log_param = array();
        $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['agency_id'];
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $this->user_arr['truename'];
        $add_log_param['type'] = 47;
        $add_log_param['text'] = '求购客源 ' . 'QG' . $customer_id;
        $add_log_param['from_system'] = 1;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = '127.0.0.1';
        $add_log_param['from_host_name'] = '127.0.0.1';
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();
        $this->operate_log_model->add_operate_log($add_log_param);
      }
    }
    echo json_encode($customer_info);
  }

  /**
   * 验证客源在本公司是唯一的
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function check_unique_customer()
  {
    //根据基本设置，判断客源是否去重
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $buy_customer_unique = intval($company_basic_data['buy_customer_unique']);
    } else {
      $buy_customer_unique = 0;
    }

    $result = array();

    if (isset($buy_customer_unique) && 1 == $buy_customer_unique) {

      $customer_num = 0;

      $truename = strip_tags($this->input->get('truename', TRUE));
      $telno1 = strip_tags($this->input->get('telno1', TRUE));
      $telno2 = strip_tags($this->input->get('telno2', TRUE));
      $telno3 = strip_tags($this->input->get('telno3', TRUE));
      $customer_id = intval($this->input->get('customer_id', TRUE));

      //符合条件个数
      $customer_num = $this->_get_customer_num_by_telno($telno1, $telno2, $telno3, $customer_id);
      $result['msg'] = $customer_num > 0 ? 'failed' : 'success';
    } else {
      $result['msg'] = 'success';
    }

    echo json_encode($result);
  }

  //判断多个房源是否存在
  public function check_is_exist_house_str()
  {
    $customer_id_str = $this->input->get('customer_id_str', TRUE);
    $result = array(
      'msg' => '',
      'exist_ids' => ''
    );
    $exist_id_arr = array();
    $customer_id_arr = explode('_', $customer_id_str);
    if (is_array($customer_id_arr) && !empty($customer_id_arr)) {
      //筛选出没有被删除的客源id
      foreach ($customer_id_arr as $k => $v) {
        $this->buy_customer_model->set_id($v);
        $customer_info = $this->buy_customer_model->get_info_by_id();
        if (isset($customer_info['status']) && $customer_info['status'] != '5') {
          $exist_id_arr[] = $v;
        }
      }
    }
    if (is_array($exist_id_arr) && !empty($exist_id_arr)) {
      $result['msg'] = 'success';
      $result['exist_ids'] = implode('_', $exist_id_arr);
      $result['exist_ids2'] = implode(',', $exist_id_arr);
    }
    echo json_encode($result);
  }

  //合作房客源数据是否合格（未删除、有效、合作）
  public function check_is_qualified_house()
  {
    $customer_id = $this->input->get('customer_id', TRUE);
    $customer_id = intval($customer_id);
    $result = array();
    if (is_int($customer_id) && !empty($customer_id)) {
      $this->buy_customer_model->set_id($customer_id);
      $customer_detail = $this->buy_customer_model->get_info_by_id();
      if (is_array($customer_detail) && !empty($customer_detail)) {
        if ($customer_detail['is_share'] === '1' && $customer_detail['status'] === '1') {
          $result['msg'] = 'success';
        }
      }
    }
    echo json_encode($result);
  }

  //判断输入号码是否为黑名单
  public function check_blacklist()
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //录入数据，是否黑名单校验
    $is_blacklist_check = $company_basic_data['is_blacklist_check'];

    $result = array();
    $telno = $this->input->get('telno', TRUE);
    $int_telno = trim($telno);
    $this->load->model('blacklist_model');
    $where_sql = ' where tel = "' . $int_telno . '"';
    $result_arr = $this->blacklist_model->get_all_by($where_sql);
    if ('1' == $is_blacklist_check && count($result_arr) > 0) {
      $result['msg'] = 'success';
    } else {
      $result['msg'] = 'failed';
    }
    echo json_encode($result);
  }


  /**
   * 根据业主姓名和电话号码获取客源个数
   *
   * @access  public
   * @param  string $truename 姓名
   * @param  string $telno1 电话号码
   * @param  string $telno2 电话号码
   * @param  string $telno3 电话号码
   * @param   int $cid 客源编号
   * @return  int 客源条数
   */
  private function _get_customer_num_by_truename_telno($truename, $telno1, $telno2 = '', $telno3 = '', $cid = 0)
  {
    $customer_num = 0;

    if (!empty($truename) && !empty($telno1)) {
      $cond_telno_str = "'" . $telno1 . "'";
      $cond_telno_str .= isset($telno2) && $telno2 != '' ? ",'" . $telno2 . "'" : '';
      $cond_telno_str .= isset($telno3) && $telno3 != '' ? ",'" . $telno3 . "'" : '';
      $cid = intval($cid);
      //经纪人信息
      $broker_id = intval($this->user_arr['broker_id']);
      $agency_id = intval($this->user_arr['agency_id']);

      if ($cid > 0) {
        $cond_where = "agency_id = '" . $agency_id . "' AND truename = '" . $truename . "' AND id != '" . $cid . "'";
      } else {
        $cond_where = "agency_id = '" . $agency_id . "' AND truename = '" . $truename . "'";
      }

      $cond_where .= " AND ( telno1 IN ($cond_telno_str) OR telno2 IN ($cond_telno_str) OR telno3 IN ($cond_telno_str)) ";
      $customer_num = $this->buy_customer_model->get_buynum_by_cond($cond_where);
    }

    return $customer_num;
  }

  /**
   * 根据业主姓名和电话号码获取客源个数
   *
   * @access  public
   * @param  string $telno1 电话号码
   * @param  string $telno2 电话号码
   * @param  string $telno3 电话号码
   * @param   int $cid 客源编号
   * @return  int 客源条数
   */
  private function _get_customer_num_by_telno($telno1, $telno2 = '', $telno3 = '', $cid = 0)
  {
    //经纪人信息
    $broker_info = $this->user_arr;
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $agency_id = intval($broker_info['agency_id']);//门店编号
    //判断经纪人当前门店类型，直营or加盟
    $this->agency_model->set_select_fields(array('id', 'agency_type'));
    $this_agency_data = $this->agency_model->get_by_id($agency_id);
    if (is_full_array($this_agency_data)) {
      $agency_type = $this_agency_data['agency_type'];
    }
    //加盟店，去重范围只在自己门店。
    if (isset($agency_type) && '2' == $agency_type) {
      $agency_ids = $agency_id;
      //直营店，去重范围，当前公司下的所有直营店。
    } else {
      //获取当前公司下的所有直营店
      $agency_type_1_list = $this->api_broker_model->get_type_1_agencys_by_company_id($company_id);
      if (is_full_array($agency_type_1_list)) {
        $arr_agency_id = array();
        foreach ($agency_type_1_list as $key => $val) {
          $arr_agency_id[] = $val['agency_id'];
        }
        $agency_ids = implode(',', $arr_agency_id);
      } else {
        $agency_ids = $agency_id;
      }
    }

    $customer_num = 0;

    if (!empty($telno1)) {
      $cond_telno_str = "'" . $telno1 . "'";
      $cond_telno_str .= isset($telno2) && $telno2 != '' ? ",'" . $telno2 . "'" : '';
      $cond_telno_str .= isset($telno3) && $telno3 != '' ? ",'" . $telno3 . "'" : '';
      $cid = intval($cid);

      if ($cid > 0) {
        $cond_where = "id > 0 AND id != '" . $cid . "'";
      } else {
        $cond_where = "id > 0 ";
      }

      $cond_where .= " AND ( telno1 IN ($cond_telno_str) OR telno2 IN ($cond_telno_str) OR telno3 IN ($cond_telno_str)) ";
      if (!empty($agency_ids)) {
        $cond_where .= " AND agency_id IN ($agency_ids)  ";
      }
      $customer_num = $this->buy_customer_model->get_buynum_by_cond($cond_where);
    }

    return $customer_num;
  }


  /**
   * 添加客源信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function add()
  {
    //添加客户信息
    $customer_info = array();//客源信息
    $add_result = array();//添加结果信息

    //录入经纪人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $broker_name = strip_tags($broker_info['truename']);
    $agency_id = intval($broker_info['agency_id']);
    $company_id = intval($broker_info['company_id']);

    //客源信息
    $customer_info['broker_id'] = $broker_id;
    $customer_info['broker_name'] = $broker_name;
    $customer_info['company_id'] = $company_id;
    $customer_info['agency_id'] = $agency_id;

    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $house_customer_system = intval($company_basic_data['house_customer_system']);
      $buy_customer_private_num = intval($company_basic_data['buy_customer_private_num']);
      $buy_customer_unique = intval($company_basic_data['buy_customer_unique']);
    } else {
      $house_customer_system = $buy_customer_private_num = $buy_customer_unique = 0;
    }

    $truename = $this->input->post('truename', TRUE);

    //验证真是姓名是不是符合要求
    if (preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $truename)) {
      //验证真是姓名长度是否符合要求
      if (abslength(trim($truename)) > 5) {
        $add_result = array('ret' => 0, 'msg' => '业主姓名最多5个字符');
        echo json_encode($add_result);
        exit;
      }

      $customer_info['truename'] = $truename;
    } else {
      $add_result = array('ret' => 0, 'msg' => '业主姓名必填，只能包含汉字、字母、数字');
      echo json_encode($add_result);
      exit;
    }

    $customer_info['sex'] = $this->input->post('sex', TRUE);
    $customer_info['idno'] = $this->input->post('idno', TRUE);

    //如果填写身份证，则验证身份证格式是否正确
    if ($customer_info['idno'] != '' &&
      !preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $customer_info['idno'])
    ) {
      $add_result = array('ret' => 0, 'msg' => '身份证格式不正确');
      echo json_encode($add_result);
      exit;
    }

    //用户手机号码
    $telno_arr = $this->input->post('telno', TRUE);
    $telno_num = count($telno_arr);
    for ($i = 1; $i <= $telno_num; $i++) {
      $telno = $telno_arr[$i - 1];

      if (trim($telno) == '') {
        if ($i > 1) {
          continue;
        } else {
          $add_result = array('ret' => 0, 'msg' => '手机号码不能为空');
          echo json_encode($add_result);
          exit;
        }
      }

      if (preg_match('/(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/', $telno)) {
        $customer_info['telno' . $i] = $telno;
      } else {
        $add_result = array('ret' => 0, 'msg' => '手机号码[' . $telno . ']格式不正确');
        echo json_encode($add_result);
        exit;
      }
    }

    //判断是否已经添加过
    $telno1 = isset($customer_info['telno1']) ? $customer_info['telno1'] : '';
    $telno2 = isset($customer_info['telno2']) ? $customer_info['telno3'] : '';
    $telno3 = isset($customer_info['telno2']) ? $customer_info['telno3'] : '';
    if (isset($buy_customer_unique) && 1 == $buy_customer_unique) {
      $is_added = $this->_get_customer_num_by_telno($telno1, $telno2, $telno3);
    } else {
      $is_added = 0;
    }
    if ($is_added >= 1) {
      $add_result = array('ret' => 0, 'msg' => '您的库中已有该客源，不可重复录入');
      echo json_encode($add_result);
      exit;
    }

    //地址
    $customer_info['address'] = $this->input->post('address', TRUE);
    //客源类型
    $customer_info['job_type'] = $this->input->post('job_type', TRUE);
    //客源等级
    $customer_info['user_level'] = $this->input->post('user_level', TRUE);
    //年龄
    $customer_info['age_group'] = $this->input->post('age_group', TRUE);
    //状态
    $customer_info['status'] = $this->input->post('status', TRUE);
    //状态必填验证
    if (!$customer_info['status']) {
      $add_result = array('ret' => 0, 'msg' => '客源信息状态必须选择');
      echo json_encode($add_result);
      exit;
    }

    //客源属性验证，如果公司设置无法发布私盘，则无法选择私盘
    $customer_info['public_type'] = intval($this->input->post('public_type', TRUE));
    $customer_info['is_share'] = $this->input->post('is_share', TRUE);
    if ('1' == $customer_info['is_share']) {
      $customer_info['set_share_time'] = time();
    }

    //状态必填验证
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $house_customer_system = intval($company_basic_data['house_customer_system']);
      $buy_customer_private_num = intval($company_basic_data['buy_customer_private_num']);
    } else {
      $house_customer_system = $buy_customer_private_num = 0;
    }

    //是否开启合作审核
    $check_cooperate = $company_basic_data['check_cooperate'];
    if ($check_cooperate == "1") {
      if ($customer_info['is_share'] != 0 && $customer_info['is_share'] != 2) {
        $add_result = array('ret' => 0, 'msg' => '是否合作必须选择');
        echo json_encode($add_result);
        exit;
      }
    } else {
      if ($customer_info['is_share'] != 0 && $customer_info['is_share'] != 1) {
        $add_result = array('ret' => 0, 'msg' => '是否合作必须选择');
        echo json_encode($add_result);
        exit;
      }
    }

    //物业类型
    $customer_info['property_type'] = intval($this->input->post('property_type', TRUE));
    //户型参数
    $customer_info['room_min'] = intval($this->input->post('room_min', TRUE));
    $customer_info['room_max'] = intval($this->input->post('room_max', TRUE));
    //户型验证
    if ($customer_info['property_type'] == 1 || $customer_info['property_type'] == 2) {
      if ($customer_info['room_min'] < 1 || $customer_info['room_max'] < 1) {
        $add_result = array('ret' => 0, 'msg' => '户型最小数值为整数1');
        echo json_encode($add_result);
        exit;
      }

      if ($customer_info['room_min'] > $customer_info['room_max']) {
        $add_result = array('ret' => 0, 'msg' => '户型数据异常');
        echo json_encode($add_result);
        exit;
      }
    } else {
      //不需要户型的强制转换为0，防止前台切换其它户型输入后产生脏数据
      $customer_info['room_min'] = 0;
      $customer_info['room_max'] = 0;
    }

    //价格参数
    $customer_info['price_min'] = floatval($this->input->post('price_min', TRUE));
    $customer_info['price_max'] = floatval($this->input->post('price_max', TRUE));

    //价格验证
    if ($customer_info['price_min'] < 1 || $customer_info['price_max'] < 1) {
      $add_result = array('ret' => 0, 'msg' => '价格最小数值为整数1');
      echo json_encode($add_result);
      exit;
    }

    if ($customer_info['price_min'] > $customer_info['price_max']) {
      $add_result = array('ret' => 0, 'msg' => '价格数据异常');
      echo json_encode($add_result);
      exit;
    }

    //面积参数
    $customer_info['area_min'] = floatval($this->input->post('area_min', TRUE));
    $customer_info['area_max'] = floatval($this->input->post('area_max', TRUE));

    //面积验证
    if ($customer_info['area_min'] < 1 || $customer_info['area_min'] < 1) {
      $add_result = array('ret' => 0, 'msg' => '面积最小数值为整数1');
      echo json_encode($add_result);
      exit;
    }

    if ($customer_info['area_min'] > $customer_info['area_max']) {
      $add_result = array('ret' => 0, 'msg' => '面积数据异常');
      echo json_encode($add_result);
      exit;
    }

    //楼层（非必填项）
    $customer_info['floor_min'] = intval($this->input->post('floor_min', TRUE));
    $customer_info['floor_max'] = intval($this->input->post('floor_max', TRUE));
    //楼层验证
    if ($customer_info['floor_min'] > $customer_info['floor_max']) {
      $add_result = array('ret' => 0, 'msg' => '楼层数据异常');
      echo json_encode($add_result);
      exit;
    }

    //区属板块
    $district_arr = $this->input->post('dist_id', TRUE);
    $street_arr = $this->input->post('street_id', TRUE);
    $dist_num = count($district_arr);

    //区属个数验证
    for ($i = 1; $i <= $dist_num; $i++) {
      if ($district_arr[$i - 1] > 0) {
        $customer_info['dist_id' . $i] = $district_arr[$i - 1];

      }

      if ($street_arr[$i - 1] > 0) {
        $customer_info['street_id' . $i] = $street_arr[$i - 1];
      }
    }

    //楼盘信息
    $cmt_arr = $this->input->post('cmt_id', TRUE);
    //$cmt_num = count($cmt_arr);

    //楼盘名称信息
    $cmtname_arr = $this->input->post('cmt_name', TRUE);

    //$cmt_key = 1;
    for ($i = 1; $i <= 3; $i++) {
      $cmt_id = !empty($cmt_arr[$i - 1]) ? intval($cmt_arr[$i - 1]) : 0;
      $cmt_name = !empty($cmtname_arr[$i - 1]) ? trim(strip_tags($cmtname_arr[$i - 1])) : '';
      if ($cmt_id > 0 && $cmt_name != '') {
        $customer_info['cmt_id' . $i] = $cmt_id;
        $customer_info['cmt_name' . $i] = $cmt_name;
        //$cmt_key ++;
      }
    }

    //朝向
    $customer_info['forward'] = intval($this->input->post('forward', TRUE));
    //装修
    $customer_info['fitment'] = intval($this->input->post('fitment', TRUE));
    //房龄
    $customer_info['house_age'] = intval($this->input->post('house_age', TRUE));
    //换房目的
    $customer_info['intent'] = intval($this->input->post('intent', TRUE));
    //信息来源
    $customer_info['infofrom'] = intval($this->input->post('infofrom', TRUE));
    //备注
    $customer_info['remark'] = $this->input->post('remark', TRUE);
    //期限
    $customer_info['deadline'] = intval($this->input->post('deadline', TRUE));
    //创建时间
    $customer_info['creattime'] = time();
    //更新时间
    $customer_info['updatetime'] = $customer_info['creattime'];
    //IP地址
    $customer_info['ip'] = get_ip();

    //基本设置，房客源制判断
    $house_private_check = true;
    //公盘私客制
    if (2 == $house_customer_system) {
      if ('2' == $customer_info['public_type']) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘私客制';
      }
    } else if (3 == $house_customer_system) {
      //公盘制 获得当前经纪人的私盘数量
      $private_where_cond = 'broker_id = "' . $broker_id . '"' . ' and status = 1 and public_type = 1';
      $private_num = $this->buy_customer_model->get_buynum_by_cond($private_where_cond);
      if ('1' == $customer_info['public_type'] && $private_num >= $buy_customer_private_num) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘制';
      }
    } else {
      $house_private_check = true;
    }

    //基本设置，公私制
    if (!$house_private_check) {
      $add_result = array('ret' => 0, 'msg' => $house_private_check_text);
      echo json_encode($add_result);
      exit;
    }

    $result = $this->buy_customer_model->add_buy_customer_info($customer_info);

    if ($result > 0) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $broker_info['company_id'];
      $add_log_param['agency_id'] = $broker_info['agency_id'];
      $add_log_param['broker_id'] = $broker_id;
      $add_log_param['broker_name'] = $broker_info['truename'];
      $add_log_param['type'] = 10;
      $add_log_param['text'] = '求购客源 ' . 'QG' . $result;
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);

      $msg = '客源信息发布成功！';
      //加载跟进信息model
      $this->load->model('follow_model');

      $needarr = array();
      $needarr['broker_id'] = $broker_id;//经纪人的ID
      $needarr['customer_id'] = $result;//客源的ID
      $needarr['company_id'] = $company_id;//公司di
      $needarr['agency_id'] = $agency_id;//门店id
      $needarr['type'] = 3;//值为(1.2.3.4)对应房源类型1.出售 2.出租 3.求购 4.求租

      //添加跟进信息返回 boolean 是否添加成功，TRUE-成功，FAlSE-失败
      $bool = $this->follow_model->customer_inster($needarr);
      //判断该房源是否设置了合作
      if ('1' == $customer_info['is_share'] || '2' == $customer_info['is_share']) {
        $follow_text = '';
        if ('1' == $customer_info['is_share']) {
          $follow_text = '是否合作:否>>是';
        } else if ('2' == $customer_info['is_share']) {
          $follow_text = '是否合作:否>>审核中';
        }
        $needarrt = array();
        $needarrt['broker_id'] = $broker_id;
        $needarrt['type'] = 3;
        $needarrt['agency_id'] = $agency_id;//门店ID
        $needarrt['company_id'] = $company_id;//总公司id
        $needarrt['customer_id'] = $result;
        $needarrt['text'] = $follow_text;
        $boolt = $this->follow_model->customer_inster_share($needarrt);
        if ('1' == $customer_info['is_share']) {
          //增加积分
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
          $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $result), 3);
          //判断积分是否增加成功
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $msg .= '+' . $credit_result['score'] . '积分';
          }
          //增加等级分值
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
          $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $result), 3);
          //判断等级分值是否增加成功
          if (is_full_array($level_result) && $level_result['status'] == 1) {
            $msg .= '+' . $level_result['score'] . '成长值';
          }
        }
      }
      //求购客源录入成功记录工作统计日志
      $this->info_count($result, 1);

      $add_result = array('ret' => 1, 'customer_id' => $result, 'msg' => $msg);
    } else {
      $add_result = array('ret' => 0, 'msg' => '客源信息发布失败');
    }

    echo json_encode($add_result);
  }


  /**
   * 修改客源信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function update()
  {
    //当前经纪人是否认证
    $this_broker_group_id = $this->user_arr['group_id'];
    $customer_id = intval($this->input->post('customer_id'));
    //列表页面地址
    $url_manage = MLS_URL . '/customer/manage';
    $this->buy_customer_model->set_id($customer_id);
    $customer_info = $this->buy_customer_model->get_info_by_id();

    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $house_customer_system = intval($company_basic_data['house_customer_system']);
      $buy_customer_private_num = intval($company_basic_data['buy_customer_private_num']);
      $buy_customer_unique = intval($company_basic_data['buy_customer_unique']);
    } else {
      $house_customer_system = $buy_customer_private_num = $buy_customer_unique = 0;
    }

    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $owner_arr = array(
      'broker_id' => $customer_info['broker_id'],
      'agency_id' => $customer_info['agency_id'],
      'company_id' => $customer_info['company_id']
    );
    $customer_modify_per = $this->broker_permission_model->check('17', $owner_arr);
    //修改客源关联门店权限
    $agency_customer_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '12');
      if (!$customer_modify_per['auth'] || $this_broker_group_id != '2') {
      $this->redirect_permission_none();
      exit();
    } else {
          if (!$agency_customer_modify_per || $this_broker_group_id != '2') {
        $this->redirect_permission_none();
        exit();
      }
    }
    //注销客源权限
    $customer_status_per = $this->broker_permission_model->check('134', $owner_arr);
    $status = $this->input->post('status', TRUE);
    if ('5' == $status && !$customer_status_per['auth'] && $customer_status_per != '1') {
      $this->redirect_permission_none();
      exit();
    }

    //添加客户信息
    $data = array();
    //$do_key = strip_tags($this->input->post('do_key'));
    $customer_broker_id = intval($this->input->post('customer_broker_id'));
    /*if( md5($customer_id.$customer_broker_id.'_365mls') !=  $do_key)
        {
            //体型参数异常，跳转到列表页
        }*/

    //录入经纪人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $broker_name = strip_tags($broker_info['truename']);
    $agency_id = intval($broker_info['agency_id']);
    $truename = $this->input->post('truename', TRUE);

    //验证真是姓名是不是符合要求
    if (preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $truename)) {
      //验证真是姓名长度是否符合要求
      if (abslength(trim($truename)) > 5) {
        $add_result = array('ret' => 0, 'msg' => '业主姓名最多5个字符');
        echo json_encode($add_result);
        exit;
      }

      $data['truename'] = $truename;
    } else {
      $add_result = array('ret' => 0, 'msg' => '业主姓名必填，只能包含汉字、字母、数字');
      echo json_encode($add_result);
      exit;
    }

    $data['sex'] = $this->input->post('sex', TRUE);
    $data['idno'] = $this->input->post('idno', TRUE);
    //如果填写身份证，则验证身份证格式是否正确
    if ($data['idno'] != '' &&
      !preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $data['idno'])
    ) {
      $add_result = array('ret' => 0, 'msg' => '身份证格式不正确');
      echo json_encode($add_result);
      exit;
    }
    //用户手机号码
    $telno_arr = $this->input->post('telno', TRUE);
    $telno_num = count($telno_arr);
    for ($i = 1; $i <= $telno_num; $i++) {
      $telno = $telno_arr[$i - 1];

      if (trim($telno) == '') {
        if ($i > 1) {
          $data['telno' . $i] = '';
          continue;
        } else {
          $add_result = array('ret' => 0, 'msg' => '手机号码不能为空');
          echo json_encode($add_result);
          exit;
        }
      }

      if (preg_match('/(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/', $telno)) {
        $data['telno' . $i] = $telno;
      } else {
        $add_result = array('ret' => 0, 'msg' => '手机号码[' . $telno . ']格式不正确');
        echo json_encode($add_result);
        exit;
      }
    }

    //判断是否已经添加过
    $telno1 = isset($data['telno1']) ? $data['telno1'] : '';
    $telno2 = isset($data['telno2']) ? $data['telno3'] : '';
    $telno3 = isset($data['telno2']) ? $data['telno3'] : '';
    if (isset($buy_customer_unique) && 1 == $buy_customer_unique) {
      $is_added = $this->_get_customer_num_by_telno($telno1, $telno2, $telno3, $customer_id);
    } else {
      $is_added = 0;
    }
    if ($is_added >= 1) {
      $add_result = array('ret' => 0, 'msg' => '您的库中已有该客源，不可重复录入');
      echo json_encode($add_result);
      exit;
    }

    $data['address'] = $this->input->post('address', TRUE);
    $data['job_type'] = $this->input->post('job_type', TRUE);
    $data['user_level'] = $this->input->post('user_level', TRUE);
    $data['age_group'] = $this->input->post('age_group', TRUE);
    $data['status'] = $this->input->post('status', TRUE);
    if ('1' == $data['status']) {
      $data['set_share_time'] = time();
    } else {
      $data['set_share_time'] = 0;
    }

    //状态必填验证
    if (!$data['status']) {
      $add_result = array('ret' => 0, 'msg' => '客源信息状态必须选择');
      echo json_encode($add_result);
      exit;
    }

    //客源属性验证，如果公司设置无法发布私盘，则无法选择私盘
    $data['public_type'] = intval($this->input->post('public_type', TRUE));
    $data['is_share'] = intval($this->input->post('is_share', TRUE));
    //状态必填验证
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //是否开启合作审核
    $check_cooperate = $company_basic_data['check_cooperate'];
    if ($check_cooperate == "1") {
      if ($data['is_share'] != 0 && $data['is_share'] != 2) {
        $add_result = array('ret' => 0, 'msg' => '是否合作必须选择');
        echo json_encode($add_result);
        exit;
      }
    } else {
      if ($data['is_share'] != 0 && $data['is_share'] != 1) {
        $add_result = array('ret' => 0, 'msg' => '是否合作必须选择');
        echo json_encode($add_result);
        exit;
      }
    }


    $data['room_min'] = intval($this->input->post('room_min', TRUE));
    $data['room_max'] = intval($this->input->post('room_max', TRUE));
    $data['property_type'] = intval($this->input->post('property_type', TRUE));

    //户型验证
    if ($data['property_type'] == 1 || $data['property_type'] == 2) {
      if ($data['room_min'] < 1 || $data['room_max'] < 1) {
        $add_result = array('ret' => 0, 'msg' => '户型最小数值为整数1');
        echo json_encode($add_result);
        exit;
      }

      if ($data['room_min'] > $data['room_max']) {
        $add_result = array('ret' => 0, 'msg' => '户型数据异常');
        echo json_encode($add_result);
        exit;
      }
    }

    $data['area_min'] = floatval($this->input->post('area_min', TRUE));
    $data['area_max'] = floatval($this->input->post('area_max', TRUE));
    //面积验证
    if ($data['area_min'] < 1 || $data['area_min'] < 1) {
      $add_result = array('ret' => 0, 'msg' => '面积最小数值为整数1');
      echo json_encode($add_result);
      exit;
    }

    if ($data['area_min'] > $data['area_max']) {
      $add_result = array('ret' => 0, 'msg' => '面积数据异常');
      echo json_encode($add_result);
      exit;
    }

    $data['price_min'] = floatval($this->input->post('price_min', TRUE));
    $data['price_max'] = floatval($this->input->post('price_max', TRUE));
    //价格验证
    if ($data['price_min'] < 1 || $data['price_max'] < 1) {
      $add_result = array('ret' => 0, 'msg' => '价格最小数值为整数1');
      echo json_encode($add_result);
      exit;
    }

    if ($data['price_min'] > $data['price_max']) {
      $add_result = array('ret' => 0, 'msg' => '价格数据异常');
      echo json_encode($add_result);
      exit;
    }

    //区属板块
    $district_arr = $this->input->post('dist_id', TRUE);
    $street_arr = $this->input->post('street_id', TRUE);
    $dist_num = count($district_arr);
    //区属个数验证
    for ($i = 1; $i <= $dist_num; $i++) {
      $dist_id = intval($district_arr[$i - 1]);
      $street_id = intval($street_arr[$i - 1]);
      $data['dist_id' . $i] = $dist_id > 0 ? $dist_id : 0;
      $data['street_id' . $i] = $street_id > 0 ? $street_id : 0;
    }

    //楼盘信息
    $cmt_arr = $this->input->post('cmt_id', TRUE);
    $cmt_num = count($cmt_arr);

    //楼盘名称信息
    $cmtname_arr = $this->input->post('cmt_name', TRUE);

    $cmt_key = 1;
    for ($i = 1; $i <= 3; $i++) {
      $cmt_id = !empty($cmt_arr[$i - 1]) ? intval($cmt_arr[$i - 1]) : 0;
      $cmt_name = !empty($cmtname_arr[$i - 1]) ? trim(strip_tags($cmtname_arr[$i - 1])) : '';

      $data['cmt_id' . $i] = 0;
      $data['cmt_name' . $i] = '';

      if ($cmt_id > 0 && $cmt_name != '') {
        $data['cmt_id' . $cmt_key] = $cmt_id;
        $data['cmt_name' . $cmt_key] = $cmt_name;
        $cmt_key++;
      }
    }

    $data['forward'] = intval($this->input->post('forward', TRUE));
    $data['fitment'] = intval($this->input->post('fitment', TRUE));
    $data['floor_min'] = intval($this->input->post('floor_min', TRUE));
    $data['floor_max'] = intval($this->input->post('floor_max', TRUE));

    //楼层验证
    if ($data['floor_min'] > $data['floor_max']) {
      $add_result = array('ret' => 0, 'msg' => '楼层数据异常');
      echo json_encode($add_result);
      exit;
    }

    $data['location'] = intval($this->input->post('location', TRUE));
    $data['house_type'] = intval($this->input->post('house_type', TRUE));
    $data['house_age'] = intval($this->input->post('house_age', TRUE));
    $data['payment'] = intval($this->input->post('payment', TRUE));
    $data['pay_commission'] = intval($this->input->post('pay_commission', TRUE));
    $data['intent'] = intval($this->input->post('intent', TRUE));
    $data['infofrom'] = intval($this->input->post('infofrom', TRUE));
    $data['remark'] = $this->input->post('remark', TRUE);
    $data['deadline'] = intval($this->input->post('deadline', TRUE));
    $data['updatetime'] = time();

    //旧数据
    $this->buy_customer_model->set_id($customer_id);
    $old = $this->buy_customer_model->get_info_by_id();

    //基本设置，房客源制判断
    $house_private_check = true;
    //公盘私客制
    if (2 == $house_customer_system) {
      if ('1' == $old['public_type'] && '2' == $data['public_type']) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘私客制';
      }
    } else if (3 == $house_customer_system) {
      //公盘制 获得当前经纪人的私盘数量
      $private_where_cond = 'broker_id = "' . $broker_id . '"' . ' and status = 1 and public_type = 1';
      $private_num = $this->buy_customer_model->get_buynum_by_cond($private_where_cond);
      if ('2' == $old['public_type'] && '1' == $data['public_type'] && $private_num >= $buy_customer_private_num) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘制';
      }
    } else {
      $house_private_check = true;
    }

    //基本设置，公私制
    if (!$house_private_check) {
      $add_result = array('ret' => 0, 'msg' => $house_private_check_text);
      echo json_encode($add_result);
      exit;
    }

    $cond_where = "id = '" . $customer_id . "' AND broker_id = '" . $customer_broker_id . "'";
    //意向区属板块，如果旧数据包含三个意向区属，修改时去除第二个，将第三个数据插入第二个，第三个清空。
    if ($old['dist_id3'] > 0 && $data['dist_id2'] == 0) {
      $data['dist_id2'] = $old['dist_id3'];
      $data['street_id2'] = $old['street_id3'];
      $data['dist_id3'] = 0;
      $data['street_id3'] = 0;
    }
    $result = $this->buy_customer_model->update_customerinfo_by_cond($data, $cond_where);
    $msg = '客源信息更新成功！';
    //新数据
    $this->buy_customer_model->set_id($customer_id);
    $new = $this->buy_customer_model->get_info_by_id_2();
    //发布合作房源增加积分
    if ($data['is_share'] == 1) {
      //增加积分
      if ($old['is_share'] != $data['is_share']) {
        //增加积分
        $this->load->model('api_broker_credit_model');
        $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
        $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $customer_id), 3);
        //判断积分是否增加成功
        if (is_full_array($credit_result) && $credit_result['status'] == 1) {
          $msg .= '+' . $credit_result['score'] . '积分';
        }
        //增加等级分值
        $this->load->model('api_broker_level_model');
        $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
        $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $customer_id), 3);
        //判断等级分值是否增加成功
        if (is_full_array($level_result) && $level_result['status'] == 1) {
          $msg .= '+' . $level_result['score'] . '成长值';
        }
      }
    }
    //求购客源修改跟进
    $text = $this->customer_follow_match($new, $old);
    $this->load->model('api_broker_model');
    $broker_messagin = $this->api_broker_model->get_baseinfo_by_broker_id($customer_broker_id);

    $this->load->model('follow_model');
    $needarr = array();
    $needarr['broker_id'] = $customer_broker_id;
    $needarr['customer_id'] = $customer_id;
    $needarr['company_id'] = $broker_messagin['company_id'];
    $needarr['agency_id'] = $broker_messagin['agency_id'];;
    $needarr['type'] = 3;
    $needarr['text'] = $text;
    if (!empty($text)) {
      $bool = $this->follow_model->customer_save($needarr);
      if (is_int($bool) && $bool > 0) {
        //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
        //获得基本设置房源跟进的天数
        //获取当前经济人所在公司的基本设置信息
        $this->load->model('house_customer_sub_model');
        $company_basic_data = $this->company_basic_arr;
        $customer_follow_day = intval($company_basic_data['customer_follow_spacing_time']);

        $select_arr = array('id', 'house_id', 'date');
        $this->follow_model->set_select_fields($select_arr);
        $where_cond = 'customer_id = "' . $customer_id . '" and follow_type != 1 and type = 3';
        $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
        if (count($last_follow_data) == 2) {
          $time1 = $last_follow_data[0]['date'];
          $time2 = $last_follow_data[1]['date'];
          $date1 = date('Y-m-d', strtotime($time1));
          $date2 = date('Y-m-d', strtotime($time2));
          $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
          if ($differ_day > $customer_follow_day) {
            $this->house_customer_sub_model->add_buy_customer_sub($customer_id, 1);
          } else {
            $this->house_customer_sub_model->add_buy_customer_sub($customer_id, 0);
          }
        } else {
          $this->house_customer_sub_model->add_buy_customer_sub($customer_id, 0);
        }
      }
    }

    if ($result > 0) {
      if ($text) {
        //求购客源修改成功记录工作统计日志
        $this->info_count($customer_id, 2);
      }
      $add_result = array('ret' => 1, 'msg' => $msg);

      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $broker_info['company_id'];
      $add_log_param['agency_id'] = $broker_info['agency_id'];
      $add_log_param['broker_id'] = $broker_id;
      $add_log_param['broker_name'] = $broker_info['truename'];
      $add_log_param['type'] = 11;
      $add_log_param['text'] = '求购客源 ' . 'QG' . $customer_id;
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
      $add_result = array('ret' => 0, 'msg' => '客源信息更新失败');
    }

    echo json_encode($add_result);
  }


  /* 根据删除客源信息
     * @param   string $actiontype 操作类型
     * @param   string $rowid_str 操作房源编号
     * @param   int $page 操作页数
     * @param   string $referer 操作后跳转的页面
    */
  public function del_customerinfo_by_ids()
  {
    $del_num = 0;
    $arr_id = array();
    $reslult = array('result' => 0, 'msg' => '抱歉，删除失败');
    //房源编号
    $customer_ids_str = $this->input->get('customer_ids', TRUE);
    $customer_info = $this->buy_customer_model->get_customer('id in (' . $customer_ids_str . ')');
    $this_broker_group_id = $this->user_arr['group_id'];

    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $owner_arr = array(
      'broker_id' => $customer_info[0]['broker_id'],
      'agency_id' => $customer_info[0]['agency_id'],
      'company_id' => $customer_info[0]['company_id']
    );
    $customer_modify_per = $this->broker_permission_model->check('134', $owner_arr);
    //修改客源关联门店权限
    $agency_customer_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '12');
      if (!$customer_modify_per['auth'] || $this_broker_group_id != '2') {
      $this->redirect_permission_none();
      exit();
    }

    foreach ($customer_info as $k => $v) {
      $arr_id[$k] = $v['id'];
    }

    if (is_full_array($arr_id)) {
      $del_num = $this->_del_customer($arr_id);
      if ($del_num > 0) {
        //操作日志
        $add_log_param = array();
        $customer_info = $this->buy_customer_model->get_customer('id in (' . $customer_ids_str . ')');

        $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['agency_id'];
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $this->user_arr['truename'];
        $add_log_param['type'] = 12;
        $add_log_param['text'] = '求购客源 ' . 'QG' . $customer_ids_str;
        $add_log_param['from_system'] = 1;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = '127.0.0.1';
        $add_log_param['from_host_name'] = '127.0.0.1';
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();

        $this->operate_log_model->add_operate_log($add_log_param);

        //添加跟进记录
        $old_data = array('status' => $customer_info[0]['status']);
        $new_data = array('status' => 5);
        $follow_str = $this->customer_follow_match($new_data, $old_data);
        if (!empty($follow_str)) {
          $follow_add_data = array();
          $follow_add_data['broker_id'] = $this->user_arr['broker_id'];
          $follow_add_data['type'] = 3;
          $follow_add_data['agency_id'] = $this->user_arr['agency_id'];//门店ID
          $follow_add_data['company_id'] = $this->user_arr['company_id'];//总公司id
          $follow_add_data['customer_id'] = $customer_ids_str;
          $follow_add_data['text'] = $follow_str;
          $this->load->model('follow_model');
          $this->follow_model->customer_save($follow_add_data);
        }

        $reslult = array('result' => 1, 'msg' => '已成功删除' . $del_num . '条客源');
      }
      echo json_encode($reslult);
    }
  }


  /**
   * 设置客源为合作
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function set_customer_share()
  {
    $up_num = 0;
    $arr_id = array();
    $reslult = array('result' => 0, 'msg' => '抱歉，设置合作失败');
    //客源编号
    $customer_ids_str = $this->input->get('customer_ids', TRUE);

    //判断是否有合作客源权限
    $customer_info = $this->buy_customer_model->get_customer('id in (' . $customer_ids_str . ')');
    foreach ($customer_info as $k => $v) {
      $arr_id[$k] = $v['id'];
    }
    if (is_full_array($arr_id)) {
      $up_num = $this->_change_share($arr_id, 1);
      //设置合作后，并添加积分
      $this->load->model('api_broker_credit_model');
      $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
      $total_score = 0;
      //设置合作后，并添加等级分值
      $this->load->model('api_broker_level_model');
      $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
      $total_score_level = 0;
      if (!empty($arr_id) && is_array($arr_id)) {
        foreach ($arr_id as $k => $v) {
          $credit_score = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $v), 3);
          if (isset($credit_score['score'])) {
            $total_score += $credit_score['score'];
          }
          $level_score = $this->api_broker_level_model->publish_cooperate_house(array('id' => $v), 3);
          if (isset($level_score['score'])) {
            $total_score_level += $level_score['score'];
          }
        }
      }
      if ($up_num > 0) {
        $reslult = array('result' => 1, 'msg' => '客源设置合作成功！');
        //判断积分是否增加成功
        if (is_full_array($credit_score) && $credit_score['status'] == 1) {
          $reslult['msg'] .= '+' . $total_score . '积分';
        }
        //判断成长值是否增加成功
        if (is_full_array($level_score) && $level_score['status'] == 1) {
          $reslult['msg'] .= '+' . $total_score_level . '成长值';
        }
      }
      echo json_encode($reslult);
    }
  }


  /**
   * 取消客源合作
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function cancle_customer_share()
  {
    $up_num = 0;
    $arr_id = array();
    $reslult = array('result' => 0, 'msg' => '抱歉，取消合作失败');
    //房源编号
    $customer_ids_str = $this->input->get('customer_ids', TRUE);

    //判断是否有取消合作客源权限
    $customer_info = $this->buy_customer_model->get_customer('id in (' . $customer_ids_str . ')');
    foreach ($customer_info as $k => $v) {
      $arr_id[$k] = $v['id'];
    }
    if (is_full_array($arr_id)) {
      $up_num = $this->_change_share($arr_id, 0);
      if ($up_num > 0) {
        $reslult = array('result' => 1, 'msg' => '已成功取消合作' . $up_num . '条客源');
      }
      echo json_encode($reslult);
    }
  }

  //设置合作审核
  public function set_is_share_2()
  {
    $result = array();
    $result['msg'] = 'failed';
    $customer_id = intval($this->input->get('str', TRUE));
    $flag = intval($this->input->get('flag', TRUE));
    if (!empty($customer_id) && !empty($flag)) {
      $cond_where = array('id' => $customer_id);
      $update_arr = array('is_share' => $flag);
      $update_result = $this->buy_customer_model->update_customerinfo_by_cond($update_arr, $cond_where);
      if (is_int($update_result) && $update_result > 0) {
        //添加跟进
        $this->load->model('follow_model');
        $text = '是否合作:否>>审核中';
        $broker_info = $this->user_arr;  //当前经纪人编号

        $needarr = array();
        $needarr['broker_id'] = $broker_info['broker_id'];
        $needarr['type'] = 3;
        $needarr['agency_id'] = $broker_info['agency_id'];//门店ID
        $needarr['company_id'] = $broker_info['company_id'];//总公司id
        $needarr['text'] = $text;
        $needarr['customer_id'] = $customer_id;
        $this->follow_model->customer_save($needarr);
        $result['msg'] = 'success';
      }
    }
    echo json_encode($result);
    exit;
  }


  /**
   * 设置取消合作状态
   *
   * @access  public
   * @param  mixed $customer_id 客源信息ID（整数值或者数组）
   * @param  int $is_share 0不合作，1合作
   * @return  void
   */
  private function _change_share($customer_id, $is_share)
  {
    $up_num = 0;

    if (!empty($customer_id)) {
      //获取客源原来的合作状态
      $this->buy_customer_model->set_id(intval($customer_id['0']));
      $old_data = $this->buy_customer_model->get_info_by_id();
      if (is_full_array($old_data)) {
        $old_is_share = $old_data['is_share'];
      }

      $update_arr['is_share'] = intval($is_share);
      if (1 == $update_arr['is_share']) {
        $update_arr['set_share_time'] = time();
      } else {
        $update_arr['set_share_time'] = 0;
      }

      $update_arr['updatetime'] = time();

      $up_num = $this->buy_customer_model->update_info_by_id($customer_id, $update_arr);

      if ($up_num > 0) {
        //添加跟进
        $this->load->model('follow_model');
        $text = $is_share ? '是否合作:否>>是' : '是否合作:是>>否';
        if ('2' == $old_is_share) {
          $text = '是否合作:审核中>>否';
        }
        $broker_info = $this->user_arr;  //当前经纪人编号

        $needarr = array();
        $needarr['broker_id'] = $broker_info['broker_id'];
        $needarr['type'] = 3;
        $needarr['agency_id'] = $broker_info['agency_id'];//门店ID
        $needarr['company_id'] = $broker_info['company_id'];//总公司id
        $needarr['text'] = $text;
        foreach ($customer_id as $value) {
          $needarr['customer_id'] = $value;
          $this->follow_model->customer_save($needarr);
        }
      }
    }

    return $up_num;
  }


  /**
   * 删除客源信息(更改为删除状态，并非物理删除)
   *
   * @access  public
   * @param  mixed $customer_id 客源编号
   * @return  void
   */
  private function _del_customer($customer_id)
  {
    $del_num = 0;

    if (!empty($customer_id)) {
      $update_arr['status'] = $status_arr = $this->buy_customer_model->get_status_arr();
      $up_status = isset($status_arr['delete']) ? intval($status_arr['delete']) : 0;

      if ($up_status > 0) {
        $update_arr['status'] = $up_status;
        if ($up_status === 5) {
          $update_arr['is_share'] = 0;
        }
        $del_num = $this->buy_customer_model->update_info_by_id($customer_id, $update_arr);
      }
    }

    return $del_num;
  }


  /**
   * 客源管理模块初始化客源配置信息(内部使用)
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function init_customer_conf()
  {
    echo '未开放初始化功能！';
    exit;
    //获取配置文件
    $arr_conf = $this->buy_customer_model->get_base_conf();

    //添加配置文件数据到数据库配置表
    $result = $this->buy_customer_model->add_customer_info($arr_conf);

    if ($result) {
      echo '执行完毕';
    }
  }


  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';
    //编号
    if (isset($form_param['id']) && $form_param['id'] > 0) {
      $id = intval($form_param['id']);
      $cond_where .= "id = '" . $id . "'";
    }

    //是否公共数据
    if (isset($form_param['is_public']) && $form_param['is_public'] > 0) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $is_public = intval($form_param['is_public']);
      $cond_where .= "is_public = '" . $is_public . "'";
    }

    //范围(门店)
    if (isset($form_param['agenctcode']) && $form_param['agenctcode'] > 0) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $agency_id = intval($form_param['agenctcode']);
      $cond_where .= "agency_id = '" . $agency_id . "'";
    }

    //范围（经纪人）
    if (isset($form_param['broker_id']) && $form_param['broker_id'] > 0) {
      $broker_id = intval($form_param['broker_id']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "broker_id = '" . $broker_id . "'";
    }

    //物业类型条件
    if (isset($form_param['property_type']) &&
      !empty($form_param['property_type']) && $form_param['property_type'] > 0
    ) {
      $property_type = intval($form_param['property_type']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "property_type = '" . $property_type . "'";
    }


    //状态条件
    if (isset($form_param['status']) && !empty($form_param['status']) && $form_param['status'] > 0) {
      $status = intval($form_param['status']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "status = '" . $status . "'";
    } else if ($form_param['status'] == 'test') {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "status IN (1,2,3,4,5)";
    } else {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "status = '1' ";
    }

    //公盘私盘
    if (isset($form_param['public_type']) && !empty($form_param['public_type']) && $form_param['public_type'] > 0) {
      $public_type = intval($form_param['public_type']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "public_type = '" . $public_type . "'";
    }

    //是否合作
    if (isset($form_param['is_share']) && $form_param['is_share'] != '') {
      $is_share = intval($form_param['is_share']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      if ($is_share == 1) {
        $cond_where .= "is_share = '" . $is_share . "'";
      } else {
        $cond_where .= "is_share in (0,2)";
      }
    }

    //区属、板块条件
    if (isset($form_param['dist_id']) && $form_param['dist_id'] > 0) {
      $dist_id = intval($form_param['dist_id']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "(dist_id1 = '" . $dist_id . "' "
        . " OR dist_id2 = '" . $dist_id . "'"
        . " OR dist_id3 = '" . $dist_id . "')";

      $street_id = intval($form_param['street_id']);
      if ($street_id > 0) {
        $cond_where .= " AND (street_id1 = '" . $street_id . "' "
          . " OR street_id2 = '" . $street_id . "'"
          . " OR street_id3 = '" . $street_id . "')";
      }
    }

    //楼盘参数
    if (isset($form_param["cmt_name"])) {
      $cmt_name = trim($form_param["cmt_name"]);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "(cmt_name1 like '%" . $cmt_name . "%' "
        . " OR cmt_name2 like '%" . $cmt_name . "%'"
        . " OR cmt_name3 like '%" . $cmt_name . "%')";
    }

    //户型条件
    if ((isset($form_param["room"]) && $form_param["room"] > 0)) {
      $room = floatval($form_param["room"]);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "room_min <= '" . $room . "' AND room_max >= '" . $room . "' ";
    }

    //面积条件
    if ((isset($form_param["area_min"]) && $form_param["area_min"] > 0)
      || (isset($form_param["area_max"]) && $form_param["area_max"] > 0)
    ) {
      $area_min = floatval($form_param["area_min"]);
      $area_max = floatval($form_param["area_max"]);

      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      if (($area_max > $area_min || $area_max == $area_min) && $area_min > 0) {
        $cond_where .= "((area_min >= '" . $area_min . "' AND "
          . "area_min <= '" . $area_max . "') OR ( area_max >= '" . $area_min . "' "
          . "AND area_max <= '" . $area_max . "') OR (area_max >= '" . $area_min . "' "
          . "AND area_min <= '" . $area_max . "') )";
      } else if ($area_max == 0 && $area_min > 0) {
        $cond_where .= "area_min >= '" . $area_min . "'";
      } else if ($area_min == 0 && $area_max > 0) {
        $cond_where .= "area_max <= " . $area_max . "";
      }
    }

    //价格条件
    if ((isset($form_param["price_min"]) && $form_param["price_min"] > 0)
      || (isset($form_param["price_min"]) && $form_param["price_min"] > 0)
    ) {
      $price_min = floatval($form_param["price_min"]);
      $price_max = floatval($form_param["price_max"]);

      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      if (($price_max > $price_min || $price_max == $price_min) && $price_min > 0) {
        $cond_where .= "((  price_min >= '" . $price_min . "' AND "
          . "price_min <= '" . $price_max . "') OR (  price_max >= '" . $price_min . "' "
          . "AND price_max <= '" . $price_max . "')  OR (  price_max >= '" . $price_min . "' "
          . "AND price_min <= '" . $price_max . "'))";
      } else if ($price_max == 0 && $price_min > 0) {
        $cond_where .= "price_min >= " . $price_min . "";
      } else if ($price_min == 0 && $price_max > 0) {
        $cond_where .= "price_max <= " . $price_max . "";
      }
    }
    //房源创建时间范围
    if (!empty($form_param['create_time_range'])) {
      $searchtime = intval($form_param['create_time_range']);
      $now_time = time();
      switch ($searchtime) {
        case '1':
          $creattime = $now_time - 86400 * 1;
          $cond_where .= " AND creattime >=  '" . $creattime . "' ";
          break;

        case '2':
          $creattime = $now_time - 86400 * 7;
          $cond_where .= " AND creattime >=  '" . $creattime . "' ";
          break;

        case '3':
          $creattime = $now_time - 86400 * 30;
          $cond_where .= " AND creattime >=  '" . $creattime . "' ";
          break;

        case '4':
          $creattime = $now_time - 86400 * 90;
          $cond_where .= " AND creattime >=  '" . $creattime . "' ";
          break;

        case '5':
          $creattime = $now_time - 86400 * 180;
          $cond_where .= " AND creattime >=  '" . $creattime . "' ";
          break;
        case '6':
          $creattime = $now_time - 86400 * 365;
          $cond_where .= " AND creattime >=  '" . $creattime . "' ";
          break;
        default:
      }
    }

    //客户电话
    if (isset($form_param['telno']) && $form_param['telno'] != '') {
        $cond_where .= " AND (telno1 like '%" . $form_param['telno']
            . "%' or telno2 like '%" . $form_param['telno']
            . "%' or telno3 like '%" . $form_param['telno'] . "%') ";
    }

      //客户姓名
      if (isset($form_param['truename']) && $form_param['truename'] != '') {
          $cond_where .= " AND truename like '%" . $form_param['truename'] . "%'";
      }
    return $cond_where;
  }


  /**
   * 根据范围提交参数，获取查询条件
   */
  private function get_house_range($form_param)
  {
    $this->load->model('api_broker_model');
    $broker_id = intval($this->user_arr['broker_id']);  //当前经纪人编号
    $agency_id = intval($this->user_arr['agency_id']);  //经纪人门店编号
    $company_id = intval($this->user_arr['company_id']);    //公司编号

    $cond_where = '';
    if (isset($form_param) && !empty($form_param)) {
      switch ($form_param) {
        case '1':
          //根据数据范围，获得门店数据
          $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_customer_match');
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

          //公司公盘
            //  $cond_where = "agency_id IN (" . $all_access_agency_ids . ")  AND nature = 2 AND status = 1 AND status != 5";
            $cond_where = "agency_id IN (" . $all_access_agency_ids . ") AND status = 1 AND status != 5";
          break;
        case '2':
          $cond_where = 'isshare = 1 AND status = 1 AND status != 5';   //合作楼盘
          break;
        case '3':
            //   $cond_where = "agency_id = '" . $agency_id . "' AND nature = 2 AND status = 1 AND status != 5";//所在门店
            $cond_where = "agency_id = '" . $agency_id . "' AND status = 1 AND status != 5";//所在门店
          break;
        case '4':
          $cond_where = "broker_id = '" . $broker_id . "' AND status = 1 AND status != 5";//本人
          break;
      }
    }

    return $cond_where;
  }


  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_house_cond_str($form_param)
  {
    $cond_where = '';

    //时间条件
    $match_time = intval($form_param['match_time']);
    $now_time = time();
    switch ($match_time) {
      case '1':
        $creattime = $now_time - 86400 * 30;
        break;

      case '2':
        $creattime = $now_time - 86400 * 90;
        break;

      case '3':
        $creattime = $now_time - 86400 * 180;
        break;

      case '4':
        $creattime = $now_time - 86400 * 365;
        break;

      default :
        $creattime = $now_time - 86400 * 180;
    }
    $cond_where .= " AND createtime >= '" . $creattime . "' ";

    //物业类型条件
    if (isset($form_param['property_type']) && !empty($form_param['property_type']) && $form_param['property_type'] > 0) {
      $property_type = intval($form_param['property_type']);
      $cond_where .= " AND sell_type = '" . $property_type . "' ";
    }

    //区属、板块条件
    if (isset($form_param['dist_id']) && $form_param['dist_id']) {
      $dist_id = $form_param['dist_id'];
      if (is_full_array($dist_id)) {
        $cond_where .= "and (";
        foreach ($dist_id as $k => $v) {
          if ($v['dist_id']) {
            if ($k > 0) {
              $cond_where .= " or ";
            }
            $cond_where .= "district_id = '" . $v['dist_id'] . "'";
          }
        }
        $cond_where .= ")";
      } else {
        $cond_where .= " AND district_id = '" . $dist_id . "' ";
      }
      if (isset($form_param['street_id']) && $form_param['street_id'] > 0) {
        $street_id = intval($form_param['street_id']);
        $cond_where .= " AND street_id = '" . $street_id . "' ";
      }
    }

    //楼盘参数
    if (isset($form_param["cmt_id"]) && $form_param['cmt_id'] > 0) {
      $cmt_id = intval($form_param["cmt_id"]);
      $cond_where .= " AND block_id = '" . $cmt_id . "' ";
    }

    //价格条件
    if ((isset($form_param["price_min"]) && $form_param["price_min"] > 0)
      || (isset($form_param["price_min"]) && $form_param["price_min"] > 0)
    ) {
      $price_min = floatval($form_param["price_min"]);
      $price_max = floatval($form_param["price_max"]);

      if ($price_max == 0 && $price_min > 0) {
        $cond_where .= " AND price >= '" . $price_min . "'";
      } else {
        $cond_where .= " AND price >= '" . $price_min . "' AND "
          . "price <= '" . $price_max . "'";
      }
    }

    //面积条件
    if ((isset($form_param["area_min"]) && $form_param["area_min"] > 0)
      || (isset($form_param["area_max"]) && $form_param["area_max"] > 0)
    ) {
      $area_min = floatval($form_param["area_min"]);
      $area_max = floatval($form_param["area_max"]);

      if ($area_max >= $area_min) {
        $cond_where .= " AND buildarea >= '" . $area_min . "' AND "
          . "buildarea <= '" . $area_max . "'";
      } else if ($area_max == 0 && $area_min > 0) {
        $cond_where .= " AND buildarea >= '" . $area_min . "'";
      }
    }

    //户型条件
    if ((isset($form_param["room_min"]) && $form_param["room_min"] > 0)
      || (isset($form_param["room_max"]) && $form_param["room_max"] > 0)
    ) {
      $room_min = intval($form_param["room_min"]);
      $room_max = intval($form_param["room_max"]);

      if ($room_max >= $room_min) {
        $cond_where .= " AND room >= '" . $room_min . "' AND "
          . "room <= '" . $room_max . "'";
      } else if ($room_max == 0 && $room_min > 0) {
        $cond_where .= " AND room >= '" . $room_min . "'";
      }

    }

    //楼盘名称
    if (isset($form_param["cmt_name"]) && !empty($form_param["cmt_name"])) {
      $cmt_name = trim($form_param["cmt_name"]);
      $cond_where .= " AND block_name LIKE '%" . $cmt_name . "%'";
    }

    return $cond_where;
  }


  //获取排序字符串
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
        $arr_order['order_key'] = 'buildarea';
        $arr_order['order_by'] = 'ASC';
        break;
      case 4:
        $arr_order['order_key'] = 'buildarea';
        $arr_order['order_by'] = 'DESC';
        break;
      case 5:
        $arr_order['order_key'] = 'price_max';
        $arr_order['order_by'] = 'ASC';
        break;
      case 6:
        $arr_order['order_key'] = 'price_max';
        $arr_order['order_by'] = 'DESC';
        break;
      case 7:
        $arr_order['order_key'] = 'creattime';
        $arr_order['order_by'] = 'DESC';
        break;
      default:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
    }

    return $arr_order;
  }


  /**
   * 添加求购客源浏览记录
   */
  public function add_brower_customer_log()
  {
    $return_data = array();
    $user = $this->user_arr;
    $customer_id = $this->input->get('customer_id');

    $param_list = array(
      'customer_id' => $customer_id,
      'broker_id' => $user['broker_id'],
      'broker_name' => $user['truename'],
      'agency_id' => $user['agency_id'],
      'agency_name' => $user['agency_name'],
      'ip' => get_ip(),
      'browertime' => time(),
    );

    $this->load->model('customer_brower_model');
    $result = $this->customer_brower_model->add($param_list);

    if (is_int($result) && !empty($result)) {
      $return_data['msg'] = 'add_success';
    } else {
      $return_data['msg'] = 'add_failed';
    }

    echo json_encode($return_data);
  }


  /**
   * 添加求租客源浏览记录
   */
  public function add_rent_brower_customer_log()
  {
    $return_data = array();
    $user = $this->user_arr;
    $customer_id = $this->input->get('customer_id');
    $param_list = array(
      'customer_id' => $customer_id,
      'broker_id' => $user['broker_id'],
      'broker_name' => $user['truename'],
      'agency_id' => $user['agency_id'],
      'agency_name' => $user['agency_name'],
      'ip' => get_ip(),
      'browertime' => time(),
    );

    $this->load->model('rent_customer_brower_model');
    $result = $this->rent_customer_brower_model->add($param_list);

    if (is_int($result) && !empty($result)) {
      $return_data['msg'] = 'add_success';
    } else {
      $return_data['msg'] = 'add_failed';
    }

    echo json_encode($return_data);
  }

  //获取当前经纪人房源
  public function source($page = 1)
  {
    //模板使用数据
    $data = array();
    $post_param = $this->input->post(NULL, TRUE);
    $broker_info = $this->user_arr;

    //经纪人的ID
    $broker_id = $broker_info['broker_id'];
    //加载出售房源MODEL
    $this->load->model('sell_house_model');
    // 分页参数
    $_page = $this->input->post('page', TRUE);
    $page = isset($_page) ? intval($_page) : 1;
    $this->_init_pagination($page, 5);

    //出售房源表
    $tbl = 'sell_house';
    //获取当前经纪人所在门店的数据范围
    //查询条件
    //$cond_where = "status = 1 and broker_id = '".$broker_id."'";
    $cond_where = "status = 1 ";
    $view_other_per_data = $this->broker_permission_model->check('2');
    if ($view_other_per_data) {
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_house_secret');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $broker_info['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $broker_info['agency_id'];
      }
      if (!empty($all_access_agency_ids)) {
        //查询房源条件
        $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";
      }
    } else {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //表单提交参数组成的查询条件
    if (isset($post_param['cname']) && !empty($post_param['cname'])) {
      if ($post_param['cname'] == '%') {
        $post_param['cname'] = '\%';
      }
      $data['cname'] = $post_param['cname'];
      $cond_where_ext = "AND block_name LIKE '%" . $post_param['cname'] . "%'";
      $cond_where .= $cond_where_ext;
    }
    $this->sell_house_model->set_tbl($tbl);

    //符合条件的总行数
    $this->_total_count = $this->sell_house_model->get_count_by_cond($cond_where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $list = $this->sell_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);
    $data['list'] = $list;

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    $data['page_list'] = $this->page_list->show('jump');

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/customer_list.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');
    //加载发布页面模板
    $this->view('customer/buy_my_source', $data);
  }

  public function customer_follow($customer_id, $num = 1, $task = 0)
  {
    //新权限 判断是否明文显示业主电话
    //获得当前数据所属的经纪人id和门店id
    $this->buy_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->buy_customer_model->set_id($customer_id);
    $owner_arr = $this->buy_customer_model->get_info_by_id();
    $customer_follow_per = $this->broker_permission_model->check('19', $owner_arr);
    //求购客源跟进关联门店权限
    $agency_customer_follow_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '14');
    if (!$customer_follow_per['auth']) {
      $this->redirect_permission_none_iframe('js_genjin');
      exit();
    } else {
      if (!$agency_customer_follow_per) {
        $this->redirect_permission_none_iframe('js_genjin');
        exit();
      }
    }

    $data = array();

    //$num区分三个tab页面
    $num = intval($num);
    $data['num'] = $num;
    if (1 == $num) {
      //操作日志
      $add_log_param = array();
      $this->buy_customer_model->set_search_fields(array('dist_id1', 'street_id1', 'area_min', 'area_max', 'price_min', 'price_max'));
      $this->buy_customer_model->set_id($customer_id);
      $customer_info = $this->buy_customer_model->get_info_by_id();

      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 13;
      $add_log_param['text'] = '求购客源 ' . 'QG' . $customer_id;
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);
    }

    $broker_info = $this->user_arr;

    $company_id = intval($broker_info['company_id']);

    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if ($company_basic_data['follow_text_num'] > 0) {
      $data['follow_text_num'] = $company_basic_data['follow_text_num'];
    } else {
      $data['follow_text_num'] = 10;
    }

    //房源id
    $customer_id = intval($customer_id);
    $data['customer_id'] = $customer_id;
    //房源类型
    $data['house_type'] = 1;
    //经纪人姓名
    $data['broker_name'] = $broker_info['truename'];

    //提醒明细
    if ($num == 3) {
      $where_cond = array(
        'tbl' => 3,
        'row_id' => $customer_id
      );
      $this->load->model('remind_model');
      $remind_list = $this->remind_model->get_remind_order($where_cond, 'create_time');
      $data['data_lists'] = $remind_list;
    } else {
      //获取跟进方式
      $this->load->model('follow_model');
      $type_tbl = 'follow_up';
      $this->follow_model->set_tbl($type_tbl);
      $follow_config = $this->follow_model->get_config();
      $data['follow_config'] = $follow_config['follow_way'];

      $follow_tbl = 'detailed_follow';
      $this->follow_model->set_tbl($follow_tbl);

      //跟进明细
      if ($num == 1) {
        $where_arr = "type = 3 AND customer_id = '" . $customer_id . "'";
        $where_arr .= " AND (follow_type = 2 OR follow_type = 3)";
      } else if ($num == 2) {
        $where_arr = "type = 3 AND customer_id = '" . $customer_id . "' AND follow_way = 5";
        $where_arr .= " AND (follow_type = 2 OR follow_type = 3)";
      }

      $follow_lists = $this->follow_model->get_lists($where_arr);
      //数据重构，获得跟进人
      $this->load->model('broker_info_model');
      $follow_lists2 = array();
      foreach ($follow_lists as $k => $v) {
        $broker_data = $this->broker_info_model->get_one_by(array('broker_id' => $v['broker_id']));
        $v['broker_name'] = $broker_data['truename'];
        $follow_lists2[] = $v;
      }
      $data['data_lists'] = $follow_lists2;
    }
    $data['task_id'] = $task;

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'css/v1.0/house_new.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/upload_wei.js,mls/js/v1.0/cooperate_common.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/backspace.js,'
      . 'mls/js/v1.0/house.js');
    $this->view('customer/buy_customer_follow', $data);
  }

  //添加跟进记录和提醒
  public function add_follow_remind()
  {
    $broker_info = $this->user_arr;
    $follow_arr = array();
    $follow_arr['customer_id'] = $this->input->get('customer_id', TRUE);//客源id
    $task_id = $this->input->get('task_id', TRUE);//任务id
    $status = $this->input->get('status', TRUE);//status
    $follow_arr['broker_id'] = $broker_info['broker_id'];//经纪人的ID
    $follow_arr['agency_id'] = $broker_info['agency_id'];//门店ID
    $follow_arr['company_id'] = $broker_info['company_id'];//总公司id
    $follow_arr['follow_way'] = $this->input->get('follow_type', TRUE);//跟进方式
    $follow_arr['house_id'] = $this->input->get('house_id', TRUE);//房源id
    $follow_arr['follow_type'] = $this->input->get('foll_type', TRUE);//跟进类型
    $follow_arr['text'] = $this->input->get('text', TRUE);//跟进内容
    $follow_arr['date'] = date('Y-m-d H:i:s');//跟进时间
    $follow_arr['type'] = 3;//类型

    $follow_date_info = array();
    $follow_date_info['updatetime'] = time();
    //1.房源数据更新update字段
    $where_cond = array('id' => $follow_arr['customer_id']);
    $result = $this->buy_customer_model->update_customerinfo_by_cond($follow_date_info, $where_cond);


    //2.添加房源跟进
    $this->load->model('follow_model');
    $tbl = 'detailed_follow';
    $this->follow_model->set_tbl($tbl);
    $follow_id = $this->follow_model->add_follow($follow_arr);
    if ($follow_id > 0) {
      //带看跟进，同时增加对应房源跟进
      if (5 == $follow_arr['follow_way']) {
        $house_follow_arr = array();
        $house_follow_arr['customer_id'] = $follow_arr['customer_id'];//客源id
        $house_follow_arr['broker_id'] = $broker_info['broker_id'];//经纪人的ID
        $house_follow_arr['agency_id'] = $broker_info['agency_id'];//门店ID
        $house_follow_arr['company_id'] = $broker_info['company_id'];//总公司id
        $house_follow_arr['follow_way'] = 5;//跟进方式
        $house_follow_arr['house_id'] = $this->input->get('house_id', TRUE);//房源id
        $house_follow_arr['follow_type'] = 1;//跟进类型
        $house_follow_arr['text'] = $follow_arr['text'];//跟进内容
        $house_follow_arr['date'] = $follow_arr['date'];//跟进时间
        $house_follow_arr['type'] = 1;//类型

        //1.房源数据更新update字段
        $where_cond = array('id' => $house_follow_arr['customer_id']);
        $this->load->model('sell_house_model');
        $this->sell_house_model->set_id($house_follow_arr['house_id']);
        $result = $this->sell_house_model->update_info_by_id($follow_date_info);
        //2.添加房源跟进
        $house_follow_id = $this->follow_model->add_follow($house_follow_arr);
        if ($house_follow_id > 0) {
          //操作日志
          $add_log_param = array();
          $follow_way_str = '带看跟进';
          $add_log_param['company_id'] = $this->user_arr['company_id'];
          $add_log_param['agency_id'] = $this->user_arr['agency_id'];
          $add_log_param['broker_id'] = $this->user_arr['broker_id'];
          $add_log_param['broker_name'] = $this->user_arr['truename'];
          $add_log_param['type'] = 46;
          $add_log_param['text'] = '出售房源 ' . 'CS' . $house_follow_arr['house_id'] . ' ' . $follow_way_str . ' ' . $house_follow_arr['text'];
          $add_log_param['from_system'] = 1;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();
          $this->operate_log_model->add_operate_log($add_log_param);

          //判断该房源是否是公共数据，如果是，变成非公共，重新归属经纪人
          $this->sell_house_model->set_search_fields(array('id', 'is_public'));
          $this->sell_house_model->set_id(intval($house_follow_arr['house_id']));
          $datainfo = $this->sell_house_model->get_info_by_id();
          if ('1' == $datainfo['is_public']) {
            $update_arr = array();
            $update_arr['is_public'] = 0;
            $update_arr['broker_id'] = $this->user_arr['broker_id'];
            $update_arr['agency_id'] = $this->user_arr['agency_id'];
            $update_arr['company_id'] = $this->user_arr['company_id'];
            $update_arr['broker_name'] = $this->user_arr['truename'];
            $this->sell_house_model->set_id(intval($house_follow_arr['house_id']));
            $update_result = $this->sell_house_model->update_info_by_id($update_arr);
            if ($update_result) {
              $house_follow_arr['follow_type'] = 3;
              $house_follow_arr['follow_way'] = 8;
              $house_follow_arr['text'] = '委托人从 无 >> ' . $this->user_arr['truename'];
              $this->follow_model->set_tbl($tbl);
              $this->follow_model->add_follow($house_follow_arr);
            }
          }

          //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
          //获得基本设置房源跟进的天数
          //获取当前经济人所在公司的基本设置信息
          $this->load->model('house_customer_sub_model');
          $company_basic_data = $this->company_basic_arr;
          $house_follow_day = intval($company_basic_data['house_follow_spacing_time']);

          $select_arr = array('id', 'house_id', 'date');
          $this->follow_model->set_select_fields($select_arr);
          $where_cond = 'house_id = "' . $house_follow_arr['house_id'] . '" and follow_type != 2 and type = 1';
          $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
          if (count($last_follow_data) == 2) {
            $time1 = $last_follow_data[0]['date'];
            $time2 = $last_follow_data[1]['date'];
            $date1 = date('Y-m-d', strtotime($time1));
            $date2 = date('Y-m-d', strtotime($time2));
            $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
            if ($differ_day > $house_follow_day) {
              $result = $this->house_customer_sub_model->add_sell_house_sub($house_follow_arr['house_id'], 1);
            } else {
              $result = $this->house_customer_sub_model->add_sell_house_sub($house_follow_arr['house_id'], 0);
            }
          }

        }
      }

      //操作日志
      $add_log_param = array();
      $follow_way_str = '';
      if ('3' == $follow_arr['follow_way']) {
        $follow_way_str = '电话跟进';
      } else if ('4' == $follow_arr['follow_way']) {
        $follow_way_str = '磋商跟进';
      } else if ('5' == $follow_arr['follow_way']) {
        $follow_way_str = '带看跟进';
      } else {
        $follow_way_str = '其它跟进';
      }
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 48;
      $add_log_param['text'] = '求购客源 ' . 'QG' . $follow_arr['customer_id'] . ' ' . $follow_way_str . ' ' . $follow_arr['text'];
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);

      //判断该房源是否是公共数据，如果是，变成非公共，重新归属经纪人
      $this->buy_customer_model->set_search_fields(array('id', 'is_public'));
      $this->buy_customer_model->set_id(intval($follow_arr['customer_id']));
      $datainfo = $this->buy_customer_model->get_info_by_id();
      if ('1' == $datainfo['is_public']) {
        $update_arr = array();
        $update_arr['is_public'] = 0;
        $update_arr['broker_id'] = $this->user_arr['broker_id'];
        $update_arr['agency_id'] = $this->user_arr['agency_id'];
        $update_arr['company_id'] = $this->user_arr['company_id'];
        $update_arr['broker_name'] = $this->user_arr['truename'];
        $update_result = $this->buy_customer_model->update_info_by_id($follow_arr['customer_id'], $update_arr);
        if ($update_result) {
          $follow_arr['follow_type'] = 3;
          $follow_arr['follow_way'] = 12;
          $follow_arr['text'] = '委托人从 无 >> ' . $this->user_arr['truename'];
          $this->follow_model->set_tbl($tbl);
          $this->follow_model->add_follow($follow_arr);
        }
      }

      //求购客源带看记录工作统计日志
      if ($follow_arr['follow_way'] == 5) {
        $this->info_count($follow_arr['customer_id'], 5, $follow_arr['house_id']);
      } else {
        $this->info_count($follow_arr['customer_id'], 9);
      }
      //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
      //获得基本设置房源跟进的天数
      //获取当前经济人所在公司的基本设置信息
      $this->load->model('house_customer_sub_model');
      $company_basic_data = $this->company_basic_arr;
      $customer_follow_day = intval($company_basic_data['customer_follow_spacing_time']);

      $select_arr = array('id', 'house_id', 'date');
      $this->follow_model->set_select_fields($select_arr);
      $where_cond = 'customer_id = "' . $follow_arr['customer_id'] . '" and follow_type != 1 and type = 3';
      $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
      if (count($last_follow_data) == 2) {
        $time1 = $last_follow_data[0]['date'];
        $time2 = $last_follow_data[1]['date'];
        $date1 = date('Y-m-d', strtotime($time1));
        $date2 = date('Y-m-d', strtotime($time2));
        $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
        if ($differ_day > $customer_follow_day) {
          $result = $this->house_customer_sub_model->add_buy_customer_sub($follow_arr['customer_id'], 1);
        } else {
          $result = $this->house_customer_sub_model->add_buy_customer_sub($follow_arr['customer_id'], 0);
        }
      } else {
        $result = $this->house_customer_sub_model->add_buy_customer_sub($follow_arr['customer_id'], 0);
      }
      if ($task_id) {
        $this->load->model('task_model');
        $this->task_model->update_by_id(array('start_date' => time(), 'status' => $status), $task_id);
      }
      echo json_encode(array('result'=>'success'));
    } else {
      echo json_encode(array('result'=>'failed'));
    }


    //2.添加事件提醒
    $ti_arr = array();
    $ti_arr['title'] = '客源跟进';
    $ti_arr['contents'] = $this->input->get('ti_text', TRUE);
    $ti_arr['agency_id'] = $broker_info['agency_id'];
    $ti_arr['broker_id'] = $broker_info['broker_id'];
    $ti_arr['broker_name'] = $broker_info['truename'];
    $ti_arr['create_time'] = strtotime(date('Y-m-d H:i:s'));
    $ti_arr['notice_time'] = strtotime($this->input->get('ti_time', TRUE));
    $ti_arr['tbl'] = 3;
    $ti_arr['row_id'] = $this->input->get('customer_id', TRUE);
    if (!empty($follow_arr['text']) && !empty($ti_arr['contents'])) {
      $ti_arr['detail_id'] = $follow_id;
    }
    if (!empty($ti_arr['notice_time']) && !empty($ti_arr['contents'])) {
      $this->load->model('remind_model');
      //事件提醒表
      $add_result = $this->remind_model->add_remind($ti_arr);
      //事情接受者表
      $receiver_data = array();
      $receiver_data['receiver_id'] = $broker_info['broker_id'];
      $receiver_data['event_id'] = $add_result;
      $this->load->model('event_receiver_model');
      $add_result2 = $this->event_receiver_model->add_receiver($receiver_data);
    }


  }

  //添加事件提醒
  function add_remind()
  {
    $broker_info = $this->user_arr;
    $ti_arr = array();
    $ti_arr['title'] = '客源跟进';
    $ti_arr['contents'] = $this->input->get('ti_text', TRUE);
    $ti_arr['agency_id'] = $broker_info['agency_id'];
    $ti_arr['broker_id'] = $broker_info['broker_id'];
    $ti_arr['broker_name'] = $broker_info['truename'];
    $ti_arr['create_time'] = strtotime(date('Y-m-d H:i:s'));
    $ti_arr['notice_time'] = strtotime($this->input->get('ti_time', TRUE));
    $ti_arr['tbl'] = 3;
    $ti_arr['row_id'] = $this->input->get('customer_id', TRUE);
    if (!empty($ti_arr['notice_time']) && !empty($ti_arr['contents'])) {
      $this->load->model('remind_model');
      //事件提醒表
      $add_result = $this->remind_model->add_remind($ti_arr);
      if (!empty($add_result) && is_int($add_result)) {
        //事情接受者表
        $receiver_data = array();
        $receiver_data['receiver_id'] = $broker_info['broker_id'];
        $receiver_data['event_id'] = $add_result;
        $this->load->model('event_receiver_model');
        $add_result2 = $this->event_receiver_model->add_receiver($receiver_data);
      }
    }
    if (isset($add_result2) && !empty($add_result2)) {
      echo 'success';
    } else {
      echo 'failed';
    }
  }

  /**
   * 报表导出
   * @author kang
   */
  public function exportReport($page = 1)
  {
    //模板使用数据
    $data = array();

    //post参数
    $posts = $this->input->post(NULL, FALSE);

    //判断是否有final_data数据
    $arr = explode('&', addslashes($posts['final_data']));
    for ($i = 0; $i < count($arr); $i++) {
      $l_arr = explode('=', $arr[$i]);
      $post_param[$l_arr[0]] = $l_arr[1];
    }

    //经纪人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    //所在公司的分店信息
    $company_id = intval($broker_info['company_id']);
    $this->load->model('api_broker_model');
    $company_id = $this->user_arr['company_id'];
    $data['agencys'] = $this->api_broker_model->get_agencys_by_company_id($company_id);

    //获取求购信息基本配置资料
    $conf_customer = $this->buy_customer_model->get_base_conf();
    $data['conf_customer'] = $conf_customer;

    //区属板块信息
    $this->load->model('district_model');

    //区属数据
    $arr_district = $this->district_model->get_district();
    $district_num = count($arr_district);
    for ($i = 0; $i < $district_num; $i++) {
      $temp_dist_arr[$arr_district[$i]['id']] = $arr_district[$i];
    }

    $data['district_arr'] = $temp_dist_arr;
    $dist_id = intval($post_param['dist_id']);
    $street_id = intval($post_param['street_id']);
    if ($dist_id > 0 && $street_id > 0) {
      $select_info['street_info'] =
        $this->district_model->get_street_bydist($dist_id);
      $data['select_info'] = $select_info;
    }

    //板块数据
    $arr_street = $this->district_model->get_street();
    $street_num = count($arr_street);
    for ($i = 0; $i < $street_num; $i++) {
      $temp_street_arr[$arr_street[$i]['id']] = $arr_street[$i];
    }
    $data['street_arr'] = $temp_street_arr;

    //查询条件
    //$cond_where = 'broker_id = '.$broker_id.' AND  status != 5';
    //$cond_where = 'status != 5';
    $cond_where = '';
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //排序字段
    $customer_order = isset($post_param['customer_order']) ? $post_param['customer_order'] : array();
    $order_arr = $this->_get_orderby_arr($customer_order);

    //根据ch（导出方式）查询相应的数据
    $ch = $posts['ch'];
    /*
        if($ch==''){
            $ch=2;
        }
        */
    $customer_list = "";
    if ($ch == 1) {//仅导出所选客源

      $ch_1_data = $posts['ch_1_data'];  //获取所选客户ID数组
      $customer_ids = explode(',', $ch_1_data);
      foreach ($customer_ids as $customer_id) {
        //查询条件
        $ch_cond_where = 'broker_id = ' . $broker_id . ' AND  status != 5';
        //表单提交参数组成的查询条件(仅针对ch=1)
        $dt['id'] = $customer_id;
        $cond_where_ch = $this->_get_cond_str($dt);
        if ($cond_where_ch !== '') {
          $ch_cond_where .= ' AND ';
        }
        $ch_cond_where .= $cond_where_ch;
        //获取列表内容
        $rs = $this->buy_customer_model->get_buylist_by_cond($ch_cond_where);
        $customer_list[] = $rs[0];
      }

    } else if ($ch == 2) {//导出当前页所有客源

      $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
      $offset = ($page - 1) * $this->_limit;
      //获取列表内容
      $customer_list = $this->buy_customer_model->get_buylist_by_cond($cond_where, $offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    } else if ($ch == 3) {//导出多页客源

      $start_page = $posts['start_page'];
      $end_page = $posts['end_page'];
      $offset = ($start_page - 1) * $this->_limit;
      $limit = (($end_page - $start_page) + 1) * $this->_limit;
      $customer_list = $this->buy_customer_model->get_buylist_by_cond($cond_where, $offset,
        $limit, $order_arr['order_key'], $order_arr['order_by']);

    } else if ($ch == 4) {
      $offset = $post_param['myoffset'];
      $limit = $post_param['mylimit'];

      $customer_list = $this->buy_customer_model->get_buylist_by_cond($cond_where, $offset,
        $limit, $order_arr['order_key'], $order_arr['order_by']);

    }
    //判断是否有数据
    if (count($customer_list) == 0) {
      echo '<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg">
                    <div class="hd">
                        <div class="title">提示</div>
                        <div class="close_pop">
                            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="javascript:location.reload();"></a>
                        </div>
                    </div>
                    <div class="mod">
                        <div class="inform_inner">
                            <div class="up_inner">
                                <p class="text"><img class="img_msg" style="margin-right:10px;" src="' . MLS_SOURCE_URL . '/mls/images/v1.0/r_ico.png">
                                    <span id="dialog_do_itp" class="span_msg">未查询出数据，无法导出！</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>';
      //echo "<script>alert('没有数据，无法导出！')</script>";
      //echo "<script>location.href = '".MLS_URL."/customer';</script>";
      return;
    }
    //循环获取经纪人姓名和门店信息
    if (count($customer_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();
      foreach ($customer_list as $key => $value) {
        $broker_id = intval($value['broker_id']);
        if ($broker_id > 0 && !in_array($broker_id, $broker_id_arr)) {
          array_push($broker_id_arr, $broker_id);
        }
      }
      //经纪人MODEL
      $this->load->model('api_broker_model');
      $broker_num = count($broker_id_arr);
      for ($i = 0; $i < $broker_num; $i++) {
        $broker_arr = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id_arr[$i]);
        $customer_broker_info[$broker_id_arr[$i]] = $broker_arr;
      }
      $data['customer_broker_info'] = $customer_broker_info;
    }
    $data['customer_list'] = $customer_list;

    //调用PHPExcel第三方类库
    $this->load->library('PHPExcel.php');
    $this->load->library('PHPExcel/IOFactory');
    //创建phpexcel对象
    $objPHPExcel = new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // 用于 2007 格式
    $objWriter->setOffice2003Compatibility(true);

    //设置phpexcel文件内容
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
      ->setLastModifiedBy("Maarten Balliauw")
      ->setTitle("Office 2007 XLSX Test Document")
      ->setSubject("Office 2007 XLSX Test Document")
      ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
      ->setKeywords("office 2007 openxml php")
      ->setCategory("Test result file");

    //设置表格导航属性
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '交易');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '状态');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '性质');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '合作');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '客户编号');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '客户姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '物业类型');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '意向区属板块');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '意向楼盘');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '户型');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '面积（m²）');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '售价（W）');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '委托门店');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '委托经纪人');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', '联系方式');

    //设置表格的值
    for ($i = 2; $i <= count($customer_list) + 1; $i++) {
      //区属板块数据处理
      $district_street = ""; //最终区属板块字符串
      $district1 = "";
      $district2 = "";
      $district3 = "";
      $street1 = "";
      $street2 = "";
      $street3 = "";
      //区属1
      if ($customer_list[$i - 2]['dist_id1'] > 0 && isset($temp_dist_arr[$customer_list[$i - 2]['dist_id1']]['district'])) {
        $district1 = $temp_dist_arr[$customer_list[$i - 2]['dist_id1']]['district'];
      }
      //板块1
      if ($customer_list[$i - 2]['street_id1'] > 0 && !empty($temp_street_arr[$customer_list[$i - 2]['street_id1']]['streetname'])) {
        $street1 = $temp_street_arr[$customer_list[$i - 2]['street_id1']]['streetname'];
      }
      //判断区属1和板块1是否一致
      if ($district1 && $street1) {
        $district_street = $district1 . "-" . $street1;
      }

      //区属2
      if ($customer_list[$i - 2]['dist_id2'] > 0 && isset($temp_dist_arr[$customer_list[$i - 2]['dist_id2']]['district'])) {
        $district2 = $temp_dist_arr[$customer_list[$i - 2]['dist_id2']]['district'];
      }
      //板块2
      if ($customer_list[$i - 2]['street_id2'] > 0 && !empty($temp_street_arr[$customer_list[$i - 2]['street_id2']]['streetname'])) {
        $street2 = $temp_street_arr[$customer_list[$i - 2]['street_id2']]['streetname'];
      }

      //判断区属2和板块2是否一致
      if ($district2 && $street2) {
        $district_street = $district_street . "、" . $district2 . "-" . $street2;
      }

      //区属3
      if ($customer_list[$i - 2]['dist_id3'] > 0 && isset($temp_dist_arr[$customer_list[$i - 2]['dist_id3']]['district'])) {
        $district3 = $temp_dist_arr[$customer_list[$i - 2]['dist_id3']]['district'];
      }
      //板块3
      if ($customer_list[$i - 2]['street_id3'] > 0 && !empty($temp_street_arr[$customer_list[$i - 2]['street_id3']]['streetname'])) {
        $street3 = $temp_street_arr[$customer_list[$i - 2]['street_id3']]['streetname'];
      }

      //判断区属3和板块3是否一致
      if ($district3 && $street3) {
        $district_street = $district_street . "、" . $district3 . "-" . $street3;
      }

      //楼盘数据处理
      $cmt = "";
      if (isset($customer_list[$i - 2]['cmt_name1']) && $customer_list[$i - 2]['cmt_name1'] != '') {
        $cmt = $customer_list[$i - 2]['cmt_name1'];
      }
      if (isset($customer_list[$i - 2]['cmt_name2']) && $customer_list[$i - 2]['cmt_name2'] != '') {
        $cmt = $cmt . "," . $customer_list[$i - 2]['cmt_name2'];
      }
      if (isset($customer_list[$i - 2]['cmt_name3']) && $customer_list[$i - 2]['cmt_name3'] != '') {
        $cmt = $cmt . "," . $customer_list[$i - 2]['cmt_name3'];
      }
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, '买');
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $customer_list[$i - 2]['status'] > 0 ? $conf_customer['status'][$customer_list[$i - 2]['status']] : "");
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $customer_list[$i - 2]['public_type'] > 0 ? $conf_customer['public_type'][$customer_list[$i - 2]['public_type']] : "");
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $conf_customer['is_share'][$customer_list[$i - 2]['is_share']]);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, format_info_id($customer_list[$i - 2]['id'], 'buy_customer'));
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $customer_list[$i - 2]['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $customer_list[$i - 2]['property_type'] > 0 ? $conf_customer['property_type'][$customer_list[$i - 2]['property_type']] : "");
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $district_street);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $cmt);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $customer_list[$i - 2]['room_min'] . "-" . $customer_list[$i - 2]['room_max']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $customer_list[$i - 2]['area_min'] . "-" . $customer_list[$i - 2]['area_max']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $customer_list[$i - 2]['price_min'] . "-" . $customer_list[$i - 2]['price_max']);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $customer_broker_info[$customer_list[$i - 2]['broker_id']]['agency_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $customer_broker_info[$customer_list[$i - 2]['broker_id']]['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $customer_broker_info[$customer_list[$i - 2]['broker_id']]['phone']);
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('buy_customer_report');
    $objPHPExcel->setActiveSheetIndex(0);

    //header("Content-type: text/csv");//重要
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');   //excel 2003
    //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');   //excel 2007
    //header('Content-Disposition: attachment;filename="求购客源.xls"');
    header("Content-Disposition: attachment;filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    //$objWriter->save("test.xls");

    //file_put_contents('E:\test.txt','hello world');

    /*header("Content-Type:text/html;charset=UTF-8");
        header("Content-Type: application/force-download");
        header('Content-Type: application/x-msexcel');
        header("Content-Disposition: attachment;filename=\"优惠码列表.xls\"");
        header('Cache-Control: max-age=0');
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save("php://output");*/

    exit;
  }


  /**
   * 设为私客
   * @access public
   * @return void
   */
  public function set_private()
  {
    $new_str = '';
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);

    //判断是否有设为私客权限
    $customer_info = $this->buy_customer_model->get_customer('id in (' . $str . ')');
    foreach ($customer_info as $v) {
      $new_str .= $v['id'] . ',';
    }
    $new_str = trim($new_str, ',');
    if ($new_str) {
      $this->_change_public_type($new_str, $flag);
    }
  }

  /**
   * 设为公客
   * @access public
   * @return void
   */
  public function set_public()
  {
    $new_str = '';
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);

    //判断是否有设为公客权限
    $customer_info = $this->buy_customer_model->get_customer('id in (' . $str . ')');
    foreach ($customer_info as $k => $v) {
      $new_str .= $v['id'] . ',';
    }
    $new_str = trim($new_str, ',');
    if ($new_str) {
      $this->_change_public_type($new_str, $flag);
    }
  }

  /**
   * 设为公客、私客
   * @access private
   * @return void
   */
  private function _change_public_type($str, $flag)
  {
    if ($str && $flag <= 2 && $flag >= 1) {
      $cond_where = "id IN (0," . $str . ") AND public_type != {$flag}";

      //跟进
      $this->load->model('follow_model');
      $ids_arr = array();

      $this->buy_customer_model->set_search_fields(array("id"));
      $list = $this->buy_customer_model->get_list_by_cond($cond_where);
      $text = $flag > 1 ? "设置公私客:私客>>公客" : "设置公私客:公客>>私客";

      foreach ($list as $key => $val) {
        $needarr = array();
        $needarr['broker_id'] = $this->user_arr['broker_id'];
        $needarr['customer_id'] = $val['id'];
        $needarr['type'] = 3;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_save($needarr);
        $ids_arr[] = $val['id'];
      }

      $arr = array('public_type' => $flag);
      if (is_full_array($ids_arr)) {
        $up_num = $this->buy_customer_model->update_info_by_id($ids_arr, $arr);
      } else {
        $up_num = 0;
      }
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }

    if ($up_num > 0) {
      $reslult = array('result' => 'ok', "arr" => $ids_arr, "msg" => "设置成功，共设置{$up_num}条数据");
    } else {
      $msg = ($flag == 1) ? "该客源已是公客" : "该客源已是私客";
      $reslult = array('result' => 'no', "msg" => $msg);
    }

    echo json_encode($reslult);
  }

  /**
   * 设为锁定
   * @access public
   * @return void
   */
  public function set_lock()
  {
    $new_str = '';
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);

    //判断是否有设为锁定权限
    $customer_info = $this->buy_customer_model->get_customer('id in (' . $str . ')');
    foreach ($customer_info as $v) {
      $new_str .= $v['id'] . ',';
    }
    $new_str = trim($new_str, ',');
    if ($new_str) {
      $this->_change_lock($new_str, $flag);
    }
  }

  /**
   * 设为解锁
   * @access public
   * @return void
   */
  public function set_unlock()
  {
    $new_str = '';
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);

    //判断是否有设为锁定权限
    $customer_info = $this->buy_customer_model->get_customer('id in (' . $str . ')');
    foreach ($customer_info as $v) {
      $new_str .= $v['id'] . ',';
    }
    $new_str = trim($new_str, ',');
    if ($new_str) {
      $this->_change_lock($new_str, $flag);
    }
  }


  /**
   * 锁定、解锁
   * @access private
   * @return void
   */
  private function _change_lock($str, $flag)
  {
    if ($str && $flag <= 1 && $flag >= 0) {
      $broker_id = $this->user_arr['broker_id'];

      if ($flag == 0) {
        //解锁
        $arr = array('lock' => $flag);
        $cond_where = "id IN (0," . $str . ") AND `lock` = {$broker_id}";
      } else if ($flag == 1) {
        //锁定
        $arr = array('lock' => $broker_id);
        $cond_where = "id IN (0," . $str . ") AND `lock` = 0";
      }

      //跟进
      $this->load->model('follow_model');
      $ids_arr = array();

      $this->buy_customer_model->set_search_fields(array("id"));
      $list = $this->buy_customer_model->get_list_by_cond($cond_where);

      $text = $flag ? '是否锁定:否>>是' : '是否锁定:是>>否';
      foreach ($list as $key => $val) {
        $needarr = array();
        $needarr['broker_id'] = $broker_id;
        $needarr['customer_id'] = $val['id'];
        $needarr['type'] = 3;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_save($needarr);
        $ids_arr[] = $val['id'];
      }
      if (is_full_array($ids_arr)) {
        $up_num = $this->buy_customer_model->update_info_by_id($ids_arr, $arr);
      } else {
        $up_num = 0;
      }
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }

    if ($up_num > 0) {
      $reslult = array('result' => 'ok', "arr" => $ids_arr, "msg" => "设置成功，共设置{$up_num}条数据");
    } else {
      $msg = ($flag == 1) ? "该客源已被锁定" : "该客源已被解锁";
      $reslult = array('result' => 'no', "msg" => $msg);
    }

    echo json_encode($reslult);
  }


  //导入报表1
  public function import()
  {
    if (!empty($_POST['sub'])) {
      $config['upload_path'] = str_replace("\\", "/", UPLOADS . DIRECTORY_SEPARATOR . 'temp');
      //目录不存在则创建目录
      if (!file_exists($config['upload_path'])) {
        $aryDirs = explode("/", substr($config['upload_path'], 0, strlen($config['upload_path'])));
        $strDir = "";
        foreach ($aryDirs as $value) {
          $strDir .= $value . "/";
          if (!@file_exists($strDir)) {
            if (!@mkdir($strDir, 0777)) {
              return "mkdirError";
            }
          }
        }
      }
      $config['file_name'] = date('YmdHis', time()) . rand(1000, 9999);
      $config['allowed_types'] = 'xlsx|xls';
      $config['max_size'] = "2000";
      $this->load->library('upload', $config);

      //打印成功或错误的信息
      if ($this->upload->do_upload('upfile')) {
        $data = array("upload_data" => $this->upload->data());
        //新权限
        //范围（1公司2门店3个人）
        $view_import_customer = $this->broker_permission_model->check('128');
        //上传的文件名称
        $broker_info = $this->user_arr;
        $this->load->model('read_model');
        $result = $this->read_model->read('buy_customer_model', $broker_info, $data['upload_data'],
          9, 2, $view_import_customer);
        unlink($data['upload_data']['full_path']); //删除文件
      } else {

        $result = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8">
                    <title>空白页面</title>
                    <link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"></head><body style="background:#F2F2F2;"><p class="up_m_b_date_up" style="text-align: center;"><span class="up_e">上传失败</span>，请选择文件上传</p></body></html>';
      }

      echo $result;
    }
  }

  /**
   * 确定导入
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function sure()
  {
    $data = array();
    //加载求购、求租基本配置MODEL
    $this->load->model('customer_base_model');
    $data['config'] = $this->customer_base_model->get_base_conf();
    $status = array();
    foreach ($data['config']['status'] as $key => $k) { //状态类型
      $status[$k] = $key;
    }
    $public_type = array();
    foreach ($data['config']['public_type'] as $key => $k) { //性质类型
      $public_type[$k] = $key;
    }
    $property_type = array();
    foreach ($data['config']['property_type'] as $key => $k) { //物业类型
      $property_type[$k] = $key;
    }
    /*
        $share = array();
        foreach($data['config']['is_share'] as $key => $k){ //是否合作
            $share[$k] = $key;
        }
        */
    $id = $this->input->post('id', true);
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $data['where']['id'] = $id;
    $data['where']['broker_id'] = $broker_id;
    $this->load->model('sell_model');
    $result = $this->sell_model->get_tmp($data['where'], '', '', '');
    $content = unserialize($result[0]['content']);
    $res = array();
    $i = 0;
    $fail_num = '';
    $content_count = count($content);
    //print_r($content);exit;

    //根据基本设置，判断客源是否去重
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $buy_customer_unique = intval($company_basic_data['buy_customer_unique']);
    } else {
      $buy_customer_unique = 0;
    }
    $this->load->model('broker_info_model');
    foreach ($content as $key => $k) {
      //通过经纪人电话号码查底所属的基本信息
      $broker = $this->broker_info_model->get_one_by(array('phone' => $k[12]));
      /***
       * $res['broker_id'] = $broker_id;
       * $res['broker_name'] = trim($broker_info['truename']);
       * $res['agency_id'] = trim($broker_info['agency_id']); //门店ID
       * $res['company_id'] = trim($broker_info['company_id']); //公司ID
       ***/
      $res['broker_id'] = $broker['broker_id'];
      $res['broker_name'] = trim($broker['truename']);
      $res['agency_id'] = trim($broker['agency_id']); //门店ID
      $res['company_id'] = intval($broker['company_id']);//获取总公司编号
      $res['truename'] = $k[0];  //客户姓名
      $res['telno1'] = "";
      $res['telno2'] = "";
      $res['telno3'] = "";
      foreach (explode("/", $k[1]) as $vo => $v) {
        $res['telno' . ($vo + 1)] = $v;
      }

      $res['status'] = $status[$k[2]];
      $res['public_type'] = $public_type[$k[3]];
      $res['property_type'] = $property_type[$k[4]];
      $res['is_share'] = 0;
      if (in_array($res['property_type'], array(1, 2))) {
        $res['room_min'] = $k[5];
        $res['room_max'] = $k[6];
      }
      $res['area_min'] = $k[7];
      $res['area_max'] = $k[8];
      $res['price_min'] = $k[9];
      $res['price_max'] = $k[10];
      foreach (explode("/", $k[11]) as $key1 => $k) {
        $n = explode("-", $k);
        $distwhere['is_show'] = 1;
        $distwhere['district'] = $n[0];
        $dis_info = $this->buy_customer_model->dist_info($distwhere);

        if (strpos($k, '-') === false) {
          $res['dist_id' . ($key1 + 1)] = $dis_info[0]['id'];
        } else {
          $streetwhere['dist_id'] = $res['dist_id' . ($key1 + 1)] = $dis_info[0]['id'];
          $streetwhere['is_show'] = 1;
          $streetwhere['streetname'] = $n[1];
          $street_info = $this->buy_customer_model->street_info($streetwhere);
          $res['street_id' . ($key1 + 1)] = $street_info[0]['id'];
        }
      }

      $res['creattime'] = time();
      $res['updatetime'] = time();
      $res['ip'] = get_ip();
      //导入数据的唯一性判断
      if (isset($buy_customer_unique) && 1 == $buy_customer_unique) {
        $customer_num = $this->_get_customer_num_by_telno($res['telno1'], $res['telno2'], $res['telno3']);
      } else {
        $customer_num = 0;
      }
      if ($customer_num == 0) {
        if (($this->buy_customer_model->add_data($res, 'db_city', 'buy_customer')) > 0) {
          $i++;
        }
      } else {
        $fail_num .= ($key + 8) . ',';
      }
      unset($res);
    }
    $fail_num = substr($fail_num, 0, -1);
    $fail_num .= '。';
    if ($i > 0 && $i == $content_count) {
      $res = array('broker_id' => $broker_id);
      $this->sell_model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '客源导入成功！<br />成功录入客源' . $i . '条。';

      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 16;
      $add_log_param['text'] = '导入求购客源' . $i . '条';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
    } else if ($i > 0 && $i != $content_count) {
      $res = array('broker_id' => $broker_id);
      $this->sell_model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '客源导入成功！<br>成功录入客源' . $i . '条。<br>重复录入客源' . ($content_count - $i) . '条。<br>重复录入表格行数为：' . $fail_num;

      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 16;
      $add_log_param['text'] = '导入求购客源' . $i . '条';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
      $result['status'] = 'error';
      $result['error'] = '客源导入失败！再试一次吧！<br />可能失败的原因：1.网络连接超时；2.重复导入客源。';
    }

    echo json_encode($result);
  }

  //分配客源
  public function allocate_customer($customer_id)
  {
    $customer_ids = '';
    $customer_id = str_replace('_', ',', $customer_id);

    //新权限
    //范围（个人或全公司）
    //获得当前数据所属的经纪人id和门店id
    $this->buy_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->buy_customer_model->set_id($customer_id);
    $owner_arr = $this->buy_customer_model->get_info_by_id();
    $share_tasks_per = $this->broker_permission_model->check('28', $owner_arr);
    //分配客源关联门店权限
    $agency_share_tasks_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '18');
    if (!$share_tasks_per['auth']) {
      $this->redirect_permission_none_iframe('js_allocate_customer');
      exit;
    } else {
      if (!$agency_share_tasks_per) {
        $this->redirect_permission_none_iframe('js_allocate_customer');
        exit;
      }
    }

    $customer_info = $this->buy_customer_model->get_customer('id in (' . $customer_id . ')');
    foreach ($customer_info as $v) {
      $customer_ids .= $v['id'] . ',';
    }
    $customer_ids = trim($customer_ids, ',');
    if ($customer_ids) {
      $data = array();
      $conf_customer = $this->buy_customer_model->get_base_conf();
      $data['conf_customer'] = $conf_customer;
      //加载区属模型类
      $this->load->model('district_model');

      //获取区属
      $all_district = $this->district_model->get_district();
      //区属数据重构
      $all_district2 = array();
      if (is_array($all_district) && !empty($all_district)) {
        foreach ($all_district as $k => $v) {
          $all_district2[$v['id']] = $v;
        }
      }
      //获取板块
      $all_street = $this->district_model->get_street();
      //板块数据重构
      $all_street2 = array();
      if (is_array($all_street) && !empty($all_street)) {
        foreach ($all_street as $k => $v) {
          $all_street2[$v['id']] = $v;
        }
      }

      $cond_where = "id IN ($customer_ids)";
      $customer_list = $this->buy_customer_model->get_customer($cond_where);

      $data['customer_id'] = $customer_ids;
      $customer_list2 = array();
      foreach ($customer_list as $k => $v) {
        //区属
        $v['dist_name'] = $all_district2[$v['dist_id1']]['district'];
        if (!empty($all_district2[$v['dist_id1']]['district']) && !empty($v['street_id1'])) {
          $v['dist_name'] .= '-' . $all_street2[$v['street_id1']]['streetname'];
        }

        if (!empty($v['dist_id2'])) {
          $v['dist_name'] .= ',' . $all_district2[$v['dist_id2']]['district'];

          if (!empty($all_district2[$v['dist_id2']]['district']) && !empty($v['street_id2'])) {
            $v['dist_name'] .= '-' . $all_street2[$v['street_id2']]['streetname'];
          }
        }
        if (!empty($v['dist_id3'])) {
          $v['dist_name'] .= ',' . $all_district2[$v['dist_id3']]['district'];

          if (!empty($all_district2[$v['dist_id3']]['district']) && !empty($v['street_id3'])) {
            $v['dist_name'] .= '-' . $all_street2[$v['street_id3']]['streetname'];
          }
        }
        if (($v['area_min'] - floor($v['area_min'])) == 0) {
          $v['area_min'] = intval($v['area_min']);
        }
        if (($v['area_max'] - floor($v['area_max'])) == 0) {
          $v['area_max'] = intval($v['area_max']);
        }
        if (($v['price_min'] - floor($v['price_min'])) == 0) {
          $v['price_min'] = intval($v['price_min']);
        }
        if (($v['price_max'] - floor($v['price_max'])) == 0) {
          $v['price_max'] = intval($v['price_max']);
        }
        //楼盘名称
        $v['cmt_name'] = $v['cmt_name1'];

        if (!empty($v['cmt_name2'])) {
          $v['cmt_name'] .= ',' . $v['cmt_name2'];
        }

        if (!empty($v['cmt_name3'])) {
          $v['cmt_name'] .= ',' . $v['cmt_name3'];
        }
        $customer_list2[] = $v;
      }
      $data['customer_list'] = $customer_list2;
      //根据总公司编号获取分店信息
      $broker_info = $this->user_arr;
      $agency_id = intval($broker_info['agency_id']);//经纪人门店编号
      $company_id = intval($broker_info['company_id']);//获取总公司编号
      $data['broker_name'] = $broker_info['truename'];
      $data['broker_id'] = $broker_info['broker_id'];
      $this->load->model('agency_model');
      $where = array('id' => $agency_id);
      $agency_name = $this->agency_model->get_one_by($where);
      $data['agency_name'] = $agency_name['name'];

      //获取全部分公司信息
      $this->load->model('api_broker_model');
      //根据权限role_id获得当前经纪人的角色，判断角色是否店长以上
      $role_level = intval($this->user_arr['role_level']);
      if (is_int($role_level) && $role_level < 6) {
        //根据数据范围，获得门店数据
        $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_customer_allocate');
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
        $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
      } else {
        $this_agency_data = $this->agency_model->get_by_id($agency_id);
        if (is_full_array($this_agency_data)) {
          $data['agency_list'] = array(
            array(
              'agency_id' => $this_agency_data['id'],
              'agency_name' => $this_agency_data['name']
            )
          );
        }
      }

      $data['page_title'] = '分配客源';
      //需要加载的css
      $data['css'] = load_css('mls/css/v1.0/base.css,'
        . 'mls/third/iconfont/iconfont.css,'
        . 'mls/css/v1.0/house_manage.css');
      //需要加载的JS
      $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
      //底部JS
      $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,'
        . 'mls/js/v1.0/house.js');
      $data['type'] = 'customer';
      //加载任务页面模板
      $this->view('customer/allocate_customer', $data);
    }
  }

  //添加分配客源
  public function add_allocate_customer()
  {
    $customer_ids = '';
    $customer_id = $this->input->post('customer_id', TRUE);//客源id

    //新权限
    //范围（个人或全公司）
    //获得当前数据所属的经纪人id和门店id
    $this->buy_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->buy_customer_model->set_id($customer_id);
    $owner_arr = $this->buy_customer_model->get_info_by_id();
    $share_tasks_per = $this->broker_permission_model->check('28', $owner_arr);
    //分配客源关联门店权限
    $agency_share_tasks_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '18');
    if (!$share_tasks_per['auth']) {
      $this->redirect_permission_none_iframe('js_allocate_customer');
      exit;
    } else {
      if (!$agency_share_tasks_per) {
        $this->redirect_permission_none_iframe('js_allocate_customer');
        exit;
      }
    }

    $customer_info = $this->buy_customer_model->get_customer('id in (' . $customer_id . ')');
    foreach ($customer_info as $v) {
      $customer_ids .= $v['id'] . ',';
    }
    $customer_ids = trim($customer_ids, ',');
    if ($customer_ids) {
      //分配给谁
      $run_broker_id = $this->input->post('run_broker_id', TRUE);
      //根据分配经纪人id，获得门店id，经纪人姓名
      $broker = $this->api_broker_model->get_baseinfo_by_broker_id($run_broker_id);
      $agency_id = 0;
      $broker_name = '';
      if (is_full_array($broker)) {
        $agency_id = $broker['agency_id'];
        $broker_name = $broker['truename'];
      }
      $cond_where = "id IN(" . $customer_ids . ")";
      $return_id = $this->buy_customer_model->update_customerinfo_by_cond(array('broker_id' => $run_broker_id, 'agency_id' => $agency_id, 'broker_name' => $broker_name), $cond_where);
      if (intval($return_id) > 0) {
        //发送站内信通知经纪人接受收房源
        $this->load->model('message_base_model');
        $params['name'] = $this->user_arr['truename'];
        $params['id'] = 'QG' . $customer_id;
        //33
        $this->message_base_model->add_message('7-48-2', $run_broker_id, $broker['truename'], '/customer/manage/', $params);

        //操作日志
        $add_log_param = array();
        $this->buy_customer_model->set_search_fields(array('dist_id1', 'street_id1', 'area_min', 'area_max', 'price_min', 'price_max'));
        $this->buy_customer_model->set_id($customer_id);
        $customer_info = $this->buy_customer_model->get_info_by_id();
        $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($run_broker_id);

        $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['agency_id'];
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $this->user_arr['truename'];
        $add_log_param['type'] = 15;
        $add_log_param['text'] = '求购客源 ' . 'QG' . $customer_id . ' 客源跟进 ' . $broker_info['agency_name'] . $broker_info['truename'];
        $add_log_param['from_system'] = 1;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = '127.0.0.1';
        $add_log_param['from_host_name'] = '127.0.0.1';
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();

        $this->operate_log_model->add_operate_log($add_log_param);
        echo '1';
      } else {
        echo '2';
      }
    }
  }

  //分配任务
  public function share_tasks($customer_id = 1, $num = 3)
  {
    $data['type'] = 'buy_customer';
    $customer_ids = '';
    $customer_id = str_replace('_', ',', $customer_id);

    //新权限
    //范围（个人或全公司）
    //获得当前数据所属的经纪人id和门店id
    $this->buy_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->buy_customer_model->set_id($customer_id);
    $owner_arr = $this->buy_customer_model->get_info_by_id();
    $share_tasks_per = $this->broker_permission_model->check('27', $owner_arr);
    //客源分配任务关联门店权限
    $agency_share_tasks_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '17');
    if (!$share_tasks_per['auth']) {
      $this->redirect_permission_none_iframe('js_fenpeirenwu');
      exit;
    } else {
      if (!$agency_share_tasks_per) {
        $this->redirect_permission_none_iframe('js_fenpeirenwu');
        exit;
      }
    }

    $customer_info = $this->buy_customer_model->get_customer('id in (' . $customer_id . ')');
    foreach ($customer_info as $v) {
      $customer_ids .= $v['id'] . ',';
    }
    $customer_ids = trim($customer_ids, ',');
    if ($customer_ids) {
      $data['type'] = 'buy_customer';
      $data['num'] = intval($num);
      //区属板块信息
      $this->load->model('district_model');

      //获取区属
      $all_district = $this->district_model->get_district();
      //区属数据重构
      $all_district2 = array();
      if (is_array($all_district) && !empty($all_district)) {
        foreach ($all_district as $k => $v) {
          $all_district2[$v['id']] = $v;
        }
      }

      //获取板块
      $all_street = $this->district_model->get_street();
      //板块数据重构
      $all_street2 = array();
      if (is_array($all_street) && !empty($all_street)) {
        foreach ($all_street as $k => $v) {
          $all_street2[$v['id']] = $v;
        }
      }

      $config_data = $this->buy_customer_model->get_base_conf();
      $data['config'] = $config_data;
      $customer_list = $this->buy_customer_model->get_all_customer_by_ids($customer_ids);

      $data['customer_ids'] = $customer_ids;

      //数据重构
      $customer_list2 = array();
      foreach ($customer_list as $k => $v) {
        //客源编号
        $v['id'] = 'QG' . $v['id'];
        //楼盘名称
        $v['cmt_name'] = $v['cmt_name1'];

        if (!empty($v['cmt_name2'])) {
          $v['cmt_name'] .= ',' . $v['cmt_name2'];
        }

        if (!empty($v['cmt_name3'])) {
          $v['cmt_name'] .= ',' . $v['cmt_name3'];
        }

        //户型
        $v['room'] = $v['room_min'] . '-' . $v['room_max'];

        //面积
        $v['area'] = strip_end_0($v['area_min']) . '-' . strip_end_0($v['area_max']);

        //总价
        $v['price'] = strip_end_0($v['price_min']) . '-' . strip_end_0($v['price_max']);

        //区属
        $v['dist_name'] = $all_district2[$v['dist_id1']]['district'];

        if (!empty($all_district2[$v['dist_id1']]['district']) && !empty($v['street_id1'])) {
          $v['dist_name'] .= '-' . $all_street2[$v['street_id1']]['streetname'];
        }

        if (!empty($v['dist_id2'])) {
          $v['dist_name'] .= ',' . $all_district2[$v['dist_id2']]['district'];

          if (!empty($all_district2[$v['dist_id2']]['district']) && !empty($v['street_id2'])) {
            $v['dist_name'] .= '-' . $all_street2[$v['street_id2']]['streetname'];
          }
        }

        if (!empty($v['dist_id3'])) {
          $v['dist_name'] .= ',' . $all_district2[$v['dist_id3']]['district'];

          if (!empty($all_district2[$v['dist_id3']]['district']) && !empty($v['street_id3'])) {
            $v['dist_name'] .= '-' . $all_street2[$v['street_id3']]['streetname'];
          }
        }
        $customer_list2[] = $v;
      }

      $data['customer_list'] = $customer_list2;

      //当前登录经纪人信息
      $this_broker = $this->user_arr;
      $data['broker_data'] = $this_broker;

      //获取所有分店的信息
      $this->load->model('agency_model');
      $this->load->model('api_broker_model');
      $company_id = $this_broker['company_id'];

      //根据权限role_id获得当前经纪人的角色，判断角色是否店长以上
      $role_level = intval($this->user_arr['role_level']);
      if (is_int($role_level) && $role_level < 6) {
        //根据数据范围，获得门店数据
        $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_customer_share_tasks');
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
        $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
      } else {
        $this_agency_data = $this->agency_model->get_by_id($this_broker['agency_id']);
        if (is_full_array($this_agency_data)) {
          $data['agency_list'] = array(
            array(
              'agency_id' => $this_agency_data['id'],
              'agency_name' => $this_agency_data['name']
            )
          );
        }
      }
      //获取同门店的所有经纪人信息
      $agency_id = $this_broker['agency_id'];
      $data['brokers'] = $this->api_broker_model->get_brokers_agency_id($agency_id);

      //需要加载的css
      $data['css'] = load_css('mls/css/v1.0/base.css,'
        . 'mls/third/iconfont/iconfont.css,'
        . 'mls/css/v1.0/house_manage.css');

      //需要加载的JS
      $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');

      //底部JS
      $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,'
        . 'mls/js/v1.0/house.js');

      //加载任务页面模板
      $this->view('customer/sell_tasks', $data);
    }
  }

  //根据门店id获取经纪人
  public function ajax_broker_list()
  {
    $agency_id = $this->input->get('agency_id', TRUE);
    $agency_id = intval($agency_id);
    $this->load->model('api_broker_model');
    $agency_arr = $this->api_broker_model->get_brokers_agency_id($agency_id);
    echo json_encode($agency_arr);
  }

  //	添加分配任务
  public function add_tasks()
  {
    $customer_arr = array();
    $customer_id = $this->input->get('customer_id', TRUE);//客源id

    //新权限
    //范围（个人或全公司）
    //获得当前数据所属的经纪人id和门店id
    $this->buy_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->buy_customer_model->set_id($customer_id);
    $owner_arr = $this->buy_customer_model->get_info_by_id();
    $share_tasks_per = $this->broker_permission_model->check('27', $owner_arr);
    //客源分配任务关联门店权限
    $agency_share_tasks_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '17');
    if (!$share_tasks_per['auth']) {
      $this->redirect_permission_none_iframe('js_fenpeirenwu');
      exit;
    } else {
      if (!$agency_share_tasks_per) {
        $this->redirect_permission_none_iframe('js_fenpeirenwu');
        exit;
      }
    }

    //判断是否有添加分配任务权限
    $customer_info = $this->buy_customer_model->get_customer('id in (' . $customer_id . ')');
    foreach ($customer_info as $k => $v) {
      $customer_arr[$k] = $v['id'];
    }

    if (is_full_array($customer_arr)) {
      $task_type = $this->input->get('task_type', TRUE);//任务类型
      $task_style = $this->input->get('task_style', TRUE);//任务方式
      $broker_info = $this->user_arr;//加载经纪人session
      $allot_broker_id = $broker_info['broker_id'];//分配人id
      $run_broker_id = $this->input->get('run_broker_id', TRUE);//执行人id
      $insert_date = time();//录入时间
      $over_date = strtotime($this->input->get('over_date', TRUE));//执行时间
      $content = $this->input->get('content', TRUE);//具体内容
      $this->load->model('task_model');
      if (!empty($customer_arr) && is_array($customer_arr)) {
        foreach ($customer_arr as $val) {
          $add_arr = array(
            'task_type' => $task_type,
            'task_style' => $task_style,
            'custom_id' => $val,
            'allot_broker_id' => $allot_broker_id,
            'run_broker_id' => $run_broker_id,
            'insert_date' => $insert_date,
            'over_date' => $over_date,
            'status' => 2,
            'content' => $content
          );
          $return_id = $this->task_model->insert($add_arr);

          if (!empty($return_id)) {
            if ($task_style == 3) {
              $val = 'QG' . $val;
            } else {
              $val = 'QZ' . $val;
            }
            $this->load->model('message_base_model');//written by angel_in_us
            $this->load->model('broker_info_base_model');//written by angel_in_us
            $allot_broker_info = $this->broker_info_base_model->get_by_broker_id($allot_broker_id);//written by angel_in_us
            $run_broker_info = $this->broker_info_base_model->get_by_broker_id($run_broker_id);//written by angel_in_us
            $params = array();
            $params['name'] = $allot_broker_info['truename'];
            $params['id'] = $val;
            //33
            $this->message_base_model->add_message('7-40-2', $run_broker_info['broker_id'], $run_broker_info['truename'], '/my_task/', $params);//written by angel_in_us
          }
        }
      }

      if (is_int($return_id) && !empty($return_id)) {
          echo json_encode(array('result' => 'insert_success'));
        //操作日志
        $add_log_param = array();
        $this->buy_customer_model->set_search_fields(array('dist_id1', 'street_id1', 'area_min', 'area_max', 'price_min', 'price_max'));
        $this->buy_customer_model->set_id($customer_id);
        $customer_info = $this->buy_customer_model->get_info_by_id();
        $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($run_broker_id);

        $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['agency_id'];
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $this->user_arr['truename'];
        $add_log_param['type'] = 14;
        $add_log_param['text'] = '求购客源 ' . 'QG' . $customer_id . ' 分配任务 ' . $broker_info['agency_name'] . $broker_info['truename'];
        $add_log_param['from_system'] = 1;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = '127.0.0.1';
        $add_log_param['from_host_name'] = '127.0.0.1';
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();

        $this->operate_log_model->add_operate_log($add_log_param);
      } else {
          echo json_encode(array('result' => 'insert_failed'));
      }
    }
  }


  public function blank()
  {
    $data = array();
    $this->load->view('customer/blank', $data);
  }


  //客源的修改跟进匹配信息
  public function customer_follow_match($new, $old)
  {
    $this->load->model('district_model');

    //获取求购信息基本配置资料
    $conf_customer = $this->buy_customer_model->get_base_conf();

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $dis[$val['id']] = $val;
    }

    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $stred[$val['id']] = $val;
    }

    $constr = '';
    foreach ($old as $key => $val) {
      if ($val != $new[$key]) {
        switch ($key) {
          case'sex':
            $constr .= '性别:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'truename':
            $constr .= '客户姓名' . $val . '>>' . $new[$key] . ',';
            break;
          case'idno':
            $constr .= '客户身份证' . $val . '>>' . $new[$key] . ',';
            break;
          case'telno1':
            $constr .= '客户电话1:' . $val . '>>' . $new[$key] . ',';
            break;
          case'telno2':
            $constr .= '客户电话2:' . $val . '>>' . $new[$key] . ',';
            break;
          case'telno3':
            $constr .= '客户电话3:' . $val . '>>' . $new[$key] . ',';
            break;
          case'address':
            $constr .= '客户地址:' . $val . '>>' . $new[$key] . ',';
            break;
          case'job_type':
            $constr .= '客户工作类型:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'user_level':
            $constr .= '客户等级:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'age_group':
            $constr .= '客户年龄段:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'status':
            $constr .= '信息状态:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'public_type':
            $constr .= '信息属性:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'is_share':
            $constr .= '是否合作:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'room_min':
            $constr .= '最小户型:' . $conf_customer['room_type'][$val] . '>>' . $conf_customer['room_type'][$new[$key]] . ',';
            break;
          case'room_max':
            $constr .= '最大户型:' . $conf_customer['room_type'][$val] . '>>' . $conf_customer['room_type'][$new[$key]] . ',';
            break;
          case'area_min':
            $constr .= '最小面积需求:' . $val . '>>' . $new[$key] . ',';
            break;
          case'area_max':
            $constr .= '最大面积需求:' . $val . '>>' . $new[$key] . ',';
            break;
          case'price_min':
            $constr .= '最低价格:' . $val . '>>' . $new[$key] . ',';
            break;
          case'price_max':
            $constr .= '最高价格:' . $val . '>>' . $new[$key] . ',';
            break;
          case'dist_id1':
            $constr .= '区属1:' . $dis[$val]['district'] . '>>' . $dis[$new['dist_id1']]['district'] . ',';
            break;
          case'street_id1';
            $constr .= '板块1：' . $stred[$val]['streetname'] . '>>' . $stred[$new['street_id1']]['streetname'] . ',';
            break;
          case'dist_id2':
            $constr .= '区属2:' . $dis[$val]['district'] . '>>' . $dis[$new['dist_id2']]['district'] . ',';
            break;
          case'street_id2';
            $constr .= '板块2:' . $stred[$val]['streetname'] . '>>' . $stred[$new['street_id2']]['streetname'] . ',';
            break;
          case'dist_id3':
            $constr .= '区属3:' . $dis[$val]['district'] . '>>' . $dis[$new['dist_id3']]['district'] . ',';
            break;
          case'street_id3';
            $constr .= '板块3:' . $stred[$val]['streetname'] . '>>' . $stred[$new['street_id3']]['streetname'] . ',';
            break;
          case'cmt_name1':
            $constr .= '意向楼盘1:' . $val . '>>' . $new[$key] . ',';
            break;
          case'cmt_name2':
            $constr .= '意向楼盘2:' . $val . '>>' . $new[$key] . ',';
            break;
          case'cmt_name3':
            $constr .= '意向楼盘3:' . $val . '>>' . $new[$key] . ',';
            break;
          case'forward':
            $constr .= '朝向:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'fitment':
            $constr .= '装修:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'floor_min':
            $constr .= '最低楼层:' . $val . '>>' . $new[$key] . ',';
            break;
          case'floor_max':
            $constr .= '最高楼层:' . $val . '>>' . $new[$key] . ',';
            break;
          case'location':
            $constr .= '地段:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'house_type':
            $constr .= '房源类型:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'property_type':
            $constr .= '物业类型:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'intent':
            $constr .= '目的:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'infofrom':
            $constr .= '信息来源:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'house_age':
            $constr .= '房龄:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'remark';
            $constr .= '描述:' . $val . '>>' . $new[$key] . ',';
            break;
          case'pay_commission':
            $constr .= '付佣方式:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
          case'deadline':
            $constr .= '期限:' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
            break;
        }
      }
    }

    return $constr;
  }

  /*工作统计日志
	 * type:1出售2出租3求购4求租
	 * $state：1信息录入2信息修改3图片上传4堪房5带看6钥匙提交
	 */
  private function info_count($customer_id, $state, $house_id = 0)
  {
    $this->load->model('count_log_model');
    $this->load->model('count_num_model');
    $broker_info = $this->user_arr;
    $insert_log_data = array(
      'company_id' => $broker_info['company_id'],
      'agency_id' => $broker_info['agency_id'],
      'broker_id' => $broker_info['broker_id'],
      'dateline' => time(),
      'YMD' => date('Y-m-d'),
      'state' => $state,
      'type' => 3,
      'customer_id' => $customer_id,
      'house_id' => $house_id
    );
    $insert_id = $this->count_log_model->insert($insert_log_data);
    if ($insert_id) {
      $count_num_info = $this->count_num_model->get_one_by('broker_id = ' . $broker_info['broker_id'] . ' and YMD = ' . "'" . date('Y-m-d') . "'");
      if (is_full_array($count_num_info)) {
        //修改数据
        switch ($state) {
          case 1://信息录入
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'insert_num' => $count_num_info['insert_num'] + 1
            );
            break;
          case 2://信息修改
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'modify_num' => $count_num_info['modify_num'] + 1
            );
            break;
          case 5://带看
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'looked_num' => $count_num_info['looked_num'] + 1
            );
            break;
          case 8://查看保密信息
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'secret_num' => $count_num_info['secret_num'] + 1
            );
            break;
          case 9://普通跟进
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'follow_num' => $count_num_info['follow_num'] + 1
            );
            break;
        }
        $row = $this->count_num_model->update_by_id($update_data, $count_num_info['id']);
        if ($row) {
          return 'success';
        } else {
          return 'error';
        }
      } else {
        //添加数据
        switch ($state) {
          case 1://信息录入
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'insert_num' => 1
            );
            break;
          case 2://信息修改
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'modify_num' => 1
            );
            break;
          case 5://带看
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'looked_num' => 1
            );
            break;
          case 8://查看保密信息
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'secret_num' => 1
            );
            break;
          case 9://普通跟进
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'follow_num' => 1
            );
            break;
        }
        $insert_num_id = $this->count_num_model->insert($insert_num_data);
        if ($insert_num_id) {
          return 'success';
        } else {
          return 'error';
        }
      }
    } else {
      return 'error';
    }
  }

  public function ceshi()
  {
    //获取求购信息基本配置资料
    $conf_customer = $this->buy_customer_model->get_base_conf();
    var_dump($conf_customer);
  }

  /**
   * 合作方审核求购
   *
   * @access  public
   * @param  void
   * @return  void
   */

  public function lists_buy($page = 1)
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      //是否开启合作中心
      $open_cooperate = $company_basic_data['open_cooperate'];
    } else {
      $open_cooperate = '';
    }
    //模板使用数据
    $data = array();
    $data['open_cooperate'] = $open_cooperate;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

    //经纪人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $data['broker_id'] = $broker_id;
    $data['agency_id'] = $broker_info['agency_id'];//经纪人门店编号
    $data['agency_name'] = $broker_info['agency_name'];//获取经纪人所对应门店的名称
    $data['phone'] = $broker_info['phone'];//获取经纪人对应的联系方式

    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $data['company_id'] = $company_id;

    //查询条件
    $cond_where = '';

    //默认公司

    if (!isset($post_param['broker_id'])) {
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    if (!isset($post_param['agenctcode'])) {
      $post_param['agenctcode'] = $this->user_arr['agency_id'];
    }

    //根据权限role_id获得当前经纪人的角色，判断店长
    $role_id = $this->user_arr['role_id'];
    $role_level = intval($this->user_arr['role_level']);
    $data['role_level'] = $role_level;
    $this->load->model('permission_agency_group_model');
    $role_data = $this->permission_agency_group_model->get_one_by(array('id' => $role_id));
    if (is_full_array($role_data)) {
      $system_group_id = intval($role_data['system_group_id']);
    }
    $data['system_group_id'] = $system_group_id;
    //店长
    if (is_int($role_level) && $role_level == 6) {
      $dist_street = $this->agency_model->get_by_id($broker_info['agency_id']);
      $agency_name = $dist_street['name'];
      $data['agency_list'] = $agency_name;
      if ($post_param['agenctcode']) {
        $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($post_param['agenctcode']);
      }
      //店长以上的获取全部分公司信息
    } else {
      //根据数据范围，获得门店数据
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
      $cond_where .= "agency_id in (" . $all_access_agency_ids . ") AND ";

      $this->load->model('agency_model');
      $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

      if ($post_param['agenctcode']) {
        $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($post_param['agenctcode']);
      }
    }

    //获取求购信息基本配置资料
    $conf_customer = $this->buy_customer_model->get_base_conf();
    $data['conf_customer'] = $conf_customer;

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');

    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //区属板块信息
    $this->load->model('district_model');

    //区属数据
    $arr_district = $this->district_model->get_district();
    $district_num = count($arr_district);
    for ($i = 0; $i < $district_num; $i++) {
      $temp_dist_arr[$arr_district[$i]['id']] = $arr_district[$i];
    }

    $data['district_arr'] = $temp_dist_arr;
    $dist_id = intval($post_param['dist_id']);
    $street_id = intval($post_param['street_id']);

    if ($dist_id > 0) {
      $select_info['street_info'] = $this->district_model->get_street_bydist($dist_id);
      $data['select_info'] = $select_info;
    }

    //板块数据
    $arr_street = $this->district_model->get_street();
    $street_num = count($arr_street);
    for ($i = 0; $i < $street_num; $i++) {
      $temp_street_arr[$arr_street[$i]['id']] = $arr_street[$i];
    }
    $data['street_arr'] = $temp_street_arr;

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;
    $pub_cond = "is_share = 2";
    $cond_where .= !empty($cond_where) ? ' AND ' . $pub_cond : $pub_cond;
    //排序字段
    $customer_order = isset($post_param['customer_order']) ? $post_param['customer_order'] : array();
    $order_arr = $this->_get_orderby_arr($customer_order);

    //符合条件的总行数
    $this->_total_count = $this->buy_customer_model->get_buynum_by_cond($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $customer_list =
      $this->buy_customer_model->get_buylist_by_cond($cond_where, $this->_offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    //根据经纪人编号获取已收藏客源数据
    $type = 'buy_customer';
    $this->load->model('customer_collect_model');
    $collected_ids = array();
    $collected_ids_temp = $this->customer_collect_model->get_collect_ids_by_bid($broker_id, $type);

    $collect_num = count($collected_ids_temp);
    for ($i = 0; $i < $collect_num; $i++) {
      $collected_ids[$i] = $collected_ids_temp[$i]['customer_id'];
    }

    $data['collected_ids'] = $collected_ids;
    //房源id数组
    $house_id_arr = array();
    //循环获取经纪人姓名和门店信息
    if (count($customer_list) > 0) {
      $broker_id_arr = array();//经纪人帐号
      foreach ($customer_list as $key => $value) {
        $house_id_arr[] = $value['id'];
        $brokeridstr .= $value['broker_id'] . ',';
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $customer_list[$key]['telno'] = $brokerinfo['phone'];
        $customer_list[$key]['broker_name'] = $brokerinfo['truename'];
        $customer_list[$key]['agency_name'] = $brokerinfo['agency_name'];
        // 最新跟进时间
        $customer_list[$key]['genjintime'] = $val['updatetime'] > 0 ? date('Y-m-d H:i', $val['updatetime']) : '';

        $broker_id_gj = intval($value['broker_id']);
        if ($broker_id_gj > 0 && !in_array($broker_id_gj, $broker_id_arr)) {
          array_push($broker_id_arr, $broker_id_gj);
        }

        $cid_arr[] = $value['id'];
      }

      //检查是否已经申请过客源合作
      $this->load->model('cooperate_model');
      $data['check_coop_reulst'] = $this->cooperate_model->check_is_cooped_by_cid($cid_arr, 'sell', $broker_id);

      //经纪人MODEL
      $this->load->model('api_broker_model');
      $this->load->model('api_broker_sincere_model');
      $broker_num = count($broker_id_arr);
      //合作成功率MODEL
      $this->load->model('cooperate_suc_ratio_base_model');
      for ($i = 0; $i < $broker_num; $i++) {
        $broker_arr = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id_arr[$i]);
        $customer_broker_info[$broker_id_arr[$i]] = $broker_arr;

        //获取经纪人好评率
        $appraise_count = array();
        $appraise_count = $this->api_broker_sincere_model->get_trust_appraise_count($broker_id_arr[$i]);
        $customer_broker_info[$broker_id_arr[$i]]['good_rate'] = !empty($appraise_count) ? $appraise_count['good_rate'] : 0;

        //经济人合作成功率
        $cop_succ_ratio_info = array();
        $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id_arr[$i]);
        $customer_broker_info[$broker_id_arr[$i]]['cop_succ_ratio_info'] = !empty($cop_succ_ratio_info) ? $cop_succ_ratio_info : array();
      }

      $data['customer_broker_info'] = $customer_broker_info;
    }

    $data['customer_list'] = $customer_list;
    //表单数据
    $data['post_param'] = $post_param;

    //如果后几个搜搜参数被选中则，后面的选择参数全部显示
    if (!empty($post_param['property_type']) || !empty($post_param['room']) ||
      !empty($post_param['public_type']) || !empty($post_param['status']) ||
      !empty($post_param['is_share']) || !empty($post_param['id'])
    ) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
    }
    //获取是否举报过
    $follow_house_id = array();
    $this->load->model('report_model');
    $follow_where = "type IN (2,3,4) ";
    $follow_where .= " AND broker_id = '$broker_id'";
    $follow_where .= " AND style = 3 ";
    $follow_house = $this->report_model->get_report_house_bid($follow_where);

    foreach ($follow_house as $key => $val) {
      $follow_house_id[] = $val['number'];
    }

    $follow_number = array_count_values($follow_house_id);
    $follow_house_num = array();

    foreach ($follow_number as $key => $val) {
      if ($val == 3) {
        $follow_house_num[] = $key;
      }
    }

    $data['follow_house_num'] = $follow_house_num;

    //菜单
    $data['user_menu'] = $this->user_menu;

    //三级功能菜单
    $data['user_func_menu'] = $this->user_func_menu;

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post/ajax
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '求购公盘';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css'
      . ',mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'mls/js/v1.0/broker_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/customer_list.js,'
      . 'mls/js/v1.0/cooperate_common.js');

    //加载发布页面模板
    $this->view('cooperation/cooperation_buy', $data);
  }


  //确认合作或拒绝的方法
  public function change_is_share()
  {
    $buy_customer_id = $this->input->get('buy_customer_id');
    $type = $this->input->get('type');
    $where = array(
      'id' => $buy_customer_id,
    );
    $data = array('is_share' => $type);
    if ('1' == $type) {
      $data['set_share_time'] = time();
    }
    $rel = $this->buy_customer_model->change_isshare_status($where, $data);
    //添加跟进信息
    $this->load->model('follow_model');
    $needarr = array();
    $needarr['broker_id'] = $this->user_arr['broker_id'];
    $needarr['customer_id'] = $buy_customer_id;
    $needarr['agency_id'] = $this->user_arr['agency_id'];//门店ID
    $needarr['company_id'] = $this->user_arr['company_id'];//总公司id
    $needarr['type'] = 3;

    //操作日志
    $add_log_param = array();
    $add_log_param['company_id'] = $this->user_arr['company_id'];
    $add_log_param['agency_id'] = $this->user_arr['agency_id'];
    $add_log_param['broker_id'] = $this->user_arr['broker_id'];
    $add_log_param['broker_name'] = $this->user_arr['truename'];
    $add_log_param['type'] = 33;
    $add_log_param['from_system'] = 1;
    $add_log_param['from_ip'] = get_ip();
    $add_log_param['mac_ip'] = '127.0.0.1';
    $add_log_param['from_host_name'] = '127.0.0.1';
    $add_log_param['hardware_num'] = '测试硬件序列号';
    $add_log_param['time'] = time();

    if ($rel && $type == 1) {
      $add_log_param['text'] = '通过客源编号为' . $buy_customer_id . '的合作审核';
      $this->operate_log_model->add_operate_log($add_log_param);

      /*通过增加积分开始*/
      $house_info = $this->cooperation_model->get_info_by_id($buy_customer_id, 'buy_customer');
      $this->api_broker_credit_model->set_broker_param(array('broker_id' => $house_info['broker_id']));
      $this->api_broker_credit_model->publish_cooperate_house(array('id' => $buy_customer_id), 3);
      /*通过增加积分结束*/
      /*通过增加等级分值开始*/
      $this->api_broker_level_model->set_broker_param(array('broker_id' => $house_info['broker_id']));
      $this->api_broker_level_model->publish_cooperate_house(array('id' => $buy_customer_id), 3);
      /*通过增加等级分值结束*/
      $needarr['text'] = "是否合作:审核中>>是";
      $this->follow_model->customer_save($needarr);
      echo json_encode($data['result'] = 'ok');
      exit;
    } else {
      $add_log_param['text'] = '拒绝客源编号为' . $buy_customer_id . '的合作审核';
      $this->operate_log_model->add_operate_log($add_log_param);

      $needarr['text'] = "是否合作:审核中>>否";
      $this->follow_model->customer_save($needarr);
      echo json_encode($data['result'] = 'fail');
      exit;
    }
  }

  //确认合作全部通过或拒绝
  public function change_all_share()
  {
    $ids = $this->input->get('ids');
    $id = explode('|', $ids);
    $type = $this->input->get('type');
    if ('0' == $type) {
      foreach ($id as $key => $val) {
        $where = array(
          'id' => $val,
        );
        $data = array('is_share' => 0);
        $rel = $this->buy_customer_model->change_isshare_status($where, $data);
      }
    } else if ('1' == $type) {
      foreach ($id as $key => $val) {
        $where = array(
          'id' => $val,
        );
        $data = array('is_share' => 1);
        $rel = $this->buy_customer_model->change_isshare_status($where, $data);
        if ($rel && $type == 1) {
          /*通过增加积分开始*/
          $house_info = $this->cooperation_model->get_info_by_id($val, 'buy_customer');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $house_info['broker_id']));
          $this->api_broker_credit_model->publish_cooperate_house(array('id' => $val), 3);
          /*通过增加积分结束*/
          /*通过增加等级分值开始*/
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $house_info['broker_id']));
          $this->api_broker_level_model->publish_cooperate_house(array('id' => $val), 3);
          /*通过增加等级分值结束*/
        }
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

  public function min_log_replace()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('broker_info_min_log_model');
    $window_min_id_arr = $this->input->get('window_min_id', TRUE);
    $is_pub = $this->input->get('is_pub', TRUE);
    $type = 'buy_customer_list';
    if ('1' == $is_pub) {
      $type = 'buy_customer_list_pub';
    }
    $last_result = 0;
    if (is_full_array($window_min_id_arr)) {
      $window_min_id_str = '';
      foreach ($window_min_id_arr as $k => $v) {
        $window_min_id_str .= $v . ',';
      }
      //判断当前经纪人是否已经有日志记录
      $where_cond = array('broker_id' => $broker_id);
      $query_result = $this->broker_info_min_log_model->get_log($where_cond);
      if (is_full_array($query_result)) {
        $update_data = array(
          $type => $window_min_id_str
        );
        $update_result = $this->broker_info_min_log_model->update_log($broker_id, $update_data);
        $last_result = $update_result;
      } else {
        $add_data = array(
          'broker_id' => $broker_id,
          $type => $window_min_id_str
        );
        $add_result = $this->broker_info_min_log_model->add_log($add_data);
        $last_result = $add_result;
      }

      if (is_int($last_result) && $last_result > 0) {
        echo 'success';
      } else {
        echo 'failed';
      }
      exit;
    }
  }

  public function min_log_del()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('broker_info_min_log_model');
    $window_min_id_arr = $this->input->get('window_min_id', TRUE);
    $is_pub = $this->input->get('is_pub', TRUE);
    $type = 'buy_customer_list';
    if ('1' == $is_pub) {
      $type = 'buy_customer_list_pub';
    }
    $last_result = 0;
    $window_min_id_str = '';
    if (is_full_array($window_min_id_arr)) {
      foreach ($window_min_id_arr as $k => $v) {
        $window_min_id_str .= $v . ',';
      }
    }
    //判断当前经纪人是否已经有日志记录
    $where_cond = array('broker_id' => $broker_id);
    $query_result = $this->broker_info_min_log_model->get_log($where_cond);
    if (is_full_array($query_result)) {
      $update_data = array(
        $type => $window_min_id_str
      );
      $update_result = $this->broker_info_min_log_model->update_log($broker_id, $update_data);
      $last_result = $update_result;
    }

    if (is_int($last_result) && $last_result > 0) {
      echo 'success';
    } else {
      echo 'failed';
    }
    exit;
  }

  //设为公共客源
  public function set_public_customer()
  {
    $customer_id = $this->input->get('customer_id');
    if (intval($customer_id) <= 0) {
      return false;
    }
    //当前经纪人是否认证
    $this_broker_group_id = $this->user_arr['group_id'];
    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->buy_customer_model->set_id($customer_id);
    $customer_info = $this->buy_customer_model->get_info_by_id();
    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $owner_arr = array(
      'broker_id' => $customer_info['broker_id'],
      'agency_id' => $customer_info['agency_id'],
      'company_id' => $customer_info['company_id'],
    );
    //设置公共客源权限
    $set_public_customer_per = $this->broker_permission_model->check('131', $owner_arr);
    //设置公共客源关联门店权限
    $agency_set_public_customer_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '27');
    if (!$set_public_customer_per['auth']) {
      $this->redirect_permission_none();
      exit();
    } else {
      if (!$agency_set_public_customer_per) {
        $this->redirect_permission_none();
        exit();
      }
    }
    $this->buy_customer_model->change_is_public_by_agency_id(array($customer_id));
    echo json_encode(array('result' => 'ok'));
  }

  public function submit_secret_info()
  {

    $result = array();

    $customer_id = intval($this->input->get("customer_id"));
    $truename = strval($this->input->get("truename"));
    $telno = strval($this->input->get("telno"));
    $telno2 = strval($this->input->get("telno2"));
    $telno3 = strval($this->input->get("telno3"));
    $idno = strval($this->input->get("idno"));
    $address = strval($this->input->get("address"));
    $job_type = strval($this->input->get("job_type"));
    $user_level = strval($this->input->get("user_level"));
    $age_group = strval($this->input->get("age_group"));

    if (isset($customer_id) && !empty($customer_id)) {
      $this->buy_customer_model->set_search_fields(array('truename', 'telno1', 'telno2', 'telno3', 'idno', 'address', 'job_type', 'user_level', 'age_group'));
      $this->buy_customer_model->set_id($customer_id);
      $old = $this->buy_customer_model->get_info_by_id();//修改前的信息

      $new = $update_info = array(
        'truename' => $truename,
        'telno1' => $telno,
        'telno2' => $telno2,
        'telno3' => $telno3,
        'idno' => $idno,
        'address' => $address,
        'job_type' => $job_type,
        'user_level' => $user_level,
        'age_group' => $age_group
      );
      $update_result = $this->buy_customer_model->update_customerinfo_by_cond($update_info, array('id' => $customer_id));
      if (1 == $update_result) {
        $result['msg'] = 'success';
        //求购客源修改跟进
        $customer_broker_id = intval($this->user_arr['broker_id']);
        $text = $this->customer_follow_match($new, $old);
        $this->load->model('api_broker_model');
        $broker_messagin = $this->api_broker_model->get_baseinfo_by_broker_id($customer_broker_id);

        $this->load->model('follow_model');
        $needarr = array();
        $needarr['broker_id'] = $customer_broker_id;
        $needarr['customer_id'] = $customer_id;
        $needarr['company_id'] = $broker_messagin['company_id'];
        $needarr['agency_id'] = $broker_messagin['agency_id'];;
        $needarr['type'] = 3;
        $needarr['text'] = $text;
        if (!empty($text)) {
          $bool = $this->follow_model->customer_save($needarr);
          if (is_int($bool) && $bool > 0) {
            //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
            //获得基本设置房源跟进的天数
            //获取当前经济人所在公司的基本设置信息
            $this->load->model('house_customer_sub_model');
            $company_basic_data = $this->company_basic_arr;
            $customer_follow_day = intval($company_basic_data['customer_follow_spacing_time']);

            $select_arr = array('id', 'house_id', 'date');
            $this->follow_model->set_select_fields($select_arr);
            $where_cond = 'customer_id = "' . $customer_id . '" and follow_type != 1 and type = 3';
            $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
            if (count($last_follow_data) == 2) {
              $time1 = $last_follow_data[0]['date'];
              $time2 = $last_follow_data[1]['date'];
              $date1 = date('Y-m-d', strtotime($time1));
              $date2 = date('Y-m-d', strtotime($time2));
              $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
              if ($differ_day > $customer_follow_day) {
                $this->house_customer_sub_model->add_buy_customer_sub($customer_id, 1);
              } else {
                $this->house_customer_sub_model->add_buy_customer_sub($customer_id, 0);
              }
            } else {
              $this->house_customer_sub_model->add_buy_customer_sub($customer_id, 0);
            }
          }
        }
      } else {
        $result['msg'] = 'failed';
      }
    } else {
      $result['msg'] = 'failed';
    }
    echo json_encode($result);

  }

}


/* End of file customer.php */
/* Location: ./applications/mls/controllers/customer.php */
