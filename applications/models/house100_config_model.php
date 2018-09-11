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
class House100_config_model extends MY_Model
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

  public function get_house_type()
  {
    $this->dbback->where('type', "f100_sell_type");
    $result = $this->dbback->get($this->_tbl)->result_array();
    $data['type'][0] = array('key' => '0', 'name' => '不限');
    foreach ($result as $key => $val) {
      $data['type'][$key + 1]['key'] = $key + 1;
      $data['type'][$key + 1]['name'] = $val['name'];
    }
    return $data;
  }

  public function get_rent_type()
  {
    $this->dbback->where('type', "f100_rent_type");
    $result = $this->dbback->get($this->_tbl)->result_array();
    $data['type'][0] = array('key' => '0', 'name' => '不限');
    foreach ($result as $key => $val) {
      $data['type'][$key + 1]['key'] = $key + 1;
      $data['type'][$key + 1]['name'] = $val['name'];
    }
    return $data;
  }
}
