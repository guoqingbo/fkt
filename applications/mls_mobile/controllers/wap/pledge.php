<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 抵押贷款
 *
 */
class Pledge extends MY_Controller
{
  private $url = MLS_FINANCE_URL . '/pledge/';

  public function __construct()
  {
    parent::__construct('html');
  }

  private function curl($url, $fields = array())
  {
    $info = vpost($url, $fields);
    if ($info) $info = json_decode($info, true);
    return $info;
  }

  public function customer()
  {
    $data = array();

    $page = $this->input->get('page', true);
    $pagesize = $this->input->get('pagesize', true);

    $broker_id = $this->user_arr['broker_id'];
    $city_sepll = $this->user_arr['city_spell'];

    $data['list'] = array();
    if ($page && $pagesize) {
      $info = $this->curl($this->url . 'get_list?city_spell=' . $city_sepll . '&broker_id=' . $broker_id . '&page=' . $page . '&pagesize=' . $pagesize);
      if ('1' == $info['result']) $data['list'] = $info['data']['list'];
    }

    $this->load->view('wap/pledge/customer', $data);
  }

  public function ad()
  {
    $data = array();
    $data['tel400'] = $this->config->item('tel400');
    $this->load->view('wap/pledge/ad', $data);
  }

  public function detail($id)
  {
    $data = array();

    $city_sepll = $this->user_arr['city_spell'];
    $info = $this->curl($this->url . 'get_info?city_spell=' . $city_sepll . '&id=' . $id);
    if ('1' == $info['result']) $data['info'] = $info['data'];

    $this->load->view('wap/pledge/detail', $data);
  }

  public function apply()
  {
    $data = array();
    $broker_id = $this->user_arr['broker_id'];
    $city_sepll = $this->user_arr['city_spell'];

    $submit_flag = $this->input->post('submit_flag');
    if ('1' == $submit_flag) {
      $post_param['borrower'] = $this->input->post('borrower');
      $post_param['sex'] = $this->input->post('sex');
      $post_param['phone'] = $this->input->post('phone');
      $post_param['block_name'] = $this->input->post('block_name');
      $post_param['price'] = $this->input->post('price');
      $post_param['intentional_money'] = $this->input->post('intentional_money');
      $post_param['from'] = $this->input->post('from');
      /**
       *调用API录入数据
       **/
      echo vpost($this->url . 'apply?city_spell=' . $city_sepll . '&broker_id=' . $broker_id, $post_param);
    } else {
      $this->load->view('wap/pledge/apply', $data);
    }
  }

  //进度
  public function progress()
  {
    $data = array();
    $city_sepll = $this->user_arr['city_spell'];
    $id = $this->input->get_post('id');
    $data['block_name'] = $this->input->get_post('block_name');
    /**
     *调用API录入数据
     **/
    $progress_json = vpost($this->url . 'progress?city_spell=' . $city_sepll . '&id=' . $id, array());
    $progress_arr = json_decode($progress_json, TRUE);
    if ($progress_arr['result']) {
      $data['progress'] = $progress_arr['data'];
    }
    $this->load->view('wap/pledge/progress', $data);
  }
}

/* End of file broker.php */
/* Location: ./application/mls/controllers/broker.php */
