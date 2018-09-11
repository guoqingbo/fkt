<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 租房分期
 *
 */
class Rental extends MY_Controller
{
  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct('html');
      $this->qr = MLS_MOBILE_URL . '/rental/apply.html';
      $this->url = MLS_FINANCE_URL . '/rental/';
  }

  public function recommend()
  {
    $data = array();

    $submit_flag = $this->input->post('submit_flag');
    if ('1' == $submit_flag) {

      $tenant_phone = $this->input->post('tenant_phone');

      //检查是否经纪人号码
      $this->load->model('broker_model');
      $row = $this->broker_model->get_by_phone($tenant_phone);
      if (!empty($row)) {
        echo json_encode(array('result' => 'no', "msg" => "该手机号为经纪人帐号，不能申请"));
        exit;
      }

      //检查是否申请过租房分期
      $url = $this->url . 'customer_check?phone=' . $tenant_phone;
      $info = $this->curl($url);
      if ('1' != $info['result']) {
        echo json_encode(array('result' => 'no', "msg" => "该用户已经申请过租房分期，无法再次申请"));
        exit;
      }

      $data = array(
        'city_spell' => $this->user_arr['city_spell'],
        'broker_id' => $this->user_arr['broker_id'],
        'tenant_name' => $this->input->post('tenant_name'),
        'tenant_phone' => $this->input->post('tenant_phone'),
        'tenant_price' => $this->input->post('tenant_price'),
        'tenant_sex' => $this->input->post('tenant_sex'),
      );
      $url = $this->qr . '?data=' . json_encode($data);

      $img = get_qrcode($url, $this->config->item('login_city'));


      echo json_encode(array('result' => 'ok', "msg" => "成功", "data" => array('img' => $img)));
      exit;
    }

    $data['broker_id'] = $this->user_arr['broker_id'];
    $data['city_spell'] = $this->user_arr['city_spell'];
    $this->load->view('wap/rental/recommend', $data);

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

    $broker_id = $this->user_arr['broker_id'];
    $city_sepll = $this->user_arr['city_spell'];

    $keywords = $this->input->get_post('keywords');
    $data['keywords'] = $keywords;

    $info = $this->curl($this->url . 'customers?city_spell=' . $city_sepll . '&broker_id=' . $broker_id . '&keywords=' . $keywords);

    if ('1' == $info['result']) $data['list'] = $info['data']['list'];

    $this->load->view('wap/rental/customer', $data);
  }

  public function apply()
  {
    $data = array();

    $broker_id = $this->user_arr['broker_id'];
    $city_sepll = $this->user_arr['city_spell'];

    $action = $this->input->get('action', true);
    if ('apply_add' == $action) {
      $post_param = $this->input->post(NULL, TRUE);


      $this->load->model('broker_sms_model');
      $validcode_id = $this->broker_sms_model->get_by_phone_validcode($post_param['tenant_phone'], $post_param['validcode']);
      if (false == $validcode_id) return $this->result('0', '验证码错误');

      $this->broker_sms_model->validcode_set_esta($validcode_id);

      /*
      $post['tenant_cart'] = $post_param['tenant_cart'];
      $result = vpost($this->url.'customer_check?city_spell='.$city_sepll,$post);
      $result = json_decode($result,true);
      if($result['result'] == '1'){
         */
      $post = array(
        'broker_id' => $broker_id,
        'tenant_name' => $post_param['tenant_name'],
        'tenant_phone' => $post_param['tenant_phone'],
        'tenant_cart' => $post_param['tenant_cart'],
        'tenant_bank_id' => $post_param['tenant_bank_id'],
        'tenant_bank' => $post_param['tenant_bank'],
        'tenant_price' => $post_param['tenant_price'],
        'tenant_sex' => $post_param['tenant_sex'],
      );
      $result = vpost($this->url . 'tenant_add?city_spell=' . $city_sepll, $post);
      $result = json_decode($result, true);
      if ($result['result'] == '1') {
        return $this->result('1', '提交成功');
      } else {
        return $this->result('0', $result['msg']);
      }
      /*
   }else{
      return $this->result('0','该用户在申请中，请勿重复提交');
   }
   */
      return $this->result('0', '提交失败');
    }

    $this->load->view('wap/rental/apply', $data);
  }

  public function ad()
  {
    $data = array();

    $data['group_id'] = $this->user_arr['group_id'];
    $this->load->view('wap/rental/ad', $data);
  }

  public function upload()
  {
    $data = array();
    $data['inputid'] = $this->input->get('inputid');
    $data['limit'] = $this->input->get('limit');
    $this->load->view('wap/rental/upload', $data);
  }

  public function house($id)
  {
    $data = array();

    $broker_id = $this->user_arr['broker_id'];
    $city_sepll = $this->user_arr['city_spell'];

    $submit_flag = $this->input->post('submit_flag');
    if ('1' == $submit_flag) {
      $house_city = $this->input->post('house_city');
      $house_name = $this->input->post('house_name');
      $house_phone = $this->input->post('house_phone');
      $house_cart = $this->input->post('house_cart');
      $house_bank = $this->input->post('house_bank');
      $house_cart_photo = $this->input->post('house_cart_photo');
      $house_cart_photo = is_full_array($house_cart_photo) ? implode('::', $house_cart_photo) : '';
      $house_property_photo = $this->input->post('house_property_photo');
      $house_property_photo = is_full_array($house_property_photo) ? implode('::', $house_property_photo) : '';
      $house_contract_photo = $this->input->post('house_contract_photo');
      $house_contract_photo = is_full_array($house_contract_photo) ? implode('::', $house_contract_photo) : '';

      $data = array(
        'house_city' => $house_city,
        'house_name' => $house_name,
        'house_phone' => $house_phone,
        'house_cart' => $house_cart,
        'house_bank' => $house_bank,
        'house_cart_photo' => $house_cart_photo,
        'house_property_photo' => $house_property_photo,
        'house_contract_photo' => $house_contract_photo,
      );
      $info = $this->curl($this->url . 'house_add?city_spell=' . $city_sepll . '&broker_id=' . $broker_id . '&id=' . $id, $data);
      die(json_encode($info));
    }

    $data['id'] = $id;
    $this->load->view('wap/rental/house', $data);

  }
}

/* End of file broker.php */
/* Location: ./application/mls/controllers/broker.php */
