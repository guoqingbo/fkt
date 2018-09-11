<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 */

/**
 * Rent_customer Controller CLASS
 *
 * 求租客源发布、列表、详情页以及客源相关管理功能 控制器
 *
 * @package         MLS
 * @subpackage      Controllers
 * @category        Controllers
 * @author          xz
 */
class Rent_customer extends MY_Controller
{

  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'nj';


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
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('customer');
    $this->load->model('follow_model');
    $this->load->model('buy_customer_model');
    $this->load->model('rent_customer_model');
    $this->load->model('agency_model');
    $this->load->model('operate_log_model');

    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
    }
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
   * 修改客源信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function modify()
  {
    $customer_info = array();
    //经纪人信息
    $broker_info = $this->user_arr;
    $customer_id = intval($this->input->post('customer_id'));

    if ($customer_id > 0) {
      $this->rent_customer_model->set_id($customer_id);
      $customers = $this->rent_customer_model->get_info_by_id();
      $customer_info = array();
      if (is_full_array($customers)) {
        //新权限
        //范围（1公司2门店3个人）
        //获得当前数据所属的经纪人id和门店id
        $owner_arr = array(
          'broker_id' => $customers['broker_id'],
          'agency_id' => $customers['agency_id'],
          'company_id' => $customers['company_id']
        );
        $customer_modify_per = $this->broker_permission_model->check('18', $owner_arr);
        //修改客源关联门店权限
        $agency_customer_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '13');
        if (!$customer_modify_per['auth']) {
          $this->result('-1', '暂无权限');
          exit();
        } else {
          if (!$agency_customer_modify_per) {
            $this->result('-1', '暂无权限');
            exit();
          }
        }

        //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
        $role_level = intval($broker_info['role_level']);
        //店长以下的经纪人不允许操作他人的私盘
        if (is_int($role_level) && $role_level > 6) {
          if ($owner_arr['broker_id'] != $broker_info['broker_id'] && $customers['public_type'] == '1') {
            $this->result('-1', '店长以下的经纪人不允许操作他人的私客');
            exit();
          }
        }

        //获取求购信息基本配置资料
        $conf_customer = $this->rent_customer_model->get_base_conf();
        if ($customers['property_type']) {
          $customer_info['property_type'] = $conf_customer['property_type'][$customers['property_type']];
        } else {
          $customer_info['property_type'] = '';
        }
        $customer_info['property_type_key'] = $customers['property_type'];
        if ($customers['fitment']) {
          $customer_info['fitment'] = $conf_customer['fitment'][$customers['fitment']];
        } else {
          $customer_info['fitment'] = '';
        }
        $customer_info['fitment_key'] = $customers['fitment'];
        if ($customers['public_type']) {
          $customer_info['public_type'] = $conf_customer['public_type'][$customers['public_type']];
        } else {
          $customer_info['public_type'] = '';
        }
        $customer_info['public_type_key'] = $customers['public_type'];
        if ($customers['status']) {
          $customer_info['status'] = $conf_customer['status'][$customers['status']];
        } else {
          $customer_info['status'] = '';
        }
        $customer_info['status_key'] = $customers['status'];
        if ($customers['lease']) {
          $customer_info['lease'] = $conf_customer['lease'][$customers['lease']];
        } else {
          $customer_info['lease'] = '';
        }
        $customer_info['lease_key'] = $customers['lease'];
        //年龄
        $customer_info['age_group_key'] = $customers['age_group'];
        if ($customers['age_group'] > 0) {
          $customer_info['age_group'] = $conf_customer['age_group'][$customers['age_group']];
        } else {
          $customer_info['age_group'] = '';
        }
        //职业
        $customer_info['job_type_key'] = $customers['job_type'];
        if ($customers['job_type'] > 0) {
          $customer_info['job_type'] = $conf_customer['job_type'][$customers['job_type']];
        } else {
          $customer_info['job_type'] = '';
        }
        //目的
        $customer_info['intent_key'] = $customers['intent'];
        if ($customers['intent'] > 0) {
          $customer_info['intent'] = $conf_customer['intent'][$customers['intent']];
        } else {
          $customer_info['intent'] = '';
        }
        $customer_info['floor_min'] = $customers['floor_min'];
        $customer_info['floor_max'] = $customers['floor_max'];
        $customer_info['room_min'] = $customers['room_min'];
        $customer_info['room_max'] = $customers['room_max'];
        $customer_info['area_min'] = strip_end_0($customers['area_min']);
        $customer_info['area_max'] = strip_end_0($customers['area_max']);
        $customer_info['price_danwei'] = $customers['price_danwei'];
        if ($customer_info['price_danwei']) {
          $price_min = round($customers['price_min'] / $customers['area_min'] / 30, 2);
          $price_max = round($customers['price_max'] / $customers['area_max'] / 30, 2);
        } else {
          $price_min = $customers['price_min'];
          $price_max = $customers['price_max'];
        }
        $customer_info['price_min'] = strip_end_0($price_min);
        $customer_info['price_max'] = strip_end_0($price_max);
        $customer_info['truename'] = $customers['truename'];
        $customer_info['telno'] = empty($customers['telno1']) ? (empty($customers['telno2']) ? $customers['telno3'] : $customers['telno2']) : $customers['telno1'];
        $customer_info['is_share'] = $customers['is_share'];
        $customer_info['is_share_taofang'] = $customers['is_share_taofang'];

        $this->load->model('district_model');
        $dist_street = array();
        $cmt = array();
        for ($i = 0; $i < 3; $i++) {
          $num = $i + 1;
          if ($customers['dist_id' . $num] && $customers['street_id' . $num]) {
            $dist_name = $this->district_model->get_distname_by_id($customers['dist_id' . $num]);
            $street_name = $this->district_model->get_streetname_by_id($customers['street_id' . $num]);
            $dist_street[$i]['dist_id'] = $customers['dist_id' . $num];
            $dist_street[$i]['street_id'] = $customers['street_id' . $num];
            $dist_street[$i]['dist_street'] = $dist_name . '-' . $street_name;
          }
        }
        $customer_info['dist_street'] = $dist_street;

        for ($i = 0; $i < 3; $i++) {
          $num = $i + 1;
          if ($customers['cmt_id' . $num] or $customers['cmt_name' . $num]) {
            $cmt[$i]['cmt_id'] = $customers['cmt_id' . $num];
            $cmt[$i]['cmt_name'] = $customers['cmt_name' . $num];
          }
        }
        $customer_info['cmt'] = $cmt;

        $this->result(1, '客源信息获取成功', $customer_info);
        return;
      } else {
        $this->result(2, '无此客源信息');
        return;
      }
    } else {
      $this->result(0, '参数非法');
      return;
    }
  }


  /**
   * 客源列表页
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function manage()
  {
    //模板使用数据
    $data = array();
    $broker_info = $this->user_arr;
    $company_id = intval($broker_info['company_id']);
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    //获取默认查询时间
    $rent_customer_query_time = $company_basic_data['rent_customer_query_time'];
    //新权限
    //范围（1公司2门店3个人）
    if ($company_id) {
      $view_other_per_data = $this->broker_permission_model->check('26');
      if ($company_id && $view_other_per_data['auth']) {
        $data['view_other_per'] = true;
      } else {
        $data['view_other_per'] = false;
      }
    } else {
      $data['view_other_per'] = false;
    }

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //经纪人信息
    $broker_id = intval($broker_info['broker_id']);
    $data['broker_id'] = $broker_id;
    $data['truename'] = $broker_info['truename'];
    $data['agency_id'] = $broker_info['agency_id'];
    $data['agency_name'] = $broker_info['agency_name'];

    $this->load->model('api_broker_model');
    //根据门店id获取所在门店下的所有经纪人
    /*if(!empty($post_param['agenctcode'])){
            $broker_arr=$this->api_broker_model->get_brokers_agency_id($post_param['agenctcode']);
            $data['broker_list']=$broker_arr;
        }else{
            $broker_arr=$this->api_broker_model->get_brokers_agency_id($broker_info['agency_id']);
            $data['broker_list']=$broker_arr;
        }*/

    //所在公司的分店信息
    $data['company_id'] = $company_id;
    $this->load->model('api_broker_model');
    $data['agencys'] = $this->api_broker_model->get_agencys_by_company_id($company_id);

    //查询条件
    $cond_where = "company_id = '" . $company_id . "'";
    //获取当前经纪人所在门店的数据范围,添加初始条件
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_view_customer');
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

    if (!isset($post_param['broker_id'])) {
      $cond_where .= " and broker_id = '" . $broker_id . "'";
    }

    //基本设置默认查询时间
    if ($post_param['create_time_range'] == 0) {
      //半年
      if ('1' == $rent_customer_query_time) {
        $half_year_time = intval(time() - 365 * 0.5 * 24 * 60 * 60);
        $cond_where .= " AND creattime>= '" . $half_year_time . "' ";
      }
      //一年
      if ('2' == $rent_customer_query_time) {
        $one_year_time = intval(time() - 365 * 24 * 60 * 60);
        $cond_where .= " AND creattime>= '" . $one_year_time . "' ";
      }
    }

    //查询有客要合作条件
    if (isset($post_param['customer_type']) && $post_param['customer_type'] == 1) {
      $cond_where .= " and is_share = 0 and status = 1 and broker_id = " . $broker_id;
    }
    //默认状态为有效
    if (!isset($post_param['status'])) {
      $cond_where .= " AND status = 1";
    }

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //排序字段
    $customer_order = isset($post_param['customer_order']) ? $post_param['customer_order'] : array();
    $order_arr = $this->_get_orderby_arr($customer_order);
    //符合条件的总行数
    $data['total_count'] = $this->rent_customer_model->get_rentnum_by_cond($cond_where);

    //获取列表内容
    $this->rent_customer_model->set_search_fields(array('id', 'truename', 'property_type', 'public_type', 'is_share', 'price_danwei', 'area_min', 'area_max', 'price_min', 'price_max', 'dist_id1', 'street_id1', 'dist_id2', 'street_id2', 'dist_id3', 'street_id3', 'cmt_name1', 'cmt_name2', 'cmt_name3', 'telno1'));
    $customer_list =
      $this->rent_customer_model->get_list_by_cond($cond_where, $this->_offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);
    $customer_list_info = array();
    if (is_full_array($customer_list)) {
      $this->load->model('district_model');

      //获取求购信息基本配置资料
      $conf_customer = $this->rent_customer_model->get_base_conf();

      foreach ($customer_list as $key => $value) {
        $dist_street1 = '';
        $dist_street2 = '';
        $dist_street3 = '';

        if ($value['property_type']) {
          $customer_list_info[$key]['property_type'] = $conf_customer['property_type'][$value['property_type']];
        } else {
          $customer_list_info[$key]['property_type'] = '';
        }
        if ($value['public_type']) {
          $customer_list_info[$key]['public_type'] = $conf_customer['public_type'][$value['public_type']];
        } else {
          $customer_list_info[$key]['public_type'] = '';
        }

        $customer_list_info[$key]['customer_id'] = $value['id'];
        $customer_list_info[$key]['truename'] = $value['truename'];
        $customer_list_info[$key]['property_type_key'] = $value['property_type'];
        $customer_list_info[$key]['public_type_key'] = $value['public_type'];
        $customer_list_info[$key]['is_share'] = $value['is_share'];
        $customer_list_info[$key]['price_danwei'] = $value['price_danwei'];
        if ($value['price_danwei']) {
          $price_min = round($value['price_min'] / 30 / $value['area_min'], 2);
          $price_max = round($value['price_max'] / 30 / $value['area_max'], 2);
        } else {
          $price_min = $value['price_min'];
          $price_max = $value['price_max'];
        }
        $customer_list_info[$key]['area'] = strip_end_0($value['area_min']) . '-' . strip_end_0($value['area_max']);
        $customer_list_info[$key]['price'] = strip_end_0($price_min) . '-' . strip_end_0($price_max);
        if ($value['dist_id1'] && $value['street_id1']) {
          $dist_name1 = $this->district_model->get_distname_by_id($value['dist_id1']);
          $street_name1 = $this->district_model->get_streetname_by_id($value['street_id1']);
          $dist_street1 = $dist_name1 . '-' . $street_name1;
        }
        if ($value['dist_id2'] && $value['street_id2']) {
          $dist_name2 = $this->district_model->get_distname_by_id($value['dist_id2']);
          $street_name2 = $this->district_model->get_streetname_by_id($value['street_id2']);
          $dist_street2 = $dist_name2 . '-' . $street_name2;
        }
        if ($value['dist_id3'] && $value['street_id3']) {
          $dist_name3 = $this->district_model->get_distname_by_id($value['dist_id3']);
          $street_name3 = $this->district_model->get_streetname_by_id($value['street_id3']);
          $dist_street3 = $dist_name3 . '-' . $street_name3;
        }

        $customer_list_info[$key]['dist_street'] = $dist_street1 . ' ' . $dist_street2 . ' ' . $dist_street3;
        $customer_list_info[$key]['cmt_name'] = $value['cmt_name1'] . ' ' . $value['cmt_name2'] . ' ' . $value['cmt_name3'];
        $customer_list_info[$key]['total_count'] = $data['total_count'];
        $customer_list_info[$key]['telno1'] = $value['telno1'];
        /*if($dist_street1 && $dist_street2 && $dist_street3){
                    $customer_list_info[$key]['dist_street'] = $dist_street1 .' '. $dist_street2;
                }else{
                    $customer_list_info[$key]['dist_street'] = $dist_street1 .' '. $dist_street2 .' '. $dist_street3;
                }

                if($value['cmt_name1'] && $value['cmt_name2'] && $value['cmt_name3']){
                    $customer_list_info[$key]['cmt_name'] = $value['cmt_name1'] .' '. $value['cmt_name2'];
                }else{
                    $customer_list_info[$key]['cmt_name'] = $value['cmt_name1'] .' '. $value['cmt_name2'] .' '. $value['cmt_name3'];
                }*/
      }
    }
    $data['customer_list'] = $customer_list_info;
    //表单数据
    $data['post_param'] = $post_param;

    $this->result(1, '获取成功', $data);
    return;
  }


  /**
   * 求租公客列表页
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function manage_pub()
  {
    //模板使用数据
    $data = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];

    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //经纪人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $data['broker_id'] = $broker_id;

    //所在公司的分店信息
    $company_id = intval($broker_info['company_id']);
    $this->load->model('api_broker_model');
    $data['agencys'] = $this->api_broker_model->get_agencys_by_company_id($company_id);

    //查询条件
    $cond_where = 'is_share = 1 and status = 1';

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //排序字段
    $customer_order = isset($post_param['customer_order']) ? $post_param['customer_order'] : array();
    $order_arr = $this->_get_orderby_arr($customer_order);

    //符合条件的总行数
    /*$this->_total_count =
        $this->rent_customer_model->get_rentnum_by_cond($cond_where);

        //计算总页数
        $pages  = $this->_total_count > 0 ? ceil( $this->_total_count / $this->_limit ) : 0;*/

    //获取列表内容
    $this->rent_customer_model->set_search_fields(array('id', 'broker_id', 'truename', 'property_type', 'public_type', 'is_share', 'price_danwei', 'area_min', 'area_max', 'price_min', 'price_max', 'dist_id1', 'street_id1', 'dist_id2', 'street_id2', 'dist_id3', 'street_id3', 'cmt_name1', 'cmt_name2', 'cmt_name3'));
    $customer_list =
      $this->rent_customer_model->get_list_by_cond($cond_where, $this->_offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);
    $customer_list_info = array();
    if (is_full_array($customer_list)) {
      $this->load->model('district_model');

      //获取求购信息基本配置资料
      $conf_customer = $this->rent_customer_model->get_base_conf();

      foreach ($customer_list as $key => $value) {
        $dist_street1 = '';
        $dist_street2 = '';
        $dist_street3 = '';

        if ($value['property_type']) {
          $customer_list_info[$key]['property_type'] = $conf_customer['property_type'][$value['property_type']];
        } else {
          $customer_list_info[$key]['property_type'] = '';
        }
        if ($value['public_type']) {
          $customer_list_info[$key]['public_type'] = $conf_customer['public_type'][$value['public_type']];
        } else {
          $customer_list_info[$key]['public_type'] = '';
        }

        $customer_list_info[$key]['customer_id'] = $value['id'];
        $customer_list_info[$key]['broker_id'] = $value['broker_id'];
        $customer_list_info[$key]['truename'] = $value['truename'];
        $customer_list_info[$key]['property_type_key'] = $value['property_type'];
        $customer_list_info[$key]['public_type_key'] = $value['public_type'];
        $customer_list_info[$key]['is_share'] = $value['is_share'];
        $customer_list_info[$key]['price_danwei'] = $value['price_danwei'];
        if ($value['price_danwei']) {
          $price_min = round($value['price_min'] / 30 / $value['area_min'], 2);
          $price_max = round($value['price_max'] / 30 / $value['area_max'], 2);
        } else {
          $price_min = $value['price_min'];
          $price_max = $value['price_max'];
        }
        $customer_list_info[$key]['area'] = strip_end_0($value['area_min']) . '-' . strip_end_0($value['area_max']);
        $customer_list_info[$key]['price'] = strip_end_0($price_min) . '-' . strip_end_0($price_max);
        if ($value['dist_id1'] && $value['street_id1']) {
          $dist_name1 = $this->district_model->get_distname_by_id($value['dist_id1']);
          $street_name1 = $this->district_model->get_streetname_by_id($value['street_id1']);
          $dist_street1 = $dist_name1 . '-' . $street_name1;
        }
        if ($value['dist_id2'] && $value['street_id2']) {
          $dist_name2 = $this->district_model->get_distname_by_id($value['dist_id2']);
          $street_name2 = $this->district_model->get_streetname_by_id($value['street_id2']);
          $dist_street2 = $dist_name2 . '-' . $street_name2;
        }
        if ($value['dist_id3'] && $value['street_id3']) {
          $dist_name3 = $this->district_model->get_distname_by_id($value['dist_id3']);
          $street_name3 = $this->district_model->get_streetname_by_id($value['street_id3']);
          $dist_street3 = $dist_name3 . '-' . $street_name3;
        }

        $customer_list_info[$key]['dist_street'] = $dist_street1 . ' ' . $dist_street2 . ' ' . $dist_street3;
        $customer_list_info[$key]['cmt_name'] = $value['cmt_name1'] . ' ' . $value['cmt_name2'] . ' ' . $value['cmt_name3'];

        /*if($dist_street1 && $dist_street2 && $dist_street3){
                    $customer_list_info[$key]['dist_street'] = $dist_street1 .' '. $dist_street2;
                }else{
                    $customer_list_info[$key]['dist_street'] = $dist_street1 .' '. $dist_street2 .' '. $dist_street3;
                }

                if($value['cmt_name1'] && $value['cmt_name2'] && $value['cmt_name3']){
                    $customer_list_info[$key]['cmt_name'] = $value['cmt_name1'] .' '. $value['cmt_name2'];
                }else{
                    $customer_list_info[$key]['cmt_name'] = $value['cmt_name1'] .' '. $value['cmt_name2'] .' '. $value['cmt_name3'];
                }*/

        //录入经济人信息
        $this->load->model('api_broker_sincere_model');
        $this->load->model('api_broker_model');

        $broker_id = $value['broker_id'];

        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
        $customer_list_info[$key]['broker_name'] = $brokerinfo['truename'];
        //合作成功率
        $this->load->model('cooperate_suc_ratio_base_model');
        $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id);
        $customer_list_info[$key]['cop_suc_ratio'] = $cop_succ_ratio_info['cop_succ_ratio'];
        //合作成功率平均值
        $avg_cop_suc_ratio = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
        $customer_list_info[$key]['differ_suc_ratio'] = $avg_cop_suc_ratio > 0 ? strip_end_0(($cop_succ_ratio_info['cop_succ_ratio'] - $avg_cop_suc_ratio) / $avg_cop_suc_ratio) : 0;
        //好评率
        $trust_appraise_count = $this->api_broker_sincere_model->
        get_trust_appraise_count($broker_id);
        if (empty($trust_appraise_count['good_rate'])) {
          $good_rate = '--';
        } else {
          $good_rate = strip_end_0($trust_appraise_count['good_rate']);
        }
        $customer_list_info[$key]['good_rate'] = $good_rate;
        //平均好评率
        $good_avg_rate = $this->api_broker_sincere_model->good_avg_rate($broker_id);
        $customer_list_info[$key]['differ_good_rate'] = $good_avg_rate['good_rate_avg_high'];

        //获取经纪人的信用值和等级
        $trust_level = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
        unset($trust_level['level']);
        $customer_list_info[$key]['trust_level'] = $trust_level;
        //信息 态度 业务 细节分值统计
        $appraise_info = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);
        unset($appraise_info['infomation']['level']);
        unset($appraise_info['attitude']['level']);
        unset($appraise_info['business']['level']);
        $customer_list_info[$key]['appraise_info'] = $appraise_info;


      }
    }
    $data['customer_list'] = $customer_list_info;
    //表单数据
    $data['post_param'] = $post_param;

    $this->result(1, '获取成功', $data);
    return;
  }


  //举报信息添加
  public function add_report()
  {
    $customer_id = $this->input->post('customer_id', TRUE);//客源的id
    $report_type = $this->input->post('report_type', TRUE);//举报类型
    $report_text = $this->input->post('report_text', TRUE);//举报的具内容
    $date = strtotime(date('Y-m-d h:i:s'));//举报时间
    $style = '4';
    $this->rent_customer_model->set_id($customer_id);
    $customer_info = $this->rent_customer_model->get_info_by_id();
    $brokered_id = $customer_info['broker_id'];//被举报人的id
    $brokered_name = $customer_info['broker_name'];//被举报人的姓名
    $broker_info = $this->user_arr;
    $brokerinfo_id = $broker_info['broker_id'];//举报人的id
    $brokerinfo_name = $broker_info['truename'];//举报人的姓名

    /*$this->buy_customer_model->set_id($customer_id);
            $customer_info = $this->buy_customer_model->get_info_by_id();*/

    $data_info = array();
    $area_min = $customer_info['area_min'];//最小面积
    $area_max = $customer_info['area_max'];//最大面积
    $data_info['buildarea'] = $area_min . '-' . $area_max;//房源面积
    $picmin = $customer_info['price_min'];//房源低价
    $picmax = $customer_info['price_max'];//房源售价
    $cmt_name = '';
    $cmt_name1 = $customer_info['cmt_name1'];//意向小区一
    $cmt_name2 = $customer_info['cmt_name2'];//意向小区二
    $cmt_name3 = $customer_info['cmt_name3'];//意向小区三
    if ($cmt_name1 != '') {
      $cmt_name .= $cmt_name1;
    } elseif ($cmt_name2 != '') {
      $cmt_name .= $cmt_name2;
    } elseif ($cmt_name3 != '') {
      $cmt_name .= $cmt_name3;
    }
    $data_info['blockname'] = $cmt_name;//小区名字
    $room_min = $customer_info['room_min'];
    $room_max = $customer_info['room_max'];
    $data_info['room'] = $room_min . '-' . $room_max;//室


    if ($style == 4) {
      $data_info['price'] = $picmin . '-' . $picmax;//求租价格
    } else {
      $data_info['price'] = $picmin . '-' . $picmax;//求购价格
    }

    //区属板块信息
    $this->load->model('district_model');

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
    //区属
    if (isset($customer_info['dist_id1']) && $customer_info['dist_id1'] != 0) {
      $dist_id1 = $dis[$customer_info['dist_id1']]['district'];//区属1
      $data_info['districtname'] = $dist_id1;
    }
    if (isset($customer_info['dist_id2']) && $customer_info['dist_id2'] != 0) {
      $dist_id2 = $dis[$customer_info['dist_id2']]['district'];//区属2
      $data_info['districtname'] .= ',' . $dist_id2;
    }
    if (isset($customer_info['dist_id3']) && $customer_info['dist_id3'] != 0) {
      $dist_id3 = $dis[$customer_info['dist_id3']]['district'];//区属3
      $data_info['districtname'] .= ',' . $dist_id3;
    }
    //板块
    if (isset($customer_info['street_id1']) && $customer_info['street_id1'] != 0) {
      $data_info['streetname'] = $stred[$customer_info['street_id1']]['streetname'];//板块
    }
    if (isset($customer_info['street_id2']) && $customer_info['street_id2'] != 0) {
      $data_info['streetname'] .= ',' . $stred[$customer_info['street_id2']]['streetname'];//板块
    }
    if (isset($customer_info['street_id3']) && $customer_info['street_id3'] != 0) {
      $data_info['streetname'] .= ',' . $stred[$customer_info['street_id3']]['streetname'];//板块
    }
    $data_info['fitment'] = $customer_info['fitment'];//装修
    $data_info['forward'] = $customer_info['forward'];//朝向
    $dbhouse_info = serialize($data_info);//序列化数组
    $tel = '';//业主电话
    if ($customer_info['telno1'] != '') {
      $tel = $customer_info['telno1'];
    } elseif ($customer_info['telno2'] != '') {
      $tel = $customer_info['telno2'];
    } elseif ($customer_info['telno3'] != '') {
      $tel = $customer_info['telno3'];
    }
    //加载图片地址
    $file = $this->input->post('img_name1', TRUE);
    $fileurl = implode(',', $file);
    $img_na = explode(',', $fileurl);
    $img_num = count($img_na);
    //图片名称
    $im_name = '';
    for ($i = 1; $i <= $img_num; $i++) {
      $im_name .= "证据";
      $im_name .= $i;
      $im_name .= ',';
    }
    $where = 'style =' . $style;
    $where .= ' AND number=' . $customer_id;
    $where .= ' AND type =' . $report_type;
    $this->load->model('report_model');
    $select_num = $this->report_model->count_by($where);//判断对该房源类型是否已经举报过
    if ($select_num > 0) {
      $data['msg'] = '该客源的类型你已经举报过了';
    }
    $insert_data = array(
      'broker_id' => $brokerinfo_id,
      'broker_name' => $brokerinfo_name,
      'brokered_id' => $brokered_id,
      'brokered_name' => $brokered_name,
      'style' => $style,
      'number' => $customer_id,
      'phone' => $tel,
      'photo_name' => $im_name,
      'type' => $report_type,
      'content' => $report_text,
      'photo_url' => $fileurl,
      'date_time' => $date,
      'status' => 1,
      'house_info' => $dbhouse_info
    );
    if (!empty($brokerinfo_id) && !empty($broker_id) && $brokerinfo_id != $broker_id && $select_num == 0) {
      $return_id = $this->report_model->insert($insert_data);
    }
    if (!empty($brokerinfo_id) && !empty($broker_id) && $brokerinfo_id != $broker_id) {
      $data['msg'] = '不能对自己的客源举报';
    }
    if ($return_id > 0) {
      $this->result('1', '举报成功');
    } else {
      $this->result('0', '举报失败', $data);
    }

  }

  /**
   * 求租客源详情页
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function details()
  {
    $data = array();
    $customer_id = intval($this->input->post('customer_id'));
    $type = intval($this->input->post('type'));
    //$customer_id = 1;
    //$type = 1;

    if ($customer_id > 0) {
      $this->rent_customer_model->set_id($customer_id);
      $customer_infos = $this->rent_customer_model->get_info_by_id();
      //新权限
      //范围（个人或全公司）
      //获得当前数据所属的经纪人id和门店id
      if ($type == '') {
        $owner_arr = array('broker_id' => $customer_infos['broker_id'], 'agency_id' => $customer_infos['agency_id'], 'company_id' => $customer_infos['company_id']);
        $view_other_per = $this->broker_permission_model->check('26', $owner_arr);
        if (!$view_other_per['auth']) {
          $this->result('-1', '暂无权限');
          exit();
        }
      }
      //以上代码被Fisher屏蔽，对详情不做权限判断，去列表做限制

      $customer_info = array();
      if (is_full_array($customer_infos)) {
        //新权限 判断是否明文显示业主电话
        /**
         * $owner_arr = array('broker_id'=>$customer_infos['broker_id'],'agency_id'=>$customer_infos['agency_id'],'company_id'=>$customer_infos['company_id']);
         * $is_phone_per = $this->broker_permission_model->check('16',$owner_arr);
         * $customer_info['is_phone_per'] = $is_phone_per['auth'];**/

        $this->load->model('district_model');
        $this->load->model('api_broker_model');

        $dist_street1 = '';
        $dist_street2 = '';
        $dist_street3 = '';

        //获取求购信息基本配置资料
        $conf_customer = $this->rent_customer_model->get_base_conf();
        if ($customer_infos['property_type']) {
          $customer_info['property_type'] = $conf_customer['property_type'][$customer_infos['property_type']];
        } else {
          $customer_info['property_type'] = '';
        }
        if ($customer_infos['public_type']) {
          $customer_info['public_type'] = $conf_customer['public_type'][$customer_infos['public_type']];
        } else {
          $customer_info['public_type'] = '';
        }
        if ($customer_infos['fitment']) {
          $customer_info['fitment'] = $conf_customer['fitment'][$customer_infos['fitment']];
        } else {
          $customer_info['fitment'] = '';
        }
        if ($customer_infos['status']) {
          $customer_info['status'] = $conf_customer['status'][$customer_infos['status']];
        } else {
          $customer_info['status'] = '';
        }
        $customer_info['lease'] = $conf_customer['lease'][$customer_infos['lease']];
        $customer_info['truename'] = $customer_infos['truename'];
        $customer_info['public_type_key'] = $customer_infos['public_type'];//信息属性
        $customer_info['is_share'] = $customer_infos['is_share'];
        $customer_info['property_type_key'] = $customer_infos['property_type'];//物业类型
        $customer_info['fitment_key'] = $customer_infos['fitment'];//装修
        $customer_info['status_key'] = $customer_infos['status'];//状态
        $customer_info['updatetime'] = date('Y-m-d', $customer_infos['updatetime']);//跟进时间
        $customer_info['creattime'] = date('Y-m-d H:i:s', $customer_infos['creattime']);//录入时间
        $customer_info['price_danwei'] = $customer_infos['price_danwei'];
        //楼层
        if ($customer_infos['floor_min'] != 0 && $customer_infos['floor_max'] != 0) {
          $customer_info['floor'] = $customer_infos['floor_min'] . '-' . $customer_infos['floor_max'];
        } else {
          $customer_info['floor'] = '';
        }
        //年龄
        if ($customer_infos['age_group'] > 0) {
          $customer_info['age_group'] = $conf_customer['age_group'][$customer_infos['age_group']];
        } else {
          $customer_info['age_group'] = '';
        }
        //职业
        if ($customer_infos['job_type'] > 0) {
          $customer_info['job_type'] = $conf_customer['job_type'][$customer_infos['job_type']];
        } else {
          $customer_info['job_type'] = '';
        }
        //目的
        if ($customer_infos['intent'] > 0) {
          $customer_info['intent'] = $conf_customer['intent'][$customer_infos['intent']];
        } else {
          $customer_info['intent'] = '';
        }
        $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($customer_infos['broker_id']);
        //获取门店所属公司名
        $company_name = '';
        if (isset($broker_info['company_id']) && !empty($broker_info['company_id'])) {
          $company_where_cond = array(
            'id' => $broker_info['company_id'],
            'company_id' => 0
          );
          $company_data = $this->agency_model->get_one_by($company_where_cond);
          if (is_full_array($company_data)) {
            $company_name = $company_data['name'];
          }
        }
        $customer_info['broker_name'] = $broker_info['truename'];//业务员姓名
        $customer_info['phone'] = $broker_info['phone'];//业务员电话
        $customer_info['photo'] = $broker_info['photo'];//业务员头像
        $customer_info['agency_name'] = $broker_info['agency_name'];//所属门店
        $customer_info['company_name'] = $company_name;//所属公司
        $customer_info['cop_suc_ratio'] = $broker_info['cop_suc_ratio'];//合作成功率
        if ($customer_info['price_danwei']) {
          $price_min = round($customer_infos['price_min'] / 30 / $customer_infos['area_min'], 2);
          $price_max = round($customer_infos['price_max'] / 30 / $customer_infos['area_max'], 2);
        } else {
          $price_min = $customer_infos['price_min'];
          $price_max = $customer_infos['price_max'];
        }

        $customer_info['area'] = strip_end_0($customer_infos['area_min']) . '-' . strip_end_0($customer_infos['area_max']);
        $customer_info['price'] = strip_end_0($price_min) . '-' . strip_end_0($price_max);
        $customer_info['room'] = $customer_infos['room_min'] . '-' . $customer_infos['room_max'];
        if ($customer_infos['dist_id1'] && $customer_infos['street_id1']) {
          $dist_name1 = $this->district_model->get_distname_by_id($customer_infos['dist_id1']);
          $street_name1 = $this->district_model->get_streetname_by_id($customer_infos['street_id1']);
          $dist_street1 = $dist_name1 . '-' . $street_name1;
        }
        if ($customer_infos['dist_id2'] && $customer_infos['street_id2']) {
          $dist_name2 = $this->district_model->get_distname_by_id($customer_infos['dist_id2']);
          $street_name2 = $this->district_model->get_streetname_by_id($customer_infos['street_id2']);
          $dist_street2 = $dist_name2 . '-' . $street_name2;
        }
        if ($customer_infos['dist_id3'] && $customer_infos['street_id3']) {
          $dist_name3 = $this->district_model->get_distname_by_id($customer_infos['dist_id3']);
          $street_name3 = $this->district_model->get_streetname_by_id($customer_infos['street_id3']);
          $dist_street3 = $dist_name3 . '-' . $street_name3;
        }
        $customer_info['dist_street'] = $dist_street1 . ' ' . $dist_street2 . ' ' . $dist_street3;
        $customer_info['cmt_name'] = $customer_infos['cmt_name1'] . ' ' . $customer_infos['cmt_name2'] . ' ' . $customer_infos['cmt_name3'];

        //录入经济人信息
        if ($type == 1) {
          if ($customer_infos['is_share'] == 0) {
            $this->result(0, '对不起该合作已被经纪人取消！');
            exit();
          }
          $this->load->model('api_broker_sincere_model');

          $broker_id = $customer_infos['broker_id'];
          $customer_info['broker_id'] = $broker_id;
          //判断是否自己发布的房源
          $customer_info['my_house'] = $this->user_arr['broker_id'] == $broker_id ? 1 : 0;
          //检测是否已经合作
          $this->load->model('cooperate_model');
          $is_applay_coop = $this->cooperate_model->check_is_cooped_by_cid(array($customer_id), 'rent', $this->user_arr['broker_id']);
          $customer_info['is_applay_coop'] = $is_applay_coop[$customer_id];
          //合作成功率
          $this->load->model('cooperate_suc_ratio_base_model');
          $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id);
          $customer_info['cop_suc_ratio'] = $cop_succ_ratio_info['cop_succ_ratio'];
          //合作成功率平均值
          $avg_cop_suc_ratio = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
          $customer_info['differ_suc_ratio'] = $avg_cop_suc_ratio > 0 ? strip_end_0(($cop_succ_ratio_info['cop_succ_ratio'] - $avg_cop_suc_ratio) / $avg_cop_suc_ratio) : 0;
          //好评率
          $trust_appraise_count = $this->api_broker_sincere_model->
          get_trust_appraise_count($broker_id);
          if (empty($trust_appraise_count['good_rate'])) {
            $good_rate = '--';
          } else {
            $good_rate = strip_end_0($trust_appraise_count['good_rate']);
          }
          $customer_info['good_rate'] = $good_rate;
          //平均好评率
          $good_avg_rate = $this->api_broker_sincere_model->good_avg_rate($broker_id);
          $customer_info['differ_good_rate'] = $good_avg_rate['good_rate_avg_high'];

          //获取经纪人的信用值和等级
          $trust_level = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
          unset($trust_level['level']);
          $customer_info['trust_level'] = $trust_level;
          //信息 态度 业务 细节分值统计
          $appraise_info = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);
          unset($appraise_info['infomation']['level']);
          unset($appraise_info['attitude']['level']);
          unset($appraise_info['business']['level']);
          $customer_info['appraise_info'] = $appraise_info;
        }
      }
      $data['data_info'] = $customer_info;
      $this->result(1, '获取成功', $data);
      return;
    } else {
      $this->result(0, '参数非法');
      return;
    }

  }

  /**
   * 求购客源保密信息访问记录
   *
   * @access  public
   * @param  int $customer_id 客源编号
   * @param  int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_brower_log($customer_id = '1')
  {
    //加载客源浏览日志MODEL
    $this->load->model('rent_customer_brower_model');

    $data['where_cond'] = array('customer_id' => $customer_id);
    //分组字段
    $group_by = 'broker_id';
    //分页开始
    $data['user_num'] = $this->rent_customer_brower_model->get_brower_log_num($data['where_cond']);
    $data['group_by_num'] = $this->rent_customer_brower_model->get_brower_log_group_num($customer_id);//分组总数
    $data['pagesize'] = 4; //设定每一页显示的记录数
    $data['pages'] = $data['group_by_num'] ? ceil($data['group_by_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    //排序字段
    $order_by_array = array('browertime', 'desc');
    //客源浏览日志数据
    $brower_list = $this->rent_customer_brower_model->get_brower_log($data['where_cond'], $data['offset'], $data['pagesize'], $order_by_array, $group_by);
    $brower_list2 = array();
    //数据重构
    foreach ($brower_list as $k => $v) {
      if (!empty($v['browertime'])) {
        $where = array('customer_id' => $customer_id, 'broker_id' => $v['broker_id']);
        $today_browertime = array(strtotime(date('Y-m-d')), strtotime(date('Y-m-d', strtotime('+1 day'))));//今天的时间戳范围
        $v['browerdate'] = date('Y-m-d H:i:s', $v['browertime']);
        $v['brower_num'] = $this->rent_customer_brower_model->get_brower_log_num($where);//总查阅次数
        $v['today_brower_num'] = $this->rent_customer_brower_model->get_today_brower_log_num($customer_id, $v['broker_id'], $today_browertime);//今日查阅次数
        $first_brower = $this->rent_customer_brower_model->get_brower_log($where, 0, 0, array('browertime', 'asc'));//最初浏览记录
        $recent_brower = $this->rent_customer_brower_model->get_brower_log($where, 0, 0, array('browertime', 'desc'));//最近浏览记录
        $v['first_brower'] = date('Y-m-d H:i:s', $first_brower[0]['browertime']);
        $v['recent_brower'] = $recent_brower[0]['browertime'];
      }
      $brower_list2[] = $v;
    }
    echo json_encode($brower_list2);

  }

  /**
   * 求购客源详情访问记录
   *
   * @access  public
   * @param  int $customer_id 客源编号
   * @param  int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_customer_view_log($customer_id = '1')
  {
    //客源访问日志信息
    $type = 'rent_customer';
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
    $data['cooperate_num'] = $this->cooperate_model->get_cooperate_num_by_cid($customer_id, 'rent');//浏览总数
    $data['pagesize'] = 2; //设定每一页显示的记录数
    $data['cooperate_pages'] = $data['cooperate_num'] ? ceil($data['cooperate_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['cooperate_page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['cooperate_page'] = ($data['cooperate_page'] > $data['cooperate_pages'] && $data['cooperate_pages'] != 0) ? $data['cooperate_pages'] : $data['cooperate_page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['cooperate_page'] - 1);   //计算记录偏移量

    $cooperate_log_list = $this->cooperate_model->get_cooperate_lists_by_cid($customer_id, 'rent', $data['offset'], $data['pagesize']);
    $cooperate_log_list2 = array();
    //合作基础配置文件
    $cooperate_conf = $this->cooperate_model->get_base_conf();
    foreach ($cooperate_log_list as $k => $v) {
      $v['creattime'] = date('Y-m-d H:i:s', $v['creattime']);
      $v['esta'] = $cooperate_conf['esta'][$v['esta']];
      $cooperate_log_list2[] = $v;
    }
    echo json_encode($cooperate_log_list2);
  }


  /**
   * 求租匹配页面
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function match()
  {
    $data = array();
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $company_id = $broker_info['company_id'];
    $agency_id = $broker_info['agency_id'];
    if ($company_id) {
      $view_other_per_data = $this->broker_permission_model->check('1');
      $data['view_other_per'] = $view_other_per_data['auth'];
    } else {
      $data['view_other_per'] = false;
    }

    $this->load->model('auth_review_model');
    //身份认证信息
    $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id . " AND type = 1", 0, 1);
    $ident_auth = (is_full_array($ident_info) && $ident_info['status'] == 2) ? 1 : 0;
    //获取系统公司基本设置
    $this->load->model('agency_basic_setting_model');
    $info = $this->agency_basic_setting_model->get_data_by_agency_id($agency_id);
    $company_setting = $info["0"];
    if (empty($company_setting)) {
      $result = $this->agency_basic_setting_model->get_default_data();
      $company_setting = $result["0"];
    }
    $open_cooperate = $company_setting['open_cooperate'];
    if ($ident_auth && $open_cooperate) {
      $data['view_share_per'] = true;
    } else {
      $data['view_share_per'] = false;
    }

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $customer_id = intval($post_param['customer_id']);
    $customer_info = array();

    if ($customer_id > 0) {
      $this->rent_customer_model->set_id($customer_id);
      $this->rent_customer_model->set_search_fields(array('truename', 'room_min', 'room_max', 'area_min', 'area_max',
        'price_min', 'price_max', 'price_danwei', 'property_type', 'dist_id1', 'street_id1', 'dist_id2', 'street_id2',
        'dist_id3', 'street_id3'));
      $customers = $this->rent_customer_model->get_info_by_id();

      if (is_full_array($customers)) {
        $this->load->model('district_model');
        $dist_street1 = '';
        $dist_street2 = '';
        $dist_street3 = '';

        $customer_info['truename'] = $customers['truename'];
        $customer_info['room'] = $customers['room_min'] . '-' . $customers['room_max'];
        $customer_info['price_danwei'] = $customers['price_danwei'];
        if ($customer_info['price_danwei']) {
          $price_min = round($customers['price_min'] / 30 / $customers['area_min'], 2);
          $price_max = round($customers['price_max'] / 30 / $customers['area_max'], 2);
        } else {
          $price_min = $customers['price_min'];
          $price_max = $customers['price_max'];
        }

        $customer_info['area'] = strip_end_0($customers['area_min']) . '-' . strip_end_0($customers['area_max']);
        $customer_info['price'] = strip_end_0($price_min) . '-' . strip_end_0($price_max);

        $customer_info['property_type'] = $customers['property_type'];

        $dist_id = array();
        for ($i = 0; $i < 3; $i++) {
          $num = $i + 1;
          $dist_id[$i]['dist_id'] = $customers['dist_id' . $num];
        }
        $customer_info['dist_id'] = $dist_id;

        if ($customers['dist_id1'] && $customers['street_id1']) {
          $dist_name1 = $this->district_model->get_distname_by_id($customers['dist_id1']);
          $street_name1 = $this->district_model->get_streetname_by_id($customers['street_id1']);
          $dist_street1 = $dist_name1 . '-' . $street_name1;
        }
        if ($customers['dist_id2'] && $customers['street_id2']) {
          $dist_name2 = $this->district_model->get_distname_by_id($customers['dist_id2']);
          $street_name2 = $this->district_model->get_streetname_by_id($customers['street_id2']);
          $dist_street2 = $dist_name2 . '-' . $street_name2;
        }
        if ($customers['dist_id3'] && $customers['street_id3']) {
          $dist_name3 = $this->district_model->get_distname_by_id($customers['dist_id3']);
          $street_name3 = $this->district_model->get_streetname_by_id($customers['street_id3']);
          $dist_street3 = $dist_name3 . '-' . $street_name3;
        }

        $customer_info['dist_street'] = $dist_street1 . ' ' . $dist_street2 . ' ' . $dist_street3;
        /*if($dist_street1 && $dist_street2 && $dist_street3){
                    $customer_info['dist_street'] = $dist_street1 .' '. $dist_street2;
                }else{
                    $customer_info['dist_street'] = $dist_street1 .' '. $dist_street2 .' '. $dist_street3;
                }*/
        //检测是否已经合作
        $this->load->model('cooperate_model');
        $is_applay_coop = $this->cooperate_model->check_is_cooped_by_cid(array($customer_id), 'rent', $this->user_arr['broker_id']);
        $customer_info['is_applay_coop'] = $is_applay_coop[$customer_id];
        $data['customer_info'] = $customer_info;

        //价格区间
        if (empty($post_param['price_min'])) {
          $post_param['price_min'] = $customers['price_min'];
        }
        if (empty($post_param['price_max'])) {
          $post_param['price_max'] = $customers['price_max'];
        }
        //物业类型
        if (empty($post_param['property_type'])) {
          $post_param['property_type'] = $customers['property_type'];
        }
        //区属
        if (empty($post_param['dist_id'])) {
          $post_param['dist_id'] = $dist_id;
        }
        //面积条件
        if (empty($post_param['area_min'])) {
          $post_param['area_min'] = '';
        }
        if (empty($post_param['area_max'])) {
          $post_param['area_max'] = '';
        }
        //时间条件
        if (empty($post_param['match_time'])) {
          $post_param['match_time'] = 3;
        }
        //户型条件（只匹配几室）
        if (empty($post_param['room_min'])) {
          $post_param['room_min'] = $customers['room_min'];
        }
        if (empty($post_param['room_max'])) {
          $post_param['room_max'] = $customers['room_max'];
        }
        // 范围
        if (empty($post_param['match_range'])) {
          $post_param['match_range'] = 1;
        }
        $data['post_param'] = $post_param;

        //根据条件搜索房源
        $this->load->model('rent_house_model');
        /** 分页参数 */
        $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination($page, $pagesize);

        //查询范围
        $cond_where = $this->get_house_range($post_param['match_range']);
        //表单提交参数组成的查询条件
        $cond_where_ext = $this->_get_house_cond_str($post_param);
        $cond_where .= $cond_where_ext;
        //合作中心请求
        if ($post_param['is_public']) {
          $cond_where .= " and isshare = 1 ";
        }
        //获取列表内容
        $this->rent_house_model->set_search_fields(array('block_name', 'isshare', 'nature', 'sell_type',
          'district_id', 'street_id', 'fitment', 'floor', 'totalfloor', 'room', 'hall', 'buildarea', 'price', 'price_danwei'));
        $houses = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, 'updatetime', 'DESC');

        $house_list = array();
        if (is_full_array($houses)) {
          $this->load->model('house_config_model');
          //获取出售信息基本配置资料
          $config = $this->house_config_model->get_config();

          foreach ($houses as $key => $value) {
            $house_list[$key]['house_id'] = $value['id'];
            $house_list[$key]['block_name'] = $value['block_name'];
            $house_list[$key]['is_share'] = $value['isshare'];
            $house_list[$key]['nature_key'] = $value['nature'];
            $house_list[$key]['nature'] = $config['nature'][$value['nature']];//公盘私盘
            $house_list[$key]['property_type_key'] = $value['sell_type'];
            $house_list[$key]['property_type'] = $config['sell_type'][$value['sell_type']];//出售类型
            $dist_name = $this->district_model->get_distname_by_id($value['district_id']);
            $street_name = $this->district_model->get_streetname_by_id($value['street_id']);
            $house_list[$key]['dist_street'] = $dist_name . '-' . $street_name;
            $house_list[$key]['fitment_key'] = $value['fitment'];
            $house_list[$key]['fitment'] = $config['fitment'][$value['fitment']];//装修程度
            $house_list[$key]['floor_info'] = $value['floor'] . '/' . $value['totalfloor'];
            $house_list[$key]['floor_info'] = '';
            $house_list[$key]['room_hall'] = $value['room'] . '室' . $value['hall'] . '厅';
            $house_list[$key]['area'] = strip_end_0($value['buildarea']);
            $house_list[$key]['price_danwei'] = $value['price_danwei'];
            if ($value['price_danwei']) {
              $price = round($value['price'] / 30 / $value['buildarea'], 2);
            } else {
              $price = $value['price'];
            }
            $house_list[$key]['price'] = strip_end_0($price);

          }
        }
        $data['house_list'] = $house_list;
        $this->result(1, '获取成功', $data);
        return;
      } else {
        $this->result(2, '无此信息');
        return;
      }
    } else {
      $this->result(0, '参数非法');
      return;
    }
  }


  /**
   * 求租详情保密信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function get_secret_info()
  {
    $customer_id = intval($this->input->post('customer_id'));
    $devicetype = $this->input->post('api_key', TRUE);
    $deviceid = $this->input->post('deviceid', TRUE);
    //录入经纪人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $this_broker_group_id = $this->user_arr['group_id'];
    $this->load->model('broker_model');
    $customer_info = array();

    if ($customer_id) {
      //新权限
      //获得当前数据所属的经纪人id和门店id
      $this->rent_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'public_type'));
      $this->rent_customer_model->set_id($customer_id);
      $owner_arr = $this->rent_customer_model->get_info_by_id();
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
        $this->result(2, '很遗憾您无权查看客户电话');
        return;
      } else {
            if (!$agency_secret_per || $this_broker_group_id != '2') {
          $this->result(2, '很遗憾您无权查看客户电话');
          return;
        }
      }

      $this->rent_customer_model->set_id($customer_id);
      $select_feilds = array('broker_id', 'truename', 'telno1',
        'telno2', 'telno3', 'lock', 'public_type');
      $this->rent_customer_model->set_search_fields($select_feilds);
      $customer_info = $this->rent_customer_model->get_info_by_id();

      $this->load->model('broker_view_secrecy_model');
      //查看自己的房客源数据，不记录次数；已经查看过的数据，不重复记录。
      $where_cond = array(
        'broker_id' => $broker_id,
        'view_type' => 4,
        'row_id' => $customer_id
      );
      $query_result = $this->broker_view_secrecy_model->get_one_by($where_cond);
      if ($customer_info['broker_id'] != $broker_info['broker_id'] && empty($query_result)) {
        $is_insert = true;
      } else {
        $is_insert = false;
      }

      $check_baomi_time = $this->broker_model->check_baomi_time($this->company_basic_arr,
        $this->user_arr, 4, $customer_id, $is_insert);
      if (!$check_baomi_time['status']) {
        $this->result(0, '您每天可查看保密信息' . $check_baomi_time['secrecy_num']
          . '次,现在已达上限');
        return false;
      }

      //判断是否锁定，有无权限查看（锁定状态下，发布人和锁定人可以查看）
      if (!empty($customer_info) && ($customer_info['lock'] == 0 || in_array($broker_id, array($customer_info['broker_id'], $customer_info['lock'])))) {
        $data['truename'] = $customer_info['truename'];

        $data['telno'] = empty($customer_info['telno1']) ? (empty($customer_info['telno2']) ? $customer_info['telno3'] : $customer_info['telno2']) : $customer_info['telno1'];
        $this->info_count($customer_id, 8);//记录查看保密信息的记录

        //操作日志
        $add_log_param = array();
        $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['agency_id'];
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $this->user_arr['truename'];
        $add_log_param['type'] = 47;
        $add_log_param['text'] = '求租客源 ' . 'QZ' . $customer_id;
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

        $this->result(1, '获取成功', $data);
        return;
      } else {
        $this->result(2, '很遗憾您无权查看客户电话');
        return;
      }
    } else {
      $this->result(0, '参数非法');
      return;
    }
  }


  /**
   * 验证客源在本公司是唯一的
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function check_unique_customer($telno1, $cid = 0)
  {
    //根据基本设置，判断客源是否去重
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $rent_customer_unique = intval($company_basic_data['rent_customer_unique']);
    } else {
      $rent_customer_unique = 0;
    }
    if (isset($rent_customer_unique) && 1 == $rent_customer_unique) {
      $msg = 0;
      $customer_num = 0;

      if (!empty($telno1)) {
        $cond_telno_str = $telno1;

        //录入经纪人信息
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
          $this->load->model('api_broker_model');
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
        if ($cid > 0) {
          $cond_where = "id > 0 AND id != '" . $cid . "'";
        } else {
          $cond_where = "id > 0 ";
        }

        $cond_where .= " AND ( telno1 IN ($cond_telno_str) OR telno2 IN ($cond_telno_str) OR telno3 IN ($cond_telno_str)) ";
        if (!empty($agency_ids)) {
          $cond_where .= " AND agency_id IN ($agency_ids)  ";
        }
        $customer_num = $this->rent_customer_model->get_rentnum_by_cond($cond_where);
      }
      $msg = $customer_num > 0 ? 1 : 0;
    } else {
      $msg = 0;
    }

    return $msg;
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
    //获得基本设置数据
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $house_customer_system = intval($company_basic_data['house_customer_system']);
      $rent_customer_private_num = intval($company_basic_data['rent_customer_private_num']);
      $rent_customer_unique = intval($company_basic_data['rent_customer_unique']);
    } else {
      $house_customer_system = $rent_customer_private_num = $rent_customer_unique = 0;
    }
    //添加客户信息
    $customer_info = array();

    //录入经纪人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $broker_name = strip_tags($broker_info['truename']);
    $agency_id = intval($broker_info['agency_id']);
    $company_id = intval($broker_info['company_id']);

    //客源信息
    $customer_info['broker_id'] = $broker_id;
    $customer_info['broker_name'] = $broker_name;
    $customer_info['agency_id'] = $agency_id;
    $customer_info['company_id'] = $company_id;

    $truename = $this->input->post('truename', TRUE);
    $devicetype = $this->input->post('api_key', TRUE);
    $deviceid = $this->input->post('deviceid', TRUE);
    //验证真是姓名是不是符合要求
    if (preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $truename)) {
      //验证真是姓名长度是否符合要求
      if (abslength(trim($truename)) > 5) {
        $this->result(2, '业主姓名最多5个字符');
        return;
      }

      $customer_info['truename'] = $truename;
    } else {
      $this->result(3, '业主姓名必填，只能包含汉字、字母、数字');
      return;
    }

    //用户手机号码
    $telno = $this->input->post('telno', TRUE);
    if (empty($telno)) {
      $this->result(4, '手机号码不能为空');
      return;
    } else {
      if (preg_match('/(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/', $telno)) {
        $customer_info['telno1'] = $telno;
      } else {
        $this->result(5, '手机号码[' . $telno . ']格式不正确');
        return;
      }
    }

    //客源唯一性判断
    $customer_num = $this->check_unique_customer($telno);
    if ($customer_num == 1) {
      $this->result(16, '操作失败，该客源已经存在');
      return;
    }

    //装修
    $customer_info['fitment'] = intval($this->input->post('fitment', TRUE));
    //客源属性验证，如果公司设置无法发布私盘，则无法选择私盘
    $customer_info['public_type'] = intval($this->input->post('public_type', TRUE));
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
      $private_num = $this->rent_customer_model->get_rentnum_by_cond($private_where_cond);
      if ('1' == $customer_info['public_type'] && $private_num >= $rent_customer_private_num) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘制';
      }
    } else {
      $house_private_check = true;
    }

    $customer_info['is_share'] = intval($this->input->post('is_share', TRUE));
    //判断是否开启了合作审核
    if (isset($company_basic_data['check_cooperate']) && '1' == $company_basic_data['check_cooperate']) {
      if (isset($customer_info['is_share']) && '1' == $customer_info['is_share']) {
        $customer_info['is_share'] = 2;
      }
    }
    if (1 == intval($customer_info['is_share'])) {
      $customer_info['set_share_time'] = time();
    } else {
      $customer_info['set_share_time'] = 0;
    }

    $customer_info['is_share_taofang'] = $this->input->post('is_share_taofang', TRUE);

    if ($customer_info['is_share'] != 0 && $customer_info['is_share'] != 1 && $customer_info['is_share'] != 2) {
      $this->result(6, '是否合作必须选择');
      return;
    }

    if ($customer_info['is_share_taofang'] != 0 && $customer_info['is_share_taofang'] != 1) {
      $this->result(7, '同步必须选择');
      return;
    }
    $customer_info['lease'] = intval($this->input->post('lease', TRUE));
    $customer_info['room_min'] = intval($this->input->post('room_min', TRUE));
    $customer_info['room_max'] = intval($this->input->post('room_max', TRUE));
    //物业类型
    $customer_info['property_type'] = intval($this->input->post('property_type', TRUE));
    //户型验证
    if ($customer_info['property_type'] == 1 || $customer_info['property_type'] == 2) {
      if ($customer_info['room_min'] < 1 || $customer_info['room_max'] < 1) {
        $this->result(8, '户型最小数值为整数1');
        return;
      }

      if ($customer_info['room_min'] > $customer_info['room_max']) {
        $this->result(9, '户型数据异常');
        return;
      }
    } else { //不需要户型的强制转换为0，防止前台切换其它户型输入后产生脏数据
      $customer_info['room_min'] = 0;
      $customer_info['room_max'] = 0;
    }

    $customer_info['area_min'] = floatval($this->input->post('area_min', TRUE));
    $customer_info['area_max'] = floatval($this->input->post('area_max', TRUE));
    //面积验证
    if ($customer_info['area_min'] < 1 || $customer_info['area_max'] < 1) {
      $this->result(10, '面积最小数值为整数1');
      return;
    }

    if ($customer_info['area_min'] > $customer_info['area_max']) {
      $this->result(11, '面积数据异常');
      return;
    }
    //单位
    $customer_info['price_danwei'] = intval($this->input->post('price_danwei', TRUE));
    $price_min = floatval($this->input->post('price_min', TRUE));
    $price_max = floatval($this->input->post('price_max', TRUE));

    //价格验证
    if ($price_min < 1 || $price_max < 1) {
      $this->result(12, '价格最小数值为整数1');
      return;
    }

    if ($price_min > $price_max) {
      $this->result(13, '价格数据异常');
      return;
    }
    if (!$house_private_check) {
      $this->result(14, $house_private_check_text);
      return;
    }


    switch ($customer_info['price_danwei']) {
      case 0:
        $customer_info['price_min'] = $price_min;
        $customer_info['price_max'] = $price_max;
        break;
      case 1:
        $customer_info['price_min'] = $price_min * $customer_info['area_min'] * 30;
        $customer_info['price_max'] = $price_max * $customer_info['area_max'] * 30;
        break;
      default:
        $this->result(14, '价格单位非法');
        return;
    }

    if ($customer_info['price_min'] > 99999999.99 || $customer_info['price_max'] > 99999999.99) {
      $this->result(15, '价格数值超过最大范围');
      return;
    }

    //区属板块
    $district_arr = $this->input->post('dist_id', TRUE);
    $district_arr = json_decode($district_arr);
    $street_arr = $this->input->post('street_id', TRUE);
    $street_arr = json_decode($street_arr);
    $dist_num = count($district_arr);
    //区属个数验证
    for ($i = 1; $i <= $dist_num; $i++) {
      if ($district_arr[$i - 1] > 0 && $street_arr[$i - 1] > 0) {
        $customer_info['dist_id' . $i] = $district_arr[$i - 1];
        $customer_info['street_id' . $i] = $street_arr[$i - 1];
      }
    }

    //楼盘信息
    $cmt_arr = $this->input->post('cmt_id', TRUE);
    $cmt_arr = json_decode($cmt_arr);
    $cmt_num = count($cmt_arr);

    for ($i = 1; $i <= $cmt_num; $i++) {
      $cmt_id = intval($cmt_arr[$i - 1]);

      if ($cmt_id > 0) {
        $customer_info['cmt_id' . $i] = $cmt_id;
      }
    }

    //楼盘名称信息
    $cmtname_arr = $this->input->post('cmt_name', TRUE);
    $cmtname_arr = json_decode($cmtname_arr);
    $cmtname_num = count($cmtname_arr);

    for ($i = 1; $i <= $cmtname_num; $i++) {
      $cmt_name = trim(strip_tags($cmtname_arr[$i - 1]));
      if ($cmt_name != '') {
        $customer_info['cmt_name' . $i] = $cmt_name;
      }
    }


    $customer_info['creattime'] = time();
    $customer_info['updatetime'] = $customer_info['creattime'];
    $customer_info['ip'] = get_ip();
    $customer_info['status'] = $this->input->post('status', TRUE);

    //判断基本设置是否开启合作中心
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    if ('0' === $data['open_cooperate'] && !empty($customer_info['is_share'])) {
      $this->result('-1', '当前公司尚未开启合作中心');
      exit();
    }

    //职业
    $customer_info['job_type'] = $this->input->post('job_type', TRUE);
    //年龄
    $customer_info['age_group'] = $this->input->post('age_group', TRUE);
    //目的
    $customer_info['intent'] = $this->input->post('intent', TRUE);
    //楼层（非必填项）
    $customer_info['floor_min'] = intval($this->input->post('floor_min', TRUE));
    $customer_info['floor_max'] = intval($this->input->post('floor_max', TRUE));

    $result = $this->rent_customer_model->add_rent_customer_info($customer_info);

    //加载跟进信息model
    $this->load->model('follow_model');
    $needarr = array();
    $needarr['broker_id'] = $broker_id;//经纪人的ID
    $needarr['customer_id'] = $result;//客源的ID
    $needarr['company_id'] = $company_id;//公司di
    $needarr['agency_id'] = $agency_id;//门店id
    $needarr['type'] = 4;//值为(1.2.3.4)对应房源类型1.出售 2.出租 3.求购 4.求租

    //添加跟进信息返回 boolean 是否添加成功，TRUE-成功，FAlSE-失败
    $this->follow_model->customer_inster($needarr);
    //判断该房源是否设置了合作
    $credit_msg = '';
    if ('1' == $customer_info['is_share'] || '2' == $customer_info['is_share']) {
      $follow_text = '';
      if ('1' == $customer_info['is_share']) {
        $follow_text = '是否合作:否>>是';
      } else if ('2' == $customer_info['is_share']) {
        $follow_text = '是否合作:否>>审核中';
      }
      $needarrt = array();
      $needarrt['broker_id'] = $broker_id;
      $needarrt['type'] = 4;
      $needarrt['agency_id'] = $agency_id;//门店ID
      $needarrt['company_id'] = $company_id;//总公司id
      $needarrt['customer_id'] = $result;
      $needarrt['text'] = $follow_text;
      $boolt = $this->follow_model->customer_inster_share($needarrt);
      if ('1' == $customer_info['is_share']) {
        //增加积分
        $this->load->model('api_broker_credit_model');
        $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
        $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $result), 4);
        //判断积分是否增加成功
        if (is_full_array($credit_result) && $credit_result['status'] == 1) {
          $credit_msg .= $credit_result['score'] . '积分';
        }
        //增加等级分值
        $this->load->model('api_broker_level_model');
        $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
        $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $result), 4);
        //判断成长值是否增加成功
        if (is_full_array($level_result) && $level_result['status'] == 1) {
          $credit_msg .= ',' . $level_result['score'] . '积分';
        }
      }
    }

    if ($result > 0) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $broker_info['company_id'];
      $add_log_param['agency_id'] = $broker_info['agency_id'];
      $add_log_param['broker_id'] = $broker_id;
      $add_log_param['broker_name'] = $broker_info['truename'];
      $add_log_param['type'] = 10;
      $add_log_param['text'] = '求租客源 ' . 'QZ' . $result;
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

      //求租客源录入成功记录工作统计日志
      $this->info_count($result, 1);
      if ($customer_info['is_share'] == 2) {
        $this->result(1, '您发布的合作店长审核中，请耐心等待');
      } else {
        $this->result(1, '客源信息已成功发布！' . $credit_msg);
      }
    } else {
      $this->result(0, '发布失败');
    }
    return;
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
    //获得基本设置数据
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $house_customer_system = intval($company_basic_data['house_customer_system']);
      $rent_customer_private_num = intval($company_basic_data['rent_customer_private_num']);
    } else {
      $house_customer_system = $rent_customer_private_num = 0;
    }
    //添加客户信息
    $customer_info = array();

    $devicetype = $this->input->post('api_key', TRUE);
    $deviceid = $this->input->post('deviceid', TRUE);
    //"id = '".$customer_id."' AND borker_id = '".$borker_id."'"
    //$do_key = strip_tags($this->input->post('do_key'));
    $customer_id = intval($this->input->post('customer_id'));
    /*$customer_broker_id = intval($this->input->post('customer_broker_id'));

        if( md5($customer_id.$customer_broker_id.'_365mls') !=  $do_key)
        {
            //体型参数异常，跳转到列表页
        }*/

    //权限
    $this->rent_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->rent_customer_model->set_id($customer_id);
    $owner_arr = $this->rent_customer_model->get_info_by_id();
    $customer_modify_per = $this->broker_permission_model->check('18', $owner_arr);
    //修改客源关联门店权限
    $agency_customer_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '13');
    if (!$customer_modify_per['auth']) {
      $this->result('-1', '暂无权限');
      exit();
    } else {
      if (!$agency_customer_modify_per) {
        $this->result('-1', '暂无权限');
        exit();
      }
    }

    //录入经纪人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $broker_name = strip_tags($broker_info['truename']);
    $agency_id = intval($broker_info['agency_id']);
    $company_id = intval($broker_info['company_id']);

    $truename = $this->input->post('truename', TRUE);
    //验证真是姓名是不是符合要求
    if (preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $truename)) {
      //验证真是姓名长度是否符合要求
      if (abslength(trim($truename)) > 5) {
        $this->result(2, '业主姓名最多5个字符');
        return;
      }

      $customer_info['truename'] = $truename;
    } else {
      $this->result(3, '业主姓名必填，只能包含汉字、字母、数字');
      return;
    }

    //用户手机号码
    $telno = $this->input->post('telno', TRUE);
    if (empty($telno)) {
      $this->result(4, '手机号码不能为空');
      return;
    } else {
      if (preg_match('/(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/', $telno)) {
        $customer_info['telno1'] = $telno;
      } else {
        $this->result(5, '手机号码[' . $telno . ']格式不正确');
        return;
      }
    }

    //客源唯一性判断
//        $customer_num = $this->check_unique_customer($telno , $customer_id);
//        if($customer_num==1){
//            $this->result(16,'操作失败，该客源已经存在');
//            return;
//        }

    $customer_info['lease'] = intval($this->input->post('lease', TRUE));
    //装修
    $customer_info['fitment'] = intval($this->input->post('fitment', TRUE));
    //客源属性验证，如果公司设置无法发布私盘，则无法选择私盘
    $customer_info['public_type'] = intval($this->input->post('public_type', TRUE));

    $customer_info['is_share'] = intval($this->input->post('is_share', TRUE));
    //判断是否开启了合作审核
    if (isset($company_basic_data['check_cooperate']) && '1' == $company_basic_data['check_cooperate']) {
      if (isset($customer_info['is_share']) && '1' == $customer_info['is_share']) {
        $customer_info['is_share'] = 2;
      }
    }
    if (1 == intval($customer_info['is_share'])) {
      $customer_info['set_share_time'] = time();
    } else {
      $customer_info['set_share_time'] = 0;
    }

    $customer_info['is_share_taofang'] = $this->input->post('is_share_taofang', TRUE);

    if ($customer_info['is_share'] != 0 && $customer_info['is_share'] != 1 && $customer_info['is_share'] != 2) {
      $this->result(6, '是否合作必须选择');
      return;
    }
    if ($customer_info['is_share_taofang'] != 0 && $customer_info['is_share_taofang'] != 1) {
      $this->result(7, '同步必须选择');
      return;
    }

    $customer_info['room_min'] = intval($this->input->post('room_min', TRUE));
    $customer_info['room_max'] = intval($this->input->post('room_max', TRUE));
    //物业类型
    $customer_info['property_type'] = intval($this->input->post('property_type', TRUE));
    //户型验证
    if ($customer_info['property_type'] == 1 || $customer_info['property_type'] == 2) {
      if ($customer_info['room_min'] < 1 || $customer_info['room_max'] < 1) {
        $this->result(8, '户型最小数值为整数1');
        return;
      }

      if ($customer_info['room_min'] > $customer_info['room_max']) {
        $this->result(9, '户型数据异常');
        return;
      }
    } else { //不需要户型的强制转换为0，防止前台切换其它户型输入后产生脏数据
      $customer_info['room_min'] = 0;
      $customer_info['room_max'] = 0;
    }

    $customer_info['area_min'] = floatval($this->input->post('area_min', TRUE));
    $customer_info['area_max'] = floatval($this->input->post('area_max', TRUE));
    $customer_info['status'] = $this->input->post('status', TRUE);
    //面积验证
    if ($customer_info['area_min'] < 1 || $customer_info['area_max'] < 1) {
      $this->result(10, '面积最小数值为整数1');
      return;
    }

    if ($customer_info['area_min'] > $customer_info['area_max']) {
      $this->result(11, '面积数据异常');
      return;
    }
    //单位
    $customer_info['price_danwei'] = intval($this->input->post('price_danwei', TRUE));

    $price_min = floatval($this->input->post('price_min', TRUE));
    $price_max = floatval($this->input->post('price_max', TRUE));
    //价格验证
    if ($price_min < 1 || $price_max < 1) {
      $this->result(12, '价格最小数值为整数1');
      return;
    }

    if ($price_min > $price_max) {
      $this->result(13, '价格数据异常');
      return;
    }

    switch ($customer_info['price_danwei']) {
      case 0:
        $customer_info['price_min'] = $price_min;
        $customer_info['price_max'] = $price_max;
        break;
      case 1:
        $customer_info['price_min'] = $price_min * $customer_info['area_min'] * 30;
        $customer_info['price_max'] = $price_max * $customer_info['area_max'] * 30;
        break;
      default:
        $this->result(14, '价格单位非法');
        return;
    }

    if ($customer_info['price_min'] > 99999999.99 || $customer_info['price_max'] > 99999999.99) {
      $this->result(15, '价格数值超过最大范围');
      return;
    }

    //区属板块
    $district_arr = $this->input->post('dist_id', TRUE);
    $district_arr = json_decode($district_arr);
    $street_arr = $this->input->post('street_id', TRUE);
    $street_arr = json_decode($street_arr);
    //$dist_num = count($district_arr);
    //区属个数验证
    for ($i = 1; $i <= 3; $i++) {
      $dist_id = intval($district_arr[$i - 1]);
      $street_id = intval($street_arr[$i - 1]);
      $customer_info['dist_id' . $i] = $dist_id > 0 ? $dist_id : 0;
      $customer_info['street_id' . $i] = $street_id > 0 ? $street_id : 0;
    }

    //楼盘信息
    $cmt_arr = $this->input->post('cmt_id', TRUE);
    $cmt_arr = json_decode($cmt_arr);
    //$cmt_num = count($cmt_arr);
    for ($i = 1; $i <= 3; $i++) {
      $cmt_id = intval($cmt_arr[$i - 1]);
      $customer_info['cmt_id' . $i] = $cmt_id > 0 ? $cmt_id : 0;
    }

    //楼盘名称信息
    $cmtname_arr = $this->input->post('cmt_name', TRUE);
    $cmtname_arr = json_decode($cmtname_arr);
    //$cmtname_num = count($cmtname_arr);
    for ($i = 1; $i <= 3; $i++) {
      $cmt_name = trim(strip_tags($cmtname_arr[$i - 1]));
      $customer_info['cmt_name' . $i] = !empty($cmt_name) ? $cmt_name : '';
    }

    $customer_info['updatetime'] = time();

    //旧数据
    $this->rent_customer_model->set_search_fields(array());
    $this->rent_customer_model->set_id($customer_id);
    $old = $this->rent_customer_model->get_info_by_id();

    //基本设置，房客源制判断
    $house_private_check = true;
    //公盘私客制
    if (2 == $house_customer_system) {
      if ('1' == $old['public_type'] && '2' == $customer_info['public_type']) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘私客制';
      }
    } else if (3 == $house_customer_system) {
      //公盘制 获得当前经纪人的私盘数量
      $private_where_cond = 'broker_id = "' . $broker_id . '"' . ' and status = 1 and public_type = 1';
      $private_num = $this->rent_customer_model->get_rentnum_by_cond($private_where_cond);
      if ('2' == $old['public_type'] && '1' == $customer_info['public_type'] && $private_num >= $rent_customer_private_num) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘制';
      }
    } else {
      $house_private_check = true;
    }
    if (!$house_private_check) {
      $this->result('14', $house_private_check_text);
      exit();
    }

    //判断基本设置是否开启合作中心
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    if ('0' === $data['open_cooperate'] && $old['is_share'] != $customer_info['is_share']) {
      $this->result('-1', '当前公司尚未开启合作中心');
      exit();
    }
    //职业
    $customer_info['job_type'] = $this->input->post('job_type', TRUE);
    //年龄
    $customer_info['age_group'] = $this->input->post('age_group', TRUE);
    //目的
    $customer_info['intent'] = $this->input->post('intent', TRUE);
    //楼层（非必填项）
    $customer_info['floor_min'] = intval($this->input->post('floor_min', TRUE));
    $customer_info['floor_max'] = intval($this->input->post('floor_max', TRUE));
    $cond_where = "id = " . $customer_id;
    $result = $this->rent_customer_model->update_customerinfo_by_cond($customer_info, $cond_where);

    //新数据
    $new = $this->rent_customer_model->get_info_by_id_2($customer_id);
    //发布合作房源增加积分
    $credit_msg = '';
    if ($customer_info['is_share'] == 1) {
      //增加积分
      if ($old['is_share'] != $customer_info['is_share']) {
        //增加积分
        $this->load->model('api_broker_credit_model');
        $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
        $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $customer_id), 4);
        //判断积分是否增加成功
        if (is_full_array($credit_result) && $credit_result['status'] == 1) {
          $credit_msg .= '+' . $credit_result['score'] . '积分';
        }
        //增加等级分值
        $this->load->model('api_broker_level_model');
        $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
        $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $customer_id), 4);
        //判断成长值是否增加成功
        if (is_full_array($level_result) && $level_result['status'] == 1) {
          $credit_msg .= ',+' . $level_result['score'] . '成长值';
        }
      }
    }
    //求租客源修改跟进
    $text = $this->customer_follow_match($new, $old);
    $this->load->model('follow_model');
    $needarr = array();
    $needarr['broker_id'] = $broker_id;
    $needarr['customer_id'] = $customer_id;
    $needarr['company_id'] = $company_id;
    $needarr['agency_id'] = $agency_id;;
    $needarr['type'] = 4;
    $needarr['text'] = $text;
    if (!empty($text)) {
      $bool = $this->follow_model->customer_save($needarr);
      if (is_int($bool) && $bool > 0) {
        //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
        //获得基本设置房源跟进的天数
        //获取当前经济人所在公司的基本设置信息
        $this->load->model('house_customer_sub_model');
        $customer_follow_day = intval($company_basic_data['customer_follow_spacing_time']);

        $select_arr = array('id', 'house_id', 'date');
        $this->follow_model->set_select_fields($select_arr);
        $where_cond = 'customer_id = "' . $customer_id . '" and follow_type != 1 and type = 4';
        $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
        if (count($last_follow_data) == 2) {
          $time1 = $last_follow_data[0]['date'];
          $time2 = $last_follow_data[1]['date'];
          $date1 = date('Y-m-d', strtotime($time1));
          $date2 = date('Y-m-d', strtotime($time2));
          $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
          if ($differ_day < $customer_follow_day) {
            $this->house_customer_sub_model->add_rent_customer_sub($customer_id, 0);
          } else {
            $this->house_customer_sub_model->add_rent_customer_sub($customer_id, 1);
          }
        } else {
          $this->house_customer_sub_model->add_rent_customer_sub($customer_id, 0);
        }
      }
    }

    if ($result >= 0) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $broker_info['company_id'];
      $add_log_param['agency_id'] = $broker_info['agency_id'];
      $add_log_param['broker_id'] = $broker_id;
      $add_log_param['broker_name'] = $broker_info['truename'];
      $add_log_param['type'] = 11;
      $add_log_param['text'] = '求租客源 ' . 'QZ' . $customer_id . ' ' . $customer_info['dist_id1'] . '-' . $customer_info['street_id1'] . ' ' . $customer_info['area_min'] . '-' . $customer_info['area_max'] . '平方米 ' . $customer_info['price_min'] . '-' . $customer_info['price_max'] . '万元';
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

      if ($text) {
        //求租客源修改成功记录工作统计日志
        $this->info_count($customer_id, 2);
      }
      if ($customer_info['is_share'] == 2) {
        $this->result(1, '您发布的合作店长审核中，请耐心等待');
      } else {
        $this->result(1, '客源信息更新成功！' . $credit_msg);
      }
    } else {
      $this->result(0, '客源信息更新失败');
    }
    return;
  }


  /* 根据删除客源信息
     * @param   string $actiontype 操作类型
     * @param   string $rowid_str 操作房源编号
     * @param   int $page 操作页数
     * @param   string $referer 操作后跳转的页面
    */
  public function del_customerinfo_by_ids()
  {
    //获取当前登录人信息
    $broker_info = $this->user_arr;
    $del_num = 0;

    $devicetype = $this->input->post('api_key', TRUE);
    $deviceid = $this->input->post('deviceid', TRUE);
    //房源编号
    $customer_ids_str = $this->input->post('customer_ids', TRUE);

    $customer_info = $this->rent_customer_model->get_customer('id in (' . $customer_ids_str . ')');

    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $owner_arr = array(
      'broker_id' => $customer_info['broker_id'],
      'agency_id' => $customer_info['agency_id'],
      'company_id' => $customer_info['company_id']
    );
    $customer_modify_per = $this->broker_permission_model->check('18', $owner_arr);
    //修改客源关联门店权限
    $agency_customer_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '13');
    if (!$customer_modify_per['auth']) {
      $this->result('-1', '暂无权限');
      exit;
    } else {
      if (!$agency_customer_modify_per) {
        $this->result('-1', '暂无权限');
        exit;
      }
    }

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    $role_level = intval($broker_info['role_level']);
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($owner_arr['broker_id'] != $broker_info['broker_id'] && $customer_info['public_type'] == '1') {
        $this->result('-1', '店长以下的经纪人不允许操作他人的私客');
        exit();
      }
    }

    $arr_id = explode(',', $customer_ids_str);
    if (is_full_array($arr_id)) {
      $del_num = $this->_del_customer($arr_id);

      if ($del_num > 0) {
        //操作日志
        $add_log_param = array();
        $customer_info = $this->rent_customer_model->get_customer('id in (' . $customer_ids_str . ')');

        $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['agency_id'];
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $this->user_arr['truename'];
        $add_log_param['type'] = 12;
        $add_log_param['text'] = '求租客源 ' . 'QZ' . $customer_ids_str;
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

        $this->result(1, '求租客源删除成功');
        return;
      } else {
        $this->result(2, '求租客源删除失败');
        return;
      }
    } else {
      $this->result(0, '参数非法');
      return;
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
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    if ('0' === $data['open_cooperate']) {
      $this->result('-1', '当前公司尚未开启合作中心');
      exit();
    }

    $up_num = 0;
    $flag = 1;
    //判断是否开启了合作审核
    if (isset($company_basic_data['check_cooperate']) && '1' == $company_basic_data['check_cooperate']) {
      $flag = 2;
    }

    $customer_ids_str = $this->input->post('customer_ids', TRUE);

    //房源编号字符串转化为数组
    $arr_id = explode(',', $customer_ids_str);
    if (is_full_array($arr_id)) {
      $up_num = $this->_change_share($arr_id, $flag);
      if ($up_num > 0) {
        if (1 == $flag) {
          //设置合作后，并添加积分
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
          $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $customer_ids_str), 4);
          $credit_msg = '';
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $credit_msg = '+' . $credit_result['score'] . '积分';
          }
          //设置合作后，并添加成长值
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
          $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $customer_ids_str), 4);
          if (is_full_array($level_result) && $level_result['status'] == 1) {
            $credit_msg .= ',+' . $level_result['score'] . '成长值';
          }
          $this->result(1, '合作成功！' . $credit_msg);
        } else if (2 == $flag) {
          $this->result(1, '当前公司开启合作审核，请等待审核...');
        }
        return;
      } elseif ($up_num == 0) {
        if (2 == $flag) {
          $this->result(3, '该客源已经发送审核');
        } else {
          $this->result(3, '已经合作');
        }
        return;
      } else {
        $this->result(2, '合作失败');
        return;
      }
    } else {
      $this->result(0, '参数非法');
      return;
    }
  }


  /**
   * 设置取消客源为合作
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function cancle_customer_share()
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    if ('0' === $data['open_cooperate']) {
      $this->result('-1', '当前公司尚未开启合作中心');
      exit();
    }

    $up_num = 0;

    $customer_ids_str = $this->input->post('customer_ids', TRUE);
    $arr_id = explode(',', $customer_ids_str);
    if (is_full_array($arr_id)) {
      $up_num = $this->_change_share($arr_id, 0);
      if ($up_num > 0) {
        $this->result(1, '取消合作成功');
        return;
      } elseif ($up_num == 0) {
        $this->result(3, '已经取消合作');
        return;
      } else {
        $this->result(2, '取消合作失败');
        return;
      }
    } else {
      $this->result(0, '参数非法');
      return;
    }
  }


  /**
   * 设置合作状态
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
      $update_arr['is_share'] = intval($is_share);
      if (1 == $update_arr['is_share']) {
        $update_arr['set_share_time'] = time();
      } else {
        $update_arr['set_share_time'] = 0;
      }
      $update_arr['updatetime'] = time();
      $this->load->model('rent_customer_model');

      $up_num = $this->rent_customer_model->update_info_by_id($customer_id, $update_arr);

      if ($up_num > 0) {
        //添加跟进
        $this->load->model('follow_model');
        $text = $is_share ? '是否合作:否>>是' : '是否合作:是>>否';
        $broker_info = $this->user_arr;  //当前经纪人编号

        $needarr = array();
        $needarr['broker_id'] = $broker_info['broker_id'];
        $needarr['type'] = 4;
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
      $update_arr['status'] = $status_arr = $this->rent_customer_model->get_status_arr();
      $up_status = isset($status_arr['delete']) ? intval($status_arr['delete']) : 0;

      if ($up_status > 0) {
        $update_arr['status'] = $up_status;
        $del_num = $this->rent_customer_model->update_info_by_id($customer_id, $update_arr);
      }
    }

    return $del_num;
  }


  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';

    //物业类型条件
    if (isset($form_param['property_type']) && $form_param['property_type'] > 0) {
      $property_type = intval($form_param['property_type']);
      $cond_where .= " AND property_type = '" . $property_type . "'";
    }
    //客户电话模糊搜索
    if (!empty($form_param['customer_search'])) {
      $cond_where .= " AND (telno1 like '%" . $form_param['customer_search'] . "%' or telno2 like '%" . $form_param['customer_search'] . "%' or telno3 like '%" . $form_param['customer_search'] . "%')";
    }
    //状态条件
    if (isset($form_param['status']) && !empty($form_param['status']) && $form_param['status'] > 0) {
      $status = intval($form_param['status']);
      $cond_where .= " AND status = '" . $status . "'";
    } else if ($form_param['status'] == 'test') {
      $cond_where .= " AND status IN (1,2,3,4)";
    }

    //性质条件：公客 私客
    if (isset($form_param['public_type']) && $form_param['public_type'] > 0) {
      $public_type = intval($form_param['public_type']);
      $cond_where .= " AND public_type = '" . $public_type . "'";
    }

    //是否合作
    if (isset($form_param['is_share']) && $form_param['is_share'] >= 0) {
      $is_share = intval($form_param['is_share']);
      $cond_where .= " AND is_share = '" . $is_share . "'";
    }

    //区属、板块条件
    if (isset($form_param['dist_id']) && $form_param['dist_id'] > 0) {
      $dist_id = intval($form_param['dist_id']);
      $cond_where .= " AND (dist_id1 = '" . $dist_id . "' "
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
    if (isset($form_param["cmt_id"]) && $form_param['cmt_id'] > 0) {
      $cmt_id = intval($form_param["cmt_id"]);
      $cond_where .= " AND (cmt_id1 = '" . $cmt_id . "' "
        . " OR cmt_id2 = '" . $cmt_id . "'"
        . " OR cmt_id3 = '" . $cmt_id . "')";
    }

    //价格条件
    if ((isset($form_param["price_key"]) && $form_param["price_key"] > 0)) {
      $conf_customer = $this->rent_customer_model->get_base_conf();
      $price = $conf_customer['rent_price'][$form_param["price_key"]];
      if ($price) {
        $price = preg_replace("#[^0-9-]#", '', $price);
        $price = explode('-', $price);
        if (count($price) == 2) {
          $cond_where .= " and price_min>='$price[0]' and price_max<='$price[1]'";
        } else {
          if ($form_param["price_key"] == 1) {
            $cond_where .= " and price_max < '$price[0]' ";
          } else {
            $cond_where .= " and price_min > '$price[0]' ";
          }
        }
        unset($price);
      }
    }

    //面积条件
    if ((isset($form_param["area_key"]) && $form_param["area_key"] > 0)) {
      $conf_customer = $this->rent_customer_model->get_base_conf();
      $area = $conf_customer['rent_area'][$form_param["area_key"]];
      if ($area) {
        $area = preg_replace("#[^0-9-]#", '', $area);
        $area = explode('-', $area);
        if (count($area) == 2) {
          $cond_where .= " and area_min>='$area[0]' and area_max<='$area[1]'";
        } else {
          if ($form_param["area_key"] == 1) {
            $cond_where .= " and area_max < '$area[0]' ";
          } else {
            $cond_where .= " and area_min > '$area[0]' ";
          }
        }
        unset($area);
      }
    }

    //户型条件
    if ((isset($form_param["room"]) && $form_param["room"] > 0)) {
      $room = floatval($form_param["room"]);
      $cond_where .= " AND  room_min <= '" . $room . "' AND "
        . "room_max >= '" . $room . "' ";
    }

    //客户编号
    if (isset($form_param['id']) && $form_param['id'] > 0) {
      $id = intval($form_param['id']);
      $cond_where .= " AND id = '" . $id . "'";
    }

    //范围(门店)
    if (isset($form_param['agency_id']) && $form_param['agency_id'] > 0) {
      $agency_id = intval($form_param['agency_id']);
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //范围（经纪人）
    if (isset($form_param['broker_id']) && $form_param['broker_id'] > 0) {
      $broker_id = intval($form_param['broker_id']);
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }

    //客户名称
    if (isset($form_param['truename']) && trim($form_param['truename']) != '') {
      $truename = trim($form_param['truename']);
      $cond_where .= " AND truename like '%{$truename}%'";
    }

    //客户电话
    if (isset($form_param['telno1']) && trim($form_param['telno1']) != '') {
      $telno1 = trim($form_param['telno1']);
      $cond_where .= " AND telno1 like '%{$telno1}%'";
    }

    //楼盘名
    if (isset($form_param['block_name']) && trim($form_param['block_name']) != '') {
      $block_name = trim($form_param['block_name']);
      $cond_where .= " AND (cmt_name1 like '%{$block_name}%'" . " or cmt_name2 like '%{$block_name}%'" . " or cmt_name3 like '%{$block_name}%')";
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
        default:
      }
    }
    return $cond_where;
  }

  /**
   * 根据范围提交参数，获取查询条件
   */
  private function get_house_range($form_param)
  {
    $this->load->model('api_broker_model');
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);  //当前经纪人编号
    $agency_id = intval($broker_info['agency_id']);  //经纪人门店编号
    $company_id = intval($broker_info['company_id']);    //公司编号

    $cond_where = '';
    if (isset($form_param) && !empty($form_param)) {
      switch ($form_param) {
        case '1':
          $cond_where = "broker_id = " . $broker_id . " AND status = 1 AND status != 5";//本人
          break;
        case '2':
          //公司下所有门店帐号
          $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
          $in_str = '';
          if (is_array($agencys) && !empty($agencys)) {

            foreach ($agencys as $key => $value) {
              $agency_id = intval($value['agency_id']);
              $in_str .= $in_str != '' ? ',' . $agency_id : $agency_id;
            }
          } else {
            $in_str = 0;
          }

          //公司公盘
          $cond_where = "agency_id IN (" . $in_str . ")  AND nature = 2 AND status = 1 AND status != 5";
          break;
        case '3':
          $cond_where = "agency_id = " . $agency_id . " AND nature = 2 AND status = 1 AND status != 5";//所在门店
          break;
        case '4':
          $cond_where = 'isshare = 1 AND status = 1 AND status != 5';   //合作楼盘
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

      if ($price_max >= $price_min) {
        $cond_where .= " AND price >= '" . $price_min . "' AND "
          . "price <= '" . $price_max . "'";
      } else if ($price_max == 0 && $price_min > 0) {
        $cond_where .= " AND price >= '" . $price_min . "'";
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
      $cond_where .= " AND  room >= '" . $room_min . "' AND "
        . "room <= '" . $room_max . "' ";
    }

    //楼盘名称
    if (isset($form_param["cmt_name"]) && !empty($form_param["cmt_name"])) {
      $cmt_name = trim($form_param["cmt_name"]);
      $cond_where .= " AND  block_name like '%" . $cmt_name . "%'";
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

  //查看客源跟进信息
  public function follow()
  {
    $data = array();
    $broker_info = $this->user_arr;
    //经纪人id
    $broker_id = $broker_info['broker_id'];
    $customer_id = $this->input->get('customer_id', TRUE);
    $customer_id = intval($customer_id);
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);

    //新权限 求租客源查看跟进权限
    //获得当前数据所属的经纪人id和门店id
    $this->rent_customer_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->rent_customer_model->set_id($customer_id);
    $owner_arr = $this->rent_customer_model->get_info_by_id();
    $view_follow_per = $this->broker_permission_model->check('20', $owner_arr);
    //求租客源跟进关联门店权限
    $agency_customer_follow_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '15');
    if (!$view_follow_per['auth']) {
      $this->result('-1', '暂无权限');
      exit();
    } else {
      if (!$agency_customer_follow_per) {
        $this->result('-1', '暂无权限');
        exit();
      }
    }

    //$where_arr="broker_id = '".$broker_id."'";
    $where_arr = "customer_id = '" . $customer_id . "'";
    $where_arr .= " AND (follow_type = 2 OR follow_type = 3)";
    $where_arr .= "  AND type = 4 ";
    $follow_tbl = 'detailed_follow';
    $follow_config = $this->follow_model->get_config();
    $this->follow_model->set_tbl($follow_tbl);

    // 分页参数
    $page = $this->input->get('page', TRUE);
    $pagesize = $this->input->get('pagesize', TRUE);
    if (!$page) {
      $page = 1;
    }
    if (!$pagesize) {
      $pagesize = 5;
    }
    $this->_init_pagination($page, $pagesize);


    $follow_lists = $this->follow_model->get_lists($where_arr, $this->_offset, $this->_limit);
    $this->load->model('api_broker_model');
    $tbl = 'rent_customer';
    $this->buy_customer_model->set_tbl($tbl);
    $where = "broker_id = '" . $broker_id . "'";
    $lists = $this->buy_customer_model->get_customer($where);

    //获取客源
    foreach ($lists as $key => $val) {
      $acustomer_list[$val['id']] = $val;
    }
    $follow_arr = array();
    if ($follow_lists) {
      foreach ($follow_lists as $key => $val) {
        $follow_arr[$key]['follow_way'] = $follow_config['follow_way'][$val['follow_way']];
        $follow_arr[$key]['follow_time'] = strtotime($val['date']);
        $follow_arr[$key]['follow_value'] = $val['text'];
        $broker_messagin = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $follow_arr[$key]['follow_broker_name'] = $broker_messagin['truename'];
        if ($val['customer_id']) {
          $follow_arr[$key]['follow_customer_name'] = $acustomer_list[$val['customer_id']]['truename'];
        }
        if ($val['follow_way'] == 4 || $val['follow_way'] == 5) {
          $this->load->model('rent_house_model');
          $this->rent_house_model->set_search_fields(array('block_name'));
          $this->rent_house_model->set_id($val['house_id']);
          $house_detail = $this->rent_house_model->get_info_by_id();
          if (is_full_array($house_detail)) {
            $follow_arr[$key]['follow_block_name'] = $house_detail['block_name'];
          } else {
            $follow_arr[$key]['follow_block_name'] = '';
          }
        }
      }
    }

    //操作日志
    $add_log_param = array();
    $this->rent_customer_model->set_search_fields(array('dist_id1', 'street_id1', 'area_min', 'area_max', 'price_min', 'price_max'));
    $this->rent_customer_model->set_id($customer_id);
    $customer_info = $this->rent_customer_model->get_info_by_id();

    $add_log_param['company_id'] = $this->user_arr['company_id'];
    $add_log_param['agency_id'] = $this->user_arr['agency_id'];
    $add_log_param['broker_id'] = $this->user_arr['broker_id'];
    $add_log_param['broker_name'] = $this->user_arr['truename'];
    $add_log_param['type'] = 13;
    $add_log_param['text'] = '求租客源 ' . 'QZ' . $customer_id;
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
    $data['follow_lists'] = $follow_arr;
    $this->result('1', '查看客源的跟进信息', $data);


  }

  //添加跟进记录
  public function addfollow()
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (isset($company_basic_data['follow_text_num']) && $company_basic_data['follow_text_num'] > 0) {
      $follow_text_num = intval($company_basic_data['follow_text_num']);
    } else {
      $follow_text_num = 0;
    }

    $broker_info = $this->user_arr;
    $follow_arr = array();
    date_default_timezone_set('Asia/Shanghai');
    $follow_arr['customer_id'] = $this->input->get('customer_id', TRUE);//客源id
    $follow_arr['broker_id'] = $broker_info['broker_id'];//经纪人的ID
    $follow_arr['agency_id'] = $broker_info['agency_id'];//门店ID
    $follow_arr['company_id'] = $broker_info['company_id'];//总公司id
    $follow_arr['follow_way'] = $this->input->get('follow_type', TRUE);//跟进方式
    $follow_arr['house_id'] = $this->input->get('house_id', TRUE);//房源id
    $follow_arr['type'] = 4;//客户类型
    $follow_arr['follow_type'] = 2;//跟进类型
    $follow_arr['text'] = $this->input->get('text', TRUE);//跟进内容
    $follow_arr['date'] = date('Y-m-d H:i:s');//跟进时间
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);

    if ($follow_text_num > 0 && (mb_strlen($follow_arr['text']) < $follow_text_num)) {
      $this->result('2', '跟进内容不得少于' . $follow_text_num . '字');
    } else {
      $tbl = 'detailed_follow';
      $this->follow_model->set_tbl($tbl);
      $follow_id = '';
      if ($follow_arr['follow_way'] && $follow_arr['customer_id'] && $follow_arr['text']) {
        $follow_id = $this->follow_model->add_follow($follow_arr);
        $customer_follow_date = array();
        $customer_follow_date['updatetime'] = time();
        $cond_where = "id = '" . $follow_arr['customer_id'] . "'";
        $result = $this->rent_customer_model->update_customerinfo_by_cond($customer_follow_date, $cond_where);
      }

      $data = array();
      $data['follow_id'] = $follow_id;
      if ($follow_id > 0) {
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
        $add_log_param['text'] = '求租客源 ' . 'QZ' . $follow_arr['customer_id'] . ' ' . $follow_way_str . ' ' . $follow_arr['text'];
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

        //求租客源带看记录工作统计日志
        if ($follow_arr['follow_way'] == 5) {
          $this->info_count($follow_arr['customer_id'], 5, $follow_arr['house_id']);
        } else {
          $this->info_count($follow_arr['customer_id'], 9);
        }
        $this->result('1', '添加跟进信息成功', $data);
      } else {
        $this->result('0', '添加跟进信息失败', $data);
      }
    }
  }

  /**
   * 设为私客
   * @access private
   * @return void
   */
  public function set_private()
  {
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_public_type($str, $flag);
  }

  /**
   * 设为公客
   * @access private
   * @return void
   */
  public function set_public()
  {
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_public_type($str, $flag);
  }

  /**
   * 设为公客、私客
   * @access private
   * @return void
   */
  public function _change_public_type($str, $flag)
  {
    if ($str && $flag <= 2 && $flag >= 1) {
      $cond_where = "id in (0," . $str . ") and public_type <> {$flag}";

      $ids_arr = array();

      $this->rent_customer_model->set_search_fields(array("id"));
      $list = $this->rent_customer_model->get_list_by_cond($cond_where);
      $text = $flag > 1 ? "设置公私客:私客>>公客" : "设置公私客:公客>>私客";
      foreach ($list as $key => $val) {
        $needarr = array();
        $needarr['broker_id'] = $this->user_arr['broker_id'];
        $needarr['customer_id'] = $val['id'];
        $needarr['type'] = 4;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_save($needarr);
        $ids_arr[] = $val['id'];
      }
      $arr = array('public_type' => $flag);
      if (is_full_array($ids_arr)) {
        $up_num = $this->rent_customer_model->update_info_by_id($ids_arr, $arr);
      } else {
        $up_num = 0;
      }
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    if ($up_num > 0) {
      $reslult = array('result' => 'ok', "arr" => $ids_arr, "msg" => "设置成功，共设置{$up_num}条数据");
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    echo json_encode($reslult);
  }

  /**
   * 设为锁定
   * @access private
   * @return void
   */
  public function set_lock()
  {
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_lock($str, $flag);
  }

  /**
   * 设为解锁
   * @access private
   * @return void
   */
  public function set_unlock()
  {
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_lock($str, $flag);
  }

  /**
   * 锁定、解锁
   * @access private
   * @return void
   */
  public function _change_lock($str, $flag)
  {
    if ($str && $flag <= 1 && $flag >= 0) {
      $broker_id = $this->user_arr['broker_id'];
      if ($flag == 0) {
        //解锁
        $arr = array('lock' => $flag);
        $cond_where = "id in (0," . $str . ") and `lock` = {$broker_id}";
      } else if ($flag == 1) {
        //锁定
        $arr = array('lock' => $broker_id);
        $cond_where = "id in (0," . $str . ") and `lock` = 0";
      }

      $ids_arr = array();

      $this->rent_customer_model->set_search_fields(array("id"));
      $list = $this->rent_customer_model->get_list_by_cond($cond_where);
      $text = $flag ? "是否锁定:否>>是" : "是否锁定:是>>否";
      foreach ($list as $key => $val) {
        $needarr = array();
        $needarr['broker_id'] = $broker_id;
        $needarr['customer_id'] = $val['id'];
        $needarr['type'] = 4;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_save($needarr);
        $ids_arr[] = $val['id'];
      }
      if (is_full_array($ids_arr)) {
        $up_num = $this->rent_customer_model->update_info_by_id($ids_arr, $arr);
      } else {
        $up_num = 0;
      }
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    if ($up_num > 0) {
      $reslult = array('result' => 'ok', "arr" => $ids_arr, "msg" => "设置成功，共设置{$up_num}条数据");
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    echo json_encode($reslult);
  }


  //导入报表
  public function import()
  {
    if (!empty($_POST['sub'])) {
      $config['upload_path'] = './temp/';
      $config['file_name'] = date('YmdHis', time()) . rand(0000, 9999);
      $config['allowed_types'] = 'xlsx|xls';
      $config['max_size'] = "2000";
      $this->load->library('upload', $config);
      //打印成功或错误的信息
      if ($this->upload->do_upload('upfile')) {
        $data = array("upload_data" => $this->upload->data());
        //上传的文件名称
        $broker_info = $this->user_arr;
        $this->load->model('read_model');
        $result = $this->read_model->read('rent_customer_model', $broker_info, $data['upload_data'], 8);
        unlink($data['upload_data']['full_path']); //删除文件
      } else {
        $result = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"></head><body style="background:#F2F2F2;"><p class="up_m_b_date_up" style="text-align: center;"><span class="up_e">上传失败</span>，请选择文件上传</p></body></html>';

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

    $this->load->model('sell_model');

    $public_type = array();
    foreach ($data['config']['public_type'] as $key => $k) { //性质类型
      $public_type[$k] = $key;
    }
    $share = array();
    foreach ($data['config']['is_share'] as $key => $k) { //是否合作
      $share[$k] = $key;
    }
    $id = $this->input->post('id', true);
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $data['where']['id'] = $id;
    $data['where']['broker_id'] = $broker_id;

    $result = $this->sell_model->get_tmp($data['where'], '', '', '');
    $content = unserialize($result[0]['content']);
    $res = array();
    $i = 0;
    foreach ($content as $key => $k) {
      $res['broker_id'] = $broker_id;
      $res['broker_name'] = trim($broker_info['truename']);
      $res['agency_id'] = trim($broker_info['agency_id']); //门店ID
      $res['truename'] = $k[0];  //客户姓名
      foreach (explode("/", $k[1]) as $vo => $v) {
        $res['telno' . ($vo + 1)] = $v;
      }
      $res['status'] = 1;
      $res['public_type'] = $public_type[$k[2]];
      $res['is_share'] = $share[$k[3]];
      $res['room_min'] = $k[4];
      $res['room_max'] = $k[5];
      $res['area_min'] = $k[6];
      $res['area_max'] = $k[7];
      $res['price_min'] = $k[8];
      $res['price_max'] = $k[9];
      foreach (explode("/", $k[10]) as $key => $k) {
        $n = explode("-", $k);
        $distwhere['is_show'] = 1;
        $distwhere['district'] = $n[0];
        $dis_info = $this->rent_customer_model->dist_info($distwhere);
        if (strpos($k, '-') === false) {
          $res['dist_id' . ($key + 1)] = $dis_info[0]['id'];
        } else {
          $streetwhere['dist_id'] = $res['dist_id' . ($key + 1)] = $dis_info[0]['id'];
          $streetwhere['is_show'] = 1;
          $streetwhere['streetname'] = $n[1];
          $street_info = $this->rent_customer_model->street_info($streetwhere);
          $res['street_id' . ($key + 1)] = $street_info[0]['id'];
        }
      }
      $res['creattime'] = time();
      $res['updatetime'] = time();
      $res['ip'] = get_ip();
      if (($this->rent_customer_model->add_data($res, 'db_city', 'rent_customer')) > 0) {
        $i++;
      }

      unset($res);
    }
    if ($i > 0) {
      $res = array('broker_id' => $broker_id);
      $this->sell_model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '客源导入成功！成功录入客源' . $i . '条。';
    } else {
      $result['status'] = 'error';
      $result['error'] = '客源导入失败！再试一次吧！可能失败的原因：1.网络连接超时；2.执行失败。';
    }
    echo json_encode($result);
  }

  /**
   * 导出求租客源报表
   * @author   kang
   */
  public function exportReport($page = 1)
  {
    //模板使用数据
    $data = array();

    //post参数
    $posts = $this->input->post(NULL, FALSE);
    //print_r($posts);exit;
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

    //获取求租信息基本配置资料
    $conf_customer = $this->rent_customer_model->get_base_conf();
    $data['conf_customer'] = $conf_customer;
    //print_r($conf_customer);

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
    $cond_where = 'broker_id = ' . $broker_id . ' AND  status != 5';

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //排序字段
    $customer_order = isset($post_param['customer_order']) ? $post_param['customer_order'] : array();
    $order_arr = $this->_get_orderby_arr($customer_order);

    //根据ch（导出方式）查询相应的数据
    $ch = $posts['ch'];
    $customer_list = '';

    if ($ch == 1) {//仅导出所选客源

      $ch_1_data = $posts['ch_1_data'];  //获取所选客户ID数组
      $customer_ids = explode(',', $ch_1_data);
      foreach ($customer_ids as $customer_id) {
        //查询条件
        $ch_cond_where = 'broker_id = ' . $broker_id . ' AND  status != 5';
        //表单提交参数组成的查询条件(仅针对ch=1)
        $dt['id'] = $customer_id;
        $cond_where_ch = $this->_get_cond_str($dt);
        //print_r($cond_where_ch);exit;
        $ch_cond_where .= $cond_where_ch;
        //获取列表内容
        $rs = $this->rent_customer_model->get_rentlist_by_cond($ch_cond_where);
        $customer_list[] = $rs[0];
      }

    } else if ($ch == 2) {//导出当前页所有客源

      $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
      $offset = ($page - 1) * $this->_limit;
      //获取列表内容
      $customer_list = $this->rent_customer_model->get_rentlist_by_cond($cond_where, $offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    } else if ($ch == 3) {//导出多页客源

      $start_page = $posts['start_page'];
      $end_page = $posts['end_page'];
      $offset = ($start_page - 1) * $this->_limit;
      $limit = (($end_page - $start_page) + 1) * $this->_limit;
      $customer_list = $this->rent_customer_model->get_rentlist_by_cond($cond_where, $offset,
        $limit, $order_arr['order_key'], $order_arr['order_by']);

    } else {

    }

    //判断是否有数据
    if (count($customer_list) == 0) {
      echo "<script>alert('没有数据，无法导出！')</script>";
      echo "<script>location.href = MLS_URL.'/rent_customer';</script>";
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
      // print_r($district_street);exit;
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
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, '租');
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $conf_customer['status'][$customer_list[$i - 2]['status']]);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $conf_customer['public_type'][$customer_list[$i - 2]['public_type']]);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $conf_customer['is_share'][$customer_list[$i - 2]['is_share']]);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, format_info_id($customer_list[$i - 2]['id'], 'rent_customer'));
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $customer_list[$i - 2]['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $conf_customer['property_type'][$customer_list[$i - 2]['property_type']]);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $district_street);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $cmt);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $customer_list[$i - 2]['room_min'] . "-" . $customer_list[$i - 2]['room_max']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $customer_list[$i - 2]['area_min'] . "-" . $customer_list[$i - 2]['area_max']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $customer_list[$i - 2]['price_min'] . "-" . $customer_list[$i - 2]['price_max']);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $customer_broker_info[$customer_list[$i - 2]['broker_id']]['agency_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $customer_broker_info[$customer_list[$i - 2]['broker_id']]['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $customer_broker_info[$customer_list[$i - 2]['broker_id']]['phone']);
    }


    $objPHPExcel->getActiveSheet()->setTitle('rent_customer_report');
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');
    header('Content-Disposition: attachment;filename="求租客源.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
    // print_r($data);exit;
    $objWriter->save('php://output');
    exit;
  }

  //分配房源
  public function allocate_house($house_id)
  {
    $data = array();
    $house_id = str_replace('_', ',', $house_id);

    $conf_customer = $this->rent_customer_model->get_base_conf();
    $data['conf_customer'] = $conf_customer;
    //加载区属模型类
    $this->load->model('district_model');
    //加载楼盘模型类
    $this->load->model('community_model');
    //获取楼盘配置信息
    $data['community'] = $this->community_model->get_cmt();
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
    if ($house_id) {
      $cond_where = "id IN ($house_id)";
      $house_list = $this->rent_customer_model->get_customer($cond_where);
    }
    $data['house_id'] = $house_id;
    $data['house_list'] = $house_list;
    //根据总公司编号获取分店信息
    $broker_info = $this->user_arr;
    $agency_id = intval($broker_info['agency_id']);//经纪人门店编号
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $data['broker_name'] = $broker_info['truename'];
    $this->load->model('agency_model');
    $where = array('id' => $agency_id);
    $agency_name = $this->agency_model->get_one_by($where);

    //权限条件($area  1 本人  2 门店  3公司)
    //$area=$this->user_func_permission['area'];
    $area = 3;
    $data['agency_name'] = $agency_name['name'];

    //获取全部分公司信息
    $this->load->model('api_broker_model');
    $data['agency_list'] = $this->api_broker_model->get_agencys_by_company_id($company_id);

    $data['page_title'] = '分配房源';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');
    $data['type'] = 'rent_customer';
    //加载任务页面模板
    $this->view('customer/allocate_house', $data);
  }

  //分配任务
  public function share_tasks($customer_id = 1, $num = 4)
  {
    $data['num'] = intval($num);
    //区属板块信息
    $this->load->model('district_model');
    //获取区属
    $all_district = $this->district_model->get_district();
    //获取板块
    $all_street = $this->district_model->get_street();

    if (!empty($customer_id)) {
      $config_data = $this->buy_customer_model->get_base_conf();
      $data['config'] = $config_data;
      $customer_ids = str_replace('%7C', ',', $customer_id);
      $customer_list = $this->rent_customer_model->get_all_customer_by_ids($customer_ids);
    }
    $data['customer_ids'] = $customer_ids;
    //数据重构
    $customer_list2 = array();
    foreach ($customer_list as $k => $v) {
      //楼盘名称
      $v['cmt_name'] = $v['cmt_name1'];
      if (!empty($v['cmt_name2'])) {
        $v['cmt_name'] .= ',' . $v['cmt_name2'];
      }
      if (!empty($v['cmt_name3'])) {
        $v['cmt_name'] .= ',' . $v['cmt_name4'];
      }
      //户型
      $v['room'] = $v['room_min'] . '-' . $v['room_max'];
      //面积
      $v['area'] = $v['area_min'] . '-' . $v['area_max'];
      //总价
      $v['price'] = $v['price_min'] . '-' . $v['price_max'];
      //区属
      $v['dist_name'] = $all_district[$v['dist_id1']]['district'];
      if (!empty($v['dist_id2'])) {
        $v['dist_name'] .= ',' . $all_district[$v['dist_id2']]['district'];
      }
      if (!empty($v['dist_id3'])) {
        $v['dist_name'] .= ',' . $all_district[$v['dist_id3']]['district'];
      }
      //板块
      $v['street_name'] = $all_street[$v['street_id1']]['streetname'];
      if (!empty($v['street_id2'])) {
        $v['street_name'] .= ',' . $all_street[$v['street_id2']]['streetname'];
      }
      if (!empty($v['street_id3'])) {
        $v['street_name'] .= ',' . $all_street[$v['street_id3']]['streetname'];
      }
      $customer_list2[] = $v;
    }
    $data['customer_list'] = $customer_list2;
    //当前登录经纪人信息
    $this_broker = $this->user_arr;
    $data['broker_data'] = $this_broker;
    //根据当前用户的权限显示任务执行人的范围
    $share_return = $this->get_func_permission('customer', 'share_tasks');
    $data['share_area'] = $share_return['area'];
    //获取所有分店的信息
    $this->load->model('agency_model');
    $this->load->model('api_broker_model');
    $company_id = $this_broker['company_id'];
    $data['agency_list'] = $this->api_broker_model->get_agencys_by_company_id($company_id);
    //获取同门店的所有经纪人信息
    $agency_id = $this_broker['agency_id'];
    $data['brokers'] = $this->api_broker_model->get_brokers_agency_id($agency_id);
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');

    //加载任务页面模板
    $this->view('customer/sell_tasks', $data);

  }

  //添加分配房源
  public function add_allocate_house()
  {
    $house_id = $this->input->get('house_id', TRUE);//房源id
    //分配给谁
    $run_broker_id = $this->input->get('run_broker_id', TRUE);

    $cond_where = "id IN(" . $house_id . ")";
    $return_id = $this->rent_customer_model->update_info_by_cond(array('broker_id' => $run_broker_id), $cond_where);
    if ($return_id) {
      echo '1';
    } else {
      echo '2';
    }
  }

  //客源的修改跟进匹配信息
  public function customer_follow_match($new, $old)
  {
    $this->load->model('district_model');
    $this->load->model('rent_customer_model');
    //获取求购信息基本配置资料
    $conf_customer = $this->rent_customer_model->get_base_conf();
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
          case'idno':
            $constr .= '客户身份证:' . $val . '>>' . $new[$key] . ',';
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
            $constr .= '客户地址' . $val . '>>' . $new[$key] . ',';
            break;
          case'job_type':
            $constr .= '客户工作类型' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
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
            $constr .= '板块1:' . $stred[$val]['streetname'] . '>>' . $stred[$new['street_id1']]['streetname'] . ',';
            break;
          case'dist_id2':
            if ($new[$key] && $val) {
              $constr .= '区属2:' . $dis[$val]['district'] . '>>' . $dis[$new['dist_id2']]['district'] . ',';
            } elseif ($new[$key] && !$val) {
              $constr .= '区属2:' . $dis[$new['dist_id2']]['district'] . '>>添加,';
            } elseif (!$new[$key] && $val) {
              $constr .= '区属2:' . $dis[$val]['district'] . '>>删除,';
            }
            break;
          case'street_id2';
            if ($new[$key] && $val) {
              $constr .= '板块2:' . $stred[$val]['streetname'] . '>>' . $stred[$new['street_id2']]['streetname'] . ',';
            } elseif ($new[$key] && !$val) {
              $constr .= '板块2:' . $stred[$new['street_id2']]['streetname'] . '>>添加,';
            } elseif (!$new[$key] && $val) {
              $constr .= '板块2:' . $stred[$val]['streetname'] . '>>删除,';
            }
            break;
          case'dist_id3':
            if ($new[$key] && $val) {
              $constr .= '区属3:' . $dis[$val]['district'] . '>>' . $dis[$new['dist_id3']]['district'] . ',';
            } elseif ($new[$key] && !$val) {
              $constr .= '区属3:' . $dis[$new['dist_id3']]['district'] . '>>添加,';
            } elseif (!$new[$key] && $val) {
              $constr .= '区属3:' . $dis[$val]['district'] . '>>删除,';
            }
            break;
          case'street_id3';
            if ($new[$key] && $val) {
              $constr .= '板块3:' . $stred[$val]['streetname'] . '>>' . $stred[$new['street_id3']]['streetname'] . ',';
            } elseif ($new[$key] && !$val) {
              $constr .= '板块3:' . $stred[$new['street_id3']]['streetname'] . '>>添加,';
            } elseif (!$new[$key] && $val) {
              $constr .= '板块3:' . $stred[$val]['streetname'] . '>>删除,';
            }
            break;
          case'cmt_name1':
            if ($new[$key] && $val) {
              $constr .= '意向楼盘1:' . $val . '>>' . $new[$key] . ',';
            } elseif ($new[$key] && !$val) {
              $constr .= '意向楼盘1:' . $new[$key] . '>>添加,';
            } elseif (!$new[$key] && $val) {
              $constr .= '意向楼盘1:' . $val . '>>删除,';
            }
            break;
          case'cmt_name2':
            if ($new[$key] && $val) {
              $constr .= '意向楼盘2:' . $val . '>>' . $new[$key] . ',';
            } elseif ($new[$key] && !$val) {
              $constr .= '意向楼盘2:' . $new[$key] . '>>添加,';
            } elseif (!$new[$key] && $val) {
              $constr .= '意向楼盘2:' . $val . '>>删除,';
            }
            break;
          case'cmt_name3':
            if ($new[$key] && $val) {
              $constr .= '意向楼盘3:' . $val . '>>' . $new[$key] . ',';
            } elseif ($new[$key] && !$val) {
              $constr .= '意向楼盘3:' . $new[$key] . '>>添加,';
            } elseif (!$new[$key] && $val) {
              $constr .= '意向楼盘3:' . $val . '>>删除,';
            }
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
          case'pay_commission:':
            $constr .= '付佣方式' . $conf_customer[$key][$val] . '>>' . $conf_customer[$key][$new[$key]] . ',';
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
      'type' => 4,
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
}

/* End of file customer.php */
/* Location: ./applications/mls/controllers/customer.php */
