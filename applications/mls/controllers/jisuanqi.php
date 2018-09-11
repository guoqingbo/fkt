<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-个人记事本
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Jisuanqi extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'sh';

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

  public function __construct()
  {
    parent::__construct();

  }

  /**
   * 税费计算
   *
   * @access public
   * @return void
   */
  public function shuifei()
  {
    $data['user_menu'] = $this->user_menu;
    //页面标题
    $data['page_title'] = '税费计算';

    $data['city'] = $this->user_arr['city_spell'];

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/calculate.js');
    $this->view('jisuanqi/shuifei', $data);
  }

  public function daikuan()
  {
    $data['user_menu'] = $this->user_menu;
    $data['city_id'] = $this->user_arr['city_id'];
    //页面标题
    $data['page_title'] = '贷款计算';
    //贷款类库
    $this->load->library('Daikuan');
    $Daikuan = new Daikuan();
    $daikuan = $Daikuan->get_daikuan();
    //echo "<pre>";
    //print_r($daikuan['loan_option']);die;
    $data['daikuan'] = $daikuan;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/calculate.js');
    $this->view('jisuanqi/daikuan', $data);
  }

  public function ceshi()
  {
    echo '1111';
  }

}
