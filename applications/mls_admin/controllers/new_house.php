<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 新房房源管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class New_house extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //查看所有的模块
    $this->load->helper('page_helper');
    $this->load->model('new_house_model');
    $this->load->helper('user_helper');
    $this->load->helper('common_load_source_helper');
    $this->load->model('house_config_model');
  }

  public function index($id = "", $is_over = "")
  {
    $data = array();
    $data['title'] = "新房管理";
    if ($id !== "" && $is_over !== "") {
      $this->new_house_model->sure_is_over($id, $is_over);
    }
    $pg = $this->input->post("pg", true);
    if ($pg == "") {
      $pg = 1;
    }
    $search = $this->input->post("search", true);
    if ($search) {
      $id = $this->input->post('id', true);
      $title = $this->input->post('title', true);
      $phone = $this->input->post('phone', true);
      $district_id = $this->input->post('district_id', true);
      $street_id = $this->input->post('street_id', true);
      $type = $this->input->post('type', true);
      $renovation = $this->input->post('renovation', true);
      $room = $this->input->post('room', true);
      $hall = $this->input->post('hall', true);
      $toilet = $this->input->post('toilet', true);
      $kitchen = $this->input->post('kitchen', true);
      $is_over = $this->input->post('is_over', true);

      //主力户型
      if ($room !== "" && $hall !== "" && $toilet !== "" && $kitchen !== "") {
        $arr = array($room, $hall, $toilet, $kitchen);
        $apartment = implode("-", $arr);
      } else {
        $apartment = "";
      }
      $where = "";
      if ($id) {
        $where .= " and id = " . $id;
      }
      if ($title) {
        $where .= " and title = " . $title;
      }
      if ($phone) {
        $where .= " and phone = " . $phone;
      }
      if ($district_id) {
        $where .= " and district_id = " . $district_id;
      }
      if ($street_id) {
        $where .= " and street_id = " . $street_id;
      }
      if ($type) {
        $where .= " and type = " . $type;
      }
      if ($renovation) {
        $where .= " and renovation = " . $renovation;
      }
      if ($apartment) {
        $where .= " and apartment = '" . $apartment . "'";
      }
      if ($is_over) {
        $where .= " and is_over = " . $is_over;
      }

      //清除条件头尾多余的“AND”和空格
      $where = trim($where);
      $where = trim($where, "and");
      $where = trim($where);
    }
    //分页开始
    $data['sold_num'] = $this->new_house_model->get_house_num_by($where);

    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['list'] = $this->new_house_model->get_all($where, $data['offset'], $data['pagesize'], 'is_over', 'ASC');
    //区域
    $data['district'] = $this->new_house_model->get_district();

    $data['house_config'] = $this->house_config_model->get_config();
    $data['params'] = array(
      'id' => $id,
      'title' => $title,
      'phone' => $phone,
      'district_id' => $district_id,
      'street_id' => $street_id,
      'type' => $type,
      'renovation' => $renovation,
      'room' => $room,
      'hall' => $hall,
      'kitchen' => $kitchen,
      'toilet' => $toilet,
      'is_over' => $is_over);
    $this->load->view('new_house/index', $data);
  }


  public function add()
  {
    $data = array();
    $setinfo = "";
    $data['title'] = "新房房源发布";
    //form 表单提交
    $submit_flag = $this->input->post('submit_flag', true);
    if ($submit_flag == "add") {
      $time = time();
      $uptime = time();
      $room = $this->input->post('room', true);
      $hall = $this->input->post('hall', true);
      $toilet = $this->input->post('toilet', true);
      $kitchen = $this->input->post('kitchen', true);
      $balcony = $this->input->post('balcony', true);
      $array = array($room, $hall, $toilet, $kitchen, $balcony);
      $apartment = implode("-", $array);

      $img_arr1 = $this->input->post("p_filename2");
      if (!empty($img_arr1)) {
        $face_img = implode(",", $img_arr1);
      }

      $img_arr2 = $this->input->post("p_filename1");
      if (!empty($img_arr2)) {
        $hx_imgurl = implode(",", $img_arr2);
      }
      $insert_array = array(
        'title' => $this->input->post('title', true),
        'phone' => $this->input->post('phone', true),
        'district_id' => $this->input->post('district', true),
        'street_id' => $this->input->post('street', true),
        'type' => $this->input->post('type', true),
        'renovation' => $this->input->post('renovation', true),
        'green' => $this->input->post('green', true),
        'open_time' => $this->input->post('open_time', true),
        'give_time' => $this->input->post('give_time', true),
        'price' => $this->input->post('price', true),
        'property' => $this->input->post('property', true),
        'wy_company' => $this->input->post('wy_company', true),
        'developers' => $this->input->post('developers', true),
        'devurl' => $this->input->post('devurl', true),
        'wy_addr' => $this->input->post('wy_addr', true),
        'address' => $this->input->post('address', true),
        'tfloor' => $this->input->post('tfloor', true),
        'parking' => $this->input->post('parking', true),
        'covered' => $this->input->post('covered', true),
        'speed' => $this->input->post('speed', true),
        'chanquan' => $this->input->post('chanquan', true),
        'households' => $this->input->post('households', true),
        'is_sell' => $this->input->post('is_sell', true),
        'shtick' => $this->input->post('shtick', true),
        'apartment' => $apartment,
        'face_img' => $face_img,
        'hx_imgurl' => $hx_imgurl,
        'keywords' => $this->input->post('keywords', true),
        'pubtime' => $time,
        'up_time' => $time,
        'b_map_x' => $this->input->post('b_map_x', true),
        'b_map_y' => $this->input->post('b_map_y', true),
        'detail' => $this->input->post('detail', true),
        'prefer' => $this->input->post('prefer', true),
        'face_img' => $face_img,
        'hx_imgurl' => $hx_imgurl
      );

      $setinfo = $this->new_house_model->add_data($insert_array);
    }
    $data['district'] = $this->new_house_model->get_district();
    $data['house_config'] = $this->house_config_model->get_config();
    $data['setinfo'] = $setinfo;

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
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->load->view('new_house/add', $data);
  }

  public function get_street()
  {
    $dist_id = $this->input->get("district_id", true);
    $result = $this->new_house_model->get_street($dist_id);
    echo json_encode($result);
  }

  public function del($id)
  {
    $data = array();
    $result = $this->new_house_model->del_by($id);
    $data['result'] = $result;
    $this->load->view("new_house/del", $data);
  }

  public function info($id)
  {
    $data = array();
    $data['title'] = "新房房源详情";
    $data['list'] = $this->new_house_model->get_all_by($id);
    $data['street'] = $this->new_house_model->get_street($data['list']['district_id']);
    $setinfo = "";
    //form 表单提交
    $submit_flag = $this->input->post('submit_flag', true);
    if ($submit_flag == "save") {
      $uptime = time();
      $room = $this->input->post('room', true);
      $hall = $this->input->post('hall', true);
      $toilet = $this->input->post('toilet', true);
      $kitchen = $this->input->post('kitchen', true);
      $balcony = $this->input->post('balcony', true);
      $array = array($room, $hall, $toilet, $kitchen, $balcony);
      $apartment = implode("-", $array);

      $img_arr1 = $this->input->post("p_filename2");
      if (!empty($img_arr1)) {
        $face_img = implode(",", $img_arr1);
      }

      $img_arr2 = $this->input->post("p_filename1");
      if (!empty($img_arr2)) {
        $hx_imgurl = implode(",", $img_arr2);
      }
      $insert_array = array(
        'title' => $this->input->post('title', true),
        'phone' => $this->input->post('phone', true),
        'district_id' => $this->input->post('district', true),
        'street_id' => $this->input->post('street', true),
        'type' => $this->input->post('type', true),
        'renovation' => $this->input->post('renovation', true),
        'green' => $this->input->post('green', true),
        'open_time' => $this->input->post('open_time', true),
        'give_time' => $this->input->post('give_time', true),
        'price' => $this->input->post('price', true),
        'property' => $this->input->post('property', true),
        'wy_company' => $this->input->post('wy_company', true),
        'developers' => $this->input->post('developers', true),
        'devurl' => $this->input->post('devurl', true),
        'wy_addr' => $this->input->post('wy_addr', true),
        'address' => $this->input->post('address', true),
        'tfloor' => $this->input->post('tfloor', true),
        'parking' => $this->input->post('parking', true),
        'covered' => $this->input->post('covered', true),
        'speed' => $this->input->post('speed', true),
        'chanquan' => $this->input->post('chanquan', true),
        'households' => $this->input->post('households', true),
        'is_sell' => $this->input->post('is_sell', true),
        'shtick' => $this->input->post('shtick', true),
        'apartment' => $apartment,
        'face_img' => $face_img,
        'hx_imgurl' => $hx_imgurl,
        'keywords' => $this->input->post('keywords', true),
        'up_time' => $uptime,
        'b_map_x' => $this->input->post('b_map_x', true),
        'b_map_y' => $this->input->post('b_map_y', true),
        'detail' => $this->input->post('detail', true),
        'prefer' => $this->input->post('prefer', true),
        'face_img' => $face_img,
        'hx_imgurl' => $hx_imgurl
      );
      $setinfo = $this->new_house_model->update_data($id, $insert_array);
    }
    $data['setinfo'] = $setinfo;
    $data['district'] = $this->new_house_model->get_district();
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->load->view('new_house/info', $data);
  }


}
