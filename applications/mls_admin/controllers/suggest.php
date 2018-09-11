<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台建议反馈
 *
 * @package    mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author     lujun
 * Date: 15-4-20
 */
class Suggest extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->helper('user_helper');
    $this->load->model('suggest_model');
  }

  /** 意见和建议首页 */
  public function index()
  {
    $city_py = $_SESSION[WEB_AUTH]["city"];
    $city_id = $this->suggest_model->get_city_id($city_py);
    //模板使用数据
    $data = array();
    $status = $this->input->post('status') ? $this->input->post('status') : 99;
    $pg = $this->input->post('pg');
    $data['status'] = $status;
    $data['title'] = "意见和建议";
    $data['conf_where'] = 'index';

    //分页开始
    $data['info_num'] = $this->suggest_model->get_count_by_cond($city_id, $status);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['info_num'] ? ceil($data['info_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['suggest'] = $this->suggest_model->get_all_suggest($city_id, $status, $data['offset'], $data['pagesize']);
    foreach ($data['suggest'] as $key => &$value) {
      if (strlen($value['feedback']) > 25) {
        $content = $this->left($value['feedback'], 25);
        $value['feedback'] = $content . ' &gt;&gt;&gt;';
      }
    }

    $this->load->view('suggest/index', $data);
  }

  /** 展示建议反馈详情 */
  public function show($id)
  {
    //模板使用数据
    $data = array();
    $data['conf_where'] = 'index';
    $where = "id = " . $id;
    $data['suggest'] = $this->suggest_model->get_info($where);
    if ($data['suggest']['status'] == 1) {
      $this->suggest_model->change_status($id, 2);
    }
    $data['title'] = "来自[" . $data['suggest']['telno'] . "]的意见反馈";
    $this->load->view('suggest/show', $data);
  }

  /** 更改状态 */
  public function change_status()
  {
    $status = $this->input->post('status');
    $id = $this->input->post('id');
    $adminfeedback = $this->input->post('adminfeedback');
    if ($status == 3) { //当为完成时，发送消息给提议者
      $where = "id = " . $id;
      $suggest = $this->suggest_model->get_info($where);
      if (strlen($suggest['feedback']) > 20) {
        $content = $this->left($suggest['feedback'], 20);
        $suggest['feedback'] = $content . ' &gt;&gt;&gt;';
      }
      $creattime = date('Y-m-d H:i:s', $suggest['dateline']);
      $broker_info = $this->suggest_model->get_broker_by_phone($suggest['telno']);
      $this->load->model('message_model');
      $this->message_model->suggest_message($broker_info['broker_id'], $broker_info['truename'],
        $suggest['feedback'], $adminfeedback, $creattime);
    }
    $result = $this->suggest_model->change_status($id, $status, $adminfeedback);
    $json_data['status'] = $result;
    echo json_encode($json_data);
  }

  /**
   * 字符串截取
   */
  public function left($str, $len, $charset = "utf-8")
  {
    //如果截取长度小于等于0，则返回空
    if (!is_numeric($len) or $len <= 0) {
      return "";
    }

    //如果截取长度大于总字符串长度，则直接返回当前字符串
    $sLen = strlen($str);
    if ($len >= $sLen) {
      return $str;
    }

    //判断使用什么编码，默认为utf-8
    if (strtolower($charset) == "utf-8") {
      $len_step = 3; //如果是utf-8编码，则中文字符长度为3
    } else {
      $len_step = 2; //如果是gb2312或big5编码，则中文字符长度为2
    }

    //执行截取操作
    $len_i = 0;
    //初始化计数当前已截取的字符串个数，此值为字符串的个数值（非字节数）
    $substr_len = 0; //初始化应该要截取的总字节数

    for ($i = 0; $i < $sLen; $i++) {
      if ($len_i >= $len) break; //总截取$len个字符串后，停止循环
      //判断，如果是中文字符串，则当前总字节数加上相应编码的中文字符长度
      if (ord(substr($str, $i, 1)) > 0xa0) {
        $i += $len_step - 1;
        $substr_len += $len_step;
      } else { //否则，为英文字符，加1个字节
        $substr_len++;
      }
      $len_i++;
    }
    $result_str = substr($str, 0, $substr_len);
    return $result_str;
  }
}
