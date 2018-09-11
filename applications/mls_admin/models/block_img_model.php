<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
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
 * Community_model CLASS
 *
 * 楼盘数据模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class Block_img_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->block_img = 'block_img';
  }

  /**
   * 筛选图片
   */
  public function get_img($offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $img = $this->get_data(array('form_name' => $this->block_img, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $img;
  }

}

/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
