<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 房源的接口
 * Created by PhpStorm.
 * User: asus
 * Date: 2017/5/11
 * Time: 13:14
 */
class House_info extends MY_Controller
{

  private $_config;
  private $_sell_type;
  private $_status;
  private $_nature;
  private $_forward;
  private $_fitment;
  private $_taxes;
  private $_entrust;
  private $_rent_tag;

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

  public function __construct()
  {
    parent::__construct();

    $data = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();

    $sell_type = array();
    foreach ($data['config']['sell_type'] as $key => $k) { //物业类型
      $sell_type[$k] = $key;
    }
    $status = array();
    foreach ($data['config']['status'] as $key => $k) { //状态类型
      $status[$k] = $key;
    }
    $nature = array();
    foreach ($data['config']['nature'] as $key => $k) { //性质类型
      $nature[$k] = $key;
    }
    $forward = array();
    foreach ($data['config']['forward'] as $key => $k) { //朝向类型
      $forward[$k] = $key;
    }
    $fitment = array();
    foreach ($data['config']['fitment'] as $key => $k) { //装修类型
      $fitment[$k] = $key;
    }
    $taxes = array();
    foreach ($data['config']['taxes'] as $key => $k) { //税费类型
      $taxes[$k] = $key;
    }
    $entrust = array();
    foreach ($data['config']['entrust'] as $key => $k) { //委托类型
      $entrust[$k] = $key;
    }

    $rent_tag = array();
    foreach ($data['config']['rent_tag'] as $key => $k) { //装修类型
      $rent_tag[$k] = $key;
    }

    $valid = array();
    foreach ($data['config']['valid'] as $key => $k) {
      $valid[$k] = $key;
    }

    $this->_config = $data['config'];
    $this->_sell_type = $sell_type;
    $this->_status = $status;
    $this->_nature = $nature;
    $this->_forward = $forward;
    $this->_fitment = $fitment;
    $this->_taxes = $taxes;
    $this->_entrust = $entrust;
    $this->_rent_tag = $rent_tag;
    $this->_valid = $valid;
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
    $this->result(1, 'entrust API for MLS.');
  }

  public function get_house_sell()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $dist = $this->input->get('dist');
    $street = $this->input->get('street');
    $price = $this->input->get('price');
    $room = $this->input->get('room');
    $order = $this->input->get('order');
    $page = intval($this->input->get('page'));
    $size = intval($this->input->get('size'));
    $start = $page > 0 ? ($page - 1) * $size : 0;
    $sql_where = '';
    $sql_where_list = array();
    if (intval($dist) > 0) {
      array_push($sql_where_list, '`district_id` = ' . $dist);
    }
    if (intval($street) > 0) {
      array_push($sql_where_list, '`street_id` = ' . $street);
    }
    if ($price) {
      switch ($price) {
        case 1:
          array_push($sql_where_list, '`price` < 100');
          break;
        case 2:
          array_push($sql_where_list, '(`price` >= 100 AND `price` < 180)');
          break;
        case 3:
          array_push($sql_where_list, '(`price` >= 180 AND `price` < 300)');
          break;
        case 4:
          array_push($sql_where_list, '`price` >= 300');
          break;
      }
    }
    if ($room) {
      switch ($room) {
        case 1:
          array_push($sql_where_list, '`room` = 1');
          break;
        case 2:
          array_push($sql_where_list, '`room` = 2');
          break;
        case 3:
          array_push($sql_where_list, '`room` = 3');
          break;
        case 4:
          array_push($sql_where_list, '(`room` = 0 OR `room` > 3)');
          break;
      }
    }
    $sql_count_where = '';
    $sql_where = ' WHERE ';
    array_push($sql_where_list, '`status` = 1');
    if (!empty($sql_where_list)) {
      $sql_where .= implode(' AND ', $sql_where_list);
      $sql_count_where .= implode(' AND ', $sql_where_list);
    }
    $sql_order = '';
    if (empty($order)) {
      $sql_order = ' ORDER BY id DESC';
    } else {
      switch ($order) {
        case 1 :
          $sql_order = ' ORDER BY price DESC ,id DESC';
          break;
        case 2:
          $sql_order = ' ORDER BY price ASC ,id DESC';
          break;
        case 3:
          $sql_order = ' ORDER BY buildarea DESC ,id DESC';
          break;
        case 4:
          $sql_order = ' ORDER BY buildarea ASC ,id DESC';
          break;
      }
    }
    $sql_select = implode(',', array(
      '`id`', '`broker_id`', '`agency_id`', '`company_id`',
      '`block_id`', '`block_name`', '`title`', '`room`',
      '`hall`', '`toilet`', '`buildarea`', '`price`', '`avgprice`',
      '`pic`', '`fitment`', '`status`'
    ));
    $sql = 'SELECT ' . $sql_select . ' FROM `sell_house`' . $sql_where . $sql_order;

    if (intval($start) > 0) {
      $this->_offset = intval($start);
    }

    if (intval($size) > 0) {
      $this->_limit = intval($size);
    }

    $sql_limit = $sql . ' LIMIT ' . $start . ',' . $this->_limit;
    $this->load->model('house_info_model');
    $this->house_info_model->set_tbl('sell_house');
    $sell_house = $this->house_info_model->get_all_by_sql($sql_limit);
    $this->_total_count = $this->house_info_model->get_count_by_cond($sql_count_where);

    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $this->_init_pagination($page, $this->_limit);

    $res = array();
    foreach ($sell_house as $key => $k) {
      $house = array();
      $house['id'] = $k['id'];
      $house['broker_id'] = $k['broker_id'];
      $house['agency_id'] = $k['agency_id'];
      $house['company_id'] = $k['company_id'];
      $house['block_id'] = $k['block_id'];
      $house['block_name'] = $k['block_name'];
      $house['title'] = $k['title'];
      $house['room'] = $k['room'];
      $house['hall'] = $k['hall'];
      $house['toilet'] = $k['toilet'];
      $house['buildarea'] = $k['buildarea'];
      $house['price'] = $k['price'];
      $house['avgprice'] = $k['avgprice'];
      $house['fitment'] = $k['fitment'];
      $house['pic'] = $k['pic'];
      $house['status'] = $k['status'];
      if ($house['status'] == 1) {
        $house['valid']['flag'] = '1';
        $house['valid']['name'] = $this->_config['valid']['1'];
      } else {
        $house['valid']['flag'] = '2';
        $house['valid']['name'] = $this->_config['valid']['2'];
      }
      $house['fitment'] = $this->_config['fitment'][$k['fitment']]; //装修类型

      array_push($res, $house);
    }

    $data['list'] = $res;
    $data['page'] = $page;
    $data['size'] = $this->_limit;
    $data['totalCount'] = $this->_total_count;
    $data['totalPages'] = $pages;
    $data['nowPage'] = $this->_current_page;
    $data['rowCount'] = count($res);
    echo $this->result(true, '', $data);
  }


  public function get_house_rent()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $dist = $this->input->get('dist');
    $street = $this->input->get('street');
    $price = $this->input->get('price');
    $room = $this->input->get('room');
    $order = $this->input->get('order');
    $page = intval($this->input->get('page'));
    $size = intval($this->input->get('size'));
    $start = $page > 0 ? ($page - 1) * $size : 0;
    $sql_where = '';
    $sql_where_list = array();
    if (intval($dist) > 0) {
      array_push($sql_where_list, '`district_id` = ' . $dist);
    }
    if (intval($street) > 0) {
      array_push($sql_where_list, '`street_id` = ' . $street);
    }
    if ($price) {
      switch ($price) {
        case 1:
          array_push($sql_where_list, '`price` < 1000');
          break;
        case 2:
          array_push($sql_where_list, '(`price` >= 1000 AND `price` < 2000)');
          break;
        case 3:
          array_push($sql_where_list, '(`price` >= 2000 AND `price` < 4000)');
          break;
        case 4:
          array_push($sql_where_list, '`price` >= 4000');
          break;
      }
    }
    if ($room) {
      switch ($room) {
        case 1:
          array_push($sql_where_list, '`room` = 1');
          break;
        case 2:
          array_push($sql_where_list, '`room` = 2');
          break;
        case 3:
          array_push($sql_where_list, '`room` = 3');
          break;
        case 4:
          array_push($sql_where_list, '(`room` = 0 OR `room` > 3)');
          break;
      }
    }
    $sql_count_where = '';
    $sql_where = ' WHERE ';
    array_push($sql_where_list, '`status` = 1');
    if (!empty($sql_where_list)) {
      $sql_where .= implode(' AND ', $sql_where_list);
      $sql_count_where .= implode(' AND ', $sql_where_list);
    }
    $sql_order = '';
    if (empty($order)) {
      $sql_order = ' ORDER BY id DESC';
    } else {
      switch ($order) {
        case 1 :
          $sql_order = ' ORDER BY price DESC ,id DESC';
          break;
        case 2:
          $sql_order = ' ORDER BY price ASC ,id DESC';
          break;
        case 3:
          $sql_order = ' ORDER BY buildarea DESC ,id DESC';
          break;
        case 4:
          $sql_order = ' ORDER BY buildarea ASC ,id DESC';
          break;
      }
    }
    $sql_select = implode(',', array(
      '`id`', '`broker_id`', '`agency_id`', '`company_id`',
      '`block_id`', '`block_name`', '`title`', '`room`',
      '`hall`', '`toilet`', '`buildarea`', '`price`', '`rent_tag`',
      '`pic`', '`fitment`', '`status`'
    ));
    $sql = 'SELECT ' . $sql_select . ' FROM `rent_house`' . $sql_where . $sql_order;

    if (intval($start) > 0) {
      $this->_offset = intval($start);
    }

    if (intval($size) > 0) {
      $this->_limit = intval($size);
    }

    $sql_limit = $sql . ' LIMIT ' . $start . ',' . $this->_limit;
    $this->load->model('house_info_model');
    $this->house_info_model->set_tbl('rent_house');
    $rent_house = $this->house_info_model->get_all_by_sql($sql_limit);
    $this->_total_count = $this->house_info_model->get_count_by_cond($sql_count_where);

    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $this->_init_pagination($page, $this->_limit);

    $res = array();
    foreach ($rent_house as $key => $k) {
      $house = array();
      $house['id'] = $k['id'];
      $house['broker_id'] = $k['broker_id'];
      $house['agency_id'] = $k['agency_id'];
      $house['company_id'] = $k['company_id'];
      $house['block_id'] = $k['block_id'];
      $house['block_name'] = $k['block_name'];
      $house['title'] = $k['title'];
      $house['room'] = $k['room'];
      $house['hall'] = $k['hall'];
      $house['toilet'] = $k['toilet'];
      $house['buildarea'] = $k['buildarea'];
      $house['price'] = $k['price'];

      $rent_tag_arr_name = array();

      if ($k['rent_tag']) {
        $rent_tag_arr = explode(',', $k['rent_tag']);
        if (is_full_array($rent_tag_arr)) {
          foreach ($rent_tag_arr as $key => $val) {
            $rent_tag = array();
            $rent_tag['sort'] = $val;
            $rent_tag['name'] = $this->_config['rent_tag'][$val];
            array_push($rent_tag_arr_name, $rent_tag);
          }
        }
      }
      $house['rent_tag'] = $rent_tag_arr_name;
      $house['fitment'] = $k['fitment'];
      $house['pic'] = $k['pic'];
      $house['status'] = $k['status'];
      if ($house['status'] == 1) {
        $house['valid']['flag'] = '1';
        $house['valid']['name'] = $this->_config['valid']['1'];
      } else {
        $house['valid']['flag'] = '2';
        $house['valid']['name'] = $this->_config['valid']['2'];
      }
      $house['fitment'] = $this->_config['fitment'][$k['fitment']]; //装修类型

      array_push($res, $house);
    }

    $data['list'] = $res;
    $data['page'] = $page;
    $data['size'] = $this->_limit;
    $data['totalCount'] = $this->_total_count;
    $data['totalPages'] = $pages;
    $data['nowPage'] = $this->_current_page;
    $data['rowCount'] = count($res);
    echo $this->result(true, '', $data);
  }

  public function get_sell_house_info()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    $id = intval($this->input->get('id'));
    $sql_select = implode(',', array(
      'sh.id', 'sh.broker_id', 'sh.agency_id', 'sh.company_id',
      'sh.block_id', 'sh.block_name', 'sh.title', 'sh.bewrite', 'sh.room',
      'sh.hall', 'sh.toilet', 'sh.kitchen', 'sh.balcony', 'sh.buildarea', 'sh.price', 'sh.avgprice',
      'sh.pic', 'sh.fitment', 'sh.pic_ids', 'cm.b_map_x', 'cm.b_map_y', 'cm.dist_id', 'cm.streetid',
      'sh.floor', 'sh.subfloor', 'sh.totalfloor', 'sh.buildyear', 'sh.forward', 'sh.status'
    ));
    $sql = 'SELECT ' . $sql_select . ' FROM (SELECT * FROM `sell_house` WHERE `id`=' . $id . ')AS `sh` LEFT JOIN `community` as `cm` ON cm.id=sh.block_id';
    $this->load->model('house_info_model');
    $sell_houses = $this->house_info_model->get_all_by_sql($sql);
    $sell_house = $sell_houses[0];
    $this->load->model('get_district_model');
    $sell_house['dist_name'] = $this->get_district_model->get_distname_by_id($sell_house['dist_id']);
    $sell_house['street_name'] = $this->get_district_model->get_streetname_by_id($sell_house['streetid']);
    //得到图片
    $pic_ids = $sell_house['pic_ids'];
    if (!empty($pic_ids)) {
      $picids = substr($pic_ids, 0, strlen($pic_ids) - 1);
      $sql_pic_select = '`block_id`,`id`,`type`,`url`';
      $sql_pic = 'SELECT ' . $sql_pic_select . ' from `upload` WHERE `id` in (' . $picids . ')';
      $house_pics = $this->house_info_model->get_all_by_sql($sql_pic);
    } else {
      $house_pics = array();
    }
    $housePicIndoor = array();
    $housePicModel = array();
    foreach ($house_pics as $key => $val) {
      $val['url'] = str_replace('/thumb', '', $val['url']);
      if ($val['type'] == 1) {
        array_push($housePicIndoor, $val);
      } else if ($val['type'] == 2) {
        array_push($housePicModel, $val);
      }
    }
    $sell_house['housePicIndoor'] = $housePicIndoor;
    $sell_house['housePicModel'] = $housePicModel;
    $sell_house['fitment'] = $this->_config['fitment'][$sell_house['fitment']]; //装修类型;
    $sell_house['forward'] = $this->_config['forward'][$sell_house['forward']]; //朝向类型;
    //$sell_house['status'] = $this->_config['status'][$sell_house['status']]; //状态类型;
    if ($sell_house['status'] == 1) {
      $sell_house['valid']['flag'] = '1';
      $sell_house['valid']['name'] = $this->_config['valid']['1'];
    } else {
      $sell_house['valid']['flag'] = '2';
      $sell_house['valid']['name'] = $this->_config['valid']['2'];
    }

//    $sell_house['bewrite_new0'] = preg_replace('/(&nbsp;)+/', ' ', strip_tags($sell_house['bewrite']));
//    $sell_house['bewrite_new1'] = trim(preg_replace('/(&nbsp;)+/', ' ', strip_tags($sell_house['bewrite'])));
//    $sell_house['bewrite_new2'] = preg_replace('/[\n\r\t\f][\n\r\t\f]*/', '\n', preg_replace('/(&nbsp;)+/', ' ', strip_tags($sell_house['bewrite'])));
//    $sell_house['bewrite_new3'] = preg_replace('/[\n\r\t\f][\n\r\t\f]*/', '\n', preg_replace('/(&nbsp;)+/', ' ', strip_tags($sell_house['bewrite'])));
    $sell_house['bewrite'] = trim(preg_replace('/\s+/', ' ', preg_replace('/[\n\r\t\f]+/', '\n', trim(preg_replace('/(&nbsp;)+/', ' ', strip_tags($sell_house['bewrite']))))));


    $this->load->model('broker_info_model');
    $sql_select = '`broker_id`,`truename`,`agency_id`,`company_id`,`phone`,`photo`';
    $sql = 'SELECT ' . $sql_select . ' FROM `broker_info` WHERE `broker_id`=' . $sell_house['broker_id'];
    $broker_infos = $this->broker_info_model->get_all_by_sql($sql);
    if (is_full_array($broker_infos)) {
      $broker_info = $broker_infos[0];
      $sql_companyName = 'SELECT name from agency WHERE id=' . $broker_info['company_id'];
      $companyNames = $this->broker_info_model->get_all_by_sql($sql_companyName);
      $sql_agencyName = 'SELECT name from agency WHERE id=' . $broker_info['agency_id'];
      $agencyNames = $this->broker_info_model->get_all_by_sql($sql_agencyName);
      if (is_full_array($companyNames)) {
        $sell_house['company_name'] = $companyNames[0]['name'];
      } else {
        $sell_house['company_name'] = '';
      }
      if (is_full_array($agencyNames)) {
        $sell_house['agency_name'] = $agencyNames[0]['name'];
      } else {
        $sell_house['agency_name'] = '';
      }
      $sell_house['broker_name'] = $broker_info['truename'];
      $sell_house['broker_phone'] = $broker_info['phone'];
      $sell_house['broker_photo'] = $broker_info['photo'];
    } else {
      $sell_house['company_name'] = '';
      $sell_house['agency_name'] = '';
      $sell_house['broker_name'] = '';
      $sell_house['broker_phone'] = '';
      $sell_house['broker_photo'] = '';
    }

    $data['data'] = $sell_house;
    echo $this->result(true, '', $data);
    //过滤数据
    //基础的数据
  }

  public function get_rent_house_info()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    $id = intval($this->input->get('id'));
    $sql_select = implode(',', array(
      'rh.id', 'rh.broker_id', 'rh.agency_id', 'rh.company_id',
      'rh.block_id', 'rh.block_name', 'rh.title', 'rh.bewrite', 'rh.room',
      'rh.hall', 'rh.toilet', 'rh.kitchen', 'rh.balcony', 'rh.buildarea', 'rh.price', 'rh.rent_tag',
      'rh.pic', 'rh.fitment', 'rh.pic_ids', 'cm.b_map_x', 'cm.b_map_y', 'cm.dist_id', 'streetid',
      'rh.floor', 'rh.subfloor', 'rh.totalfloor', 'rh.buildyear', 'rh.forward', 'rh.status'
    ));
    $sql = 'SELECT ' . $sql_select . ' FROM (SELECT * FROM `rent_house` WHERE `id`=' . $id . ')AS `rh` LEFT JOIN `community` as `cm` ON cm.id=rh.block_id';
    $this->load->model('house_info_model');
    $rent_houses = $this->house_info_model->get_all_by_sql($sql);
    $rent_house = $rent_houses[0];
    $this->load->model('get_district_model');
    $rent_house['dist_name'] = $this->get_district_model->get_distname_by_id($rent_house['dist_id']);
    $rent_house['street_name'] = $this->get_district_model->get_streetname_by_id($rent_house['streetid']);
    //得到图片
    $pic_ids = $rent_house['pic_ids'];
    if (!empty($pic_ids)) {
      $picids = substr($pic_ids, 0, strlen($pic_ids) - 1);
      $sql_pic_select = '`block_id`,`id`,`type`,`url`';
      $sql_pic = 'SELECT ' . $sql_pic_select . ' from `upload` WHERE `id` in (' . $picids . ')';
      $house_pics = $this->house_info_model->get_all_by_sql($sql_pic);
    } else {
      $house_pics = array();
    }
    $housePicIndoor = array();
    $housePicModel = array();
    foreach ($house_pics as $key => $val) {
      $val['url'] = str_replace('/thumb', '', $val['url']);
      if ($val['type'] == 1) {
        array_push($housePicIndoor, $val);
      } else if ($val['type'] == 2) {
        array_push($housePicModel, $val);
      }
    }
    $rent_house['housePicIndoor'] = $housePicIndoor;
    $rent_house['housePicModel'] = $housePicModel;
    $rent_house['fitment'] = $this->_config['fitment'][$rent_house['fitment']]; //装修类型;
    $rent_house['forward'] = $this->_config['forward'][$rent_house['forward']]; //朝向类型;
    // $rent_house['status'] = $this->_config['status'][$rent_house['status']]; //状态类型;
    if ($rent_house['status'] == 1) {
      $rent_house['valid']['flag'] = '1';
      $rent_house['valid']['name'] = $this->_config['valid']['1'];
    } else {
      $rent_house['valid']['flag'] = '2';
      $rent_house['valid']['name'] = $this->_config['valid']['2'];
    }
    $rent_tag_arr_name = array();
    if ($rent_house['rent_tag']) {
      $rent_tag_arr = explode(',', $rent_house['rent_tag']);
      if (is_full_array($rent_tag_arr)) {
        foreach ($rent_tag_arr as $key => $val) {
          $rent_tag = array();
          $rent_tag['sort'] = $val;
          $rent_tag['name'] = $this->_config['rent_tag'][$val];
          array_push($rent_tag_arr_name, $rent_tag);
        }
      }
    }
    $rent_house['rent_tag'] = $rent_tag_arr_name;

    $rent_house['bewrite'] = trim(preg_replace('/\s+/', ' ', preg_replace('/[\n\r\t\f]+/', '\n', trim(preg_replace('/(&nbsp;)+/', ' ', strip_tags($rent_house['bewrite']))))));

    $this->load->model('broker_info_model');
    $sql_select = '`broker_id`,`truename`,`agency_id`,`company_id`,`phone`,`photo`';
    $sql = 'SELECT ' . $sql_select . ' FROM `broker_info` WHERE `broker_id`=' . $rent_house['broker_id'];
    $broker_infos = $this->broker_info_model->get_all_by_sql($sql);
    if (is_full_array($broker_infos)) {
      $broker_info = $broker_infos[0];

      $sql_companyName = 'SELECT name from agency WHERE id=' . $broker_info['company_id'];
      $companyNames = $this->broker_info_model->get_all_by_sql($sql_companyName);
      $sql_agencyName = 'SELECT name from agency WHERE id=' . $broker_info['agency_id'];
      $agencyNames = $this->broker_info_model->get_all_by_sql($sql_agencyName);
      if (is_full_array($companyNames)) {
        $rent_house['company_name'] = $companyNames[0]['name'];
      } else {
        $rent_house['company_name'] = '';
      }
      if (is_full_array($agencyNames)) {
        $rent_house['agency_name'] = $agencyNames[0]['name'];
      } else {
        $rent_house['agency_name'] = '';
      }

      $rent_house['broker_name'] = $broker_info['truename'];
      $rent_house['broker_phone'] = $broker_info['phone'];
      $rent_house['broker_photo'] = $broker_info['photo'];
    } else {
      $rent_house['company_name'] = '';
      $rent_house['agency_name'] = '';
      $rent_house['broker_name'] = '';
      $rent_house['broker_phone'] = '';
      $rent_house['broker_photo'] = '';
    }

    $data['data'] = $rent_house;
    echo $this->result(true, '', $data);
    //过滤数据
    //基础的数据
  }

  /*
      public function get_sell_houses_info()
      {
          $post_param = $this->input->post(NULL, TRUE);

          $city = ltrim($post_param['city']);
          $city = $city ? $city : 'hz';
          //设置城市参数
          $this->set_city($city);

          $sql_where = '';
          $sql_where_list = array();

          $arr_ids = $post_param['ids'];

          if (isset($arr_ids) && !empty($arr_ids)) {
              //查询字段
              $arr_ids_str = ltrim($arr_ids);
              array_push($sql_where_list, "id in(" . $arr_ids_str . ")");

              if (!empty($sql_where_list)) {
                  $sql_where = ' WHERE ';
                  $sql_where .= implode(' AND ', $sql_where_list);
              }
              $sql_select = implode(',', array(
                  'sh.id', 'sh.broker_id', 'sh.agency_id', 'sh.company_id',
                  'sh.block_id', 'sh.block_name', 'sh.title', 'sh.room',
                  'sh.hall', 'sh.toilet', 'sh.buildarea', 'sh.price', 'sh.avgprice',
                  'sh.pic', 'sh.fitment', 'sh.status','bi.truename','bi.phone'
              ));

              $sql = 'SELECT ' . $sql_select . ' FROM (SELECT * FROM `sell_house`'.$sql_where.') AS `sh` LEFT JOIN `broker_info` AS `bi` ON bi.broker_id = sh.broker_id';
              $this->load->model('house_info_model');
              $sell_house = $this->house_info_model->get_all_by_sql($sql);

              $res = array();
              foreach ($sell_house as $key => $k) {
                  $house = array();
                  $house['id'] = $k['id'];
                  $house['broker_id'] = $k['broker_id'];
                  $house['broker_name'] = $k['truename']? $k['truename']:'';
                  $house['broker_phone'] = $k['phone']? $k['phone']:'';
                  $house['agency_id'] = $k['agency_id'];
                  $house['company_id'] = $k['company_id'];
                  $house['block_id'] = $k['block_id'];
                  $house['block_name'] = $k['block_name'];
                  $house['title'] = $k['title'];
                  $house['room'] = $k['room'];
                  $house['hall'] = $k['hall'];
                  $house['toilet'] = $k['toilet'];
                  $house['buildarea'] = $k['buildarea'];
                  $house['price'] = $k['price'];
                  $house['avgprice'] = $k['avgprice'];
                  $house['pic'] = $k['pic'];
                  $house['status'] = $k['status'];
                  $house['valid'] = array();
                  if($house['status'] == 1){
                      $house['valid']['flag'] = '1';
                      $house['valid']['name'] = $this->_config['valid']['1'];
                  } else {
                      $house['valid']['flag'] = '2';
                      $house['valid']['name'] = $this->_config['valid']['2'];
                  }
                  $house['fitment'] = $this->_config['fitment'][$k['fitment']]; //装修类型

                  array_push($res, $house);
              }

              $data['list'] = $res;
              $data['rowCount'] = count($res);
              echo $this->result(true, '', $data);
          } else {
              $data['list'] = array();
              $data['rowCount'] = 0;
              echo $this->result(true, '', $data);
          }
      }
      */

  public function get_sell_houses_info()
  {
    $post_param = $this->input->post(NULL, TRUE);

    $city = ltrim($post_param['city']);
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $sql_where = '';
    $sql_where_list = array();

    $arr_ids = $post_param['ids'];

    if (isset($arr_ids) && !empty($arr_ids)) {
      //查询字段
      $arr_ids_str = ltrim($arr_ids);
      array_push($sql_where_list, "id in(" . $arr_ids_str . ")");

      if (!empty($sql_where_list)) {
        $sql_where = ' WHERE ';
        $sql_where .= implode(' AND ', $sql_where_list);
      }
      $sql_select = implode(',', array(
        'sh.id', 'sh.broker_id', 'sh.agency_id', 'sh.company_id',
        'sh.block_id', 'sh.block_name', 'sh.title', 'sh.room',
        'sh.hall', 'sh.toilet', 'sh.buildarea', 'sh.price', 'sh.avgprice',
        'sh.pic', 'sh.fitment', 'sh.status', 'bi.truename', 'bi.phone'
      ));

      $sql = 'SELECT ' . $sql_select . ' FROM (SELECT * FROM `sell_house`' . $sql_where . ') AS `sh` LEFT JOIN `broker_info` AS `bi` ON bi.broker_id = sh.broker_id';
      $this->load->model('house_info_model');
      $sell_house = $this->house_info_model->get_all_by_sql($sql);

      $res = array();
      foreach ($sell_house as $key => $k) {
        $house = array();
        $house['id'] = $k['id'];
        $house['broker_id'] = $k['broker_id'];
        $house['broker_name'] = $k['truename'] ? $k['truename'] : '';
        $house['broker_phone'] = $k['phone'] ? $k['phone'] : '';
        $house['agency_id'] = $k['agency_id'];
        $house['company_id'] = $k['company_id'];
        $house['block_id'] = $k['block_id'];
        $house['block_name'] = $k['block_name'];
        $house['title'] = $k['title'];
        $house['room'] = $k['room'];
        $house['hall'] = $k['hall'];
        $house['toilet'] = $k['toilet'];
        $house['buildarea'] = $k['buildarea'];
        $house['price'] = $k['price'];
        $house['avgprice'] = $k['avgprice'];
        $house['pic'] = $k['pic'];
        $house['status'] = $k['status'];
        $house['valid'] = array();
        if ($house['status'] == 1) {
          $house['valid']['flag'] = '1';
          $house['valid']['name'] = $this->_config['valid']['1'];
        } else {
          $house['valid']['flag'] = '2';
          $house['valid']['name'] = $this->_config['valid']['2'];
        }
        $house['fitment'] = $this->_config['fitment'][$k['fitment']]; //装修类型

        array_push($res, $house);
      }
      $houses = array();
      foreach ($res as $key => $k) {
        $houses[$k['id']] = $k;
      }
      $ids_list = explode(',', $arr_ids);
      $houses_list = array();
      foreach ($ids_list as $key => $k) {
        $houses_list[$key] = $houses[$k] ? $houses[$k] : null;
      }
      $data['list'] = $houses_list;
      $data['rowCount'] = count($res);
      echo $this->result(true, '', $data);
    } else {
      $data['list'] = array();
      $data['rowCount'] = 0;
      echo $this->result(true, '', $data);
    }
  }


  public function get_rent_houses_info()
  {

    $post_param = $this->input->post(NULL, TRUE);

    $city = ltrim($post_param['city']);
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    //  $order = $post_param['order'];

    $sql_where_list = array();
    $arr_ids = $post_param['ids'];
    if (isset($arr_ids) && !empty($arr_ids)) {
      //查询字段
      $arr_ids_str = ltrim($arr_ids);
      array_push($sql_where_list, "id in(" . $arr_ids_str . ")");


      if (!empty($sql_where_list)) {
        $sql_where = ' WHERE ';
        $sql_where .= implode(' AND ', $sql_where_list);
      }
      $sql_select = implode(',', array(
        'rh.id', 'rh.broker_id', 'rh.agency_id', 'rh.company_id',
        'rh.block_id', 'rh.block_name', 'rh.title', 'rh.room',
        'rh.hall', 'rh.toilet', 'rh.buildarea', 'rh.price', 'rh.rent_tag',
        'rh.pic', 'rh.fitment', 'rh.status', 'bi.truename', 'bi.phone'
      ));
      $sql = 'SELECT ' . $sql_select . ' FROM (SELECT * FROM `rent_house`' . $sql_where . ') AS `rh` LEFT JOIN `broker_info` AS `bi` ON bi.broker_id = rh.broker_id';
      $this->load->model('house_info_model');
      $sell_house = $this->house_info_model->get_all_by_sql($sql);

      $res = array();
      foreach ($sell_house as $key => $k) {
        $house = array();
        $house['id'] = $k['id'];
        $house['broker_id'] = $k['broker_id'];
        $house['broker_name'] = $k['truename'] ? $k['truename'] : '';
        $house['broker_phone'] = $k['phone'] ? $k['phone'] : '';
        $house['agency_id'] = $k['agency_id'];
        $house['company_id'] = $k['company_id'];
        $house['block_id'] = $k['block_id'];
        $house['block_name'] = $k['block_name'];
        $house['title'] = $k['title'];
        $house['room'] = $k['room'];
        $house['hall'] = $k['hall'];
        $house['toilet'] = $k['toilet'];
        $house['buildarea'] = $k['buildarea'];
        $house['price'] = $k['price'];
        $rent_tag_arr_name = array();
        if ($k['rent_tag']) {
          $rent_tag_arr = explode(',', $k['rent_tag']);
          if (is_full_array($rent_tag_arr)) {
            foreach ($rent_tag_arr as $key => $val) {
              $rent_tag = array();
              $rent_tag['sort'] = $val;
              $rent_tag['name'] = $this->_config['rent_tag'][$val];
              array_push($rent_tag_arr_name, $rent_tag);
            }
          }
        }
        $house['rent_tag'] = $rent_tag_arr_name;
        $house['fitment'] = $k['fitment'];
        $house['pic'] = $k['pic'];
        $house['status'] = $k['status'];
        $house['valid'] = array();
        if ($house['status'] == 1) {
          $house['valid']['flag'] = '1';
          $house['valid']['name'] = $this->_config['valid']['1'];
        } else {
          $house['valid']['flag'] = '2';
          $house['valid']['name'] = $this->_config['valid']['2'];
        }
        $house['fitment'] = $this->_config['fitment'][$k['fitment']]; //装修类型
        array_push($res, $house);
      }
      $houses = array();
      foreach ($res as $key => $k) {
        $houses[$k['id']] = $k;
      }
      $ids_list = explode(',', $arr_ids);
      $houses_list = array();
      foreach ($ids_list as $key => $k) {
        $houses_list[$key] = $houses[$k] ? $houses[$k] : null;
      }
      $data['list'] = $houses_list;
      $data['rowCount'] = count($houses_list);
      echo $this->result(true, '', $data);
    } else {
      $data['list'] = array();
      $data['rowCount'] = 0;
      echo $this->result(true, '', $data);
    }
  }
}
