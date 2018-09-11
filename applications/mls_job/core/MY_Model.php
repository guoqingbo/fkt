<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS房源管理系统
 *
 * 基于Codeigniter的经纪人房源管理系统
 *
 * MLS房源管理系统是服务于房产经纪人的后台房源管理系统
 *
 *
 * @package         ZSB
 * @author          xz
 * @copyright       Copyright (c) 2006 - 2012, HOUSE365.com.
 * @link            http://nj.zsb.house365.com/
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
   * 公共主库
   *
   * @access protected
   * @var Object
   */
  protected $db = NULL;

  /**
   * 公共从库
   *
   * @access protected
   * @var Object
   */
  protected $dbback = NULL;

  /**
   * MLS主库
   *
   * @access protected
   * @var Object
   */
  protected $db_mls = NULL;

  /**
   * MLS从库
   *
   * @access protected
   * @var Object
   */
  protected $dbback_mls = NULL;


  /**
   * 城市主库
   *
   * @access protected
   * @var Object
   */
  protected $db_city = NULL;

  /**
   * jjr主库
   *
   * @access protected
   * @var Object
   */
  protected $db_jjr = NULL;

  /**
   * 城市从库
   *
   * @access protected
   * @var Object
   */
  protected $dbback_city = NULL;

  /**
   * jjr从库
   *
   * @access protected
   * @var Object
   */
  protected $dbback_jjr = NULL;

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    /***连接数据库****/

    //加载db
    $this->load->library('My_DB', '', 'mydb');
    $this->db = $this->db_mls = $this->mydb->get_db_obj('db');

    //加载dbback
    $this->load->library('My_DB', '', 'mydbback');
    $this->dbback = $this->dbback_mls = $this->mydbback->get_db_obj('dbback');

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
    $login_city = $this->config->item('login_city');
    $this->set_city_db($login_city);
    //  $this->set_jjr_db();
  }

  /**
   * 根据城市缩写指定访问的城市数据库
   * @param type $city_spell
   */
  public function set_city_db($city_spell = '')
  {
    $city_spell = trim($city_spell);
    if (isset($city_spell) && $city_spell <> '') {
      //加载db
      $this->load->library('My_DB', '', 'mydb_city');
      $this->db_city = $this->mydb_city->get_db_obj('db_' . $city_spell);
      //加载dbback
      $this->load->library('My_DB', '', 'mydbback_city');
      $this->dbback_city = $this->mydbback_city->get_db_obj('dbback_' . $city_spell);
    }
  }

  /**
   * 根据城市缩写指定访问的城市数据库
   * @param type $city_spell
   */
  /*
  public function set_jjr_db()
  {
    //加载db_jjr
    $this->load->library('My_DB', '', 'mydb_jjr');
    $this->db_jjr = $this->mydb_jjr->get_db_obj('db_jjr');
    //加载dbback_jjr
    $this->load->library('My_DB', '', 'mydbback_jjr');
    $this->dbback_jjr = $this->mydbback_jjr->get_db_obj('dbback_jjr');//print_r($this->dbback_jjr);die();
  }
  */

  /**
   * @param Str $form_name 设置表名
   * @return true
   */
  public function set_table($form_name)
  {
    $this->form_name = $form_name;
  }


  /**
   *
   * @return Array
   */
  public function findAll()
  {
    $list = array();
    $query = $this->db->get($this->form_name);
    $result = $query->result_array();
    foreach ($result as $key1 => $val1) {
      foreach ($val1 as $key2 => $val2) {
        $row[$key2] = stripslashes($val2);
      }

      $list[] = $row;
      $row = array();
    }

    return $list;
  }


  /**
   * @param Array ('form_name' = 表单名,'where'=where条件格式 array('id'=>$id, 'status'=>1);
   *根据条件查询数据
   *
   * @return Array
   */
  public function get_data($data = array(), $database = 'db')
  {
    $this->dbselect($database);
    if (!empty($data['form_name']) AND isset($data['form_name'])) {
      $this->form_name = $data['form_name'];
    }

    if (isset($data['select']) AND !empty($data['select'])) {
      $data['select_str'] = implode($data['select'], ',');
      $this->db->select($data['select_str']);
    }

    if (isset($data['where']) AND !empty($data['where'])) {
      $this->db->where($data['where']);
    }

    if (isset($data['like']) AND !empty($data['like'])) {
      $this->db->like($data['like'], 'both');
    }

    if (isset($data['or_like']) AND !empty($data['or_like'])) {
      if (isset($data['or_like']['like_key']) AND !empty($data['or_like']['like_key'])) {//多个or条件
        foreach ($data['or_like']['like_key'] as $v) {
          $this->db->or_like(array($v => $data['or_like']['like_value']));
        }
        //特殊干预=>多个or_like加括号
        if (is_full_array($this->db->ar_like)) {
          $this->db->ar_like = array(sprintf("( %s )", implode(' ', $this->db->ar_like)));
        }
      } else {//单个or条件
        $this->db->or_like($data['or_like']);
      }
    }

    if (isset($data['or_where']) AND !empty($data['or_where'])) {
      $this->db->or_where($data['or_where']);
    }

    if (isset($data['where_in']) AND !empty($data['where_in'])) {
      $this->db->where_in($data['where_in'][0], $data['where_in'][1]);
    }

    if (isset($data['order_by']) AND !empty($data['order_by'])) {
      $this->db->order_by($data['order_by'], 'desc');
    }

    if (isset($data['order_by_array']) AND !empty($data['order_by_array'])) {
      if ($data['order_by_array'][1] == 'asc') {
        $this->db->order_by($data['order_by_array'][0], 'asc');
      } else {
        $this->db->order_by($data['order_by_array'][0], 'desc');
      }
    }

    if (isset($data['group_by']) AND !empty($data['group_by'])) {
      $this->db->group_by($data['group_by']);
    }

    if (isset($data['offset']) && strlen($data['offset']) > 0
      && is_numeric($data['offset'])
    ) {
      if ($data['offset'] > 0) {
        if (strlen($data['limit']) && is_numeric($data['limit'])) {
          ($data['limit'] > 0) ? $this->db->limit($data['offset'], $data['limit']) : $this->db->limit($data['offset']);
        } else {
          $this->db->limit($data['offset']);
        }
      } else {
        if (strlen($data['limit']) && is_numeric($data['limit'])) {
          if ($data['limit'] > 0)
            $this->db->limit($data['limit']);
        }
      }
    } else if (isset($data['limit']) && strlen($data['limit']) > 0 && is_numeric($data['limit'])) {
      if ($data['limit'] > 0) {
        $this->db->limit($data['limit']);
      }
    }
    $result = $this->findAll();

    return $result;
  }

  /**
   *添加数据
   * @param Array $data
   * @return insert_id or 0
   */
  public function add_data($data, $database = 'db', $form_name = '', $replace = 0)
  {
    $this->dbselect($database);
    $this->set_table($form_name);
    if (empty($data) || !is_array($data)) {
      $result = 0;
    } else {
      if ($replace == 0) {
        $this->db->insert($this->form_name, $data);//插入数据
        if (($this->db->affected_rows()) >= 1) {
          $result = $this->db->insert_id();//如果插入成功，则返回插入的id
        } else {
          $result = 0;    //如果插入失败,返回0
        }
      } else {
        $this->db->replace($this->form_name, $data);//插入数据
      }
    }

    return $result;
  }

  /**
   * 修改
   *
   * @param int $id
   * @return 0 or 1
   */
  public function modify_data($arr, $data, $database = 'db', $form_name = '')
  {
    $this->dbselect($database);
    $this->set_table($form_name);
    if (!empty($data) && is_array($data) && !empty($arr) && is_array($arr)) {
      $this->db->where($arr);
      if ($return = $this->db->update($this->form_name, $data)) {
        $result = 1; //更新成功，返回1
      } else {
        $result = 0; //失败，返回0
      }
    } else {
      $result = 0;
    }

    return $result;
  }


  /**
   * 删除
   *
   * @param int $id
   * @return 0 or 1
   */
  public function del($arr = array(), $database = 'db', $form_name = '')
  {
    $this->dbselect($database);
    $this->set_table($form_name);
    if (!empty($arr) && is_array($arr)) {
      $this->db->where($arr);
      $this->db->delete($this->form_name);

      if (($this->db->affected_rows()) >= 1) {
        $result = 1;      //如果删除成功，则返回1
      } else {
        $result = 0;    //如果删除失败，返回0
      }
    } else {
      $result = 0;
    }

    return $result;
  }

  function dbselect($con)
  {
    switch ($con) {
      case 'db':
        $this->db = $this->db_mls;
        break;

      case 'db_city':
        $this->db = $this->db_city;
        break;

      case 'dbback':
        $this->db = $this->dbback_mls;
        break;

      case 'dbback_city':
        $this->db = $this->dbback_city;
        break;
      /*
    case 'db_jjr':
      $this->db = $this->db_jjr;
      break;

    case 'dbback_jjr':
      $this->db = $this->dbback_jjr;
      break;
      */
    }
  }

  public function query($sql)
  {
    return $this->db->query($sql)->result_array();
  }

  public function execute($sql)
  {
    return $this->db->query($sql);
  }
}

/* End of file MY_Model.php */
/* Location: ./applications/mls/core/MY_Model.php */
