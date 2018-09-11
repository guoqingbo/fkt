<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Customer_demand extends MY_Controller
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

  public function __construct()
  {
    parent::__construct();
    $this->load->model('customer_demand_model');
    $this->load->model('house_config_model');
  }


  /**
   * 根据表单提交参数，获取查询条件
   * @param array
   *
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = " is_check = 2 and status =1";
    //姓名
    if (isset($form_param['realname']) && !empty($form_param['realname'])) {
      $cond_where .= " and realname LIKE '%" . $form_param['realname'] . "%'";
    }
    //电话
    if (isset($form_param['phone']) && !empty($form_param['phone'])) {
      $cond_where .= " and phone LIKE '%" . $form_param['phone'] . "%'";
    }
    //区属
    if (isset($form_param['district_id']) && !empty($form_param['district_id'])) {
      $cond_where .= " and district_id =" . $form_param['district_id'];
    }
    //户型
    if (isset($form_param['room']) && !empty($form_param['room'])) {
      if ($form_param['room'] <= 5) {
        $cond_where .= " and room =" . $form_param['room'];
      } else {
        $cond_where .= " and room >=" . $form_param['room'];
      }
    }
    //最低面积
    if (isset($form_param['larea']) && !empty($form_param['larea']) && is_numeric($form_param['larea'])) {
      $cond_where .= " and larea >= " . $form_param['larea'];
    }
    //最高面积
    if (isset($form_param['harea']) && !empty($form_param['harea']) && is_numeric($form_param['harea'])) {
      $cond_where .= " and harea <=" . $form_param['harea'];
    }
    //最低价格
    if (isset($form_param['lprice']) && !empty($form_param['lprice']) && is_numeric($form_param['lprice'])) {
      $cond_where .= " and lprice >= " . $form_param['lprice'];
    }
    //最高价格
    if (isset($form_param['hprice']) && !empty($form_param['hprice']) && is_numeric($form_param['hprice'])) {
      $cond_where .= " and hprice <=" . $form_param['hprice'];
    }
    $cond_where = trim($cond_where);
    return $cond_where;
  }

  public function seek_buy($type = "")
  {
    if ($type) {
      $this->_seek_buy_grab();
    } else {
      $this->_seek_buy();
    }
  }

  private function _seek_buy()
  {
    $data = array();
    $data['page_title'] = "客户需求";
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;

    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    $cond_where = $this->_get_cond_str($post_param);
    $cond_where .= " and seek_sell.id not in (select grab_house.ent_id from grab_house where grab_house.broker_id = {$this->user_arr['broker_id']} and grab_house.type = 3)";
    //符合条件的总行数
    $this->_total_count =
      $this->customer_demand_model->count_by($cond_where, $tbl = "seek_sell");

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->customer_demand_model->get_all_by($cond_where, $tbl = "seek_sell", $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        $list[$key]['ctime'] = date('Y-m-d H:i:s', $val['ctime']);
      }
    }
    $data['list'] = $list;
    //今天增加房源数
    $data['today_total'] = $this->customer_demand_model->get_today_total_num($tbl = 'seek_sell');
    //意向区属
    $data['district'] = $this->customer_demand_model->get_all_district();

    $result = $this->house_config_model->get_config();

    //期望户型
    $result['room'][6] = "五室以上";
    $data['hope_room'] = $result['room'];
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
    $data['page'] = $page;
    //加载css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/market.css');
    //加载js
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/upload_wei.js,mls/js/v1.0/cooperate_common.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //加载foot_js
    $data['foot_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('marketing_center/customer_demand/seek_buy', $data);
  }

  private function _seek_buy_grab()
  {
    $data = array();
    $data['page_title'] = "客户需求";
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;

    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    $cond_where = $this->_get_cond_str($post_param);
    $cond_where .= " and seek_sell.id in (select grab_house.ent_id from grab_house where grab_house.broker_id = {$this->user_arr['broker_id']} and grab_house.type = 3)";
    //符合条件的总行数
    $this->_total_count =
      $this->customer_demand_model->count_by($cond_where, $tbl = "seek_sell");

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->customer_demand_model->get_all_by($cond_where, $tbl = "seek_sell", $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        $list[$key]['ctime'] = date('Y-m-d H:i:s', $val['ctime']);
      }
    }
    $data['list'] = $list;
    //今天增加房源数
    $data['today_total'] = $this->customer_demand_model->get_today_total_num($tbl = 'seek_sell');
    //意向区属
    $data['district'] = $this->customer_demand_model->get_all_district();

    $result = $this->house_config_model->get_config();

    //期望户型
    $result['room'][6] = "五室以上";
    $data['hope_room'] = $result['room'];
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

    //加载css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/market.css');
    //加载js
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/upload_wei.js,mls/js/v1.0/cooperate_common.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //加载foot_js
    $data['foot_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('marketing_center/customer_demand/seek_buy_grab', $data);
  }

  public function seek_rent($type = "")
  {
    if ($type) {
      $this->_seek_rent_grab();
    } else {
      $this->_seek_rent();
    }
  }

  private function _seek_rent()
  {
    $data = array();
    $data['page_title'] = "客户需求";
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $cond_where = $this->_get_cond_str($post_param);
    $cond_where .= " and seek_rent.id not in (select grab_house.ent_id from grab_house where grab_house.broker_id = {$this->user_arr['broker_id']} and grab_house.type = 4)";

    //符合条件的总行数
    $this->_total_count =
      $this->customer_demand_model->count_by($cond_where, $tbl = "seek_rent");

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->customer_demand_model->get_all_by($cond_where, $tbl = "seek_rent", $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        $list[$key]['ctime'] = date('Y-m-d H:i:s', $val['ctime']);
      }
    }
    $data['list'] = $list;
    //今天增加房源数
    $data['today_total'] = $this->customer_demand_model->get_today_total_num($tbl = 'seek_rent');
    //意向区属
    $data['district'] = $this->customer_demand_model->get_all_district();
    $result = $this->house_config_model->get_config();

    //期望户型
    $result['room'][6] = "五室以上";
    $data['hope_room'] = $result['room'];

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
    $data['page'] = $page;
    //加载css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/market.css');
    //加载js
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/upload_wei.js,mls/js/v1.0/cooperate_common.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //加载foot_js
    $data['foot_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('marketing_center/customer_demand/seek_rent', $data);
  }


  private function _seek_rent_grab()
  {
    $data = array();
    $data['page_title'] = "客户需求";
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $cond_where = $this->_get_cond_str($post_param);
    $cond_where .= " and seek_rent.id in (select grab_house.ent_id from grab_house where grab_house.broker_id = {$this->user_arr['broker_id']} and grab_house.type = 4)";

    //符合条件的总行数
    $this->_total_count =
      $this->customer_demand_model->count_by($cond_where, $tbl = "seek_rent");

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->customer_demand_model->get_all_by($cond_where, $tbl = "seek_rent", $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        $list[$key]['ctime'] = date('Y-m-d H:i:s', $val['ctime']);
      }
    }
    $data['list'] = $list;
    //今天增加房源数
    $data['today_total'] = $this->customer_demand_model->get_today_total_num($tbl = 'seek_rent');
    //意向区属
    $data['district'] = $this->customer_demand_model->get_all_district();
    $result = $this->house_config_model->get_config();

    //期望户型
    $result['room'][6] = "五室以上";
    $data['hope_room'] = $result['room'];

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

    //加载css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/market.css');
    //加载js
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/upload_wei.js,mls/js/v1.0/cooperate_common.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //加载foot_js
    $data['foot_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('marketing_center/customer_demand/seek_rent_grab', $data);
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

  //获取查看信息
  public function get_details($id, $tbl)
  {
    $isajax = $this->input->post('isajax', TRUE);
    if ($isajax) {
      $list = $this->customer_demand_model->get_list_by($id, $tbl);
      echo json_encode($list);
    }
  }

  //检查当前页是否有数据，如果没有则刷新
  public function check_list()
  {
    // 分页参数

    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $tbl = $this->input->post('tbl');
    $type = $this->input->post('type');
    $this->_init_pagination($page);

    $cond_where = $this->_get_cond_str($post_param);
    $cond_where .= " and {$tbl}.id not in (select grab_house.ent_id from grab_house where grab_house.broker_id = {$this->user_arr['broker_id']} and grab_house.type = {$type})";
    //获取列表内容
    $list = $this->customer_demand_model->get_all_by($cond_where, $tbl, $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      echo 1;
    } else {
      echo 0;
    }
  }
}
