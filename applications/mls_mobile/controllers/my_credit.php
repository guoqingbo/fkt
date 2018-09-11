<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-我的成长-我的积分记录
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_credit extends MY_Controller
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
    $this->load->model('credit_record_model');
  }

  //所有记录
  public function index()
  {
    $data = array();
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('broker_info_model');
    $data['credit_total'] = $this->broker_info_model->get_credit_by_broker_id($broker_id) ? $this->broker_info_model->get_credit_by_broker_id($broker_id) : '0';

    $where = 'broker_id = ' . $broker_id;

    //get参数  分页
    $get_param = $this->input->get(NULL, TRUE);
    $page = isset($get_param['page']) ? intval($get_param['page']) : 1;
    $pagesize = isset($get_param['pagesize']) ? intval($get_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $this->_total_count = $this->credit_record_model->count_by($where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //任务信息
    $credit_info = $this->credit_record_model->get_all_by($where, $this->_offset, $this->_limit, 'id', 'desc');
    //$data['credit_info'] = $credit_info?$credit_info:'0';
    //引入积分模型
    $this->load->model('api_broker_credit_model');
    $credit_way = $this->api_broker_credit_model->get_way();
    if (is_full_array($credit_info)) {
      foreach ($credit_info as $key => $value) {
        $credit[$key]['record_id'] = $value['id'];
        $credit[$key]['broker_id'] = $value['broker_id'];
        $credit[$key]['remark'] = $value['remark'];
        $credit[$key]['score'] = $value['score'];
        $credit[$key]['create_time'] = $value['create_time'];
        $credit[$key]['credit_way'] = $credit_way[$value['type']]['action'];
      }
      unset($credit_info);
    }
    $data['credit'] = $credit;
    $this->result("1", "查询个人所有积分成功", $data);
    return;
  }

  /**
   * 使用记录
   *
   */
  public function use_credit()
  {
    $data = array();
    $broker_id = $this->user_arr['broker_id'];

    $this->load->model('broker_info_model');
    $data['credit_total'] = $this->broker_info_model->get_credit_by_broker_id($broker_id) ? $this->broker_info_model->get_credit_by_broker_id($broker_id) : '0';

    $where = 'broker_id = ' . $broker_id . ' and score < 0';

    //get参数  分页
    $get_param = $this->input->get(NULL, TRUE);
    $page = isset($get_param['page']) ? intval($get_param['page']) : 1;
    $pagesize = isset($get_param['pagesize']) ? intval($get_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $this->_total_count = $this->credit_record_model->count_by($where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    //任务信息
    $credit_info = $this->credit_record_model->get_all_by($where, $this->_offset, $this->_limit, 'id', 'desc');
    //$data['credit_info'] = $credit_info;
    //echo '<pre>';print_r($data['credit_info']);die;
    //引入积分模型
    $this->load->model('api_broker_credit_model');
    $credit_way = $this->api_broker_credit_model->get_way();
    if (is_full_array($credit_info)) {
      foreach ($credit_info as $key => $value) {
        $credit[$key]['record_id'] = $value['id'];
        $credit[$key]['broker_id'] = $value['broker_id'];
        $credit[$key]['remark'] = $value['remark'];
        $credit[$key]['score'] = $value['score'];
        $credit[$key]['create_time'] = $value['create_time'];
        $credit[$key]['credit_way'] = $credit_way[$value['type']]['action'];
      }
      unset($credit_info);
    }
    $data['credit'] = $credit;

    $this->result("1", "查询个人使用积分成功", $data);
    return;
  }

  /**
   * 获得记录
   *
   */
  public function obtain_credit()
  {
    $data = array();
    $broker_id = $this->user_arr['broker_id'];

    $this->load->model('broker_info_model');
    $data['credit_total'] = $this->broker_info_model->get_credit_by_broker_id($broker_id) ? $this->broker_info_model->get_credit_by_broker_id($broker_id) : '0';

    $where = 'broker_id = ' . $broker_id . ' and score > 0';

    //get参数  分页
    $get_param = $this->input->get(NULL, TRUE);
    $page = isset($get_param['page']) ? intval($get_param['page']) : 1;
    $pagesize = isset($get_param['pagesize']) ? intval($get_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    $this->_total_count = $this->credit_record_model->count_by($where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;


    //任务信息
    $credit_info = $this->credit_record_model->get_all_by($where, $this->_offset, $this->_limit, 'id', 'desc');
    //$data['credit_info'] = $credit_info;
    //echo '<pre>';print_r($data['credit_info']);die;
    //引入积分模型
    $this->load->model('api_broker_credit_model');
    $credit_way = $this->api_broker_credit_model->get_way();
    if (is_full_array($credit_info)) {
      foreach ($credit_info as $key => $value) {
        $credit[$key]['record_id'] = $value['id'];
        $credit[$key]['broker_id'] = $value['broker_id'];
        $credit[$key]['remark'] = $value['remark'];
        $credit[$key]['score'] = $value['score'];
        $credit[$key]['create_time'] = $value['create_time'];
        $credit[$key]['credit_way'] = $credit_way[$value['type']]['action'];
      }
      unset($credit_info);
    }
    $data['credit'] = $credit;
    $this->result("1", "查询个人获取积分成功", $data);
    return;
  }

  //何如获取积分
  public function protocol()
  {
    //结果状态
    $protocol_html = file_get_contents(dirname(__FILE__) . '/../views/my_credit/credit.php');
    $protocol_html_arr = array('protocol' => $protocol_html);
    $this->result(1, '查询成功', $protocol_html_arr);
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

/* End of file my_growing_credit.php */
/* Location: ./application/mls/controllers/my_growing_credit.php */
