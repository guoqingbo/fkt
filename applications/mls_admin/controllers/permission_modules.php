<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 权限模块管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author
 */
class Permission_modules extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('permission_modules_model');
    $this->load->helper('user_helper');
  }

  /**
   * 权限模块列表页面
   */
  public function index()
  {
    $data['title'] = '权限模块列表';
    $data['conf_where'] = 'index';
    $where = "";
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //分页开始
    $data['permission_menu_num'] = $this->permission_modules_model->count_by($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['permission_menu_num'] ? ceil($data['permission_menu_num'] / $data['pagesize']) : 0;
    //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $whereAll = " l.mid = m.id order by l.mid ASC";
    $data['permission_modules'] = $this->permission_modules_model->get_all_by($whereAll, $data['offset'], $data['pagesize']);
    //print_r($data['permission_modules']);
    $this->load->view('permission_modules/index', $data);
  }

  /**
   * 添加权限模块
   */
  public function add()
  {
    $modules = $this->permission_modules_model->get_all_by_modules();
    $data['modules'] = $modules;
    $data['title'] = '添加权限模块';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'mid' => trim($this->input->post('mid')),
        'pname' => trim($this->input->post('pname')),
        'tab_id' => trim($this->input->post('tab_id')),
        'secondtab_id' => trim($this->input->post('secondtab_id')),
        'status' => intval($this->input->post('status')),
      );
      if (empty($paramArray['pname'])) {
        $data['mess_error'] = '权限名称不能为空';
      } else {
        $addResult = $this->permission_modules_model->insert($paramArray);
      }
    }
    if (is_full_array($modules)) //根据第一个模块，选择
    {
      $first_module_id = $modules[0]['mid'];
      //菜单列表信息
      $this->load->model('permission_tab_menu_model');
      $this->permission_tab_menu_model->set_select_fields(array("id", "name"));
      $tab = $this->permission_tab_menu_model->get_all_by(array("module_id" => $first_module_id), -1);
      $data['tab'] = $tab;
    }
    $data['addResult'] = $addResult;
    $this->load->view('permission_modules/add', $data);
  }

  /**
   * 修改权限模块
   */
  public function modify($id)
  {
    $modules = $this->permission_modules_model->get_all_by_modules();
    //print_r($modules);
    $data['modules'] = $modules;
    $data['title'] = '修改权限模块';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $data['permission_menu'] = $this->permission_modules_model->get_by_id($id);
      $module = $this->permission_modules_model->get_modules_by($data['permission_menu']['mid']);
      $module_id = $module['mid'];
      //查找模块下的一级菜单
      $tab = $this->permission_modules_model->get_all_tab($module_id);
      $data['tab'] = $tab;
      //如果设置了一级菜单刚查询二级菜单
      if (isset($data['permission_menu']['tab_id']) && $data['permission_menu']['mid'] > 0) {
        $tab_id = $data['permission_menu']['tab_id'];
        $secondtab = $this->permission_modules_model->get_secondtabs_by_tab_id($tab_id);
        $data['secondtab'] = $secondtab;
      }
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'mid' => trim($this->input->post('mid')),
        'pname' => trim($this->input->post('pname')),
        'tab_id' => trim($this->input->post('tab_id')),
        'secondtab_id' => trim($this->input->post('secondtab_id')),
        'status' => intval($this->input->post('status')),
        'is_this_user_hold' => intval($this->input->post('is_this_user_hold'))
      );
      if (empty($paramArray['pname'])) {
        $data['mess_error'] = '权限名称不能为空';
      } else {
        $modifyResult = $this->permission_modules_model->update_by_id($paramArray, $id);
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('permission_modules/modify', $data);
  }

  /**
   * 取得菜单信息
   */
  public function get_tab_list()
  {
    $module_id = $this->input->get('module_id', TRUE);
    $tabs = $this->permission_modules_model->get_tabs_by_module_id($module_id);
    if (is_full_array($tabs)) {
      $result = array('result' => 'ok', 'list' => $tabs);
    } else {
      $result = array('result' => 'no');
    }
    echo json_encode($result);
  }

  //一级菜单获取二级菜单
  public function get_secondtab_list()
  {
    $tab_id = $this->input->get('tab_id', TRUE);
    $secondtabs = $this->permission_modules_model->get_secondtabs_by_tab_id($tab_id);
    $result = array();
    if (is_full_array($secondtabs)) {
      $result = array('result' => 'ok', 'list' => $secondtabs);
    } else {
      $result = array('result' => 'no');
    }
    echo json_encode($result);
  }
  /**
   * 删除权限模块
   */
  /*public function del_del()
    {
        $pid = $this->input->post("pid");
        $paramArray = array('status' => 0);
        if ($pid)
            {
                $num = $this->permission_modules_model->update_by_id($paramArray,$pid);
            }

        if($num){
                $info["status"]=1;
                $info["pid"]=$pid;
                echo json_encode($info);
            }else{
                $info["status"]=0;
                echo json_encode($info);
            }
    }


    public function del($id)
    {
        $data['title'] = '删除权限模块';
        $data['conf_where'] = 'index';
        $delResult = '';
        $data['delResult'] = $delResult;
        if(!empty($id))
        {
            $permission_menuData = $this->permission_modules_model->delete_by_id($id);
            if(true)
            {
                $this->load->model('permission_menu_model');
                $this->load->model('permission_func_model');
                $menu_ids = $this->permission_menu_model->get_by_modules_id($id);
                $new_menu_ids = array();
                foreach($menu_ids as $v)
                {
                    $new_menu_ids[] = $v['id'];
                }
                if ($this->permission_menu_model->delete_by_modules_id($id)
                        && $this->permission_func_model->delete_by_menu_id($new_menu_ids))
                {
                    $delResult = 1;//删除成功
                }
                else
                {
                    $delResult = 0;//删除失败
                }
            }
            else
            {
                $delResult = 0;//删除失败
            }
        }
        $data['delResult'] = $delResult;
        $this->load->view('permission_modules/del',$data);
    }*/

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
