<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 经纪人诚信管理
 *
 * @package     mls_admin
 * @subpackage  Controllers
 * @category    Controllers
 * @author      angel_in_us
 */
class Broker_trust_manage extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('broker_manage_model');
    $this->load->model('api_broker_model');
    $this->load->model('api_broker_sincere_model');
    $this->load->model('sincere_trust_level_model');
    $this->load->model('sincere_appraise_cooperate_model');
    $this->load->helper('user_helper');
  }

  /**
   * 经纪人诚信管理列表
   * date   : 2015-01-27
   * author : angel_in_us
   */
  public function index()
  {
    $data['title'] = '经纪人诚信管理列表';
    $data['conf_where'] = 'index';

    //查询条件
    $cond_where = array();
    $cond_between = array();
    $order_by = array();
    if (@$_POST['angela_wen'] == 'angel_in_us') {
      $phone = $this->input->post('phone', TRUE);
      $truename = $this->input->post('truename', TRUE);
      $level = $this->input->post('level', TRUE);
      $order_name = $this->input->post('order_name', TRUE);
      $order_way = $this->input->post('order_way', TRUE);
      if ($phone != "") {
        $cond_where = array('phone' => $phone);
      }
      if ($truename != "") {
        $cond_where['truename'] = $truename;
      }
      if ($level != "") {
        $level = intval($level);
        //根据post过来的 等级编号 id 来查询 sincere_trust_level 表中的 up 和 down
        $up_down = $this->broker_manage_model->get_min_max_trust($level);
        $up_down = (array)$up_down[0];
        $data['name_icon'] = $up_down['name_icon'];
        $cond_between = array('down' => $up_down['down'], 'up' => $up_down['up']);
      }
      if ($order_name != "") {
        if ($order_way != "") {
          $order_by = array('order_name' => $order_name, 'order_way' => $order_way);
        }
      }
      if (!empty($cond_where)) {

      }
    }

    //组装等级标识，在模板文件里用
    $level = $this->sincere_trust_level_model->get_all_by('');
    $data['level_list'] = $level;

    //分页开始
    $data['info_num'] = $this->broker_manage_model->get_count_by_cond($cond_where, $cond_between);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['info_num'] ? ceil($data['info_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['info_list'] = $this->broker_manage_model->get_row_by_cond($cond_where, $cond_between, $order_by, $data['offset'], $data['pagesize']);

    //根据经纪人信用分，获得信用等级
    foreach ($data['info_list'] as $key => $value) {
      $data['info_list'][$key]->level = $this->api_broker_sincere_model->get_level_by_trust($value->trust);
    }


    //根据经纪人id，获得好评率
    foreach ($data['info_list'] as $key => $value) {
      $good_rate_arr = $this->api_broker_sincere_model->get_trust_appraise_count($value->broker_id);
      $data['info_list'][$key]->good_rate = $good_rate_arr['good_rate'];
    }
    $this->load->view('broker_manage/info_list', $data);
  }

  /**
   * 来自合作方的评价/我给合作方的评价
   * @param int $broker_id
   */
  public function info_detail($broker_id)
  {
    $data = array();
    $data['conf_where'] = 'index';
    $data['broker_id'] = $broker_id;
    //页面标题
    $data['title'] = '经纪人诚信明细';

    $where = 'id <> 0';
    $pg = $this->input->post('pg');
    $type = $this->input->get('type');
    $data['type'] = $type;
    if ($type) {
      $where .= ' and broker_id = ' . $broker_id;
    } else {
      $where .= ' and partner_id = ' . $broker_id;
    }
    //分页开始
    $data['count'] = $this->sincere_appraise_cooperate_model->count_by($where);
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['count'] ? ceil($data['count']
      / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages']
      && $data['pages'] != 0) ? $data['pages']
      : $data['page'];  //判断跳转页数
    //计算记录偏移量
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);

    //任务信息
    $cooperate_info = $this->sincere_appraise_cooperate_model->get_all_by($where, $data['offset'], $data['pagesize']);


    if (is_full_array($cooperate_info)) {
      $this->load->model('sincere_trust_config_model');
      $config_info = $this->sincere_trust_config_model->get_config();
      foreach ($cooperate_info as $key => $value) {
        $cooperate_info[$key]['trust_name'] = $config_info['appraise_type_description'][$value['trust_type_id']];
        //通过分数获取星星
        $cooperate_info[$key]['info_star'] = $this->api_broker_sincere_model->get_appraise_level($value['infomation']);//信息真实度
        $cooperate_info[$key]['atti_star'] = $this->api_broker_sincere_model->get_appraise_level($value['attitude']);//合作满意度
        $cooperate_info[$key]['busi_star'] = $this->api_broker_sincere_model->get_appraise_level($value['business']);//业务专业度
        /*
          if($type){//我给合作方的评价
              $brokerId = $value['partner_id'];
          }else{
              $brokerId = $value['broker_id'];
          }
          */
        $brokerId = $value['broker_id'];

        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($brokerId);
        $cooperate_info[$key]['truename'] = $brokers['truename'];
        //获取评价人的信用值和等级
        $cooperate_info[$key]['broker_level'] = $this->api_broker_sincere_model->get_trust_level_by_broker_id($brokerId);
      }
    }
    $data['cooperate_info'] = $cooperate_info;
    $this->load->view('broker_manage/info_detail', $data);
  }

  /**
   * 评价失效
   */
  public function modify()
  {
    $id = $this->input->post('id');
    $this->sincere_appraise_cooperate_model->update_by_id(array('status' => 2), $id);
    $this->load->model('appeal_model');
    $this->load->model('message_model');
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
    } else if ($cooperate_baseinfo['apply_type'] == 2) {
      $params['id'] = $cooperate_baseinfo['customer_id'];
      $tbl .= '_customer';
    }
    $params['id'] = format_info_id($params['id'], $tbl);
    //33
    $this->message_model->add_message('2-21', $brokered_id, $brokered_name, '/my_evaluate/', $params);
    echo '修改成功,已失效！';
  }

  /**
   * 处罚记录
   * @param int $broker_id
   */
  public function info_punish($broker_id)
  {
    $this->load->model('sincere_punish_model');
    $data = array();
    $data['conf_where'] = 'index';
    $data['broker_id'] = $broker_id;
    //页面标题
    $data['title'] = '经纪人处罚记录';
    $type = $this->input->get('type');
    $data['type'] = $type;
    $where = 'broker_id = ' . $broker_id;
    $pg = $this->input->post('pg');

    //分页开始
    $data['count'] = $this->sincere_punish_model->count_by($where);
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['count'] ? ceil($data['count']
      / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages']
      && $data['pages'] != 0) ? $data['pages']
      : $data['page'];  //判断跳转页数
    //计算记录偏移量
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);
    $data['config_info'] = $this->sincere_punish_model->get_config();
    //处罚信息
    $punish_info = $this->sincere_punish_model->get_all_by($where, $data['offset'], $data['pagesize']);

    if (is_full_array($punish_info)) {
      $this->load->model('broker_info_model');
      foreach ($punish_info as $key => $value) {
        //echo $value['brokered_id'];exit;
        $brokered_info = $this->broker_info_model->get_by_broker_id($value['brokered_id']);//通过举报人id获取记录
        $punish_info[$key]['brokered_name'] = $brokered_info['truename'];
      }

    }

    $data['punish_info'] = $punish_info;
    $this->load->view('broker_manage/info_punish', $data);
  }
}
/* End of file broker_trust_manage.php */
/* Location: ./application/controllers/broker_trust_manage.php */
