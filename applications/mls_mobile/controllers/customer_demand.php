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
  private function _get_cond_str($form_param, $type)
  {
    $house_config = $this->house_config_model->get_config();
    $cond_where = " is_check = 2";
    //姓名
    if (isset($form_param['realname']) && !empty($form_param['realname'])) {
      $cond_where .= " and realname LIKE '%" . $form_param['realname'] . "%'";
    }
    //电话
    if (isset($form_param['phone']) && !empty($form_param['phone'])) {
      $cond_where .= " and phone LIKE '%" . $form_param['phone'] . "%'";
    }
    //区属
    if (isset($form_param['district']) && !empty($form_param['district'])) {
      $cond_where .= " and district_id =" . $form_param['district'];
    }
    //户型
    if (isset($form_param['room']) && !empty($form_param['room'])) {
      if ($form_param['room'] <= 5) {
        $cond_where .= " and room =" . $form_param['room'];
      } else {
        $cond_where .= " and room >=" . $form_param['room'];
      }
    }
    //面积区间
    if (isset($form_param['area_key']) && !empty($form_param['area_key'])) {
      if ($type == "sell") {
        $area = $house_config['sell_area'][$form_param['area_key']];
      } else {
        $area = $house_config['rent_area'][$form_param['area_key']];
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
        $cond_where .= " and larea >= " . $area_min;
      }
      if (isset($area_max) && !empty($area_max)) {
        $cond_where .= " and harea <= " . $area_max;
      }
    }

    //面积区间
    if (isset($form_param['price_key']) && !empty($form_param['price_key'])) {
      if ($type == "sell") {
        $price = $house_config['sell_price'][$form_param['price_key']];
      } else {
        $price = $house_config['rent_price'][$form_param['price_key']];
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
        $cond_where .= " and lprice >= " . $price_min;
      }
      if (isset($price_max) && !empty($price_max)) {
        $cond_where .= " and hprice <= " . $price_max;
      }
    }

    $cond_where = trim($cond_where);
    return $cond_where;
  }

  public function seek_buy()
  {
    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //post参数
    $post_param = $this->input->post(NULL, TRUE) ? $this->input->post(NULL, TRUE) : array();

    //今天增加客源数
    $data['today_total'] = $this->customer_demand_model->get_today_total_num($tbl = 'seek_sell');

    $cond_where = $this->_get_cond_str($post_param, 'sell');

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
        $list[$key]['grab_times_total'] = 10;
      }
    }
    $data['list'] = $list;

    $this->result(1, '查询成功', $data);
  }

  public function seek_buy_grab()
  {
    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //post参数
    $post_param = $this->input->post(NULL, TRUE) ? $this->input->post(NULL, TRUE) : array();
    //今天增加房源数
    $data['today_total'] = $this->customer_demand_model->get_today_total_num($tbl = 'seek_sell');

    $cond_where = $this->_get_cond_str($post_param, 'sell');
    $cond_where .= " and seek_sell.id in (select grab_house.ent_id from grab_house where grab_house.broker_id = {$this->user_arr['broker_id']} and grab_house.type = 3)";
    //符合条件的总行数
    $this->_total_count =
      $this->customer_demand_model->count_by($cond_where, $tbl = "seek_sell");

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->customer_demand_model->get_all_by($cond_where, $tbl = "seek_sell", $this->_offset, $this->_limit);
    $data['list'] = $list;

    $this->result(1, '查询成功', $data);
  }

  public function seek_rent()
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE) ? $this->input->post(NULL, TRUE) : array();
    //今天增加客源数
    $data['today_total'] = $this->customer_demand_model->get_today_total_num($tbl = 'seek_rent');
    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $cond_where = $this->_get_cond_str($post_param, 'rent');
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
        $list[$key]['grab_times_total'] = 10;
      }
    }
    $data['list'] = $list;

    $this->result(1, '查询成功', $data);
  }


  public function seek_rent_grab()
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE) ? $this->input->post(NULL, TRUE) : array();
    //今天增加房源数
    $data['today_total'] = $this->customer_demand_model->get_today_total_num($tbl = 'seek_rent');
    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $cond_where = $this->_get_cond_str($post_param, 'rent');
    $cond_where .= " and seek_rent.id in (select grab_house.ent_id from grab_house where grab_house.broker_id = {$this->user_arr['broker_id']} and grab_house.type = 4)";

    //符合条件的总行数
    $this->_total_count =
      $this->customer_demand_model->count_by($cond_where, $tbl = "seek_rent");

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->customer_demand_model->get_all_by($cond_where, $tbl = "seek_rent", $this->_offset, $this->_limit);
    $data['list'] = $list;

    $this->result(1, '查询成功', $data);
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
}
