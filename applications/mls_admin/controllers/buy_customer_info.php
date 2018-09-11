<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台求购详情页
 *
 * @package    mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      kang
 * Date: 15-2-9
 * Time: 下午1:12
 */
class Buy_customer_info extends MY_Controller
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


  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->helper('user_helper');
    $this->load->model('buy_customer_model');
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
   * 求购详情页首页列表
   */
  public function index($page = 1)
  {
    //模板使用数据
    $data = array();

    $data['title'] = "求购客源";
    $data['conf_where'] = 'index';

    //post参数
    $post_param = $this->input->post(NULL, TRUE);

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

    //查询条件
    $cond_where = 'a.status IN(1,2,3,4)';
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;
    //默认排序
    $order_arr['order_key'] = 'a.id';
    $order_arr['order_by'] = 'ASC';

    //条件排序
    $customer_order = isset($post_param['customer_order']) ? $post_param['customer_order'] : '';
    $where_order = isset($post_param['where_order']) ? $post_param['where_order'] : '';
    if ($customer_order && $where_order) {
      $order_arr = $this->_get_orderby($customer_order, $where_order);
    }

    //每页显示条件
    $data['pagesize'] = isset($post_param['where_page']) ? $post_param['where_page'] : 10;
    //符合条件的总行数
    $this->_total_count = $this->buy_customer_model->get_total_by_cond($cond_where);
    $data['total_count'] = $this->_total_count;
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $data['pagesize']) : 0;

    //计算记录偏移量
    $data['pages'] = $pages;
    $data['page'] = isset($post_param['pg']) ? intval($post_param['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);

    $customer_list = $this->buy_customer_model->get_buycustomerlist_by_cond($cond_where, $data['offset'],
      $data['pagesize'], $order_arr['order_key'], $order_arr['order_by']);

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

    //表单数据
    $data['post_param'] = $post_param;
    $this->load->view('buy_customer_info/index', $data);
  }

  /**
   * 客源详情页
   * @param $id 列表页ID
   */
  public function modify($id)
  {
    $data['title'] = "修改客源详情";
    $data['conf_where'] = 'index';

    $data['id'] = $id;

    $this->load->model('buy_customer_model');
    //获取求购信息基本配置资料
    $data['conf_customer'] = $this->buy_customer_model->get_base_conf();

    //区属板块信息
    $this->load->model('district_model');
    $data['district_arr'] = $this->district_model->get_district();

    //客源数据
    $customer_info = $this->buy_customer_model->get_customer_info_by_id($id);
    $data['customer_info'] = $customer_info[0];
    //print_r($data['customer_info']);

    //板块数据
    $dist_id1 = intval($customer_info[0]['dist_id1']);
    $street_id1 = intval($customer_info[0]['street_id1']);
    //选择的区属1
    if ($dist_id1 > 0 && $street_id1 > 0) {
      $select_info['street_info'] =
        $this->district_model->get_street_bydist($dist_id1);
      $data['select_info1'] = $select_info;
    }

    //板块数据
    $dist_id2 = intval($customer_info[0]['dist_id2']);
    $street_id2 = intval($customer_info[0]['street_id2']);

    //选择的区属2
    if ($dist_id2 > 0 && $street_id2 > 0) {
      $select_info['street_info'] =
        $this->district_model->get_street_bydist($dist_id2);
      $data['select_info2'] = $select_info;
    }

    //板块数据
    $dist_id3 = intval($customer_info[0]['dist_id3']);
    $street_id3 = intval($customer_info[0]['street_id3']);

    //选择的区属3
    if ($dist_id3 > 0 && $street_id3 > 0) {
      $select_info['street_info'] =
        $this->district_model->get_street_bydist($dist_id3);
      $data['select_info3'] = $select_info;
    }


    $this->load->view('buy_customer_info/modify', $data);
  }

  public function modify_info()
  {
    //添加客户信息
    $customer_info = array();

    //列表页面地址
    $url_manage = MLS_ADMIN_URL . "/buy_customer_info/index";

    $customer_id = intval($this->input->post('customer_id'));
    $customer_broker_id = intval($this->input->post('customer_broker_id'));

    $truename = $this->input->post('truename', TRUE);
    //验证真是姓名是不是符合要求
    $this->load->helper('common_string_helper');
    if (preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $truename)) {
      //验证真是姓名长度是否符合要求
      if (abslength(trim($truename)) > 5) {
        $this->jump($url_manage, '业主姓名最多5个字符', 3000);
      }

      $customer_info['truename'] = $truename;
    } else {
      $this->jump($url_manage, '业主姓名必填，只能包含汉字、字母、数字', 3000);
    }

    $customer_info['sex'] = $this->input->post('sex', TRUE);
    $customer_info['idno'] = $this->input->post('idno', TRUE);
    //print_r($customer_info);exit;
    //如果填写身份证，则验证身份证格式是否正确
    if ($customer_info['idno'] != '' &&
      !preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $customer_info['idno'])
    ) {
      $this->jump($url_manage, '身份证格式不正确', 3000);
    }
    //用户手机号码
    $telno_arr = $this->input->post('telno', TRUE);
    $telno_num = count($telno_arr);
    for ($i = 1; $i <= $telno_num; $i++) {
      $telno = $telno_arr[$i - 1];

      if (trim($telno) == '') {
        if ($i > 1) {
          $customer_info['telno' . $i] = '';
          continue;
        } else {
          $this->jump($url_manage, '手机号码不能为空', 3000);
        }
      }

      if (preg_match('/(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/', $telno)) {
        $customer_info['telno' . $i] = $telno;
      } else {
        $this->jump($url_manage, '手机号码[' . $telno . ']格式不正确', 3000);
      }
    }
    $customer_info['address'] = $this->input->post('address', TRUE);
    $customer_info['job_type'] = $this->input->post('job_type', TRUE);
    $customer_info['user_level'] = $this->input->post('user_level', TRUE);
    $customer_info['age_group'] = $this->input->post('age_group', TRUE);
    $customer_info['status'] = $this->input->post('status', TRUE);

    //状态必填验证
    if (!$customer_info['status']) {
      $this->jump($url_manage, '客源信息状态必须选择', 3000);
    }

    //客源属性验证，如果公司设置无法发布私盘，则无法选择私盘
    $customer_info['public_type'] = intval($this->input->post('public_type', TRUE));
    $customer_info['room_min'] = intval($this->input->post('room_min', TRUE));
    $customer_info['room_max'] = intval($this->input->post('room_max', TRUE));
    //户型验证
    if ($customer_info['room_min'] < 1 || $customer_info['room_max'] < 1) {
      $this->jump($url_manage, '户型最小数值为整数1', 3000);
    }

    if ($customer_info['room_min'] > $customer_info['room_max']) {
      $this->jump($url_manage, '户型数据异常', 3000);
    }
    $customer_info['area_min'] = floatval($this->input->post('area_min', TRUE));
    $customer_info['area_max'] = floatval($this->input->post('area_max', TRUE));
    //面积验证
    if ($customer_info['area_min'] < 1 || $customer_info['area_min'] < 1) {
      $this->jump($url_manage, '面积最小数值为整数1', 3000);
    }

    if ($customer_info['area_min'] > $customer_info['area_max']) {
      $this->jump($url_manage, '面积数据异常', 3000);
    }

    $customer_info['price_min'] = floatval($this->input->post('price_min', TRUE));
    $customer_info['price_max'] = floatval($this->input->post('price_max', TRUE));
    //价格验证
    if ($customer_info['price_min'] < 1 || $customer_info['price_max'] < 1) {
      $this->jump($url_manage, '价格最小数值为整数1', 3000);
    }

    if ($customer_info['price_min'] > $customer_info['price_max']) {
      $this->jump($url_manage, '价格数据异常', 3000);
    }

    //区属板块
    $district_arr = $this->input->post('dist_id', TRUE);
    $street_arr = $this->input->post('street_id', TRUE);
    $dist_num = count($district_arr);
    //区属个数验证
    for ($i = 1; $i <= $dist_num; $i++) {
      $dist_id = intval($district_arr[$i - 1]);
      $street_id = intval($street_arr[$i - 1]);
      $customer_info['dist_id' . $i] = $dist_id > 0 ? $dist_id : 0;
      $customer_info['street_id' . $i] = $street_id > 0 ? $street_id : 0;
    }

    //楼盘信息
    $cmt_arr = $this->input->post('cmt_id', TRUE);
    $cmt_num = count($cmt_arr);
    for ($i = 1; $i <= $cmt_num; $i++) {
      $cmt_id = intval($cmt_arr[$i - 1]);
      $customer_info['cmt_id' . $i] = $cmt_id > 0 ? $cmt_id : 0;
    }

    //楼盘名称信息
    $cmtname_arr = $this->input->post('cmt_name', TRUE);
    $cmtname_num = count($cmtname_arr);
    for ($i = 1; $i <= $cmtname_num; $i++) {
      $cmt_name = trim(strip_tags($cmtname_arr[$i - 1]));
      $customer_info['cmt_name' . $i] = !empty($cmt_name) ? $cmt_name : '';
    }

    $customer_info['forward'] = intval($this->input->post('forward', TRUE));
    $customer_info['fitment'] = intval($this->input->post('fitment', TRUE));
    $customer_info['floor_min'] = intval($this->input->post('floor_min', TRUE));
    $customer_info['floor_max'] = intval($this->input->post('floor_max', TRUE));

    //楼层验证
    if ($customer_info['floor_min'] > $customer_info['floor_max']) {
      $this->jump($url_manage, '楼层数据异常', 3000);
    }

    $customer_info['location'] = intval($this->input->post('location', TRUE));
    $customer_info['house_type'] = intval($this->input->post('house_type', TRUE));
    $customer_info['property_type'] = intval($this->input->post('property_type', TRUE));
    $customer_info['house_age'] = intval($this->input->post('house_age', TRUE));
    $customer_info['payment'] = intval($this->input->post('payment', TRUE));
    $customer_info['pay_commission'] = intval($this->input->post('pay_commission', TRUE));
    $customer_info['intent'] = intval($this->input->post('intent', TRUE));
    $customer_info['infofrom'] = intval($this->input->post('infofrom', TRUE));
    $customer_info['remark'] = $this->input->post('remark', TRUE);
    $customer_info['deadline'] = intval($this->input->post('deadline', TRUE));
    $customer_info['updatetime'] = time();
    $customer_info['is_share'] = intval($this->input->post('is_share', TRUE));

    //加载客户MODEL
    $this->load->model('buy_customer_model');
    $cond_where = "id = '" . $customer_id . "' AND broker_id = '" . $customer_broker_id . "'";
    $result = $this->buy_customer_model->update_customerinfo_by_cond($customer_info, $cond_where);

    if ($result > 0) {
      $page_text = '客源信息更新成功';
    } else {
      $page_text = '客源信息更新失败';
    }

    $this->jump($url_manage, $page_text, 3000);
  }

  /**
   * 查看跟进
   */
  public function follow($id)
  {

    $data['title'] = "客源跟进信息";
    $data['conf_where'] = 'index';
    //type=3 在数据库表示的是客源求购跟进信息
    $this->load->model('buy_customer_model');
    $condition = "a.type = 3 AND b.id = " . $id;
    //print_r($condition);
    //echo "<br>";
    $data['follows'] = $this->buy_customer_model->get_follows_by_cond($condition);
    //print_r($data['follows']);

    $this->load->view("buy_customer_info/follow", $data);
  }

  /**
   * 根据关键字获取门店（公司）信息
   */
  public function get_agencyinfo_by_kw()
  {

    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('buy_customer_model');
    $agencys = $this->buy_customer_model->get_total_agency($keyword);

    foreach ($agencys as $key => $value) {
      $agencys[$key]['label'] = $value['name'];
    }

    if (empty($agencys)) {
      $agencys[0]['id'] = 0;
      $agencys[0]['label'] = '暂无小区';
      $agencys[0]['averprice'] = 0.00;
      $agencys[0]['address'] = '暂无地址';
      $agencys[0]['status'] = -1;
      $agencys[0]['districtname'] = '暂无信息';
      $agencys[0]['streetname'] = '暂无信息';
    }

    echo json_encode($agencys);
  }

  /**
   * 删除客源信息(更改为删除状态，并非物理删除)
   *
   * @access  public
   * @param  mixed $customer_id 客源编号
   * @return  void
   */
  public function del_customer($customer_id)
  {
    $del_num = 0;

    if (!empty($customer_id)) {
      $this->load->model('buy_customer_model');
      $rs = $this->buy_customer_model->upd_info_by_id($customer_id);

      if ($rs !== false) {
        echo "<script>alert('操作成功！');</script>";
      } else {
        echo "<script>alert('操作失败！');</script>";
      }
    }
    echo "<script>location.href='" . MLS_ADMIN_URL . "/buy_customer_info/index';</script>";
  }

  /**
   * 根据关键词获取楼盘信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_cmtinfo_by_kw()
  {
    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('buy_customer_model');
    $cmt_info = $this->buy_customer_model->get_total_community($keyword);

    foreach ($cmt_info as $key => $value) {
      $cmt_info[$key]['label'] = $value['cmt_name'];
    }

    if (empty($cmt_info)) {
      $cmt_info[0]['id'] = 0;
      $cmt_info[0]['label'] = '暂无小区';
      $cmt_info[0]['averprice'] = 0.00;
      $cmt_info[0]['address'] = '暂无地址';
      $cmt_info[0]['status'] = -1;
      $cmt_info[0]['districtname'] = '暂无信息';
      $cmt_info[0]['streetname'] = '暂无信息';
    }

    echo json_encode($cmt_info);
  }

  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';

    //物业类型条件
    if (isset($form_param['property_type']) && !empty($form_param['property_type']) && $form_param['property_type'] > 0) {
      $property_type = intval($form_param['property_type']);
      $cond_where .= " AND a.property_type = '" . $property_type . "'";
    }

    //状态条件
    if (isset($form_param['status']) && !empty($form_param['status']) && $form_param['status'] > 0) {
      $status = intval($form_param['status']);
      $cond_where .= " AND a.status = '" . $status . "'";
    }

    //是否合作
    if (isset($form_param['is_share']) && !empty($form_param['is_share']) && $form_param['is_share'] > 0) {
      $is_share = intval($form_param['is_share']);
      $cond_where .= " AND a.is_share = '" . $is_share . "'";
    }

    //区属、板块条件
    if (isset($form_param['dist_id']) && $form_param['dist_id'] > 0) {
      $dist_id = intval($form_param['dist_id']);

      $cond_where .= " AND (a.dist_id1 = '" . $dist_id . "' "
        . " OR a.dist_id2 = '" . $dist_id . "'"
        . " OR a.dist_id3 = '" . $dist_id . "')";


      $street_id = intval($form_param['street_id']);
      if ($street_id > 0) {
        $cond_where .= " AND (a.street_id1 = '" . $street_id . "' "
          . " OR a.street_id2 = '" . $street_id . "'"
          . " OR a.street_id3 = '" . $street_id . "')";
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

    //价格条件
    if ((isset($form_param["price_min"]) && $form_param["price_min"] > 0)
      || (isset($form_param["price_min"]) && $form_param["price_min"] > 0)
    ) {
      $price_min = floatval($form_param["price_min"]);
      $price_max = floatval($form_param["price_max"]);

      if ($price_max >= $price_min) {
        $cond_where .= " AND a.price_min >= '" . $price_min . "' AND "
          . "a.price_max <= '" . $price_max . "'";
      }
    }

    //面积条件
    if ((isset($form_param["area_min"]) && $form_param["area_min"] > 0)
      || (isset($form_param["area_max"]) && $form_param["area_max"] > 0)
    ) {
      $area_min = floatval($form_param["area_min"]);
      $area_max = floatval($form_param["area_max"]);
      $cond_where .= " AND a.area_min >= '" . $area_min . "' AND "
        . "a.area_max <= '" . $area_max . "'";
    }

    //户型条件
    if ((isset($form_param["room"]) && $form_param["room"] > 0)) {
      $room = floatval($form_param["room"]);
      $cond_where .= " AND  a.room_min <= '" . $room . "' AND "
        . "a.room_max >= '" . $room . "' ";
    }

    //客户编号
    if ($form_param['id']) {
      $id = substr($form_param['id'], 0, 2);
      if ($id == "QG") {
        $id = substr($form_param['id'], 2, strlen($form_param['id']) - 2);
      } else {
        $id = $form_param['id'];
      }
      $cond_where .= " AND a.id = '" . $id . "'";
    }

    //客户内部编号
    //to  do

    //性质条件
    if (isset($form_param['public_type']) && !empty($form_param['public_type'])) {
      $public_type = $form_param['public_type'];
      $cond_where .= " AND a.public_type = '" . $public_type . "'";
    }

    //公司名条件
    if (isset($form_param['agency_id']) && !empty($form_param['agency_id'])) {
      //$company_name = trim($form_param['company_name']);
      //$cond_where .= " AND c.name LIKE '%" . $company_name . "%'";
      $agency_id = $form_param['agency_id'];
      $cond_where .= " AND a.agency_id = '" . $agency_id . "'";
    }

    //经纪人信息条件
    if ((isset($form_param['where_broker_info']) && !empty($form_param['where_broker_info'])) && (isset($form_param['broker_content']) && !empty($form_param['broker_content']))) {
      $where_broker_info = $form_param['where_broker_info'];
      $broker_content = trim($form_param['broker_content']);
      $cond_where .= " AND " . $where_broker_info . " = '" . $broker_content . "'";
    }
    return $cond_where;
  }

  //获取排序字符串
  public function _get_orderby($customer_order, $where_order)
  {
    if ($where_order == 1) {
      $arr_order['order_key'] = "a." . $customer_order;
      $arr_order['order_by'] = 'ASC';
    } else if ($where_order == 2) {
      $arr_order['order_key'] = "a." . $customer_order;
      $arr_order['order_by'] = 'DESC';
    } else {
      $arr_order['order_key'] = 'a.id';
      $arr_order['order_by'] = 'DESC';
    }
    return $arr_order;
  }

  public function set_share_time($start = 0, $limit = 1000)
  {
    $where_cond = 'is_share > 0';
    $customer_list = $this->buy_customer_model->get_buylist_by_cond($where_cond, $start, $limit);

    if (is_full_array($customer_list)) {
      $update_arr = array();
      foreach ($customer_list as $k => $v) {
        $update_arr['set_share_time'] = $v['creattime'];
        $update_result = $this->buy_customer_model->update_customerinfo_by_cond($update_arr, array('id' => $v['id']));
        echo 'id:' . $v['id'];
        var_dump($update_result);
        echo '<br>';
      }
    }
  }

}
