<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Data_transfer extends MY_Controller
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
    //加载区属模型类
    $this->load->model('district_model');
    //加载楼盘模型类
    //$this->load->model('community_model');
    //表单验证
    //$this->load->library('form_validation');
    //加载客户MODEL
    $this->load->model('rent_house_model');
    $this->load->model('sell_house_model');
    //加载房源标题模板类
    //$this->load->model('house_title_template_model');
    $this->load->model('house_config_model');
    $this->load->model('broker_model');
    //$this->load->model('house_collect_model');
    $this->load->model('api_broker_model');
    //$this->load->model('follow_model');
    //$this->load->model('report_model');

    //求购模版
    $this->load->helper('customer');
    $this->load->model('buy_customer_model');
    $this->load->model('rent_customer_model');


    //员工信息模版
    $this->load->model('broker_info_model');
    $this->load->model('operate_log_model');
    $this->load->model('agency_model');

    //合作模版
    $this->load->model('cooperate_model');
    if (is_full_array($this->user_arr)) {
      //门店关联权限
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
    }
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


    //范围(门店)
    if (isset($form_param['store_name_out']) && $form_param['store_name_out'] > 0) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $agency_id = intval($form_param['store_name_out']);
      $cond_where .= "agency_id = '" . $agency_id . "'";
    }

    //范围（经纪人）
    if (isset($form_param['broker_id']) && $form_param['broker_id'] > 0) {
      $broker_id = intval($form_param['broker_id']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "broker_id = '" . $broker_id . "'";
    }

    if ($form_param['type'] == "sell" || $form_param['type'] == "rent") {
      //物业类型条件出售出租
      if (isset($form_param['property_type']) && !empty($form_param['property_type']) && $form_param['property_type'] > 0) {
        $sell_type = intval($form_param['property_type']);
        $cond_where .= !empty($cond_where) ? ' AND ' : '';
        $cond_where .= "sell_type = '" . $sell_type . "'";
      }

      //板块 ，区属出售出租
      if (isset($form_param['dist_id']) && $form_param['dist_id'] > 0) {
        $district = intval($form_param['dist_id']);
        $cond_where .= !empty($cond_where) ? ' AND ' : '';
        $cond_where .= "district_id = '" . $district . "'";
        if ($street > 0) {
          $cond_where .= " AND street_id = '" . $street . "'";
        }

      }

      //楼盘ID出售出租
      if (!empty($form_param['cmt_name']) && $form_param['cmt_id'] > 0) {
        $cond_where .= !empty($cond_where) ? ' AND ' : '';
        $cond_where .= "block_id = '" . $form_param['cmt_id'] . "'";
      }
    }

    if ($form_param['type'] == "buy_customer" || $form_param['type'] == "rent_customer") {
      //物业类型条件求购求租
      if (isset($form_param['property_type']) &&
        !empty($form_param['property_type']) && $form_param['property_type'] > 0
      ) {
        $property_type = intval($form_param['property_type']);
        $cond_where .= !empty($cond_where) ? ' AND ' : '';
        $cond_where .= "property_type = '" . $property_type . "'";
      }

      //区属、板块条件求购求租
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

      //楼盘参数求购求租
      if (isset($form_param["cmt_id"]) && $form_param['cmt_id'] > 0) {
        $cmt_id = intval($form_param["cmt_id"]);
        $cond_where .= !empty($cond_where) ? ' AND ' : '';
        $cond_where .= "(cmt_id1 = '" . $cmt_id . "' "
          . " OR cmt_id2 = '" . $cmt_id . "'"
          . " OR cmt_id3 = '" . $cmt_id . "')";
      }
    }

    //状态条件
    /*if (isset($form_param['status']) && !empty($form_param['status']) && $form_param['status'] > 0)
    {
        $status = intval($form_param['status']);
        $cond_where .= !empty($cond_where) ?  ' AND ' : '';
        $cond_where .= "status = '".$status."'";
    } else if($form_param['status'] == 'test'){
        $cond_where .= !empty($cond_where) ?  ' AND ' : '';
        $cond_where .= "status IN (1,2,3,4)";
    }
    else
    {
        $cond_where .= !empty($cond_where) ?  ' AND ' : '';
        $cond_where .= "status = '1' ";
    }*/


    //转出经纪人搜索条件添加
    if (isset($form_param['broker_id_out']) && !empty($form_param['broker_id_out']) && $form_param['broker_id_out'] > 0) {
      $broker_id = intval($form_param['broker_id_out']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "broker_id = '" . $broker_id . "'";
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
        $arr_order['order_key'] = 'price';
        $arr_order['order_by'] = 'ASC';
        break;
      case 6:
        $arr_order['order_key'] = 'price';
        $arr_order['order_by'] = 'DESC';
        break;
      default:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
    }

    return $arr_order;
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


  public function index()
  {

    $type = $this->input->post('type', true);
    if ($type == "") {

      $type = "none";

    }

    switch ($type) {
      case "none":
        $this->_index();
        break;
      case "sell":
        $this->_sellzy(1);
        break;
      case "rent":
        $this->_rentzy(1);
        break;

      case "buy_customer":
        $this->_buy_customer(1);

        break;
      case "rent_customer":
        $this->_rent_customer(1);
        break;
    }

  }

  //初始页面
  public function _index()
  {
    //模板使用数据
    $data = array();
    $broker_info = $this->user_arr;

    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //获取默认查询时间
    $buy_customer_query_time = $company_basic_data['buy_customer_query_time'];

    //页面菜单
    $data['user_menu'] = $this->user_menu;


    //post参数
    $post_param = $this->input->post(NULL, TRUE);

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


    //获取当前经纪人所在门店的数据范围
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_data_transfer');
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
    $data['agency'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

    //$cond_where = "id > 0 ";
    //基本设置默认查询时间
    //半年
    /*if('1'==$buy_customer_query_time){
            $half_year_time = intval(time()-365*0.5*24*60*60);
            $cond_where .= " AND creattime>= '".$half_year_time."' ";
        }
        //一年
        if('2'==$buy_customer_query_time){
            $one_year_time = intval(time()-365*24*60*60);
            $cond_where .= " AND creattime>= '".$one_year_time."' ";
        }*/

    //页面标题
    $data['page_title'] = '初始页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/cal.css,'
      . 'mls/css/v1.0/system_set.css,'
      . 'mls/css/v1.0/house_new.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js,mls/js/v1.0/cal.js,'
      . 'mls/js/v1.0/shuifei.js,'
      . 'mls/js/v1.0/get_store.js');
    //表单数据
    $data['post_param'] = $post_param;

    //如果后几个搜搜参数被选中则，后面的选择参数全部显示
    if (!empty($post_param['property_type']) || !empty($post_param['room']) ||
      !empty($post_param['public_type']) || !empty($post_param['status']) ||
      !empty($post_param['is_share'])
    ) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
    }

    //加载发布页面模板

    $this->view("/agency/data_transfer/_index", $data);


  }

  //出售页面
  public function _sellzy($page = 1)
  {
    //遗留 判断是否登录
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //模板使用数据
    $data = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //获取房源默认排序字段
    $house_list_order_field = $company_basic_data['house_list_order_field'];
    //获取默认查询时间
    $sell_house_query_time = $company_basic_data['sell_house_query_time'];
    //页面菜单
    $data['user_menu'] = $this->user_menu;

    $data['agency_id'] = $broker_info['agency_id'];//经纪人门店编号
    $data['agency_name'] = $broker_info['agency_name'];//获取经纪人所对应门店的名称

    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //默认状态为有效
    /*if(!isset($post_param['status'])){
        $post_param['status'] = 1;
    }*/

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

    //获取当前经纪人所在门店的数据范围
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_data_transfer');
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
    $data['agency'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

    //查询房源条件
    $cond_where = "is_public = 0 ";
    //基本设置默认查询时间
    //半年
    /*if('1'==$sell_house_query_time){
        $half_year_time = intval(time()-365*0.5*24*60*60);
        $cond_where .= " AND createtime>= '".$half_year_time."' ";
    }
    //一年
    if('2'==$sell_house_query_time){
        $one_year_time = intval(time()-365*24*60*60);
        $cond_where .= " AND createtime>= '".$one_year_time."' ";
    }*/
    //搜索所属总公司全部
    $cond_where .= " AND company_id = {$company_id}";

    //表单提交参数组成的查询条件
    //print_r($post_param);
    $cond_where_ext = $this->_get_cond_str($post_param);
    if ($cond_where_ext) {
      $cond_where .= ' AND ' . $cond_where_ext;
    }

    if ('none' == $post_param['store_name_out']) {
      if (!empty($all_access_agency_ids)) {
        //查询房源条件
        $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";
      }
    }
    //$cond_where .= !empty($cond_where_ext) ? ' AND '.$cond_where_ext : $cond_where_ext;
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
      $this->sell_house_model->get_count_by_cond($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $data['pages'] = $pages;
    //获取列表内容
    $list = $this->sell_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    $this->sell_house_model->set_search_fields(array('id'));
    $sell_list_all = $this->sell_house_model->get_list_by_cond($cond_where, $this->_offset, 0, $order_arr['order_key'], $order_arr['order_by']);
    foreach ($sell_list_all as $k => $v) {
      $sell_id_all[] = $v['id'];
    }
    if ($sell_id_all) {
      $sell_id_all_string = implode(",", $sell_id_all);
      //print_r($sell_id_all_string);
      $data['sell_id_all_string'] = $sell_id_all_string;
    }


    //房源id数组
    $house_id_arr = array();

    $this->load->model('api_broker_model');
    $brokeridstr = '';
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
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '出售房源列表页';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/cal.css,'
      . 'mls/css/v1.0/system_set.css,'
      . 'mls/css/v1.0/house_new.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js,mls/js/v1.0/cal.js,'
      . 'mls/js/v1.0/shuifei.js,'
      . 'mls/js/v1.0/get_store.js');
    $data['post_param'] = $post_param;

    //如果后几个搜搜参数被选中则，后面的选择参数全部显示
    if (!empty($post_param['property_type']) || !empty($post_param['room']) ||
      !empty($post_param['public_type']) || !empty($post_param['status']) ||
      !empty($post_param['is_share'])
    ) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
    }

    $this->view("/agency/data_transfer/_sellzy", $data);
  }


  //出租页面
  private function _rentzy($page = 1)
  {
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    //使用模板数据
    $data = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //获取房源默认排序字段
    $house_list_order_field = $company_basic_data['house_list_order_field'];
    //获取默认查询时间
    $rent_house_query_time = $company_basic_data['rent_house_query_time'];

    //菜单
    $data['user_menu'] = $this->user_menu;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //$blockname = $this->input->post('blockname',true);
    //默认状态为有效
    /*if(!isset($post_param['status'])){
        $post_param['status'] = 1;
    }*/

    //根据查看房源权限获取门店与经济人相关信息
    $borker_id_arr = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    $agency_id = $borker_id_arr['agency_id'];//经纪人门店编号
    $agency_id = intval($broker_info['agency_id']);//经纪人门店编号
    $data['agency_id'] = $agency_id;
    $agency_name = $this->api_broker_model->get_by_agency_id($agency_id);
    $data['agency_name'] = $agency_name['name'];//获取经纪人所对应门店的名称
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    //获取区属
    $this->load->model('district_model');
    $district = $this->district_model->get_district();

    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }

    //获取板块
    $street = $this->district_model->get_street();

    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

    //获取当前经纪人所在门店的数据范围
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_data_transfer');
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
    $data['agency'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

    //查询房源条件
    $cond_where = "is_public = 0 ";
    //基本设置默认查询时间
    //半年
    /*if('1'==$rent_house_query_time){
        $half_year_time = intval(time()-365*0.5*24*60*60);
        $cond_where .= " AND createtime>= '".$half_year_time."' ";
    }
    //一年
    if('2'==$rent_house_query_time){
        $one_year_time = intval(time()-365*24*60*60);
        $cond_where .= " AND createtime>= '".$one_year_time."' ";
    }*/
    //搜索所属总公司全部
    $cond_where .= " AND company_id = {$company_id} ";

    //表单提交参数组成的查询条件
    //print_r($post_param);
    $cond_where_ext = $this->_get_cond_str($post_param);
    if ($cond_where_ext) {
      $cond_where .= ' AND ' . $cond_where_ext;
    }
    if ('none' == $post_param['store_name_out']) {
      if (!empty($all_access_agency_ids)) {
        //查询房源条件
        $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";
      }
    }

    //排序字段
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
      $this->rent_house_model->get_count_by_cond($cond_where);

    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $data['pages'] = $pages;

    //获取出租信息详细表
    $list = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    $this->rent_house_model->set_search_fields(array('id'));
    $rent_list_all = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, 0, $order_arr['order_key'], $order_arr['order_by']);
    foreach ($rent_list_all as $k => $v) {
      $rent_id_all[] = $v['id'];
    }
    if ($rent_id_all) {
      $rent_id_all_string = implode(",", $rent_id_all);
      //print_r($rent_id_all_string);
      $data['rent_id_all_string'] = $rent_id_all_string;
    }
    //房源id数组
    $house_id_arr = array();


    $this->load->model('api_broker_model');
    $brokeridstr = '';

    if ($list) {
      foreach ($list as $key => $val) {
        $house_id_arr[] = $val['id'];
        $brokeridstr .= $val['broker_id'] . ',';

        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $list[$key]['telno'] = $brokerinfo['phone'];
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];

        $list[$key]['genjintime'] = date('Y-m-d H:i', $val['updatetime']);

        if ($val['lowprice_danwei'] > 0) {
          $list[$key]['lowprice'] = ($val['lowprice'] / $val['buildarea']) / 30;
        }
        if ($val['price_danwei'] > 0) {
          $list[$key]['price'] = ($val['price'] / $val['buildarea']) / 30;
        }
      }


    }
    $data['list'] = $list;

    $data['remind_house_id'] = $remind_house_id;
    $data['follow_red_house_id'] = $follow_yes_kanfang_house_id2;

    //加载出租基本配置MODEL
    $this->load->model('house_config_model');
    //获取出租信息基本配置资料
    $config_data = $this->house_config_model->get_config();
    if (isset($config_data['status']) && !empty($config_data['status'])) {
      foreach ($config_data['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $config_data['status'][$k] = '暂不租';
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

    //文件标题
    $data['page_title'] = '出租房源列表页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/cal.css,'
      . 'mls/css/v1.0/system_set.css,'
      . 'mls/css/v1.0/house_new.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js,mls/js/v1.0/cal.js,'
      . 'mls/js/v1.0/shuifei.js,'
      . 'mls/js/v1.0/get_store.js');
    $data['post_param'] = $post_param;

    //如果后几个搜搜参数被选中则，后面的选择参数全部显示
    if (!empty($post_param['property_type']) || !empty($post_param['room']) ||
      !empty($post_param['public_type']) || !empty($post_param['status']) ||
      !empty($post_param['is_share'])
    ) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
    }

    //加载发布页面模板
    $this->view("/agency/data_transfer/_rentzy", $data);
  }


  //求购页面
  private function _buy_customer($page = 1)
  {

    //模板使用数据
    $data = array();
    $broker_info = $this->user_arr;

    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //获取默认查询时间
    $buy_customer_query_time = $company_basic_data['buy_customer_query_time'];

    //页面菜单
    $data['user_menu'] = $this->user_menu;


    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

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

    //获取当前经纪人所在门店的数据范围
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_data_transfer');
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
    $data['agency'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

    //查询条件
    $cond_where = "id > 0 AND is_public = 0 ";
    //基本设置默认查询时间
    //半年
    /*if('1'==$buy_customer_query_time){
        $half_year_time = intval(time()-365*0.5*24*60*60);
        $cond_where .= " AND creattime>= '".$half_year_time."' ";
    }
    //一年
    if('2'==$buy_customer_query_time){
        $one_year_time = intval(time()-365*24*60*60);
        $cond_where .= " AND creattime>= '".$one_year_time."' ";
    }*/

    //表单提交参数组成的查询条件
    //print_r($post_param);
    //echo "<hr/>";
    $cond_where_ext = $this->_get_cond_str($post_param);
    if ($cond_where_ext) {
      $cond_where .= ' AND ' . $cond_where_ext;
    }

    if ('none' == $post_param['store_name_out']) {
      if (!empty($all_access_agency_ids)) {
        //查询房源条件
        $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";
      }
    }

    //排序字段
    $customer_order = isset($post_param['orderby_id']) ? $post_param['orderby_id'] : array();
    $order_arr = $this->_get_orderby_arr($customer_order);
    if (strpos($cond_where, "status = '1'")) {
      $data['check_on'] = 'check_on';
    }

    //符合条件的总行数
    $this->_total_count =
      $this->buy_customer_model->get_buynum_by_cond($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $data['pages'] = $pages;

    //获取列表内容
    $customer_list =
      $this->buy_customer_model->get_buylist_by_cond($cond_where, $this->_offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    $this->buy_customer_model->set_search_fields(array('id'));
    $customer_list_all = $this->buy_customer_model->get_buylist_by_cond($cond_where, $this->_offset, 0, $order_arr['order_key'], $order_arr['order_by']);
    foreach ($customer_list_all as $k => $v) {
      $customer_id_all[] = $v['id'];
    }
    if ($customer_id_all) {
      $customer_id_all_string = implode(",", $customer_id_all);
      //print_r($customer_id_all_string);
      $data['customer_id_all_string'] = $customer_id_all_string;
    }
    //客源id数组
    $customer_id_arr = array();

    //循环获取经纪人姓名和门店信息
    if (count($customer_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();
      foreach ($customer_list as $key => $value) {
        $customer_id_arr[] = $value['id'];
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
    //页面标题
    $data['page_title'] = '求购信息列表页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/cal.css,'
      . 'mls/css/v1.0/system_set.css,'
      . 'mls/css/v1.0/house_new.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js,mls/js/v1.0/cal.js,'
      . 'mls/js/v1.0/shuifei.js,'
      . 'mls/js/v1.0/get_store.js');
    //表单数据
    $data['post_param'] = $post_param;

    //如果后几个搜搜参数被选中则，后面的选择参数全部显示
    if (!empty($post_param['property_type']) || !empty($post_param['room']) ||
      !empty($post_param['public_type']) || !empty($post_param['status']) ||
      !empty($post_param['is_share'])
    ) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
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

    //加载发布页面模板

    $this->view("/agency/data_transfer/_buy_customer", $data);
  }


  //求租页面
  private function _rent_customer($page = 1)
  {
    //模板使用数据
    $data = array();
    $broker_info = $this->user_arr;

    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //获取默认查询时间
    $rent_customer_query_time = $company_basic_data['rent_customer_query_time'];

    //页面菜单
    $data['user_menu'] = $this->user_menu;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

    //获取求租信息基本配置资料
    $conf_customer = $this->rent_customer_model->get_base_conf();
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

    if ($dist_id > 0) {
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

    //获取当前经纪人所在门店的数据范围
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_data_transfer');
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
    $data['agency'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    //查询条件
    $cond_where = "id > 0 AND is_public = 0 ";
    //基本设置默认查询时间
    //半年
    /*if('1'==$rent_customer_query_time){
        $half_year_time = intval(time()-365*0.5*24*60*60);
        $cond_where .= " AND creattime>= '".$half_year_time."' ";
    }
    //一年
    if('2'==$rent_customer_query_time){
        $one_year_time = intval(time()-365*24*60*60);
        $cond_where .= " AND creattime>= '".$one_year_time."' ";
    }*/

    //表单提交参数组成的查询条件
    //print_r($post_param);
    $cond_where_ext = $this->_get_cond_str($post_param);
    if ($cond_where_ext) {
      $cond_where .= ' AND ' . $cond_where_ext;
    }
    if ('none' == $post_param['store_name_out']) {
      if (!empty($all_access_agency_ids)) {
        //查询房源条件
        $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";
      }
    }

    //排序字段
    $customer_order = isset($post_param['orderby_id']) ? $post_param['orderby_id'] : array();
    $order_arr = $this->_get_orderby_arr($customer_order);
    if (strpos($cond_where, "status = '1'")) {
      $data['check_on'] = 'check_on';
    }

    //符合条件的总行数
    $this->_total_count =
      $this->rent_customer_model->get_rentnum_by_cond($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $data['pages'] = $pages;

    //获取列表内容
    $customer_list =
      $this->rent_customer_model->get_rentlist_by_cond($cond_where, $this->_offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    $this->rent_customer_model->set_search_fields(array('id'));
    $customer_list_all = $this->rent_customer_model->get_rentlist_by_cond($cond_where, $this->_offset, 0, $order_arr['order_key'], $order_arr['order_by']);
    foreach ($customer_list_all as $k => $v) {
      $customer_id_all[] = $v['id'];
    }
    if ($customer_id_all) {
      $customer_id_all_string = implode(",", $customer_id_all);
      //print_r($customer_id_all_string);
      $data['customer_id_all_string'] = $customer_id_all_string;
    }
    //客源id数组
    $customer_id_arr = array();
    $remind_customer_id = array();

    //循环获取经纪人姓名和门店信息
    if (count($customer_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();
      foreach ($customer_list as $key => $value) {
        $customer_id_arr[] = $value['id'];
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

    $data['remind_customer_id'] = $remind_customer_id;
    $data['follow_red_customer_id'] = $follow_yes_phone_customer_id2;
    $data['customer_list'] = $customer_list;
    //页面标题
    $data['page_title'] = '求租信息列表页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/cal.css,'
      . 'mls/css/v1.0/system_set.css,'
      . 'mls/css/v1.0/house_new.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js,mls/js/v1.0/cal.js,'
      . 'mls/js/v1.0/shuifei.js,'
      . 'mls/js/v1.0/get_store.js');
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

    //加载发布页面模板
    $this->view('/agency/data_transfer/_rent_customer', $data);
  }


  //获取分店下非本人的所有经纪人
  public function get_all_by()
  {
    $aid = $this->input->post("id", true);
    $mark = $this->input->post("mark", true);
    if ($aid == "none") {
      $result = "none";
    } else {
      $cid = $this->user_arr['company_id'];
      $model = $mark . "_model";
      $result = $this->$model->get_all_by($aid, $cid);
    }
    echo json_encode($result);
  }

  /*
    * 取消合作
    * $c_id ;         //合同ID
    * $step ;         //合同现阶段步骤
    * $old_esta ;     //合作状态
    * $cancle_type ;  //取消合同原因类别
    * $cancle_reason; //取消合同原因
    * //被转移的经纪人(即甲方经纪人)信息
    * $broker_id ; //经纪人编号
    * $broker_name ; //经纪人姓名
    */
  public function cancle_cooperation($c_id, $step, $old_esta, $cancle_type, $cancle_reason, $broker_id, $broker_name)
  {

    //原有的加密字符串
    $secret_param_old = array('cid' => $c_id, 'step' => $step, 'esta' => $old_esta);
    $cop_secret_key = $this->verify->user_enrypt($secret_param_old);//加密字符串

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

        //更新合同步骤日志
        if ($up_num > 0) {
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
              if ($cooperate_info['esta'] < 4) {
                $msg_type_self = '1-14-4';
              } else {
                $msg_type_self = '1-14-4';
              }
              $broker_id_self = $broker_id;
              $broker_name_self = $broker_name;
              $fromer_self = '';
              $url_self = '/my_growing_punish/index/';
              if ($cooperate_info['apply_type'] == 1) {
                $params['type'] = "f";
                $params['name'] = $fromer_self;
                $params['id'] = $cooperate_info['rowid'];
              } else if ($cooperate_info['apply_type'] == 2) {
                $params['name'] = $fromer_self;
                $params['id'] = $cooperate_info['customer_id'];
                $tbl .= '_customer';
              }
              $params['id'] = format_info_id($params['id'], $tbl);
              //33
              $this->message_base_model->add_message($msg_type_self, $broker_id_self, $broker_name_self, $url_self, $params);
            }
          }

          //发送站内信
          $broker_id_msg = $broker_id == $broker_id_a ? $broker_id_b : $broker_id_a;
          $url_msg = $broker_id == $broker_id_a ? '/cooperate/send_order_list/?cid=' . $c_id : '/cooperate/accept_order_list/?cid=' . $c_id;
          $broker_name_msg = $broker_id == $broker_id_a ? $broker_name_b : $broker_name_a;
          $fromer = $broker_name;
          if ($cooperate_info['esta'] < 4) {
            $msg_type = '1-7-1';
          } else {
            $msg_type = '1-7-2';
          }
          if ($cooperate_info['apply_type'] == 1) {
            $params['type'] = "f";
            $params['name'] = $fromer_self;
            $params['id'] = $cooperate_info['rowid'];
          } else if ($cooperate_info['apply_type'] == 2) {
            $params['name'] = $fromer_self;
            $params['id'] = $cooperate_info['customer_id'];
            $tbl .= '_customer';
          }
          $params['id'] = format_info_id($params['id'], $tbl);
          //33
          $msg_id = $this->message_base_model->add_message($msg_type, $broker_id_msg, $broker_name_msg, $url_msg, $params);
          $this->push_func_model->send(1, 1, 9, $this->user_arr['broker_id'], $broker_id_msg, array('msg_id' => $msg_id));
        }
      } else {
        $cop_config = $this->cooperate_model->get_base_conf();
        $esta_str = !empty($cop_config['esta'][$cooperate_info['esta']]) ? $cop_config['esta'][$cooperate_info['esta']] : '';
        $msg = !empty($esta_str) ? "合作状态已变更为'" . $esta_str . "'" : '合作状态已更新';
        $msg .= ",刷新合作列表页后再操作。<a href='javascript:void(0)' onclick='search_form.submit();return false;'>刷新>></a>";
        $result = array('is_ok' => 0, 'msg' => $msg);
      }
    }


  }

  //转移数据
  public function move_data()
  {
    $type = $this->input->post('type', TRUE);
    $ids = $this->input->post('id', TRUE);
    $broker_id = $this->input->post('broker_id', TRUE);
    $this->broker_info_model->set_select_fields(array('truename', 'agency_id'));
    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);
    $agency_info = $this->agency_model->get_one_by(array('id' => $broker_info['agency_id']));
    $this->broker_info_model->set_select_fields(array('truename', 'agency_id'));
    //根据转出数据，获得经纪人和门店，用于记录日志
    if (is_full_array($ids)) {
      $post_data_id = intval($ids['0']);
    }
    if ($type == "buy_customer") {
      $share = $this->buy_customer_model->get_all_isshare_by_ids(implode(",", $ids));
      if (isset($post_data_id) && !empty($post_data_id)) {
        $this->buy_customer_model->set_search_fields(array('broker_id'));
        $this->buy_customer_model->set_id($post_data_id);
        $sell_house_info = $this->buy_customer_model->get_info_by_id($post_data_id);
        $broker_id_out = 0;
        if (is_full_array($sell_house_info)) {
          $broker_id_out = $sell_house_info['broker_id'];
        }
      }
    } else if ($type == "rent_customer") {
      $share = $this->rent_customer_model->get_all_isshare_by_ids(implode(",", $ids));
      if (isset($post_data_id) && !empty($post_data_id)) {
        $this->rent_customer_model->set_search_fields(array('broker_id'));
        $this->rent_customer_model->set_id($post_data_id);
        $sell_house_info = $this->rent_customer_model->get_info_by_id($post_data_id);
        $broker_id_out = 0;
        if (is_full_array($sell_house_info)) {
          $broker_id_out = $sell_house_info['broker_id'];
        }
      }
    } else if ($type == "sell") {
      $share = $this->sell_house_model->get_all_isshare_by_ids(implode(",", $ids));
      if (isset($post_data_id) && !empty($post_data_id)) {
        $this->sell_house_model->set_search_fields(array('broker_id'));
        $this->sell_house_model->set_id($post_data_id);
        $sell_house_info = $this->sell_house_model->get_info_by_id($post_data_id);
        $broker_id_out = 0;
        if (is_full_array($sell_house_info)) {
          $broker_id_out = $sell_house_info['broker_id'];
        }
      }
    } else if ($type == "rent") {
      $share = $this->rent_house_model->get_all_isshare_by_ids(implode(",", $ids));
      if (isset($post_data_id) && !empty($post_data_id)) {
        $this->rent_house_model->set_search_fields(array('broker_id'));
        $this->rent_house_model->set_id($post_data_id);
        $sell_house_info = $this->rent_house_model->get_info_by_id($post_data_id);
        $broker_id_out = 0;
        if (is_full_array($sell_house_info)) {
          $broker_id_out = $sell_house_info['broker_id'];
        }
      }
    }
    if (!empty($broker_id_out)) {
      $broker_info_out = $this->broker_info_model->get_by_broker_id($broker_id_out);
      $agency_info_out = $this->agency_model->get_one_by(array('id' => $broker_info_out['agency_id']));
    }

    if (!empty($cooperate_arr) && is_array($share)) {
      foreach ($share as $vo) {
        if ($vo['isshare'] == 1) {
          foreach ($ids as $vid) {
            $cooperate_arr = $this->cooperate_model->get_cooperation_by($vid, $broker_id, $broker_info['agency_id']);
            if (!empty($cooperate_arr) && is_array($cooperate_arr)) {
              foreach ($cooperate_arr as $c) {
                $this->cancle_cooperation($c['id'], $c['step'], $c['esta'], 3, "个人意愿撤销合作", $broker_id, $broker_info['truename']);
              }
            }
          }
        }
      }
    }


    //print_r($broker_info);
    //die();
    $update_arr = array("broker_id" => $broker_id, "broker_name" => $broker_info['truename'], "agency_id" => $broker_info['agency_id']);
    if ($type == "buy_customer") {
      //添加积分兑换操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 32;
      $add_log_param['text'] = $agency_info_out['name'] . ' ' . $broker_info_out['truename'] . '的数据转移给' . $agency_info['name'] . ' ' . $broker_info['truename'];
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
      echo $this->buy_customer_model->update_info_by_id($ids, $update_arr);
    } else if ($type == "rent_customer") {
      //添加积分兑换操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 32;
      $add_log_param['text'] = $agency_info_out['name'] . ' ' . $broker_info_out['truename'] . '的数据转移给' . $agency_info['name'] . ' ' . $broker_info['truename'];
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
      echo $this->rent_customer_model->update_info_by_id($ids, $update_arr);
    } else if ($type == "sell") {
      //添加积分兑换操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 32;
      $add_log_param['text'] = $agency_info_out['name'] . ' ' . $broker_info_out['truename'] . '的数据转移给' . $agency_info['name'] . ' ' . $broker_info['truename'];
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
      echo $this->sell_house_model->update_info_by_ids($ids, $update_arr);
    } else if ($type == "rent") {
      //添加积分兑换操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 32;
      $add_log_param['text'] = $agency_info_out['name'] . ' ' . $broker_info_out['truename'] . '的数据转移给' . $agency_info['name'] . ' ' . $broker_info['truename'];
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
      echo $this->rent_house_model->update_info_by_ids($ids, $update_arr);
    }
  }


}

