<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市地图
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      angel_in_us
 */
class City_map extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'sh';


  public function __construct()
  {
    parent::__construct();
    $this->load->model('city_model');
  }

  /**
   * 导入百度地图
   *
   * @access public
   * @return void
   */
  public function index()
  {
    $data['user_menu'] = $this->user_menu;
    $user_arr = $this->user_arr;
    $city = $this->city_model->get_by_id($user_arr['city_id']);

    $data['city_name'] = $city['cityname'];
    $data['lng'] = $city['b_map_x'];
    $data['lat'] = $city['b_map_y'];
    //页面标题
    $data['page_title'] = $city['cityname'] . '地图';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/calculate.js');

    $this->view('city_map/map', $data);
  }
}
