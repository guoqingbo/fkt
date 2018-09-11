<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * district_model CLASS
 *
 * 区属数据模型类
 *
 * @package         mls
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */

load_m('District_base_model');

class District_model extends District_base_model
{

  public function __construct()
  {
    parent::__construct();

    //设置区属表名称
    parent::set_district_tbl('district');

    //设置板块表名称
    parent::set_street_tbl('street');
  }
}

/* End of file district_model.php */
/* Location: ./applications/mls_guli/models/district_model.php */
