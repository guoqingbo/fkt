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
 * Advert_app_manage_base_model CLASS
 *
 * APP广告管理
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Advert_app_manage_base_model extends MY_Model
{

  private $_tbl = '';

  private $_tbl1 = 'advert_app';

  private $_tbl2 = 'advert_app_news';

  private $_tbl3 = 'advert_app_push';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->set_tbl();
  }

  public function set_tbl($tbl_type = 1)
  {
    if ($tbl_type == 1) {
      $this->_tbl = $this->_tbl1;
    } else if ($tbl_type == 2) {
      $this->_tbl = $this->_tbl2;
    } else if ($tbl_type == 3) {
      $this->_tbl = $this->_tbl3;
    }
  }

  public function get_tbl()
  {
    return $this->_tbl;
  }

  /**
   * 获取所有广告
   */
  public function get_all_by()
  {
    $tbl = $this->get_tbl();
    $this->dbback_city->order_by('id', 'ASC');
    //返回结果
    return $this->dbback_city->get($tbl)->result_array();
  }

  public function get_one_by($id = '')
  {
    $tbl = $this->get_tbl();
    //查询条件
    if ($id) {
      $this->dbback_city->where('id', $id);
    }
    return $this->dbback_city->get($tbl)->row_array();
  }

  public function insert($insert_data)
  {
    $tbl = $this->get_tbl();
    //单条插入
    if ($this->db_city->insert($tbl, $insert_data)) {
      return $this->db_city->insert_id();
    }
    echo $this->db_city->last_query();
    return false;
  }

  public function update_by_id($update_data, $id = '')
  {
    $tbl = $this->get_tbl();
    if ($id) {
      $this->db_city->where('id', $id);
    }
    $this->db_city->update($tbl, $update_data);
    return $this->db_city->affected_rows();
  }
}

/* End of file Advert_app_manage_base_model.php */
/* Location: ./app/models/Advert_app_manage_base_model.php */
