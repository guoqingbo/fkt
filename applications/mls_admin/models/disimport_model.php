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
 * Buy_match_model CLASS
 *
 * 通过二维数组将相应的区属和板块入库
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class disimport_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 将配置一个二维数组导入到相应的区属和板块表
   */
  public function import($arr = array())
  {
    if (!empty($arr) && is_array($arr)) {
      foreach ($arr as $key => $value) {
        $paramDistrict = array('district' => $key, 'city_id' => 1);
        $result = $this->add_data($paramDistrict, 'db_city', 'district'); //区属入库
        var_dump('成功插入区属ID：' . $result);
        echo '<br>';
        if ($result) {
          foreach ($value as $k => $v) {
            $paramStreet = array('streetname' => $v, 'dist_id' => $result);
            $this->add_data($paramStreet, 'db_city', 'street'); //板块入库
          }
        }
      }
    }
  }

}


/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
