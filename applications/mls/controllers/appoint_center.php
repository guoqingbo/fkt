<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 营销中心-业主预约
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Appoint_center extends MY_Controller
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


  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('agency_model');
    $this->load->model('appoint_center_model');
    $this->load->model('company_employee_model');
    $this->load->model('district_model');//区属模型类
    $this->load->model('sell_house_model');
    //加载楼盘模型类
    $this->load->model('community_model');
    //表单验证
    $this->load->library('form_validation');
    //加载房源标题模板类
    $this->load->model('house_title_template_model');
    $this->load->model('rent_house_model');
    $this->load->model('broker_model');
    $this->load->model('house_collect_model');
    $this->load->model('sell_model');
    $this->load->model('api_broker_model');
    $this->load->library('Verify');

    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
    }
  }

  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param, $type = '')
  {
    if ($type == 1) {
      $cond_where = 'rent_house.status = 1';
    } else {
      $cond_where = 'sell_house.status = 1';
    }

    if (!empty($form_param['house_id']) && $form_param['house_id'] > 0) {
      $house_id = intval($form_param['house_id']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "house_id = '" . $house_id . "'";
    }


    //查看户型条件
    if (isset($form_param['room']) && !empty($form_param['room'])) {
      $room = intval($form_param['room']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "room = '" . $room . "'";
    } else if ($form_param['room'] == '0') {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "room IN (0,1,2,3,4,5,6)";
    }

    //区属
    $district_id = intval($form_param['dist_id']);
    //板块
    $street_id = intval($form_param['street_id']);
    if ($street_id) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      if ($type == 1) {
        $cond_where .= "rent_house.street_id = '" . $street_id . "'";
      } else {
        $cond_where .= "sell_house.street_id = '" . $street_id . "'";
      }
    } else if ($district_id) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "district_id = '" . $district_id . "'";
    }

    //楼盘ID出售出租
    if (!empty($form_param['block_name']) && $form_param['block_id'] > 0) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "block_id = '" . $form_param['block_id'] . "'";
    }

    //电话
    if (isset($form_param['phone']) && !empty($form_param['phone'])) {
      $phone = intval($form_param['phone']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "apnt.phone LIKE '%" . $phone . "%'";
    }


    //最小面积
    if (isset($form_param['buildarea1']) && !empty($form_param['buildarea1'])) {
      $buildarea1 = trim($form_param['buildarea1']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "buildarea >= '" . $buildarea1 . "'";
    }

    //最大面积
    if (isset($form_param['buildarea2']) && !empty($form_param['buildarea2'])) {
      $buildarea2 = trim($form_param['buildarea2']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "buildarea <= '" . $buildarea2 . "'";
    }

    //最小价格
    if (isset($form_param['price1']) && !empty($form_param['price1'])) {
      $price1 = trim($form_param['price1']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "price >= '" . $price1 . "'";
    }

    //最大价格
    if (isset($form_param['price2']) && !empty($form_param['price2'])) {
      $price2 = trim($form_param['price2']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "price <= '" . $price2 . "'";
    }

    //最小时间
    if (isset($form_param['stimemin']) && !empty($form_param['stimemin'])) {
      $stimemin = (trim($form_param['stimemin']));
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "sdate >= '" . $stimemin . "'";
    }

    //最大时间
    if (isset($form_param['stimemax']) && !empty($form_param['stimemax'])) {
      $stimemax = (trim($form_param['stimemax']));
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "sdate <= '" . $stimemax . "'";
    }

    return $cond_where;
  }


  //获取排序字符串
  private function _get_orderby_arr($order_val)
  {
    $arr_order = array();

    switch ($order_val) {
      case 1:
        $arr_order['order_key'] = 'ctime';
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

  public function app_sell()
  {
    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //当前用户的所有信息
    //$broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
    //$agency_info = $this->agency_model->get_by_id($broker_info['agency_id']);

    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //print_r($post_param);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_init_pagination($page, $this->_limit);

    //表单提交参数组成的查询条件
    //print_r($post_param);
    //echo "<hr/>";
    $cond_where_ext = $this->_get_cond_str($post_param);
    //$cond_where .= $cond_where_ext. ' AND bid = '.$broker_id;
    $cond_where .= $cond_where_ext . ' AND type = 1 AND broker_info.broker_id = ' . $broker_id;
    //排序字段
    $roomorder = 1;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count = $this->appoint_center_model->count_by_sell($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取提醒列表内容
    $list = $this->appoint_center_model->get_list_by_sell($cond_where, $this->_offset, $this->_limit, 'ctime', 'desc');
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
    $data['page_title'] = '业主预约出售表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/personal_center.css,mls/css/v1.0/house_manage.css'
      . ',mls/css/v1.0/market.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js,mls/js/v1.0/backspace.js');

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
    $this->view('marketing_center/appoint_center/appoint_sell', $data);
  }

  public function app_rent()
  {
    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //当前用户的所有信息
    //$broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
    //$agency_info = $this->agency_model->get_by_id($broker_info['agency_id']);

    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //print_r($post_param);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_init_pagination($page, $this->_limit);

    //表单提交参数组成的查询条件
    //print_r($post_param);
    //echo "<hr/>";
    $cond_where_ext = $this->_get_cond_str($post_param, 1);
    //$cond_where .= $cond_where_ext. ' AND bid = '.$broker_id;
    $cond_where .= $cond_where_ext . ' AND type = 2 AND broker_info.broker_id = ' . $broker_id;
    //排序字段
    $roomorder = 1;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count = $this->appoint_center_model->count_by_rent($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取提醒列表内容
    $list = $this->appoint_center_model->get_list_by_rent($cond_where, $this->_offset, $this->_limit, 'ctime', 'desc');
    if ($list) {
      foreach ($list as $key => $val) {
        if ($val['price_danwei'] > 0) {
          $list[$key]['price'] = ($val['price'] / $val['buildarea']) / 30;
        }
      }
    }
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
    $data['page_title'] = '业主预约出售表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/personal_center.css,mls/css/v1.0/house_manage.css'
      . ',mls/css/v1.0/market.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js,mls/js/v1.0/backspace.js');

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
    $this->view('marketing_center/appoint_center/appoint_rent', $data);
  }


  /**
   * 删除 预约
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function del($del_id = 0)
  {
    //遗留 判断有无删除此房源权限
    $isajax = $this->input->get('isajax', TRUE);

    if ($isajax) {
      $str = $this->input->get('str', TRUE);
    } else {
      $str = $del_id;
    }
    $app_id = $this->input->get('app_id', TRUE);
    $arr = array('id' => $app_id);
    $up_num = $this->appoint_center_model->del_appoint($arr);
    //echo json_encode($up_num);die();

    if ($isajax) {
      echo json_encode(array('result' => 'ok'));
    } else {
      $this->jump('/appoint_center/app_sell/', '删除成功');
    }
  }
}
/* End of file entrust.php */
/* Location: ./applications/mls/controllers/entrust.php */
