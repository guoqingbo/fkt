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
 * Perssion_tab_model CLASS
 *
 * 菜单生成
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Permission_tab_base_model");

class Permission_tab_model extends Permission_tab_base_model
{

  /**
   * 一级菜单表
   * @var string
   */
  private $_tbl = 'permission_tab';

  /**
   * 二级菜单表
   * @var string
   */
  private $_tbl_2 = 'permission_secondtab';

  /**
   * 全部有效菜单
   * @var array
   */
  private $_tabs = array();

  /**
   * 全部有效功能模块
   * @var array
   */
  private $_module = array();

  /**
   * 当前模块ID
   * @var int
   */
  private $_now_module_id = 0;

  /**
   * 当前一级菜单ID
   * @var int
   */
  private $_now_tab_id = 0;

  /**
   * 一级菜单数组
   * @var array
   */
  private $_tab_arr = array();

  /**
   * 二级菜单数组
   * @var array
   */
  private $_secondtab_arr = array();

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
      $this->load->model('broker_permission_model');
    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_permission_tab_base_model_';
    $this->_init_tabs();
  }

  //获取一级菜单数组
  public function get_tab_arr()
  {
    return $this->_tab_arr;
  }

  //获取二级菜单数组
  public function get_secondtab_arr()
  {
    return $this->_secondtab_arr;
  }

  //输出树形的菜单
  public function get_tree_menu($class, $method)
  {
    //当没有一级菜单和二级菜单的时候，获取相应菜单数据
    if (!is_full_array($this->_tab_arr)) {
      if (!is_full_array($this->get_tab($class, $method))) {
        $this->reset_tabs($class, $method);
      }
    }
    if (!is_full_array($this->_secondtab_arr)) {
      $this->get_secondtab($class, $method);
    }
    if (is_full_array($this->_tab_arr) && ($this->_now_module_id == 21||$this->_now_module_id == 26)) //合同管理,签约中心
    {
      $menu_html = '<ul>';
      foreach ($this->_tab_arr as $tab) {
        //判断当前菜单是否有二级菜单
        $is_exist_secondtab = $this->check_secondtab_by_tabid($tab['id']);
        $tabcm = explode("/", $tab['url']);
        $tabclass = '';
        //只有一级菜单
        if (!$is_exist_secondtab && $class == $tabcm[0] && $method == $tabcm[1]) {
          $tabclass = "class='active'";
        } //有二级菜单并且当前正好在此菜单下
        else if ($is_exist_secondtab && $this->_now_tab_id == $tab['id']) {
          $tabclass = "class='t-more'";
        } else if ($is_exist_secondtab) //有二级菜单但当前没有选中
        {
          $tabclass = "class='t-more t-more2'";
        }
        $menu_html .= '<li ' . $tabclass . '><a class="t" href="/'
          . $tab['url'] . '">' . $tab['name'] . '</a>';
        if ($this->_now_tab_id == $tab['id'] && is_full_array($this->_secondtab_arr)) {
          $menu_html .= '<div class="t-list">';
          foreach ($this->_secondtab_arr as $secondtab) {
            $secondtabclass = '';
            if ($class == $secondtab['class'] && $method == $secondtab['method']) {
              $secondtabclass = ' active';
            }
            $menu_html .= '<a class="b' . $secondtabclass . '" href="/'
              . $secondtab['class'] . '/' . $secondtab['method'] . '/">'
              . $secondtab['name'] . '</a>';
          }
          $menu_html .= '</div>';
        }
        $menu_html .= '</li>';
      }
      $menu_html .= '</ul>';
    }
    return $menu_html;
  }

  //读取全部的功能模块
  private function _init_module()
  {
    $mem_key = $this->_mem_key . '_init_module';
    //$this->mc->delete($mem_key);
    $cache = $this->mc->get($mem_key);

    $module = array();

    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $module = $cache['data'];
    } else {
      $this->load->model('permission_module_model');
      $where = array('is_display' => 1);
      $module = $this->permission_module_model->get_all_by($where);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $module), 3600);
    }

    return $module;
  }

  public function _init_tabs()
  {
    $module_arr = $this->_init_module();

    if (is_full_array($module_arr)) {
      $module = array();
      foreach ($module_arr as $key => $value) {
        $module[$value['id']] = $value;
      }
      unset($module_arr);
      //一级菜单
      $mem_key = $this->_mem_key . '_init_tabs';
      //$this->mc->delete($mem_key);
      $cache = $this->mc->get($mem_key);

      if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
        $tab_arr = $cache['data'];
      } else {
        $where = array('is_display' => 1);
        $tab_arr = $this->get_all_tab($where);

        $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $tab_arr), 3600);
      }

      //二级菜单
      $mem_key = $this->_mem_key . '_init_secondtabs';
      // $this->mc->delete($mem_key);
      $cache = $this->mc->get($mem_key);

      if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
        $secondtab_arr = $cache['data'];
      } else {
        $where = array('is_display' => 1);
        $secondtab_arr = $this->get_all_secondtab($where);

        $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $secondtab_arr), 3600);
      }
      $this->_module = array();
      $this->_tabs = array();
      $this->_secondtabs = array();
      if (is_full_array($secondtab_arr)) {
        $secondtab_temp_arr = array();

        $this->load->model('broker_model');
        $broker_data = $this->broker_model->get_user_session();

        if (is_full_array($broker_data)) {
          //读取经纪人认证信息
          $group_id = $broker_data['group_id'];

            //检查门店是否参加区域公盘
//            $this->load->model('cooperate_district_model');
//            $this->cooperate_district_model->set_tbl("cooperate_district_join");//选择表
//            $conf_where = "agency_id = {$broker_data['agency_id']} and status = 1";
//            $is_join_district = $this->cooperate_district_model->get_all_by($conf_where);
            $is_join_district = 1;


            if (!empty($broker_data['broker_id'])) {
            $this->broker_permission_model->set_broker_id($broker_data['broker_id'], $broker_data['company_id']);
          }
          //有权限的二级菜单
          foreach ($secondtab_arr as $secondtab) {
            $permision = $this->broker_permission_model->check($secondtab['pid']);
            //检查该菜单是否有权限
            if ($secondtab['pid'] > 0 && is_full_array($permision) && TRUE == $permision['auth']) {
              $secondtab_temp_arr[$secondtab['tab_id']][] = $secondtab;
            } //无权限时检查是否需要认证
            else if ($secondtab['pid'] == 0) {
              $secondtab_temp_arr[$secondtab['tab_id']][] = $secondtab;
            }
          }
          $this->_secondtabs = $secondtab_temp_arr;
          //一级菜单

          foreach ($tab_arr as $tab) {
            $permision = $this->broker_permission_model->check($tab['pid']);
            if (isset($secondtab_temp_arr[$tab['id']]))//取第一个取代原有的地址
            {
              $tab['url'] = $secondtab_temp_arr[$tab['id']][0]['class'] . '/'
                . $secondtab_temp_arr[$tab['id']][0]['method'];
            }
            //检查该菜单是否有权限
            if ($tab['pid'] > 0 && is_full_array($permision) && TRUE == $permision['auth']) {
              if (0 == $tab['check_group']) {
                $tab_temp_arr[$tab['module_id']][] = $tab;
              } else if (1 == $tab['check_group'] && $group_id != 1) {
                $tab_temp_arr[$tab['module_id']][] = $tab;
              } else if (2 == $tab['check_group'] && $is_join_district) {
                  $tab_temp_arr[$tab['module_id']][] = $tab;
              }
            } //无权限时检查是否需要认证
            else if ($tab['pid'] == 0) {
              if (0 == $tab['check_group']) {
                $tab_temp_arr[$tab['module_id']][] = $tab;
              } else if (1 == $tab['check_group'] && $group_id != 1) {
                  $tab_temp_arr[$tab['module_id']][] = $tab;
              } else if (2 == $tab['check_group'] && $is_join_district) {
                  $tab_temp_arr[$tab['module_id']][] = $tab;
              }
            }
          }
            if (in_array($broker_data['role_level'], array(10, 11)) && isset($tab_temp_arr[17])) {//正式经纪人和见习经纪人去掉管理功能模块
                unset($tab_temp_arr[17]);
            }
          if (is_full_array($tab_temp_arr)) {
            foreach ($tab_temp_arr as $tab_key => $tab_temp) {
              foreach ($tab_temp as $tabval) {
                $tabarr = array();
                if (isset($module[$tabval['module_id']])) {
                  if (!isset($this->_module[$tabval['module_id']])) {
                    $this->_module[$tabval['module_id']] = array(
                      'id' => $module[$tabval['module_id']]['id'],
                      'name' => $module[$tabval['module_id']]['name'],
                      'url' => $tabval['url'],
                      'style' => $module[$tabval['module_id']]['style'],
                      'order' => $module[$tabval['module_id']]['order']
                    );
                  }

                  $tabarr = array(
                    'id' => $tabval['id'],
                    'name' => $tabval['name'],
                    'url' => $tabval['url'],
                    'icon' => $tabval['icon']
                  );

                  $this->_tabs[$tabval['module_id']][$tabval['id']] = $tabarr;
                }
              }
            }

            if (is_full_array($this->_module)) {
              $this->_module = array_values($this->_module);
              $count = count($this->_module);
              for ($i = 0; $i < $count; ++$i) {
                for ($j = $count - 1; $j > $i; --$j) {
                  if ($this->_module[$j]['order'] > $this->_module[$j - 1]['order']) {
                    $tmp = $this->_module[$j];
                    $this->_module[$j] = $this->_module[$j - 1];
                    $this->_module[$j - 1] = $tmp;
                  }
                }
              }
            }
          }
        }
      }
    }
  }


  //获取tab
  public function get_tab($class, $method)
  {
    $nowtabarr = array();
    if (is_full_array($this->_tabs)) {
      foreach ($this->_tabs as $module_id => $tabarr) {
        foreach ($tabarr as $tabid => $tab) {
          $tabcm = explode("/", $tab['url']);

          if ($tabcm[0] == $class && $tabcm[1] == $method) {
            $this->_now_tab_id = $tabid;
            $this->_now_module_id = $module_id;
            $nowtabarr = $tabarr;
            $nowtabarr[$tabid]['selected'] = TRUE;
          }
        }
      }
    }

    //菜单列表
    $menu_str = '';
    if (is_full_array($nowtabarr)) {
      foreach ($nowtabarr as $v) {
        //暂时产品测试用
//         	    if(   in_array($v['url'], array('group_site_deal/publish_logs_publish','group_site_deal/queue_sell','group_site_deal/refresh_manage'))
//         	            &&  isset($this->broker_info['phone'])
//         	            &&  false == in_array($this->broker_info['phone'],array('15950534079','13605183679','13677777777'))
//         	       ){ continue; }

        $link = isset($v['selected']) && $v['selected'] ? 'link link_on' : 'link';
        $menu_str .= '<a href="/' . $v['url'] . '" class="' . $link
          . '"><span class="iconfont">' . $v['icon'] . '</span>'
          . $v['name'] . '</a>';
      }
    }
    $this->_tab_arr = $nowtabarr;
    return $menu_str;
  }

  //获取功能模块
  public function get_module()
  {
    return $this->_module;
  }

  //获取2级菜单
  public function get_secondtab($class, $method)
  {
    //菜单功能列表
    $secondtab_str = '';
    if ($this->_now_tab_id > 0
      && is_full_array($this->_secondtabs[$this->_now_tab_id])
    ) {
      $secondtab_arr = $this->_secondtabs[$this->_now_tab_id];
      foreach ($secondtab_arr as $v) {
        $link = $v['class'] == $class && $v['method'] == $method ? 'link link_on' : 'link';
        $secondtab_str .= '<a href="/' . $v['class'] . '/' . $v['method']
          . '/" class="' . $link
          . '"><span class="iconfont hide">&#xe607;</span>'
          . $v['name'] . '</a>';
      }
    }
    $this->_secondtab_arr = $secondtab_arr;
    return $secondtab_str;
  }

  /**
   * 获取全部一级菜单
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_tab($where = '', $start = -1, $limit = 20,
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
   * 获取全部一级菜单
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_secondtab($where = '', $start = -1, $limit = 20,
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
    return $this->dbback_city->get($this->_tbl_2)->result_array();
  }

  /**
   * 重置菜单
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function reset_tabs($class, $method)
  {
    $mem_key = $this->_mem_key . 'reset_tabs_' . $class . '_' . $method;
    //$this->mc->delete($mem_key);
    $cache = $this->mc->get($mem_key);
    if ($cache['is_ok'] == 1) {
      $tab = $cache['data'];
    } else {
      $where = array('class' => $class, 'method' => $method, 'is_display' => 1);
      $this->dbback_city->select(array('tab_id'));
      $this->dbback_city->where($where);
      $tab = $this->dbback_city->get($this->_tbl_2)->row_array();

      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $tab), 3600);
    }

    $this->_now_tab_id = is_full_array($tab) ? $tab['tab_id'] : 0;
    return $this->_get_tab_by_tabid();
  }

  /**
   * 根据菜单ID获取菜单
   * @return str 菜单HTML
   */
  private function _get_tab_by_tabid()
  {
    if ($this->_now_tab_id > 0) {
      $mem_key = $this->_mem_key . '_get_tab_by_tabid_' . $this->_now_tab_id;//$this->mc->delete($mem_key);
      $cache = $this->mc->get($mem_key);
      if ($cache['is_ok'] == 1) {
        $tabcm = $cache['data'];
      } else {
        if (isset($this->_secondtabs[$this->_now_tab_id]))//取第一个取代原有的地址
        {
          //默认取第一个
          $class = $this->_secondtabs[$this->_now_tab_id][0]['class'];
          $method = $this->_secondtabs[$this->_now_tab_id][0]['method'];
        } else {
          $where = array('id' => $this->_now_tab_id);
          $this->dbback_city->select(array('url'));
          $this->dbback_city->where($where);
          $tab = $this->dbback_city->get($this->_tbl)->row_array();

          $tabcm = is_full_array($tab) ? explode("/", $tab['url']) : array();

          $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $tabcm), 3600);
          $class = $tabcm[0];
          $method = $tabcm[1];
        }
      }
      return $this->get_tab($class, $method);
    } else {
      return '';
    }
  }

  //检测是否有二级菜单
  public function check_secondtab_by_tabid($tab_id)
  {
    $mem_key = $this->_mem_key . '_check_secondtab_by_tabid_' . $tab_id;//$this->mc->delete($mem_key);
    $cache = $this->mc->get($mem_key);
    if ($cache['is_ok'] == 1) {
      $is_exist_secondtab = $cache['data'];
    } else {
      $where = array('tab_id' => $tab_id);
      $this->dbback_city->select(array('id'));
      $this->dbback_city->where($where);
      $tab = $this->dbback_city->get($this->_tbl_2)->row_array();
      $is_exist_secondtab = is_full_array($tab) ? 1 : 0;
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $is_exist_secondtab), 3600);
    }
    return $is_exist_secondtab;
  }
}

/* End of file permission_tab_model.php */
/* Location: ./app/models/permission_tab_model.php */
