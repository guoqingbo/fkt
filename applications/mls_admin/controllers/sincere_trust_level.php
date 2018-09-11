<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 信用值设置
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Sincere_trust_level extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('sincere_trust_level_model');
    $this->load->helper('user_helper');
  }

  public function index()
  {
    $data_view = array();
    $level = $this->sincere_trust_level_model->get_all_by('');
    $data_view['level'] = $level;
    $data_view['title'] = '信用值设置';
    $data_view['conf_where'] = 'index';
    $this->load->view('sincere_trust/level', $data_view);
  }

  public function save()
  {
    $update_fields = array();
    $data = $this->input->post('data');
    if (is_full_array($data)) {
      foreach ($data as $value) {
        $update_fields[$value['way_alias']] = $value['score'];
        $id = $value['id'];
      }
      $this->sincere_trust_level_model->update_by_id($update_fields, $id);
      echo json_encode(array('result' => 1));
    } else {
      echo json_encode(array('result' => 0));
    }
  }
}
