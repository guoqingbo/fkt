<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * 功能迭代通知表 业务类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * features_notice_base_model CLASS
 *
 * 功能迭代通知表 数据管理相关功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          yuan
 */
class Features_notice_base_model extends MY_Model
{
  /**
   * 功能迭代通知表表名称
   * @var string
   */
  private $_tbl = 'features_notice';

  /**
   * 功能迭代留言表
   * @var string
   */
  private $_tb2 = 'features_leave_message';


  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';


  /**
   * 设置功能迭代通知表表名称
   *
   * @access  public
   * @param  string $tblname 功能迭代通知表表名称
   * @return  void
   */
  public function set_tbl($tblname)
  {
    $this->_tbl = trim(strip_tags($tblname));
  }


  /**
   * 获取功能迭代通知表表名称
   *
   * @access  public
   * @param  void
   * @return  string 区属表名称
   */
  public function get_tbl()
  {
    return $this->_tbl;
  }


  /**
   * 设置功能迭代留言表名称
   *
   * @access  public
   * @param  string $tblname 功能迭代通知表图片表名称
   * @return  void
   */
  public function set_tb2($tblname)
  {
    $this->_tb2 = trim(strip_tags($tblname));
  }


  /**
   * 获取功能迭代留言表表名称
   *
   * @access  public
   * @param  void
   * @return  string 功能迭代通知表图片表名称
   */
  public function get_tb2()
  {
    return $this->_tb2;
  }


  /**
   * 设置需要查询的字段
   * @param array $select_fields
   */
  public function set_select_fields($select_fields)
  {
    $select_fields_str = '';

    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
    }

    $this->select_fields = $select_fields;
  }

  /**
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->select_fields;
  }

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

}


/* End of file block_base_model.php */
/* Location: ./models/block_base_model.php */
