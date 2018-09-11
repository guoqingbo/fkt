<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 添加分门店
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Project_cooperate_lol_success_applay extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('project_cooperate_lol_model');
    $this->load->model('cooperate_model');
    $this->load->model('message_model');
    $this->load->model('broker_info_base_model');
    $this->load->helper('page_helper');
  }

  //门店管理页
  public function index()
  {
    $data['title'] = '英雄联盟-称霸江湖';
    $data['conf_where'] = 'index';
    //筛选条件
    $data['where_cond'] = '';
    $order_sn = $this->input->post('order_sn', TRUE);
    if ($order_sn) {
      $data['where_cond'] .= "order_sn like '%" . $order_sn . "%'";
    }
    //分页开始
    $data['num'] = $this->project_cooperate_lol_model->get_cooperate_success_applay_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['num'] ? ceil($data['num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $list = $this->project_cooperate_lol_model->get_cooperate_success_applay_list($data['where_cond'], $data['offset'], $data['pagesize']);
    foreach ($list as $key => $vo) {
      $success_list_one = $this->project_cooperate_lol_model->get_cooperate_success_id($vo['s_id']);
      $success_list_arr = $this->project_cooperate_lol_model->get_cooperate_success_by_cid($vo['c_id'], $success_list_one['city_id']);
      $list[$key]['success_list_arr'] = $success_list_arr;
    }
    $data['list'] = $list;
    $data['param_array'] = array(
      'order_sn' => $order_sn
    );
    //print_r($data['list']);
    $this->load->view('project/cooperate/lol/success_applay_list', $data);
  }

  public function modify($id, $review = '')
  {
    $data['title'] = '英雄联盟-称霸江湖';
    $data['conf_where'] = 'index';
    $data['review'] = $review;
    $data['modifyResult'] = '';
    $submit_flag = $this->input->post('submit_flag');
    $data['success_applay'] = $this->project_cooperate_lol_model->get_cooperate_success_applay_by_id($id);
    //print_r($data['success_applay']);
    $c_id = $data['success_applay']['c_id'];
    $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($c_id);
    $success_list_one = $this->project_cooperate_lol_model->get_cooperate_success_id($data['success_applay']['s_id']);

    $success_list_arr = $this->project_cooperate_lol_model->get_cooperate_success_by_cid($c_id, $success_list_one['city_id']);
    if (is_full_array($success_list_arr)) {
      foreach ($success_list_arr as $vo) {
        if ($vo['broker_id'] == $vo['operate_broker_id']) {
          $data['success_applay']['broker_a'] = array(
            'broker_name' => $vo['broker_name'], 'agency_name' => $vo['agency_name'],
            'company_name' => $vo['company_name'], 'agency_type' => $vo['agency_type'],
            'phone' => $vo['phone'], 'broker_id' => $vo['broker_id']
          );
        } else {
          $data['success_applay']['broker_b'] = array(
            'broker_name' => $vo['broker_name'], 'agency_name' => $vo['agency_name'],
            'company_name' => $vo['company_name'], 'agency_type' => $vo['agency_type'],
            'phone' => $vo['phone'], 'broker_id' => $vo['broker_id']
          );
        }
      }
    }
    if ($submit_flag == 'modify') {
      if ($data['success_applay']['status'] != 0) {
        echo '此记录已经审核过';
      } else {
        $status = $this->input->post('status');
        if ($status == 1) {
          $this->load->library('Sms_codi', array('city' => 'hz', 'jid' => '2', 'template' => 'cooperate_lol_pass'), 'sms');
          $return_a = $this->sms->send($data['success_applay']['broker_a']['phone'], array('order_sn' => $data['success_applay']['order_sn']));
          $result_a['status'] = $return_a['success'] ? 1 : 0;
          $result_a['msg'] = $return_a['success'] ? '短信发送成功' : $return_a['errorMessage'];
          $return_b = $this->sms->send($data['success_applay']['broker_b']['phone'], array('order_sn' => $data['success_applay']['order_sn']));
          $result_b['status'] = $return_b['success'] ? 1 : 0;
          $result_b['msg'] = $return_b['success'] ? '短信发送成功' : $return_b['errorMessage'];

          $broker_info_a = $this->broker_info_base_model->get_by_broker_id($data['success_applay']['broker_a']['broker_id']);
          $broker_info_b = $this->broker_info_base_model->get_by_broker_id($data['success_applay']['broker_b']['broker_id']);
          if ($broker_info_a['company_id'] == $broker_info_b['company_id']) {
            if ($cooperate_info['brokerid_a'] == $data['success_applay']['broker_a']['broker_id']) {
              $url = '/cooperate/accept_order_list/';
            } else {
              $url = '/cooperate/send_order_list/';
            }
            $this->message_model->add_message('1-49-3', $data['success_applay']['broker_a']['broker_id'], $data['success_applay']['broker_a']['broker_name'], $url, array('order_sn' => $data['success_applay']['order_sn']));
            $this->message_model->add_message('1-49-4', $data['success_applay']['broker_b']['broker_id'], $data['success_applay']['broker_b']['broker_name'], $url, array('order_sn' => $data['success_applay']['order_sn']));
          } else {
            $this->message_model->add_message('1-49-1', $data['success_applay']['broker_a']['broker_id'], $data['success_applay']['broker_a']['broker_name'], '/gift_exchange/index', array('order_sn' => $data['success_applay']['order_sn']));
            $this->message_model->add_message('1-49-2', $data['success_applay']['broker_b']['broker_id'], $data['success_applay']['broker_b']['broker_name'], '/gift_exchange/index', array('order_sn' => $data['success_applay']['order_sn']));
          }
        } else if ($status == 2) {
          $this->load->library('Sms_codi', array('city' => 'hz', 'jid' => '2', 'template' => 'cooperate_lol_fail'), 'sms');
          $return_a = $this->sms->send($data['success_applay']['broker_a']['phone'], array('order_sn' => $data['success_applay']['order_sn']));
          $result_a['status'] = $return_a['success'] ? 1 : 0;
          $result_a['msg'] = $return_a['success'] ? '短信发送成功' : $return_a['errorMessage'];
          $return_b = $this->sms->send($data['success_applay']['broker_b']['phone'], array('order_sn' => $data['success_applay']['order_sn']));
          $result_b['status'] = $return_b['success'] ? 1 : 0;
          $result_b['msg'] = $return_b['success'] ? '短信发送成功' : $return_b['errorMessage'];

          if ($cooperate_info['brokerid_a'] == $data['success_applay']['broker_a']['broker_id']) {
            $url = '/cooperate/accept_order_list/';
          } else {
            $url = '/cooperate/send_order_list/';
          }
          $this->message_model->add_message('1-50', $data['success_applay']['broker_a']['broker_id'], $data['success_applay']['broker_a']['broker_name'], $url, array('order_sn' => $data['success_applay']['order_sn']));
        }
        $this->project_cooperate_lol_model->update_cooperate_success_applay_status($id, $data['success_applay']['c_id'], $status);
        $this->load->model('api_broker_credit_model');
        $this->api_broker_credit_model->set_broker_param('', 1);
        $this->api_broker_credit_model->cooperate_confirm_deal($cooperate_info);
        echo '修改成功';
      }
      return false;
    }
    $data['success_applay']['id'] = $id;
    $this->load->view('project/cooperate/lol/success_applay_modify', $data);
  }

  function download_pics()
  {
    $urls = $this->input->post("pic_urls", TRUE);
    $order_sn = $this->input->post("order_sn", TRUE);
    $url = $this->input->post("url", TRUE);
    $file = "/fang100_lol_success_applay/" . date("Ymd") . '_' . $order_sn . '/';
    if (@opendir($file) == NULL) {
      @mkdir($file, 0777, true);
    }
    $url_arr = explode(",", $urls);
    //print_r($url_arr);exit;
    if (is_full_array($url_arr) && !empty($url_arr)) {
      foreach ($url_arr as $key => $vo) {
        if (stristr($vo, 'error')) {
          continue;
        }
        $curl = curl_init($vo);
        $filename = $file . date("Ymdhis") . $key . ".jpg";
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $imageData = curl_exec($curl);
        $info = curl_getinfo($curl);
        //print_r($info);exit;
        curl_close($curl);
        //@mkdir("D:\\", 0777, true);
        if ($info['http_code'] != 200) {
          $imageData = NULL;
        }
        $tp = @fopen($filename, "a");
        fwrite($tp, $imageData);
        fclose($tp);
      }
    }
    echo "下载完毕！";
  }

}

/* End of file Project_cooperate_lol_effect.php */
/* Location: ./application/mls_admin/controllers/Project_cooperate_lol_effect.php */
