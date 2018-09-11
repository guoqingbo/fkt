<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-个人记事本
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Company_employee extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'hz';

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

  public function __construct()
  {
    parent::__construct();
    $this->load->model('company_employee_model');
    $this->load->model('agency_model');
  }

  /**
   * 员工通讯录
   *
   * @access public
   * @return void
   */
  public function index()
  {
    $broker_id = $this->user_arr['broker_id'];
    $broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
    $b_id = $broker_info['broker_id'];
    $c_id = $broker_info['company_id'];
    $data['user_menu'] = $this->user_menu;

    //新权限
    $view_other_per_data = $this->broker_permission_model->check('1');
    $view_other_per = $view_other_per_data['auth'];
    $data['view_other_per'] = $view_other_per;
    $data['agency_id'] = $broker_info['agency_id'];//经纪人门店编号
    $this_agency_data = $this->agency_model->get_by_id(intval($broker_info['agency_id']));
    if (is_full_array($this_agency_data)) {
      $data['agency_name'] = $this_agency_data['name'];//获取经纪人所对应门店的名称
    }

    //根据数据范围，获得门店数据
    $this->load->model('agency_permission_model');
    $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_view_house');
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

    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $this->_init_pagination($page);


    //表单提交参数组成的查询条件
    $store_name = $this->input->post('store_name');
    if ($store_name && $store_name !== "no") {
      $cond_where .= " and a.name LIKE '%" . $store_name . "%'";
    }
    //查询消息的条件
    if ($c_id == "0") {
      $cond_where = " where b.broker_id = {$b_id} ";
    } elseif ($c_id > 0) {
      //查询消息的条件
      $cond_where = " where b.company_id = {$c_id} ";
      //表单提交参数组成的查询条件
      $store_name = $this->input->post('store_name');
      if (!$view_other_per) {
        $store_name = $data['agency_name'];
      }
      if ($store_name && $store_name !== "no") {
        $cond_where .= " and a.name LIKE '%" . $store_name . "%'";
      }
      $e_name = $this->input->post('e_name');
      if ($e_name) {
        $cond_where .= " and b.truename LIKE '%" . $e_name . "%'";
      }
      $tel = $this->input->post('tel');
      if ($tel) {
        $cond_where .= " and b.phone LIKE '%" . $tel . "%'";
      }
    }
    //符合条件的总行数
    $this->_total_count =
      $this->company_employee_model->count_by($cond_where);

    $cond_where = $cond_where . " order by b.agency_id ASC,p.system_group_id ASC ";


    //如果没有门店挂靠获取注册信息表里面的门店名称
    if ($c_id == "0") {
      $this->load->model('blacklist_model');
      $agency_name = $this->blacklist_model->get_register_broker_by_id($broker_info['id']);
      $data['storename'] = $agency_name['storename'];
    }

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->company_employee_model->get_all_by($c_id, $cond_where, $this->_offset, $this->_limit);

    $agency = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

    $data['list'] = $list;
    $data['store_name'] = $store_name;
    $data['e_name'] = $e_name;
    $data['tel'] = $tel;
    $data['agency'] = $agency;
    $data['company_id'] = $c_id;
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
    $data['page_title'] = '员工通讯录';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/house_new.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js');

    $this->view('company_employee/employee_contents', $data);
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
   * 归属公司详情
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function company_details()
  {
    $city_name = $this->user_arr['cityname'];
    $broker_id = $this->user_arr['broker_id'];
    //详情信息
    $broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
    if (!$broker_info['company_id']) {
      $id = $broker_info['agency_id'];
    } else {
      $id = $broker_info['company_id'];
    }
    $data = $this->company_employee_model->get_by_id($id);
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $data['city'] = $city_name;
    //页面标题
    $data['page_title'] = '归属公司信息';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js');

    $this->view('company_employee/show_company', $data);
  }

  /**
   * 员工基本工资设置
   * @access public
   * @return void
   */
  public function employee_base_salary()
  {
    $broker_id = $this->user_arr['broker_id'];
    $broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
    $agency_id = $broker_info['agency_id'];
    $company_id = $broker_info['company_id'];
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $this->_init_pagination($page);

    //查询条件
    $cond_where = " where b.company_id = {$company_id} ";

    //表单提交参数组成的查询条件
    $store_name = $this->input->post('store_name');
    if ($store_name) {
      $cond_where .= " and a.name LIKE '%" . $store_name . "%'";
    }
    $e_name = $this->input->post('e_name');
    if ($e_name) {
      $cond_where .= " and b.truename LIKE '%" . $e_name . "%'";
    }
    //符合条件的总行数
    $this->_total_count =
      $this->company_employee_model->count_by($cond_where);

    $cond_where = $cond_where . " order by agency_id ASC ";
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->company_employee_model->get_all_by($cond_where, $this->_offset, $this->_limit);

    $data['list'] = $list;
    $data['store_name'] = $store_name;
    $data['e_name'] = $e_name;

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
    $data['page_title'] = '员工基础工资';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js');

    $this->view('company_employee/employee_base_salary', $data);
  }

  public function modify_salary()
  {
    $broker_id = $this->input->post('broker_id');

    $base_salary = $this->input->post('base_salary');
    $rs = $this->company_employee_model->modify_salary($broker_id, $base_salary);
    if ($rs) {
      $url_manage = MLS_URL . '/company_employee/employee_base_salary';
      $page_text = '添加成功';
    } else {
      $url_manage = MLS_URL . '/company_employee/employee_base_salary';
      $page_text = '添加失败';
    }
    $this->jump($url_manage, $page_text, 1000);
  }

  public function salary()
  {
    $this->load->model('statistic_analysis_model');
    //post参数
    $data['store_name'] = $this->input->post('store_name');
    $data['e_name'] = $this->input->post('e_name');
    $count_year = $this->input->post('count_year');
    $count_month = $this->input->post('count_month');
    $now_year = date('Y');
    $now_month = date('m');
    $data['count_year'] = $count_year ? $count_year : $now_year;
    $data['count_month'] = $count_month ? $count_month : $now_month;
    $data['now_year'] = $now_year;
    $day_num = date('t', strtotime($data['count_year'] . "-" . $data['count_month'] . "-01"));//选中月份的天数
    $start_time = mktime(0, 0, 0, $data['count_month'], 1, $data['count_year']);//选中月份，当月1日零点
    $end_time = mktime(23, 59, 59, $data['count_month'], $day_num, $data['count_year']);//选中月份当月最后一日23点59分59秒
    $data['show_date'] = $data['count_year'] . '-' . $data['count_month'];
    $broker_id = $this->user_arr['broker_id'];
    $broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
    $agency_id = $broker_info['agency_id'];
    $company_id = $broker_info['company_id'];
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;

    //查询条件
    $cont_where = " where b.company_id = {$company_id} ";
    $where = "a.company_id = {$company_id} ";
    if ($data['store_name']) {
      $where .= " AND b.name LIKE '%" . $data['store_name'] . "%'";
      $cont_where .= " AND a.name LIKE '%" . $data['store_name'] . "%'";
    }
    if ($data['e_name']) {
      $where .= " AND a.truename LIKE '%" . $data['e_name'] . "%'";
      $cont_where .= " AND b.truename LIKE '%" . $data['e_name'] . "%'";
    }
    //符合条件的总行数
    $this->_total_count =
      $this->company_employee_model->count_by($cont_where);
    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $this->_init_pagination($page);
    //获取提成信息
    //先联表获取经纪人信息和买卖业绩，并根据总业绩排序
    $files = "a.id,a.base_salary,a.broker_id,a.truename,b.name";
    $order_key = 'a.agency_id';
    $order_by = 'ASC';
    $rows = $this->statistic_analysis_model->get_broker_info($files, $where, $this->_offset, $this->_limit, $order_key, $order_by);

    foreach ($rows as $k => $v) {
      //添加买卖提成
      $sell_file = "sum(c.price) sell_price";
      $sell_where = "d.type = 1 AND c.broker_id = " . $v['broker_id'] . " AND d.completed_time >= " . $start_time . " AND d.completed_time <= " . $end_time;
      $sell_price = $this->statistic_analysis_model->get_broker_commission($sell_file, $sell_where, '', '', '', '');
      $rows[$k]['sell_price'] = isset($sell_price[0]['sell_price']) ? $sell_price[0]['sell_price'] : '';
      //添加租赁提成
      $rent_file = "sum(c.price) rent_price";
      $rent_where = "d.type = 2 AND c.broker_id = " . $v['broker_id'] . " AND d.completed_time >= " . $start_time . " AND d.completed_time <= " . $end_time;
      $rent_price = $this->statistic_analysis_model->get_broker_commission($rent_file, $rent_where, '', '', '', '');
      $rows[$k]['rent_price'] = isset($rent_price[0]['rent_price']) ? $rent_price[0]['rent_price'] : '';
    }
    $data['list'] = $rows;

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
    $data['page_title'] = '员工工资';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js');

    $this->view('company_employee/employee_salary', $data);
  }


  //获取备注信息
  public function get_remark($id)
  {
    $isajax = $this->input->get('isajax', TRUE);
    if ($isajax) {
      $broker_id = $this->user_arr['broker_id'];
      $broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
      $c_id = $broker_info['company_id'];
      $list = $this->company_employee_model->get_remark_by($id, $c_id);
      if ($list) {
        $result = array('result' => 'ok', 'list' => $list);
      } else {
        $result = array('result' => 'no');
      }
      echo json_encode($result);
    }
  }

  //更新备注信息
  public function update_remark()
  {
    $this_user = $this->user_arr;
    $broker_id = $this_user['broker_id'];
    $id = $this->input->post('id', true);
    $remarker_id = $this->input->post('remarker_id', true);
    $remark = $this->input->post('remark', true);
    if ($id == "") {
      $remark_data = $this->company_employee_model->insert_remark($broker_id, $remarker_id, $remark);
    } else {
      $remark_data = $this->company_employee_model->update_remark($id, $remark);
    }
    echo $remark_data;
  }
}
