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
class Community_model extends MY_Model
{

  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';

  /**
   * 楼盘表名称
   * @var string
   */
  private $_cmt_tbl = 'community';

  public function __construct()
  {
    parent::__construct();
    $this->mls_community = 'community';
  }

  /**
   * 筛选楼盘
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  public function getcommunity($where = array(), $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->mls_community, 'where' => $where, 'select' => array('id', 'b_map_x', 'b_map_y')), $database);
    return $comm;
  }


  /**
   * 筛选楼盘
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  /*
    public function getloupan($where = array(), $database = 'dbback_jjr')
    {
        $comm = $this->get_data(array('form_name' => 'keeper_loupan', 'where' => $where, 'select' => array('lp_id','city','lng', 'lat')), $database);
        return $comm;
    }
    */


}

/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
