<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 房客源合作控制器
 * @package     mls
 * @subpackage  Controllers
 * @category    Controllers
 * @author      fisher
 */
class Cooperate_admin extends MY_Controller
{

  /**
   * 经纪人id
   *
   * @access private
   * @var int
   */
  private $_broker_id = 0;

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
  private $_limit = 15;

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
  }


  /*
   * 发起合作申请
   * @param string $tbl (sell/rent)
   * @param int $rowid
   * @param int $broker_a_id
   * @param int $broker_b_id
   */
  public function index()
  {
    $this->apply_cooperate();
  }


  /**
   * 发起的合作申请
   *
   * @access  public
   * @return  void
   */
  public function send_order_list()
  {
    $this->_order_list('send');
  }


  /**
   * 收到的合作申请
   *
   * @access  public
   * @return  void
   */
  public function accept_order_list()
  {
    $this->_order_list('accept');
  }


  //我接收的合作详情页
  public function my_accept_order($c_id, $city)
  {
    $is_login = $this->broker_model->check_online();
    if (!$is_login) {
      $init_data_session = array(
        'broker_id' => 10,
        'city_spell' => $city,
        'city_id' => 1
      );
      $this->broker_model->set_user_session($init_data_session);
      $this->broker_model->check_online();
    }
    $this->load->model('cooperate_model');

    $c_id = intval($c_id);

    $cooperate_info = array();
    $log_arr = array();
    //$data['brokerid'] = $this->user_arr['broker_id']; //经纪人编号

    if ($c_id > 0) {
      $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);

      if (is_array($cooperate_info) && !empty($cooperate_info)) {
        //经纪人基础信息和信用积分模块
        $this->load->model('api_broker_base_model');
        $this->load->model('api_broker_sincere_model');

        //合同房源信息
        $cooperate_info['houseinfo'] = !empty($cooperate_info['house']) ?
          unserialize($cooperate_info['house']) : array();
        //合同甲方经济人信息
        $cooperate_info['brokerinfo_a'] = !empty($cooperate_info['broker_a']) ?
          unserialize($cooperate_info['broker_a']) : array();
        $broker_a_id = intval($cooperate_info['brokerinfo_a']['broker_id']);

        //甲方经纪人积分、信用值信息
        $cooperate_info['appraise_avg_info_a'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_a_id);
        $cooperate_info['broker_a_now'] = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_a_id);
        $cooperate_info['trust_level_a'] = $this->api_broker_sincere_model->get_level_by_trust($cooperate_info['broker_a_now']['trust']);

        //合同乙方经纪人信息
        $cooperate_info['brokerinfo_b'] = !empty($cooperate_info['broker_b']) ?
          unserialize($cooperate_info['broker_b']) : array();
        //已方经纪人积分、信用值信息
        $broker_b_id = intval($cooperate_info['brokerinfo_b']['broker_id']);
        $cooperate_info['appraise_avg_info_b'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_b_id);
        $cooperate_info['broker_b_now'] = $this->api_broker_base_model->get_baseinfo_by_broker_id($broker_b_id);
        $cooperate_info['trust_level_b'] = $this->api_broker_sincere_model->get_level_by_trust($cooperate_info['broker_b_now']['trust']);

        //对于分公司查找总公司
        if ($cooperate_info['brokerinfo_a']['company_id'] !== 0) {
          $company_id_a = $cooperate_info['brokerinfo_a']['company_id'];
          //调用公司模型
          $this->load->model('agency_model');
          $agency_a = $this->agency_model->get_by_id($company_id_a);
          $cooperate_info['company_name_a'] = $agency_a['name'];
        }
        if ($cooperate_info['brokerinfo_b']['company_id'] !== 0) {
          $company_id_b = $cooperate_info['brokerinfo_b']['company_id'];
          //调用公司模型
          $this->load->model('agency_model');
          $agency_b = $this->agency_model->get_by_id($company_id_b);
          $cooperate_info['company_name_b'] = $agency_b['name'];
        }

        //合同取消原因信息
        $cooperate_info['cancel_reason'] = !empty($cooperate_info['cancel_reason']) ?
          unserialize($cooperate_info['cancel_reason']) : array();
        //合同拒绝原因信息
        $cooperate_info['refuse_reason'] = !empty($cooperate_info['refuse_reason']) ?
          unserialize($cooperate_info['refuse_reason']) : array();
        //合同佣金分配信息
        $cooperate_info['ratio'] = !empty($cooperate_info['ratio']) ?
          unserialize($cooperate_info['ratio']) : array();

        $rowid = $cooperate_info['rowid'];

        if ($cooperate_info['step'] < 2) {
          if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'sell') {
            if ($cooperate_info['step'] < 2) {
              $this->load->model('sell_house_share_ratio_model');
              $cooperate_info['init_ratio'] = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
            }

            //加载MODEL
            $this->load->model('sell_house_model');
            $this->sell_house_model->set_search_fields(array('broker_id'));
            $this->sell_house_model->set_id($rowid);
            $owner_arr = $this->sell_house_model->get_info_by_id();
            $data['house_owner'] = $owner_arr['broker_id'];

            $cooperate_info['master_a'] = '买方';
            $cooperate_info['master_b'] = '卖方';
          } else if (!empty($cooperate_info['tbl']) && $cooperate_info['tbl'] == 'rent') {
            if ($cooperate_info['step'] < 2) {
              $this->load->model('rent_house_share_ratio_model');
              $cooperate_info['init_ratio'] = $this->rent_house_share_ratio_model->get_house_ratio_by_rowid($rowid);
            }

            //加载MODEL
            $this->load->model('rent_house_model');
            $this->rent_house_model->set_search_fields(array('broker_id'));
            $this->rent_house_model->set_id($rowid);
            $owner_arr = $this->rent_house_model->get_info_by_id();
            $data['house_owner'] = $owner_arr['broker_id'];

            $cooperate_info['master_a'] = '承租方';
            $cooperate_info['master_b'] = '租赁方';
          }
        }
      }

      //客源申请
      if (2 == $cooperate_info['apply_type']) {
        $house_broker_info = $cooperate_info['brokerinfo_b'];
        $customer_broker_info = $cooperate_info['brokerinfo_a'];
        $trust_info_house = $cooperate_info['trust_level_b'];
        $trust_info_customer = $cooperate_info['trust_level_a'];
        $appraise_avg_info_house = $cooperate_info['appraise_avg_info_b'];
        $appraise_avg_info_customer = $cooperate_info['appraise_avg_info_a'];
        $broker_house_now = $cooperate_info['broker_b_now'];
        $broker_customer_now = $cooperate_info['broker_a_now'];
      } else {
        //房源申请
        $house_broker_info = $cooperate_info['brokerinfo_a'];
        $customer_broker_info = $cooperate_info['brokerinfo_b'];
        $trust_info_house = $cooperate_info['trust_level_a'];
        $trust_info_customer = $cooperate_info['trust_level_b'];
        $appraise_avg_info_house = $cooperate_info['appraise_avg_info_a'];
        $appraise_avg_info_customer = $cooperate_info['appraise_avg_info_b'];
        $broker_house_now = $cooperate_info['broker_a_now'];
        $broker_customer_now = $cooperate_info['broker_b_now'];
      }
      $data['house_broker_info'] = $house_broker_info;
      $data['customer_broker_info'] = $customer_broker_info;
      $data['trust_info_house'] = $trust_info_house;
      $data['trust_info_customer'] = $trust_info_customer;
      $data['appraise_avg_info_house'] = $appraise_avg_info_house;
      $data['appraise_avg_info_customer'] = $appraise_avg_info_customer;
      $data['broker_house_now'] = $broker_house_now;
      $data['broker_customer_now'] = $broker_customer_now;

      //操作日志
      $log_arr = $this->cooperate_model->get_cooperation_log_by_cid($c_id);
      $log_num = count($log_arr);
      $temp_log = array();

      for ($i = 0; $i < $log_num; $i++) {
        $step = $log_arr[$i]['step'];
        $esta = $log_arr[$i]['esta'];
        $temp_log[$step][$esta] = $log_arr[$i];
      }

      $log_arr = $temp_log;
    }

    $cooperate_info['log_record'] = $log_arr;

    //配置文件
    $cooperate_info['config'] = $this->cooperate_model->get_base_conf();

    //合作信息
    $data['cooperate_info'] = $cooperate_info;
    $cooperate_esta = intval($cooperate_info['esta']);
    $data['ct_id'] = $c_id;

    //获取出售信息基本配置资料
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();
    $data['title'] = '合作申请详情';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/myStyle.css'
      . ',mls/css/v1.0/guest_disk.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/cooperate_common.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js,'
      . 'common/js/jquery.form.js,common/js/jquery.validate.min.js');
    switch ($cooperate_esta) {
      case '1':
        $template_name = "my_accept_order"; //我收到的合作页面
        break;
      case '2':
        $template_name = "my_accept_order_fenyong"; //佣金分成页面
        break;
      case '3':
        $template_name = "my_accept_order_wait_confirmed";  //待确认佣金分成
        break;
      case '4':
        $template_name = "order_sub_result";    //合作生效页面
        break;
      case '5':
        $template_name = "my_accept_order_failure"; //合作失败页面
        break;
      case '6':
        $template_name = "my_accept_order_cancled";//取消合作页面
        break;
      case '7':
        $template_name = "my_accept_order_sucess";  //合作成功页面
        break;
      case '8':
      case '9':
      case '10':
      case '11':
        $template_name = "my_accept_order_failure"; //合作逾期页面
        break;
      default :
        $template_name = "my_accept_order";
    }
    $data['effect'] = 1;
    $data['is_admin'] = true;
    $this->view("cooperate/" . $template_name, $data);
  }


}

/* End of file cooperate.php */
/* Location: ./application/mls/controllers/cooperate.php */
