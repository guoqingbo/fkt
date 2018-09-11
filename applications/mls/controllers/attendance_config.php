<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class Attendance_config extends MY_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    //加载考勤模型类
    $this->load->model('attendance_config_model');
  }

  public function index()
  {
    $company_id = $this->user_arr['company_id'];

    $post_param = $this->input->post(NULL, TRUE);
    $submit_flag = $post_param['submit_flag'];
    if ($submit_flag == 1) {
      //信息数组
      $id = intval($post_param['id']);
      $datainfo = array(
        'is_am_pm' => intval($post_param['is_am_pm']),
        'start_time' => $post_param['start_time'],
        'end_time' => $post_param['end_time'],
        'is_first' => intval($post_param['is_first']),
        'protect_time' => intval($post_param['protect_time']),
        'refresh_time' => intval($post_param['refresh_time']),
        'is_remind' => intval($post_param['is_remind'])
      );
      if ($id) {
        //修改
        $rs = $this->attendance_config_model->update_by_id($datainfo, $id);
      } else {
        $datainfo['company_id'] = $company_id;
        //添加
        $rs = $this->attendance_config_model->add_info($datainfo);
      }
      if ($rs) {
        $this->jump(MLS_URL . '/attendance_config', '保存设置成功');
        exit;
      } else {
        $this->jump(MLS_URL . '/attendance_config', '保存设置失败');
        exit;
      }
    }
    $data = array();


    $info = $this->attendance_config_model->get_one_by("company_id = {$company_id}");
    $data["info"] = $info;

    //页面标题
    $data['page_title'] = '考勤设置';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/attendance_config_manage.css,'
      . 'mls/css/v1.0/house_manage.css,' . 'mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,' . 'mls/js/v1.0/jquery.validate.min.js');
    $this->view("attendance/config", $data);
  }


}

/* End of file attendance_config.php */
/* Location: ./application/mls/controllers/attendance_config.php */
