<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2015
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * notice_access_model CLASS
 *
 * 委托房源接口业务逻辑
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
load_m("notice_access_base_model");

class Notice_access_model extends Notice_access_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }


  //获取接入KEY
  public function get_access_key($department_id)
  {
    $return_arr = array();
    $this->dbback->select('access_key');
    $cond_where = "department_id = '" . $department_id . "'";
    $this->dbback->where($cond_where);
    $this->dbback->limit(1, 0);
    //查询
    $return_arr = $this->dbback->get($this->department_tbl)->row_array();

    return isset($return_arr['access_key']) ? $return_arr['access_key'] : '';
  }
}

/* End of file notice_access_model.php */
/* Location: ./app/models/notice_access_model.php */
