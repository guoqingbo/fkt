<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notice_access extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->model('notice_access_model', 'na');
  }

  public function index()
  {
    $this->result(1, 'Hello Notice Access.');
  }

  //提交消息数据
  public function post()
  {
    //响应结果
    $post_state = 0;
    $post_result = 'Notice post failure.';

    //获取参数
    $department_id = $this->input->get('na_id', TRUE);
    $access_key = $this->input->get('na_key', TRUE);

    //判断合法
    $check_key = $this->na->get_access_key($department_id);
    if ('' != $access_key && $access_key == $check_key) {
      $ntype_arr = $this->na->get_notice_type();
      $natype = $this->input->get('na_type', TRUE);

      if (!isset($ntype_arr[$natype])) {
        $post_result = 'The post type is error.';
      } else {
        //信息处理入库
        $message = array();
        $message['department_id'] = $department_id;
        $message['data'] = $this->input->get('na_data', TRUE);
        $message['type'] = $natype;
        $message['dateline'] = time();

        $result = $this->na->add_message($message);

        if ($result > 0) {
          $post_state = 1;
          $post_result = 'Notice post success.';
        }
      }
    }

    $this->result($post_state, $post_result);
  }
}

/* End of file notice_access.php */
/* Location: ./application/controllers/notice_access.php */
