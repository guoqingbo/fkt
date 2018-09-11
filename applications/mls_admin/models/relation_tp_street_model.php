<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of relation_tp_street_model
 * 第三方板块关联
 * @author ccy
 */
class Relation_tp_street_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  //采集区属关联表
  private $_district = "relation_district";

  //采集板块关联表
  private $_street = "relation_street";

  //采集板块关联表
  private $_tp_district = "relation_tp_district";

  //采集板块关联表
  private $_tp_street = "relation_tp_street";

  /**
   * 查询第三方网站板块全部数据
   */
  public function select_tp_street($where)
  {
    $this->dbback_city->where($where);
    return $this->dbback_city->get($this->_tp_street)->result_array();
  }

  /**
   * 根据板块名，在第三方板块关联表返回对应数据的id
   */
  public function select_street($where)
  {
    $this->dbback_city->where($where);
    return $this->dbback_city->get($this->_street)->result_array();
  }

  /**
   * 更新对应id的板块第三方数据
   */
  public function update_street($id = '', $data)
  {
    $this->db_city->where('id', $id);
    return $this->db_city->update($this->_street, $data);
  }

  /**
   * 查询第三方网站区属全部数据
   */
  public function select_tp_district($where)
  {
    $this->dbback_city->where_in($where);
    return $this->dbback_city->get($this->_tp_district)->result_array();
  }

  /**
   * 添加第三方网站区属全部数据
   */
  public function add_tp_district($data)
  {
    $result = $this->select_tp_district($data);
    $id = $result[0]['district_id'];
    if (!$id) {
      $id = $this->db_city->insert($this->_tp_district, $data);
    }
    return $id;
  }

  /**
   * 第三方网站区属全部数据同步到区属
   */
  public function add_district($data)
  {
    $select_data = array('district' => $data['district'], 'city_id' => $data['city_id']);
    $this->dbback_city->where($select_data);
    $result = $this->dbback_city->get('district')->row_array();
    $id = $result['id'];
    if (!$result) {
      $this->db_city->insert('district', $data);
      $id = $this->db_city->insert_id();
    }
    return $id;
  }


  /**
   * 第三方网站区属全部数据同步到区属
   */
  public function add_district_xf($data)
  {
    $select_data = array('district' => $data['district']);
    $this->dbback_city->where($select_data);
    $result = $this->dbback_city->get('district_xf')->row_array();
    $id = $result['id'];
    if (!$result) {
      $this->db_city->insert('district_xf', $data);
      $id = $this->db_city->insert_id();
    }
    return $id;
  }

  /**
   * 添加第三方网站板块全部数据
   */
  public function add_tp_street($data)
  {
    $result = $this->select_tp_street($data);
    if (!$result) {
      $result = $this->db_city->insert($this->_tp_street, $data);
    }
    return $result;
  }

  /**
   * 第三方网站区属全部数据同步到区属
   */
  public function add_street($data)
  {
    $select_data = array('streetname' => $data['streetname'], 'dist_id' => $data['dist_id']);
    $this->dbback_city->where($select_data);
    $result = $this->dbback_city->get('street')->row_array();
    if (!$result) {
      $result = $this->db_city->insert('street', $data);
    }
    return $result;
  }

  /**
   * 第三方网站区属全部数据同步到区属
   */
  public function add_street_xf($data)
  {
    $select_data = array('streetname' => $data['streetname']);
    $this->dbback_city->where($select_data);
    $result = $this->dbback_city->get('street_xf')->row_array();
    if (!$result) {
      $result = $this->db_city->insert('street_xf', $data);
    }
    return $result;
  }


  /**
   * 根据区属id查询对应区属内所有板块
   */
  public function select_id_street($id = '', $type = '')
  {
    $where = array('dist_id' => $id, 'tp_type' => $type);
    return $this->get_data(array('form_name' => $this->_tp_street, 'where' => $where), 'dbback_city');
  }

  /**
   * 添加关联第三方数据
   */
  public function add_relation_street($data)
  {
    $this->dbback_city->where($data);
    $result = $this->dbback_city->get($this->_street)->row_array();
    if (!$result) {
      $this->db_city->insert($this->_street, $data);
    }
    return $this->db_city->insert_id();
  }

  /**
   * 根据区属id获取第三方数据
   */
  public function select_relation_street($street_id)
  {
    $where = array('street_id' => $street_id);
    return $this->get_data(array('form_name' => $this->_street, 'where' => $where), 'dbback_city');
  }

  /**
   * 根据对应板块id更新关联表
   */
  function update_relation_street($street_id, $data)
  {
    $this->db_city->where('street_id', $street_id);
    return $this->db_city->update($this->_street, $data);
  }

  /**
   * 根据区属id获取板块数据
   */
  function get_streets($where = array())
  {
    $result = $this->get_data(array('form_name' => 'street', 'where' => $where), 'dbback_city');
    return $result;
  }
}
