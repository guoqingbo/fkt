<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Customer_collect
 *
 * @author liuhu
 */
class Customer_collect extends MY_Controller
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
  private $_limit = 10;

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

  //构造函数
  public function __construct()
  {
    parent::__construct();
  }


  //首页
  public function index()
  {
    $this->buy_customer_collects();
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
   * 收藏客源列表
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function buy_customer_collects($page = 1)
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

    $broker_id = intval($this->user_arr['broker_id']);
    $data['broker_id'] = $broker_id;
    $post_param = $this->input->post(NULL, TRUE);

    //所在公司的分店信息
    $company_id = intval($this->user_arr['company_id']);
    $this->load->model('api_broker_model');
    $company_id = $this->user_arr['company_id'];
    $data['agencys'] = $this->api_broker_model->get_agencys_by_company_id($company_id);

    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

    //加载求购客户MODEL
    $this->load->model('buy_customer_model');
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

    //收藏MODEL
    $this->load->model('customer_collect_model');
    $type = 'buy_customer';
    //查询条件
    $cond_where = "customer_collect.broker_id = '" . $broker_id . "' AND "
      . "customer_collect.status = 1 AND customer_collect.tbl = '" . $type . "'";
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_collect_cond_str($post_param, $type);
    $cond_where .= $cond_where_ext;

    //符合条件的总行数
    $this->_total_count =
      $this->customer_collect_model->get_collection_num_by_cond($cond_where, $type);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    //获取列表内容
    $customer_list = $this->customer_collect_model->get_collection_list_by_cond($cond_where, $type, $this->_offset, $this->_limit);

    //循环获取经纪人姓名和门店信息
    if (count($customer_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();
      foreach ($customer_list as $key => $value) {
        $broker_id_gj = intval($value['broker_id']);
        if ($broker_id_gj > 0 && !in_array($broker_id_gj, $broker_id_arr)) {
          array_push($broker_id_arr, $broker_id_gj);
        }

        $cid_arr[] = $value['customer_id'];
      }

      //检查是否已经申请过客源合作
      $this->load->model('cooperate_model');
      $data['check_coop_reulst'] = $this->cooperate_model->check_is_cooped_by_cid($cid_arr, 'sell', $broker_id);

      //经纪人MODEL
      $this->load->model('api_broker_model');
      $this->load->model('api_broker_sincere_model');
      //合作成功率MODEL
      $this->load->model('cooperate_suc_ratio_base_model');
      $broker_num = count($broker_id_arr);
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

    //菜单
    $data['user_menu'] = $this->user_menu;

    //三级功能菜单
    $data['user_func_menu'] = $this->user_func_menu;

    //页面标题
    $data['page_title'] = '求购信息列表页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css'
      . ',mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'mls/js/v1.0/broker_common.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/customer_list.js,'
      . 'mls/js/v1.0/cooperate_common.js');

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
    $this->view('customer/buy_customer_list_collect', $data);
  }


  /**
   * 收藏客源列表
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function rent_customer_collects($page = 1)
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

    $broker_id = intval($this->user_arr['broker_id']);
    $data['broker_id'] = $broker_id;
    $post_param = $this->input->post(NULL, TRUE);

    //所在公司的分店信息
    $company_id = intval($this->user_arr['company_id']);
    $this->load->model('api_broker_model');
    $company_id = $this->user_arr['company_id'];
    $data['agencys'] = $this->api_broker_model->get_agencys_by_company_id($company_id);

    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

    //加载求租客户MODEL
    $this->load->model('rent_customer_model');
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

    //收藏MODEL
    $this->load->model('customer_collect_model');
    $type = 'rent_customer';
    //查询条件
    $cond_where = "customer_collect.broker_id = '" . $broker_id . "' AND "
      . "customer_collect.status = 1 AND customer_collect.tbl = '" . $type . "'";
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_collect_cond_str($post_param, $type);
    $cond_where .= $cond_where_ext;

    //符合条件的总行数
    $this->_total_count =
      $this->customer_collect_model->get_collection_num_by_cond($cond_where, $type);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $customer_list = $this->customer_collect_model->get_collection_list_by_cond($cond_where, $type, $this->_offset, $this->_limit);

    //循环获取经纪人姓名和门店信息
    if (count($customer_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();

      foreach ($customer_list as $key => $value) {
        $broker_id_gj = intval($value['broker_id']);
        if ($broker_id_gj > 0 && !in_array($broker_id_gj, $broker_id_arr)) {
          array_push($broker_id_arr, $broker_id_gj);
        }

        $cid_arr[] = $value['customer_id'];
      }

      //检查是否已经申请过客源合作
      $this->load->model('cooperate_model');
      $data['check_coop_reulst'] = $this->cooperate_model->check_is_cooped_by_cid($cid_arr, 'rent', $broker_id);

      //经纪人MODEL
      $this->load->model('api_broker_model');
      $this->load->model('api_broker_sincere_model');
      //合作成功率MODEL
      $this->load->model('cooperate_suc_ratio_base_model');
      $broker_num = count($broker_id_arr);
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

    //菜单
    $data['user_menu'] = $this->user_menu;

    //三级功能菜单
    $data['user_func_menu'] = $this->user_func_menu;

    //页面标题
    $data['page_title'] = '求购信息列表页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css'
      . ',mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'mls/js/v1.0/broker_common.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/customer_list.js,'
      . 'mls/js/v1.0/cooperate_common.js');

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
    $this->view('customer/rent_customer_list_collect', $data);
  }


  /**
   * 收藏客源信息
   *
   * @access  public
   * @param  mixed $customer_id 客源编号
   * @return  void
   */
  public function add_collect_customer()
  {
    $customer_id = intval($this->input->get('customer_id', TRUE));
    $type = strip_tags($this->input->get('type', TRUE));
    $result = array();

    if ($customer_id > 0 && $type != '') {
      //加载经纪人MODEL
      $broker_info = $this->user_arr;
      $broker_id = intval($broker_info['broker_id']);
      $agency_id = intval($broker_info['agency_id']);

      //收藏数据
      $collect_arr = array();
      $collect_arr['broker_id'] = $broker_id;
      $collect_arr['agency_id'] = $agency_id;
      $collect_arr['customer_id'] = $customer_id;
      $collect_arr['tbl'] = $type;
      $collect_arr['creattime'] = time();
      $collect_arr['status'] = 1;

      $this->load->model('customer_collect_model');
      //判断是否已经收藏过改条客源
      $collect_num = $this->customer_collect_model->get_collectionnum_by_cid($customer_id, $broker_id, $type, '1');

      //添加收藏数据
      if (0 == intval($collect_num)) {
        $msg = $this->customer_collect_model->add_collection($collect_arr);
        if ($msg > 0) {
          $result['is_ok'] = 1;
          $result['msg'] = '已收藏';
        } else {
          $result['is_ok'] = 0;
          $result['msg'] = '收藏失败';
        }
      } else {
        $result['is_ok'] = 0;
        $result['msg'] = '已收藏过该客源信息';
      }
    }
    echo json_encode($result);
  }


  /**
   * 取消收藏客源信息
   *
   * @access  public
   * @param  mixed $customer_id 客源编号
   * @return  void
   */
  public function cancle_collect_customer()
  {
    $c_id = intval($this->input->get('collect_id', TRUE));
    $type = strip_tags($this->input->get('type', TRUE));
    $result = array();

    if ($c_id > 0 && $type != '') {
      //加载经纪人MODEL
      $broker_info = $this->user_arr;
      $broker_id = intval($broker_info['broker_id']);
      $this->load->model('customer_collect_model');
      $msg = $this->customer_collect_model->cancel_collection_by_id($c_id, $broker_id, $type);

      if ($msg > 0) {
        $result['is_ok'] = 1;
        $result['msg'] = '已取消收藏';
      } else {
        $result['is_ok'] = 0;
        $result['msg'] = '取消收藏失败';
      }
    }

    echo json_encode($result);
  }


  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_collect_cond_str($form_param, $tbl_name)
  {
    $cond_where = '';

    //物业类型条件
    if (isset($form_param['property_type']) && !empty($form_param['property_type']) && $form_param['property_type'] > 0) {
      $property_type = intval($form_param['property_type']);
      $cond_where .= " AND $tbl_name.property_type = '" . $property_type . "'";
    }

    //状态条件
    if (isset($form_param['status']) && !empty($form_param['status']) && $form_param['status'] > 0) {
      $status = intval($form_param['status']);
      $cond_where .= " AND $tbl_name.status = '" . $status . "'";
    }

    //性质
    if (isset($form_param['public_type']) && !empty($form_param['public_type'])) {
      $public_type = intval($form_param['public_type']);
      $cond_where .= " AND $tbl_name.public_type = '" . $public_type . "'";
    }

    //是否合作
    if (isset($form_param['is_share']) && !empty($form_param['is_share']) && $form_param['is_share'] > 0) {
      $is_share = intval($form_param['is_share']);
      $cond_where .= " AND $tbl_name.is_share = '" . $is_share . "'";
    }

    //区属、板块条件
    if (isset($form_param['dist_id']) && $form_param['dist_id'] > 0) {
      $dist_id = intval($form_param['dist_id']);

      $cond_where .= " AND ($tbl_name.dist_id1 = '" . $dist_id . "' "
        . " OR $tbl_name.dist_id2 = '" . $dist_id . "'"
        . " OR $tbl_name.dist_id3 = '" . $dist_id . "')";


      $street_id = intval($form_param['street_id']);
      if ($street_id > 0) {
        $cond_where .= " AND ($tbl_name.street_id1 = '" . $street_id . "' "
          . " OR $tbl_name.street_id2 = '" . $street_id . "'"
          . " OR $tbl_name.street_id3 = '" . $street_id . "')";
      }
    }

    //楼盘参数
    if (isset($form_param["cmt_id"]) && $form_param['cmt_id'] > 0) {
      $cmt_id = intval($form_param["cmt_id"]);

      $cond_where .= " AND ($tbl_name.cmt_id1 = '" . $cmt_id . "' "
        . " OR $tbl_name.cmt_id2 = '" . $cmt_id . "'"
        . " OR $tbl_name.cmt_id3 = '" . $cmt_id . "')";
    }

    //价格条件
    if ((isset($form_param["price_min"]) && $form_param["price_min"] > 0)
      || (isset($form_param["price_min"]) && $form_param["price_min"] > 0)
    ) {
      $price_min = floatval($form_param["price_min"]);
      $price_max = floatval($form_param["price_max"]);

      if ($price_max >= $price_min) {
        $cond_where .= " AND ((  $tbl_name.price_min >= '" . $price_min . "' AND "
          . "$tbl_name.price_min <= '" . $price_max . "') OR ($tbl_name.price_max >= '" . $price_min . "' "
          . "AND $tbl_name.price_max <= '" . $price_max . "') )";
      } else if ($price_max == 0 && $price_min > 0) {
        $cond_where .= " AND $tbl_name.price_min >= '" . $price_min . "'";
      }
    }

    //面积条件
    if ((isset($form_param["area_min"]) && $form_param["area_min"] > 0)
      || (isset($form_param["area_max"]) && $form_param["area_max"] > 0)
    ) {
      $area_min = floatval($form_param["area_min"]);
      $area_max = floatval($form_param["area_max"]);

      if ($area_max >= $area_min) {
        $cond_where .= " AND ( ($tbl_name.area_min >= '" . $area_min . "' AND "
          . "$tbl_name.area_min <= '" . $area_max . "') OR (  $tbl_name.area_max >= '" . $area_min . "' "
          . "AND $tbl_name.area_max <= '" . $area_max . "') )";
      } else if ($area_max == 0 && $area_min > 0) {
        $cond_where .= " AND $tbl_name.area_min >= '" . $area_min . "'";
      }
    }

    //户型条件
    if ((isset($form_param["room"]) && $form_param["room"] > 0)) {
      $room = floatval($form_param["room"]);
      $cond_where .= " AND $tbl_name.room_min <= '" . $room . "' AND "
        . "$tbl_name.room_max >= '" . $room . "' ";
    }

    //客户编号
    if (isset($form_param['id']) && $form_param['id'] > 0) {
      $id = intval($form_param['id']);
      $cond_where .= " AND $tbl_name.id = '" . $id . "'";
    }

    //楼盘参数
    if (isset($form_param["cmt_name"])) {
      $cmt_name = trim($form_param["cmt_name"]);
      $cond_where .= "AND ($tbl_name.cmt_name1 like '%" . $cmt_name . "%' "
        . " OR $tbl_name.cmt_name2 like '%" . $cmt_name . "%'"
        . " OR $tbl_name.cmt_name3 like '%" . $cmt_name . "%')";
    }

    return $cond_where;
  }
}

/* End of file customer_base_model.php */
/* Location: ./applications/models/customer_base_model.php */
