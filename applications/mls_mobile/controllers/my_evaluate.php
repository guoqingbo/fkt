<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-我的评价
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_evaluate extends MY_Controller
{
  /**
   * 当前页码
   *
   * @access private
   * @var string
   */
  private $_current_page = 1;

  /**
   * 每页条目数
   *
   * @access private
   * @var int
   */
  private $_limit = 20;

  /**
   * 偏移
   *
   * @access private
   * @var int
   */
  private $_offset = 0;

  /**
   * 条目总数
   *
   * @access private
   * @var int
   */
  private $_total_count = 0;

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('api_broker_model');
    $this->load->model('cooperate_model');
    $this->load->model('api_broker_sincere_model');
    $this->load->model('sincere_appraise_cooperate_model');
  }

  //我的评价
  public function evaluate_info()
  {
    $data = array();
    $broker_id = $this->input->post('broker_id');
    if (empty($broker_id)) {
      $broker_info = $this->user_arr;
      $broker_id = $broker_info['broker_id'];
    } else {
      $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    }
    //获取经纪人的基本信息
    if (is_full_array($broker_info)) {
      $data['photo'] = $broker_info['photo'];//个人头像
      $data['truename'] = $broker_info['truename'];//姓名
      $data['register_time'] = date('Y-m-d', $broker_info['register_time']);//注册时间
      $company = $this->api_broker_model->get_by_agency_id($broker_info['company_id']);
      $data['company_name'] = $company['name'];//公司名称
      $data['agency_name'] = $broker_info['agency_name'];//门店名称
      //$data['cop_suc_ratio'] = $broker_info['cop_suc_ratio'];//合作成功率

      //合作成功率
      $this->load->model('cooperate_suc_ratio_base_model');
      $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id);
      //合作成功率
      $data['cop_suc_ratio'] = $cop_succ_ratio_info['cop_succ_ratio'];
      //合作成功率平均值
      $avg_cop_suc_ratio = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
      $data['differ_suc_ratio'] = $avg_cop_suc_ratio > 0 ? strip_end_0(($cop_succ_ratio_info['cop_succ_ratio'] - $avg_cop_suc_ratio) / $avg_cop_suc_ratio) : 0;

      //好评率
      $trust_appraise_count = $this->api_broker_sincere_model->
      get_trust_appraise_count($broker_id);
      if (empty($trust_appraise_count['good_rate'])) {
        $good_rate = '--';
      } else {
        $good_rate = strip_end_0($trust_appraise_count['good_rate']);
      }
      $data['good_rate'] = $good_rate;

      //好评率比平均值高
      $good_avg_rate = $this->api_broker_sincere_model->good_avg_rate($broker_id);
      $data['differ_good_rate'] = $good_avg_rate['good_rate_avg_high'];

      //统计收到别人发起的合作数量
      $data['received'] = $this->cooperate_model->get_cooperate_num_by_cond('brokerid_a = ' . $broker_id);
      //统计向别人发起的合作数量
      $data['initiate'] = $this->cooperate_model->get_cooperate_num_by_cond('brokerid_b = ' . $broker_id);
      //统计收到别人发起的合作中我接受的数量
      $data['accept'] = $this->cooperate_model->get_cooperate_num_by_cond('esta = 4 and brokerid_a = ' . $broker_id);
      //统计向别人发起的合作中被对方接受的数量
      $data['accepted'] = $this->cooperate_model->get_cooperate_num_by_cond('esta = 4 and brokerid_b = ' . $broker_id);


      //获取经纪人的信用值和等级
      $trust_level = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
      unset($trust_level['level']);
      $data['trust_level'] = $trust_level;
      //获取好评率/总数/好评/中评/差评
      //$data['count_info'] = $this->api_broker_sincere_model->get_trust_appraise_count($broker_id);
      //获取经纪人动态评分基本统计信息
      $appraise_info = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);
      unset($appraise_info['infomation']['level']);
      unset($appraise_info['attitude']['level']);
      unset($appraise_info['business']['level']);
      $data['appraise_info'] = $appraise_info;

      //列表信息
      $pg = $this->input->post('page');
      $pagesize = $this->input->post('pagesize');
      $type = $this->input->post('type');//0:来自合作方的评价 1:我给合作方的评价
      $trust = $this->input->post('trust');//0:全部 1:好评 2:中评 3:差评


      if ($type) {
        $where = 'broker_id = ' . $broker_id;
        $data['good_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 1');
        $data['medium_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 2');
        $data['bad_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 3');
      } else {
        $where = 'partner_id = ' . $broker_id;
        $data['good_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 1');
        $data['medium_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 2');
        $data['bad_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 3');
      }

      if ($trust) {
        $where .= ' and trust_type_id = ' . $trust;
      }
      $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
      $this->_init_pagination($page, $pagesize);
      $this->_total_count = $this->sincere_appraise_cooperate_model->count_by($where);

      //任务信息
      $evaluate_lists = $this->sincere_appraise_cooperate_model->get_all_by($where, $this->_offset, $this->_limit);
      $evaluate_list = array();
      if (is_full_array($evaluate_lists)) {
        $this->load->model('sincere_trust_config_model');
        $config_info = $this->sincere_trust_config_model->get_config();
        foreach ($evaluate_lists as $key => $value) {
          $evaluate_list[$key]['infomation'] = $value['infomation'];//真实度值
          $evaluate_list[$key]['attitude'] = $value['attitude'];//满意度值
          $evaluate_list[$key]['business'] = $value['business'];//业务专业度值
          $evaluate_list[$key]['content'] = $value['content'];
          $evaluate_list[$key]['create_time'] = date('m-d H:i', $value['create_time']);
          $evaluate_list[$key]['trust_name'] = $config_info['appraise_type_description'][$value['trust_type_id']];
          if ($type) {//我给合作方的评价
            $brokerId = $value['partner_id'];
          } else {
            $brokerId = $value['broker_id'];
          }
          $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($brokerId);
          $evaluate_list[$key]['truename'] = $brokers['truename'];
          $evaluate_list[$key]['photo'] = $brokers['photo'];
          //获取合作方的信用值和等级
          $trust_level = $this->api_broker_sincere_model->get_trust_level_by_broker_id($brokerId);
          unset($trust_level['level']);
          $evaluate_list[$key]['trust_level'] = $trust_level;
        }
      }
      $data['evaluate_list'] = $evaluate_list;

      $this->result(1, '我的评价信息获取成功', $data);
      return;
    } else {
      $this->result(2, '无此经纪人信息');
      return;
    }
  }

  //查看他人的评级
  /*public function evaluate()
  {
      $data = array();
      $broker_id = $this->input->post('broker_id');
      if($broker_id){
          //获取经纪人的基本信息
          $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
          if(is_full_array($broker_info)){
              $data['photo'] = $broker_info['photo'];//个人头像
              $data['truename'] = $broker_info['truename'];//姓名
              $data['register_time'] = date('Y-m-d',$broker_info['register_time']);//注册时间
              $data['cop_suc_ratio'] = $broker_info['cop_suc_ratio'];//合作成功率

              //合作成功率平均值
              $avg_cop_suc_ratio = $this->api_broker_model->avg_cop_suc_ratio();

              $data['differ_suc_ratio'] = $data['cop_suc_ratio'] - $avg_cop_suc_ratio;//与平均值的差值

              //好评率
              $trust_appraise_count = $this->api_broker_sincere_model->
              get_trust_appraise_count($broker_id);
              $data['good_rate'] = $trust_appraise_count['good_rate'];
              //平均好评率
              $this->load->model('sincere_trust_count_model');
              $good_avg_rate_info = $this->sincere_trust_count_model->good_avg_rate();
              $good_avg_rate = round($good_avg_rate_info['good_rate'],2);

              $data['differ_good_rate'] = $data['good_rate'] - $good_avg_rate;//与平均值的差值
              //统计收到别人发起的合作数量
              $data['received']=$this->cooperate_model->get_cooperate_num_by_cond('brokerid_a = '.$broker_id);
              //统计向别人发起的合作数量
              $data['initiate']=$this->cooperate_model->get_cooperate_num_by_cond('brokerid_b = '.$broker_id);
              //统计收到别人发起的合作中我接受的数量
              $data['accept']=$this->cooperate_model->get_cooperate_num_by_cond('esta = 2 and brokerid_a = '.$broker_id);
              //统计向别人发起的合作中被对方接受的数量
              $data['accepted']=$this->cooperate_model->get_cooperate_num_by_cond('esta = 2 and brokerid_b = '.$broker_id);


              //获取经纪人的信用值和等级
              $trust_level = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
              unset($trust_level['level']);
              $data['trust_level'] = $trust_level;
              //获取好评率/总数/好评/中评/差评
              //$data['count_info'] = $this->api_broker_sincere_model->get_trust_appraise_count($broker_id);
              //获取经纪人动态评分基本统计信息
              $appraise_info = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);
              unset($appraise_info['infomation']['level']);
              unset($appraise_info['attitude']['level']);
              unset($appraise_info['business']['level']);
              $data['appraise_info'] = $appraise_info;

              //列表信息
              $pg = $this->input->post('page');
              $pagesize = $this->input->post('pagesize');

              $trust = $this->input->post('trust');//0:全部 1:好评 2:中评 3:差评

              $where = 'partner_id = '.$broker_id;
              $data['good_count'] = $this->sincere_appraise_cooperate_model->count_by($where.' and trust_type_id = 1');
              $data['medium_count'] = $this->sincere_appraise_cooperate_model->count_by($where.' and trust_type_id = 2');
              $data['bad_count'] = $this->sincere_appraise_cooperate_model->count_by($where.' and trust_type_id = 3');

              if($trust){
                  $where .= ' and trust_type_id = '.$trust;
              }
              $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
              $this->_init_pagination($page,$pagesize);
              $this->_total_count = $this->sincere_appraise_cooperate_model->count_by($where);

              //任务信息
              $evaluate_lists = $this->sincere_appraise_cooperate_model->get_all_by($where,$this->_offset, $this->_limit);
              $evaluate_list = array();
              if(is_full_array($evaluate_lists)){
                  $this->load->model('sincere_trust_config_model');
                  $config_info = $this->sincere_trust_config_model->get_config();
                  foreach ($evaluate_lists as $key=>$value){
                      $evaluate_list[$key]['infomation'] = $value['infomation'];//真实度值
                      $evaluate_list[$key]['attitude'] = $value['attitude'];//满意度值
                      $evaluate_list[$key]['business'] = $value['business'];//业务专业度值
                      $evaluate_list[$key]['content'] = $value['content'];
                      $evaluate_list[$key]['create_time'] = date('m-d H:i',$value['create_time']);
                      $evaluate_list[$key]['trust_name'] = $config_info['appraise_type_description'][$value['trust_type_id']];
                      $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
                      $evaluate_list[$key]['truename'] = $broker_info['truename'];
                      $evaluate_list[$key]['photo'] = $broker_info['photo'];
                      //获取评价人的信用值和等级
                      $trust_level = $this->api_broker_sincere_model->get_trust_level_by_broker_id($value['broker_id']);
                      unset($trust_level['level']);
                      $evaluate_list[$key]['trust_level'] = $trust_level;
                  }
              }
              $data['evaluate_list'] = $evaluate_list;

              $this->result(1,'评价信息获取成功',$data);
              return;
          }else{
              $this->result(2,'无此经纪人信息');
              return;
          }

      }else{
          $this->result(0,'参数非法');
          return;
      }

  }*/

  //评价列表
  public function evaluate_list()
  {
    $data = array();
    $broker_id = $this->input->post('broker_id');
    if (empty($broker_id)) {
      $broker_id = $this->user_arr['broker_id'];
    }

    $pg = $this->input->post('page');
    $pagesize = $this->input->post('pagesize');
    $type = $this->input->post('type');//0:来自合作方的评价 1:我给合作方的评价
    $trust = $this->input->post('trust');//0:全部 1:好评 2:中评 3:差评


    if ($type) {
      $where = 'broker_id = ' . $broker_id;
      $data['good_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 1');
      $data['medium_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 2');
      $data['bad_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 3');
    } else {
      $where = 'partner_id = ' . $broker_id;
      $data['good_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 1');
      $data['medium_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 2');
      $data['bad_count'] = $this->sincere_appraise_cooperate_model->count_by($where . ' and trust_type_id = 3');
    }


    if ($trust) {
      $where .= ' and trust_type_id = ' . $trust;
    }
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page, $pagesize);
    $this->_total_count = $this->sincere_appraise_cooperate_model->count_by($where);

    //任务信息
    $evaluate_lists = $this->sincere_appraise_cooperate_model->get_all_by($where, $this->_offset, $this->_limit);
    $evaluate_list = array();
    if (is_full_array($evaluate_lists)) {
      $this->load->model('sincere_trust_config_model');
      $config_info = $this->sincere_trust_config_model->get_config();
      foreach ($evaluate_lists as $key => $value) {
        $evaluate_list[$key]['infomation'] = $value['infomation'];//真实度值
        $evaluate_list[$key]['attitude'] = $value['attitude'];//满意度值
        $evaluate_list[$key]['business'] = $value['business'];//业务专业度值
        $evaluate_list[$key]['content'] = $value['content'];
        $evaluate_list[$key]['create_time'] = date('m-d H:i', $value['create_time']);
        $evaluate_list[$key]['trust_name'] = $config_info['appraise_type_description'][$value['trust_type_id']];
        if ($type) {//我给合作方的评价
          $brokerId = $value['partner_id'];
        } else {
          $brokerId = $value['broker_id'];
        }
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($brokerId);
        $evaluate_list[$key]['truename'] = $brokers['truename'];
        $evaluate_list[$key]['photo'] = $brokers['photo'];
        //获取评价人的信用值和等级
        $trust_level = $this->api_broker_sincere_model->get_trust_level_by_broker_id($brokerId);
        unset($trust_level['level']);
        $evaluate_list[$key]['trust_level'] = $trust_level;
      }
    }
    $data['evaluate_list'] = $evaluate_list;

    $this->result(1, '我的评价信息列表获取成功', $data);
    return;

  }

  /*
   * 提交申诉
   */
  public function modify()
  {
    $broker_id = $this->user_arr['broker_id'];
    $appraise_id = $this->input->post('id');
    $reason = $this->input->post('content');
    $photo_url = $this->input->post('photo_url');
    $photo_name = $this->input->post('photofile');
    $this->api_broker_sincere_model->appraise_appeal($broker_id, $appraise_id, $photo_url, $photo_name, $reason);
    $this->sincere_appraise_cooperate_model->update_by_id(array('status' => 1), $appraise_id);
    echo '申诉成功，请等待审核';
  }

  /**
   * 初始化分页参数
   *
   * @access public
   * @param  int $current_page
   * @param  int $page_size
   * @return void
   */
  private function _init_pagination($current_page = 1, $page_size = 0)
  {
    /** 当前页 */
    $this->_current_page = ($current_page && is_numeric($current_page)) ?
      intval($current_page) : 1;

    /** 每页多少项 */
    $this->_limit = ($page_size && is_numeric($page_size)) ?
      intval($page_size) : $this->_limit;

    /** 偏移量 */
    $this->_offset = ($this->_current_page - 1) * $this->_limit;

    if ($this->_offset < 0) {
      redirect(base_url());
    }
  }

}

/* End of file my_evaluate.php */
/* Location: ./application/mls/controllers/my_evaluate.php */
