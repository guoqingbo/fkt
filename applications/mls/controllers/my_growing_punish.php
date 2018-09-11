<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-我的成长-我的处罚记录
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_growing_punish extends MY_Controller
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
    $this->load->model('sincere_punish_model');
  }

  public function index()
  {
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    //获取平台经济人总数
    $this->load->model('broker_info_model');
    $broker_total = $this->broker_info_model->count_by();

    $broker_id = $this->user_arr['broker_id'];
    $pg = $this->input->post('page');

    $where = 'brokered_id = ' . $broker_id;
    //获取每种类型的处罚总数
    $data['total1'] = $this->sincere_punish_model->count_by($where . ' and type = 1');
    $data['total2'] = $this->sincere_punish_model->count_by($where . ' and type = 2');
    $data['total3'] = $this->sincere_punish_model->count_by($where . ' and type = 3');
    $data['total4'] = $this->sincere_punish_model->count_by($where . ' and (type = 4 or type = 5)');
    $data['total5'] = $this->sincere_punish_model->count_by($where . ' and type = 6');
    $data['total6'] = $this->sincere_punish_model->count_by($where . ' and type = 7');
    //$data['total7'] = $this->sincere_punish_model->count_by($where.' and type = 8');

    //页面标题
    $data['page_title'] = '我的成长-我的处罚记录';

    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->sincere_punish_model->count_by($where);
    $data['total_count'] = $this->_total_count;
    //获取每个经济人的处罚平均数(保留小数点后两位，四舍五入)
    if ($broker_total) {
      $data['avg'] = round($data['total_count'] / $broker_total, 2);
      //$data['avg'] = sprintf("%.2f", $data['total_count']/$broker_total);
    }
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
    //处罚信息
    $punish_info = $this->sincere_punish_model->get_all_by($where, $this->_offset, $this->_limit, 'id', 'desc');
    //房源配置信息
    $this->load->model('house_config_model');
    $house_config = $this->house_config_model->get_config();
    $this->load->model('buy_customer_model');
    //获取配置文件
    $customer_config = $this->buy_customer_model->get_base_conf();
    if (is_full_array($punish_info)) {
      //获取处罚类型名称
      foreach ($punish_info as $key => $value) {
        $house_info = unserialize($punish_info[$key]['house_info']);
        $punish_info[$key]['type_name'] = $this->sincere_punish_model->get_func_by_type($value['type']);
        if ($house_info['tbl'] == 'sell' || $house_info['tbl'] == 'rent') {
          $house_info['forward_str'] = $house_config['forward'][$house_info['forward']];
          $house_info['fitment_str'] = $house_config['fitment'][$house_info['fitment']];
        } else {
          $house_info['forward_str'] = '';
          $house_info['fitment_str'] = '';
          if ($house_info['forward']) {
            $house_info['forward_str'] = $customer_config['forward'][$house_info['forward']];
          }
          if ($house_info['fitment']) {
            $house_info['fitment_str'] = $customer_config['fitment'][$house_info['fitment']];
          }
        }
        if (!isset($house_info['hall'])) {
          $house_info['hall'] = '';
        }
        if (!isset($house_info['toilet'])) {
          $house_info['toilet'] = '';
        }
        $punish_info[$key]['house_info'] = $house_info;
      }
    }
    //var_dump($punish_info);exit;
    $data['punish_info'] = $punish_info;

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/personal_center.js');
    $this->view('uncenter/my_growing/my_growing_punish', $data);
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

/* End of file my_growing_punish.php */
/* Location: ./application/mls/controllers/my_growing_punish.php */
