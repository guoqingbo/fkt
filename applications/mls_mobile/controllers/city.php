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

  public function get_config()
  {
    $config = array('province' => array(), 'citys' => array());
    $province = $this->get_province();
    if (is_full_array($province)) {
      $config['province'] = $province;
      $citys = $this->get_citys_group_province();
      if (is_full_array($citys)) {
        $new_citys = array();
        foreach ($citys as $k => $v) {
          if (!isset($new_citys[$v['province']])) {
            $new_citys[$v['province']] = array();
          }
          $new_citys[$v['province']][] = array(
            'id' => $v['id'], 'cityname' => $v['cityname'],
          );
        }
        $config['citys'] = $new_citys;
      }
    }
    echo $this->result(1, '查询成功', $config);
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
   * 以省份分组并获取城市名称
   * @param string $province 以GET方式获取省份名下城市
   * @return json 城市列表[{'id' ： 城市编号, 'cityname' : '城市名称'}]
   */
  public function get_citys_group_province()
  {
    $get_citys = $this->city_model->get_citys_group_province();
    return $get_citys;
  }
}
/* End of file city.php */
/* Location: ./application/mls/controllers/city.php */
