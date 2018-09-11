<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller
{

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *    http://example.com/index.php/welcome
   *  - or -
   *    http://example.com/index.php/welcome/index
   *  - or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see http://codeigniter.com/user_guide/general/urls.html
   */
  public function index($pclogin = 0)
  {
    setcookie('mortgage', '0', time() - 3600, '/');

      $data_view = array();
      //当前用户信息
      $this_user = $this->user_arr;
      $data_view['this_user'] = $this_user;
    //系统标题
    $data_view['title'] = $this->config->item('title');
    //系统菜单
    $data_view['menu'] = $this->purview_tab_model->get_module();

      if ($pclogin == 1) {
      $this->frame('pcwelcome', $data_view);
    } else {
      $this->frame('welcome', $data_view);
    }
  }

  public function save_suggest()
  {
    //意见建议
    $this->load->model('suggest_model');

    //经纪人信息
    $this->load->model('signatory_model');
    $feedback = $this->input->post('feedback');
    $signatory_id = $this->user_arr['signatory_id'];
    $signatory_info = $this->signatory_model->get_by_id($signatory_id);
    $telno = $signatory_info['phone'];
    $city_id = $signatory_info['city_id'];
    $add_data = array(
      'feedback' => $feedback,
      'telno' => $telno,
      'city_id' => $city_id,
      'status' => 1,
      'dateline' => time()
    );

    $result = $this->suggest_model->insert($add_data);

    if ($result) {
      echo 1;
    } else {
      echo 0;
    }
  }

  public function test()
  {
    $this->load->model('purview_tab_model');

    $this->purview_tab_model->get_module();
    echo $this->purview_tab_model->reset_tabs('rent', 'lists_pub');
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
