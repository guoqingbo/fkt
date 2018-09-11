<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 交易 Class
 *
 * 城市控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      lalala
 */
class Contract_flow extends MY_Controller
{

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
    //加载合同MODEL
    $this->load->model('contract_model');
    //加载合同跟进模型类
    $this->load->model('contract_log_model');
    //加载实收实付MODEL
    $this->load->model('contract_flow_model');
    //加载合同基本配置MODEL
    $this->load->model('contract_config_model');
    //加载经纪人MODEL
    $this->load->model('broker_info_model');
    //加载门店MODEL
    $this->load->model('agency_model');
    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
    }
  }

  public function _get_cond_str($form_param)
  {
    //合同标号
    $number = isset($form_param['number']) ? trim($form_param['number']) : '';
    if ($number) {
      $cond_where .= " AND a.`number` like '%" . $number . "%'";
    }

    //款类
    $money_type = isset($form_param['money_type']) ? intval($form_param['money_type']) : 0;
    if ($money_type) {
      $cond_where .= " AND f.`money_type` = '" . $money_type . "'";
    }

    //付款方和收款方
    $collect_type = isset($form_param['collect_type']) ? intval($form_param['collect_type']) : 0;
    if ($collect_type) {
      $cond_where .= " AND (f.`collect_type` = " . $collect_type . ' or f.`pay_type` = ' . $collect_type . ')';
    }
    //合同状态
    if (isset($form_param['status']) && $form_param['status'] != '') {
      $cond_where .= " AND f.`status` = '" . $form_param['status'] . "'";
    }
    //时间条件
    $date_type = isset($form_param['datetype']) ? intval($form_param['datetype']) : 0;
    //起止时间
    $start_time = isset($form_param['start_time']) && !empty($form_param['start_time']) ? strtotime($form_param['start_time'] . ' 0:0:0') : "";
    $end_time = isset($form_param['end_time']) && !empty($form_param['end_time']) ? strtotime($form_param['end_time'] . ' 23:59:59') : "";

    if ($date_type == 1) {
      //签约日期
      if ($start_time) {
        $cond_where .= " AND a.`signing_time` >= '" . $start_time . "'";
      }
      if ($end_time) {
        $cond_where .= " AND a.`signing_time` <= '" . $end_time . "'";
      }
    } elseif ($date_type == 2) {

      //收付日期
      if ($start_time) {
        $cond_where .= " AND f.`flow_time` >= '" . date('Y-m-d', $start_time) . "'";
      }
      if ($end_time) {
        $cond_where .= " AND f.`flow_time` <= '" . date('Y-m-d', $end_time) . "'";
      }
    } else {
      //录入时间
      if ($start_time) {
        $cond_where .= " AND f.`entry_time` >= '" . $start_time . "'";
      }
      if ($end_time) {
        $cond_where .= " AND f.`entry_time` <= '" . $end_time . "'";
      }
    }

    $entry_broker_id = isset($form_param['entry_broker_id']) ? intval($form_param['entry_broker_id']) : 0;
    if ($entry_broker_id) {
      $cond_where .= " AND f.`entry_broker_id` = '" . $entry_broker_id . "'";
    }
    $entry_agency_id = isset($form_param['entry_agency_id']) ? intval($form_param['entry_agency_id']) : 0;
    if ($entry_agency_id) {
      $cond_where .= " AND f.`entry_agency_id` = '" . $entry_agency_id . "'";
    }
    $entry_company_id = isset($form_param['entry_company_id']) ? intval($form_param['entry_company_id']) : 0;
    if ($entry_company_id) {
      $cond_where .= " AND f.`entry_company_id` = '" . $entry_company_id . "'";
    }
    return $cond_where;
  }

  //    应收应付
  public function should_list()
  {
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;

    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $this->contract_flow_model->set_tbl('contract_should_flow');

    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['entry_broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['entry_agency_id']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];
    //表单提交参数组成的查询条件
    $cond_where .= $this->_get_cond_str($post_param);
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, 'AND');
    $cond_where = trim($cond_where);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);
    //符合条件的总行数
    $this->_total_count = $this->contract_flow_model->count_by($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    if ($post_param['orderby_id'] == 1) {
      $order_key = 'collect_money';
      $order_by = 'ASC';
    } elseif ($post_param['orderby_id'] == 2) {
      $order_key = 'collect_money';
      $order_by = 'DESC';
    } elseif ($post_param['orderby_id'] == 3) {
      $order_key = 'pay_money';
      $order_by = 'ASC';
    } elseif ($post_param['orderby_id'] == 4) {
      $order_key = 'pay_money';
      $order_by = 'DESC';
    } else {
      $order_key = 'id';
      $order_by = 'DESC';
    }

    $list = $this->contract_flow_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_key, $order_by);
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        if (mb_strlen($val['remark'], 'UTF8') > 10) {
          $list[$key]['remark'] = mb_substr($val['remark'], 0, 9, 'utf-8') . '...';
        }
      }
    }
    $data['config'] = $this->contract_config_model->get_config();
    //收付总计
    $data['total'] = $this->contract_flow_model->get_total2($cond_where);

    $data['list'] = $list;
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page, //当前页数
      'list_rows' => $this->_limit, //每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    $data['page_title'] = '应收应付列表';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('contract/contract_should_flow', $data);
  }

//    实收实付
  public function actual_list()
  {

    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;

    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;


    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $this->contract_flow_model->set_tbl('contract_actual_flow');
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['entry_broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['entry_agency_id']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];
    //表单提交参数组成的查询条件
    $cond_where .= $this->_get_cond_str($post_param);
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, 'AND');
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_total_count = $this->contract_flow_model->count_by($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    if ($post_param['orderby_id'] == 1) {
      $order_key = 'collect_money';
      $order_by = 'ASC';
    } elseif ($post_param['orderby_id'] == 2) {
      $order_key = 'collect_money';
      $order_by = 'DESC';
    } elseif ($post_param['orderby_id'] == 3) {
      $order_key = 'pay_money';
      $order_by = 'ASC';
    } elseif ($post_param['orderby_id'] == 4) {
      $order_key = 'pay_money';
      $order_by = 'DESC';
    } else {
      $order_key = 'id';
      $order_by = 'DESC';
    }

    $list = $this->contract_flow_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_key, $order_by);
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        if (mb_strlen($val['remark'], 'UTF8') > 10) {
          $list[$key]['remark'] = mb_substr($val['remark'], 0, 9, 'utf-8') . '...';
        }
      }
    }
    $data['config'] = $this->contract_config_model->get_config();

    //收付总计
    $data['total'] = $this->contract_flow_model->get_total2($cond_where);

    $data['list'] = $list;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page, //当前页数
      'list_rows' => $this->_limit, //每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    $data['page_title'] = '实收实付列表';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('contract/contract_actual_flow', $data);
  }

  //实收实付审核
  public function should_review_list()
  {

    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $this->contract_flow_model->set_tbl('contract_should_flow');
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['entry_broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['entry_agency_id']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];

    //权限
    $should_review_per = $this->broker_permission_model->check('59');
    $should_fanreview_per = $this->broker_permission_model->check('60');
    $data['auth'] = array(
      'review' => $should_review_per, 'fanreview' => $should_fanreview_per
    );
    //表单提交参数组成的查询条件
    $cond_where .= $this->_get_cond_str($post_param);
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, 'AND');
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_total_count = $this->contract_flow_model->count_by($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    $list = $this->contract_flow_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_key = 'status', $order_by = 'ASC');
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        if (mb_strlen($val['remark'], 'UTF8') > 10) {
          $list[$key]['remark'] = mb_substr($val['remark'], 0, 9, 'utf-8') . '...';
        }
      }
    }
    $data['config'] = $this->contract_config_model->get_config();


    $data['list'] = $list;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page, //当前页数
      'list_rows' => $this->_limit, //每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    $data['page_title'] = '应收应付审核列表';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('contract/contract_should_review_list', $data);
  }

  //实收实付
  public function actual_review_list()
  {

    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : 0;
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $this->contract_flow_model->set_tbl('contract_actual_flow');

    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['entry_broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['entry_agency_id']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];

    //权限
    $actual_review_per = $this->broker_permission_model->check('65');
    $actual_fanreview_per = $this->broker_permission_model->check('66');
    $data['auth'] = array(
      'review' => $actual_review_per, 'fanreview' => $actual_fanreview_per
    );
    //表单提交参数组成的查询条件
    $cond_where .= $this->_get_cond_str($post_param);
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, 'AND');
    $cond_where = trim($cond_where);

    //符合条件的总行数
    $this->_total_count = $this->contract_flow_model->count_by($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    $list = $this->contract_flow_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_key = 'status', $order_by = 'ASC');
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        if (mb_strlen($val['remark'], 'UTF8') > 10) {
          $list[$key]['remark'] = mb_substr($val['remark'], 0, 9, 'utf-8') . '...';
        }
      }
    }
    $data['config'] = $this->contract_config_model->get_config();
    $data['list'] = $list;
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page, //当前页数
      'list_rows' => $this->_limit, //每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    $data['page_title'] = '实收实付审核列表';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('contract/contract_actual_review_list', $data);
  }

  /**
   * 收付审核
   */
  public function sure_review()
  {
    $flow_id = $this->input->post('flow_id');
    $review_type = intval($this->input->post('review_type'));
    $flow_type = trim($this->input->post('flow_type'));

    //合同配置项
    $config = $this->contract_config_model->get_config();
    $update_data = array(
      'status' => $review_type,
      'check_time' => time(),
      'check_agency_id' => $this->user_arr['agency_id'],
      'check_broker_id' => $this->user_arr['broker_id']
    );
    if ($flow_type == 'actual') {
      $this->contract_flow_model->set_tbl('contract_actual_flow');
    } else {
      $this->contract_flow_model->set_tbl('contract_should_flow');
    }
    //更改合同表中合同状态
    $result = $this->contract_flow_model->modify_data($flow_id, $update_data);
    //审核通过后发送消息
    if ($result) {
      //获得该收付信息
      $flow = $this->contract_flow_model->get_by_id($flow_id);
      //记入日志
      if ($review_type == 1) {
        if ($flow_type == 'actual') {
          $add_data = array(
            'c_id' => $flow['c_id'],
            'type_name' => "财务审核",
            'content' => "审核通过该实收实付，款类：{$config['money_type'][$flow['money_type']]}。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
        } else {
          $add_data = array(
            'c_id' => $flow['c_id'],
            'type_name' => "财务审核",
            'content' => "审核通过该应收应付，款类：{$config['money_type'][$flow['money_type']]}。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
        }
      } elseif ($review_type == 2) {
        if ($flow_type == 'actual') {
          $add_data = array(
            'c_id' => $flow['c_id'],
            'type_name' => "财务审核",
            'content' => "审核拒绝该实收实付，款类：{$config['money_type'][$flow['money_type']]}。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
        } else {
          $add_data = array(
            'c_id' => $flow['c_id'],
            'type_name' => "财务审核",
            'content' => "审核拒绝该应收应付，款类：{$config['money_type'][$flow['money_type']]}。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
        }
      }
      $this->contract_log_model->add_info($add_data);
      $return_data['result'] = 'ok';

      //操作日志
      $info = $this->contract_model->get_by_id($flow['c_id']);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      if ($flow_type == 'actual') {
        $add_log_param['text'] = '审核编号为' . $info['number'] . '的交易合同的实收实付。';
      } else {
        $add_log_param['text'] = '审核编号为' . $info['number'] . '的交易合同的应收应付。';
      }
      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
      $return_data['result'] = 'no';
    }
    echo json_encode($return_data);
  }

  /**
   * 合同审核
   */
  public function cancel_review()
  {
    $flow_id = $this->input->post('flow_id');
    $flow_type = $this->input->post('flow_type');
    $update_data = array(
      'status' => 0
    );
    if ($flow_type == 'actual') {
      $this->contract_flow_model->set_tbl('contract_actual_flow');
    } else {
      $this->contract_flow_model->set_tbl('contract_should_flow');
    }
    //更改合同表中合同状态
    $result = $this->contract_flow_model->modify_data($flow_id, $update_data);
    //审核通过后发送消息
    if ($result) {
      //获得该收付信息
      $flow = $this->contract_flow_model->get_by_id($flow_id);
      //记入日志
      if ($flow_type == 'actual') {
        $add_data = array(
          'c_id' => $flow['c_id'],
          'type_name' => "财务审核",
          'content' => "反审核操作该实收实付，款类：{$config['money_type'][$flow['money_type']]}。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
      } else {
        $add_data = array(
          'c_id' => $flow['c_id'],
          'type_name' => "财务审核",
          'content' => "反审核操作该应收应付，款类：{$config['money_type'][$flow['money_type']]}。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
      }
      $this->contract_log_model->add_info($add_data);
      $return_data['result'] = 'ok';

      //操作日志
      $info = $this->contract_model->get_by_id($flow['c_id']);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      if ($flow_type == 'actual') {
        $add_log_param['text'] = '反审核编号为' . $info['number'] . '的交易合同的实收实付。';
      } else {
        $add_log_param['text'] = '反审核编号为' . $info['number'] . '的交易合同的应收应付。';
      }
      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
      $return_data['result'] = 'no';
    }
    echo json_encode($return_data);
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
   * 导出实付实收数据
   * @author   wang
   */
  public function exportActual()
  {
    ini_set('memory_limit', '-1');
    $post_param = $this->input->post(NULL, true);
    $config = $this->contract_config_model->get_config();

    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['entry_broker_id'] = $this->user_arr['broker_id'];
    }

    //表单提交参数组成的查询条件
    $cond_where = $this->_get_cond_str($post_param);
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, 'AND');
    $cond_where = trim($cond_where);
    $this->contract_flow_model->set_tbl('contract_actual_flow');
    //符合条件的总行数
    $limit = $this->contract_flow_model->count_by($cond_where);

    if ($post_param['orderby_id'] == 1) {
      $order_key = 'collect_money';
      $order_by = 'ASC';
    } elseif ($post_param['orderby_id'] == 2) {
      $order_key = 'collect_money';
      $order_by = 'DESC';
    } elseif ($post_param['orderby_id'] == 3) {
      $order_key = 'pay_money';
      $order_by = 'ASC';
    } elseif ($post_param['orderby_id'] == 3) {
      $order_key = 'pay_money';
      $order_by = 'DESC';
    } else {
      $order_key = 'id';
      $order_by = 'DESC';
    }

    $productlist = $this->contract_flow_model->get_list_by_cond($cond_where, $this->_offset, $limit, $order_key, $order_by);
    $list = array();
    if (is_full_array($productlist)) {
      foreach ($productlist as $key => $value) {
        $list[$key]['signing_time'] = date('Y-m-d', $value['signing_time']);
        $list[$key]['type'] = $value['type'] == 1 ? '出售' : '出租';
        $list[$key]['number'] = $value['number'];
        $list[$key]['money_type'] = $config['money_type'][$value['money_type']];
        $list[$key]['collect_type'] = $config['collect_type'][$value['collect_type']];
        $list[$key]['collect_money'] = $value['collect_money'];
        $list[$key]['pay_type'] = $config['pay_type'][$value['pay_type']];
        $list[$key]['pay_money'] = $value['pay_money'];
        $list[$key]['flow_time'] = $value['flow_time'];
        $list[$key]['status'] = $config['flow_status'][$value['flow_status']];
      }
      $list = array_values($list);
    }
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '签约时间');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '交易类型');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '合同编号');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '款类');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '收方');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '实收金额(元)');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '付方');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '实付金额(元)');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '收付时间');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '状态');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['signing_time']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['type']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['number']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['money_type']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['collect_type']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['collect_money']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['pay_type']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['pay_money']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['flow_time']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['status']);
    }

    $fileName = 'shishoushifu' . strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('实收实付列表');
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
    exit;
  }

  /**
   * 导出应收应付数据
   * @author   wang
   */
  public function exportShould()
  {
    ini_set('memory_limit', '-1');
    $post_param = $this->input->post(NULL, true);
    $config = $this->contract_config_model->get_config();

    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['entry_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['entry_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['entry_broker_id'] = $this->user_arr['broker_id'];
    }

    //表单提交参数组成的查询条件
    $cond_where = $this->_get_cond_str($post_param);
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, 'AND');
    $cond_where = trim($cond_where);
    $this->contract_flow_model->set_tbl('contract_should_flow');
    //符合条件的总行数
    $limit = $this->contract_flow_model->count_by($cond_where);

    if ($post_param['orderby_id'] == 1) {
      $order_key = 'collect_money';
      $order_by = 'ASC';
    } elseif ($post_param['orderby_id'] == 2) {
      $order_key = 'collect_money';
      $order_by = 'DESC';
    } elseif ($post_param['orderby_id'] == 3) {
      $order_key = 'pay_money';
      $order_by = 'ASC';
    } elseif ($post_param['orderby_id'] == 3) {
      $order_key = 'pay_money';
      $order_by = 'DESC';
    } else {
      $order_key = 'id';
      $order_by = 'DESC';
    }

    $productlist = $this->contract_flow_model->get_list_by_cond($cond_where, $this->_offset, $limit, $order_key, $order_by);
    $list = array();
    if (is_full_array($productlist)) {
      foreach ($productlist as $key => $value) {
        $list[$key]['signing_time'] = date('Y-m-d', $value['signing_time']);
        $list[$key]['type'] = $value['type'] == 1 ? '出售' : '出租';
        $list[$key]['number'] = $value['number'];
        $list[$key]['money_type'] = $config['money_type'][$value['money_type']];
        $list[$key]['collect_type'] = $config['collect_type'][$value['collect_type']];
        $list[$key]['collect_money'] = $value['collect_money'];
        $list[$key]['pay_type'] = $config['pay_type'][$value['pay_type']];
        $list[$key]['pay_money'] = $value['pay_money'];
        $list[$key]['flow_time'] = $value['flow_time'];
        $list[$key]['status'] = $config['flow_status'][$value['flow_status']];
      }
      $list = array_values($list);
    }
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '签约时间');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '交易类型');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '合同编号');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '款类');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '收方');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '实收金额(元)');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '付方');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '实付金额(元)');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '收付时间');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '状态');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['signing_time']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['type']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['number']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['money_type']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['collect_type']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['collect_money']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['pay_type']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['pay_money']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['flow_time']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['status']);
    }

    $fileName = 'yingshouyingfu' . strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('应收应付列表');
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
    exit;
  }
}

/* End of file flow.php */
/* Location: ./application/mls/controllers/flow.php */
