<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 权限菜单管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Permission_tab_menu extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('permission_module_model');
    $this->load->model('permission_tab_menu_model');
    $this->load->helper('user_helper');
  }

  /**
   * 权限菜单列表页面
   */
  public function index($module_id = 0)
  {
    $data['title'] = '权限一级菜单设置管理';
    $data['conf_where'] = 'index';
    //权限模块数据
    $data['module_list'] = $this->permission_module_model->get_all_by("", -1);
    $where = "";
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    $module_id = isset($post_param['module_id']) ? $post_param['module_id'] : $module_id;
    if ($module_id) {
      $where = "module_id = {$module_id}";
    }
    $data['module_id'] = $module_id;
    //分页开始
    $data['permission_tab_menu_num'] = $this->permission_tab_menu_model->count_by($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['permission_tab_menu_num'] ? ceil($data['permission_tab_menu_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    //获取权限所有节点
    $permission_list = $this->permission_tab_menu_model->get_all_list();
    //print_r($permission_list);
    $data['permission_list'] = $permission_list;
    $data['permission_tab_menu'] = $this->permission_tab_menu_model->get_list_by($where, $data['offset'], $data['pagesize']);
    $this->load->view('permission_tab_menu/index', $data);
  }

  /**
   * 添加权限菜单
   */
  public function add()
  {
    $data['title'] = '添加权限菜单';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'module_id' => intval($this->input->post('module_id')),
        'name' => trim($this->input->post('name')),
        'url' => trim($this->input->post('url')),
        'is_display' => intval($this->input->post('is_display')),
        'icon' => trim($this->input->post('icon')),
        'order' => trim($this->input->post('order')),
        'pid' => trim($this->input->post('pid')),
      );
      if (!empty($paramArray['module_id']) && !empty($paramArray['name']) && !empty($paramArray['url'])) {
        $addResult = $this->permission_tab_menu_model->insert($paramArray);
      } else {
        $data['mess_error'] = '权限模块/菜单名称/链接地址不能为空';
      }
    }
    //获取权限所有节点
    $permission_list = $this->permission_tab_menu_model->get_all_list();
    //print_r($permission_list);
    $data['permission_list'] = $permission_list;

    //权限模块数据
    $data['module_list'] = $this->permission_module_model->get_all_by("", -1);
    $data['addResult'] = $addResult;
    $this->load->view('permission_tab_menu/add', $data);
  }

  /**
   * 修改权限菜单
   */
  public function modify($id)
  {
    $data['title'] = '修改权限菜单';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $data['permission_tab_menu'] = $this->permission_tab_menu_model->get_by_id($id);
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'module_id' => intval($this->input->post('module_id')),
        'name' => trim($this->input->post('name')),
        'url' => trim($this->input->post('url')),
        'is_display' => intval($this->input->post('is_display')),
        'icon' => trim($this->input->post('icon')),
        'order' => trim($this->input->post('order')),
        'pid' => trim($this->input->post('pid')),
      );
      if (!empty($paramArray['module_id']) && !empty($paramArray['name']) && !empty($paramArray['url'])) {
        $modifyResult = $this->permission_tab_menu_model->update_by_id($paramArray, $id);
      } else {
        $data['mess_error'] = '权限模块/菜单名称/链接地址不能为空';
      }
    }
    //权限模块数据
    $data['module_list'] = $this->permission_module_model->get_all_by("", -1);
    //获取权限所有节点
    $permission_list = $this->permission_tab_menu_model->get_all_list();
    //print_r($permission_list);
    $data['permission_list'] = $permission_list;

    $data['modifyResult'] = $modifyResult;
    $this->load->view('permission_tab_menu/modify', $data);
  }

  /**
   * 删除权限菜单
   */
  public function del($id)
  {
    $data['title'] = '删除权限菜单';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $permission_tab_menuData = $this->permission_tab_menu_model->delete_by_id($id);
      if ($permission_tab_menuData) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('permission_tab_menu/del', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
