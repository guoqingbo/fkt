<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * 保密信息和跟进进程类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Secret_follow_process_base_model CLASS
 *
 *
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          yuan
 */
class Secret_follow_process_base_model extends MY_Model
{
  /**
   * 表名称
   * @var string
   */
  private $_tbl = 'secret_follow_process';


  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';


  /**
   * 设置表名称
   *
   * @access  public
   * @param  string $tblname 表名称
   * @return  void
   */
  public function set_tbl($tblname)
  {
    $this->_tbl = trim(strip_tags($tblname));
  }


  /**
   * 获取表名称
   *
   * @access  public
   * @param  void
   * @return  string
   */
  public function get_tbl()
  {
    return $this->_tbl;
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

  /**
   * 添加
   * @param array $paramlist 字段
   * @return insert_id or 0
   */
  function add($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db_city->insert($this->_tbl, $paramlist);//插入数据

      if (($this->db_city->affected_rows()) >= 1) {
        $result = $this->db_city->insert_id();//如果插入成功，则返回插入的id
      } else {
        $result = 0;    //如果插入失败,返回0
      }
    } else {
      $result = 0;
    }

    return $result;
  }

  /**
   * 修改
   * @param array $paramlist 字段
   * @return insert_id or 0
   */
  function update($id = 0, $update_data = array())
  {
    if ($id) {
      $this->db_city->where('id', $id);
    }
    if (isset($update_data) && is_array($update_data)) {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 筛选
   * @param array $where where字段
   * @return array 以信息组成的多维数组
   */
  public function get($where = array(), $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->_tbl, 'where' => $where), $database);
    return $comm;
  }

}


/* End of file block_base_model.php */
/* Location: ./models/block_base_model.php */
