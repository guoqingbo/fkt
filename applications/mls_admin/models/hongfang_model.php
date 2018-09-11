<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of relation_street_district_model
 *
 * @author ccy
 */
class Hongfang_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  function add_hongfang_data($data)
  {
    $result = $this->add_data($data, 'db_city', 'hongfang_data');
  }

  function get_hongfang_data($id)
  {
    $where = array('id' => $id, 'status' => 0);
    return $this->get_data(array('form_name' => 'hongfang_data', 'where' => $where, 'limit' => 20), 'dbback_city');
  }

  function update_hongfang_data($id)
  {
    $paramlist = array('status' => 1);
    return $this->modify_data(array('id' => $id), $paramlist, 'db_city', 'hongfang_data');
  }


  /**
   * 添加关联第三方区属数据
   */
  function add_block($data)
  {
    $result = $this->add_data($data, 'db_city', 'hongfang_block');
    return $result;
  }


  /**
   * 添加关联第三方区属数据
   */
  function add_door($data)
  {
    $result = $this->add_data($data, 'db_city', 'hongfang_door');
    return $result;
  }

  /**
   * 添加关联第三方区属数据
   */
  function add_room($data)
  {
    $result = $this->add_data($data, 'db_city', 'hongfang_room');
    return $result;
  }

  function get_block($id)
  {
    $where = array('id>' => $id);
    return $this->get_data(array('form_name' => 'hongfang_block', 'where' => $where, 'limit' => 20), 'dbback_city');
  }
}
