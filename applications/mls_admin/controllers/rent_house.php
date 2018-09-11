<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 出租成交房源
 *
 * @package     mls_admin
 * @subpackage  Controllers
 * @category    Controllers
 * @author      angel_in_us
 */
class Rent_house extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('cooperate_model');
    $this->load->model('rent_house_model');
    $this->load->helper('user_helper');
    $this->load->library('Curl');
    // $this->load->library('Pinganfang');
  }

  /**
   * 出租房源列表
   * date   : 2015-01-26
   * author : angel_in_us
   */
  public function index()
  {
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();
    $this->load->model('rent_house_model');
    $data['district'] = $this->rent_house_model->community_info('');

    $data['title'] = '出租房源列表';
    $data['conf_where'] = 'index';

    //筛选条件
      $where = " status != 5 and agency_id > 0";
    $post_param = $this->input->post(NULL, TRUE);
    if (!empty($post_param['id'])) {
      $where .= ' and id = ' . $post_param['id'];
    }
    if (!empty($post_param['order_sn'])) {
      $where .= ' and order_sn = ' . $post_param['order_sn'];
    }

    if ($post_param['isshare'] == 1) {
      $where .= ' and isshare = 1';
      $post_param['status'] = 1;
    }

    if (!empty($post_param['status'])) {
      $where .= ' and status = ' . $post_param['status'];
    }
    if (!empty($post_param['nature'])) {
      $where .= ' and nature = ' . $post_param['nature'];
    }
    if (!empty($post_param['sell_type'])) {
      $where .= ' and sell_type = ' . $post_param['sell_type'];
    }
    if (!empty($post_param['district_id'])) {
      $where .= ' and district_id = ' . $post_param['district_id'];
      $this->load->model('district_model');
      $data['street'] = $this->district_model->get_street_bydist($post_param['district_id']);
    }
    if (!empty($post_param['street'])) {
      $where .= ' and street_id = ' . $post_param['street'];
    }
    if (!empty($post_param['block_name'])) {
      $where .= ' and block_name = "' . $post_param['block_name'] . '"';
    }
    if (!empty($post_param['room'])) {
      $where .= ' and room = "' . $post_param['room'] . '"';
    }

    if (!empty($post_param['min_area']) && !empty($post_param['max_area']) && ($post_param['max_area'] > $post_param['min_area'])) {
      $where .= ' and buildarea between ' . $post_param['min_area'] . ' and ' . $post_param['max_area'];
    }
    if (!empty($post_param['min_price']) && !empty($post_param['max_price']) && ($post_param['max_price'] > $post_param['min_price'])) {
      $where .= ' and price between ' . $post_param['min_price'] . ' and ' . $post_param['max_price'];
    }
      //是否为推荐房源（金品app）
      if ($post_param['recommend_house'] == 1) {
          $where .= ' and recommend_house_id > 0 ';
      } else if ($post_param['recommend_house'] == 2) {
          $where .= ' and recommend_house_id < 1 ';
      }
      //是否为喜欢房源（金品app）
      if ($post_param['is_like_house'] == 1) {
          $where .= ' and is_set_like = 1 ';
      } else if ($post_param['is_like_house'] == 2) {
          $where .= ' and is_set_like = 0 ';
      }

    $company_id = $this->input->post('company_id');
    $company_name = $this->input->post('company_name');
    $agency_id = $this->input->post('agency_id');
    if ($company_id || $agency_id) {
      $this->load->model('agency_model');
      $agencys = $this->agency_model->get_children_by_company_id($company_id);
    }
    if ($agency_id) {
      $where .= ' and agency_id = ' . $agency_id;
      $data['agencys'] = $agencys;
    } else if ($company_id) {
      if (is_full_array($agencys)) {
        $agency_id = array();
        foreach ($agencys as $v) {
          $agency_id[] = $v['id'];
        }
        $agency_ids = implode(',', $agency_id);
        $where .= ' and agency_id in(' . $agency_ids . ')';
      }
    }
    if (!empty($post_param['search_value'])) {
      if (!empty($post_param['type'])) {
        if ($post_param['type'] == 'broker_id') {
          $where .= ' and broker_id = "' . $post_param['search_value'] . '"';
        } elseif ($post_param['type'] == 'broker_name') {
          $where .= ' and broker_name = "' . $post_param['search_value'] . '"';
        } elseif ($post_param['type'] == 'phone') {
          $cond_where['phone'] = $post_param['search_value'];
          $result = $this->rent_house_model->broker($cond_where);
          $where .= ' and broker_id = "' . $result[0]['broker_id'] . '"';
        }
      }
    }
    if (!empty($post_param['order_type'])) {
      $order_key = $post_param['order_type'];
    } else {
      $order_key = 'createtime';
    }
    if (!empty($post_param['order_value'])) {
      $order_by = $post_param['order_value'];
    } else {
      $order_by = 'DESC';
    }

    if (!empty($post_param['pages'])) {
      $pagesize = $post_param['pages'];
    } else {
      $pagesize = 10;
    }

    //清除条件头尾多余的“AND”和空格
    $where = trim($where);
    $where = trim($where, "and");
    $where = trim($where);

    $data['post_param'] = $post_param;
    //分页开始
    $data['sold_num'] = $this->rent_house_model->get_rent_house_num_by_cond($where);

    $data['pagesize'] = $pagesize; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($post_param['pg']) ? intval($post_param['pg']) : 1; // 获取当前页数

    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['sell_list'] = $this->rent_house_model->get_list_by_cond($where, $data['offset'], $data['pagesize'], $order_key, $order_by);
    //echo '<pre>';print_r($data['sell_list']);die;
    //如果查询到成交数据
    if (count($data['sell_list']) >= 1) {
      foreach ($data['sell_list'] as $key => $val) {
        //取小数点后2位
        if ($val['price_danwei'] > 0) {
          $xiaoshu = substr((($val['price'] / $val['buildarea'] / 30) - intval($val['price'] / $val['buildarea'] / 30)), 0, 4);
          $v = intval($val['price'] / $val['buildarea'] / 30) + $xiaoshu;
          $data['sell_list'][$key]['price_buildarea'] = $v;
        }
        $cond_where = 'a.broker_id = ' . $val['broker_id'];
        $result = $this->rent_house_model->broker_info($cond_where);

        //$data['sell_list'][$key]['buildarea'] = substr($val['buildarea'],);
        $data['sell_list'][$key]['agency_name'] = $result[0]['name'];
        $data['sell_list'][$key]['phone'] = $result[0]['phone'];
        $data['sell_list'][$key]['status'] = $data['config']['status'][$val['status']];
        $data['sell_list'][$key]['nature'] = $data['config']['nature'][$val['nature']];
        $data['sell_list'][$key]['sell_type'] = isset($data['config']['sell_type'][$val['sell_type']]) ? $data['config']['sell_type'][$val['sell_type']] : '';
        $data['sell_list'][$key]['fitment'] = $data['config']['fitment'][$val['fitment']];
      }
    } else {
      $data['sell_list'] = array();
    }

    $this->load->view('rent_house/rent_list', $data);
  }

  public function kft($house_id = 0, $price = 0)
  {
    $submit_flag = $this->input->post('submit_flag');
    //城市id
    $city_id = $_SESSION['esfdatacenter']['city_id'];
    $data_view['price'] = $price;
    if ($submit_flag == 'add') {
      $this->load->library('form_validation');//表单验证
      $this->form_validation->set_rules('commission_price', 'commission_price', 'required');
      //获取参数
      $commission_source = intval($this->input->post('commission_source'));
      $commission_price = floatval($this->input->post('commission_price'));
      $commission_ratio = floatval($this->input->post('commission_ratio'));
      $look = intval($this->input->post('look'));
      $signed = intval($this->input->post('signed'));

      if ($this->form_validation->run() === true || $commission_source == 2) {
        $this->load->model('cooperate_broker_model');
        $this->load->model('broker_info_model');
        $this->load->model('agency_model');
        $this->load->model('auth_review_model');
        //获得房源数据
        if (isset($house_id) && intval($house_id) > 0) {
          $arr = array('id' => intval($house_id));
          $where_in = array('id', $arr);
          $house_info = $this->rent_house_model->get_house_info_byids($where_in);
          if (is_full_array($house_info)) {
            //是否已同步判断
            if ('0' == $house_info[0]['is_kft']) {
              //经纪人数据处理
              $broker_id = intval($house_info[0]['broker_id']);
              $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);
              //头像处理成小图
              if (!empty($broker_info['photo'])) {
                $broker_photo_arr = explode('/', $broker_info['photo']);
                if (is_full_array($broker_photo_arr)) {
                  $broker_info['photo'] = '';
                  foreach ($broker_photo_arr as $key => $value) {
                    if ($key == count($broker_photo_arr) - 1) {
                      $broker_info['photo'] .= 'thumb' . '/';
                    }
                    $broker_info['photo'] .= $value . '/';
                  }
                }
              }
              $broker_info['photo'] = trim($broker_info['photo'], '/');
              if (is_full_array($broker_info)) {
                $phone = $broker_info['phone'];
                $url = MLS_ADMIN_URL . '/collect/phone_check/' . $phone;
                $check_result = $this->curl->vget($url, '');
                $check_obj = json_decode($check_result);
                $check_arr = $this->pinganfang->object_to_array($check_obj);

                //是否存在经纪人
                $kft_broker_id = 0;
                if (!($check_arr['status'] == '1')) {
                  //获得公司门店名
                  $company_id = intval($broker_info['company_id']);
                  $agency_id = intval($broker_info['agency_id']);
                  $company_data = $this->agency_model->get_by_id($company_id);
                  $agency_data = $this->agency_model->get_by_id($agency_id);
                  $company_name = '';
                  if (is_full_array($company_data)) {
                    $company_name = $company_data['name'];
                  }
                  $agency_name = '';
                  if (is_full_array($agency_data)) {
                    $agency_name = $agency_data['name'];
                  }
                  //身份证、个人名片
                  $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id, 0, 1);
                  //图片url处理换成大图
                  $photo_str_2 = '';
                  if (!empty($ident_info['photo2'])) {
                    $photo_str_2 = changepic($ident_info['photo2']);
                  }
                  $photo_str_3 = '';
                  if (!empty($ident_info['photo3'])) {
                    $photo_str = '';
                    $photo_str_3 = changepic($ident_info['photo3']);
                  }

                  $add_broker_data = array(
                    'phone' => $phone,
                    //'username' => $broker_info['truename'],
                    'idno' => $broker_info['idno'],
                    //'company_name' => $company_name,
                    //'agency_name' => $agency_name,
                    'is_auth' => 1,
                    //'photo' => $broker_info['photo'],//头像
                    //'idno_face' => $photo_str_2,
                    //'idno_back' => $photo_str_3,
                    'status' => 1,
                    'register_time' => time(),
                    'auth_time' => time(),
                    'city_id' => $city_id,
                    'register_city_id' => $city_id
                  );
                  //经纪人入库
                  $url = MLS_ADMIN_URL . '/collect/add_broker/';
                  $this->curl->vpost($url, $add_broker_data);
                  //获得经纪人id
                  $url = MLS_ADMIN_URL . '/collect/phone_check/' . $phone;
                  $broker_result = $this->curl->vget($url, '');
                  $broker_obj = json_decode($broker_result);
                  //$broker_arr = $this->pinganfang->object_to_array($broker_obj);
                  //if(is_full_array($broker_arr)){
                  //    $kft_broker_id = intval($broker_arr['broker_data']['id']);
                  // }
                } else {
                  if (is_full_array($check_arr['broker_data'])) {
                    $kft_broker_id = intval($check_arr['broker_data']['id']);
                  }
                }
                //房源数据处理，获得房源数据
                //室内图、户型图
                $pic_ids = $house_info[0]['pic_ids'];
                $pic_id_arr = explode(',', trim($pic_ids, ','));
                if (is_full_array($pic_id_arr)) {
                  $this->load->model('pic_model');
                  $pid_data = $this->pic_model->find_house_pic_by_ids('upload', $pic_ids);
                }
                $pic_str_1 = '';
                $pic_str_2 = '';
                if (isset($pid_data) && is_full_array($pid_data)) {
                  $pic_type_1 = '';
                  $pic_type_2 = '';
                  foreach ($pid_data as $key => $value) {
                    if (1 == $value['type']) {
                      $pic_type_1 .= $value['url'] . ',';
                      $pic_str_1 = trim($pic_type_1, ',');
                    }
                    if (2 == $value['type']) {
                      $pic_type_2 .= $value['url'] . ',';
                      $pic_str_2 = trim($pic_type_2, ',');
                    }
                  }
                }

                $rent_house_data = array(
                  'cooperate_type' => 2,
                  'block_name' => $house_info[0]['block_name'],
                  'block_id' => $house_info[0]['block_id'],
                  'district_id' => $house_info[0]['district_id'],
                  'street_id' => $house_info[0]['street_id'],
                  'price' => $house_info[0]['price'],
                  'area' => $house_info[0]['buildarea'],
                  'room' => $house_info[0]['room'],
                  'hall' => $house_info[0]['hall'],
                  'toilet' => $house_info[0]['toilet'],
                  'floor' => $house_info[0]['floor'],
                  'totalfloor' => $house_info[0]['totalfloor'],
                  'fitment' => $house_info[0]['fitment'],
                  'tag' => $house_info[0]['rent_tag'],
                  'remark' => $house_info[0]['remark'],
                  'pic1' => $pic_str_1,
                  'pic2' => $pic_str_2,
                  'commission_source' => $commission_source,
                  'commission_price' => $commission_price,
                  'commission_ratio' => $commission_ratio,
                  'look' => $look,
                  'signed' => $signed,
                  'create_time' => time(),
                  'broker_id' => $kft_broker_id,
                  'city_id' => $city_id,
                  'is_share' => 1,
                );

                if ($commission_source != 2 && (empty($rent_house_data['commission_ratio']) || empty($rent_house_data['commission_price']))) {
                  $data_view['mess_error'] = '佣金比例不能为空';
                } else {
                  if ($commission_source != 2 && $rent_house_data['price'] < $rent_house_data['commission_price']) {
                    $rent_house_data['commission_price'] = $rent_house_data['price'] * $rent_house_data['commission_ratio'] * 0.01;
                  }

                  //房源类型
                  if (in_array($house_info[0]['sell_type'], array('1', '2', '3', '4'))) {
                    $rent_house_data['property_type'] = $house_info[0]['sell_type'];
                  } else {
                    $rent_house_data['property_type'] = '1';
                  }

                  $url = MLS_ADMIN_URL . '/collect/add_house/';
                  $add_house_result = $this->curl->vpost($url, $rent_house_data);
                  if (isset($add_house_result) && !empty($add_house_result)) {
                    $data_view['addResult'] = $add_house_result;
                    //是否同步字段修改
                    $update_arr = array('is_kft' => 1);
                    $where_cond = array('id' => intval($house_id));
                    $this->rent_house_model->update_house($update_arr, $where_cond);
                  }
                }

              } else {
                $data_view['mess_error'] = '房源经纪人数据错误';
              }
            } else {
              $data_view['mess_error'] = '该房源已同步';
            }

          }
        }
      } else {
        $data_view['mess_error'] = '带 * 为必填字段';
      }
    }

    $this->load->view('rent_house/kft', $data_view);
  }

  /**
   * 页面ajax请求根据属区获得对应板块
   * @access  public
   * @param  int 区属id
   * @return  array
   */
  public function find_street_bydis($districtID)
  {
    //加载区属模型类
    $this->load->model('district_model');
    if (!empty($districtID)) {
      $districtID = intval($districtID);
      $street = $this->district_model->get_street_bydist($districtID);
      echo json_encode($street);
    } else {
      echo json_encode(array('result' => 'no result'));
    }
  }


  public function house_detail($house_id)
  {
    //加载出租基本配置MODEL
    $this->load->model('house_config_model');
    //房源佣金分成数据
    $this->load->model('sell_house_share_ratio_model');
    $data['ratio_info'] = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
    //获取出租信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();
    $data['title'] = '出租房源详情';
    $data['conf_where'] = 'index';

    $arr = array('id' => $house_id);
    $where_in = array('id', $arr);
    $house_info = $this->rent_house_model->get_house_info_byids($where_in);
    $this->load->model('community_model');
    $cmt_info = $this->community_model->auto_cmtname($house_info[0]['block_name'], 10);
    $house_info[0]['districtname'] = $cmt_info[0]['districtname'];
    $house_info[0]['streetname'] = $cmt_info[0]['streetname'];
    //echo '<pre>';print_r($data['house_detail']['price_danwei']);die;
    //取小数点后2位
    if ($house_info[0]['price_danwei'] > 0) {
      $xu = substr((($house_info[0]['price'] / $house_info[0]['buildarea'] / 30) - intval($house_info[0]['price'] / $house_info[0]['buildarea'] / 30)), 0, 4);
      $v = intval($house_info[0]['price'] / $house_info[0]['buildarea'] / 30) + $xu;
      $house_info[0]['price_buildarea'] = $v;
      //print_r($house_info[0]['price_buildarea']);
    }
    $data['house_detail'] = $house_info[0];
    $this->load->view('rent_house/rent_house_detail', $data);
  }

  /**
   * 添加出租信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function add()
  {
    //添加出租信息
    $datainfo = array();
//        $broker_info = array();
//        $broker_info = $this->user_arr;
//        $broker_id = intval($broker_info['broker_id']);
//        $broker_name = strip_tags($broker_info['truename']);
//        $agency_id = intval($broker_info['agency_id']);
    if ($broker_id == 0) {
      //退出系统
    }

    $house_id = $this->input->post('house_id', TRUE);
    if (empty($house_id)) {
//            $datainfo['broker_id'] = $broker_id;
//            $datainfo['broker_name'] = $broker_name;
//            $datainfo['agency_id'] = $agency_id;
      $datainfo['createtime'] = time();
      $datainfo['ip'] = get_ip();
    }

    $datainfo['sell_type'] = $this->input->post('sell_type', TRUE);
    $datainfo['block_name'] = $this->input->post('block_name', TRUE);
    $datainfo['block_id'] = $this->input->post('block_id', TRUE);
    $datainfo['district_id'] = $this->input->post('district_id', TRUE);
    $datainfo['street_id'] = $this->input->post('street_id', TRUE);
    $datainfo['address'] = $this->input->post('address', TRUE);

    $datainfo['dong'] = $this->input->post('dong', TRUE);
    $datainfo['unit'] = $this->input->post('unit', TRUE);
    $datainfo['door'] = $this->input->post('door', TRUE);
    $datainfo['owner'] = $this->input->post('owner', TRUE);
    $datainfo['idcare'] = $this->input->post('idcare', TRUE);
    $datainfo['telno1'] = $this->input->post('telno1', TRUE);
    $datainfo['telno2'] = $this->input->post('telno2', TRUE);
    $datainfo['telno3'] = $this->input->post('telno3', TRUE);
    $datainfo['proof'] = $this->input->post('proof', TRUE);
    $datainfo['mound_num'] = $this->input->post('mound_num', TRUE);
    $datainfo['record_num'] = $this->input->post('record_num', TRUE);

    $datainfo['status'] = $this->input->post('status', TRUE);
    $datainfo['nature'] = $this->input->post('nature', TRUE);
    $datainfo['room'] = $this->input->post('room', TRUE);
    $datainfo['hall'] = $this->input->post('hall', TRUE);
    $datainfo['toilet'] = $this->input->post('toilet', TRUE);
    $datainfo['kitchen'] = $this->input->post('kitchen', TRUE);
    $datainfo['balcony'] = $this->input->post('balcony', TRUE);
    $datainfo['isshare'] = $this->input->post('isshare', TRUE);
    $datainfo['floor_type'] = $this->input->post('floor_type', TRUE);
    $datainfo['floor'] = $this->input->post('floor', TRUE);
    if ($datainfo['floor_type'] == 2) {
      $datainfo['floor'] = $this->input->post('floor2', TRUE);
    }
    $datainfo['subfloor'] = $this->input->post('subfloor', TRUE);
    $datainfo['totalfloor'] = $this->input->post('totalfloor', TRUE);
    $datainfo['forward'] = $this->input->post('forward', TRUE);
    $datainfo['fitment'] = $this->input->post('fitment', TRUE);
    $datainfo['buildyear'] = $this->input->post('buildyear', TRUE);
    $datainfo['buildarea'] = $this->input->post('buildarea', TRUE);
    $datainfo['price'] = $this->input->post('price', TRUE);
    $datainfo['lowprice'] = $this->input->post('lowprice', TRUE);
    $datainfo['keys'] = $this->input->post('keys', TRUE);
    $datainfo['key_number'] = $this->input->post('key_number', TRUE);
    //$datainfo['pact'] = $this->input->post('pact' , TRUE);
    $datainfo['rententrust'] = $this->input->post('rententrust', TRUE);

    $datainfo['house_type'] = $this->input->post('house_type', TRUE);
    //$datainfo['struct'] = $this->input->post('struct' , TRUE);
    $datainfo['rentpaytype'] = $this->input->post('rentpaytype', TRUE);
    $datainfo['property'] = $this->input->post('property', TRUE);
    //$datainfo['rebate_type'] = $this->input->post('rebate_type' , TRUE);
    //$datainfo['look'] = $this->input->post('look' , TRUE);
    $datainfo['current'] = $this->input->post('current', TRUE);
    $datainfo['infofrom'] = $this->input->post('infofrom', TRUE);
    //$datainfo['paperwork'] = $this->input->post('paperwork' , TRUE);
    $datainfo['renttime'] = $this->input->post('renttime', TRUE);
    $datainfo['deposit'] = $this->input->post('deposit', TRUE);

    //佣金分配相关数据
    $datainfo['a_ratio'] = $this->input->post('a_ratio', TRUE);
    $datainfo['b_ratio'] = $this->input->post('b_ratio', TRUE);
    $datainfo['buyer_ratio'] = $this->input->post('buyer_ratio', TRUE);
    $datainfo['seller_ratio'] = $this->input->post('seller_ratio', TRUE);
    $this->load->model('sell_house_share_ratio_model');
    $this->sell_house_share_ratio_model->update_house_ratio_by_rowid($house_id,
      $datainfo['seller_ratio'], $datainfo['buyer_ratio'], $datainfo['a_ratio'], $datainfo['b_ratio']);

    $equipment = $this->input->post('equipment', TRUE);
    if ($equipment) {
      $datainfo['equipment'] = implode(',', $equipment);
    }
    $setting = $this->input->post('setting', TRUE);
    if ($setting) {
      $datainfo['setting'] = implode(',', $setting);
    }
    $datainfo['strata_fee'] = $this->input->post('strata_fee', TRUE);
    $datainfo['costs_type'] = $this->input->post('costs_type', TRUE);
    $datainfo['pay_date'] = $this->input->post('pay_date', TRUE);
    $datainfo['remark'] = $this->input->post('remark', TRUE);
    $datainfo['updatetime'] = time();

    //别墅
    if ($datainfo['sell_type'] == 2) {
      $datainfo['villa_type'] = $this->input->post('villa_type', TRUE);
      $datainfo['hall_struct'] = $this->input->post('hall_struct', TRUE);
      $datainfo['park_num'] = $this->input->post('park_num', TRUE);
      $datainfo['garden_area'] = $this->input->post('garden_area', TRUE);
      $datainfo['floor_area'] = $this->input->post('floor_area', TRUE);
      $datainfo['light_type'] = $this->input->post('light_type', TRUE);
    }
    //商铺
    if ($datainfo['sell_type'] == 3) {
      $datainfo['shop_type'] = $this->input->post('shop_type', TRUE);
      $shop_trade = $this->input->post('shop_trade', TRUE);
      if ($shop_trade) {
        $datainfo['shop_trade'] = implode(',', $shop_trade);
      }
      $datainfo['division'] = $this->input->post('division', TRUE);
    }
    //写字楼
    if ($datainfo['sell_type'] == 4) {
      $datainfo['division'] = $this->input->post('division2', TRUE);
      $datainfo['office_trade'] = $this->input->post('office_trade', TRUE);
      $datainfo['office_type'] = $this->input->post('office_type', TRUE);
    }

    //加载客户MODEL
    if (empty($house_id)) {
//            $house_id = $this->rent_house_model->add_rent_house_info($datainfo);
//
//            if($house_id > 0)
//            {
//                //添加钥匙
//                if($datainfo['keys'] && $datainfo['key_number']){
//                    $this->add_key($house_id,$datainfo['key_number']);
//                }
//				//添加房源日志录入
//                $this->load->model('follow_model');
//		        $needarr=array();
//				$needarr['broker_id']=$broker_id;
//				$needarr['type']=2;
//				$needarr['house_id']=$house_id;
//				$bool=$this->follow_model->house_inster($needarr);
//                $url_manage = '/rent/lists/';
//                $page_text = '发布成功';
//            }
//            else
//            {
//                $url_manage = '/rent/lists/';
//                $page_text = '发布失败';
//            }
    } else {
      $this->rent_house_model->set_id($house_id);
      $rent_backinfo = $this->rent_house_model->get_info_by_id();  //修改前的信息
      $result = $this->rent_house_model->update_info_by_id($datainfo);
      $rent_dataifno = $this->rent_house_model->get_info_by_id();  //修改过后信息

      //添加钥匙
      if (!$rent_backinfo['keys'] && $rent_dataifno['keys'] && $rent_dataifno['key_number']) {
        $this->add_key($house_id, $rent_dataifno['key_number']);
      }

      $rent_cont = $this->insetmatch($rent_backinfo, $rent_dataifno);


      //修改房源日志录入
      $this->load->model('follow_model');
      $needarrt = array();
//			$needarrt['borker_id']=$broker_id;
      $needarrt['type'] = 2;
      $needarrt['house_id'] = $house_id;
      $needarrt['text'] = $rent_cont;
      $boolt = $this->follow_model->house_save($needarrt);
      $refer = $this->input->post('refer', TRUE);
      $pos = strpos($refer, 'group_publish');
      if ($pos) {
        $url_manage = $refer;
      } else {
        $url_manage = '/rent_house/index';
      }
      if ($result) {
        $page_text = "修改成功";
      } else {
        $page_text = "修改失败";
      }
    }

    if ($house_id > 0) {
      $pics = $picinfo = array();
      $pics['p_filename2'] = $this->input->post('p_filename2', TRUE);
      $pics['add_pic2'] = $this->input->post('add_pic2', TRUE);
      $pics['p_filename1'] = $this->input->post('p_filename1', TRUE);
      $pics['add_pic1'] = $this->input->post('add_pic1', TRUE);
      $this->rent_house_model->insert_house_pic($pics, 'rent_house', $house_id, $datainfo['block_id'], 1);

      if ($pics['add_pic2']) {
        $picinfo['pic'] = $pics['add_pic2'];
        $this->rent_house_model->update_info_by_id($picinfo);
      } elseif ($pics['p_filename2']) {
        $picinfo['pic'] = $pics['p_filename2'][0];
        $this->rent_house_model->update_info_by_id($picinfo);
      }
    }

    $this->jump($url_manage, $page_text, 3000);

  }

//        /**
//	* 根据合作 id 查看房源详情
//	* date   : 2015-01-27
//	* author : angel_in_us
//	*/
//	public function house_detail($id,$type)
//	{
//
//		//加载出租基本配置MODEL
//                $this->load->model('house_config_model');
//                //获取出租信息基本配置资料
//                $house_config = $this->house_config_model->get_config();
//
//		$data['title'] = '用户数据中心欢迎你';
//		$data['conf_where'] = 'index';
//
//		//控制标题输出
//		if($type=='sell'){
//			$data['type'] = '二手房';
//		}else if($type=='rent'){
//			$data['type'] = '租房';
//		}
//
//		$arr = array('id'=>$id);
//		$where_in = array('id',$arr);
//		$house_info = $this->cooperation_model->get_house_info_byids($where_in);
//		//echo "<pre>";print_r($house_info);die;
//
//		$house_detail = unserialize($house_info[0]['house']);
//		$house_detail['fitment'] = $house_config['fitment'][$house_detail['fitment']];
//		$house_detail['forward'] = $house_config['forward'][$house_detail['forward']];
//
//		$data['house_detail'] = $house_detail;
//
//		$this->load->view('sold_list/rent_house_detail',$data);
//	}

  /**
   * 根据合作 id 查询合作信息
   * date   : 2015-01-27
   * author : angel_in_us
   */
  public function cooperation_detail($id)
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';

    $arr = array('id' => $id);
    $where_in = array('id', $arr);
    $cooperation_info = $this->cooperation_model->get_cooperation_byids($where_in);
    $cooperation_detail = $cooperation_info[0];
    //echo "<pre>";print_r($cooperation_detail);die;

    $data['detail'] = $cooperation_detail;

    $this->load->view('sold_list/cooperation_detail', $data);
  }

  /**
   * 根据合作 id 查询合作信息
   * date   : 2015-01-27
   * author : angel_in_us
   */
  public function reward_real_price($id)
  {
    echo "<script>alert('页面尚未完成，请稍后重试~!');history.go(-1);</script>";
    die;
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';

    $arr = array('id' => $id);
    $where_in = array('id', $arr);
    $cooperation_info = $this->cooperation_model->get_cooperation_byids($where_in);
    $cooperation_detail = $cooperation_info[0];
    //echo "<pre>";print_r($cooperation_detail);die;

    $data['detail'] = $cooperation_detail;

    $this->load->view('sold_list/cooperation_detail', $data);
  }

  /**
   * 根据合作 id 关联失效
   * date   : 2015-01-27
   * author : No.one
   */
  public function del_sell($rent_id)
  {
    if (!empty($rent_id)) {
      $this->load->model('rent_house_model');
      $rs = $this->rent_house_model->del_sell($rent_id);

      if ($rs !== false) {
        echo "<script>alert('操作成功！');</script>";
      } else {
        echo "<script>alert('操作失败！');</script>";
      }
    }
    echo "<script>location.href='" . FREME_URL . "/rent_house/index';</script>";
  }

  /**
   * 根据合作 id 下架
   * date   : 2015-01-27
   * author : No.one
   */
  public function xiajia($rent_id)
  {
    if (!empty($rent_id)) {
      $this->load->model('rent_house_model');
      $rs = $this->rent_house_model->xiajia($rent_id);

      if ($rs !== false) {
        echo "<script>alert('操作成功！');</script>";
      } else {
        echo "<script>alert('操作失败！');</script>";
      }
    }
    echo "<script>location.href='" . FREME_URL . "/rent_house/index';</script>";
  }

  //修改房源的日志录入匹配
  public function insetmatch($backinfo, $datainfo)
  {
    //加载出租基本配置MODEL
    $this->load->model('house_config_model');
    //获取出租信息基本配置资料
    $config = $this->house_config_model->get_config();
    //加载区属模型类
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

    $constr = '';
    foreach ($backinfo as $key => $val) {
      if ($val != $datainfo[$key]) {
        switch ($key) {
          case 'sell_type':
            $constr .= '物业类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'dong':
            $constr .= '栋座:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'block_name':
            $constr .= '小区名字:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'district_id':
            $constr .= '区属：' . $dis[$val]['district'] . '>>' . $dis[$datainfo[$key]]['district'] . ',';
            break;

          case 'street_id':
            $constr .= '板块：' . $stred[$val]['streetname'] . '>>' . $stred[$datainfo[$key]]['streetname'] . ',';
            break;

          case 'address':
            $constr .= '地址:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'unit':
            $constr .= '单元:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'door':
            $constr .= '门牌:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'owner':
            $constr .= '业主姓名:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'idcare':
            $constr .= '身份证:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'telno1':
            $constr .= '电话1:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'telno2':
            $constr .= '电话2:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'telno3':
            $constr .= '电话3:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'proof':
            $constr .= '证书号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'mound_num':
            $constr .= '丘地号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'record_num':
            $constr .= '备案号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'status':
            $constr .= '状态:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'nature':
            $constr .= '房源性质:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'room':
            $constr .= '室:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'hall':
            $constr .= '厅:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'toilet':
            $constr .= '卫:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'balcony':
            $constr .= '阳台:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'floor_type':
            if ($val == 1) {
              $val = '单层';
            } else {
              $val = '跃层';
            }
            if ($datainfo[$key] == 1) {
              $datainfo[$key] = '单层';

            } else {
              $datainfo[$key] = '跃层';
            }
            $constr .= '楼层类型:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'floor':
            $constr .= '楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'totalfloor':
            $constr .= '总楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'forward':
            $constr .= '朝向:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'totalfloor':
            $constr .= '总楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'fitment':
            $constr .= '装修:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'buildarea':
            $constr .= '面积:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'buildyear':
            $constr .= '房龄:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'price':
            $constr .= '售价:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'avgprice':
            $constr .= '单价:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'taxes':
            $constr .= '税费:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'keys':
            $constr .= '钥匙:' . $config['look'][$val] . '>>' . $config['look'][$datainfo[$key]] . ',';
            break;

          case 'key_number':
            $constr .= '钥匙编号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'division':
            if ($val == 1) {
              $ver = '是';
            } elseif ($val == 2) {
              $ver = '否';
            }
            if ($datainfo[$key] == 1) {
              $data = '是';
            } elseif ($datainfo[$key] == 2) {
              $data = '否';
            }
            $constr .= '是否分割:' . $ver . '>>' . $data . ',';
            break;

          case 'property':
            $constr .= '产权:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'current':
            $constr .= '状态:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'infofrom':
            $constr .= '信息来源:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'villa_type':
            $constr .= '别墅类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'remark':
            $constr .= '备注:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'deposit':
            $constr .= '租金:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'lowprice':
            $constr .= '最低租金:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'house_type':
            $constr .= '住宅类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'rentpaytype':
            $constr .= '付款方式:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'equipment':
            $gg = explode(',', $val);
            $tt = explode(',', $datainfo[$key]);
            $constr .= '房屋设施:';
            foreach ($gg as $keyy) {
              $constr .= $config[$key][$keyy] . ',';
            }
            $constr .= '>>';
            foreach ($tt as $tty) {
              $constr .= $config[$key][$tty] . ',';
            }
            break;

          case 'strata_fee':
            if (empty($val)) {
              $val = '空';
            }
            if ($datainfo[$key]) {
              $datainfo[$key] = '空';
            }
            $constr .= '物业费:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'costs_type':
            if ($val == 1) {
              $val = '元/月/㎡';
            } else {
              $val = '元/月';
            }
            if ($datainfo[$key] == 1) {
              $datainfo[$key] = '元/月/㎡';
            } else {
              $datainfo[$key] = '元/月';
            }
            $constr .= '物业费类型:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'setting':
            $gg = explode(',', $val);
            $tt = explode(',', $datainfo[$key]);
            $constr .= '周边配套:';
            foreach ($gg as $keyy) {
              $constr .= $config[$key][$keyy] . ',';
            }
            $constr .= '>>';
            foreach ($tt as $tty) {
              $constr .= $config[$key][$tty] . ',';
            }
            break;
        }
      }
    }
    return $constr;
  }

  /**
   * 查看跟进
   */
  public function follow($id)
  {

    $data['title'] = "出租房源跟进信息";
    $data['conf_where'] = 'index';
    //type=2 在数据库表示的是出租房源跟进信息
    $this->load->model('rent_house_model');
    $condition = "a.type = 2 AND b.id = " . $id;

    $data['follows'] = $this->rent_house_model->get_follows_by_cond($condition);

    $this->load->view("rent_house/follow", $data);
  }

  //echo "<pre>";print_r($house_detail);die;
  //初始化合作房源设置合作时间，同创建时间
  public function set_share_time()
  {
    $this->load->model('rent_house_model');
    $where_cond = 'select id,isshare,set_share_time,createtime from rent_house where isshare = 1 and set_share_time = 0';
    $house_list = $this->rent_house_model->query($where_cond)->result_array();
    if (is_full_array($house_list)) {
      $update_arr = array();
      foreach ($house_list as $k => $v) {
        $update_arr['set_share_time'] = $v['createtime'];
        $update_result = $this->rent_house_model->update_house($update_arr, array('id' => $v['id']));
        echo 'id:' . $v['id'];
        var_dump($update_result);
        echo '<br>';
      }
    }
  }

  //根据户型图，室内图情况，定义房源等级字段，刷错误数据
  public function deal_house_level($page = 1)
  {
    $this->load->model('rent_house_model');
    $this->load->model('pic_model');
    //分页开始
    $data['sold_num'] = $this->rent_house_model->get_rent_house_num_by_cond($where);

    $data['pagesize'] = 1000; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($page) ? intval($page) : 1; // 获取当前页数

    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $this->rent_house_model->set_search_fields(array('id', 'pic_ids'));
    $sell_list = $this->rent_house_model->get_list_by_cond($where, $data['offset'], $data['pagesize']);
    foreach ($sell_list as $k => $v) {
      $house_level = 0;
      if (!empty($v['pic_ids'])) {
        $pic_ids = trim($v['pic_ids'], ',');
//                var_dump($v['id']);
//                var_dump($pic_ids);
//                echo '---<br>';

        $pid_data = $this->pic_model->find_house_pic_by_ids('upload', $pic_ids);
        if (is_full_array($pid_data)) {
          $pic_type_1 = array();
          $pic_type_2 = array();
          foreach ($pid_data as $key => $value) {
            if (1 == $value['type']) {
              $pic_type_1[] = $value;
            }
            if (2 == $value['type']) {
              $pic_type_2[] = $value;
            }
          }
//                    print_r($pic_type_1);
//                    print_r($pic_type_2);
//                    var_dump(count($pic_type_1));
//                    var_dump(count($pic_type_2));
          if (count($pic_type_1) > 0 && count($pic_type_2) > 0) {
            $house_level = 2;
            if (count($pic_type_1) >= 3) {
              $house_level = 3;
            }
          } else if (count($pic_type_1) == 0 && count($pic_type_2) == 0) {
            $house_level = 0;
          } else {
            $house_level = 1;
          }
          $update_data = array('house_level' => $house_level);
          $where_cond = array('id' => intval($v['id']));
          $result = $this->rent_house_model->update_house($update_data, $where_cond);
          var_dump($house_level);
          var_dump($v['id']);
          echo '结果：';
          var_dump($result);
        }
        echo '---<br>';
      }
    }
  }


    //设为推荐房源(金品app)
    public function add_recommend_house()
    {

        $house_id = $this->input->post("house_id");
        //判断该房源是否为有效房源
        $this->rent_house_model->set_id($house_id);
        $houseinfo = $this->rent_house_model->get_info_by_id();
        if ($houseinfo['status'] != 1) {
            $res = ['status' => 'fail', 'msg' => '非有效房源不可设为优质房源'];
            echo json_encode($res);
            return;
        }
        //更新该房源is_recommend字段为1
        $this->load->model("recommend_house_base_model");
        $data = [
            'city' => $_SESSION[WEB_AUTH]["city"],
            'house_id' => $house_id,
            'type' => "rent_house",
        ];
        //检查推荐表中是否已有
        $data['create_time'] = time();
        $recommendHouse = $this->recommend_house_base_model->getbycond($data);
        if (!empty($recommendHouse)) {
            $res = ['status' => 'fail', 'msg' => '该房源已设为优质房源'];
            echo json_encode($res);
            return;
        }
        $data['create_time'] = time();
        $recommend_house_id = $this->recommend_house_base_model->insert($data);
        if ($recommend_house_id) {
            if ($this->rent_house_model->update_house(['recommend_house_id' => $recommend_house_id], array('id' => $house_id))) {
                $res = ['status' => 'success', 'msg' => '插入成功'];
            };
        } else {
            $res = ['status' => 'fail', 'msg' => '插入失败'];
        }
        echo json_encode($res);
    }

    //取消推荐房源(金品app)
    public function cancel_recommend_house()
    {
        $house_id = $this->input->post("house_id");
        //更新该房源is_recommend字段为0
        if ($this->rent_house_model->update_house(['recommend_house_id' => 0], array('id' => $house_id))) {
            $this->load->model("recommend_house_base_model");
            $data = [
                'city' => $_SESSION[WEB_AUTH]["city"],
                'house_id' => $house_id,
                'type' => "rent_house",
            ];
            if ($this->recommend_house_base_model->delete($data)) {
                echo json_encode(['status' => 'success', 'msg' => '取消成功']);
            }
        }
    }

    //设为推荐房源(金品app)
    public function add_like_house()
    {
        $house_id = $this->input->post("house_id");
        //判断该房源是否为有效房源
        $this->rent_house_model->set_id($house_id);
        $houseinfo = $this->rent_house_model->get_info_by_id();
        if ($houseinfo['status'] != 1) {
            $res = ['status' => 'fail', 'msg' => '非有效房源不可设为优质房源'];
            echo json_encode($res);
            return;
        }
        if ($houseinfo['is_set_like'] == 1) {
            $res = ['status' => 'fail', 'msg' => '已设为猜你喜欢的房源，无需重复设置'];
            echo json_encode($res);
            return;
        }
        //最多设置十条猜你喜欢房源
        $condWhere = 'is_set_like = 1';
        $likeHouseNum = $this->rent_house_model->get_rent_house_num_by_cond($condWhere);
        if ($likeHouseNum > 9) {
            $res = ['status' => 'fail', 'msg' => '最多设置十条猜你喜欢的房源'];
            echo json_encode($res);
            return;
        }
        if ($this->rent_house_model->update_house(['is_set_like' => 1], array('id' => $house_id))) {
            $res = ['status' => 'success', 'msg' => '设置成功'];
        } else {
            $res = ['status' => 'fail', 'msg' => '设置失败'];
        }
        echo json_encode($res);
    }

    //取消推荐房源(金品app)
    public function cancel_like_house()
    {
        $house_id = $this->input->post("house_id");
        //判断该房源是否为有效房源
        $this->rent_house_model->set_id($house_id);
        $houseinfo = $this->rent_house_model->get_info_by_id();
        if (empty($houseinfo)) {
            echo json_encode(['status' => 'fail', 'msg' => '没有该房源']);
            return;
        }
        //更新该房源is_recommend字段为0
        if ($this->rent_house_model->update_house(['is_set_like' => 0], array('id' => $house_id))) {
            echo json_encode(['status' => 'success', 'msg' => '取消成功']);
        } else {
            echo json_encode(['status' => 'fail', 'msg' => '取消失败']);
        }
    }
}

/* End of file rent_house.php */
/* Location: ./application/controllers/rent_house.php */
