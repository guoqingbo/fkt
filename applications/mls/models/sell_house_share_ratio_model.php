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
 * Sell_house_share_ratio_moedel CLASS
 *
 * 出售合作房源佣金比例管理类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */

//加载父类文件
load_m('House_share_ratio_base_model');

class Sell_house_share_ratio_model extends House_share_ratio_base_model
{
  //出售房源佣金比例表
  private $_sell_house_share_ratio_tbl = "sell_house_share_ratio";

  public function __construct()
  {
    parent::__construct();
    parent::set_ratio_tbl($this->_sell_house_share_ratio_tbl);
  }
}

/* End of file sell_house_share_ratio_moedel.php */
/* Location: ./application/models/sell_house_share_ratio_moedel.php */
