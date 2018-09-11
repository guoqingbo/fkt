<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 权限菜单管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class District_public extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('page_helper');
        $this->load->model('district_model');
        $this->load->model('cooperate_district_model');

//        $this->load->model('cooperate_district_model_apply');
//        $this->load->model('cooperate_district_model_pass');
        $this->load->helper('user_helper');
        //选择表
        $this->cooperate_district_model->set_tbl("cooperate_district");
    }

    /**
     * 权限菜单列表页面
     */
    public function index($district_id = 0)
    {
        $data['title'] = '区域公盘设置管理';
        $data['conf_where'] = 'index';

        //获取区域
        $data['district_list'] = $this->district_model->get_district(array('is_show' => 1));
        $where = "";
        //post参数
        $post_param = $this->input->post(NULL, TRUE);
        $data['post_param'] = $post_param;
        $district_id = isset($post_param['district_id']) ? $post_param['district_id'] : $district_id;
        if ($district_id) {
            $where = "district_id = $district_id";
            $data['cond_where']['district_id'] = $district_id;
        }

        //分页开始
        $data['district_num'] = $this->cooperate_district_model->count_by($where);
        $data['pagesize'] = 10;//设定每一页显示的记录数
        $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

        //区域公盘
        $data['district_public_list'] = $this->cooperate_district_model->get_all_by($where, $data['offset'], $data['pagesize']);
        //获取权限所有节点
        //$permission_list = $this->cooperate_district_model->get_all_list();
        //print_r($permission_list);
        //$data['permission_list'] = $permission_list;
        //$data['permission_tab_menu'] = $this->cooperate_district_model->get_list_by($where, $data['offset'], $data['pagesize']);
        $this->load->view('district_public/index', $data);
    }

    /**
     * 添加权限菜单
     */
    public function add()
    {
        $data['title'] = '添加区域公盘';
        $data['conf_where'] = 'index';
        $addResult = '';
        $submit_flag = $this->input->post('submit_flag');
        if ('add' == $submit_flag) {
            $paramArray = array(
                'district_id' => intval($this->input->post('district_id')),
                'district_name' => trim($this->input->post('district_name')),
                'name' => trim($this->input->post('name')),
                'status' => intval($this->input->post('status')),
                'updatetime' => time(),
                'createtime' => time(),
                'hidden_call_able' => intval($this->input->post('hidden_call_able')),
            );
            if (!empty($paramArray['district_id']) && !empty($paramArray['name'])) {

                //检查该区域是否已经有区域公盘
                $is_exist = $this->cooperate_district_model->get_one_by(array('district_id' => $paramArray['district_id']));
                if ($is_exist) {
                    $data['mess_error'] = '该区域已有区域公盘';
                } else {//添加区域公盘
                    $addResult = $this->cooperate_district_model->insert($paramArray);
                }
            } else {
                $data['mess_error'] = '所属区域/公盘名称不能为空';
            }
        }
        //获取区域
        $data['district_list'] = $this->district_model->get_district(array('is_show' => 1));
        $data['addResult'] = $addResult;
        $this->load->view('district_public/add', $data);
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
            $data['district_public'] = $this->cooperate_district_model->get_one_by(array('id' => $id));
        }
        if ('modify' == $submit_flag) {
            $paramArray = array(
                'district_id' => intval($this->input->post('district_id')),
                'district_name' => trim($this->input->post('district_name')),
                'name' => trim($this->input->post('name')),
                'status' => intval($this->input->post('status')),
                'updatetime' => time(),
                'hidden_call_able' => intval($this->input->post('hidden_call_able')),
            );
            if (!empty($paramArray['district_id']) && !empty($paramArray['name'])) {
                //检查该区域是否已经有区域公盘
                $cond_where = "district_id = {$paramArray['district_id']} and id!={$id}";
                $is_exist = $this->cooperate_district_model->get_one_by($cond_where);
                if ($is_exist) {
                    $data['mess_error'] = '该区域已有区域公盘';
                } else {//更新区域公盘
                    $modifyResult = $this->cooperate_district_model->update_by_id($id, $paramArray);
                }
            } else {
                $data['mess_error'] = '所属区域/公盘名称不能为空';
            }
        }
        //获取区域
        $data['district_list'] = $this->district_model->get_district(array('is_show' => 1));
        $data['modifyResult'] = $modifyResult;
        $this->load->view('district_public/modify', $data);
    }

    /**
     * 删除权限菜单
     */
    public function del($id)
    {
        $data['title'] = '删除公盘';
        $data['conf_where'] = 'index';
        $delResult = '';
        $data['delResult'] = $delResult;
        if (!empty($id)) {
            $res = $this->cooperate_district_model->delete_by_id($id);
            if ($res) {
                $delResult = 1;//删除成功
            } else {
                $delResult = 0;//删除失败
            }
        }
        $data['delResult'] = $delResult;
        $this->load->view('district_public/del', $data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
