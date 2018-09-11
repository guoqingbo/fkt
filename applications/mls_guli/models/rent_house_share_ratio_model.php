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
 * rent_house_share_ratio_model CLASS
 *
 * 出租合作房源佣金比例管理类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */

//加载父类文件
load_m('House_share_ratio_base_model');

class Rent_house_share_ratio_model extends House_share_ratio_base_model
{

  //出租房源佣金比例表
  private $_rent_house_share_ratio_tbl = "rent_house_share_ratio";

  public function __construct()
  {
    parent::__construct();
    parent::set_ratio_tbl($this->_rent_house_share_ratio_tbl);
  }
}

/* End of file rent_house_share_ratio_model.php */
/* Location: ./application/models/rent_house_share_ratio_model.php */
