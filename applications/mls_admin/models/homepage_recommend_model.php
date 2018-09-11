<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class homepage_recommend_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  private $_tbl = 'homepage_recommend';

  /*获取所有信息
   *
   */
  public function get_all()
  {
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  //获取所有信息
  public function get_by_id($id)
  {
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 更新系统权限角色数据
   * @param array $update_data 更新的数据源数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    $this->db_city->where_in('id', $id);
    $this->db_city->update($this->_tbl, $update_data);
    return $this->db_city->affected_rows();
  }
}
