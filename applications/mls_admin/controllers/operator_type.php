<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 操作日志类型管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Operator_type extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('permission_module_model');
    $this->load->model('operator_type_model');
    $this->load->helper('user_helper');
  }

  /**
   * 操作日志类型列表页面
   */
  public function index()
  {
    $data['title'] = '操作日志类型设置管理';
    $data['conf_where'] = 'index';
    //权限模块数据
    $data['module_list'] = $this->permission_module_model->get_all_by("", -1);

    $where = "";
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    $module_id = $post_param['module_id'];
    if ($module_id) {
      $where = "module_id = {$module_id}";
    }
    //分页开始
    $data['operator_type_num'] = $this->operator_type_model->count_by($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['operator_type_num'] ? ceil($data['operator_type_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['operator_type'] = $this->operator_type_model->get_list_by($where, $data['offset'], $data['pagesize']);
    $this->load->view('operator_type/index', $data);
  }

  /**
   * 添加操作日志类型
   */
  public function add()
  {
    $data['title'] = '添加操作日志类型';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'module_id' => intval($this->input->post('module_id')),
        'name' => trim($this->input->post('name'))
      );
      if (!empty($paramArray['module_id']) && !empty($paramArray['name'])) {
        $addResult = $this->operator_type_model->insert($paramArray);
      } else {
        $data['mess_error'] = '权限模块/操作名称不能为空';
      }
    }
    //权限模块数据
    $data['module_list'] = $this->permission_module_model->get_all_by("", -1);

    $data['addResult'] = $addResult;
    $this->load->view('operator_type/add', $data);
  }

  /**
   * 修改操作日志类型
   */
  public function modify($id)
  {
    $data['title'] = '修改操作日志类型';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $data['operator_type'] = $this->operator_type_model->get_by_id($id);
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'module_id' => intval($this->input->post('module_id')),
        'name' => trim($this->input->post('name'))
      );
      if (!empty($paramArray['module_id']) && !empty($paramArray['name'])) {
        $modifyResult = $this->operator_type_model->update_by_id($paramArray, $id);
      } else {
        $data['mess_error'] = '权限模块/操作名称不能为空';
      }
    }
    //权限模块数据
    $data['module_list'] = $this->permission_module_model->get_all_by("", -1);

    $data['modifyResult'] = $modifyResult;
    $this->load->view('operator_type/modify', $data);
  }

  /**
   * 删除操作日志类型
   */
  public function del($id)
  {
    $data['title'] = '删除操作日志类型';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $operator_typeData = $this->operator_type_model->delete_by_id($id);
      if ($operator_typeData) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('operator_type/del', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
