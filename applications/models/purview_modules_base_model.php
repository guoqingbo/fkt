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
 * Permission_modules_base_model CLASS
 *
 * 权限模块添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Purview_modules_base_model extends MY_Model
{

  /**
   * 权限模块表
   * @var string
   */
  private $_tbl = 'purview_modules';
  private $_tbl_list = 'purview_list';

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = '';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_purview_modules_base_model_';
  }

  //目前只有合同管理有节点配置，其它模块是没有的
  public function get_all_tab()
  {
    $tabs = array(
      array('id' => 1, 'name' => '诚意金', 'mid' => 6),
      array('id' => 2, 'name' => '合同报备', 'mid' => 6),
      array('id' => 3, 'name' => '交易合同', 'mid' => 6),
      array('id' => 4, 'name' => '托管合同', 'mid' => 6),
    );
    return $tabs;
  }

  //通过module_id获取所有tab
  public function get_tabs_by_module_id($module_id)
  {
    $tabs = $this->get_all_tab();
    foreach ($tabs as $value) {
      if ($value['mid'] == $module_id) {
        $result[] = $value;
      }
    }
    return $result;
  }

  //获取一条tab
  public function get_tab_id($tab_id)
  {
    $tabs = $this->get_all_tab();
    foreach ($tabs as $value) {
      if ($value['id'] == $tab_id) {
        return $value;
      }
    }
    return array();
  }

  //目前只有合同管理有节点配置，其它模块是没有的
  public function get_all_sencondtab()
  {
    $secondtabs = array(
      array('id' => 1, 'name' => '交易合同', 'tab_id' => 3),
      array('id' => 2, 'name' => '业务分成', 'tab_id' => 3),
      array('id' => 3, 'name' => '应收应付', 'tab_id' => 3),
      array('id' => 4, 'name' => '实收实付', 'tab_id' => 3),
      array('id' => 5, 'name' => '权证流程', 'tab_id' => 3),
      array('id' => 6, 'name' => '托管合同', 'tab_id' => 4),
      array('id' => 7, 'name' => '付款业主', 'tab_id' => 4),
      array('id' => 8, 'name' => '管家费用', 'tab_id' => 4),
      array('id' => 9, 'name' => '出租合同', 'tab_id' => 4),
      array('id' => 10, 'name' => '收款客户', 'tab_id' => 4),
    );
    return $secondtabs;
  }

  //通过module_id获取所有tab
  public function get_secondtabs_by_tab_id($tab_id)
  {
    $secondtabs = $this->get_all_sencondtab();
    foreach ($secondtabs as $value) {
      if ($value['tab_id'] == $tab_id) {
        $result[] = $value;
      }
    }
    return $result;
  }

  //获取一条secondtab
  public function get_secondtab_id($secondtab_id)
  {
    $tabs = $this->get_all_sencondtab();
    foreach ($tabs as $value) {
      if ($value['id'] == $secondtab_id) {
        return $value;
      }
    }
    return array();
  }

  /**
   * 设置需要查询的字段
   * @param array $select_fields
   */
  public function set_select_fields($select_fields)
  {
    $select_fields_str = '';
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
    }
    $this->_select_fields = $select_fields_str;
  }

  /**
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->_select_fields;
  }

  /**
   * 获取所有的模块，用于缓存
   * @return array
   */
  public function get_all()
  {
    $mem_key = $this->_mem_key . 'get_all';//$this->mc->delete($mem_key);
    $cache = $this->mc->get($mem_key);
    $whereAll = " l.mid = m.id order by l.mid ASC";
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $modules = $cache['data'];
    } else {
      $modules = $this->get_all_by($whereAll);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $modules), 86400);
    }
    $modules = $this->get_all_by($whereAll);
    return $modules;
  }

  /**
   * 通过是否需要权限字段来获取所有模块
   * @param int $init_auth 是否需要权限判断 1 需要权限 0 无需权限
   * @return array
   */
  public function get_all_by_init_auth($init_auth = 1)
  {
    $modules = $this->get_all();
    $new_modules = array();
    if (is_full_array($modules)) {
      foreach ($modules as &$v) {
        if ($v['init_auth'] == 1) {
          $new_modules[] = $v;
        }
      }
    }
    return $new_modules;
  }


  /**
   * 通过模块编号获取记录
   * @param int $id 模块编号
   * @return array 记录组成的一维数组
   */
  public function get_by_id($id)
  {
    $modules = $this->get_all();
    $new_modules = array();
    if (is_full_array($modules)) {
      foreach ($modules as &$v) {
        if ($v['pid'] == $id) {
          $new_modules = $v;
          break;
        }
      }
    }
    return $new_modules;
  }

  public function get_all_by_id($where = '', $start = -1, $limit = 20,
                                $order_key = 'order', $order_by = 'desc')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tbl_list);
  }

  /**
   * 管理后台获取权限模块列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where = '', $start = -1, $limit = 20)
  {
    //排序条件
    if ($start >= 0 && $limit > 0) {
      $where = $where . " limit " . $start . "," . $limit;
    }
    $sql = "select pid,name,pname,is_this_user_hold,l.status as status,l.mid,l.tab_id,l.secondtab_id from purview_list l LEFT JOIN purview_modules m on " . $where;
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;

  }

  /**
   * 获取权限模块的所有内容
   *
   */
  public function get_all_by_modules()
  {
    $sql = "select * from purview_modules where status = 1 ";
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  public function get_modules_by($id)
  {
    $sql = "select * from purview_modules where status=1 and id = '" . $id . "'";
    $result = $this->dbback_city->query($sql)->row_array();
    return $result;
  }

  /**
   * 插入权限模块数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的权限组id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl_list, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl_list, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 更新权限模块数据
   * @param array $update_data 更新的数据源数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('pid', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl_list, $update_data);
    } else {
      $this->db_city->update($this->_tbl_list, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 删除权限模块数据
   * @param int $id 编号
   * @return boolean true 成功 false 失败
   */
  public function delete_by_id($id)
  {
    //多条删除
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    if ($ids) {
      $this->db_city->where_in('pid', $ids);
      $this->db_city->delete($this->_tbl_list);
    }
    if ($this->db_city->affected_rows() > 0) {
      return true;
    } else {
      return false;
    }
  }

  //清空数据库
  public function truncate()
  {
    $this->db_city->from($this->_tbl);
    $this->db_city->truncate();
  }
}

/* End of file purview_modules_base_model.php */
/* Location: ./applications/models/purview_modules_base_model.php */
