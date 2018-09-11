<?php

/**
 * Description of push_record
 *
 * @author user
 */
class Push_record_base_model extends MY_Model
{

  private $_message_result_tbl = '';

  /**
   * 设置查询数据的表名
   * @param string $tbl
   */
  public function set_result_tbl($message_result_tbl)
  {
    $this->_message_result_tbl = $message_result_tbl;
  }

  /**
   * 获取查询数据的表名
   * @return string $tbl 表名
   */
  public function get_result_tbl()
  {
    return $this->_message_result_tbl;
  }

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function insert_unicast_result($insert_data)
  {
    $this->db_city->insert($this->_message_result_tbl, $insert_data);
  }

  public function insert_broadcase_result($insert_data)
  {
    $this->db->insert($this->_message_result_tbl, $insert_data);
  }
}
