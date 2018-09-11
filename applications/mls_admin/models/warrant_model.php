<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS-admin
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * buy_customer_info_model CLASS
 *
 * 后台客源管理求租模型类
 *
 * @package         MLS-admin
 * @subpackage      Models
 * @category        Models
 * @author          kang
 */
//load_m('warrant_base_model');
class Warrant_model extends Warrant_base_model
{
  public function __construct()
  {
    //parent::__construct();
  }

  /** 获取所有流程步骤数据 */
  public function get_all_stage()
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('warrant_all_stage');

    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

}
