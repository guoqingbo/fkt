<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 身份、资质审核
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Appeal extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('appeal_model');
    $this->load->model('api_broker_sincere_model');
    $this->load->helper('user_helper');
  }

  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');//引入分页类
    $where = 'id <> 0';
    $status = $this->input->post('status');
    $data_view['status'] = $status;
    if ($status) {
      $where .= ' and status = ' . $status;
    }
    $appraise_id = $this->input->post('appraise_id');
    $data_view['appraise_id'] = $appraise_id;
    if ($appraise_id) {
      $where .= ' and appraise_id = ' . $appraise_id;
    }

    $broker_name = $this->input->post('broker_name');
    $data_view['broker_name'] = $broker_name;
    if ($broker_name) {
      $where .= ' and broker_name = "' . $broker_name . '"';
    }

    $pg = $this->input->post('pg');

    //分页开始
    $data_view['count'] = $this->appeal_model->count_by($where);
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //申请列表
    $appeal_info = $this->appeal_model->get_all_by($where, $data_view['offset'], $data_view['pagesize']);

    $this->load->model('house_config_model');
    $house_config = $this->house_config_model->get_config();
    $this->load->model('sincere_trust_config_model');
    $config_info = $this->sincere_trust_config_model->get_config();
    $this->load->model('broker_info_model');
    $this->load->model('sincere_appraise_cooperate_model');
    foreach ($appeal_info as $key => $value) {
      $cooperate_info = $this->sincere_appraise_cooperate_model->get_by_id($value['appraise_id']);
      //var_dump($broker_info);exit;
      $house_info = unserialize($cooperate_info['house_info']);//房源信息
      $house_info['forward_str'] = $house_config['forward'][$house_info['forward']];
      $house_info['fitment_str'] = $house_config['fitment'][$house_info['fitment']];
      $appeal_info[$key]['house_info'] = $house_info;

      $appeal_info[$key]['trust_name'] = $config_info['appraise_type_description'][$cooperate_info['trust_type_id']];//信用类型名称

      //通过分数获取星星
      $appeal_info[$key]['info_star'] = $this->api_broker_sincere_model->get_appraise_level($cooperate_info['infomation']);//信息真实度
      $appeal_info[$key]['atti_star'] = $this->api_broker_sincere_model->get_appraise_level($cooperate_info['attitude']);//合作满意度
      $appeal_info[$key]['busi_star'] = $this->api_broker_sincere_model->get_appraise_level($cooperate_info['business']);//业务专业度

      $appeal_info[$key]['content'] = $cooperate_info['content'];//评价内容
      $appeal_info[$key]['create_time'] = $cooperate_info['create_time'];//评价时间

      $broker_info = $this->broker_info_model->get_by_broker_id($cooperate_info['broker_id']);
      $appeal_info[$key]['cooperate_broker_name'] = $broker_info['truename'];//评价方名字

    }

    $data_view['appeal_info'] = $appeal_info;

    $data_view['title'] = '评价申诉审核';
    $data_view['conf_where'] = 'index';
    //var_dump($data_view);exit;
    $this->load->view('appeal/index', $data_view);
  }

  /**
   * 修改评价申诉信息
   *
   */
  public function modify()
  {
    $id = $this->input->post('id');
    $status = $this->input->post('status');
    $this->api_broker_sincere_model->update_appraise_appeal_status($id, $status);
    if ($status == 2 || $status == 3) {
      $appeal_info = $this->appeal_model->get_by_id($id);
      $brokered_id = $appeal_info['broker_id'];//申诉人-被评价人
      $brokered_name = $appeal_info['broker_name'];//申诉人-被评价人
      $transaction_id = $appeal_info['transaction_id'];
      $this->load->model('cooperate_model');
      $cooperate_baseinfo = array();
      $cooperate_baseinfo = $this->cooperate_model->get_cooperate_baseinfo_by_order_sn($transaction_id);
      $tbl = $cooperate_baseinfo['tbl'];
      if ($cooperate_baseinfo['apply_type'] == 1) {
        $params['type'] = "f";
        $params['id'] = $cooperate_baseinfo['rowid'];
        $params['pf'] = 1;
      } else if ($cooperate_baseinfo['apply_type'] == 2) {
        $params['id'] = $cooperate_baseinfo['customer_id'];
        $params['pf'] = 1;
        $tbl .= '_customer';
      }
      $params['id'] = format_info_id($params['id'], $tbl);
      $this->load->model('message_model');
      if ($status == 2) {
        $this->load->model('sincere_appraise_cooperate_model');
        $this->load->model('api_broker_model');
        $cooperate_info = $this->sincere_appraise_cooperate_model->get_by_id($appeal_info['appraise_id']);
        $broker_id = $cooperate_info['broker_id'];//评价人
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
        $broker_name = $brokerinfo['truename'];//评价人
        //双方发消息
        //33
        $this->message_model->add_message('2-20', $broker_id, $broker_name, '/my_evaluate/', $params);
        $this->message_model->add_message('2-18', $brokered_id, $brokered_name, '/my_evaluate/', $params);
        $this->message_model->add_message('2-21', $brokered_id, $brokered_name, '/my_evaluate/', $params);
      } else {
        $this->message_model->add_message('2-19', $brokered_id, $brokered_name, '/my_evaluate/', $params);
      }
    }
    echo '修改成功';
  }
}

/* End of file appeal.php */
/* Location: ./application/mls_admin/controllers/appeal.php */
