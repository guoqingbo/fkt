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

load_m('Community_base_model');

class Community_model extends Community_base_model
{
  /**
   * 楼盘表名称
   * @var string
   */
  private $_cmt_tbl = 'community';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    parent::set_cmt_tbl('community');
    parent::set_cmt_img_tbl('cmt_img');
  }


  /**
   * 根据楼盘ID获取楼盘的上传图片按钮显示值
   * @param int $id 楼盘ID
   *
   * @return array
   */
  public function get_is_upload_pic_id_by($id)
  {
    $this->dbback_city->select('is_upload_pic');
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_cmt_tbl)->result_array();
  }
}
