<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市控制器
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class City extends MY_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('city_model');
  }

  /**
   * 获取开通的省份
   * @return json 省份列表 ['江苏', '上海']
   */
  public function get_province()
  {
    return $this->city_model->get_province();
  }

  /**
   * 根据省份名称获取城市名称
   * @param string $province 以GET方式获取省份名下城市
   * @return json 城市列表[{'id' ： 城市编号, 'cityname' : '城市名称'}]
   */
  public function get_city()
  {
    $province = $this->input->get('province', TRUE);
    $get_citys = $this->city_model->get_city_by_province($province);
    if (!$get_citys) {
      $get_citys = array();
    }
    echo json_encode($get_citys);
  }
}
/* End of file city.php */
/* Location: ./application/mls/controllers/city.php */
