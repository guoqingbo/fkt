<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 营销中心-预约出售
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
    $this->load->model('appoint_center_model');
    $this->load->model('house_config_model');
  }

  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param, $type = '')
  {
    $house_config = $this->house_config_model->get_config();
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
    if (!empty($form_param['block_id']) && $form_param['block_id'] > 0) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "block_id = '" . $form_param['block_id'] . "'";
    }

    //电话
    if (isset($form_param['phone']) && !empty($form_param['phone'])) {
      $phone = intval($form_param['phone']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "apnt.phone LIKE '%" . $phone . "%'";
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
        $cond_where .= " and buildarea >= " . $area_min;
      }
      if (isset($area_max) && !empty($area_max)) {
        $cond_where .= " and buildarea <= " . $area_max;
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
        $cond_where .= " and price >= " . $price_min;
      }
      if (isset($price_max) && !empty($price_max)) {
        $cond_where .= " and price <= " . $price_max;
      }
    }

    //时间
    if (isset($form_param['apnt_time_key']) && !empty($form_param['apnt_time_key'])) {
      $apnt_date_time = $this->appoint_center_model->apnt_date_time();
      $apnt_date_time['sdate'][0] = preg_replace('/([\x80-\xff]*)/i', '', $apnt_date_time['sdate'][0]);
      $apnt_date_time['sdate'][1] = preg_replace('/([\x80-\xff]*)/i', '', $apnt_date_time['sdate'][1]);
      $apnt_date_time['sdate'][2] = preg_replace('/([\x80-\xff]*)/i', '', $apnt_date_time['sdate'][2]);
      $apnt_date_time['sdate'][4] = preg_replace('/([\x80-\xff]*)/i', '', $apnt_date_time['sdate'][4]);
      $apnt_date_time['sdate'][8] = preg_replace('/([\x80-\xff]*)/i', '', $apnt_date_time['sdate'][8]);
      switch ($form_param['apnt_time_key']) {
        case 1:
          $cond_where .= " and sdate  < '" . $apnt_date_time['sdate'][0] . "'";
          break;
        case 2:
          $cond_where .= " and sdate  = '" . $apnt_date_time['sdate'][1] . "'";
          break;
        case 3:
          $cond_where .= " and sdate >= '" . $apnt_date_time['sdate'][2] . "' and sdate <= '" . $apnt_date_time['sdate'][4] . "'";
          break;
        case 4:
          $cond_where .= " and sdate >= '" . $apnt_date_time['sdate'][2] . "' and sdate <= '" . $apnt_date_time['sdate'][8] . "'";
          break;
      }
    }

    return $cond_where;
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
    $broker_id = $this->user_arr['broker_id'];
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;// 获取当前页数
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext . ' AND type = 1 AND broker_info.broker_id = ' . $broker_id;
    //符合条件的总行数
    $data['total_count'] = $this->appoint_center_model->count_by_sell($cond_where);

    //获取提醒列表内容
    $data['list'] = $this->appoint_center_model->get_list_by_sell($cond_where, $this->_offset, $pagesize, 'ctime', 'DESC');

    $this->result(1, '预约出售列表获取成功', $data);
  }

  public function app_rent()
  {
    //模板使用数据
    $data = array();
    $broker_id = $this->user_arr['broker_id'];
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;// 获取当前页数
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param, 1);
    $cond_where .= $cond_where_ext . ' AND type = 2 AND broker_info.broker_id = ' . $broker_id;
    //符合条件的总行数
    $data['total_count'] = $this->appoint_center_model->count_by_rent($cond_where);

    //获取提醒列表内容
    $data['list'] = $this->appoint_center_model->get_list_by_rent($cond_where, $this->_offset, $pagesize, 'ctime', 'DESC');

    if ($data['list']) {
      foreach ($data['list'] as $key => $val) {
        if ($val['price_danwei'] > 0) {
          $data['list'][$key]['price'] = ($val['price'] / $val['buildarea']) / 30;
        }
      }
    }

    $this->result(1, '预约出租列表获取成功', $data);
  }


  /**
   * 获取预约显示日期时间
   */
  public function apnt_date_time()
  {
    $apnt_date_time = $this->appoint_center_model->apnt_date_time();
    $this->result(1, '成功获取预约显示时间', $apnt_date_time);
  }

}
/* End of file entrust.php */
/* Location: ./applications/mls/controllers/entrust.php */
