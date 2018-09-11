<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 系统默认角色
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Signatory_purview extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    //查看所有的模块
    $this->load->model('signatory_system_role_model');
    $this->load->model('signatory_system_group_model');
    $this->load->model('signatory_company_group_model');
    $this->load->model('signatory_department_group_model');
  }

  //获取系统默认的角色
  public function index()
  {
    $data_view = array();
    $data_view['title'] = '用户权限管理';
    $data_view['conf_where'] = 'index';
    //获取系统默认权限
    $permission_role = $this->signatory_system_group_model->get_role();
    $data_view['permission_role'] = $permission_role;
    $this->load->view('signatory_purview/index', $data_view);
  }


  /**
   * 设置角色权限
   * @param int $role_id 角色编号
   */
  public function set_role_func($role_id)
  {
    $data_view = array();
    $data_view['title'] = '用户默认权限功能设置';
    $data_view['conf_where'] = 'index';

    $setResult = '';
//       $this->load->model('permission_menu_model');
    $this->load->model('signatory_purview_modules_model');
    $this->load->model('signatory_purview_list_model');
    //用户默认权限角色
    $this->signatory_system_group_model->set_select_fields(array("func_auth", "range"));
    $system_role_info = $this->signatory_system_group_model->get_by_id($role_id);

    //反序列化
    $func_auth_arr = unserialize($system_role_info['func_auth']);
    $range = $system_role_info['range'];

    $data_view['range'] = $range;

    $role_func1 = array();
    if ($func_auth_arr) {
      foreach ($func_auth_arr as $key => $val) {
        foreach ($val as $k => $v) {
          $role_func1[] = $v;
        }
      }
    }

    $data_view['role_func1'] = $role_func1;

    //权限模块、菜单、功能数组
    $this->signatory_purview_modules_model->set_select_fields(array("id", "name"));
    $module = $this->signatory_purview_modules_model->get_all_by_id("status = '1'", -1, -1, "id", "ASC");

    foreach ($module as $key => $val) {
      $this->signatory_purview_list_model->set_select_fields(array("pid", "pname", 'tab_id', 'secondtab_id'));
      $func = $this->signatory_purview_list_model->get_all_by("mid = $val[id] AND status = '1'", -1, 20, "pid");
      $func_list = array();
      foreach ($func as $v) {
        if (!isset($func_list[$v['tab_id']])) {
          if ($v['tab_id'] == 0) {
            $tab_name = '';
          } else {
            //获取一级菜单名称
            $tab = $this->signatory_purview_modules_model->get_tab_id($v['tab_id']);
            $tab_name = $tab['name'];
          }
          $func_list[$v['tab_id']] = array('name' => $tab_name, 'list' => array());
        }
        if (!isset($func_list[$v['tab_id']]['list'][$v['secondtab_id']])) {
          if ($v['secondtab_id'] == 0) {
            $secondtab_name = '';
          } else {
            $secondtab = $this->signatory_purview_modules_model->get_secondtab_id($v['secondtab_id']);
            $secondtab_name = $secondtab['name'];
          }
          $func_list[$v['tab_id']]['list'][$v['secondtab_id']] = array('name' => $secondtab_name, 'list' => array());
        }
        array_push($func_list[$v['tab_id']]['list'][$v['secondtab_id']]['list'], $v);
      }
      $module[$key]['func'] = $func_list;
    }
    $data_view['id'] = $role_id;
    $data_view['module'] = $module;
    //form表单提交
    $submit_flag = $this->input->post('submit_flag');
    if ('set' == $submit_flag) {
      //菜单中url地址信息

      //form表单中post选中的模块、菜单、功能数据
      $range = $this->input->post('range');
      $module = $this->input->post('module');
      $func = $this->input->post('func_auth');
      $system_group_id = $this->input->post('system_group_id');

      $func_auth_new = array();//功能权限数组
      foreach ($func as $key => $val) {
        $k = reset(explode("/", $val));
        $v = end(explode("/", $val));
        $func_auth_new[$k][] = $v;
      }
      //序列化数组
      $func_auth = serialize($func_auth_new);

      $update_array = array(
        'func_auth' => $func_auth
//                        );
//            //只有总经理角色执行
//            if('1'==$system_group_id){
//                //公司权限表变更对应角色权限
//                $this->signatory_company_group_model->update_by_id($update_array,$system_group_id);
//                //门店权限表变更对应角色权限
//                $num = $this->signatory_department_group_model->update_by_id($update_array,$system_group_id);
//            }
      );

      //公司权限表变更对应角色权限
      $this->signatory_company_group_model->update_by_id($update_array, $system_group_id);
      //门店权限表变更对应角色权限
      $num = $this->signatory_department_group_model->update_by_id($update_array, $system_group_id);

      $paramArray = array(
        'range' => $range,
        'func_auth' => $func_auth
      );
      $setResult = $this->signatory_system_group_model->update_by_id($paramArray, $role_id);
    }

    $data_view['setResult'] = $setResult;
    $this->load->view('signatory_purview/set_role_func', $data_view);

  }

  public function addgroup()
  {
    $name = $this->input->post('name');
    $description = $this->input->post('description');
    $level = $this->input->post('level');
    $insert_data = array(
      'name' => $name,
      'level' => $level
    );
    $result = $this->signatory_system_group_model->add_group($insert_data);
    //添加成功后初始化角色权限
    //新角色基本信息
    $group_info = $this->signatory_system_group_model->get_by_id($result);
    //初始化公司表
    $company_id_arr = $this->signatory_company_group_model->get_all_company_id();
    $insert_company = array();
    if ($company_id_arr) {
      $i = 0;
      foreach ($company_id_arr as $key => $vo) {
        if ($vo['company_id'] > 0) {
          $insert_company[$i]['company_id'] = $vo['company_id'];
          $insert_company[$i]['system_group_id'] = $group_info['id'];
          $i++;
        }
      }
    }
    $this->signatory_company_group_model->insert($insert_company);
    //初始化门店表
    $agency_id_arr = $this->signatory_department_group_model->get_all_department_id();
    $insert_agency = array();
    if ($agency_id_arr) {
      $j = 0;
      foreach ($agency_id_arr as $key => $vo) {
        if ($vo['company_id'] > 0 && $vo['department_id'] > 0) {
          $insert_agency[$j]['department_id'] = $vo['department_id'];
          $insert_agency[$j]['company_id'] = $vo['company_id'];
          $insert_agency[$j]['system_group_id'] = $group_info['id'];
          $j++;
        }
      }
    }
    $this->signatory_department_group_model->insert($insert_agency);
    echo $result;
  }

  public function get_group($id)
  {
    $isajax = $this->input->get('isajax', TRUE);
    if ($isajax) {
      $list = $this->signatory_system_group_model->get_by_id($id);
      if ($list) {
        $result = array('result' => 'ok', 'list' => $list);
      } else {
        $result = array('result' => 'no');
      }
      echo json_encode($result);
    }
  }

  public function del_group($id)
  {
    $isajax = $this->input->get('isajax', TRUE);
    if ($isajax) {
      $result = $this->signatory_system_group_model->delete_by_id($id);
      echo json_encode($result);
    }
  }

  public function update_group()
  {
    $id = $this->input->post('id');
    $name = $this->input->post('name');
    $level = $this->input->post('level');
    $update_data = array(
      'name' => $name,
      'level' => $level
    );
    $result = $this->signatory_system_group_model->update_by_id($update_data, $id);
    echo $result;
  }
}

/* End of file permission.php */
/* Location: ./application/mls_admin/controllers/permission.php */
