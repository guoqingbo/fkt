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
 * Blacklist_model CLASS
 *
 * 黑名单业务逻辑类 提供用户添加 删除 编辑黑名单
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Department_organization_base_Model");

class Organization_model extends Department_organization_base_Model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

}

/* End of file Blacklist_model_model.php */
/* Location: ./app/models/Blacklist_model.php */
