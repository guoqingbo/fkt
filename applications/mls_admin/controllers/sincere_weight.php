<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 诚信指数权重设置
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Sincere_weight extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('sincere_weight_model');
    $this->load->helper('user_helper');
  }

  public function index()
  {
    $data_view = array();
    $weight = $this->sincere_weight_model->get_all_by('');
    $data_view['weight'] = $weight;
    $data_view['title'] = '动态评分权重设置';
    $data_view['conf_where'] = 'index';
    $this->load->view('sincere_trust/weight', $data_view);
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
      $this->sincere_weight_model->update_by_id($update_fields, $id);
      echo json_encode(array('result' => 1));
    } else {
      echo json_encode(array('result' => 0));
    }
  }
}
