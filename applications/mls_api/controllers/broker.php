<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 经纪人基本信息接口
 * @package         applications
 * @author          fisher
 * @copyright       Copyright (c) 2006 - 2015
 * @version         1.0
 */
class Broker extends MY_Controller
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
    foreach ($data['config']['rent_tag'] as $key => $k) { //朝向类型
      $rent_tag[$k] = $key;
    }

    $valid = array();
    foreach ($data['config']['valid'] as $key => $k) { //朝向类型
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
   * Index Page for this controller.
   */
  public function index()
  {
    $this->result(1, 'entrust API for MLS.');
  }

  public function get_broker_list()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    $data = array();

    $this->load->model('agency_model');
    $this->load->model('broker_info_model');
    $page = intval($this->input->get('page'));
    $size = intval($this->input->get('size'));
    // $start = $page > 0 ? ($page - 1) * $size : 0;
    $this->_init_pagination($page, $size);
    $dist_id = $this->input->get('dist');
    $street_id = $this->input->get('street');

    //筛选出门店
    $agency_where = 'status = 1 AND company_id!=0';
    if ($dist_id) {
      $agency_where .= ' and dist_id = ' . $dist_id;
    }
    if ($street_id) {
      $agency_where .= ' and street_id = ' . $street_id;
    }
    $agencys = $this->agency_model->get_all_by($agency_where);
    if (empty($agencys)) {
      $data['list'] = [];
      $data['page'] = $page;
      $data['size'] = $this->_limit;
      $data['totalCount'] = 0;
      $data['totalPages'] = 0;
      $data['nowPage'] = 0;
      $data['rowCount'] = 0;
    } else {
      $agency_ids = array();
      foreach ($agencys as $key => $value) {
        //目前的数据库id就是agency_id
        array_push($agency_ids, $value['id']);
      }
      $get_broker = ' (SELECT * FROM `broker_info` WHERE `agency_id` in (' . implode(',', $agency_ids) . ')) AS `bi`';

      //select的内容
      $select_list = implode(',', array(
        'bi.broker_id', 'bi.truename', 'bi.agency_id', 'bi.company_id',
        'bi.phone', 'bi.photo', 'IFNULL(sh.sc,0) as sell_count', 'IFNULL(rh.rc,0) as rent_count',
        'ag.name as agency_name', 'ay.name as company_name'
      ));

      //得到company_name
      $get_company_name = ' LEFT JOIN agency as ay ON bi.company_id=ay.id';

      //得到agency_name
      $get_agency_name = ' LEFT JOIN agency as ag ON bi.agency_id=ag.id';

      //排序
      $order_condition = ' LEFT JOIN (SELECT broker_id, COUNT(*)AS `sc` FROM `sell_house` GROUP BY `broker_id`) AS `sh` ON sh.broker_id=bi.broker_id';
      $order_condition .= ' LEFT JOIN (SELECT broker_id, COUNT(*)AS `rc` FROM `rent_house` GROUP BY `broker_id`) AS `rh` ON rh.broker_id=bi.broker_id';
      $order_condition .= ' ORDER BY sell_count+rent_count DESC, bi.broker_id DESC';

      //分页
      $paging = ' LIMIT ' . $this->_offset . ',' . $this->_limit;

      $sql = 'SELECT ' . $select_list . ' FROM' . $get_broker . $get_agency_name . $get_company_name . $order_condition . $paging;
      $brokers = $this->broker_info_model->get_all_by_sql($sql);

      $this->_total_count = $this->broker_info_model->count_all_broker('`agency_id` in (' . implode(',', $agency_ids) . ')');
      $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
      $data['list'] = $brokers;
      $data['page'] = $page;
      $data['size'] = $this->_limit;
      $data['totalCount'] = $this->_total_count;
      $data['totalPages'] = $pages;
      $data['nowPage'] = $this->_current_page;
      $data['rowCount'] = count($brokers);
    }
    echo $this->result(true, '', $data);
  }

  public function get_broker_info()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    $data = array();
    $broker_id = intval($this->input->get('id'));
    $this->load->model('broker_info_model');

    $sql_select = '`broker_id`,`truename`,`agency_id`,`company_id`,`phone`,`photo`';
    $sql = 'SELECT ' . $sql_select . ' FROM `broker_info` WHERE `broker_id`=' . $broker_id;
    $broker_infos = $this->broker_info_model->get_all_by_sql($sql);
    $broker_info = $broker_infos[0];

    $sql_companyName = 'SELECT name from agency WHERE id=' . $broker_info['company_id'];
    $companyNames = $this->broker_info_model->get_all_by_sql($sql_companyName);
    $sql_agencyName = 'SELECT name from agency WHERE id=' . $broker_info['agency_id'];
    $agencyNames = $this->broker_info_model->get_all_by_sql($sql_agencyName);
    $broker_info['company_name'] = $companyNames[0]['name'];
    $broker_info['agency_name'] = $agencyNames[0]['name'];

    $data['data'] = $broker_info;
    echo $this->result(true, '', $data);
  }

  public function get_broker_sell()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    $data = array();
    $broker_id = intval($this->input->get('id'));
    $page = intval($this->input->get('page'));
    $size = intval($this->input->get('size'));
    $this->_init_pagination($page, $size);

    //select
    $sql_select = implode(',', array(
      '`id`', '`broker_id`', '`agency_id`', '`company_id`',
      '`block_id`', '`block_name`', '`title`', '`room`',
      '`hall`', '`toilet`', '`buildarea`', '`price`', '`avgprice`',
      '`pic`', '`fitment`', '`status`'
    ));

    //where
    $where = '`broker_id` = ' . $broker_id;
    $where .= ' AND `status` = 1';

    //order
    $order = ' ORDER BY id DESC';

    //分页
    $paging = ' LIMIT ' . $this->_offset . ',' . $this->_limit;

    $sql = 'SELECT ' . $sql_select . ' FROM sell_house WHERE ' . $where . $order . $paging;

    $this->load->model('house_info_model');
    $this->house_info_model->set_tbl('sell_house');
    $sell_house = $this->house_info_model->get_all_by_sql($sql);

    $this->_total_count = $this->house_info_model->get_count_by_cond($where);
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

  public function get_broker_rent()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    $data = array();
    $broker_id = intval($this->input->get('id'));
    $page = intval($this->input->get('page'));
    $size = intval($this->input->get('size'));
    $this->_init_pagination($page, $size);

    $this->load->model('broker_info_model');
    //select
    $sql_select = implode(',', array(
      '`id`', '`broker_id`', '`agency_id`', '`company_id`',
      '`block_id`', '`block_name`', '`title`', '`room`',
      '`hall`', '`toilet`', '`buildarea`', '`price`', '`rent_tag`',
      '`pic`', '`fitment`', '`status`'
    ));
    //where
    $where = '`broker_id` = ' . $broker_id;
    $where .= ' AND `status` = 1';
    //order
    $order = ' ORDER BY id DESC';
    //分页
    $paging = ' LIMIT ' . $this->_offset . ',' . $this->_limit;

    $sql = 'SELECT ' . $sql_select . ' FROM rent_house WHERE ' . $where . $order . $paging;
    $this->load->model('house_info_model');
    $this->house_info_model->set_tbl('rent_house');
    $rent_house = $this->house_info_model->get_all_by_sql($sql);

    $this->_total_count = $this->house_info_model->get_count_by_cond($where);
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
            //array_push($rent_tag_arr_name, $this->_config['rent_tag'][$val]);
          }
        }
      }
      $house['rent_tag'] = $rent_tag_arr_name;
      $house['fitment'] = $k['fitment'];
      $house['pic'] = $k['pic'];
      $house['status'] = $k['status'];

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

  //获取经纪人信息
  public function get_argent()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    $broker_id = intval($this->input->get('aid', TRUE));
    if ($city == '' || intval($broker_id) == 0) {
      $this->result(0, '参数不合法');
      return;
    }
    //经纪人基本信息类库
    $this->load->model('api_broker_model');
    $broker = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    if (is_full_array($broker)) {
      //查找公司名称
      $this->load->model('agency_model');
      $company = $this->agency_model->get_by_id($broker['company_id']);
      $this->load->model('api_broker_sincere_model');
      //获取经纪人的信用等级
      $level = $this->api_broker_sincere_model->get_level_id_by_trust($broker['trust']);
      //好评率
      $good_avg_rate = $this->api_broker_sincere_model->good_avg_rate($broker_id);
      $good_avg_status = -1;
      if ($good_avg_rate['good_rate'] >= 0) {
        if ($good_avg_rate['good_rate_avg_high'] >= 0) {
          $good_avg_status = 1;
        } else if ($good_avg_rate['good_rate_avg_high'] < 0) {
          $good_avg_status = 2;
        }
      }
      if (!empty($good_avg_rate['good_rate']) && $good_avg_rate['good_rate'] != '') {
        $good_avg_rate['good_rate'] = $good_avg_rate['good_rate'] . '%';
      } else {
        $good_avg_rate['good_rate'] = '--%';
      }
      $praised = array(
        'title' => $good_avg_rate['good_rate'],
        'status' => $good_avg_status,
        'rose' => abs($good_avg_rate['good_rate_avg_high']) . '%'
      );
      //合作成功率 合作成功率平均值
      $this->load->model('cooperate_suc_ratio_base_model');
      $avg_cop_suc_ratio = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
      $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id);
      if ($cop_succ_ratio_info['cop_succ_ratio'] == '') {
        $cop_succ_ratio_info['cop_succ_ratio'] = '--%';
      } else {
        $cop_succ_ratio_info['cop_succ_ratio'] = $cop_succ_ratio_info['cop_succ_ratio'] . '%';
      }
      $cop_avg_status = -1;
      if ($cop_succ_ratio_info['cop_succ_ratio'] > 0) {
        $n = $avg_cop_suc_ratio > 0 ? round(($cop_succ_ratio_info['cop_succ_ratio'] - $avg_cop_suc_ratio) / $avg_cop_suc_ratio, 2) : 0;
        if ($n >= 0) {
          $cop_avg_status = 1;
        } else if ($n < 0) {
          $cop_avg_status = 2;
        }
      } else {
        $n = 0;
      }
      $cpsucceed = array(
        'title' => $cop_succ_ratio_info['cop_succ_ratio'],
        'status' => $good_avg_status,
        'rose' => abs($n) . '%'
      );
      //动态评分值
      $appraise_and_avg = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);
      $appraise_info = $this->api_broker_sincere_model->get_appraise_info($broker_id);
      for ($i = 1; $i <= 5; $i++) {
        //信息真实度
        if ($appraise_info['infomation_sum'] != 0) {
          $appraise_info['infomation'][$i]['info_percent'] = round($appraise_info['infomation'][$i]['count'] / $appraise_info['infomation_sum'] * 100);
        } else {
          $appraise_info['infomation'][$i]['info_percent'] = 0;
        }
        //态度满意度
        if ($appraise_info['attitude_sum'] != 0) {
          $appraise_info['attitude'][$i]['info_percent'] = round($appraise_info['attitude'][$i]['count'] / $appraise_info['attitude_sum'] * 100);
        } else {
          $appraise_info['attitude'][$i]['info_percent'] = 0;
        }
        //业务专业度
        if ($appraise_info['business_sum'] != 0) {
          $appraise_info['business'][$i]['info_percent'] = round($appraise_info['business'][$i]['count'] / $appraise_info['business_sum'] * 100);
        } else {
          $appraise_info['business'][$i]['info_percent'] = 0;
        }
      }
      $information = array(
        'title' => $appraise_and_avg['infomation']['score'],
        'staus' => $appraise_and_avg['infomation']['rate'] > 0 ? 1 : 2,
        'rose' => $appraise_and_avg['infomation']['avg'] . '%',
        'count' => $appraise_info['infomation_sum'],
        'fen1' => $appraise_info['infomation'][1]['info_percent'] . '%',
        'fen2' => $appraise_info['infomation'][2]['info_percent'] . '%',
        'fen3' => $appraise_info['infomation'][3]['info_percent'] . '%',
        'fen4' => $appraise_info['infomation'][4]['info_percent'] . '%',
        'fen5' => $appraise_info['infomation'][5]['info_percent'] . '%',
      );
      $attitude = array(
        'title' => $appraise_and_avg['attitude']['score'],
        'staus' => $appraise_and_avg['attitude']['rate'] > 0 ? 1 : 2,
        'rose' => $appraise_and_avg['attitude']['avg'] . '%',
        'count' => $appraise_info['attitude_sum'],
        'fen1' => $appraise_info['attitude'][1]['info_percent'] . '%',
        'fen2' => $appraise_info['attitude'][2]['info_percent'] . '%',
        'fen3' => $appraise_info['attitude'][3]['info_percent'] . '%',
        'fen4' => $appraise_info['attitude'][4]['info_percent'] . '%',
        'fen5' => $appraise_info['attitude'][5]['info_percent'] . '%',
      );
      $business = array(
        'title' => $appraise_and_avg['business']['score'],
        'staus' => $appraise_and_avg['business']['rate'] > 0 ? 1 : 2,
        'rose' => $appraise_and_avg['business']['avg'] . '%',
        'count' => $appraise_info['business_sum'],
        'fen1' => $appraise_info['business'][1]['info_percent'] . '%',
        'fen2' => $appraise_info['business'][2]['info_percent'] . '%',
        'fen3' => $appraise_info['business'][3]['info_percent'] . '%',
        'fen4' => $appraise_info['business'][4]['info_percent'] . '%',
        'fen5' => $appraise_info['business'][5]['info_percent'] . '%',
      );
      //获取经纪人评价的总和
      $this->load->model('sincere_appraise_cooperate_model');
      $where = 'broker_id = ' . $broker_id;
      $appraise_cooperate_count = $this->sincere_appraise_cooperate_model->count_by($where);
      //返回结果
      $result = array(
        'id' => $broker_id, 'name' => $broker['truename'],
        'image' => $broker['photo'], 'phone' => $broker['phone'],
        'certification' => $broker['ident_auth'] . ',' . $broker['quali_auth'],
        'company' => $company['name'], 'store' => $broker['agency_name'],
        'integrity' => $level, 'praised' => $praised, 'cpsucceed' => $cpsucceed,
        'information' => $information, 'attitude' => $attitude,
        'business' => $business, 'assessnum' => $appraise_cooperate_count
      );
      $this->result(1, '查询成功', $result);
    } else {
      $this->result(0, '查无经纪人');
    }
  }

  //获取经纪人评价
  public function feedback_list()
  {
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    $broker_id = intval($this->input->get('aid', TRUE));
    $type = intval($this->input->get('type', TRUE));
    if ($city == '' || intval($broker_id) == 0) {
      $this->result(0, '参数不合法');
      return;
    }
    //设置城市参数
    $where = 'broker_id = ' . $broker_id;
    $new_appraise_cooperate = array(
      'totalnum' => array('good' => 0, 'medium' => 0, 'bad' => 0, 'total' => 0),
      'list' => array());

    $this->load->model('sincere_appraise_cooperate_model');
    $good = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 1');
    $medium = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 2');
    $bad = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 3');
    $total = $good + $medium + $bad;
    $new_appraise_cooperate['totalnum'] = array(
      'good' => $good, 'medium' => $medium, 'bad' => $bad, 'total' => $total
    );
    if ($type != 0) {
      $where .= ' and trust_type_id = ' . $type;
    }
    $page = intval($this->input->get('page', TRUE));
    $pagesize = intval($this->input->get('pagesize', TRUE));
    $page = isset($page) && $page ? intval($page) : 1; // 获取当前页数
    $this->_init_pagination($page, $pagesize);
    $appraise_cooperate = $this->sincere_appraise_cooperate_model->get_all_by($where, $this->_offset, $this->_limit);
    if (is_full_array($appraise_cooperate)) {
      $this->load->model('sincere_trust_config_model');
      $this->load->model('house_config_model');
      $house_config = $this->house_config_model->get_config();
      $config_info = $this->sincere_trust_config_model->get_config();
      //经纪人基本信息类库
      $this->load->model('api_broker_model');
      //print_r($cooperate_info);
      foreach ($appraise_cooperate as $key => $value) {
        $house_info = unserialize($value['house_info']);
        $fitment_name = $house_config['fitment'][$house_info['fitment']];
        $forward_name = $house_config['forward'][$house_info['forward']];
        $broker = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $unit = $house_info['tbl'] == 'sell' ? '万' : '元/月';
        $cooperate_house = $house_info['districtname'] . '-' . $house_info['streetname'] . ' '
          . $house_info['blockname'] . ' ' . $house_info['room'] . '室'
          . $house_info['hall'] . '厅' . $house_info['toilet'] . '卫 '
          . $fitment_name . ' ' . $forward_name . ' ' . strip_end_0($house_info['buildarea']) . ' ㎡ '
          . strip_end_0($house_info['price']) . $unit;
        $new_appraise_cooperate['list'][$key] = array(
          'id' => $value['broker_id'], 'name' => $broker['truename'],
          'image' => $broker['photo'], 'shopName' => $broker['agency_name'],
          'information' => $value['infomation'], 'attitude' => $value['attitude'],
          'business' => $value['business'], 'content' => $value['content'],
          'cooperateHouse' => $cooperate_house, 'datetime' => $value['create_time'],

        );
      }
    }
    $this->result(1, '查询成功', $new_appraise_cooperate);
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
}
