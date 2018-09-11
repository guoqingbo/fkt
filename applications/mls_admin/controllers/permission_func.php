<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 权限菜单功能管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Permission_func extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('permission_module_model');
    $this->load->model('permission_menu_model');
    $this->load->model('permission_func_model');
    $this->load->helper('user_helper');
  }

  /**
   * 权限菜单功能列表页面
   */
  public function index($module_id = 0, $menu_id = 0)
  {
    $data['title'] = '权限菜单功能管理';
    $data['conf_where'] = 'index';
    //权限模块数据
    $data['module_list'] = $this->permission_module_model->get_all_by();
    $where = "";
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    $module_id = isset($post_param['module_id']) ? $post_param['module_id'] : $module_id;
    $data['module_id'] = $module_id;
    if ($module_id) {
      $data['menu_list'] = $this->permission_menu_model->get_all_by("module_id = {$module_id}", -1);
      $menu_ids = array();
      $menu_ids[] = 0;
      foreach ($data['menu_list'] as $key => $val) {
        $menu_ids[] = $val["id"];
      }
      $menu_ids_str = implode(",", $menu_ids);
      $where = "menu_id in ({$menu_ids_str})";

    }
    $menu_id = isset($post_param['menu_id']) ? $post_param['menu_id'] : $menu_id;
    $data['menu_id'] = $menu_id;
    if ($menu_id) {
      $where = "menu_id = {$menu_id}";
    }
    //分页开始
    $data['permission_func_num'] = $this->permission_func_model->count_by($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['permission_func_num'] ? ceil($data['permission_func_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $permission_func = $this->permission_func_model->get_list_by($where, $data['offset'], $data['pagesize']);

    $area_name_arr = array("1" => "本人", "2" => "门店", "3" => "公司");
    foreach ($permission_func as $key => $val) {
      if ($val['is_area'] && $val['area']) {
        $area_name_arr1 = array();
        $area_arr = explode(",", $val['area']);
        foreach ($area_arr as $k => $v) {
          $area_name_arr1[] = $area_name_arr[$v];
        }
        $area_name_str1 = implode(",", $area_name_arr1);
      } else {
        $area_name_str1 = "";
      }
      $permission_func[$key]['area_name'] = $area_name_str1;
    }
    $data['permission_func'] = $permission_func;
    $this->load->view('permission_func/index', $data);
  }

  /**
   * 添加权限菜单功能
   */
  public function add($module_id = 0, $menu_id = 0)
  {
    $data['title'] = '添加权限菜单功能';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'menu_id' => intval($this->input->post('menu_id')),
        'name' => trim($this->input->post('name')),
        'init_auth' => intval($this->input->post('init_auth')),
        'is_menu' => intval($this->input->post('is_menu')),
        'class' => trim($this->input->post('class')),
        'method' => trim($this->input->post('method'))
      );
      $is_area = $this->input->post('is_area');
      $paramArray['is_area'] = $is_area;
      if ($is_area == 1) {
        $area_arr = $this->input->post('area');
        if ($area_arr) {
          $area_str = implode(",", $area_arr);
          $paramArray['area'] = $area_str;
        }
      } else {
        $paramArray['area'] = '';
      }
      if (!empty($paramArray['menu_id']) && !empty($paramArray['name'])
        && !empty($paramArray['class']) && !empty($paramArray['method'])
      ) {
        $addResult = $this->permission_func_model->insert($paramArray);
      } else {
        $data['mess_error'] = '菜单/功能名称/类名/方法名不能为空';
      }
    }
    //权限模块数据
    $data['module_list'] = $this->permission_module_model->get_all_by("", -1);

    $data['module_id'] = $module_id;
    if ($module_id) {
      $data['menu_list'] = $this->permission_menu_model->get_all_by("module_id = {$module_id}", -1);
    }
    $data['menu_id'] = $menu_id;

    $data['addResult'] = $addResult;
    $this->load->view('permission_func/add', $data);
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
      $data['permission_func'] = $this->permission_func_model->get_by_id($id);
      $area_str = $data['permission_func']['area'];
      $area_arr = explode(",", $area_str);
      $data['permission_func']['area_arr'] = $area_arr;

    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'menu_id' => intval($this->input->post('menu_id')),
        'name' => trim($this->input->post('name')),
        'init_auth' => intval($this->input->post('init_auth')),
        'is_menu' => intval($this->input->post('is_menu')),
        'class' => trim($this->input->post('class')),
        'method' => trim($this->input->post('method'))
      );
      $is_area = $this->input->post('is_area');
      $paramArray['is_area'] = $is_area;
      if ($is_area == 1) {
        $area_arr = $this->input->post('area');
        if ($area_arr) {
          $area_str = implode(",", $area_arr);
          $paramArray['area'] = $area_str;
        }
      } else {
        $paramArray['area'] = '';
      }
      if (!empty($paramArray['menu_id']) && !empty($paramArray['name'])
        && !empty($paramArray['class']) && !empty($paramArray['method'])
      ) {
        $modifyResult = $this->permission_func_model->update_by_id($paramArray, $id);
      } else {
        $data['mess_error'] = '菜单/功能名称/类名/方法名不能为空';
      }
    }
    //权限模块数据
    $data['module_list'] = $this->permission_module_model->get_all_by("init_auth = '1'", -1);
    $menu_info = $this->permission_menu_model->get_by_id($data['permission_func']['menu_id']);
    $module_id = $menu_info['module_id'];
    $data['module_id'] = $module_id;
    if ($module_id) {
      $data['menu_list'] = $this->permission_menu_model->get_all_by("module_id = {$module_id}", -1);
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('permission_func/modify', $data);
  }

  /**
   * 删除权限菜单功能
   */
  public function del($id, $module_id = 0, $menu_id = 0)
  {
    $data['title'] = '删除权限菜单功能';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $permission_funcData = $this->permission_func_model->delete_by_id($id);
      if ($permission_funcData) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['module_id'] = $module_id;
    $data['menu_id'] = $menu_id;
    $data['delResult'] = $delResult;
    $this->load->view('permission_func/del', $data);
  }

  /**
   * 取得菜单信息
   */
  public function get_menu_list()
  {
    $module_id = $this->input->get('module_id', TRUE);
    if ($module_id) {
      //菜单列表信息
      $this->permission_menu_model->set_select_fields(array("id", "name"));
      $list = $this->permission_menu_model->get_all_by(array("module_id" => $module_id), -1);
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
