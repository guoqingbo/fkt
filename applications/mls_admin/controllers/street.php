<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Street extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('district_model');
    $this->load->model('city_model');
    $this->load->model('relation_tp_street_model');
  }

  /**
   * 列表页面
   */
  public function index()
  {
    $data['title'] = '板块列表';
    $data['conf_where'] = 'index';
    //获得所有区属
    $data['all_district'] = $this->district_model->get_district();
    //筛选条件
    $data['where_cond'] = array();
    $dist_id = intval($this->input->post('dist_id'));
    if (!empty($dist_id)) {
      $data['where_cond']['dist_id'] = $dist_id;
    }
    //分页开始
    $data['district_num'] = $this->district_model->get_street_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['street_list'] = $this->district_model->get_street($data['where_cond'], $data['offset'], $data['pagesize']);
    //数据重构
    $data['street_list2'] = array();
    foreach ($data['street_list'] as $k => $v) {
      $dist_data = $this->district_model->get_district_by_id($v['dist_id']);
      $v['dist_name'] = $dist_data[0]['district'];
      $data['street_list2'][] = $v;
    }
    $this->load->view('street/index', $data);
  }

  /**
   * 添加板块
   */
  public function add()
  {
    $data['title'] = '添加板块';
    $data['conf_where'] = 'index';
    //获得所有区属
    $data['all_district'] = $this->district_model->get_district();
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    //获取58、赶集的区属
    $data['wb_district'] = $this->relation_tp_street_model->select_tp_district(1);
    $data['gj_district'] = $this->relation_tp_street_model->select_tp_district(2);
    //print_r($data['gj_district']);
    if ('add' == $submit_flag) {
      $date = array(
        'street_name' => trim($this->input->post('streetname')),
        'dist_id' => intval($this->input->post('dist_id')),
        'wuba_dist_id' => intval($this->input->post('wb_dist')),
        'wuba_street_id' => intval($this->input->post('wb_street')),
        'ganji_dist_id' => intval($this->input->post('gj_dist')),
        'ganji_street_id' => intval($this->input->post('gj_street'))
      );
      $paramArray = array(
        'streetname' => trim($this->input->post('streetname')),
        'name_spell' => trim($this->input->post('name_spell')),
        'dist_id' => intval($this->input->post('dist_id')),
        'order' => intval($this->input->post('order')),
        'is_show' => intval($this->input->post('is_show')),
        'b_map_x' => trim($this->input->post('b_map_x')),
        'b_map_y' => trim($this->input->post('b_map_y')),
      );
      if (!empty($paramArray['streetname']) && !empty($paramArray['is_show'])) {
        $addResult = $this->district_model->add_street($paramArray);
        $date['street_id'] = $addResult;
        $this->relation_tp_street_model->add_relation_street($date);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }
    $city = $_SESSION['esfdatacenter']['city'];
    switch ($city) {
      //苏州
      case 'sz':
        $lng = 120.602085;
        $lat = 31.303672;
        $city_name = '苏州';
        break;
      //无锡
      case 'wx':
        $lng = 120.31293;
        $lat = 31.563145;
        $city_name = '无锡';
        break;
      //杭州
      case 'hz':
        $lng = 120.165333;
        $lat = 30.269226;
        $city_name = '杭州';
        break;
      //合肥
      case 'hf':
        $lng = 117.282699;
        $lat = 31.866942;
        $city_name = '合肥';
        break;
      //西安
      case 'xa':
        $lng = 108.953098;
        $lat = 34.2778;
        $city_name = '西安';
        break;
      //石家庄
      case 'sjz':
        $lng = 114.504155;
        $lat = 38.052094;
        $city_name = '石家庄';
        break;
      //昆明
      case 'km':
        $lng = 102.707436;
        $lat = 25.041879;
        $city_name = '昆明';
        break;
      //哈尔滨
      case 'hrb':
        $lng = 126.553915;
        $lat = 45.807982;
        $city_name = '哈尔滨';
        break;
      //郑州
      case 'zz':
        $lng = 113.633649;
        $lat = 34.759419;
        $city_name = '郑州';
        break;
      //天津
      case 'tj':
        $lng = 117.182342;
        $lat = 39.142191;
        $city_name = '天津';
        break;
      //沈阳
      case 'sy':
        $lng = 123.438975;
        $lat = 41.81134;
        $city_name = '沈阳';
        break;
      //武汉
      case 'wuhan':
        $lng = 114.28366;
        $lat = 30.598179;
        $city_name = '武汉';
        break;
      //长春
      case 'cc':
        $lng = 125.338794;
        $lat = 43.868592;
        $city_name = '长春';
        break;
      //常州
      case 'cz':
        $lng = 119.981861;
        $lat = 31.813628;
        $city_name = '常州';
        break;
      //重庆
      case 'cq':
        $lng = 106.555931;
        $lat = 29.566222;
        $city_name = '重庆';
        break;
      //芜湖
      case 'wh':
        $lng = 118.381233;
        $lat = 31.329506;
        $city_name = '芜湖';
        break;
      //昆山
      case 'ks':
        $lng = 120.98593;
        $lat = 31.384196;
        $city_name = '昆山';
        break;
      //兰州
      case 'lz':
        $lng = 103.814821;
        $lat = 36.066612;
        $city_name = '兰州';
        break;
      //南京
      case 'nj':
        $lng = 118.799921;
        $lat = 32.060174;
        $city_name = '南京';
        break;
      //上海
      case 'sh':
        $lng = 121.471613;
        $lat = 31.236552;
        $city_name = '上海';
        break;
      //成都
      case 'cd':
        $lng = 118.805652;
        $lat = 32.072333;
        $city_name = '成都';
        break;
      //默认为南京
      default:
        $lng = 118.799921;
        $lat = 32.060174;
        $city_name = '南京';
        break;
    }
    $data['city_name'] = $city_name;
    $data['lng'] = $lng;
    $data['lat'] = $lat;
    $data['addResult'] = $addResult;
    $this->load->view('street/add', $data);
  }

  /**
   * 页面ajax请求根据属区获得对应板块
   */
  public function find_street_bydis()
  {
    $districtID = $_POST['districtID'];
    $type = $_POST['type'];
    if ($districtID && $type) {
      $street = $this->relation_tp_street_model->select_id_street($districtID, $type);
      echo json_encode($street);
    } else {
      echo json_encode(array('result' => 'no result'));
    }
  }

  /**
   * 修改区属
   */
  public function modify($id)
  {
    //获取58、赶集的区属
    $data['wb_district'] = $this->relation_tp_street_model->select_tp_district(1);
    $data['gj_district'] = $this->relation_tp_street_model->select_tp_district(2);
    $message = $this->relation_tp_street_model->select_relation_street($id);
    $data['message'] = $message[0];
    //获取58、感激对应的板块
    $data['wb_street'] = $this->relation_tp_street_model->select_id_street($message[0]['wuba_dist_id'], 1);
    $data['gj_street'] = $this->relation_tp_street_model->select_id_street($message[0]['ganji_dist_id'], 2);
    //print_r($data['wb_street']);
    $data['title'] = '修改板块';
    $data['conf_where'] = 'index';
    //获得所有区属
    $data['all_district'] = $this->district_model->get_district();

    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $street_data = $this->district_model->get_street_by_id($id);
      if (!empty($street_data[0]) && is_array($street_data[0])) {
        $data['street_data'] = $street_data[0];
      }
    }
    if ('modify' == $submit_flag) {
      $date = array(
        'street_name' => trim($this->input->post('streetname')),
        'dist_id' => intval($this->input->post('dist_id')),
        'wuba_dist_id' => intval($this->input->post('wb_dist')),
        'wuba_street_id' => intval($this->input->post('wb_street')),
        'ganji_dist_id' => intval($this->input->post('gj_dist')),
        'ganji_street_id' => intval($this->input->post('gj_street')),
      );
      $paramArray = array(
        'streetname' => trim($this->input->post('streetname')),
        'name_spell' => trim($this->input->post('name_spell')),
        'dist_id' => intval($this->input->post('dist_id')),
        'order' => intval($this->input->post('order')),
        'is_show' => intval($this->input->post('is_show')),
        'b_map_x' => trim($this->input->post('b_map_x')),
        'b_map_y' => trim($this->input->post('b_map_y')),
      );
      if (!empty($paramArray['streetname']) && !empty($paramArray['is_show'])) {
        $modifyResult = $this->district_model->modify_street($id, $paramArray);
        $this->relation_tp_street_model->update_relation_street($id, $date);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }
    $city = $_SESSION['esfdatacenter']['city'];
    switch ($city) {
      //苏州
      case 'sz':
        $lng = 120.602085;
        $lat = 31.303672;
        $city_name = '苏州';
        break;
      //无锡
      case 'wx':
        $lng = 120.31293;
        $lat = 31.563145;
        $city_name = '无锡';
        break;
      //杭州
      case 'hz':
        $lng = 120.165333;
        $lat = 30.269226;
        $city_name = '杭州';
        break;
      //合肥
      case 'hf':
        $lng = 117.282699;
        $lat = 31.866942;
        $city_name = '合肥';
        break;
      //西安
      case 'xa':
        $lng = 108.953098;
        $lat = 34.2778;
        $city_name = '西安';
        break;
      //石家庄
      case 'sjz':
        $lng = 114.504155;
        $lat = 38.052094;
        $city_name = '石家庄';
        break;
      //昆明
      case 'km':
        $lng = 102.707436;
        $lat = 25.041879;
        $city_name = '昆明';
        break;
      //哈尔滨
      case 'hrb':
        $lng = 126.553915;
        $lat = 45.807982;
        $city_name = '哈尔滨';
        break;
      //郑州
      case 'zz':
        $lng = 113.633649;
        $lat = 34.759419;
        $city_name = '郑州';
        break;
      //天津
      case 'tj':
        $lng = 117.182342;
        $lat = 39.142191;
        $city_name = '天津';
        break;
      //沈阳
      case 'sy':
        $lng = 123.438975;
        $lat = 41.81134;
        $city_name = '沈阳';
        break;
      //武汉
      case 'wuhan':
        $lng = 114.28366;
        $lat = 30.598179;
        $city_name = '武汉';
        break;
      //长春
      case 'cc':
        $lng = 125.338794;
        $lat = 43.868592;
        $city_name = '长春';
        break;
      //常州
      case 'cz':
        $lng = 119.981861;
        $lat = 31.813628;
        $city_name = '常州';
        break;
      //重庆
      case 'cq':
        $lng = 106.555931;
        $lat = 29.566222;
        $city_name = '重庆';
        break;
      //芜湖
      case 'wh':
        $lng = 118.381233;
        $lat = 31.329506;
        $city_name = '芜湖';
        break;
      //昆山
      case 'ks':
        $lng = 120.98593;
        $lat = 31.384196;
        $city_name = '昆山';
        break;
      //兰州
      case 'lz':
        $lng = 103.814821;
        $lat = 36.066612;
        $city_name = '兰州';
        break;
      //南京
      case 'nj':
        $lng = 118.799921;
        $lat = 32.060174;
        $city_name = '南京';
        break;
      //上海
      case 'sh':
        $lng = 121.471613;
        $lat = 31.236552;
        $city_name = '上海';
        break;
      //成都
      case 'cd':
        $lng = 118.805652;
        $lat = 32.072333;
        $city_name = '成都';
        break;
      //默认为南京
      default:
        $lng = 118.799921;
        $lat = 32.060174;
        $city_name = '南京';
        break;
    }
    $data['city_name'] = $city_name;
    $data['lng'] = $lng;
    $data['lat'] = $lat;
    $data['modifyResult'] = $modifyResult;
    $this->load->view('street/modify', $data);
  }

  /**
   * 修改城市状态
   */
  public function change_status($id, $status)
  {
    $data['title'] = '修改状态';
    $modifyResult = '';
    if (!empty($id) && !empty($status)) {
      $paramArray = array(
        'status' => intval($status),
      );
      $modifyResult = $this->city_model->modify_city(intval($id), $paramArray);
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('city/change_status', $data);
  }

  /**
   * 删除节点
   */
  public function del($id)
  {
    $data['title'] = '删除用户组';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $userData = $this->user_group_model->del_user_group($id);
      if ($userData == 1) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('purview_node/del', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
