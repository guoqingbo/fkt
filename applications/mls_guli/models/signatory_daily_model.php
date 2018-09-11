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
 * signatory_daily_model CLASS
 *
 * 经纪人工作日报
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */

//加载父类文件
load_m('signatory_daily_base_model');

class signatory_daily_model extends signatory_daily_base_model
{


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file signatory_daily_model.php */
/* Location: ./applications/mls_guli/models/signatory_daily_model.php */
