<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 权限菜单功能管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class signatory_secondtab_menu extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('signatory_purview_module_model');
    $this->load->model('signatory_basetab_menu_model');
    $this->load->model('signatory_purview_secondtab_menu_model');
    $this->load->helper('user_helper');
  }

  /**
   * 权限菜单功能列表页面
   */
  public function index($module_id = 0, $tab_id = 0)
  {
    $data['title'] = '权限二级菜单功能管理';
    $data['conf_where'] = 'index';
    //权限模块数据
    $data['module_list'] = $this->signatory_purview_module_model->get_all_by();
    $where = "";
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    $module_id = isset($post_param['module_id']) ? $post_param['module_id'] : $module_id;
    $data['module_id'] = $module_id;
    if ($module_id) {
      $data['tab_list'] = $this->signatory_basetab_menu_model->get_all_by("module_id = {$module_id}", -1);
      $tab_ids = array();
      $tab_ids[] = 0;
      foreach ($data['tab_list'] as $key => $val) {
        $tab_ids[] = $val["id"];
      }
      $tab_ids_str = implode(",", $tab_ids);
      $where = "tab_id in ({$tab_ids_str})";

    }
    $tab_id = isset($post_param['tab_id']) ? $post_param['tab_id'] : $tab_id;
    $data['tab_id'] = $tab_id;
    if ($tab_id) {
      $where = "tab_id = {$tab_id}";
    }
    //分页开始
    $data['permission_secondtab_menu_num'] = $this->signatory_purview_secondtab_menu_model->count_by($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['permission_secondtab_menu_num'] ? ceil($data['permission_secondtab_menu_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $permission_secondtab_menu = $this->signatory_purview_secondtab_menu_model->get_list_by($where, $data['offset'], $data['pagesize']);

    $data['permission_secondtab_menu'] = $permission_secondtab_menu;
    //获取权限所有节点
    $permission_list = $this->signatory_basetab_menu_model->get_all_list();
    //print_r($permission_list);
    $data['permission_list'] = $permission_list;
    $this->load->view('permission_secondtab_menu/index', $data);
  }

  /**
   * 添加权限菜单功能
   */
  public function add($module_id = 0, $tab_id = 0)
  {
    $data['title'] = '添加权限菜单功能';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'tab_id' => intval($this->input->post('tab_id')),
        'name' => trim($this->input->post('name')),
        'is_display' => intval($this->input->post('is_display')),
        'class' => trim($this->input->post('class')),
        'method' => trim($this->input->post('method')),
        'order' => trim($this->input->post('order')),
        'pid' => trim($this->input->post('pid')),
      );
      if (!empty($paramArray['tab_id']) && !empty($paramArray['name'])
        && !empty($paramArray['class']) && !empty($paramArray['method'])
      ) {
        $addResult = $this->signatory_purview_secondtab_menu_model->insert($paramArray);
      } else {
        $data['mess_error'] = '菜单/功能名称/类名/方法名不能为空';
      }
    }
    //权限模块数据
    $data['module_list'] = $this->signatory_purview_module_model->get_all_by("", -1);

    $data['module_id'] = $module_id;
    if ($module_id) {
      $data['tab_list'] = $this->signatory_basetab_menu_model->get_all_by("module_id = {$module_id}", -1);
    }
    $data['tab_id'] = $tab_id;
    //获取权限所有节点
    $permission_list = $this->signatory_basetab_menu_model->get_all_list();
    //print_r($permission_list);
    $data['permission_list'] = $permission_list;
    $data['addResult'] = $addResult;
    $this->load->view('permission_secondtab_menu/add', $data);
  }

  /**
   * 修改权限菜单功能
   */
  public function modify($id)
  {
    $data['title'] = '修改权限菜单功能';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $data['permission_secondtab_menu'] = $this->signatory_purview_secondtab_menu_model->get_by_id($id);
      $data['permission_secondtab_menu']['area_arr'] = $area_arr;

    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'tab_id' => intval($this->input->post('tab_id')),
        'name' => trim($this->input->post('name')),
        'is_display' => intval($this->input->post('is_display')),
        'class' => trim($this->input->post('class')),
        'method' => trim($this->input->post('method')),
        'order' => trim($this->input->post('order')),
        'pid' => trim($this->input->post('pid')),
      );
      if (!empty($paramArray['tab_id']) && !empty($paramArray['name'])
        && !empty($paramArray['class']) && !empty($paramArray['method'])
      ) {
        $modifyResult = $this->signatory_purview_secondtab_menu_model->update_by_id($paramArray, $id);
      } else {
        $data['mess_error'] = '菜单/功能名称/类名/方法名不能为空';
      }
    }
    //权限模块数据
    $data['module_list'] = $this->signatory_purview_module_model->get_all_by("init_auth = '0'", -1);
    $menu_info = $this->signatory_basetab_menu_model->get_by_id($data['permission_secondtab_menu']['tab_id']);
    $module_id = $menu_info['module_id'];
    $data['module_id'] = $module_id;
    if ($module_id) {
      $data['tab_list'] = $this->signatory_basetab_menu_model->get_all_by("module_id = {$module_id}", -1);
    }
    $data['modifyResult'] = $modifyResult;
    //获取权限所有节点
    $permission_list = $this->signatory_basetab_menu_model->get_all_list();
    $data['permission_list'] = $permission_list;
    $this->load->view('permission_secondtab_menu/modify', $data);
  }

  /**
   * 删除权限菜单功能
   */
  public function del($id, $module_id = 0, $tab_id = 0)
  {
    $data['title'] = '删除权限菜单功能';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $permission_secondtab_menuData = $this->signatory_purview_secondtab_menu_model->delete_by_id($id);
      if ($permission_secondtab_menuData) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['module_id'] = $module_id;
    $data['tab_id'] = $tab_id;
    $data['delResult'] = $delResult;
    $this->load->view('permission_secondtab_menu/del', $data);
  }

  /**
   * 取得菜单信息
   */
  public function get_tab_list()
  {
    $module_id = $this->input->get('module_id', TRUE);
    if ($module_id) {
      //菜单列表信息
      $this->signatory_basetab_menu_model->set_select_fields(array("id", "name"));
      $list = $this->signatory_basetab_menu_model->get_all_by(array("module_id" => $module_id), -1);
      if (is_full_array($list)) {
        $result = array('result' => 'ok', 'list' => $list);
      } else {
        $result = array('result' => 'no');
      }
    } else {
      $result = array('result' => 'no');
    }
    echo json_encode($result);
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
