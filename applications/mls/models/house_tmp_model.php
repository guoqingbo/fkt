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
 * house_tmp_model CLASS
 *
 * 出售房源信息管理类,提供增加、修改、删除、查询 出售房源信息的方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */
load_m('house_tmp_base_model');

class House_tmp_model extends House_tmp_base_model
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 获取经纪人模板列表
   * @param $where  查询条件
   * @return mixed
   */
  public function get_tmps($where)
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('house_template');
    if ($where) {
      $this->dbback_city->where($where);
    }

    $data = $this->dbback_city->get()->result_array();
    return $data;
  }


}
