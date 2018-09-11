<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cooperate_Success CLASS
 *
 * 合作成功控制器
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          angel_in_us
 */
class Cooperate_Success extends My_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->helper('page_helper');
    $this->load->library('form_validation');//表单验证
    $this->load->helper('user_helper');
    $this->load->model('cooperate_report_model');
    $this->load->model('broker_model');
    $this->load->model('api_broker_model');
    $this->load->model('api_broker_sincere_model');
  }

  public function index()
  {
    $num = $this->uri->segment(3);
    $type = $this->uri->segment(4);
    if (empty($num) && empty($type)) {
      $num = 1;
      $type = 2;
    }
    $num = intval($num);
    $type = intval($type);
    $data = array();
    $post_param = $this->input->post(NULL, TRUE);//获取所有post信息
    $data['post_param'] = $post_param;
    $this->load->model('cooperate_report_model');//加载合作举报model
    $where = $this->get_cusmoter($post_param, $num, $type);//获取查询条件
    $data['conf_where'] = 'index';
    //分页开始
    $limit = 10;//设定每一页显示的记录数
    $data['user_num'] = $this->cooperate_report_model->count_by($where);//总记录数
    $data['pagesize'] = $limit;
    $data['pages'] = $data['user_num'] ? ceil($data['user_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $lists = $this->cooperate_report_model->get_all_by($where, $data['offset'], $limit);//获取符合条件的所有数据
    $data['lists'] = $lists;
    if ($type == 1) {
      $data['title'] = '合作生效举报管理';
    } else {
      $data['title'] = '合作成功举报管理';
    }
    //判断加载哪个模板
    $viewname = '';
    if ($num == 1) {
      $viewname = 'index';
    }
    if ($num == 2) {
      $viewname = 'house_rent';
    }
    if ($num == 3) {
      $viewname = 'customer_sell';
    }
    if ($num == 4) {
      $viewname = 'customer_rent';
    }
    $this->load->view('cooperate/' . $viewname, $data);

  }

  //设置查询条件
  public function get_cusmoter($post_param, $num, $type)
  {
    $where = '';
    if ($type == 1) {
      $where = 'cooperate_type = 1';//生效合作
    } else {
      $where = 'cooperate_type = 2';//成功合作
    }
    //审核状态
    if ($post_param['status']) {
      $where .= ' AND status =' . $post_param['status'] . '';
    }
    //举报类型
    if ($post_param['type']) {
      $where .= ' AND report_type =' . $post_param['type'] . '';
    }
    //举报人的姓名
    if ($post_param['broker_name']) {
      $where .= ' AND broker_name LIKE  "%' . $post_param['broker_name'] . '%"';
    }
    //被举报人的姓名
    if ($post_param['brokered_name']) {
      $where .= ' AND brokered_name LIKE  "%' . $post_param['brokered_name'] . '%"';
    }
    //出售公盘
    if ($num == 1) {
      $where .= ' AND house_type = "sell" AND type= 1';
    }
    //出租公盘
    if ($num == 2) {
      $where .= ' AND house_type = "rent" AND type= 1';
    }
    //求购公客
    if ($num == 3) {
      $where .= ' AND house_type = "sell" AND type= 2';
    }
    //求租公客
    if ($num == 4) {
      $where .= ' AND house_type = "rent" AND type= 2';
    }
    return $where;
  }

  public function modify()
  {
    $id = $this->input->post('id');
    $modify_status = $this->input->post('status_modify');
    $this->load->model('api_broker_sincere_model');
    $report_info = $this->cooperate_report_model->get_by_id($id);
    $broker_id = $report_info['broker_id'];//举报人id
    $brokered_id = $report_info['brokered_id'];//被举报人id
    $house_info = $report_info['house_info'];
    $house_arr = unserialize($house_info);
    $numberr = $house_arr['rowid'];//编号
    $number = $report_info['cooperate_no'];
    /**
     * //房源编号转换
     * if($report_info['house_type']=='sell' && $report_info['type']==1 ){
     * $number=format_info_id($numberr,'sell');
     * }
     * if($report_info['house_type']=='rent' && $report_info['type']==1 ){
     * $number=format_info_id($numberr,'rent');
     * }
     * //客源编号
     * if($report_info['house_type']=='sell' && $report_info['type']==2 ){
     * $number=format_info_id($numberr,'buy_customer');
     * }
     * if($report_info['house_type']=='rent' && $report_info['type']==2 ){
     * $number=format_info_id($numberr,'rent_customer');
     * }**/
    $type = $report_info['report_type'];
    $cooperate_id = $report_info['cooperate_id'];
    $cooperate_no = $report_info['cooperate_no'];//合作编号
    $cond_where = "status = 2 ";
    $cond_where .= " AND cooperate_id = '$cooperate_id'";
    $cond_where .= " AND report_type= '$type'";
    $cond_where .= " AND cooperate_type= 2 ";
    $return_num = $this->cooperate_report_model->count_by($cond_where);
    $this->load->model('message_base_model');
    $allot_broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    $run_broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($brokered_id);
    $cooperate_baseinfo = array();
    $cooperate_baseinfo = $this->cooperate_model->get_cooperate_baseinfo_by_order_sn($cooperate_no);
    if ($cooperate_baseinfo['apply_type'] == 1) {
      $params['type'] = "f";
      $params['id'] = $cooperate_baseinfo['rowid'];
      $params['pf'] = 1;
      $params['jf'] = 50;
    } else if ($cooperate_baseinfo['apply_type'] == 2) {
      $params['id'] = $cooperate_baseinfo['customer_id'];
      $params['jf'] = 50;
    }
    if ($return_num == 0 && $modify_status == 2) {
      if ($type == 3) {
        //举报人奖励
        //$score_result=$this->api_broker_credit_model->increase($broker_id, 'report_trade_success');
        $func_alias = 'report_trade_success';
        $alias_effect = 'no_accord_agreement_trade_success';
        $score = $this->api_broker_sincere_model->update_trust($brokered_id, $alias_effect);
        //录入处罚库
        $this->api_broker_sincere_model->punish($broker_id, $brokered_id, $alias_effect, $score, $number, $house_info);
        $type_id = '1-12';
        $typed = '1-14-3';
        $score_prize = array();
        //举报人信息提示
        $params['name'] = $run_broker_info['truename'];
        $params['prize'] = 0;
        //33
        $this->message_base_model->add_message($type_id, $allot_broker_info['broker_id'], $allot_broker_info['truename'], '/my_growing_punish/index', $params);
        //被举报人信息提示
        $params['name'] = $allot_broker_info['truename'];
        $this->message_base_model->add_message($typed, $run_broker_info['broker_id'], $run_broker_info['truename'], '/my_growing_punish/index', $params);

      }

    }
    //不按协议未通过审核
    if ($modify_status == 3 && $type == 3) {
      $type_id = '1-13';
      $params['name'] = $allot_broker_info['truename'];
      $this->message_base_model->add_message($type_id, $allot_broker_info['broker_id'], $allot_broker_info['truename'], '/my_growing_punish/index', $params);
    }


    $this->cooperate_report_model->update_by_id(array('status' => $modify_status), $id);
    echo '成功';

  }

}

?>
