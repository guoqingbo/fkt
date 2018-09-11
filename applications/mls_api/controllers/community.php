<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * autocollect_nj controller CLASS
 *
 * 跟新小区经纬度（临时类，可删除）
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          angel_in_us
 */
class Community extends My_Controller
{

    public function __construct()
    {
        parent::__construct();
        //设置成熟参数
        $this->set_city('hz');
        $this->load->model('community_model');
    }

    /**
     * 获取小区列表
     */
    public function index()
    {
        $this->load->view('community.html');
    }

    /**
     * 获取小区列表
     */
    public function getCommunity()
    {
        $update_res = 2;
        $where = 'b_map_x = 0 and `update_res` = ' . $update_res;
        $selectField = 'id,cmt_name,address,dist_id,streetid, b_map_x,b_map_y';
        $limit = 0;
        $communityList = $this->community_model->get_by_where($where, $selectField, $limit);
        if ($update_res == 2) {//更新地址查不到的小区，通过区域板块楼盘名查找
            $disAndStr = $this->getDistrictAndStreet();
            foreach ($communityList as $key => &$val) {
                $val['address'] = $disAndStr['district'][$val['dist_id']] . $disAndStr['street'][$val['streetid']] . $val['cmt_name'];
            }
        }
        echo json_encode($communityList, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取区域板块
     */
    public function getDistrictAndStreet()
    {
        $res = [];
        $this->load->model('get_district_model');
        $district = $this->get_district_model->get_district();
        if (!empty($district)) {
            foreach ($district as $key => $val) {
                $res['district'][$val['id']] = $val['district'];
            }
        }
        $street = $this->get_district_model->get_street();
        if (!empty($street)) {
            foreach ($street as $key => $val) {
                $res['street'][$val['id']] = $val['streetname'];
            }
        }
        return $res;
    }

    /**
    /**
     * 批量更新经纬度
     */
    public function updateCommunity()
    {
        $communityList = $this->input->post('communityList');
//        $where = 'b_map_x = 0 and `update_res` = 0 and id in (262,523,565,566)';
//        $communityList = $this->community_model->get_by_where($where);
        if (!empty($communityList)) {

            $updateRes = $this->community_model->update_batch($communityList);
            if ($updateRes) {
                return json_encode(['success' => true, 'msg' => '更新成功']);
            } else {
                return json_encode(['success' => false, 'msg' => '更新失败']);
            }
        } else {
            return json_encode(['success' => false, 'msg' => '小区列表不能为空']);
        }
    }

    /**
     *单条更新
     */
    public function updateCommunityByOne()
    {
        $community = $this->input->post('community');
//        $where = 'b_map_x = 0 and `update_res` = 0 and id in (262,523,565,566)';
//        $communityList = $this->community_model->get_by_where($where);
        if (!empty($community)) {
            $updateRes = $this->community_model->update($community);
            if ($updateRes) {
                return json_encode(['success' => true, 'msg' => '更新成功']);
            } else {
                return json_encode(['success' => false, 'msg' => '更新失败']);
            }
        } else {
            return json_encode(['success' => false, 'msg' => '小区列表不能为空']);
        }
    }
}

