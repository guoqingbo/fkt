<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * Permission Controller CLASS
 *
 * 员工角色、权限管理功能 控制器
 *
 * @package         MLS
 * @subpackage      Controllers
 * @category        Controllers
 * @author          sun
 */
class Purview extends MY_Controller
{

  /**
   * 当前页码
   *
   * @access private
   * @var string
   */
  private $_current_page = 1;

  /**
   * 每页条目数
   *
   * @access private
   * @var int
   */
  private $_limit = 10;

  /**
   * 偏移
   *
   * @access private
   * @var int
   */
  private $_offset = 0;

  /**
   * 条目总数
   *
   * @access private
   * @var int
   */
  private $_total_count = 0;

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    //引用模型层
    $this->load->model('purview_company_group_model');
    $this->load->model('purview_tab_model');
    $this->load->model('purview_department_group_model');
    $this->load->model('operate_log_model');

      $this->load->model('purview_modules_model');
      $this->load->model('purview_list_model');

      $this->load->model('purview_system_group_model');
      $this->load->model('department_model');
  }

  public function index()
  {
    //获取搜索列表框内容（无重复）
    $c_id = $this->user_arr['company_id'];
    $r_id = $this->user_arr['role_id'];
      $a_id = $this->user_arr['department_id'];
//    if ($a_id > 0) {
//      $system_group_id = $this->purview_department_group_model->get_system_group_id($r_id);
//    } else {
//      $system_group_id = $this->purview_company_group_model->get_system_group_id($r_id);
//    }
//      $system_group_id = $this->user_arr['role_level'];
//    if (empty($system_group_id)) {
//      $system_group_id = 9;
//      $cond_where = " and b.company_id = {$c_id} and p.system_group_id >= {$system_group_id}";
//    }
      $cond_where = " and company_id = {$c_id}";
//    if ($a_id > 0) {
//      $cond_where .= " and department_id = {$a_id}";
//    }

    $bond_where = "where company_id = {$c_id} and status = 1";
//    if ($system_group_id >= 4) {
//      $bond_where .= " and id = {$a_id}";
//    }
      //获取下拉框部门列表
      $agency = $this->purview_department_group_model->get_department_norepeat($bond_where);
    //获取列表内容
    $list = $this->purview_department_group_model->get_all_by($cond_where);
      //获取全部权限等级
      foreach ($list as $key => $vo) {
          $list[$key]['role_name'] = $this->purview_system_group_model->get_role_name(explode(",", $vo['role_level']));
      }

    $data['list'] = $list;
    $user_arr = $this->user_arr;
    $data['user_arr'] = $user_arr;
    $data['agency'] = $agency;
//    $data['system_group_id'] = $system_group_id;
    //用户菜单栏
    $user_menu = $this->user_menu;
    $data['user_menu'] = $user_menu;

    $data['user_func_menu'] = $this->user_func_menu;

    //页面标题
    $data['page_title'] = '权限设置';
    //需要加载的css
    $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css'
      . ',mls_guli/css/v1.0/house_manage.css,'
      . 'mls_guli/css/v1.0/system_set.css,'
      . 'mls_guli/css/v1.0/cal.css,'
      . 'mls_guli/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['foot_js'] = load_js('mls_guli/js/v1.0/openWin.js'
      . ',mls_guli/js/v1.0/house.js,mls_guli/js/v1.0/backspace.js,mls_guli/js/v1.0/backspace.js');
    $data['user_arr'] = $user_arr;
      $this->view('purview/index', $data);
  }

  public function get_group()
  {
      $department_id = $this->input->get('department_id', true);
    $c_id = $this->user_arr['company_id'];

      $cond_where = " and company_id = {$c_id} and department_id = {$department_id}";

    //获取列表内容
    $list = $this->purview_department_group_model->get_all_by($cond_where);
      //获取全部权限等级
      foreach ($list as $key => $vo) {
          $list[$key]['role_name'] = $this->purview_system_group_model->get_role_name(explode(",", $vo['role_level']));
      }
    echo json_encode($list);
  }

  /**
   * 没有权限访问页面
   */
  public function none()
  {
    $data = array();
    //页面标题
    $data['page_title'] = '系统访问权限受限';
    //需要加载的css
    $data['css'] = load_css('mls_guli/css/v1.0/base.css'
      . ',mls_guli/css/v1.0/house_manage.css'
      . ',mls_guli/css/v1.0/myStyle.css');
    $this->view('/agency/purview/none', $data);
  }

  /**
   * 没有权限访问页面
   */
  public function none_iframe($iframe_id = '')
  {
    $data = array();
    //页面标题
    $data['page_title'] = '系统访问权限受限';
    //需要加载的JS
    $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js'
      . ',mls_guli/js/v1.0/house.js,mls_guli/js/v1.0/backspace.js');
    //需要加载的css
    $data['css'] = load_css('mls_guli/css/v1.0/base.css'
      . ',mls_guli/css/v1.0/house_manage.css'
      . ',mls_guli/css/v1.0/myStyle.css');
    $data['iframe_id'] = $iframe_id;
      $this->load->view('/agency/purview/none_iframe', $data);
  }

  /**
   * 初始化分页参数
   *
   * @access public
   * @param  int $current_page
   * @param  int $page_size
   * @return void
   */

  public function set_group_func($role_id, $agency_id, $company_id)
  {
      $role_id = explode('-', $role_id);
    //加载模型层
    $data = array();
    $company_id = $this->user_arr['company_id'];
    $data['now_agency_id'] = $this->user_arr['agency_id'];

    //当前公司下所有的一级二级门店
      $company_info = $this->department_model->get_children_by_company_id($company_id);
    //门店数据重构，一级门店下面排列所属于它的二级门店
    //当前公司下所有门店
    $all_company_info = array();
    if (is_full_array($company_info)) {
      foreach ($company_info as $k => $v) {
        //判断门店下是否有下属门店
          $where_cond = array('department_id' => $v['id']);
        $is_has_agency = '0';
          $next_agency_data = $this->department_model->get_all_by($where_cond);
        if (is_full_array($next_agency_data)) {
          $is_has_agency = '1';
        }
        $company_info[$k]['is_has_agency'] = $is_has_agency;
      }
      foreach ($company_info as $k => $v) {
        //一级门店追加
        if (0 == $v['agency_id']) {
          $all_company_info[] = $v;
        }
      }
      //二级门店追加
      foreach ($company_info as $k => $v) {
        if ($v['agency_id'] != 0) {
          foreach ($all_company_info as $key => $val) {
            if ($v['agency_id'] == $val['id']) {
              $all_company_info[$key]['next_agency_data'][] = $v;
            }
          }
        }
      }
    }
    $data['all_company_info'] = $all_company_info;

    //门店默认权限组
    if ($agency_id > 0) {
        $group = $this->purview_system_group_model->get_group($agency_id);
    } else {
      $group = $this->purview_system_group_model->get_group_company($role_id, $company_id);
    }

    //用户菜单栏
    $data['user_menu'] = $this->purview_tab_model->get_tab("purview", "index");

    $data['group'] = $group;

    //需要加载的css
    $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
      . 'mls_guli/css/v1.0/house_manage.css,'
      . 'mls_guli/css/v1.0/system_set.css,'
      . 'mls_guli/css/v1.0/cal.css,'
      . 'mls_guli/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['foot_js'] = load_js('mls_guli/js/v1.0/openWin.js'
      . ',mls_guli/js/v1.0/house.js,mls_guli/js/v1.0/backspace.js,mls_guli/js/v1.0/backspace.js');
      $this->view('purview/set_group_func', $data);
  }

  public function save_button_submit()
  {
    $role_id = $this->user_arr['role_id'];
    //所操作的门店角色权限id
    $id = $this->input->get('id', true);
    $company_id = $this->user_arr['company_id'];
    $agency_id = $this->input->get('agency_id', true);
    //权限值
    $equipment = $this->input->get('equipment', true);

    //获得当前经纪人的角色等级
    $this_level = 0;
    if ($agency_id > 0) {
      $agency_group_data = $this->purview_department_group_model->get_one_by(array('id' => intval($role_id)));
    } else {
      $agency_group_data = $this->purview_company_group_model->get_one_by(array('id' => intval($role_id)));
    }
    if (is_full_array($agency_group_data)) {
      $this_system_group_id = $agency_group_data['system_group_id'];
      $this_system_role_data = $this->purview_system_group_model->get_one_by(array('id' => intval($this_system_group_id)));
      if (is_full_array($this_system_role_data)) {
        $this_level = intval($this_system_role_data['level']);
      }
    }
    $return_array = array();
    //获得所操作的角色等级
    $deal_role_level = 0;
    if ($agency_id > 0) {
      $agency_group_data2 = $this->purview_department_group_model->get_one_by(array('id' => intval($id)));
    } else {
      $agency_group_data2 = $this->purview_company_group_model->get_one_by(array('id' => intval($id)));
    }
    if (is_full_array($agency_group_data2)) {
      $this_role_system_group_id = $agency_group_data2['system_group_id'];
      $this_deal_role_data = $this->purview_system_group_model->get_one_by(array('id' => intval($this_role_system_group_id)));
      if (is_full_array($this_deal_role_data)) {
        $this_deal_role_level = intval($this_deal_role_data['level']);
      }
    }

    //所操作的角色等级为店长以上（公司范围），则变更该公司下的所有该角色权限。
    $kind = 0;
    if (is_int($this_deal_role_level) && $this_deal_role_level < 6) {
      $kind = 1;
      $update_result = $this->purview_department_group_model->save_group_func_by_company($equipment, intval($company_id), intval($this_role_system_group_id));
    } else {
      //判断当前经纪人的角色等级，如果为店长及以下，则只变更当前门店的权限；如果为店长以上，则弹框选择门店。
      if ($this_level > 5) {
        //店长及以下
        $kind = 2;
        $update_result = $this->purview_department_group_model->save_group_func_by_id($equipment, $id);
      } else {
        //店长以上
        $kind = 3;

      }
    }
    //$info=$this->purview_department_group_model->save_group_func_by_id($equipment,$id);
    $return_array['kind'] = $kind;
    $return_array['update_result'] = $update_result;
    if (isset($update_result) && $update_result > 0) {
      //操作日志
      $log_text = '';
      switch ($this_deal_role_level) {
        case  1 :
          $log_text = '修改总经理权限';
          break;
        case  2 :
          $log_text = '修改副总经理权限';
          break;
        case  3 :
          $log_text = '修改总经理助理权限';
          break;
        case  4 :
          $log_text = '修改业务总监权限';
          break;
        case  5 :
          $log_text = '修改区域经理权限';
          break;
        case  6 :
          $log_text = '修改店长权限';
          break;
        case  7 :
          $log_text = '修改店务秘书权限';
          break;
        case  8 :
          $log_text = '修改销售经理权限';
          break;
        case  9 :
          $log_text = '修改客服专员权限';
          break;
        case  10 :
          $log_text = '修改正式经纪人权限';
          break;
        case  11 :
          $log_text = '修改见习经纪人权限';
          break;
      }
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 30;
      $add_log_param['text'] = $log_text;
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
    }
    echo json_encode($return_array);
  }

  public function save_button_submit_acency()
  {
    $agency_group_id = $this->input->post('agency_group_id', TRUE);//角色id
    $agency_arr = $this->input->post('agency_access_area', TRUE);//门店id
    $per_id_str = $this->input->post('per_id_str', TRUE);//所选权限节点

    $agency_group_data = $this->purview_department_group_model->get_one_by(array('id' => intval($agency_group_id)));
    if (is_full_array($agency_group_data)) {
      $this_role_system_group_id = $agency_group_data['system_group_id'];
    }
    $equipment = array();
    if (!empty($per_id_str)) {
      $equipment = explode(',', trim($per_id_str, ','));
    }
    if (is_full_array($agency_arr) && !empty($this_role_system_group_id)) {
      $role_data = $this->purview_system_group_model->get_by_id($this_role_system_group_id);
      if (is_full_array($role_data)) {
        $this_deal_role_level = $role_data['level'];
      }
        $update_result = $this->purview_department_group_model->save_group_func_by_department($equipment, $agency_arr, intval($this_role_system_group_id));
    }
    if (is_int($update_result) && $update_result > 0) {
      //操作日志
      $log_text = '';
      switch ($this_deal_role_level) {
        case  1 :
            $log_text = '修改超级管理员权限';
          break;
        case  2 :
            $log_text = '修改签约人员权限';
          break;
        case  3 :
            $log_text = '修改财务人员权限';
            break;
          case  4 :
              $log_text = '修改权证内勤权限';
          break;
          case  5 :
              $log_text = '修改管理人员权限';
          break;
          case  6 :
              $log_text = '修改权证外勤权限';
          break;
          case  7 :
              $log_text = '修改一手办证权限';
          break;
      }
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['department_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 30;
      $add_log_param['text'] = $log_text;
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);

      echo 'success';
    } else {
      echo 'failed';
    }
    exit;

  }


  public function get_group_func($id = "")
  {
    $data_view = array();

    $company_id = $this->user_arr['company_id'];
      $agency_id = $this->user_arr['department_id'];
    if ($agency_id > 0) {
      $menu_auth = $this->purview_department_group_model->get_menu_by($id, $agency_id);
      $func_auth = $this->purview_department_group_model->get_func_by($id, $agency_id);
    } else {
      $menu_auth = $this->purview_company_group_model->get_menu_by($id, $company_id);
      $func_auth = $this->purview_company_group_model->get_func_by($id, $company_id);
    }
    $result['menu_auth'] = $menu_auth;
    $result['func_auth'] = $func_auth;
    $result['id'] = $id;
    $result['company_id'] = $company_id;
    $result['agency_id'] = $agency_id;
    echo json_encode($result);
  }

  //获得公司权限内容
    public function get_company_func()
  {
    //权限模块、菜单、功能数组
      $isajax = $this->input->post('isajax', true);
      $role_id = $this->input->post('role_id', true);
    if ($isajax) {
      //从purview_agency_grounp中获取权限功能，解序列化
        $result = $this->purview_department_group_model->get_func_by_id($role_id);
      if (!empty($result)) {
        foreach ($result as $key => $val) {
          //从purview_modules中获取一级菜单名
          $menu['list'][$key] = $this->purview_modules_model->get_modules_by($key);
          $func = array();
          $func_list = array();
          foreach ($val as $k => $val1) {
            //根据pid和mid从purview_list中获取功能
            $func = $this->purview_list_model->get_list_by($val1, $key);
            if (!isset($func_list[$func['tab_id']])) {
              if ($func['tab_id'] == 0) {
                $tab_name = '';
              } else {
                //获取一级菜单名称
                $tab = $this->purview_modules_model->get_tab_id($func['tab_id']);
                $tab_name = $tab['name'];
              }
              $func_list[$func['tab_id']] = array('name' => $tab_name, 'list' => array());
            }


            if (!isset($func_list[$func['tab_id']]['list'][$func['secondtab_id']])) {
              if ($func['secondtab_id'] == 0) {
                $secondtab_name = '';
              } else {
                $secondtab = $this->purview_modules_model->get_secondtab_id($func['secondtab_id']);
                $secondtab_name = $secondtab['name'];
              }
              $func_list[$func['tab_id']]['list'][$func['secondtab_id']] = array('name' => $secondtab_name, 'list' => array());
            }
            array_push($func_list[$func['tab_id']]['list'][$func['secondtab_id']]['list'], $func);
          }
          $menu['list'][$key]['func'] = $func_list;
        }
      }
        $menu['level'] = $role_id;
      //print_r($menu);
      echo json_encode($menu);
    }
  }


  public function get_company_group($id)
  {
    //权限模块、菜单、功能数组
    $isajax = $this->input->get('isajax', true);
    if ($isajax) {
      $result = $this->purview_department_group_model->get_func_by_id($id);
      if (!empty($result)) {
        foreach ($result as $key => $val) {
          $menu[$key] = $this->purview_modules_model->get_modules_by($key);
          $func = array();
          foreach ($val as $k => $v) {
            $func[] = $this->purview_list_model->get_list_by($v, $key);
          }
          $menu[$key]['func'] = $func;
        }
      }
      echo json_encode($menu);
    }
  }

  //获得系统权限内容
  public function get_system_group($id)
  {
    //权限模块、菜单、功能数组
    $isajax = $this->input->get('isajax', true);
    if ($isajax) {
      $result = $this->purview_system_group_model->get_func_by_id($id);
      if (!empty($result)) {
        foreach ($result as $key => $val) {
          $menu['list'][$key] = $this->purview_modules_model->get_modules_by($key);
          $func = array();
          $func_list = array();
          foreach ($val as $k => $val1) {
            $func = $this->purview_list_model->get_list_by($val1, $key);
            if (!isset($func_list[$func['tab_id']])) {
              if ($func['tab_id'] == 0) {
                $tab_name = '';
              } else {
                //获取一级菜单名称
                $tab = $this->purview_modules_model->get_tab_id($func['tab_id']);
                $tab_name = $tab['name'];
              }
              $func_list[$func['tab_id']] = array('name' => $tab_name, 'list' => array());
            }


            if (!isset($func_list[$func['tab_id']]['list'][$func['secondtab_id']])) {
              if ($func['secondtab_id'] == 0) {
                $secondtab_name = '';
              } else {
                $secondtab = $this->purview_modules_model->get_secondtab_id($func['secondtab_id']);
                $secondtab_name = $secondtab['name'];
              }
              $func_list[$func['tab_id']]['list'][$func['secondtab_id']] = array('name' => $secondtab_name, 'list' => array());
            }
            array_push($func_list[$func['tab_id']]['list'][$func['secondtab_id']]['list'], $func);
          }
          $menu['list'][$key]['func'] = $func_list;
        }
      }
      $menu['level'] = $id;
      echo json_encode($menu);
    }
  }

  public function purview_rule()
  {
    /*//获取搜索列表框内容（无重复）
    $c_id = $this->user_arr['company_id'];
    $r_id = $this->user_arr['role_id'];
    $a_id = $this->user_arr['agency_id'];
    $system_group_id=$this->purview_department_group_model->get_system_group_id($r_id);
    if(empty($system_group_id)){
        $system_group_id = 9;
    }
    $cond_where=" and b.company_id = {$c_id} and p.system_group_id >= {$system_group_id}";
    if($system_group_id >= 4){
         $cond_where .= " and b.agency_id = {$a_id}";
    }

    $bond_where ="where company_id = {$c_id} and status = 1";
    if($system_group_id >= 4){
         $bond_where .= " and id = {$a_id}";
    }
    //获取下拉框公司列表
    $agency = $this->purview_department_group_model->get_agency_norepeat($bond_where);
    //获取列表内容
    $list = $this->purview_department_group_model->get_all_by($cond_where);

    $data['list']=$list;
    $user_arr=$this->user_arr;
    $data['user_arr']=$user_arr;
    $data['agency'] = $agency;
    $data['system_group_id'] = $system_group_id;
    //用户菜单栏
    $user_menu=$this->user_menu;
    $data['user_menu']=$user_menu;

    $data['user_func_menu']=$this->user_func_menu;

    //页面标题
    $data['page_title'] = '权限设置';
    //需要加载的css
    $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css'
            .',mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/system_set.css,'
            . 'mls_guli/css/v1.0/cal.css,'
            . 'mls_guli/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['foot_js'] = load_js('mls_guli/js/v1.0/openWin.js'
                        . ',mls_guli/js/v1.0/house.js,mls_guli/js/v1.0/backspace.js,mls_guli/js/v1.0/backspace.js');
    $data['user_arr']=$user_arr;*/
    $data = array();
    $this->view('agency/purview/purview_rule', $data);
  }

    //修改系统角色名称
    public function modify_role_name()
    {
        $system_group_id = $this->input->post('system_group_id', true);
        $role_name = $this->input->post('role_name', true);
        $update_data = [
            'name' => $role_name
        ];
        $res = $this->purview_system_group_model->update_by_id($update_data, $system_group_id);
        if ($res) {
            $data = ['status' => 'success', 'msg' => '修改成功'];
        } else {
            $data = ['status' => 'nomodify', 'msg' => '未作任何修改'];
        }
        echo json_encode($data);
    }
}

/* End of file purview.php */
/* Location: ./application/mls_guli/controllers/purview.php */
