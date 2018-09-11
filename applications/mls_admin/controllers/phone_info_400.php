<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Phone_info_400 extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('purview_father_node_model');
    $this->load->model('purview_node_model');
    $this->load->model('user_group_model');
    $this->load->model('city_model');
    $this->load->model('phone_info_400_model');
  }

  //刷入初始数据
  public function initial_data($type = 0, $start_num = 0, $end_num = 0)
  {
    ini_set("memory_limit", "80M");
    $start_num = intval($start_num);
    $end_num = intval($end_num);
    $insert_data = array();
    //内部号
    if (1 == $type) {
      $flag = 1;
    } else if (2 == $type) {
      //外部号
      $flag = 2;
    }
    if ($type > 0 && $start_num > 0 && $end_num > 0) {
      for ($i = $start_num; $i < $end_num; $i++) {
        $insert_data['flag'] = $flag;
        $insert_data['num_group'] = $i;
        $insert_data['status'] = 1;
        $result = $this->phone_info_400_model->insert_data($insert_data);
        var_dump($result);
        echo '---<br>';
        unset($insert_data);
        unset($result);
      }
    }
  }

  /**
   * 列表页面
   */
  public function index()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'index';
    $data['city'] = $this->city_model->get_all_city();
    //筛选条件
    $data['where_cond'] = array();
    $city_id = $this->input->post('city');
    if (!empty($city_id)) {
      $data['where_cond']['city_id'] = $city_id;
    }

    $where_str = 'city_id > 0 and flag = 1';
    if (!empty($data['where_cond']['city_id'])) {
      $where_str .= ' and city_id = "' . intval($data['where_cond']['city_id']) . '"';
    }

    //分页开始
    $data['node_num'] = $this->phone_info_400_model->get_phone_num($where_str);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['node_num'] ? ceil($data['node_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['user_group_list'] = $this->phone_info_400_model->get_phone($where_str, $data['offset'], $data['pagesize']);
    $user_group_list2 = array();
    foreach ($data['user_group_list'] as $k => $v) {
      $city_data = $this->city_model->get_by_id($v['city_id']);
      $v['city_name'] = $city_data['cityname'];
      $user_group_list2[] = $v;
    }
    $data['user_group_list2'] = $user_group_list2;
    $this->load->view('phone_info_400/index', $data);
  }

  /**
   * 列表页面
   */
  public function index2()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'index';

    $city_spell = $_SESSION[WEB_AUTH]['city'];
    $city_id_data = $this->city_model->get_city_by_spell($city_spell);
    $city_id = $city_id_data['id'];

    $where_str = 'flag = 1';
    if (!empty($city_id)) {
      $where_str .= ' and city_id = "' . $city_id . '"';
    }

    //分页开始
    $data['node_num'] = $this->phone_info_400_model->get_phone_num($where_str);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['node_num'] ? ceil($data['node_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['user_group_list'] = $this->phone_info_400_model->get_phone($where_str, $data['offset'], $data['pagesize']);
    $user_group_list2 = array();
    foreach ($data['user_group_list'] as $k => $v) {
      $city_data = $this->city_model->get_by_id($v['city_id']);
      $v['city_name'] = $city_data['cityname'];
      $user_group_list2[] = $v;
    }
    $data['user_group_list2'] = $user_group_list2;
    $this->load->view('phone_info_400/index2', $data);
  }

  /**
   * 添加
   */
  public function add()
  {
    $data['title'] = '分配号码';
    $data['conf_where'] = 'index';
    $data['city'] = $this->city_model->get_all_city();
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    $num_group = $this->input->post('num_group');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'city_id' => trim($this->input->post('city'))
      );
      if (!empty($num_group)) {
        //判断短号是否已有城市归属
        $where_cond = array(
          'id' => intval($num_group)
        );
        $phone_data = $this->phone_info_400_model->get_data_by_cond($where_cond);
        if (is_full_array($phone_data)) {
          if ($phone_data[0]['city_id'] == 0 && $phone_data[0]['status'] == 1) {
            $addResult = $this->phone_info_400_model->modify_phone(intval($num_group), $paramArray);
          } else {
            $data['mess_error'] = '该短号已有城市归属';
          }
        } else {
          $data['mess_error'] = '号码不能为空';
        }
      } else {
        $data['mess_error'] = '号码不能为空';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('phone_info_400/add', $data);
  }

  public function del($id = 0)
  {
    $data['title'] = '取消分配';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $paramArray = array(
        'status' => 1,
        'city_id' => 0,
        'phone' => ''
      );
      $userData = $this->phone_info_400_model->modify_phone(intval($id), $paramArray);
      if ($userData == 1) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('phone_info_400/del', $data);
  }

  public function modify($id = 0)
  {
    $data['title'] = '修改';
    $data['conf_where'] = 'index';
    $data['id'] = $id;
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $phone_info_data = $this->phone_info_400_model->get_data_by_cond(array('id' => $id));
      if (!empty($phone_info_data[0]) && is_array($phone_info_data[0])) {
        $data['phone_info_data'] = $phone_info_data[0];
      }
    }

    if ('modify' == $submit_flag) {
      $paramArray = array(
        'status' => 2,
        'phone' => trim($this->input->post('phone'))
      );
      if (!empty($paramArray['phone'])) {
        $modifyResult = $this->phone_info_400_model->modify_phone($id, $paramArray);
      } else {
        $data['mess_error'] = '绑定号码不能为空';
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('phone_info_400/modify', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
