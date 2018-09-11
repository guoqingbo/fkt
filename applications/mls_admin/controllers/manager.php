<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Manager extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('user_model');
    $this->load->model('user_group_model');
    $this->load->model('purview_node_model');
    $this->load->model('purview_father_node_model');
    $this->load->model('city_model');
  }

  public function index()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'index';
    //分页开始
    $data['user_num'] = $this->user_model->getusernum();
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['user_num'] ? ceil($data['user_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['user'] = $this->user_model->get_user_where_in(array('role', array(2, 3, 4)), $data['offset'], $data['pagesize']);
    $this->load->view('manager/index', $data);
  }

  /**
   * 设置管理员
   */
  public function set($method = '', $user_id = '')
  {
    $user_id = intval($user_id);
    $modifyResult = '';
    $is_set = true;
    if (is_int($user_id) && !empty($user_id)) {
      if ('site' == $method) {
        $paramlist = array(
          'role' => 3
        );
      } else if ('cancel' == $method) {
        $paramlist = array(
          'role' => 2
        );
      } else if ('site_city' == $method) {
        //判断该帐号归属的用户组，是否只属于一个城市
        $user_data = $this->user_model->getuserByid($user_id);
        if (is_full_array($user_data)) {
          $user_group_ids = trim($user_data[0]['user_group_ids'], ',');
          $user_group_arr = explode(',', $user_group_ids);
          if (is_full_array($user_group_arr)) {
            $where_arr = array('id', $user_group_arr);
            $city_id_arr = $this->user_group_model->get_cityid_by_groupid($where_arr);
            $city_count = count($city_id_arr);
          }
        }
        if (isset($city_count) && $city_count == 1) {
          $paramlist = array(
            'role' => 4
          );
        } else {
          $is_set = false;
        }
      }
      if ($is_set) {
        $modifyResult = $this->user_model->modifyuser($user_id, $paramlist);
      } else {
        $modifyResult = 0;
      }
    }
    $data['modifyResult'] = $modifyResult;
    $data['is_set'] = $is_set;
    $this->load->view('manager/modify', $data);
  }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
