<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Im_sync_message extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();


  }

  public function fkt()
  {
    $timestamp = $this->input->get('timestamp', true);
    $nonce = $this->input->get('nonce', true);
    $signature = $this->input->get('signature', true);
    $this->load->library('im/RongCloudApi', '', 'rca');
    $validate_sign = $this->rca->validate_sign($nonce, $timestamp, $signature);
    if ($validate_sign == true) {
      $fromUserId = $this->input->post('fromUserId', true);
      $toUserId = $this->input->post('toUserId', true);
      $objectName = $this->input->post('objectName', true);
      $content = $this->input->post('content', true);
      $timestamp = $this->input->post('timestamp', true);
      $channelType = $this->input->post('channelType', true);
      $msgTimestamp = $this->input->post('msgTimestamp', true);
      $msgUID = $this->input->post('msgUID', true);

      $this->load->model('broker_model');
      $broker = $this->broker_model->get_by_id($fromUserId);
      if (!is_full_array($broker)) {
        echo $this->result(false, '失败');
        return false;
      }

      $this->load->model('city_model');
      $city = $this->city_model->get_by_id($broker['city_id']);

      $this->set_city($city['spell']);

      $this->load->model('im_sync_message_base_model');
      $sync_data = array(
        'from_user_id' => $fromUserId, 'to_user_id' => $toUserId,
        'object_name' => $objectName, 'content' => $content,
        'timestamp' => $timestamp, 'channel_type' => $channelType,
        'msg_timestamp' => $msgTimestamp, 'msg_uid' => $msgUID,
        'update_time' => time(),
      );
      $insert_id = $this->im_sync_message_base_model->insert_fkt_data($sync_data);
      if ($insert_id > 0) {
        echo $this->result(true);
      } else {
        echo $this->result(false, '失败');
      }
    } else {
      echo $this->result(false, '失败');
    }
  }
}

?>
