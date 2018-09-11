<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 门店添加和删除审核
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Agency_review extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('agency_review_model');
    $this->load->helper('user_helper');
  }

  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $search_where = $this->input->post('search_where');
    $search_value = $this->input->post('search_value');
    $search_status = $this->input->post('search_status');
    if (!$search_status) {
      $search_status = 99;
    }
    $where = '';
    if ($search_where && $search_value) {
      $this->load->model('agency_model');
      $this->agency_model->set_select_fields(array('id'));
      $agency = $this->agency_model->get_all_by("status = 0 and name like "
        . "'%$search_value%'");
      if (is_full_array($agency)) {
        $agency_id = array();
        foreach ($agency as $v) {
          $agency_id[] = $v['id'];
        }
        $agency_ids = implode(',', $agency_id);
        $where = "agency_id in ($agency_ids)";
      } else {
        $where = 'agency_id = 0';
      }
    }
    if ($search_status != 99) {
      $search_sta = $search_status - 1;
      if ($where == '') {
        $where = "status = " . $search_sta;
      } else {
        $where .= " and status = " . $search_sta;
      }
    }
    //条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value, 'search_status' => $search_status
    );
    //分页开始
    $data_view['count'] = $this->agency_review_model->count_by($where);
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //门店列表
    $data_view['agency'] = $this->agency_review_model->get_all_by(
      $where, $data_view['offset'], $data_view['pagesize']);
    $data_view['title'] = '门店审核';
    $data_view['conf_where'] = 'index';
    $this->load->view('agency_review/index', $data_view);
  }

  public function modify($id)
  {
    $data_view['title'] = '门店审核';
    $data_view['conf_where'] = 'index';
    $data_view['modifyResult'] = '';
    $submit_flag = $this->input->post('submit_flag');
    $data_view['agency_review'] = $this->agency_review_model->get_by_id($id);
    if ($submit_flag == 'modify') {
      //获取审核过后的状态
      $status = $this->input->post('status');
      $remark = $this->input->post('remark');
      //只有队列中的记录，才可以审核
      if ($data_view['agency_review']['status'] == 0) {
        $this->load->model('agency_model');
        //添加中介公司审核
        if ($data_view['agency_review']['action'] == 1) {
          //审核通过
          if ($status == 1) //通过
          {
            $this->agency_model->set_status_pass($data_view['agency_review']['agency_id']);
          }
        } else //删除中介公司审核
        {
          //审核通过
          if ($status == 1) //通过
          {
            $this->agency_model->set_status_delete($data_view['agency_review']['agency_id']);
          }
        }
        //更新数据
        $modify_result = $this->agency_review_model->review(
          $data_view['agency_review']['id'],
          $status, $remark);
        $data_view['modifyResult'] = $modify_result;
      } else {
        $data_view['mess_error'] = '此记录无需重新审核';
      }
    }
    $this->load->view('agency_review/modify', $data_view);
  }

}
