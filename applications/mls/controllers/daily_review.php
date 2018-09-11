<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Daily_review extends MY_Controller
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
    $this->load->model('broker_daily_model');
    $this->load->model('broker_info_model');
    $this->load->model('agency_model');
  }

  public function index()
  {
    $broker_info = $this->user_arr;
    $broker_id = $this->user_arr['broker_id'];
    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $agency_id = intval($broker_info['agency_id']);//获取总公司编号
    $data['company_id'] = $company_id;
    //获取当前经纪人在官网注册时的公司和门店名
    $register_info = $this->broker_info_model->get_register_info_by_brokerid(intval($broker_info['id']));
    $data['register_info'] = $register_info;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //根据权限role_id获得当前经纪人的角色，判断店长
    $role_level = intval($this->user_arr['role_level']);
    //店长
    if (is_int($role_level) && $role_level == 6) {
      $agency = $this->agency_model->get_by_id($agency_id);
      $agency_name = $dist_street['name'];
      $data['agency_list'] = array(
        array(
          'agency_id' => $agency['id'],
          'agency_name' => $agency['name']
        )
      );
      if ($post_param['post_agency_id']) {  //根据门店编号获取经纪人列表数组
        $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($post_param['post_agency_id']);
      }
      //店长以上的获取全部分公司信息
    } else {
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_review_daily');
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
    //在没有post_broker_id时，应该设置为'0'
    if (!isset($post_param['post_broker_id'])) {
      //$post_param['post_broker_id'] = $this->user_arr['broker_id'];
      $post_param['post_broker_id'] = '0';
    }
    if (!isset($post_param['post_agency_id'])) {
      $post_param['post_agency_id'] = $this->user_arr['agency_id'];
    }
    //默认公司
    $post_param['post_company_id'] = $this->user_arr['company_id'];
    $data['post_param'] = $post_param;
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);
    //查询房源条件
    $cond_where = "create_time > 0";
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;
    //符合条件的总行数
    $this->_total_count =
      $this->broker_daily_model->count_by($cond_where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->broker_daily_model->get_all_by($cond_where, $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      foreach ($list as $k => $v) {
        $broker = $this->broker_info_model->get_by_broker_id($v['broker_id']);
        $list[$k]['truename'] = $broker['truename'];
      }
    }
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
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '工作日报列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    //print_r($data);
    $this->view('uncenter/my_daily/daily_review', $data);
  }

  //获取日志详情
  public function find_daily($id)
  {
    if (intval($id) <= 0) {
      return false;
    }
    $cond_where = 'id = ' . $id . ' AND company_id = ' . $this->user_arr['company_id'];
    //根据权限role_id获得当前经纪人的角色，判断店长
    $role_level = intval($this->user_arr['role_level']);
    //店长
    if (is_int($role_level) && $role_level == 6) {
      $cond_where .= " AND agency_id = " . $this->user_arr['agency_id'];
      //店长以上的获取全部分公司信息
    } else {
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_review_daily');
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
    }
    $daily = $this->broker_daily_model->get_one_by($cond_where);
    if (is_full_array($daily) && $daily['comment_broker_id'] > 0) {
      $this->load->model('broker_info_model');
      $broker = $this->broker_info_model->get_by_broker_id($daily['comment_broker_id']);
      $daily['broker'] = $broker;
    }
    $data['daily'] = $daily;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_contract.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    $this->view('uncenter/my_daily/review_details', $data);
  }


  //评论
  public function review()
  {
    $id = $this->input->post('id', true);
    $comment = $this->input->post('comment', true);
    if (intval($id) <= 0 || trim($comment) == '') {
      return false;
    }
    $cond_where = 'id = ' . $id;
    //根据权限role_id获得当前经纪人的角色，判断店长
    $role_level = intval($this->user_arr['role_level']);
    //店长
    if (is_int($role_level) && $role_level == 6) {
      $cond_where .= " AND agency_id = " . $this->user_arr['agency_id'];
    } else {
      //店长以上的获取全部分公司信息
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_review_daily');
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
    }
    $update_set = array('comment' => $comment, 'comment_broker_id' => $this->user_arr['broker_id']);
    $update_rows = $this->broker_daily_model->update_comment_by($cond_where, $update_set);
    if ($update_rows > 0) {
      $result = array('status' => 1);
    } else {
      $result = array('status' => 2);
    }
    echo json_encode($result);
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
    //时间条件
    if (isset($form_param['start_date_begin']) && $form_param['start_date_begin']) {
      $start_time = strtotime($form_param['start_date_begin'] . " 00:00:00");
      $cond_where .= " AND create_time >= '" . $start_time . "'";
    }

    if (isset($form_param['start_date_end']) && $form_param['start_date_end']) {
      $end_time = strtotime($form_param['start_date_end'] . " 23:59:59");
      $cond_where .= " AND create_time <= '" . $end_time . "'";
    }
    if (isset($form_param['comment']) && $form_param['comment']) {
      if ($form_param['comment'] == 1) {
        $cond_where .= " AND comment_broker_id = 0";
      } else {
        $cond_where .= " AND comment_broker_id > 0";
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
}

