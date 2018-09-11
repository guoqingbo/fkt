<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 门店关联权限模块管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author
 */
class Agency_permission_node extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('agency_permission_node_model');
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
    $data['permission_menu_num'] = $this->agency_permission_node_model->count_by($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['permission_menu_num'] ? ceil($data['permission_menu_num'] / $data['pagesize']) : 0;
    //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $whereAll = " l.mid = m.id order by l.mid ASC";
    $data['permission_modules'] = $this->agency_permission_node_model->get_all_by($whereAll, $data['offset'], $data['pagesize']);
    //print_r($data['permission_modules']);
    $this->load->view('agency_permission_node/index', $data);
  }

  /**
   * 添加权限模块
   */
  public function add()
  {
    $modules = $this->agency_permission_node_model->get_all_by_modules();
    //print_r($modules);
    $data['modules'] = $modules;
    $data['title'] = '添加权限模块';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'mid' => trim($this->input->post('mid')),
        'pname' => trim($this->input->post('pname')),
        'status' => intval($this->input->post('status')),
      );
      if (empty($paramArray['pname'])) {
        $data['mess_error'] = '权限名称不能为空';
      } else {
        $addResult = $this->agency_permission_node_model->insert($paramArray);
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('agency_permission_node/add', $data);
  }

  /**
   * 修改权限模块
   */
  public function modify($id)
  {
    $modules = $this->agency_permission_node_model->get_all_by_modules();
    //print_r($modules);
    $data['modules'] = $modules;
    $data['title'] = '修改权限模块';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $data['permission_menu'] = $this->agency_permission_node_model->get_by_id($id);
    }
    //print_r($data['permission_menu']);
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'mid' => trim($this->input->post('mid')),
        'pname' => trim($this->input->post('pname')),
        'status' => intval($this->input->post('status'))
      );
      if (empty($paramArray['pname'])) {
        $data['mess_error'] = '权限名称不能为空';
      } else {
        $modifyResult = $this->agency_permission_node_model->update_by_id($paramArray, $id);
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('agency_permission_node/modify', $data);
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
                $num = $this->agency_permission_node_model->update_by_id($paramArray,$pid);
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
            $permission_menuData = $this->agency_permission_node_model->delete_by_id($id);
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
        $this->load->view('agency_permission_node/del',$data);
    }*/

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
