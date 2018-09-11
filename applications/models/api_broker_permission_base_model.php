<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * zsb
 *
 * 业务类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * 经纪人权限API接口
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Api_broker_permission_base_model extends MY_Model
{

  /**
   * 缓存key
   * @var string
   */
  private $_mem_key = '';

  protected $user_permission = array(); //用户权限

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_api_broker_permission_base_model_';
    //初始化经纪人基本信息
    if (is_full_array($this->broker_info)) {
      //引用模块权限
      $this->load->model('permission_module_base_model');
      //引入菜单权限
      $this->load->model('permission_menu_base_model');
      //引入功能权限
      $this->load->model('permission_func_base_model');
      //初始化用户权限
      $this->_init_user_permission();
    }
  }

  //根据公司编号和权限编号获取相应的菜单和功能权限
  private function _init_user_permission()
  {
    //判断经纪人是否是联网经纪人
    if ($this->broker_info['package_id'] == 2 && $this->broker_info['area_id'] == 2) {
      $this->load->model('permission_system_role_base_model');
      $permission = $this->permission_system_role_base_model->get_by_id(2);
    } else {
      //根据公司编号和经纪人角色获取相应的权限
      $company_id = $this->broker_info['company_id'];
      $role_id = $this->broker_info['role_id'];
      $this->load->model('permission_company_role_base_model');
      $permission = $this->permission_company_role_base_model->
      get_by_company_id_role_id($company_id, $role_id);
    }
    $this->user_permission = array(
      'menu_auth' => isset($permission['menu_auth']) ?
        unserialize($permission['menu_auth'])
        : array(),
      'func_auth' => isset($permission['func_auth']) ?
        unserialize($permission['func_auth'])
        : array(),
    );
  }

  /**
   * 根据用户的公司编号和角色及权限设置，默取模块
   */
  public function get_module()
  {
    $all_module = $this->permission_module_base_model->get_all();
    //经纪人的菜单和功能权限
    $menu_auth = $this->user_permission['menu_auth'];
    $func_auth = $this->user_permission['func_auth'];
    //模块菜单
    $module_menu = array();
    $none_permission_url = '/permission/none/';
    //根据权限判断显示的菜单
    foreach ($all_module as $v_module) {
      if ($v_module['is_display'] == 0) {
        continue;
      }
      $module_id = $v_module['id'];
      //勾选过module选项
      if ($v_module['init_auth'] == 1) //需要进行权限控制
      {
        $v_module['url'] = $none_permission_url;
        //查看模块下有多少菜单 以order排序
        $menu = $this->permission_menu_base_model->get_by_module_id($module_id);
        $is_auth = 0;
        foreach ($menu as $v_m) {
          $menu_id = $v_m['id'];
          //菜单地址无需权限控制跳出下一个模块
          if ($v_m['init_auth'] == 0) {
            $v_module['url'] = $v_m['url'];
            $is_auth = 1;
            break;
          }
          //优先需要权限判断,并且设置了菜单查询
          if ($v_m['init_auth'] == 1
            && isset($menu_auth[$module_id][$menu_id])
          ) {
            //判断权限功能是否勾选此功能
            $menu_url = $menu_auth[$module_id][$menu_id];
            $menu_url_arr = explode('/', $menu_url);
            $class = !empty($menu_url_arr[0]) ? $menu_url_arr[0] : '';
            $method = !empty($menu_url_arr[1]) ? $menu_url_arr[1] : '';
            //获取此类和方法的功能ID
            $func = $this->permission_func_base_model->get_by_class_method($class, $method);
            if (is_full_array($func)) {
              if (in_array($func['id'], array_keys($func_auth))) //勾起了权限
              {
                $v_module['url'] = $v_m['url'];
                $is_auth = 1;
              } else {
                if ($func['is_menu'] == 1) //功能为菜单功能
                {
                  //查询菜单下所有功能
                  $func_menu = $this->permission_func_base_model->get_func_menu_by_menu_id($menu_id);
                  foreach ($func_menu as $v_f) {
                    if (in_array($v_f['id'], array_keys($func_auth))
                      || $v_f['init_auth'] == 0
                    ) //勾起了权限
                    {
                      $v_module['url'] = $v_f['class'] . '/' . $v_f['method'];
                      $is_auth = 1;
                      break;
                    }
                  }
                }
              }
            }
          }
          //找到地址直接进入下个模块判断
          if ($v_module['url'] != $none_permission_url) {
            break;
          }
        }
        if ($is_auth == 1) {
          $module_menu[] = $v_module;
        }
      } else if ($v_module['init_auth'] == 0) {
        $module_menu[] = $v_module;
      }
    }
    return $module_menu;
  }

  //获取某个模块下的菜单权限
  public function get_menu_permission($class, $method)
  {
    $user_menu = ''; //用户菜单
    $user_func_menu = ''; //用户功能菜单
    $menu_auth = $this->user_permission['menu_auth'];
    $func_auth = $this->user_permission['func_auth'];
    //菜单地址
    $url = $class . '/';
    $url = $method ? $url . $method . '/' : $url;
    $new_menu = array(); //菜单
    $menu_id = 0; //菜单编号
    $new_func_menu = array();//菜单功能
    $func_menu_id = 0; //菜单功能编号
    //先根据菜单地址获取菜单信息
    $menu = $this->permission_menu_base_model->get_by_url($url);
    //根据功能去查是否存在菜单功能
    $func = $this->permission_func_base_model->get_by_class_method($class, $method);
    if (!is_full_array($menu) && is_full_array($func)) {
      $menu = $this->permission_menu_base_model->get_by_id($func['menu_id']);
    }
    //查看模块是否需要做权限
    if (is_full_array($menu)) {
      $module = $this->permission_module_base_model->get_by_id($menu['module_id']);
    }
    //菜单功能
    if (is_full_array($func) && $func['is_menu'] == 1) {
      //查询菜单下所有功能
      $func_menu = $this->permission_func_base_model->get_func_menu_by_menu_id($func['menu_id']);
      $child_menu_url = false;
      foreach ($func_menu as $v_k => $v_f) {
        //当前选中哪个子菜单
        if ($v_f['class'] == $class && $v_f['method'] == $method) {
          $func_menu_id = $v_f['id'];
        }
        //需要权限判断
        if ($module['init_auth'] == 1 && $menu['init_auth'] == 1) {
          //菜单初始化的地址
          if ((in_array($v_f['id'], array_keys($func_auth))
              || $v_f['init_auth'] == 0) && !$child_menu_url
          ) //勾起了权限
          {
            $menu['url'] = $v_f['class'] . '/' . $v_f['method'] . '/';
            $child_menu_url = true;
          }
          //删除没有权限的菜单的功能
          if (!in_array($v_f['id'], array_keys($func_auth))
            && $v_f['init_auth'] == 1
          ) {
            unset($func_menu[$v_k]);
          }
        }
      }
      $new_func_menu = $func_menu; //菜单功能
    }
    //1、判断菜单下有没有功能菜单  menu_id is_menu 如果有 查找是否有功能权限
    if (is_full_array($menu)) //菜单不需要做权限控制
    {
      $module_id = $menu['module_id'];
      $menu_id = $menu['id'];
      //获取模块下所有菜单
      $module_menu = $this->permission_menu_base_model->get_by_module_id($module_id);
      //根据菜单权限筛选出合法的菜单
      foreach ($module_menu as $v) {
        if ($module['init_auth'] == 0
          || ($module['init_auth'] == 1 && $v['init_auth'] == 0)
        ) {
          $new_menu[] = $v;
          continue;
        }
        if ($v['id'] == $menu['id'])  //此菜单下的信息已经查过了
        {
          $v['url'] = $menu['url'];
        } else //判断是否为菜单功能
        {
          //看看有没有菜单功能
          $func_menu = $this->permission_func_base_model->get_func_menu_by_menu_id($v['id']);
          if (is_full_array($func_menu)) //此菜单下有菜单功能
          {
            foreach ($func_menu as $v_k => $v_f) {
              if (in_array($v_f['id'], array_keys($func_auth))
                || $v_f['init_auth'] == 0
              ) //勾起了权限
              {
                $menu = $this->permission_menu_base_model->get_by_id($v_f['menu_id']);
                $v['url'] = $v_f['class'] . '/' . $v_f['method'] . '/';
                break;
              }
            }
          }
        }
        if (isset($menu_auth[$module_id][$v['id']])) {
          $new_menu[] = $v;
        }
      }
    }
    //菜单列表
    $menu_str = '';
    if (is_full_array($new_menu)) {
      foreach ($new_menu as $v) {
        if ($v['is_display'] == 0) {
          continue;
        }
        $link = $v['id'] == $menu_id ? 'link link_on' : 'link';
        $menu_str .= '<a href="/' . $v['url'] . '" class="' . $link
          . '"><span class="iconfont">' . $v['icon'] . '</span>'
          . $v['name'] . '</a>';
      }
      $user_menu = $menu_str;
    }
    //菜单功能列表
    $func_menu_str = '';
    if (is_full_array($new_func_menu)) {
      foreach ($new_func_menu as $v) {
        $link = $v['id'] == $func_menu_id ? 'link link_on' : 'link';
        $func_menu_str .= '<a href="/' . $v['class'] . '/' . $v['method']
          . '/" class="' . $link
          . '"><span class="iconfont hide">&#xe607;</span>'
          . $v['name'] . '</a>';
      }
      $user_func_menu = $func_menu_str;
    }
    return array('user_menu' => $user_menu, 'user_func_menu' => $user_func_menu);
  }

  /**
   * 判断功能是否有权限
   * @param string $class 类名
   * @param string $method 方法名
   * @return ['area' : 1(1本人，2门店，3公司), 'auth' => 1（1有权限 0 无权限）]
   */
  public function get_func_permission($class, $method)
  {
    //获取用户的功能权限
    $func_auth = $this->user_permission['func_auth'];
    //根据class和method获取当前菜单编号
    //根据菜单编号获取模块编号
    $func = $this->permission_func_base_model->get_by_class_method($class, $method);
    //权限控制
    $func_auth_result = array();
    $func_auth_result['area'] = '';
    if (is_full_array($func) && $func['init_auth'] == 1) //需要做权限判断，其它的放开
    {
      //获取权限功能id
      $func_id = $func['id'];
      //根据功能权限查找菜单和模块是不是需要做权限控制
      $menu = $this->permission_menu_base_model->get_by_id($func['menu_id']);
      $module = $this->permission_module_base_model->get_by_id($menu['module_id']);
      if ($module['init_auth'] == 1 && $menu['init_auth'] == 1) {
        if (isset($func_auth[$func_id])) //有权限
        {
          $func_auth_result['auth'] = 1;
          //查找菜单是否有范围
          if ($func['is_area'] == 1) //有范围
          {
            $func_auth_result['area'] = $func_auth[$func_id];
          }
        } else //没有权限
        {
          $func_auth_result['auth'] = 0;
        }
      } else //模块和菜单无需菜单权限
      {
        $func_auth_result['auth'] = 1; //有权限
      }
    } else //无需权限判断-默认有权限
    {
      $func_auth_result['auth'] = 1;
    }
    return $func_auth_result;
  }

  /**
   * 批量判断功能是否有权限
   * @param array $arr_func 功能数组 array(array('class' => 'sell', 'method' => 'add')))
   * @return [['area' : 1(1本人，2门店，3公司), 'auth' => 1（1有权限 0 无权限）, 'class' => 'sell', 'method' : 'list'],
   * ['area' : 1(1本人，2门店，3公司), 'auth' => 1（1有权限 0 无权限）, 'class' => 'sell', 'method' : 'add']]
   */
  public function get_batch_func_permission($arr_func)
  {
    $batch_func_auth_result = array();
    if (is_full_array($arr_func)) {
      foreach ($arr_func as &$v) {
        $func_auth_result = $this->get_func_permission($v['class'], $v['method']);
        $v['auth'] = $func_auth_result['auth'];
        if (isset($func_auth_result['area'])) {
          $v['area'] = $func_auth_result['area'];
        }
        $batch_func_auth_result[] = $v;
      }
    }
    return $batch_func_auth_result;
  }

  /**
   * 经纪人编号、门店编号和纪纪人编号都有
   * 比较查出的权限是否和经纪人所寄于的权限是否一致
   * @param array $area_auth 功能默认权限
   * @param array $compare_param 对比的权限
   */
  public function compare_has_permission($area_auth, $compare_param)
  {
    $compare_result = false;
    if ($area_auth['area'] == 1 && isset($compare_param['broker_id'])) //本人
    {
      $compare_result = $compare_param['broker_id']
      == $this->broker_info['broker_id'] ? true : false;
    } else if ($area_auth['area'] == 2) //门店
    {
      if (!isset($compare_param['agency_id'])) {
        //通过经纪人编号查找门店编号
        //@todo
        $this->load->model('api_broker_base_model');//经纪人接口模型类
        $broker_info = $this->api_broker_base_model->get_by_broker_id($compare_param['broker_id']);
        $compare_param['agency_id'] = $broker_info['agency_id'];
      }
      $compare_result = $compare_param['agency_id']
      == $this->broker_info['agency_id'] ? true : false;
    } else if ($area_auth['area'] == 3) //公司
    {

      if (!isset($compare_param['company_id'])) {
        if (isset($compare_param['agency_id'])) //设置了门店
        {
          $this->load->model('api_broker_base_model');
          $company = $this->api_broker_base_model->get_by_agency_id($compare_param['agency_id']);
          $compare_param['company_id'] = $company['company_id'];
        } else {
          //通过经纪人编号查找公司编号
          //@todo
          $this->load->model('api_broker_base_model');//经纪人接口模型类
          $broker_info = $this->api_broker_base_model->get_by_broker_id($compare_param['broker_id']);
          $compare_param['company_id'] = $broker_info['company_id'];
        }
      }
      $compare_result = $compare_param['company_id']
      == $this->broker_info['company_id'] ? true : false;
    }
    return $compare_result;
  }
}

?>
