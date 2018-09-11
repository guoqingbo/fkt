<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-个人考勤
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_attendance extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'sh';


  /**
   * 录入经纪人id
   *
   * @access private
   * @var int
   */
  private $_boker_id = 0;

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
    $this->load->model('attendance_model');
  }

  public function index()
  {
    //模板使用数据
    $data = array();

    $broker_id = $this->user_arr['broker_id'];
    $broker_name = $this->user_arr['truename'];
    $data['broker_name'] = $broker_name;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    //查询房源条件
    $cond_where = "broker_id = {$broker_id} ";

    $date = $post_param['date'] ? trim($post_param['date']) : date("Y-m", time());
    $data['date'] = $date;

    $cond_where .= "AND datetime1 like '{$date}-%' ";

    $date_str = date("Y年m月", strtotime($date));
    $data['date_str'] = $date_str;

    //给定月份所应有的天数
    $date_t = date("t", strtotime($date));
    $data['date_t'] = $date_t;

    $date_array = array();
    for ($i = 1; $i <= $date_t; $i++) {
      $date_array[$i] = array();
      $date1 = $date . "-" . sprintf("%02d", $i);
      //日期
      $date_array[$i]['date'] = $date1;
      //星期中的第几天
      $date_array[$i]['week'] = date("w", strtotime($date1));
      //考勤
      $date_array[$i]['list'] = array();
    }

    $list = $this->attendance_model->get_all_by($cond_where, -1, 0, "datetime1", "ASC");
    foreach ($list as $key => $val) {
      $date_j = date("j", strtotime($val['datetime1']));
      $date_array[$date_j]['list'][] = $val;
    }

    $data['date_array'] = $date_array;

    $config = $this->attendance_model->get_config();
    $data['config'] = $config;

    //页面标题
    $data['page_title'] = '个人考勤';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic.js');

    $this->view('uncenter/my_attendance/my_attendance', $data);
  }
}
