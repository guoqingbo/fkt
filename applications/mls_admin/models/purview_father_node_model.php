<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 */

// ------------------------------------------------------------------------

/**
 * Buy_match_model CLASS
 *
 * 权限根节点模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class Purview_father_node_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->purview_node_base = 'purview_node_base';
  }

  /**
   * 获得节点
   */
  public function get_base_node($offset = 0, $pagesize = 0, $database = 'dbback')
  {
    $data = $this->get_data(array('form_name' => $this->purview_node_base, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $data;
  }

  /**
   * 根据ID获得详情
   */
  public function get_node_by_id($id = '', $database = 'dbback')
  {
    $wherecond = array('id' => $id);
    $userData = $this->get_data(array('form_name' => $this->purview_node_base, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 获取节点总数
   */
  function get_base_node_num($database = 'dbback')
  {
    $node = $this->get_data(array('form_name' => $this->purview_node_base, 'select' => array('count(*) as num')), $database);
    return $node[0]['num'];
  }

  /**
   * 添加用户
   */
  function add_node($paramlist = array(), $database = 'db')
  {
    $result = $this->add_data($paramlist, $database, $this->purview_node_base);
    return $result;
  }

  /**
   * 修改节点
   */
  function modify_node($id, $paramlist = array(), $database = 'db')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->purview_node_base);
    return $result;
  }

  /**
   * 删除节点
   */
  function del_node($id = '')
  {
    $result = $this->del(array('id' => $id), 'db', $this->purview_node_base);
    return $result;
  }

}

/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
