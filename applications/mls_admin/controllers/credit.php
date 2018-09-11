<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 积分管理设置
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Credit extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
  }

  //展示所有的积分规则
  public function index()
  {
    $data_view = array();
    $this->load->model('credit_way_model');
    $credit_way = $this->credit_way_model->get_all_by('ishidden = 0');
    $data_view['credit_way'] = $credit_way;
    $data_view['title'] = '积分设置';
    $data_view['conf_where'] = 'index';
    $this->load->view('credit/seting', $data_view);
  }


  public function save()
  {
    $update_fields = array();
    $data = $this->input->post('data');
    if (is_full_array($data)) {
      $this->load->model('credit_way_model');
      foreach ($data as $value) {
        $update_fields[$value['way_alias']] = $value['score'];
        $id = $value['id'];
      }
      $this->credit_way_model->update_by_id($update_fields, $id);
      echo json_encode(array('result' => 1));
    } else {
      echo json_encode(array('result' => 0));
    }
  }
}
