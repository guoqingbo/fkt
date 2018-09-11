<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统业务类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * Community_model MODEL CLASS
 *
 * 区属板块管理 控制器
 *
 * @package         MLS
 * @subpackage      MODEL
 * @category        MODEL
 * @author          xz
 */

load_m('Cmt_correction_base_model');

class Cmt_correction_model extends Cmt_correction_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    parent::set_cmt_tbl('cmt_correction');
    parent::set_cmt_img_tbl('cmt_img');
  }
}
