<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * blacklist_controller CLASS
 *
 * 中介黑名控制器类
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          angel_in_us
 */
class pinganFang extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('pinganhouse_model');
    $this->load->model('house_config_model');//加载出售基本配置MODEL
    $this->load->library('Pinganhouse');
    $this->load->library('Curl');
    $this->load->model('agency_model');
    $this->load->model('district_model');//区属模型类
    $this->load->model('community_model');//楼盘模型类
    $this->load->model('cmt_correction_model');//楼盘纠错模型类
    $this->load->model('help_center_model');//帮助中心模型类
    $this->load->model('broker_info_model');
    $this->load->model('auth_review_model');
    $this->load->model('sell_house_model');
    $this->set_from();
    $this->set_url();
  }

  private function _get_str_cond($params)
  {
    $cond_where = '';
    //公司id
    if ($params['company_id']) {
      $cond_where .= " AND s.company_id = '" . $params['company_id'] . "'";
    }
    //门店id
    if ($params['agency_id']) {
      $cond_where .= " AND s.agency_id = '" . $params['agency_id'] . "'";
    }
    //手机号
    if ($params['phone']) {
      $cond_where .= " AND b.phone = '" . $params['phone'] . "'";
    }
    //经纪人姓名
    if ($params['broker_name']) {
      $cond_where .= " AND s.broker_name = '" . $params['broker_name'] . "'";
    }
    //房源编号
    if ($params['house_id']) {
      $cond_where .= " AND s.id = '" . intval($params['house_id']) . "'";
    }
    //楼盘id
    if ($params['block_id']) {
      $cond_where .= " AND s.block_id = '" . $params['block_id'] . "'";
    }
    //区属
    if ($params['district_id']) {
      $cond_where .= " AND s.district_id = '" . $params['district_id'] . "'";
    }
    //板块
    if ($params['street_id']) {
      $cond_where .= " AND s.street_id = '" . $params['street_id'] . "'";
    }
    //面积
    if ($params['areamin']) {
      $cond_where .= " AND s.buildarea >= '" . $params['areamin'] . "'";
    }
    if ($params['areamax']) {
      $cond_where .= " AND s.buildarea <= '" . $params['areamax'] . "'";
    }
    //价格
    if ($params['pricemin']) {
      $cond_where .= " AND s.price >= '" . $params['pricemin'] . "'";
    }
    //价格
    if ($params['pricemax']) {
      $cond_where .= " AND s.price <= '" . $params['pricemax'] . "'";
    }
    //户型
    if ($params['room']) {
      $cond_where .= " AND s.room = '" . $params['room'] . "'";
    }

    //上传时间
    if ($params['timemin']) {
      $cond_where .= " AND p.outside_time  >= '" . strtotime($params['timemin'] . ' 00:00:00') . "'";
    }
    if ($params['timemax']) {
      $cond_where .= " AND p.outside_time  <= '" . strtotime($params['timemax'] . ' 23:59:59') . "'";
    }

    //审核状态
    if ($params['is_check'] != '') {
      $cond_where .= " AND p.is_check  = '" . $params['is_check'] . "'";
    }

    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, 'AND');
    $cond_where = trim($cond_where);

    return $cond_where;
  }


  public function index()
  {
    $data = array();
    $data['title'] = "平安好房审核管理";
    //form 表单提交
    $post_params = $this->input->post(null, true);
    $data['post_params'] = $post_params;
    $where = $this->_get_str_cond($post_params);
    //分页开始
    $data['sold_num'] = $this->pinganhouse_model->get_num_by($where);
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($post_params['pg']) ? intval($post_params['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['list'] = $this->pinganhouse_model->get_list_by_cond($where, $data['offset'], $data['pagesize']);
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();
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

    $this->load->view("pinganFang/index", $data);
  }

  public function house_detail($house_id = 0, $id = 0)
  {
    //房源佣金分成数据
    $this->load->model('sell_house_share_ratio_model');
    //加载出售基本配置MODEL
    $this->load->model('sell_house_model');
    $data['ratio_info'] = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();
    $data['title'] = '出售房源详情';
    $data['conf_where'] = 'index';
    $arr = array('id' => $house_id);
    $where_in = array('id', $arr);
    $house_info = $this->sell_house_model->get_house_info_byids($where_in);
    $this->load->model('community_model');
    $cmt_info = $this->community_model->auto_cmtname($house_info[0]['block_name'], 10);
    $house_info[0]['districtname'] = $cmt_info[0]['districtname'];
    $house_info[0]['streetname'] = $cmt_info[0]['streetname'];
    $data['house_detail'] = $house_info[0];
    $data['house_id'] = $house_id;
    $data['id'] = $id;
    $this->load->view('pinganFang/house_detail', $data);
  }

  //更新审核状态
  public function update_status()
  {
    $id = $this->input->post('id');
    $house_id = $this->input->post('house_id');
    $data['is_check'] = $this->input->post('is_check');
    $data['check_reason'] = $this->input->post('check_reason');
    $result = $this->pinganhouse_model->update_house($id, $data);
    if ($result) {
      if ($data['is_check'] == 1) {
        $data = $this->post_all_data($house_id, $id);
      } else {
        $info = $this->pinganhouse_model->get_info_by_id($id);
        if ($info['is_outside'] == 1) {
          $data = $this->house_down($house_id, $id);
        } else {
          $data = array(
            'code' => 'success',
            'msg' => '审核成功'
          );
        }
      }
    } else {
      $data = array(
        'code' => 'update_error',
        'msg' => '审核失败'
      );
    }
    echo json_encode($data);
  }


  //更新同步状态 0 未同步 1 已同步 2 已下架
  public function update_outside($id, $is_outside)
  {
    $data['is_outside'] = $is_outside;
    if ($is_outside == 1) {
      $data['update_time'] = time();
    } else {
      $data['update_time'] = '';
    }
    $result = $this->pinganhouse_model->update_house($id, $data);
    if ($result) {
      $data['result'] = 1;
    } else {
      $data['result'] = 0;
    }
  }


  private function set_from()
  {
    $this->_from = $this->pinganhouse->get_from();
  }

  private function set_url()
  {
    $this->_url = $this->pinganhouse->get_url();
  }

  public function ceshi()
  {
    $param_arr = array(
      '_trackid' => '8E786F49-684A-1108-E13F-91C1E45A97CB',
      '_token' => 'c3dc23ee6e456579c24913d81e7531e6',
      'company_id' => '18',
      'city_id' => '816',
      'company_name' => '江苏苏商房产销售有限公司总部1',
      'company_full_name' => '江苏苏商房产销售有限公司总部1',
      '_format' => 'json',
      '_from' => $this->_from,
      '_requesttime' => '1468811439'
    );

    $result = $this->curl->vpost_pingan('http://' . $this->_url . '/hft/1.0/sync_company_info', $param_arr);
    print_r($result);
  }

  //楼盘基础数据同步
  public function community_data($page = 0)
  {
    $data['where_cond'] = array(
      'status' => 2
    );
    $data['like_code'] = array();
    $strcode = $this->input->post('strcode');
    if (isset($strcode) && !empty($strcode)) {
      $data['like_code']['cmt_name'] = trim($strcode);
    }
    $data['strcode'] = $strcode;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    /** 分页参数 */
    $this->load->model('community_model');
    $this->_total_count = $this->community_model->get_community_num($data['where_cond'], $data['like_code']);
    $_limit = 50;
    $_offset = $page * $_limit;
    $data['community'] = $this->community_model->get_community($data['where_cond'], $data['like_code'], $_offset, $_limit);
    if (is_full_array($data['community'])) {
      foreach ($data['community'] as $key => $value) {
        if (!empty($value['dist_id']) && !empty($value['streetid'])) {
          //获得区属、板块名
          $dist_name = $this->district_model->get_distname_by_id(intval($value['dist_id']));
          $street_name = $this->district_model->get_streetname_by_id(intval($value['streetid']));
          //根据区属板块名，获得对应id
          $return_id_arr = $this->pinganhouse->get_district_street_id_by_name($dist_name, $street_name);
          $this->load->library('Pinganfang');
          $param_arr = array(
            'id' => intval($value['id']),
            'name' => $value['cmt_name'],
            'city_id' => 816,
            'region_id' => $return_id_arr['district_id'],
            'sub_region_id' => $return_id_arr['street_id'],
            'address' => $value['address'],
          );
          $return_param = $this->return_trackid_token_2($param_arr);
          if (is_full_array($return_param)) {
            $this->post_data_2('/xq/1.0/sync_xq', $return_param);
            echo '------<br>';
          }
        }
      }
      $page++;
      echo "<script>window.location.href='" . MLS_ADMIN_URL . "/pinganhaofang/community_data/" . $page . "';</script>";
    }
  }

  //公司基础数据同步
  public function company_data($page = 0)
  {
    $where = 'company_id = 0 and status = 1';
    $this->load->model('agency_model');
    $this->_total_count = $this->agency_model->count_by($where);
    $_limit = 20;
    $_offset = $page * $_limit;
    $data['company'] = $this->agency_model->get_all_by($where, $_offset, $_limit);
    if (is_full_array($data['company'])) {
      foreach ($data['company'] as $key => $value) {
        $this->load->library('Pinganfang');
        $param_arr = array(
          'company_id' => intval($value['id']),
          'city_id' => 816,
          'company_name' => $value['name'],
          'company_full_name' => $value['name']
        );
        $return_param = $this->return_trackid_token_2($param_arr);
        if (is_full_array($return_param)) {
          $this->post_data_2('/hft/1.0/sync_company_info', $return_param);
          echo '------<br>';
        }
      }
      $page++;
      echo "<script>window.location.href='" . MLS_ADMIN_URL . "/pinganhaofang/company_data/" . $page . "';</script>";
    }
  }

  //门店基础数据同步
  public function agency_data($page = 0)
  {
    $where = 'company_id <> 0 and status = 1';
    $this->load->model('agency_model');
    $this->_total_count = $this->agency_model->count_by($where);
    $_limit = 20;
    $_offset = $page * $_limit;
    $data['agency'] = $this->agency_model->get_all_by($where, $_offset, $_limit);
    if (is_full_array($data['agency'])) {
      foreach ($data['agency'] as $key => $value) {
        $this->load->library('Pinganfang');
        $param_arr = array(
          'dept_id' => intval($value['id']),
          'name' => $value['name'],
          'dept_address' => $value['address'],
          'company_id' => $value['company_id'],
          'parent_dept_id' => 0
        );
        $return_param = $this->return_trackid_token_2($param_arr);
        if (is_full_array($return_param)) {
          $this->post_data_2('/hft/1.0/sync_user_dept', $return_param);
          echo '------<br>';
        } else {
          echo '参数为空<br>';
        }
      }
      $page++;
      echo "<script>window.location.href='" . MLS_ADMIN_URL . "/pinganhaofang/agency_data/" . $page . "';</script>";
    } else {
      echo '当前页数据为空<br>';
    }
  }

  //经纪人基础数据同步
  public function broker_data($page = 0)
  {
    $where = 'group_id = 2';
    $this->load->model('broker_info_model');
    $this->load->model('auth_review_model');
    $this->_total_count = $this->broker_info_model->count_by($where);
    $_limit = 10;
    $_offset = $page * $_limit;
    $data['broker'] = $this->broker_info_model->get_all_by($where, $_offset, $_limit);
    if (is_full_array($data['broker'])) {
      foreach ($data['broker'] as $key => $value) {
        //获得经纪人身份证号
        $broker_id = intval($value['broker_id']);
        $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id, 0, 1);
        $idcard = 0;
        if (is_full_array($ident_info)) {
          $idcard = $ident_info['idcard'];
        }
        //根据门店id，获得区属板块
        $agency_id = intval($value['agency_id']);
        $this->load->model('agency_model');
        $agency_data = $this->agency_model->get_by_id($agency_id);
        if (is_full_array($agency_data)) {
          $dist_id = $agency_data['dist_id'];
          $street_id = $agency_data['street_id'];
          //获得区属板块名
          $dist_name = $this->district_model->get_distname_by_id($dist_id);
          $street_name = $this->district_model->get_streetname_by_id($street_id);
          //根据区属板块名，获得对应id
          $return_id_arr = $this->pinganhouse->get_district_street_id_by_name($dist_name, $street_name);
          $this->load->library('Pinganfang');
          $param_arr = array(
            'user_id' => $value['broker_id'],
            'user_name' => $value['truename'],
            'user_mobile' => $value['phone'],
            'user_card_no' => $idcard,
            'city_id' => 816,
            'area_id' => $return_id_arr['district_id'],
            'block_id' => $return_id_arr['street_id'],
            'company_id' => $value['company_id'],
            'store_id' => $value['agency_id']
          );
          $return_param = $this->return_trackid_token_2($param_arr);
          if (is_full_array($return_param)) {
            $this->post_data_2('/hft/1.0/user/profile', $return_param);
            echo '------<br>';
          }
        }
      }
      $page++;
      echo "<script>window.location.href='" . MLS_ADMIN_URL . "/pinganhaofang/broker_data/" . $page . "';</script>";
    }
  }

  //随机生成数据跟踪id,并根据参数获得加密字符串，返回所有参数
  public function return_trackid_token()
  {
    $param_arr = $this->input->get(NULL, TRUE);
    $result = array();
    if (is_full_array($param_arr)) {
      $param_arr['_format'] = 'json';
      $param_arr['_from'] = $this->_from;
      $param_arr['_requesttime'] = time();
      $return_arr = $this->pinganhouse->return_trackid_token($param_arr);
      $result = array_merge($return_arr, $param_arr);
    }
    echo json_encode($result);
    exit;
  }

  //随机生成数据跟踪id,并根据参数获得加密字符串，返回所有参数
  public function return_trackid_token_2($param_arr = array())
  {
    $result = array();
    if (is_full_array($param_arr)) {
      $param_arr['_format'] = 'json';
      $param_arr['_from'] = $this->_from;
      $param_arr['_requesttime'] = time();
      $return_arr = $this->pinganhouse->return_trackid_token($param_arr);
      $result = array_merge($return_arr, $param_arr);
    }
    return $result;
  }

  //发送post请求
  public function post_data($method = '')
  {
    $param_arr = $this->input->get(NULL, TRUE);
    $result = $this->curl->vpost_pingan('http://' . $this->_url . '/' . $method, $param_arr);
    echo $result;
  }

  //发送post请求
  public function post_data_2($method = '', $param_arr = array())
  {
    $result = $this->curl->vpost_pingan('http://' . $this->_url . '/' . $method, $param_arr);
    echo strstr($result, '{"code"');
  }

  //发送post请求
  public function post_data_3($method = '', $param_arr = array())
  {
    $result = $this->curl->vpost_pingan('http://' . $this->_url . $method, $param_arr);
    $result_str = strstr($result, '{"code"');
    return $result_str;
  }

  //同步房源到平安好房，总方法
  public function post_all_data($house_id, $id)
  {
    $param_arr = $this->get_data_by_house_id($house_id);
    $result_arr = array();
    $is_ajax = $this->input->get('is_ajax');
    if (is_full_array($param_arr)) {
      //推送房源所属楼盘
      $return_param = $this->return_trackid_token_2($param_arr['cmt_data']);
      if (is_full_array($return_param)) {
        $cmt_return_json = $this->post_data_3('/xq/1.0/sync_xq', $return_param);
        $cmt_return_arr = json_decode($cmt_return_json);
        $cmt_msg = $cmt_return_arr->msg;
        if ('ok' == $cmt_msg) {
          //推送经纪人所在公司
          $return_param = $this->return_trackid_token_2($param_arr['company_data']);
          if (is_full_array($return_param)) {
            $company_return_json = $this->post_data_3('/hft/1.0/sync_company_info', $return_param);
            $company_return_arr = json_decode($company_return_json);
            $company_msg = $company_return_arr->msg;
            if ('ok' == $company_msg) {
              //推送经纪人所在门店
              $return_param = $this->return_trackid_token_2($param_arr['agency_data']);
              if (is_full_array($return_param)) {
                $agency_return_json = $this->post_data_3('/hft/1.0/sync_user_dept', $return_param);
                $agency_return_arr = json_decode($agency_return_json);
                $agency_msg = $agency_return_arr->msg;
                if ('ok' == $agency_msg) {
                  //推送经纪人数据
                  $return_param = $this->return_trackid_token_2($param_arr['broker_data']);
                  if (is_full_array($return_param)) {
                    $broker_return_json = $this->post_data_3('/hft/1.0/user/profile', $return_param);
                    $broker_return_arr = json_decode($broker_return_json);
                    $broker_msg = $broker_return_arr->msg;
                    if ('ok' == $broker_msg) {
                      //推送房源
                      $return_param = $this->return_trackid_token_2($param_arr['house_data']);
                      if (is_full_array($return_param)) {
                        $house_return_json = $this->post_data_3('/esf/1.0/sync', $return_param);
                        $house_return_arr = json_decode($house_return_json);
                        $house_msg = $house_return_arr->msg;
                        if ('ok' == $house_msg) {
                          $this->update_outside($id, 1);
                          $result_arr = array(
                            'code' => 'success',
                            'msg' => '房源同步成功'
                          );
                          if (is_full_array($param_arr['pic_data'])) {
                            //推送图片数据
                            $return_param = $this->return_trackid_token_2($param_arr['pic_data']);
                            if (is_full_array($return_param)) {
                              $pic_return_json = $this->post_data_3('/esf/1.0/mul_picture', $return_param);
                              $pic_return_arr = json_decode($pic_return_json);
                              $pic_msg = $pic_return_arr->msg;
                            }
                          }
                        } else {
                          $result_arr = array(
                            'code' => 'house_error',
                            'msg' => '推送房源失败,' . $house_msg
                          );
                        }
                      }
                    } else {
                      $result_arr = array(
                        'code' => 'broker_error',
                        'msg' => '推送经纪人失败,' . $broker_msg
                      );
                    }
                  }
                } else {
                  $result_arr = array(
                    'code' => 'agency_error',
                    'msg' => '推送门店失败,' . $agency_msg
                  );
                }
              }
            } else {
              $result_arr = array(
                'code' => 'company_error',
                'msg' => '推送公司失败,' . $company_msg
              );
            }
          }
        } else {
          $result_arr = array(
            'code' => 'community_error',
            'msg' => '推送楼盘失败,' . $cmt_msg
          );
        }
      }
    }
    if ($is_ajax) {
      echo json_encode($result_arr);
    } else {
      return $result_arr;
    }
  }

  //房源下架
  public function house_down($house_id, $id)
  {
    $param_arr = array();
    $result_arr = array();
    $is_ajax = $this->input->get('is_ajax');
    if (isset($house_id) && intval($house_id) > 0) {
      $param_arr['id'] = intval($house_id);
      $return_param = $this->return_trackid_token_2($param_arr);
      $down_return_json = $this->post_data_3('/esf/1.0/offline', $return_param);
      $down_return_arr = json_decode($down_return_json);
      $down_msg = $down_return_arr->msg;
      if ('ok' == $down_msg) {
        $this->update_outside($id, 2);
        $result_arr = array(
          'code' => 'success',
          'msg' => '下架成功'
        );
      } else {
        $result_arr = array(
          'code' => 'error',
          'msg' => $down_msg
        );
      }
    } else {
      $result_arr = array(
        'code' => 'error',
        'msg' => '操作失败'
      );
    }
    if ($is_ajax) {
      echo json_encode($result_arr);
    } else {
      return $result_arr;
    }
  }

  /**
   * 根据房源id，获得所在楼盘、公司、门店、经纪人数据
   * @access  public
   * @param  int 区属id
   * @return  array
   */
  public function get_data_by_house_id($house_id)
  {
    $result_arr = array();
    $this->sell_house_model->set_id($house_id);
    $select_feilds = array();
    $this->sell_house_model->set_search_fields($select_feilds);
    $data_info = $this->sell_house_model->get_info_by_id();
    if (is_full_array($data_info)) {
      //楼盘数据
      $cmt_id = intval($data_info['block_id']);
      $cmt_data = $this->community_model->get_cmtinfo_longitude($cmt_id);
      if (is_full_array($cmt_data)) {
        //获得区属、板块名
        $dist_name = $this->district_model->get_distname_by_id(intval($cmt_data['dist_id']));
        $street_name = $this->district_model->get_streetname_by_id(intval($cmt_data['streetid']));
        $dist_name = '武侯';
        $street_name = '川音';
        //根据区属板块名，获得对应id
        $return_id_arr = $this->pinganhouse->get_district_street_id_by_name($dist_name, $street_name);

        $result_arr['cmt_data'] = array(
          'id' => $cmt_data['id'],
          'name' => $cmt_data['cmt_name'],
          'city_id' => 816,
          'region_id' => $return_id_arr['district_id'],
          'sub_region_id' => $return_id_arr['street_id'],
          'address' => $cmt_data['address']
        );
      }
      //公司数据
      $company_id = intval($data_info['company_id']);
      $company_data = $this->agency_model->get_by_id($company_id);
      if (is_full_array($company_data)) {
        $result_arr['company_data'] = array(
          'company_id' => $company_data['id'],
          'city_id' => 816,
          'company_name' => $company_data['name'],
          'company_full_name' => $company_data['name']
        );
      }
      //门店数据
      $agency_id = intval($data_info['agency_id']);
      $agency_data = $this->agency_model->get_by_id($agency_id);
      if (is_full_array($agency_data)) {
        $result_arr['agency_data'] = array(
          'dept_id' => $agency_data['id'],
          'name' => $agency_data['name'],
          'dept_address' => $agency_data['address'],
          'company_id' => $agency_data['company_id'],
          'parent_dept_id' => 0
        );
      }
      //经纪人数据
      $broker_id = intval($data_info['broker_id']);
      //获得经纪人身份证号
      $this->load->model('auth_review_model');
      $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id, 0, 1);
      $idcard = 0;
      if (is_full_array($ident_info)) {
        $idcard = $ident_info['idcard'];
      }
      $broker_data = $this->broker_info_model->get_one_by(array('broker_id' => $broker_id));
      if (is_full_array($broker_data)) {
        $result_arr['broker_data'] = array(
          'user_id' => $broker_id,
          'user_name' => $broker_data['truename'],
          'user_mobile' => $broker_data['phone'],
          'user_card_no' => $idcard,
          'city_id' => 816,
          'area_id' => $return_id_arr['district_id'],
          'block_id' => $return_id_arr['street_id'],
          'company_id' => $broker_data['company_id'],
          'store_id' => $broker_data['agency_id']
        );
      }

      //房源数据
      if (is_full_array($broker_data)) {
        $house_config_pa = $this->pinganhouse->get_house_config();
        $this->load->model('house_config_model');
        $house_config_mls = $this->house_config_model->get_config();
        //房源类型 默认其它
        $house_type = 8;
        $house_config_type = $house_config_mls['house_type'];
        if (!empty($data_info['house_type'])) {
          foreach ($house_config_pa['secondhand_housetype'] as $key => $value) {
            if ($value == $house_config_type[$data_info['house_type']]) {
              $house_type = $key;
            }
          }
        }
        //装修风格
        $house_config_fitment = $house_config_mls['fitment'];
        if ('毛坯' == $house_config_fitment[$data_info['house_type']]) {
          $fitment = 1;
        } else if ('简装' == $house_config_fitment[$data_info['house_type']]) {
          $fitment = 2;
        } else if ('中装' == $house_config_fitment[$data_info['house_type']]) {
          $fitment = 2;
        } else if ('精装' == $house_config_fitment[$data_info['house_type']]) {
          $fitment = 3;
        } else if ('豪装' == $house_config_fitment[$data_info['house_type']]) {
          $fitment = 4;
        } else {
          $fitment = 5;
        }

        //朝向类型 默认其它
        $house_config_forward = $house_config_mls['forward'];
        if (!empty($data_info['forward'])) {
          if (is_full_array($house_config_forward)) {
            foreach ($house_config_pa['secondhand_toward'] as $key => $value) {
              if ($value == $house_config_forward[$data_info['forward']]) {
                $forward = $key;
              }
            }
          }
        }

        $result_arr['house_data'] = array(
          'id' => $data_info['id'],//房源id
          'loupan_id' => $data_info['block_id'],//楼盘id
          'user_id' => $broker_id,//经纪人id
          //'unique_id' => $data_info['truename'],
          'title' => $data_info['title'],//房源标题
          'desc' => $data_info['bewrite'],//房源描述
          'price' => $data_info['price'],//价格
          'room_num' => $data_info['room'],//室
          'hall_num' => $data_info['hall'],//厅
          'toilet_num' => $data_info['toilet'],//卫
          'current_floor' => $data_info['floor'],//当前楼层
          'total_floor' => $data_info['totalfloor'],//总楼层
          'space' => $data_info['buildarea'],//面积
          'house_type' => $house_type,//房型
          'decoration' => $fitment,//装修风格
          'toward' => $forward,//朝向
          'building_year' => $data_info['buildyear'],//建筑年代
//                    'door_plate' => $data_info['truename'],
//                    'room_no' => $data_info['truename'],
//                    'tag' => $data_info['truename'],
//                    'create_time' => $data_info['truename'],
        );
      }

      //房源图片数据
      if (!empty($data_info['pic_ids'])) {
        $pic_ids = trim($data_info['pic_ids'], ',');
        $this->load->model('pic_model');
        $pic_info = $this->pic_model->find_house_pic_by_ids('upload', $pic_ids);
        if (is_full_array($pic_info)) {
          $pic_info_2 = array();
          foreach ($pic_info as $key => $value) {
            $arr = array(
              'image_id' => $value['id'],
              'pic_type' => $value['type'],
              'url' => str_replace('thumb', 'initial', $value['url']),
              'default' => $value['is_top']
            );
            $pic_info_2[] = $arr;
          }
          $result_arr['pic_data'] = array(
            'house_id' => $data_info['id'],
            'urls' => $pic_info_2
          );
        }
      }
    }
    return $result_arr;
  }

  public function get_house_pic($house_id)
  {
    //统计室内图的数量
    $result = $this->pinganhouse_model->find_house_pic_by('sell_house', $house_id);
    //室内图
    if (is_full_array($result)) {
      foreach ($result as $key => $val) {
        if ($val['type'] == 1) {
          $data['shinei'][] = $val;
        } elseif ($val['type'] == 2) {
          $data['huxing'][] = $val;
        }

      }
    }
    $this->load->view('pinganFang/pic_detail', $data);
  }
}

/* End of file blacklist.php */
/* Location: ./application/controllers/blacklist.php */
