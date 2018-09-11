<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * blacklist_model CLASS
 *
 * 中介黑名单模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          angel_in_us
 */
class Blacklist_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->agent_blacklist = 'agent_blacklist';
    $this->agent_reportlist = 'agent_reportlist';
  }


  /**
   * 获得所有中介黑名单
   */
  public function get_blacklist($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback')
  {
    $city_id = $_SESSION['esfdatacenter']['city_id'];
    $where['city'] = $city_id;
    $blacklist = $this->get_data(array('form_name' => $this->agent_blacklist, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $blacklist;
  }


  /**
   * 获取中介黑名单总数
   */
  function get_blacklist_num($where = array(), $database = 'dbback')
  {
    $city_id = $_SESSION['esfdatacenter']['city_id'];
    $where['city'] = $city_id;
    $blacklist = $this->get_data(array('form_name' => $this->agent_blacklist, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $blacklist[0]['num'];
  }


  /**
   * 查询即将入库的电话是否在agent_blacklist表里已经存在
   */
  function check_tel($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->agent_blacklist, 'where' => $where, 'select' => array('tel,store')), $database);
    return @$result[0]['tel'];
  }


  /**
   * 获得所有待审核中介号码
   */
  public function get_reportlist($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city', $order_by = 'r_addtime')
  {
    $reportlist = $this->get_data(array('form_name' => $this->agent_reportlist, 'where' => $where, 'order_by' => $order_by, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $reportlist;
  }

  /**
   * 详情
   */
  public function get_report_info_by_id($cmt_id, $limit = 1, $database = 'dbback_city')
  {
    $report_info = array();
    $keyword = intval(strip_tags($cmt_id));

    if ($cmt_id != '') {
      $where_cond = array(
        'r_id' => $cmt_id
      );
      $report_info = $this->get_data(array('form_name' => $this->agent_reportlist, 'where' => $where_cond), $database);
    }
    return $report_info;
  }


  /**
   * 把已审核的中介黑名单插入到 agent_blacklist
   */
  public function add_blacklist($data = array(), $database = 'db_city', $form_name = '')
  {
    $agent_blacklist = $this->add_data($data, $database, $this->agent_blacklist);
    return $agent_blacklist;
  }


  /**
   * 获得待审核中介相关信息
   */
  public function get_report_agent($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $report_agent = $this->get_data(array('form_name' => $this->agent_reportlist, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $report_agent;
  }


  /**
   * 获取待审核中介总数
   */
  function get_reportlist_num($where = array(), $database = 'dbback_city')
  {
    $reportlist = $this->get_data(array('form_name' => $this->agent_reportlist, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $reportlist[0]['num'];
  }


  /**
   * 删除中介黑名单
   */
  function del_blacklist($arr = array())
  {
    $result = $this->del($arr, 'db_city', $this->agent_blacklist);
    return $result;
  }


  /**
   * 删除虚假待审核的中介号码
   */
  function del_reportlist($arr = array())
  {
    $result = $this->del($arr, 'db_city', $this->agent_reportlist);
    return $result;
  }


  /**
   * 修改待审核中介状态
   */
  function update_reportlist($where = array(), $arr = array())
  {
    $result = $this->modify_data($where, $arr, 'db_city', $this->agent_reportlist);
    return $result;
  }
}

/* End of file blacklist_model.php */
/* Location: ./application/mls_admin/models/blacklist_model.php */
