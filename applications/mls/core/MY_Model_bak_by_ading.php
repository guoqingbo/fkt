<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS房源管理系统
 *
 * 基于Codeigniter的经纪人房源管理系统
 *
 *  MLS房源管理系统是服务于房产经纪人的后台房源管理系统
 *
 *
 * @package         ZSB
 * @author          xz
 * @copyright       Copyright (c) 2006 - 2012.
 * @version         4.0
 */

// ------------------------------------------------------------------------

/**
 *  模型类基类
 * （Codeigniter所有的模型类都必须继承CI_Model类，但CI_Model类位于esf_system目录下，
 *  不方便修改，所以创建MY_Model，用来继承CI_Model）
 *
 * 所有的模型类都继承MY_Model， MY_Model主要实现数据库的初始化连接以及一些公用方法
 *
 * @package         admincp
 * @subpackage      core
 * @category        MY_Model
 * @author          xz
 */
class MY_Model extends CI_Model
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    /***连接数据库****/

    //加载db
    $this->load->library('My_DB', '', 'mydb');
    $this->db = $this->mydb->get_db_obj('db');

    //加载dbback
    $this->load->library('My_DB', '', 'mydbback');
    $this->dbback = $this->mydb->get_db_obj('dbback');

    //加载memcached
    $this->load->library('My_memcached', '', 'mc');

    //加载登录城市数据库
    $this->init_city_db();
  }

  /**
   * 验证登录后获取所需要访问城市数据库 init_city_db
   */
  public function init_city_db()
  {
    $user_session = $this->session->userdata(USER_SESSION_KEY);
    if (isset($user_session['id']) && intval($user_session['id']) > 0) {
      $this->set_city_db();
    }
  }

  /**
   * 根据城市缩写指定访问的城市数据库
   * @param type $city_spell
   */
  public function set_city_db($city_spell = '')
  {
    //加载db
    $this->load->library('My_DB', '', 'mydb_city');
    $this->db_city = $this->mydb_city->get_db_obj('db_city');

    //加载dbback
    $this->dbback_city = $this->mydb_city->get_db_obj('dbback_city');
  }
}

/* End of file MY_Model.php */
/* Location: ./applications/core/MY_Model.php */
