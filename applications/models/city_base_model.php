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
 * City_base_model CLASS
 *
 * 城市类，用于获取省份和城市之间关联的
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class city_base_model extends MY_Model
{

  /**
   * 城市表
   * @var string
   */
  private $_tbl = 'city';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }


  /**
   * 获取开通的省份
   * @return json 省份列表 ['江苏', '上海']
   */
  public function get_province()
  {
    $this->dbback->distinct();
    $this->dbback->select('province');
    $this->dbback->where('status', 1);
    $this->dbback->order_by('order', 'ASC');
    $province = $this->dbback->get($this->_tbl)->result_array();
    $new_province = array();
    if ($province) {
      foreach ($province as $v) {
        $new_province[] = $v['province'];
      }
    }
    return $new_province;
  }

  /**
   * 获取开通的城市
   * @return array
   */
  public function get_all_city()
  {
    $this->dbback->select('province, id, cityname ,spell');
    $this->dbback->where('status', 1);
    return $this->dbback->get($this->_tbl)->result_array();
  }

  /**
   * 根据省份名称获取城市列表
   * @param string $province 省名
   * @return array
   */
  public function get_city_by_province($province)
  {
    $this->dbback->select('id, cityname');
    $this->dbback->where('status', 1);
    $this->dbback->where('province', $province);
    return $this->dbback->get($this->_tbl)->result_array();
  }

  /**
   * 根据省份名称获取城市列表
   * @param string $province 省名
   * @return array
   */
  public function get_citys_group_province()
  {
    $this->dbback->select('province, id, cityname');
    $this->dbback->where('status', 1);
    $this->dbback->group_by('province');
    return $this->dbback->get($this->_tbl)->result_array();
  }

  /**
   * 根据城市编号获取城市详细信息
   * @param type $id
   * @return type
   */
  public function get_by_id($id)
  {
    $this->dbback->where('id', $id);
    $this->dbback->where('status', 1);
    return $this->dbback->get($this->_tbl)->row_array();
  }

  /**
   * 根据城市拼音spell获取城市id
   * @param string $spell 城市拼音
   * @return array
   */
  public function get_city_by_spell($spell)
  {
    $this->dbback->select('id,cityname');
    $this->dbback->where('status', 1);
    $this->dbback->where('spell', $spell);
    return $this->dbback->get($this->_tbl)->row_array();
  }

  /**
   * 获取第一个有效城市spell
   * @return array
   */
  public function get_first_by()
  {
    $this->dbback->where('status', 1);
    return $this->dbback->get($this->_tbl)->row_array();
  }

}

/* End of file city_base_model.php */
/* Location: ./application/models/city_base_model.php */
