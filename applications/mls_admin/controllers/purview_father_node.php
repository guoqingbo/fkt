<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purview_father_node extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('purview_father_node_model');
    $this->load->model('purview_node_model');
  }

  /**
   * 用户列表页面
   */
  public function index()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'index';
    //分页开始
    $data['node_num'] = $this->purview_father_node_model->get_base_node_num();
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['node_num'] ? ceil($data['node_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['father_node_list'] = $this->purview_father_node_model->get_base_node($data['offset'], $data['pagesize']);
    $this->load->view('purview_father_node/index', $data);
  }

  /**
   * 添加节点
   */
  public function add()
  {
    $data['title'] = '添加根节点';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'name' => trim($this->input->post('name')),
        'status' => trim($this->input->post('status')),
        'order' => trim($this->input->post('order')),
        'class_str' => trim($this->input->post('class_str')),
      );
      if (!empty($paramArray['name'])) {
        $addResult = $this->purview_father_node_model->add_node($paramArray);
      } else {
        $data['mess_error'] = '节点名称不能为空';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('purview_father_node/add', $data);
  }

  /**
   * 修改节点
   */
  public function modify($id)
  {
    $data['title'] = '修改节点';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $userData = $this->purview_father_node_model->get_node_by_id($id);
      if (!empty($userData[0]) && is_array($userData[0])) {
        $data['node'] = $userData[0];
      }
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'name' => trim($this->input->post('name')),
        'status' => trim($this->input->post('status')),
        'order' => trim($this->input->post('order')),
        'class_str' => trim($this->input->post('class_str')),
      );
      if (!empty($paramArray['name'])) {
        $modifyResult = $this->purview_father_node_model->modify_node($id, $paramArray);
      } else {
        $data['mess_error'] = '节点名称不能为空';
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('purview_father_node/modify', $data);
  }

  /**
   * 删除根节点
   */
  public function del($id)
  {
    $data['title'] = '删除节点';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      //删除根节点
      $del_result1 = $this->purview_father_node_model->del_node($id);
      //删除该根节点下的所有子节点
      $del_result2 = $this->purview_node_model->del_node_by_father_id($id);
      if ($del_result1 == 1) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('purview_father_node/del', $data);
  }
//
//    /**
//     * 城市切换
//     */
//    public function change_city($_city){
//        if(!empty($_city)){
//            $_SESSION[WEB_AUTH]["city"] = $_city;
//        }
//        header("Location: ".FRAME_INDEX);
//    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
