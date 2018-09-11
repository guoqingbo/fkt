<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Metro_site extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('metro_model');
    $this->load->model('city_model');
  }

  /**
   * 列表页面
   */
  public function index()
  {
    $data['title'] = '地铁站点列表';
    $data['conf_where'] = 'index';
    //获得所有线路
    $data['all_metro_line'] = $this->metro_model->get_metro_line();
    //筛选条件
    $data['where_cond'] = array();
    $metro_id = intval($this->input->post('metro_id'));
    if (!empty($metro_id)) {
      $data['where_cond']['metro_id'] = $metro_id;
    }
    //分页开始
    $data['metro_site_num'] = $this->metro_model->get_metro_site_num($data['where_cond']);
    //echo $data['metro_site_num'];
    $data['pagesize'] = 30;//设定每一页显示的记录数
    $data['pages'] = $data['metro_site_num'] ? ceil($data['metro_site_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['metro_site_list'] = $this->metro_model->get_metro_site($data['where_cond'], $data['offset'], $data['pagesize']);
    //数据重构
    $data['metro_site_list2'] = array();
    foreach ($data['metro_site_list'] as $k => $v) {
      $metro_data = $this->metro_model->get_metro_line_by_id($v['metro_id']);
      //echo '<pre>';print_r($metro_data);
      $v['line_name'] = $metro_data[0]['line_name'];
      $data['metro_site_list2'][] = $v;
    }
    //echo $data['pages'];
    $this->load->view('metro/metro_site', $data);
  }

  /**
   * 添加站点
   */
  public function add()
  {
    $data['title'] = '添加地铁站点';
    $data['conf_where'] = 'index';
    //获得所有线路
    $data['all_metro_line'] = $this->metro_model->get_metro_line();
    //print_r($data['all_metro_line']);
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'site_name' => trim($this->input->post('site_name')),
        'metro_id' => intval($this->input->post('metro_id')),
        'order' => intval($this->input->post('order')),
        'is_show' => intval($this->input->post('is_show')),
        'line_center_point' => $this->input->post('line_center_point'),
        'b_map_x' => trim($this->input->post('b_map_x')),
        'b_map_y' => trim($this->input->post('b_map_y')),
      );
      //判断地铁线路中心站台只能设一个
      $line_center_point_status = true;
      if ($paramArray['line_center_point'] == 1) {
        $site_where = array('metro_id' => $paramArray['metro_id'],
          'line_center_point' => $paramArray['line_center_point']);
        $line_center_point = $this->metro_model->get_metro_site_num($site_where, 'db_city');
        if ($line_center_point >= 1) {
          $data['mess_error'] = '一条线路只能设置一个中心点站点';
          $line_center_point_status = false;
        }
      }
      if ($line_center_point_status) {
        if (!empty($paramArray['site_name']) && !empty($paramArray['is_show'])) {
          $addResult = $this->metro_model->add_metro_site($paramArray);
        } else {
          $data['mess_error'] = '带*为必填字段';
        }
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
    $this->load->view('metro/site_add', $data);
  }

  /**
   * 修改站点名
   */
  public function modify($id)
  {
    $data['title'] = '修改地铁站点名';
    $data['conf_where'] = 'index';
    //获得所有区属
    $data['all_metro_line'] = $this->metro_model->get_metro_line();

    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $site_data = $this->metro_model->get_metro_site_by_id($id);
      if (!empty($site_data[0]) && is_array($site_data[0])) {
        $data['site_data'] = $site_data[0];
      }
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'site_name' => trim($this->input->post('site_name')),
        'metro_id' => intval($this->input->post('metro_id')),
        'order' => intval($this->input->post('order')),
        'is_show' => intval($this->input->post('is_show')),
        'line_center_point' => $this->input->post('line_center_point'),
        'b_map_x' => trim($this->input->post('b_map_x')),
        'b_map_y' => trim($this->input->post('b_map_y')),
      );
      //判断地铁线路中心站台只能设一个
      $line_center_point_status = true;
      if ($paramArray['line_center_point'] == '1') {
        $site_where = array('metro_id' => $paramArray['metro_id'],
          'line_center_point' => $paramArray['line_center_point'],
          'id <>' => $id);
        $line_center_point = $this->metro_model->get_metro_site_num($site_where, 'db_city');
        if ($line_center_point >= 1) {
          $data['mess_error'] = '一条线路只能设置一个中心点站点';
          $line_center_point_status = false;
        }
      }
      if ($line_center_point_status) {
        if (!empty($paramArray['site_name']) && !empty($paramArray['is_show'])) {
          $modifyResult = $this->metro_model->modify_metro_site($id, $paramArray);
        } else {
          $data['mess_error'] = '带*为必填字段';
        }
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
        $lng = 104.072528;
        $lat = 30.662865;
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
    $this->load->view('metro/site_modify', $data);
  }

  /**
   * 删除站点
   */
  public function del($id)
  {
    $data['title'] = '删除地铁站点';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $userData = $this->metro_model->del_metro_site($id);
      if ($userData == 1) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('metro/site_del', $data);
  }

  /**
   * 保存排序
   */
  public function save()
  {

  }

  /**
   * 修改城市状态
   */
  /*	public function change_status($id,$status){
          $data['title'] = '修改状态';
          $modifyResult = '';
          if(!empty($id)&&!empty($status)){
              $paramArray = array(
                  'status' => intval($status),
              );
              $modifyResult = $this->city_model->modify_city(intval($id),$paramArray);
          }
          $data['modifyResult'] = $modifyResult;
          $this->load->view('city/change_status',$data);
      }
  */


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
