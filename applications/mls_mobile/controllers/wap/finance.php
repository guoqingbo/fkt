<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 租房分期
 *
 */
class Finance extends MY_Controller
{

  private $url = MLS_FINANCE_URL;

  public function __construct()
  {
    parent::__construct('html');
  }

  public function index()
  {
    $city_spell = !empty($this->user_arr['city_spell']) ? $this->user_arr['city_spell'] : 'cd';
    if (in_array($city_spell, array('cd', 'sz', 'km'))) {
      //新金融
      $data['city_spell'] = $city_spell;
      $data['banner'] = 'list_banner.jpg';
      if ($data['city_spell'] == 'cd') {
        $data['banner'] = 'list_banner_cd.png';
      }
      if ($data['city_spell'] == 'km') {
        $data['banner'] = 'list_banner_km.png';
      }
      if ($data['city_spell'] == 'sz') {
        $data['banner'] = 'list_banner_sz.png';
      }
      $this->load->view('wap/newfinance', $data);
    } else {
      $data = array();
      $city_id = $this->user_arr['city_id'];
      $this->load->model('city_model');
      $city = $this->city_model->get_by_id($city_id);
      $data['mortgage'] = $city['mortgage'];
      $data['pledge'] = $city['pledge'];
      $data['rental'] = $city['rental'];
      $this->load->view('wap/finance', $data);
    }
  }

  public function business_info()
  {
    $city_spell = !empty($this->user_arr['city_spell']) ? $this->user_arr['city_spell'] : 'cd';
    $type = $this->input->get('type');

    $data = array();
    switch ($type) {
      case '1':
        $tpl = 'cd_mmb';
        break;
      case '2':
        $tpl = 'cd_txb';
        break;
      case '3':
        $tpl = 'cd_dyb';
        break;
      case '4':
        $tpl = 'cd_mfb';
        break;
      case '5':
        $tpl = 'cd_ajb';
        break;
      case '6':
        $tpl = 'cd_sld';
        break;
      default:
        $tpl = 'cd_dyb';
        break;
    }
    $data['broker_id'] = $this->user_arr['broker_id'];
    $data['tel400'] = $this->config->item('tel400');
    $data['type'] = $type;
    $this->load->view('wap/info/' . $tpl, $data);
  }

  public function loan_info()
  {
    $city_spell = !empty($this->user_arr['city_spell']) ? $this->user_arr['city_spell'] : 'cd';
    $type = $this->input->get('type');

    $data = array();
    if ($city_spell == 'sz') {
      switch ($type) {
        case '1':
          $tpl = 'sz_dyd';
          break;
        case '2':
          $tpl = 'sz_sld';
          break;
        case '3':
          $tpl = 'sz_ajb';
          break;
        default:
          $tpl = 'sz_dyd';
          break;
      }
      $data['tel400'] = $this->config->item('tel400');
    }
    if ($city_spell == 'km') {
      switch ($type) {
        case '1':
          $tpl = 'km_dyd';
          break;
        case '2':
          $tpl = 'km_sld';
          break;
        default:
          $tpl = 'km_dyd';
          break;
      }
      $data['tel400'] = $this->config->item('tel400');
    }
    $data['broker_id'] = $this->user_arr['broker_id'];
    $data['type'] = $type;
    $this->load->view('wap/info/' . $tpl, $data);
  }

  private function curl($url, $fields = array())
  {
    $data = http_build_query($fields);
    $opts = array(
      'http' => array(
        'method' => "POST",
        'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
          "Content-length:" . strlen($data) . "\r\n" .
          "Cookie: \r\n" .
          "\r\n",
        'content' => $data,
      )
    );
    $cxContext = stream_context_create($opts);
    $info = file_get_contents($url, true, $cxContext);
    if ($info) $info = json_decode($info, true);
    return $info;
  }

  public function apply_business()
  {
    $city_spell = !empty($this->user_arr['city_spell']) ? $this->user_arr['city_spell'] : 'cd';
    $submit_flg = $this->input->get('submit_flg');
    if ($submit_flg == '1') {
      $type = $this->input->get('type');
      $borrower = $this->input->get('borrower');
      $phone = $this->input->get('phone');
      $broker_id = $this->user_arr['broker_id'];
      $broker_phone = $this->user_arr['phone'];
      $expander = $this->user_arr['truename'];

      $post_param = array('type' => $type, 'broker_id' => $broker_id, 'broker_phone' => $broker_phone, 'expander' => $expander, 'phone' => $phone, 'borrower' => $borrower, 'from' => 4);
      $result = $this->curl($this->url . 'business/apply?city_spell=' . $city_spell, $post_param);
      if ('1' != $result['result']) die(json_encode(array('status' => 'error')));
      die(json_encode(array('status' => 'success')));
    }
    $data = array();
    $this->load->model('city_model');
    $city = $this->city_model->get_city_by_spell($city_spell);
    $data['cityname'] = isset($city['cityname']) ? $city['cityname'] : '';
    $data['type'] = $this->input->get('type');
    switch ($data['type']) {
      case '1':
        $data['logo'] = 'cd/cd_jr_mmb.png';
        break;
      case '2':
        $data['logo'] = 'cd/cd_jr_txb.png';
        break;
      case '3':
        $data['logo'] = 'cd/cd_jr_dyb.png';
        break;
      case '4':
        $data['logo'] = 'cd/cd_jr_mfb.png';
        break;
      case '5':
        $data['logo'] = 'cd/cd_jr_ajb.png';
        break;
      case '6':
        $data['logo'] = 'cd/cd_jr_sld.png';
        break;
      default:
        $data['logo'] = 'cd/cd_jr_dyb.png';
        break;
    }
    $data['tel400'] = $this->config->item('tel400');
    $this->load->view('wap/apply_business', $data);
  }

  public function apply_loan()
  {
    $city_spell = $this->user_arr['city_spell'];
    $submit_flg = $this->input->get('submit_flg');
    if ($submit_flg == '1') {
      $type = $this->input->get('type');
      $borrower = $this->input->get('borrower');
      $phone = $this->input->get('phone');
      $broker_id = $this->user_arr['broker_id'];
      $broker_phone = $this->user_arr['phone'];
      $expander = $this->user_arr['truename'];

      $post_param = array('type' => $type, 'broker_id' => $broker_id, 'broker_phone' => $broker_phone, 'expander' => $expander, 'phone' => $phone, 'borrower' => $borrower, 'from' => 4);
      $result = $this->curl($this->url . 'loan/apply?city_spell=' . $city_spell, $post_param);
      if ('1' != $result['result']) die(json_encode(array('status' => 'error')));
      die(json_encode(array('status' => 'success')));
    }
    $data = array();
    $this->load->model('city_model');
    $city = $this->city_model->get_city_by_spell($city_spell);
    $data['cityname'] = isset($city['cityname']) ? $city['cityname'] : '';
    $data['type'] = $this->input->get('type');
    if ($city_spell == 'sz') {
      switch ($data['type']) {
        case '1':
          $data['logo'] = 'sz/sz_jr_dyd.png';
          break;
        case '2':
          $data['logo'] = 'sz/sz_jr_sld.png';
          break;
        case '3':
          $data['logo'] = 'sz/sz_jr_ajb.png';
          break;
        default:
          $data['logo'] = 'sz/sz_jr_dyd.png';
          break;
      }
      $data['tel400'] = $this->config->item('tel400');
    }
    if ($city_spell == 'km') {
      switch ($data['type']) {
        case '1':
          $data['logo'] = 'km/km_jr_dyd.png';
          break;
        case '2':
          $data['logo'] = 'km/km_jr_sld.png';
          break;
        default:
          $data['logo'] = 'km/km_jr_dyd.png';
          break;
      }
      $data['tel400'] = $this->config->item('tel400');
    }
    $this->load->view('wap/apply_loan', $data);
  }

  public function page()
  {
    $data = array();
    $city_id = $this->user_arr['city_id'];
    $this->load->model('city_model');
    $city = $this->city_model->get_by_id($city_id);
    $data['mortgage'] = $city['mortgage'];
    $data['pledge'] = $city['pledge'];
    $data['rental'] = $city['rental'];
    $this->load->view('wap/list', $data);
  }

  /**
   * 其它图片上传
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function upload()
  {
    $data = array();
    $this->load->model('pic_model');
    $input_name = 'pic';
    $this->pic_model->set_filename($input_name);
    $house_image_url = $this->pic_model->upload("common");
    $data['other_image_url'] = $house_image_url;
    if ($house_image_url) {
      $this->result('1', '返回其它图片地址', $data);
    } else {
      $this->result('0', '上传失败');
    }
  }

  //ajax验证用户名和验证码
  public function ajaxValid()
  {
    $data = array();
    $phone = $this->input->post('phone');
    $this->load->model('broker_model');

    $result = $this->broker_model->get_by_phone($phone);
    if ($result && isset($result['id']) && $result['id'] > 0) {
      //return $this->result('0','该手机号为经纪人帐号，不能申请');
    }
    $this->load->model('broker_sms_model');
    $result = $this->broker_sms_model->send_sms($phone, 'rent_finance');
    if ($result) {
      return $this->result('1', '发送成功');
    }
    $this->result('0', '发送失败');
  }

  public function test()
  {
    $data = array();
    $city_id = $this->user_arr['city_id'];
    $this->load->model('city_model');
    $city = $this->city_model->get_by_id($city_id);
    $data['mortgage'] = $city['mortgage'];
    $data['pledge'] = $city['pledge'];
    $data['rental'] = $city['rental'];
    $this->load->view('wap/test', $data);
  }
}

/* End of file broker.php */
/* Location: ./application/mls/controllers/broker.php */
