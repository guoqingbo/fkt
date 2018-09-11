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
 * @copyright       Copyright (c) 2006 - 2012
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
   * 城市主库
   *
   * @access protected
   * @var Object
   */
  protected $db_city = NULL;


  /**
   * 公共从库
   *
   * @access protected
   * @var Object
   */
  protected $dbback = NULL;


  /**
   * 城市从库
   *
   * @access protected
   * @var Object
   */
  protected $dbback_city = NULL;

  /**
   * 数据库别名
   *
   * @access protected
   * @var Object
   */
  protected $_db = NULL;


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    /***连接数据库****/

    //公共主库
    $this->db = $this->load->database('db', TRUE);

    //公共从库
    $this->dbback = $this->load->database('dbback', TRUE);


    if (!empty($_SESSION[WEB_AUTH]["city"])) {
      $this->config->set_item('login_city', $_SESSION[WEB_AUTH]["city"]);
      //城市主库
      $this->db_city = $this->load->database('db_' . $_SESSION[WEB_AUTH]["city"], TRUE);

      //城市从库
      $this->dbback_city = $this->load->database('dbback_' . $_SESSION[WEB_AUTH]["city"], TRUE);
    }

  }


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
    $query = $this->_db->get($this->form_name);
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
      $this->_db->select($data['select_str']);
    }

    if (isset($data['where']) AND !empty($data['where'])) {
      $this->_db->where($data['where']);
    }

    if (isset($data['like']) AND !empty($data['like'])) {
      $this->_db->like($data['like'], 'both');
    }

    if (isset($data['or_like']) AND !empty($data['or_like'])) {
      $this->_db->or_like($data['or_like'], 'after');
    }

    if (is_full_array($data['or_like_arr'])) {
      foreach ($data['or_like_arr'] as $key => $value) {
        if (is_full_array($value)) {
          $this->_db->or_like($value, 'both');
        }
      }
    }

    if (isset($data['or_where']) AND !empty($data['or_where'])) {
      $this->_db->or_where($data['or_where']);
    }

    if (isset($data['where_in']) AND !empty($data['where_in'])) {
      $this->_db->where_in($data['where_in'][0], $data['where_in'][1]);
    }

    if (isset($data['order_by']) AND !empty($data['order_by'])) {
      if (is_array($data['order_by'])) {
        $this->_db->order_by($data['order_by'][0], $data['order_by'][1]);
      } else {
        $this->_db->order_by($data['order_by'], 'desc');
      }
    }

    if (isset($data['group_by']) AND !empty($data['group_by'])) {
      $this->_db->group_by($data['group_by']);
    }

    if (isset($data['offset']) && strlen($data['offset']) > 0
      && is_numeric($data['offset'])
    ) {
      if ($data['offset'] > 0) {
        if (strlen($data['limit']) && is_numeric($data['limit'])) {
          ($data['limit'] > 0) ? $this->_db->limit($data['offset'], $data['limit']) : $this->_db->limit($data['offset']);
        } else {
          $this->_db->limit($data['offset']);
        }
      } else {
        if (strlen($data['limit']) && is_numeric($data['limit'])) {
          if ($data['limit'] > 0)
            $this->_db->limit($data['limit']);
        }
      }
    } else if (isset($data['limit']) && strlen($data['limit']) > 0 && is_numeric($data['limit'])) {
      if ($data['limit'] > 0) {
        $this->_db->limit($data['limit']);
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
  public function add_data($data, $database = 'db', $form_name = '')
  {
    $this->dbselect($database);
    $this->set_table($form_name);
    if (empty($data) || !is_array($data)) {
      $result = 0;
    } else {
      $this->_db->insert($this->form_name, $data);//插入数据
      if (($this->_db->affected_rows()) >= 1) {
        $result = $this->_db->insert_id();//如果插入成功，则返回插入的id
      } else {
        $result = 0;    //如果插入失败,返回0
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
      $this->_db->where($arr);
      if ($return = $this->_db->update($this->form_name, $data)) {
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
      $this->_db->where($arr);
      $this->_db->delete($this->form_name);

      if (($this->_db->affected_rows()) >= 1) {
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
        $this->_db = $this->db;
        break;

      case 'db_city':
        $this->_db = $this->db_city;
        break;

      case 'dbback':
        $this->_db = $this->dbback;
        break;

      case 'dbback_city':
        $this->_db = $this->dbback_city;
        break;
    }
  }

}

/* End of file MY_Model.php */
/* Location: ./applications/core/MY_Model.php */
