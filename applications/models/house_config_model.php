<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */
// ------------------------------------------------------------------------


/**
 * House_config_model CLASS *
 *
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Lion
 */
class House_config_model extends MY_Model
{
  /**
   * 类型
   *
   * @access private
   * @var string
   */
  private $_type = NULL;


  /**
   * 表名
   *
   * @access private
   * @var string
   */
  protected $_tbl = 'house_config';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_mem_key = 'common_house_config_model_';
  }

  /**
   * 获取基础配置参数
   * @return array
   */
  public function get_config()
  {
    $mem_key = $this->_mem_key . 'config';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $data = $cache['data'];
    } else {
      $this->dbback->order_by('sort', 'ASC');
      $config = $this->dbback->get($this->_tbl)->result_array();
      //echo $this->dbback->last_query();
      $data = array();
      foreach ($config as $key => $val) {
        $data[$val['type']][$val['sort']] = $val['name'];
      }

      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $data), 3600);
    }
    return $data;
  }

  /**
   * 获取基础配置参数
   * @return array
   */
  public function get_house_status_config($type = 'sell')
  {
    //获取出租信息基本配置资料
    $house_config = $this->get_config();
    //基本信息‘状态’数据处理
    if (!empty($house_config['status']) && is_array($house_config['status'])) {
      foreach ($house_config['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $house_config['status'][$k] = $type == 'sell' ? '暂不售' : '暂不租';
        }
      }
    }

    return $house_config['status'];
  }

  /**
   * 获取基础配置参数
   * @return array
   */
  public function get_config_xffx()
  {
    $this->dbback_city->order_by('sort', 'ASC');
    $config = $this->dbback_city->get($this->_tbl)->result_array();
    //echo $this->dbback->last_query();
    $data = array();
    foreach ($config as $key => $val) {
      $data[$val['type']][$val['sort']] = iconv('UTF-8', 'GBK', $val['name']);
    }
    return $data;
  }
}


/* End of file customer_base_model.php */
/* Location: ./application/models/customer_base_model.php */
