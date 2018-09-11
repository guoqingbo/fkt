<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Module_news_base_model
 *
 * @author 365
 */
class Module_news_base_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->module_news = 'module_news';
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_get_num($where = '')
  {
    $node = $this->get_data(array('form_name' => $this->module_news, 'where' => $where, 'select' => array('count(*) as num')), 'dbback_city');
    return $node[0]['num'];
  }

  /**
   * 把公告插入到module_news
   */
  public function add_news($data = array(), $database = 'db_city')
  {
    $result = $this->add_data($data, $database, $this->module_news);
    return $result;
  }

  /**
   * 获取采集群发公告
   * module_news
   */
  public function get_news($where = '', $offset = 0, $pagesize = 0)
  {
    $data = $this->get_data(array('form_name' => $this->module_news, 'where' => $where, 'limit' => $offset, 'order_by' => 'id', 'offset' => $pagesize), 'dbback_city');
    return $data;
  }

  /**
   * 根据id获取采集群发公告
   * module_news
   */
  public function get_news_byid($id = '')
  {
    $where = array('id' => $id);
    $data = $this->get_data(array('form_name' => $this->module_news, 'where' => $where), 'dbback_city');
    return $data[0];
  }

  /**
   * 更新module_news信息
   */
  public function update_news($where, $data, $database = 'db_city', $form_name = '')
  {
    $result = $this->modify_data($where, $data, $database, $this->module_news);
    return $result;
  }

  /**
   * 删除module_news表中数据
   */
  public function del_news($where, $database = 'db_city')
  {
    $result = $this->del($where, $database, $this->module_news);
    return $result;
  }
}
