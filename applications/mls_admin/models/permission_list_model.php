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
 * Permission_func_model CLASS
 *
 * 权限功能添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Permission_list_base_model");

class Permission_list_model extends Permission_list_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();

  }
}
/*public function get_list_by($where, $start = -1, $limit = 20,
        $order_key = 'id' , $order_by = 'ASC')
{
    //查询字段
    $this->dbback_city->select("{$this->_tbl1}.*,{$this->_tbl2}.name AS menu_name,{$this->_tbl3}.name AS module_name");

    if ($where)
    {
        //查询条件
        $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl1);
    $this->dbback_city->join($this->_tbl2, "{$this->_tbl1}.menu_id = {$this->_tbl2}.id");
    $this->dbback_city->join($this->_tbl3, "{$this->_tbl2}.module_id = {$this->_tbl3}.id");

    //排序条件
    $this->dbback_city->order_by($this->_tbl1.'.'.$order_key, $order_by);
    if ($start >= 0 && $limit > 0 )
    {
        $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
}
}*/

/* End of file permission_func_model.php */
/* Location: ./app/models/permission_func_model.php */
