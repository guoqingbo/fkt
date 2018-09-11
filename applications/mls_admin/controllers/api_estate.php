<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api_estate extends CI_Controller
{

  private $user = '';

  /**
   * 构造函数
   *
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();

    $user = array(
      //洪房
      '73565d9c24d2956b64d96036833b1eb3' => array('allow_city' => array('wuhan'))
    );

    $key = $this->input->get('key', TRUE);
    $city = $this->input->get('city', TRUE);
    if (is_full_array($user[$key]['allow_city']) && in_array($city, $user[$key]['allow_city'])) {
      $_SESSION[WEB_AUTH]["city"] = $city;
    } else {
      $msg = array(
        'result' => 0,
        'msg' => '接入用户身份、城市非法',
        'data' => array()
      );

      echo json_encode($msg);
      die();
    }
  }

  /**
   * 楼盘数据同步接口
   */
  public function index()
  {
    $msg = array(
      'result' => 1,
      'msg' => '楼盘数据同步接口',
      'data' => array()
    );
    echo json_encode($msg);
  }

  /*
    * 获取区属数据
    * GET: explain = 1 解释接口字段
    */
  public function get_dist($return = 0)
  {
    $explain = $this->input->get('explain', TRUE);

    if (1 == $explain) {
      $msg = array(
        'result' => 1,
        'msg' => '解释接口字段',
        'data' => array('id' => '区属ID', 'district' => '区属名称')
      );
    } else {
      $this->load->model('district_model');//楼盘模型类
      $arr = $this->district_model->get_district();

      $data = array();
      if (is_full_array($arr)) {
        foreach ($arr as $value) {
          $data[$value['id']]['id'] = $value['id'];
          $data[$value['id']]['district'] = $value['district'];
        }
      }

      if (1 == $return) {
        return $data;
      } else {
        $msg = array(
          'result' => 1,
          'msg' => '获取区属数据',
          'data' => $data
        );
      }
    }

    echo json_encode($msg);
  }

  /*
    * 获取板块数据
    * GET: explain = 1 解释接口字段
    */
  public function get_street($return = 0)
  {
    $explain = $this->input->get('explain', TRUE);

    if (1 == $explain) {
      $msg = array(
        'result' => 1,
        'msg' => '解释接口字段',
        'data' => array('id' => '板块ID', 'district' => '板块名称')
      );
    } else {
      $this->load->model('district_model');//楼盘模型类
      $arr = $this->district_model->get_street();

      $data = array();
      if (is_full_array($arr)) {
        foreach ($arr as $value) {
          $data[$value['id']]['id'] = $value['id'];
          $data[$value['id']]['streetname'] = $value['streetname'];
        }
      }

      if (1 == $return) {
        return $data;
      } else {
        $msg = array(
          'result' => 1,
          'msg' => '获取板块数据',
          'data' => $data
        );
      }
    }

    echo json_encode($msg);
  }

  /*
    * 获取楼盘数据
    * GET: explain = 1 解释接口字段
     * GET: num 获取楼盘的数量，默认为全部楼盘
    * GET: lastid 上次读取的最后一个楼盘的ID，用于增量读取
    */
  public function get_estate()
  {
    $num = $this->input->get('num', TRUE);
    $lastid = $this->input->get('lastid', TRUE);
    $explain = $this->input->get('explain', TRUE);

    $fieldarr = array(
      'id' => '楼盘ID', 'type' => '楼盘类型：1住宅2别墅3商铺4写字楼5厂房6仓库7车库', 'cmt_name' => '楼盘名称', 'dist_id' => '区属ID', 'address' => '楼盘地址', 'developers' => '开发商', 'property_company' => '物业公司', 'buildarea' => '建筑面积', 'coverarea' => '占地面积', 'build_date' => '建筑年代', 'deliver_date' => '交付日期', 'parking' => '停车位', 'total_room' => '总户数', 'build_num' => '总栋数', 'floor_instruction' => '楼层状况', 'property_year' => '产权年限', 'plot_ratio' => '容积率', 'green_rate' => '绿化率', 'introduction' => '楼盘简介', 'facilities' => '周边配套', 'bus_line' => '公交', 'subway' => '地铁', 'primary_school' => '小学', 'high_school' => '中学', 'property_fee' => '物业费', 'b_map_x' => '百度坐标X', 'b_map_y' => '百度坐标Y', 'averprice' => '均价', 'streetid' => '板块ID', 'build_type' => '物业业态', 'alias' => '楼盘别名'
    );

    if (1 == $explain) {
      $fieldarr['totalnum'] = '楼盘总量';
      $fieldarr['district'] = '区属名称';
      $fieldarr['street'] = '板块名称';
      $fieldarr['type_text'] = '楼盘类型名称';
      $msg = array(
        'result' => 1,
        'msg' => '解释接口字段',
        'data' => $fieldarr
      );
    } else {
      $this->load->model('community_model');//楼盘模型类

      $select_field = array_flip($fieldarr);
      $where = array('status' => 2);
      if ($lastid > 0) {
        $where['id >'] = $lastid;
      }

      $totalnum = $this->community_model->getcommunitynum($where, array());

      $this->community_model->set_select_fields($select_field);
      $num = 0 == $num ? $totalnum : $num;
      $arr = $this->community_model->getcommunity2($where, array(), $num);

      $data = array();
      if (is_full_array($arr)) {
        $dist = $this->get_dist(1);
        $street = $this->get_street(1);
        foreach ($arr as $key => $value) {
          $value['district'] = $dist[$value['dist_id']]['district'];
          $value['street'] = $street[$value['streetid']]['streetname'];
          switch ($value['type']) {
            case 1 :
              $value['type_text'] = '住宅';
              break;
            case 2 :
              $value['type_text'] = '别墅';
              break;
            case 3 :
              $value['type_text'] = '商铺';
              break;
            case 4 :
              $value['type_text'] = '写字楼';
              break;
            case 5 :
              $value['type_text'] = '厂房';
              break;
            case 6 :
              $value['type_text'] = '仓库';
              break;
            case 7 :
              $value['type_text'] = '车库';
              break;
          }
          $data[] = $value;

          unset($arr[$key]);
        }
      }

      $msg = array(
        'result' => 1,
        'msg' => '获取楼盘数据',
        'data' => array('totalnum' => $totalnum, 'data' => $data)
      );
    }

    echo json_encode($msg);
  }
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
