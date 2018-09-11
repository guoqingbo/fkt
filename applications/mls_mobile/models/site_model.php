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
 * site_model CLASS
 *
 * 采集模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @date      2014-12-28
 * @author          angel_in_us
 */
class Site_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->mass_site = 'mass_site';
    $this->mass_site_broker = 'mass_site_broker';
  }


  /**
   * 获取要设置网站的总数量
   * @date      2015-01-21
   * @author       angel_in_us
   */
  function get_site_num($where = array(), $like = array(), $database = 'db_city')
  {
    $sell_sum = $this->get_data(array('form_name' => $this->mass_site, 'where' => $where, 'like' => $like, 'select' => array('count(*) as num')), $database);
    return $sell_sum[0]['num'];
  }


  /**
   * 获取要设置网站的信息
   * @date      2015-01-21
   * @author       angel_in_us
   */
  function get_site_info($where = array(), $where_in = array(), $like = array(), $offset = 0, $limit = 10, $database = 'db_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }


  /**
   * 根据经纪人id获取他(她)所开通的群发网站信息
   * @date      2015-01-22
   * @author       angel_in_us
   */
  function get_broker_site($where = array(), $where_in = array(), $like = array(), $offset = 0, $limit = 10, $database = 'db_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site_broker, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }


  /**
   * 修改待网站是否启用
   * @date      2015-01-22
   * @author       angel_in_us
   */
  function delete_site_usage($where = array())
  {
    $result = $this->del($where, 'db_city', $this->mass_site_broker);
    return $result;
  }


  /**
   * 经纪人启用对应网站端口
   * @date      2015-01-22
   * @author       angel_in_us
   */
  function add_broker_interface($data = array())
  {
    $result = $this->add_data($data, 'db_city', $this->mass_site_broker);
    return $result;
  }


  /**
   * 根据id查询群发网站信息
   * @date      2015-01-25
   * @author       angel_in_us
   */
  function get_site_byid($where = array(), $database = 'db_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site, 'where' => $where), $database);
    return $result;
  }


  /**
   * 根据id查询群发网站信息
   * @date      2015-01-26
   * @author       angel_in_us
   */
  function get_brokerinfo_byids($where = array(), $database = 'db_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site_broker, 'where' => $where), $database);
    return $result;
  }


  /**
   * 根据username查询经纪人是否绑定网站
   * @date      2015-01-25
   * @author       angel_in_us
   */
  function check_broker_site($where = array(), $database = 'db_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site_broker, 'where' => $where), $database);
    return $result;
  }


  /**
   * 根据 username 来更新 mass_site_broker 表中的密码
   */
  function update_broker_pwd($where = array(), $arr = array())
  {
    $result = $this->modify_data($where, $arr, 'db_city', $this->mass_site_broker);
    return $result;
  }

  /**
   * 根据 broker_id 获取绑定 群发站点
   */
  function get_mess_site($broker_id, $site = '')
  {
    $data = array();
    if ($broker_id) {
      $this->dbback_city->select($this->mass_site . ".*");
      $where = $this->mass_site_broker . ".broker_id = '$broker_id'";
      if ($site) {
        $where .= " and " . $this->mass_site . ".id in ($site,0)";
      }
      $this->dbback_city->where($where);
      $this->dbback_city->from($this->mass_site_broker);
      $this->dbback_city->join($this->mass_site, "$this->mass_site_broker.site_id =  $this->mass_site.id");
      $data = $this->dbback_city->get()->result_array();
    }
    return $data;
  }
}

/* End of file site_model.php */
/* Location: ./application/mls/models/site_model.php */
