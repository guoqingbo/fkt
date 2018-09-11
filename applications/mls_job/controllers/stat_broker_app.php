<?php

/**
 * 统计APP数据
 * @package     mls
 * @subpackage  Controllers
 * @category    Controllers
 * @author      Fisher
 */
class Stat_broker_app extends MY_Controller
{

  private $_city = '';

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('notice_access_model', 'na');
  }

  /**
   * @param string $city 城市
   */
  public function index()
  {
    $this->_city = $this->input->get('city', TRUE);

    $this->load->model('city_model');//城市模型类
    $citydata = $this->city_model->get_city_by_spell($this->_city);
    $cityid = intval($citydata['id']);
    unset($citydata);

    $this->load->model('stat_broker_app_model', 'ba');//APP统计模型类

    $ymd = date('Y-m-d');
    $return = $this->ba->add_broker_app_count($ymd, $cityid);

    if ($return > 0) {
      echo 'stat success';
    } else {
      echo 'stat fail';
    }
  }
}
