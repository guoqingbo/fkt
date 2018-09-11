<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 权限菜单管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class District_public_join extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('page_helper');
        $this->load->model('district_model');
        $this->load->model('cooperate_district_model');
        $this->load->model('sell_house_model');
        $this->load->model('rent_house_model');

//        $this->load->model('cooperate_district_model_apply');
//        $this->load->model('cooperate_district_model_pass');
        $this->load->helper('user_helper');

        //选择表
        $this->cooperate_district_model->set_tbl("cooperate_district_join");
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

        //加入公盘门店列表

        $data['agency_join_list'] = $this->cooperate_district_model->get_all_by($where, $data['offset'], $data['pagesize']);

        $this->load->view('district_public_join/index', $data);
    }

    /**
     * 添加权限菜单
     */
    public function add()
    {
        $data['title'] = '门店加入区域公盘';
        $data['conf_where'] = 'index';
        $addResult = '';
        //获取区域
        //$data['district_list'] = $this->district_model->get_district(array('is_show' => 1));
        $submit_flag = $this->input->post('submit_flag');
        if ('add' == $submit_flag) {
            $paramArray = array(
                'agency_id' => intval($this->input->post('agency_id')),
                'agency_name' => trim($this->input->post('agency_name')),
                'cooperate_district_id' => intval($this->input->post('cooperate_district_id')),
                'cooperate_district_name' => trim($this->input->post('cooperate_district_name')),
                'district_id' => intval($this->input->post('district_id')),
                'district_name' => trim($this->input->post('district_name')),
                'status' => intval($this->input->post('status')),
                'updatetime' => time(),
                'createtime' => time()
            );
            if (!empty($paramArray['agency_id']) && !empty($paramArray['cooperate_district_id'])) {
                //检查该门店是否已经加入区域公盘
                $is_exist = $this->cooperate_district_model->get_one_by(array('agency_id' => $paramArray['agency_id']));
                if ($is_exist) {
                    $data['mess_error'] = '该门店已加入区域公盘';
                } else {//添加
                    $addResult = $this->cooperate_district_model->insert($paramArray);
                    $data['addResult'] = $addResult;
                }
            } else {
                $data['mess_error'] = '门店/公盘不能为空';
            }
        }
        $this->load->helper('common_load_source_helper');
        $data['css'] = load_css('mls/css/v1.0/autocomplete.css');
        //需要加载的JS
        $data['js'] = load_js('common/third/jquery-ui-1.9.2.custom.min.js');

        $this->load->view('district_public_join/add', $data);
    }

    /**
     * 修改权限菜单
     */
    public function modify($id)
    {
        $data['title'] = '修改门店区域公盘';
        $data['conf_where'] = 'index';
        $modifyResult = '';
        $submit_flag = $this->input->post('submit_flag');

        if (!empty($id)) {
            $data['agency_ditrict_public'] = $this->cooperate_district_model->get_one_by(array('id' => $id));
        }
        if ('modify' == $submit_flag) {
            $paramArray = array(
                'agency_id' => intval($this->input->post('agency_id')),
                'agency_name' => trim($this->input->post('agency_name')),
                'cooperate_district_id' => intval($this->input->post('cooperate_district_id')),
                'cooperate_district_name' => trim($this->input->post('cooperate_district_name')),
                'district_id' => intval($this->input->post('district_id')),
                'district_name' => trim($this->input->post('district_name')),
                'status' => intval($this->input->post('status')),
                'updatetime' => time(),
                'createtime' => time()
            );
            if (!empty($paramArray['agency_id']) && !empty($paramArray['cooperate_district_id'])) {
                //检查该门店是否已经加入区域公盘
                $conf_where = "agency_id = {$paramArray['agency_id']} and id <>{$id} ";
                $is_exist = $this->cooperate_district_model->get_one_by($conf_where);
                if ($is_exist) {
                    $data['mess_error'] = '该门店已加入区域公盘';
                } else {//更新区域公盘
                    $modifyResult = $this->cooperate_district_model->update_by_id($id, $paramArray);
                    $data['modifyResult'] = $modifyResult;
                }
            } else {
                $data['mess_error'] = '门店/公盘不能为空';
            }
        }
        $this->load->helper('common_load_source_helper');
        $data['css'] = load_css('mls/css/v1.0/autocomplete.css');
        //需要加载的JS
        $data['js'] = load_js('common/third/jquery-ui-1.9.2.custom.min.js');

        $this->load->view('district_public_join/modify', $data);
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
            //剔除门店发送到区域公盘的房源
            $cooperate_district_info = $this->cooperate_district_model->get_one_by(array('id' => $id));
            $update_arr = array(
                'isshare_district' => 0,
                'is_district_public' => 0,
                'district_broker_name' => "",
                'district_broker_id' => "",
                'set_district_share_time' => "",

            );
            $cond_where = array(
                'agency_id' => $cooperate_district_info['agency_id'],
                'isshare_district' => 1,
            );
            $update_sell_num = $this->sell_house_model->update_house($update_arr, $cond_where);
            $update_rent_num = $this->rent_house_model->update_house($update_arr, $cond_where);
            $res = $this->cooperate_district_model->delete_by_id($id);
            if ($res) {
                $delResult = 1;//删除成功
            } else {
                $delResult = 0;//删除失败
            }
        }
        $data['delResult'] = $delResult;
        $this->load->view('district_public_join/del', $data);
    }

    /**
     * 根据关键字获取门店
     */
    public function get_agency_info_by_kw()
    {
        $keyword = $this->input->get('keyword', TRUE);
        $this->load->model('agency_model');
        $select_fields = array('id', 'name', 'agency_type');
        $this->agency_model->set_select_fields($select_fields);
        $cmt_info = $this->agency_model->get_agencys_by_kw($keyword);

        foreach ($cmt_info as $key => $value) {
            if ($value['agency_type'] == 3) {
                $cmt_info[$key]['label'] = $value['name'] . "（合作）";
            } else {
                $cmt_info[$key]['label'] = $value['name'];
            }
        }

        if (empty($cmt_info)) {
            $cmt_info[0]['id'] = 0;
            $cmt_info[0]['label'] = '暂无门店';
        }

        echo json_encode($cmt_info);
    }

    /**
     * 根据门店id获取门店所在区域
     */
    public function get_agency_district()
    {
        $data = array();
        $agency_id = intval($this->input->post('agency_id'));
        //获取门店所在区域id
        $this->load->model('agency_model');
        $this->agency_model->set_select_fields(array('dist_id'));
        $agency = $this->agency_model->get_by_id($agency_id);
        $district_id = $agency['dist_id'];
        //获取区域
        $district = $this->district_model->get_district(array('id' => $district_id, 'is_show' => 1));
        if ($district) {
            $data['district'] = $district["0"];
            //获取区域中的公盘
            $this->cooperate_district_model->set_tbl("cooperate_district");
            $district_public = $this->cooperate_district_model->get_one_by(array('district_id' => $district["0"]["id"], 'status' => 1));
            if ($district_public) {
                $data['district_public'] = $district_public;
                $data['msg'] = "success";
            } else {
                $data['msg'] = "该区域（" . $district[0]['district'] . "）无公盘，请先创建区域公盘";
            }
        } else {
            $data['msg'] = "获取该门店区域失败";
        }


        echo json_encode($data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
