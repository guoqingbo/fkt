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
  private $_limit = 10;

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

  public function index()
  {
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $broker_id = $this->user_arr['broker_id'];

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

    //获取经纪人的基本信息
    $data['broker_info'] = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    //获取经纪人的信用值和等级
    $data['trust_level'] = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
    //获取好评率/总数/好评/中评/差评
    $data['count_info'] = $this->api_broker_sincere_model->get_trust_appraise_count($broker_id);

    //平均好评率
    /*$this->load->model('sincere_trust_count_model');
    $good_avg_rate_info = $this->sincere_trust_count_model->good_avg_rate();
    $data['good_avg_rate'] = round($good_avg_rate_info['good_rate'],2);*/

    //好评率比平均值高
    $good_avg_rate = $this->api_broker_sincere_model->good_avg_rate($broker_id);
    $data['diff_good_rate'] = $good_avg_rate['good_rate_avg_high'];

    //获取经纪人动态评分基本统计信息
    $data['appraise_avg_info'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);
    //经纪人动态评分详细信息(得星星)
    $appraise_info = $this->api_broker_sincere_model->get_appraise_info($broker_id);

    $appraise_info['infomation_score_html'] = $this->api_broker_sincere_model->get_appraise_level($data['appraise_avg_info']['infomation']['score']);
    $appraise_info['attitude_score_html'] = $this->api_broker_sincere_model->get_appraise_level($data['appraise_avg_info']['attitude']['score']);
    $appraise_info['business_score_html'] = $this->api_broker_sincere_model->get_appraise_level($data['appraise_avg_info']['business']['score']);

    for ($i = 1; $i <= 5; $i++) {
      //信息真实度
      if ($appraise_info['infomation_sum'] != 0) {
        $appraise_info['infomation'][$i]['info_percent'] = round($appraise_info['infomation'][$i]['count'] / $appraise_info['infomation_sum'] * 100);
      } else {
        $appraise_info['infomation'][$i]['info_percent'] = 0;
      }
      //态度满意度
      if ($appraise_info['attitude_sum'] != 0) {
        $appraise_info['attitude'][$i]['atti_percent'] = round($appraise_info['attitude'][$i]['count'] / $appraise_info['attitude_sum'] * 100);
      } else {
        $appraise_info['attitude'][$i]['atti_percent'] = 0;
      }
      //业务专业度
      if ($appraise_info['business_sum'] != 0) {
        $appraise_info['business'][$i]['busi_percent'] = round($appraise_info['business'][$i]['count'] / $appraise_info['business_sum'] * 100);
      } else {
        $appraise_info['business'][$i]['busi_percent'] = 0;
      }
    }
    $data['appraise_info'] = $appraise_info;

    $pg = $this->input->post('page');
    $type = $this->input->get('type');
    if (empty($type)) {
      $type = 0;
    }
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
      //echo($house_config['fitment'][2]);exit;
      //print_r($house_config);exit;

      $config_info = $this->sincere_trust_config_model->get_config();
      foreach ($cooperate_info as $key => $value) {

        $house_info = unserialize($value['house_info']);

        $cooperate_info[$key]['house_info_add']['fitment_name'] = $house_config['fitment'][$house_info['fitment']];
        $cooperate_info[$key]['house_info_add']['forward_name'] = $house_config['forward'][$house_info['forward']];
        if ($house_info['tbl'] == 'sell') {
          $cooperate_info[$key]['house_info_add']['price_danwei'] = '万元';
        } elseif ($house_info['tbl'] == 'rent') {
          $cooperate_info[$key]['house_info_add']['price_danwei'] = '元/月';
        }

        $cooperate_info[$key]['trust_name'] = $config_info['appraise_type_description'][$value['trust_type_id']];
        //通过分数获取星星
        $cooperate_info[$key]['info_star'] = $this->api_broker_sincere_model->get_appraise_level($value['infomation']);//信息真实度
        $cooperate_info[$key]['atti_star'] = $this->api_broker_sincere_model->get_appraise_level($value['attitude']);//合作满意度
        $cooperate_info[$key]['busi_star'] = $this->api_broker_sincere_model->get_appraise_level($value['business']);//业务专业度
        if ($type) {//我给合作方的评价
          $broker_id = $value['partner_id'];
        } else {
          $broker_id = $value['broker_id'];
        }
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
        $cooperate_info[$key]['truename'] = $brokerinfo['truename'];
        //获取合作方的信用值和等级
        $cooperate_info[$key]['broker_level'] = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
      }
    }
    //print_r($cooperate_info);exit;
      $data['type'] = $type;
    $data['cooperate_info'] = $cooperate_info;

    //页面标题
    $data['page_title'] = '我的评价';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/uploadimg.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/disk.js,mls/js/v1.0/tipe_num.js');
    //var_dump($data);exit;
    $this->view('uncenter/my_evaluate/my_evaluate', $data);
  }

  //申诉页面加载
  public function shensu($id, $transaction_id)
  {
    $data = array();
    $data['id'] = $id;
    $data['transaction_id'] = $transaction_id;
    /*$this->load->model('pic_model');
    $data['picinfo'] = $this->pic_model->find_house_pic_by('r_house',$house_id);*/

    //页面标题
    $data['page_title'] = '我要申诉';

    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadimg.js,'
      . 'mls/js/v1.0/cooperate_common.js'
    );
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification.js,mls/js/v1.0/backspace.js');

    //加载发布页面模板
    $this->view('uncenter/my_evaluate/evaluate_shensu', $data);

  }

  /*
   * 提交申诉
   */
  public function modify()
  {
    $broker_id = $this->user_arr['broker_id'];
    $appraise_id = $this->input->post('id');
    $transaction_id = $this->input->post('transaction_id');
    $reason = $this->input->post('content');
    if (empty($reason) or strlen($reason) < 20) {
      echo '{"status":"failed","msg":"申诉说明必填，至少20字"}';
      exit;
    }

    $photo_url = $this->input->post('photo_url');
    $photo_name = '';
    //照片非必填项 不限制
    /*if(empty($photo_url) or empty($photo_name)){
        echo '{"status":"failed","msg":"请上传图片"}';exit;
    }*/

    $this->api_broker_sincere_model->appraise_appeal($broker_id, $appraise_id, $transaction_id, $photo_url, $photo_name, $reason);
    $rows = $this->sincere_appraise_cooperate_model->update_by_id(array('status' => 1), $appraise_id);
    if ($rows >= 0) {
      echo '{"status":"success","msg":"申诉成功，请等待审核"}';
    } else {
      echo '{"status":"failed","msg":"申诉不成功，请重新申诉"}';
    }

  }

  /*
   * 上传图片
   */
  public function upload_photo()
  {
    $filename = $this->input->post('action');

    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
    //echo "<script>alert('".$fileurl."')</script>";exit;
    echo "<script>window.parent.changePic('" . $fileurl . "')</script>";
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
