<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cooperate_Success CLASS
 *
 * 合作生效控制器
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          angel_in_us
 */
class Cooperate_Effect extends My_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->helper('page_helper');
    $this->load->library('form_validation');//表单验证
    $this->load->helper('user_helper');
    $this->load->model('cooperate_model');
    $this->load->model('cooperate_report_model');
    $this->load->model('broker_model');
    $this->load->model('api_broker_model');
    $this->load->model('api_broker_sincere_model');
    $this->load->model('sell_house_model');
    $this->load->model('rent_house_model');
    $this->load->model('buy_customer_model');
    $this->load->model('rent_customer_model');
  }

  public function index()
  {
    $num = $this->uri->segment(3);
    $type = $this->uri->segment(4);
    if (empty($num) && empty($type)) {
      $num = 1;
      $type = 1;
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
    $this->load->view('cooperate/cooperate_effect/' . $viewname, $data);

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
    $this->load->model('api_broker_sincere_model');
    $this->load->model('message_base_model');

    //$score_result = array();
    $score_prize = array();

    $id = $this->input->post('id');//合作举报id
    $modify_status = $this->input->post('status_modify');//操作选项（待审核、已通过、未通过）

    //获得合作举报相关数据
    $report_info = $this->cooperate_report_model->get_by_id($id);
    $broker_id = $report_info['broker_id'];//举报人id
    $brokered_id = $report_info['brokered_id'];//被举报人id
    $house_info = $report_info['house_info'];
    $house_arr = unserialize($house_info);
    $numberr = $house_arr['rowid'];//编号
    $number = '';
    //房源编号转换
    if ($report_info['house_type'] == 'sell' && $report_info['type'] == 1) {
      $number = format_info_id($numberr, 'sell');
    }
    if ($report_info['house_type'] == 'rent' && $report_info['type'] == 1) {
      $number = format_info_id($numberr, 'rent');
    }
    //客源编号
    if ($report_info['house_type'] == 'sell' && $report_info['type'] == 2) {
      $number = format_info_id($numberr, 'buy_customer');
    }
    if ($report_info['house_type'] == 'rent' && $report_info['type'] == 2) {
      $number = format_info_id($numberr, 'rent_customer');
    }
    $cooperate_no = $report_info['cooperate_no'];//合作编号
    $type = $report_info['report_type'];
    $cooperate_id = $report_info['cooperate_id'];
    $cond_where = "status = 2 ";
    $cond_where .= " AND cooperate_id = '$cooperate_id'";
    $cond_where .= " AND report_type= '$type'";
    $cond_where .= " AND cooperate_type= 1 ";
    $return_num = $this->cooperate_report_model->count_by($cond_where);

    //获得举报人、被举报人信息
    $allot_broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    $run_broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($brokered_id);

    //审核‘已通过’
    if ($return_num == 0 && $modify_status == 2) {
      if ($type == 1 || $type == 2) {
        $alias = $type == 1 ? 'house_info_false' : 'customer_info_false';
        //奖励
        $score = $this->api_broker_sincere_model->update_infomation($brokered_id, $alias);
        //录入处罚库
        $this->api_broker_sincere_model->punish($broker_id, $brokered_id, $alias, $score, $number, $house_info);
      }
      if ($type == 1) {
        //房源虚假
        //房源下架
        $update_arr = array('status' => 5);
        if (isset($report_info['house_type']) && !empty($report_info['house_type']) && !empty($house_arr['rowid'])) {
          $where_arr = array('id' => intval($house_arr['rowid']));
          //判断求购求租
          if ('sell' == $report_info['house_type']) {
            $this->sell_house_model->update_house($update_arr, $where_arr);
          } else if ('rent' == $report_info['house_type']) {
            $this->rent_house_model->update_house($update_arr, $where_arr);
          }
        }
        $type_id = '1-12';
        $typed = '1-14-2';

        //消息数组
        $params['type'] = 'f';
        $params['id'] = $number;
        $params['reason'] = "房源虚假";
        /**
         * if(isset($score_result['score']) && !empty($score_result['score'])){
         * $params['pf'] = $score_result['score'];
         * $params['name'] = $run_broker_info['truename'];
         * //举报人信息提示
         * //33
         * $this->message_base_model->add_message($type_id,$allot_broker_info['broker_id'],$allot_broker_info['truename'],'/my_growing_punish/index',$params);
         * }**/
        $params['name'] = $allot_broker_info['truename'];
        //被举报人信息提示
        $this->message_base_model->add_message($typed, $run_broker_info['broker_id'], $run_broker_info['truename'], '/my_growing_punish/index', $params);
      } else if ($type == 2) {
        //客源虚假
        //客源下架
        //根据合作id,获得举报客源id
        $cooperate_data = $this->cooperate_model->get_cooperate_by_cid(intval($cooperate_id));
        if (isset($cooperate_data['customer_id']) && !empty($cooperate_data['customer_id'])) {
          $custoemr_id = intval($cooperate_data['customer_id']);
          $where_cond = array('id' => $custoemr_id);
          $update_cond = array('status' => 5);
          //判断求购求租
          if ('sell' == $report_info['house_type']) {
            $this->buy_customer_model->update_customerinfo_by_cond($update_cond, $where_cond);
          } else if ('rent' == $report_info['house_type']) {
            $this->rent_customer_model->update_customerinfo_by_cond($update_cond, $where_cond);
          }
        }
        $type_id = '1-12';
        $typed = '1-14-2';

        //消息数组
        $params['id'] = $number;
        $params['reason'] = "客源虚假";
        /**
         * if(isset($score_result['score']) && !empty($score_result['score'])){
         * $params['pf'] = $score_result['score'];
         * $params['name'] = $run_broker_info['truename'];
         * //举报人信息提示
         * $this->message_base_model->add_message($type_id,$allot_broker_info['broker_id'],$allot_broker_info['truename'],'/my_growing_punish/index',$params);
         * }**/
        $params['name'] = $allot_broker_info['truename'];
        //被举报人信息提示
        $this->message_base_model->add_message($typed, $run_broker_info['broker_id'], $run_broker_info['truename'], '/my_growing_punish/index', $params);
      } else if ($type == 3) {
        //不按协议履行合作
        //奖励
        $func_alias = 'report_info_false';
        $report_score = $this->api_broker_credit_model->increase($broker_id, $func_alias);
        $alias_effect = 'no_accord_agreement_signature';
        $score = $this->api_broker_sincere_model->update_trust($brokered_id, $alias_effect);
        //录入处罚库
        $this->api_broker_sincere_model->punish($broker_id, $brokered_id, $alias_effect, $score, $number, $house_info);
        $type_id = '1-12';
        $typed = '1-14-3';

        //消息数组
        if ($report_info['type']) {
          $params['type'] = 'f';
        }
        $params['id'] = $number;
        $params['reason'] = "不按协议履行合作";
        /**
         * if(isset($score_result['score']) && !empty($score_result['score'])){
         * $params['pf'] = $score_result['score'];
         * $params['name'] = $run_broker_info['truename'];
         * //举报人信息提示
         * $this->message_base_model->add_message($type_id,$allot_broker_info['broker_id'],$allot_broker_info['truename'],'/my_growing_credit/index',$params);
         * }***/
        $params['name'] = $allot_broker_info['truename'];
        //被举报人信息提示
        $this->message_base_model->add_message($typed, $run_broker_info['broker_id'], $run_broker_info['truename'], '/my_growing_punish/index', $params);
      } else if ($type == 4) {
        //其它
        $type_id = '1-12';
        $typed = '1-14-2';
        //消息数组
        if ($report_info['type']) {
          $params['type'] = 'f';
        }
        $params['id'] = $number;
        $params['reason'] = "其他";
        /**
         * if(isset($score_result['score']) && !empty($score_result['score'])){
         * $params['pf'] = $score_result['score'];
         * $params['name'] = $run_broker_info['truename'];
         * //举报人信息提示
         * $this->message_base_model->add_message($type_id,$allot_broker_info['broker_id'],$allot_broker_info['truename'],'/my_growing_punish/index',$params);
         * }**/
        $params['name'] = $allot_broker_info['truename'];
        //被举报人信息提示
        $this->message_base_model->add_message($typed, $run_broker_info['broker_id'], $run_broker_info['truename'], '/my_growing_punish/index', $params);
      }

      //被举报合作，合作终止
      $update_arr = array('esta' => 11);
      $where_arr = array('id' => $report_info['cooperate_id']);
      $this->cooperate_model->update_info_by_cond($update_arr, $where_arr);
      //审核‘未通过’
    } else if ($modify_status == 3) {
      $params['id'] = $number;
      //已改
      //房源虚假
      if ($type == 1) {
        $type_id = '1-13';
        $params['name'] = $run_broker_info['truename'];
        $this->message_base_model->add_message($type_id, $allot_broker_info['broker_id'], $allot_broker_info['truename'], '/my_growing_punish/index', $params);
        //客源虚假
      } else if ($type == 2) {
        $type_id = '1-13';
        $params['name'] = $cooperate_no;
        $this->message_base_model->add_message($type_id, $allot_broker_info['broker_id'], $allot_broker_info['truename'], '/my_growing_punish/index', $params);
        //不按协议履行合作
      } else if ($type == 3) {
        $type_id = '1-13';
        $params['name'] = $cooperate_no;
        $this->message_base_model->add_message($type_id, $allot_broker_info['broker_id'], $allot_broker_info['truename'], '/my_growing_punish/index', $params);
        //其它
      } else if ($type == 4) {
        $type_id = '1-13';
        $params['reason'] = $report_info['report_text'];
        $params['name'] = $cooperate_no;
        $result = $this->message_base_model->add_message($type_id, $allot_broker_info['broker_id'], $allot_broker_info['truename'], '/my_growing_punish/index', $params);
      }
    }
    //合作举报数据状态修改
    $this->cooperate_report_model->update_by_id(array('status' => $modify_status), $id);
    echo '成功';
  }
}

?>
