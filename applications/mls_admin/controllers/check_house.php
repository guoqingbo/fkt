<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 权限菜单管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class check_house extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('check_house_model');
    $this->load->helper('user_helper');
  }

  /**
   * 权限菜单列表页面
   */
  public function index()
  {
    $type = $this->input->post('type', true);
    if ($type == "") {

      $type = "sell";

    }

    switch ($type) {
      case "sell":
        $this->_sell();
        break;
      case "rent":
        $this->_rent();
        break;
      case "new_house":
        $this->_new_house();
        break;
    }
  }

  //新房
  public function _new_house()
  {
    $city = $_SESSION[WEB_AUTH]["city"];
    $where = "apnt.type = 3 ";
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //分页开始
    $data['check_house_num'] = $this->check_house_model->count_by_new_house($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['check_house_num'] ? ceil($data['check_house_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $apnt = $this->check_house_model->get_list_by_new_house_apnt($where, $data['offset'], $data['pagesize']);
    $new_house = $this->check_house_model->get_list_by_new_house(array('city' => $city));
    //print_r($new_house);
    foreach ($apnt as $key => $vo) {
      foreach ($new_house as $h) {
        if ($vo['house_id'] == $h['lp_id']) {
          $data['check_house'][$key]['id'] = $vo['id'];
          $data['check_house'][$key]['uname'] = $vo['uname'];
          $data['check_house'][$key]['house_id'] = $vo['house_id'];
          $data['check_house'][$key]['phone'] = $vo['phone'];
          $data['check_house'][$key]['ctime'] = $vo['ctime'];
          $data['check_house'][$key]['lp_name'] = $h['lp_name'];
          $data['check_house'][$key]['lp_loc'] = $h['lp_loc'];
        }
      }
    }
    //print_r($data['check_house']);
    //$this->load->view('check_house/_new_house',$data);
  }


  //出售房源
  public function _sell()
  {
    $where = "apnt.type = 1 ";
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $post_param['type'] = 'sell';
    $data['post_param'] = $post_param;
    //分页开始
    $data['check_house_num'] = $this->check_house_model->count_by_sell($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['check_house_num'] ? ceil($data['check_house_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['check_house'] = $this->check_house_model->get_list_by_sell($where, $data['offset'], $data['pagesize']);
    //print_r($data['check_house']);
    $this->load->view('check_house/_sell', $data);

  }

  //出租房源
  public function _rent()
  {
    $where = "apnt.type = 2 ";
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //分页开始
    $data['check_house_num'] = $this->check_house_model->count_by_rent($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['check_house_num'] ? ceil($data['check_house_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['check_house'] = $this->check_house_model->get_list_by_rent($where, $data['offset'], $data['pagesize']);
    //print_r($data['check_house']);
    $this->load->view('check_house/_sell', $data);

  }

}


/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
