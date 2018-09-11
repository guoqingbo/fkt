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

class fang100_model extends MY_Model
{

  /**
   * 类初始化
   */

  public function __construct()
  {
    parent::__construct();
  }

  //表参数
  protected $_tbl = "";

  //设置表方法
  public function set_tbl($tbl)
  {
    $this->_tbl = $tbl;
  }


  //获得所有委托满30天且未下架的委托出售信息
  public function get_all_notdel_list()
  {
    $time = strtotime(date('Y-m-d', time())) - 3600 * 24 * 30;
    $this->dbback_city->where('ctime >', $time);
    $this->dbback_city->where('status', 1);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  public function update_del($id, $update_array)
  {
    $this->db_city->where('id', $id);
    $this->db_city->update($this->_tbl, $update_array);
    return $this->db_city->affected_rows();
  }

  public function update_house($id)
  {
    $this->db_city->where('id', $id);
    $info = $this->db_city->get('sell_house_copy')->row_array();
    $sql = "update sell_house set createtime = " . $info['createtime'] . " where id = {$info['id']}";
    echo $sql;
    $this->db_city->query($sql);
    echo $this->db_city->affected_rows();
  }

  public function get_excel_house($id)
  {
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get('sell_house1')->row_array();
  }
}

/* End of file City_model.php */
/* Location: ./app/models/City_model.php */
