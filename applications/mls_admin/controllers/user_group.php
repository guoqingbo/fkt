<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_group extends My_Controller
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
        $data['where_cond']['status'] = 1;
    }
    //分页开始
    $data['node_num'] = $this->user_group_model->get_user_group_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['node_num'] ? ceil($data['node_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['user_group_list'] = $this->user_group_model->get_user_group($data['where_cond'], $data['offset'], $data['pagesize']);
    $user_group_list2 = array();
    foreach ($data['user_group_list'] as $k => $v) {
      $city_data = $this->city_model->get_by_id($v['city_id']);
      $v['city_name'] = $city_data['cityname'];
      $user_group_list2[] = $v;
    }
    $data['user_group_list2'] = $user_group_list2;
    $this->load->view('user_group/index', $data);
  }

  /**
   * 添加用户组
   */
  public function add()
  {
    $data['title'] = '添加节点';
    $data['conf_where'] = 'index';
    $data['city'] = $this->city_model->get_all_city();
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'name' => trim($this->input->post('name')),
        'city_id' => trim($this->input->post('city')),
        'order' => trim($this->input->post('order')),
      );
      if (!empty($paramArray['name'])) {
        $addResult = $this->user_group_model->add_user_group($paramArray);
      } else {
        $data['mess_error'] = '用户组名称不能为空';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('user_group/add', $data);
  }

  /**
   * 修改节点
   */
  public function modify($id)
  {
    $data['title'] = '修改用户组';
    $data['conf_where'] = 'index';
    $data['father_node'] = $this->purview_father_node_model->get_base_node();
    $data['city'] = $this->city_model->get_all_city();
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $user_group_data = $this->user_group_model->get_user_group_by_id($id);
      if (!empty($user_group_data[0]) && is_array($user_group_data[0])) {
        $data['user_group_data'] = $user_group_data[0];
      }
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'name' => trim($this->input->post('name')),
        'city_id' => trim($this->input->post('city_id')),
        'order' => trim($this->input->post('order')),
      );
      if (!empty($paramArray['name'])) {
        $modifyResult = $this->user_group_model->modify_user_group($id, $paramArray);
      } else {
        $data['mess_error'] = '用户组名称不能为空';
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('user_group/modify', $data);
  }

  /**
   *  修改权限节点
   */
  public function modify_purview_node($id)
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'index';
    $data['p_ids'] = $this->purview_node_model->get_pid();
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    //获取所有权限菜单节点，数据重构
    $purview_node_arr = array();
    foreach ($data['p_ids'] as $k => $v) {
      $p_data = $this->purview_father_node_model->get_node_by_id($v['p_id']);
      $v['p_name'] = $p_data[0]['name'];
      $v['purview_node_children'] = $this->purview_node_model->get_base_node(array('p_id' => $v['p_id']));
      $purview_node_arr[] = $v;
    }
    $data['purview_node_arr'] = $purview_node_arr;
    //获得当前用户组的权限菜单
    $user_group_data = $this->user_group_model->get_user_group_by_id($id);
    $data['this_purview_nodes'] = $user_group_data[0]['purview_nodes'];
    //提交动作
    if ('modify' == $submit_flag) {
      $purview_nodes_arr = $this->input->post('purview_nodes');
      if (!empty($purview_nodes_arr)) {
        $purview_nodes_str = ',' . implode(',', $purview_nodes_arr) . ',';
      } else {
        $purview_nodes_str = '';
      }
      $paramArray = array('purview_nodes' => $purview_nodes_str);
      $modifyResult = $this->user_group_model->modify_user_group($id, $paramArray);
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('user_group/modify_purview_node', $data);
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
