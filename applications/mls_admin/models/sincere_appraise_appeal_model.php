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
 * Sincere_punish_model CLASS
 *
 * 经纪人合作申诉申诉管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Sincere_appraise_appeal_base_model");

class Sincere_appraise_appeal_model extends Sincere_appraise_appeal_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file Sincere_appraise_appeal_model.php */
/* Location: ./app/models/Sincere_appraise_appeal_model.php */
