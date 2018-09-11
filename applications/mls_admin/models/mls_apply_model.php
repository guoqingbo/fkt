<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class mls_apply_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  private $_tbl = 'apply_manage';


  /*获取所有信息
   *
   */
  public function get_all($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback')
  {
    $where = array('type' => 1);
    $data = $this->get_data(array('form_name' => $this->_tbl, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $data;
  }

  /**
   * 添加管理数据
   */
  function add_mls($paramlist = array(), $database = 'db')
  {
    $result = $this->add_data($paramlist, $database, $this->_tbl);
    return $result;
  }

  /**
   * 根据ID获得详情
   */
  public function get_by_id($id = '', $database = 'dbback')
  {
    $wherecond = array('id' => $id);
    $userData = $this->get_data(array('form_name' => $this->_tbl, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 修改意见反馈
   */
  function update_by_id($id, $paramlist = array(), $database = 'db')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->_tbl);
    return $result;
  }

}
