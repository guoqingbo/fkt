<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Agency_access_area_base_model CLASS
 *
 * 门店访问数据范围类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
class House_modify_history_base_model extends MY_Model
{


  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->sell_house_modify_history = 'sell_house_modify_history';
    $this->rent_house_modify_history = 'rent_house_modify_history';
  }

  function get_sell_house_modify_history($id, $database = 'dbback_city')
  {
    $where = array('house_id' => $id);
    //$result = $this->get_data(array('form_name' => //$this->sell_house_modify_history,'where'=>$where,'order_by'=>'createtime','limit'=>1),$database);

    $this->dbback_city->where($where);
    $this->dbback_city->order_by('createtime', 'DESC');
    $this->dbback_city->limit(1, 0);
    //查询
    $result = $this->dbback_city->get($this->sell_house_modify_history)->row_array();
    return $result;
  }

  function add_sell_house_modify_history($data, $database = 'db_city')
  {
    $result = $this->add_data($data, $database, $this->sell_house_modify_history);
    return $result;
  }

  function get_rent_house_modify_history($id, $database = 'dbback_city')
  {
    $where = array('house_id' => $id);
    //$result = $this->get_data(array('form_name' => $this->rent_house_modify_history,'where'=>$where,'order_by'=>'createtime','limit'=>1),$database);
    //return $result;

    $this->dbback_city->where($where);
    $this->dbback_city->order_by('createtime', 'DESC');
    $this->dbback_city->limit(1, 0);
    //查询
    $result = $this->dbback_city->get($this->rent_house_modify_history)->row_array();
    return $result;
  }

  function add_rent_house_modify_history($data, $database = 'db_city')
  {
    $result = $this->add_data($data, $database, $this->rent_house_modify_history);
    return $result;
  }
}

/* End of file agency_base_model.php */
/* Location: ./applications/models/agency_base_model.php */
