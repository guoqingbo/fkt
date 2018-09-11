<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 权限模块管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Signatory_purview_module extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('signatory_purview_module_model');
    $this->load->helper('user_helper');
  }

  /**
   * 权限模块列表页面
   */
  public function index()
  {
    $data['title'] = '签约权限模块列表';
    $data['conf_where'] = 'index';
    $where = "";
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //分页开始
    $data['permission_menu_num'] = $this->signatory_purview_module_model->count_by($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['permission_menu_num'] ? ceil($data['permission_menu_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['signatory_purview_module'] = $this->signatory_purview_module_model->get_all_by($where, $data['offset'], $data['pagesize']);
    $this->load->view('signatory_purview_module/index', $data);
  }

  /**
   * 添加权限模块
   */
  public function add()
  {
    $data['title'] = '添加签约权限模块';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'name' => trim($this->input->post('name')),
        'url' => trim($this->input->post('url')),
        'init_auth' => intval($this->input->post('init_auth')),
        'order' => trim($this->input->post('order')),
        'style' => trim($this->input->post('style')),
      );
      if (empty($paramArray['name'])) {
        $data['mess_error'] = '模块名称不能为空';
      } else if ($paramArray['init_auth'] == 0 && empty($paramArray['url'])) {
        $data['mess_error'] = '默认权限为空时，链接地址不能为空';
      } else {
        $addResult = $this->signatory_purview_module_model->insert($paramArray);
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('signatory_purview_module/add', $data);
  }

  /**
   * 修改权限模块
   */
  public function modify($id)
  {
    $data['title'] = '修改签约权限模块';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $data['permission_menu'] = $this->signatory_purview_module_model->get_by_id($id);
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'name' => trim($this->input->post('name')),
        'url' => trim($this->input->post('url')),
        'init_auth' => intval($this->input->post('init_auth')),
        'order' => trim($this->input->post('order')),
        'style' => trim($this->input->post('style')),
      );
      if (empty($paramArray['name'])) {
        $data['mess_error'] = '模块名称不能为空';
      } else if ($paramArray['init_auth'] == 0 && empty($paramArray['url'])) {
        $data['mess_error'] = '默认权限为空时，链接地址不能为空';
      } else {
        $modifyResult = $this->signatory_purview_module_model->update_by_id($paramArray, $id);
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('signatory_purview_module/modify', $data);
  }

  /**
   * 删除权限模块
   */
  public function del($id)
  {
    $data['title'] = '删除权限模块';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $permission_menuData = $this->signatory_purview_module_model->delete_by_id($id);
      if ($permission_menuData) {
        $delResult = 1;//删除成功
//                $this->load->model('permission_menu_model');
//                $this->load->model('permission_func_model');
//                $menu_ids = $this->permission_menu_model->get_by_module_id($id);
//                $new_menu_ids = array();
//                foreach($menu_ids as $v)
//                {
//                    $new_menu_ids[] = $v['id'];
//                }
//                if ($this->permission_menu_model->delete_by_module_id($id)
//                        && $this->permission_func_model->delete_by_menu_id($new_menu_ids))
//                {
//                    $delResult = 1;//删除成功
//                }
//				else
//                {
//                    $delResult = 0;//删除失败
//                }
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('signatory_purview_module/del', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
