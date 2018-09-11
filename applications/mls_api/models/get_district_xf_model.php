<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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

class Get_district_xf_model extends District_base_model
{

  public function __construct()
  {
    parent::__construct();

    //设置区属表名称
    parent::set_district_tbl('district_xf');

    //设置板块表名称
    parent::set_street_tbl('street_xf');
  }
}

/* End of file district_model.php */
/* Location: ./applications/mls/models/district_model.php */
