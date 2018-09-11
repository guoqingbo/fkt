<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2015
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * notice_access_model CLASS
 *
 * 委托房源接口业务逻辑
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
load_m("notice_access_base_model");

class Notice_access_model extends Notice_access_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->na_city = 'na_city';
  }

  //job信息提交
  public function post_job_notice($news, $city)
  {
    //判断城市ID
    $where = array('spell' => $city);
    $result = $this->get_data(array('form_name' => $this->na_city, 'where' => $where, 'select' => array('id')), 'db');
    //信息处理入库
    if (isset($result[0]['id']) && !empty($result[0]['id'])) {
      $message = array();
      $message['department_id'] = 1;
      $message['data'] = $news;
      $message['city_id'] = $result[0]['id'];
      $message['dateline'] = time();
      $this->add_message($message);
    }
  }
}

/* End of file notice_access_model.php */
/* Location: ./app/models/notice_access_model.php */
