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
    $this->load->model('entrust_center_model');
    $this->load->model('house_config_model');
    $this->load->model('district_model');
  }

  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param, $type = '')
  {
    $house_config = $this->house_config_model->get_config();
    if ($type == 1) {
      $cond_where = 'ent_rent.status = 1 and is_check = 2';
    } else {
      $cond_where = 'ent_sell.status = 1 and is_check = 2';
    }

    //查看状态条件 暂不删
    /*if (isset($form_param['is_look']) && !empty($form_param['is_look']) )
    {
        $is_look = intval($form_param['is_look']); //
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
    if (!empty($form_param['block_id']) && $form_param['block_id'] > 0) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "comt_id = '" . $form_param['block_id'] . "'";
    }

    //面积
    if (isset($form_param['area_key']) && !empty($form_param['area_key'])) {
      if ($type) {
        $area = $house_config['rent_area'][$form_param['area_key']];
      } else {
        $area = $house_config['sell_area'][$form_param['area_key']];
      }
      $area = preg_replace("#[^0-9-]#", '', $area);
      $area = explode('-', $area);
      if (count($area) == 2) {
        $area_min = $area[0];
        $area_max = $area[1];
      } else {
        if ($form_param['area_key'] == 1) {
          $area_max = $area[0];
        } else {
          $area_min = $area[0];
        }
      }
      if (isset($area_min) && !empty($area_min)) {
        $cond_where .= " and area >= " . $area_min;
      }
      if (isset($area_max) && !empty($area_max)) {
        $cond_where .= " and area <= " . $area_max;
      }
    }


    //价格
    if (isset($form_param['price_key']) && !empty($form_param['price_key'])) {
      if ($type) {
        $price = $house_config['rent_price'][$form_param['price_key']];
      } else {
        $price = $house_config['sell_price'][$form_param['price_key']];
      }
      $price = preg_replace("#[^0-9-]#", '', $price);
      $price = explode('-', $price);
      if (count($price) == 2) {
        $price_min = $price[0];
        $price_max = $price[1];
      } else {
        if ($form_param['price_key'] == 1) {
          $price_max = $price[0];
        } else {
          $price_min = $price[0];
        }
      }
      if (isset($price_min) && !empty($price_min)) {
        $cond_where .= " and hprice >= " . $price_min;
      }
      if (isset($price_max) && !empty($price_max)) {
        $cond_where .= " and hprice <= " . $price_max;
      }
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

  public function ent_sell()
  {
    //模板使用数据
    $data = array();

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;// 获取当前页数
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //今天增加房源数
    $data['today_total'] = $this->entrust_center_model->get_today_total_num($tbl = 'ent_sell');

    //表单提交参数组成的查询条件
    //print_r($post_param);
    //echo "<hr/>";
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where = 'community.status = 2 and ent_sell.id not in (select grab_house.ent_id from grab_house where type = 1 and broker_id = ' . $broker_id . ') ';
    $cond_where .= !empty($cond_where) && !empty($cond_where_ext) ? ' AND ' . $cond_where_ext : '';

    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $data['total_count'] = $this->entrust_center_model->entrust_count_by_sell($cond_where);

    //获取提醒列表内容
    $data['list'] = $this->entrust_center_model->get_all_entrust_by_sell($cond_where, $this->_offset, $pagesize);
    foreach ($data['list'] as $key => $vo) {
      $data['list'][$key]['district_name'] = $this->district_model->get_distname_by_id($vo['dist_id']);
      $data['list'][$key]['street_name'] = $this->district_model->get_streetname_by_id($vo['streetid']);
      $data['list'][$key]['grab_times_total'] = 10;
    }
    $this->result(1, '房源委托出售列表获取成功', $data);
  }

  public function ent_sell_grab()
  {
    //模板使用数据
    $data = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //print_r($post_param);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);


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

    //获取提醒列表内容
    $list = $this->entrust_center_model->get_all_entrust_by_sell_grab($cond_where, $this->_offset, $pagesize);
    $data['list'] = $list;
    //print_r($data['list']);
    foreach ($data['list'] as $key => $vo) {
      $data['list'][$key]['district_name'] = $this->district_model->get_distname_by_id($vo['dist_id']);
      $data['list'][$key]['street_name'] = $this->district_model->get_streetname_by_id($vo['streetid']);
      $data['list'][$key]['grab_times_total'] = 10;
    }
    $this->result(1, '房源委托出售抢拍成功列表获取成功', $data);
  }

  public function ent_rent()
  {
    //模板使用数据
    $data = array();

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;// 获取当前页数
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= !empty($cond_where) ? ' AND ' . $cond_where_ext : $cond_where_ext;
    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $data['total_count'] = $this->entrust_center_model->entrust_count_by_rent($cond_where);

    //获取提醒列表内容
    $list = $this->entrust_center_model->get_all_entrust_by_rent($cond_where, $this->_offset, $this->_limit);
    $data['list'] = $list;
    $this->result(1, '业主委托出租列表获取成功', $data);
  }

  /**
   * 出售详情页
   * @access  public
   * @return  json
   */
  public function detail_sell()
  {
    $id = $this->input->post('id', TRUE);
    $this->entrust_center_model->update_sell_by_id(array("is_look" => 2), $id);
    $data = $this->entrust_center_model->get_sell_by_id($id);
    $this->result(1, '业主委托出售详情获取成功', $data);

  }

  /**
   * 出租详情页
   * @access  public
   * @return  json
   */
  public function detail_rent()
  {
    $id = $this->input->post('id', TRUE);
    $this->entrust_center_model->update_rent_by_id(array("is_look" => 2), $id);
    $data = $this->entrust_center_model->get_rent_by_id($id);
    $this->result(1, '业主委托出租详情获取成功', $data);

  }
}
/* End of file entrust.php */
/* Location: ./applications/mls_mobile/controllers/entrust.php */
