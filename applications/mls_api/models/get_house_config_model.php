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

load_m('House_config_model');

class Get_house_config_model extends House_config_model
{

  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file district_model.php */
/* Location: ./applications/mls/models/district_model.php */
