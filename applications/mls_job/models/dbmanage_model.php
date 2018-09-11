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
 * Dbmanage_model CLASS
 *
 * 数据库管理数量
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lu
 */
class Dbmanage_model extends MY_Model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function do_request($sql)
  {
    $this->db_city->query($sql);
  }
}

/* End of file stat_group_publish_model.php */
/* Location: ./applications/mls_job/models/stat_group_publish_model.php */
