<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 营销中心-业主委托
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Entrust_center extends MY_Controller
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
    $this->load->model('entrust_center_model');
    $this->load->model('district_model');
  }

  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = 'ent_sell.status = 1 and is_check = 2';

    //查看状态条件 暂不删
    /*if (isset($form_param['is_look']) && !empty($form_param['is_look']) )
    {
        $is_look = intval($form_param['is_look']);
        $cond_where .= !empty($cond_where) ?  ' AND ' : '';
        $cond_where .= "is_look = '".$is_look."'";
    }else if($form_param['is_look'] == '0'){
        $cond_where .= !empty($cond_where) ?  ' AND ' : '';
        $cond_where .= "is_look IN (0,1,2)";
    }

    //查看出租方式条件  暂不删
    if (isset($form_param['type']) && !empty($form_param['type']) )
    {
        $type = intval($form_param['type']);
        $cond_where .= !empty($cond_where) ?  ' AND ' : '';
        $cond_where .= "type = '".$type."'";
    }else if($form_param['type'] == '0'){
        $cond_where .= !empty($cond_where) ?  ' AND ' : '';
        $cond_where .= "type IN (0,1,2,3)";
    }

    //查看户型条件 暂不删
    if (isset($form_param['room']) && !empty($form_param['room']) )
    {
        $room = intval($form_param['room']);
        $cond_where .= !empty($cond_where) ?  ' AND ' : '';
        $cond_where .= "room = '".$room."'";
    }else if($form_param['room'] == '0'){
        $cond_where .= !empty($cond_where) ?  ' AND ' : '';
        $cond_where .= "room IN (0,1,2,3,4,5,6)";
    }*/

    //姓名
    if (isset($form_param['realname']) && !empty($form_param['realname'])) {
      $realname = trim($form_param['realname']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "realname LIKE '%" . $realname . "%'";
    }

    //电话
    if (isset($form_param['phone']) && !empty($form_param['phone'])) {
      $phone = intval($form_param['phone']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "phone LIKE '%" . $phone . "%'";
    }

    //楼盘ID出售出租
    if (!empty($form_param['block_name']) && $form_param['block_id'] > 0) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "comt_id = '" . $form_param['block_id'] . "'";
    }

    //最小面积
    if (isset($form_param['buildarea1']) && !empty($form_param['buildarea1'])) {
      $buildarea1 = trim($form_param['buildarea1']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "area >= '" . $buildarea1 . "'";
    }

    //最大面积
    if (isset($form_param['buildarea2']) && !empty($form_param['buildarea2'])) {
      $buildarea2 = trim($form_param['buildarea2']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "area <= '" . $buildarea2 . "'";
    }

    //最小价格
    if (isset($form_param['price1']) && !empty($form_param['price1'])) {
      $price1 = trim($form_param['price1']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "hprice >= '" . $price1 . "'";
    }

    //最大价格
    if (isset($form_param['price2']) && !empty($form_param['price2'])) {
      $price2 = trim($form_param['price2']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "hprice <= '" . $price2 . "'";
    }
    //区属
    if (isset($form_param['district']) && !empty($form_param['district'])) {
      $district = intval($form_param['district']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "community.dist_id = '" . $district . "'";
    }

    //板块
    if (isset($form_param['street']) && !empty($form_param['street'])) {
      $street = intval($form_param['street']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "community.streetid = '" . $street . "'";
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

  public function ent_sell($type = '')
  {
    if ($type) {
      $this->_ent_sell_grab();
    } else {
      $this->_ent_sell();
    }
  }

  private function _ent_sell()
  {
    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //print_r($post_param);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page, $this->_limit);

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
    //今天增加房源数
    $data['today_total'] = $this->entrust_center_model->get_today_total_num($tbl = 'ent_sell');

    //表单提交参数组成的查询条件
    //print_r($post_param);
    //echo "<hr/>";
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where = 'community.status = 2 and ent_sell.id not in (select grab_house.ent_id from grab_house where type = 1 and broker_id = ' . $broker_id . ')';
    $cond_where .= !empty($cond_where) && !empty($cond_where_ext) ? ' AND ' . $cond_where_ext : '';
    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);
    //符合条件的总行数
    $this->_total_count = $this->entrust_center_model->entrust_count_by_sell($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取提醒列表内容
    $list = $this->entrust_center_model->get_all_entrust_by_sell($cond_where, $this->_offset, $this->_limit);
    $data['list'] = $list;
    //print_r($data['list']);exit;
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
    $data['page_title'] = '房源委托表';
    $data['page'] = $page;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/market.css,mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js');

    $data['post_param'] = $post_param;

    //如果后几个搜搜参数被选中则，后面的选择参数全部显示
    if (!empty($post_param['property_type']) || !empty($post_param['room']) ||
      !empty($post_param['public_type']) || !empty($post_param['status']) ||
      !empty($post_param['is_look'])
    ) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
    }
    $this->view('marketing_center/entrust_center/entrust_sell', $data);
  }

  private function _ent_sell_grab()
  {
    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //print_r($post_param);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page, $this->_limit);

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
    //今天增加房源数
    $data['today_total'] = $this->entrust_center_model->get_today_total_num($tbl = 'ent_sell');
    //表单提交参数组成的查询条件
    //print_r($post_param);
    //echo "<hr/>";
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where = 'grab_house.broker_id = ' . $broker_id;
    $cond_where .= !empty($cond_where) && !empty($cond_where_ext) ? ' AND ' . $cond_where_ext : '';
    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);
    //符合条件的总行数
    $this->_total_count = $this->entrust_center_model->entrust_count_by_sell_grab($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取提醒列表内容
    $list = $this->entrust_center_model->get_all_entrust_by_sell_grab($cond_where, $this->_offset, $this->_limit);
    $data['list'] = $list;
    //print_r($data['list']);
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
    $data['page_title'] = '业主委托出售表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/personal_center.css,mls/css/v1.0/house_manage.css'
      . ',mls/css/v1.0/market.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js');

    $data['post_param'] = $post_param;

    //如果后几个搜搜参数被选中则，后面的选择参数全部显示
    if (!empty($post_param['property_type']) || !empty($post_param['room']) ||
      !empty($post_param['public_type']) || !empty($post_param['status']) ||
      !empty($post_param['is_look'])
    ) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
    }
    $this->view('marketing_center/entrust_center/entrust_sell_grab', $data);
  }

  public function ent_rent()
  {
    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //print_r($post_param);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page, $this->_limit);

    //表单提交参数组成的查询条件
    //print_r($post_param);
    //echo "<hr/>";
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= !empty($cond_where) ? ' AND ' . $cond_where_ext : $cond_where_ext;
    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count = $this->entrust_center_model->entrust_count_by_rent($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取提醒列表内容
    $list = $this->entrust_center_model->get_all_entrust_by_rent($cond_where, $this->_offset, $this->_limit);
    $data['list'] = $list;
    //print_r($data['entrust_list']);exit;
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
    $data['page_title'] = '业主委托出租表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/personal_center.css,mls/css/v1.0/house_manage.css'
      . ',mls/css/v1.0/market.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');

    $data['post_param'] = $post_param;

    //如果后几个搜搜参数被选中则，后面的选择参数全部显示
    if (!empty($post_param['property_type']) || !empty($post_param['room']) ||
      !empty($post_param['public_type']) || !empty($post_param['status']) ||
      !empty($post_param['is_look'])
    ) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
    }
    $this->view('marketing_center/entrust_center/entrust_rent', $data);
  }

  /**
   * 跟进提醒出售详情页
   * @access  public
   * @return  json
   */
  public function detail_sell()
  {
    $id = $this->input->post('id', TRUE);
    //$this->entrust_center_model->update_sell_by_id(array("is_look"=>2),$id);
    $data = $this->entrust_center_model->get_sell_by_id($id);
    echo json_encode($data);

  }

  /**
   * 跟进提醒出租详情页
   * @access  public
   * @return  json
   */
  public function detail_rent()
  {
    $id = $this->input->post('id', TRUE);
    //$this->entrust_center_model->update_rent_by_id(array("is_look"=>2),$id);
    $data = $this->entrust_center_model->get_rent_by_id($id);
    echo json_encode($data);

  }

  //检查当前页是否有数据，如果没有则刷新
  public function check_list()
  {
    // 分页参数

    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $this->_init_pagination($page);

    $cond_where = $this->_get_cond_str($post_param);
    $cond_where .= " and ent_sell.id not in (select grab_house.ent_id from grab_house where grab_house.broker_id = {$this->user_arr['broker_id']} and grab_house.type = 1)";
    //获取列表内容
    $list = $this->entrust_center_model->get_all_entrust_by_sell($cond_where, $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      echo 1;
    } else {
      echo 0;
    }
  }
}
/* End of file entrust.php */
/* Location: ./applications/mls/controllers/entrust.php */
