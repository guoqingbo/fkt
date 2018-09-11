<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 诚信信息
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_trust_info extends MY_Controller
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
  private $_limit = 2;

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
    $this->load->model('agency_model');
  }

  public function index()
  {
    //当前登录经纪人
    $this_login_broker_id = $this->user_arr['broker_id'];
    //数据归属经纪人
    $broker_id = $this->input->get('broker_id');
    $data_id = $this->input->get('data_id');
    $type = $this->input->get('type');
    //判断经纪人联系方式是否需要显示
    $is_phone_show = true;
    if (!empty($data_id) && !empty($type)) {
      //是否显示经纪人电话。如果当前经纪人未参与到该房源的合作，不显示。
      $this->load->model('cooperate_model');
      if ('sell_house' == $type) {
        $where_cond = 'tbl = "sell" and rowid = "' . $data_id . '" and apply_type = 1';
        $query_num = $this->cooperate_model->get_cooperate_num_apply($this_login_broker_id, $where_cond);
      } else if ('rent_house' == $type) {
        $where_cond = 'tbl = "rent" and rowid = "' . $data_id . '" and apply_type = 1';
        $query_num = $this->cooperate_model->get_cooperate_num_apply($this_login_broker_id, $where_cond);
      } else if ('buy_customer' == $type) {
        $where_cond = 'tbl = "sell" and customer_id = "' . $data_id . '" and apply_type = 2';
        $query_num = $this->cooperate_model->get_cooperate_num_apply($this_login_broker_id, $where_cond);
      } else if ('rent_customer' == $type) {
        $where_cond = 'tbl = "rent" and customer_id = "' . $data_id . '" and apply_type = 2';
        $query_num = $this->cooperate_model->get_cooperate_num_apply($this_login_broker_id, $where_cond);
      }
      //是自己的房源，展示电话号码
      if ($this_login_broker_id == $broker_id) {
        $is_phone_show = true;
      } else {
        if (is_int($query_num) && $query_num > 0) {
          $is_phone_show = true;
        } else {
          $is_phone_show = false;
        }
      }
    }

    $data = array();
    $data['is_phone_show'] = $is_phone_show;
    $this->load->model('report_model');

    //举报总次数
    $data['report_sun'] = $this->report_model->count_by('broker_id = ' . $broker_id);

    //合作成功率平均值
    $this->load->model('cooperate_suc_ratio_base_model');
    $data['avg_cop_suc_ratio'] = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
    $data['cop_succ_ratio_info'] = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id);

    //获取经纪人的基本信息
    $data['broker_info'] = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    $data['broker_info']['trust_level'] = $this->api_broker_sincere_model->
    get_level_by_trust($data['broker_info']['trust']);
    //获得公司名
    $company_name = '';
    if (!empty($data['broker_info']['company_id'])) {
      $company_data = $this->agency_model->get_by_id(intval($data['broker_info']['company_id']));
    }
    if (is_full_array($company_data)) {
      $company_name = $company_data['name'];
    }
    $data['broker_info']['company_name'] = $company_name;
    //平均好评率
    $data['good_avg_rate'] = $this->api_broker_sincere_model->good_avg_rate($broker_id);

    //细节分值统计
    $data['appraise_avg'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);
    //页面标题
    $data['page_title'] = '诚信信息';
    $data['broker_id'] = $broker_id;

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css'
      . ',mls/css/v1.0/myStyle.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/personal_center.js');

    echo $this->load->view('trust/index', $data);
  }

  public function evaluate($broker_id)
  {
    $data = array();
    //获取经纪人的基本信息
    $data['broker_id'] = $broker_id;
    $data['broker_info'] = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    //合作成功率平均值
    $this->load->model('cooperate_suc_ratio_base_model');
    $data['avg_cop_suc_ratio'] = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
    $data['cop_succ_ratio_info'] = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id);

    //统计收到别人发起的合作数量
    $data['received'] = $this->cooperate_model->get_cooperate_num_by_cond('brokerid_a = ' . $broker_id);
    //统计向别人发起的合作数量
    $data['initiate'] = $this->cooperate_model->get_cooperate_num_by_cond('brokerid_b = ' . $broker_id);
    //统计收到别人发起的合作中我接受的数量
    $data['accept'] = $this->cooperate_model->get_cooperate_num_by_cond('esta = 4 and brokerid_a = ' . $broker_id);
    //统计向别人发起的合作中被对方接受的数量
    $data['accepted'] = $this->cooperate_model->get_cooperate_num_by_cond('esta = 4 and brokerid_b = ' . $broker_id);

    $data['broker_info']['trust_level'] = $this->api_broker_sincere_model->
    get_level_by_trust($data['broker_info']['trust']);
    //好评率
    $data['trust_appraise_count'] = $this->api_broker_sincere_model->
    get_trust_appraise_count($broker_id);
    //细节分值统计
    $data['appraise_avg_info'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);
    //经纪人动态评分详细信息(得星星)
    $appraise_info = $this->api_broker_sincere_model->get_appraise_info($broker_id);

    $appraise_info['infomation_score_html'] = $this->api_broker_sincere_model->get_appraise_level($data['appraise_avg_info']['infomation']['score']);
    $appraise_info['attitude_score_html'] = $this->api_broker_sincere_model->get_appraise_level($data['appraise_avg_info']['attitude']['score']);
    $appraise_info['business_score_html'] = $this->api_broker_sincere_model->get_appraise_level($data['appraise_avg_info']['business']['score']);

    for ($i = 1; $i <= 5; $i++) {
      //信息真实度
      if ($appraise_info['infomation_sum'] != 0) {
        $appraise_info['infomation'][$i]['percent'] = round($appraise_info['infomation'][$i]['count'] / $appraise_info['infomation_sum'] * 100);
      } else {
        $appraise_info['infomation'][$i]['percent'] = 0;
      }
      //态度满意度
      if ($appraise_info['attitude_sum'] != 0) {
        $appraise_info['attitude'][$i]['percent'] = round($appraise_info['attitude'][$i]['count'] / $appraise_info['attitude_sum'] * 100);
      } else {
        $appraise_info['attitude'][$i]['percent'] = 0;
      }
      //业务专业度
      if ($appraise_info['business_sum'] != 0) {
        $appraise_info['business'][$i]['percent'] = round($appraise_info['business'][$i]['count'] / $appraise_info['business_sum'] * 100);
      } else {
        $appraise_info['business'][$i]['percent'] = 0;
      }
    }
    $data['appraise_info'] = $appraise_info;
    $pg = $this->input->post('page');
    $type = $this->input->get('type');
    if ($type) {
      $where .= 'broker_id = ' . $broker_id;
    } else {
      $where .= 'partner_id = ' . $broker_id;
    }
    $trust = $this->input->get('trust');
    $data['trust'] = $trust;
    if ($trust) {
      $where .= ' and trust_type_id = ' . $trust;
    }
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->sincere_appraise_cooperate_model->count_by($where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $pg,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');
    //任务信息
    $cooperate_info = $this->sincere_appraise_cooperate_model->get_all_by($where, $this->_offset, $this->_limit);
    if (is_full_array($cooperate_info)) {
      $this->load->model('sincere_trust_config_model');
      $this->load->model('house_config_model');

      $house_config = $this->house_config_model->get_config();
      $config_info = $this->sincere_trust_config_model->get_config();
      foreach ($cooperate_info as $key => $value) {

        $house_info = unserialize($value['house_info']);

        $cooperate_info[$key]['house_info_add']['fitment_name'] = $house_config['fitment'][$house_info['fitment']];
        $cooperate_info[$key]['house_info_add']['forward_name'] = $house_config['forward'][$house_info['forward']];

        $cooperate_info[$key]['trust_name'] = $config_info['appraise_type_description'][$value['trust_type_id']];
        //通过分数获取星星
        $cooperate_info[$key]['info_star'] = $this->api_broker_sincere_model->get_appraise_level($value['infomation']);//信息真实度
        $cooperate_info[$key]['atti_star'] = $this->api_broker_sincere_model->get_appraise_level($value['attitude']);//合作满意度
        $cooperate_info[$key]['busi_star'] = $this->api_broker_sincere_model->get_appraise_level($value['business']);//业务专业度

        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $cooperate_info[$key]['truename'] = $brokerinfo['truename'];
        //获取评价人的信用值和等级
        $cooperate_info[$key]['broker_level'] = $this->api_broker_sincere_model->get_trust_level_by_broker_id($value['broker_id']);
      }
    }
    $data['cooperate_info'] = $cooperate_info;

    //页面标题
    $data['page_title'] = $data['broker_info']['truename'] . '的评价';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/disk.js');
    $this->view('trust/evaluate', $data);
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

/* End of file my_trust_info.php */
/* Location: ./application/mls/controllers/my_trust_info.php */
