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
 * entrust_model CLASS
 *
 * 委托房源接口业务逻辑
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
load_m("entrust_base_model");

class Entrust_model extends Entrust_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 增加委托房源记录
   */
  public function add_entrust_house($fid)
  {
    $insert_arr = array(
      'houseid' => $fid,
      'state' => 1,
      'dateline' => time()
    );
    return $this->add_data($insert_arr, 'db_city', $this->entrust_house_tbl);
  }

  /**
   * 停止委托房源
   */
  public function stop_entrust_house($fid)
  {
    $data_arr = array('state' => 2);
    $where_arr = array('houseid' => $fid);

    return $this->modify_data($where_arr, $data_arr, 'db_city', $this->entrust_house_tbl);
  }
}

/* End of file entrust_model.php */
/* Location: ./app/models/entrust_model.php */
