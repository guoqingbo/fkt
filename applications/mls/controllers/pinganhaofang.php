<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 平安好房
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      yuan
 */
class Pinganhaofang extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'hz';

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
    $this->load->library('Pinganfang');
    $this->load->library('Curl');

    $this->load->model('district_model');//区属模型类
    //$this->load->model('community_model');//楼盘模型类
    $this->load->model('cmt_correction_model');//楼盘纠错模型类
    $this->load->model('help_center_model');//帮助中心模型类
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
      '_from' => 'f100',
      '_requesttime' => '1468811439'
    );

    $result = $this->curl->vpost('http://api.pinganfang.com/hft/1.0/sync_company_info', $param_arr);
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
          $return_id_arr = $this->pinganfang->get_district_street_id_by_name($dist_name, $street_name);
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
      echo "<script>window.location.href='" . MLS_URL . "/pinganhaofang/community_data/" . $page . "';</script>";
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
      echo "<script>window.location.href='" . MLS_URL . "/pinganhaofang/company_data/" . $page . "';</script>";
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
      echo "<script>window.location.href='" . MLS_URL . "/pinganhaofang/agency_data/" . $page . "';</script>";
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
          $return_id_arr = $this->pinganfang->get_district_street_id_by_name($dist_name, $street_name);
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
      echo "<script>window.location.href='" . MLS_URL . "/pinganhaofang/broker_data/" . $page . "';</script>";
    }
  }

  //随机生成数据跟踪id,并根据参数获得加密字符串，返回所有参数
  public function return_trackid_token()
  {
    $param_arr = $this->input->get(NULL, TRUE);
    $result = array();
    if (is_full_array($param_arr)) {
      $param_arr['_format'] = 'json';
      $param_arr['_from'] = 'f100';
      $param_arr['_requesttime'] = time();
      $return_arr = $this->pinganfang->return_trackid_token($param_arr);
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
      $param_arr['_from'] = 'f100';
      $param_arr['_requesttime'] = time();
      $return_arr = $this->pinganfang->return_trackid_token($param_arr);
      $result = array_merge($return_arr, $param_arr);
    }
    return $result;
  }

  //发送post请求
  public function post_data($method = '')
  {
    $param_arr = $this->input->get(NULL, TRUE);
    $result = $this->curl->vpost('http://api.pinganfang.com/' . $method, $param_arr);
    echo $result;
  }

  //发送post请求
  public function post_data_2($method = '', $param_arr = array())
  {
    $result = $this->curl->vpost('http://api.pinganfang.com/' . $method, $param_arr);
    echo strstr($result, '{"code"');
  }

  //发送post请求
  public function post_data_3($method = '', $param_arr = array())
  {
    $result = $this->curl->vpost('http://api.pinganfang.com' . $method, $param_arr);
    $result_str = strstr($result, '{"code"');
    return $result_str;
  }

  //同步房源到平安好房，总方法
  public function post_all_data()
  {
    $param_arr = $this->input->get(NULL, TRUE);
    $result_arr = array();
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
//                                            $result_arr = array(
//                                                'code' => 'success',
//                                                'msg' => '房源同步成功'
//                                            );
                        } else {
                          $result_arr = array(
                            'code' => 'house_error',
                            'msg' => '推送房源失败'
                          );
                        }
                      }
                    } else {
                      $result_arr = array(
                        'code' => 'broker_error',
                        'msg' => '推送门店失败'
                      );
                    }
                  }
                } else {
                  $result_arr = array(
                    'code' => 'agency_error',
                    'msg' => '推送门店失败'
                  );
                }
              }
            } else {
              $result_arr = array(
                'code' => 'company_error',
                'msg' => '推送公司失败'
              );
            }
          }
        } else {
          $result_arr = array(
            'code' => 'community_error',
            'msg' => '推送楼盘失败'
          );
        }
      }
    }
    echo json_encode($result_arr);
  }

  //房源下架
  public function house_down()
  {
    $house_id = $this->input->get('house_id');
    $param_arr = array();
    $result_arr = array();
    if (isset($house_id) && intval($house_id) > 0) {
      $param_arr['id'] = intval($house_id);
      $return_param = $this->return_trackid_token_2($param_arr);
      $down_return_json = $this->post_data_3('/esf/1.0/offline', $return_param);
      $down_return_arr = json_decode($down_return_json);
      $down_msg = $down_return_arr->msg;
      if ('ok' == $down_msg) {
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
    echo json_encode($result_arr);
  }

}
/* End of file my_info.php */
/* Location: ./applications/mls/controllers/my_info.php */
