<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Dictionary_type extends MY_Controller
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
  private $_limit = 20;

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


  public function __construct()
  {
    parent::__construct();
    $this->load->model('department_model');
    $this->load->model('signatory_info_model');

    $this->load->model('signatory_model');
    $this->load->model('company_employee_model');

    $this->load->model('dictionary_type_model');
    $this->load->model('dictionary_model');

    $this->load->model('signatory_operate_log_model');
  }

  /**
   * 初始化分页参数
   *
   * @access public
   * @param  int $current_page
   * @param  int $page_size
   * @return void
   */
  private function _init_pagination($current_page = 1, $page_size = 0)
  {
    /** 当前页 */
    $this->_current_page = ($current_page && is_numeric($current_page)) ?
      intval($current_page) : 1;

    /** 每页多少项 */
    $this->_limit = ($page_size && is_numeric($page_size)) ?
      intval($page_size) : $this->_limit;

    /** 偏移量 */
    $this->_offset = ($this->_current_page - 1) * $this->_limit;

    if ($this->_offset < 0) {
      redirect(base_url());
    }
  }


  /**
   * 根据关键词获取类型信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_dictionary_type_info_by_kw()
  {
    //当前经纪人等级
    $role_level = intval($this->user_arr['role_level']);
    //当前经纪人所在公司
    $company_id = intval($this->user_arr['company_id']);
    //根据角色，决定搜索范围
    $search_arr = array(
      'role_level' => $role_level,
      'company_id' => $company_id
    );

    $keyword = $this->input->get('keyword', TRUE);
    $select_fields = array('id', 'name');
    $this->dictionary_type_model->set_select_fields($select_fields);
    $cmt_info = $this->dictionary_type_model->get_dictionary_type_info_by_kw2($keyword, $search_arr, 10);
    foreach ($cmt_info as $key => $value) {
      $cmt_info[$key]['label'] = $value['name'];
      $cmt_info[$key]['is_has_dictionary_type'] = $value['sub'] ? '1' : '0';
    }

    if (empty($cmt_info) || empty($search_arr['role_level']) || empty($search_arr['company_id'])) {
      $cmt_info[0]['id'] = 0;
      $cmt_info[0]['label'] = '暂无类型';
    }

    echo json_encode($cmt_info);
  }


  /**
   * 根据关键词获取数据信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_dictionary_info_by_kw()
  {
    //当前经纪人等级
    $role_level = intval($this->user_arr['role_level']);
    //当前经纪人所在公司
    $company_id = intval($this->user_arr['company_id']);
    //根据角色，决定搜索范围
    $search_arr = array(
      'role_level' => $role_level,
      'company_id' => $company_id
    );

    $keyword = $this->input->get('keyword', TRUE);
    $dictionary_type_id = $this->input->get('dictionary_type_id', TRUE);
    $select_fields = array('id', 'name');
    $this->dictionary_model->set_select_fields($select_fields);
    $cmt_info = $this->dictionary_model->get_dictionary_info_by_kw($keyword, $dictionary_type_id, $search_arr, 10);
    foreach ($cmt_info as $key => $value) {
      $cmt_info[$key]['label'] = $value['name'];
    }

    if (empty($cmt_info) || empty($search_arr['role_level']) || empty($search_arr['company_id'])) {
      $cmt_info[0]['id'] = 0;
      $cmt_info[0]['label'] = '暂无类型';
    }

    echo json_encode($cmt_info);
  }

  public function index($dictionary_type_id = "", $dictoinary_id = "", $b_id = '')
  {
    $view_type = 'index';//用于区分首页还是搜索页
    if ($this->input->get('view_type')) {
      $view_type = $this->input->get('view_type');
    }
    $search_dictionary_type_data = array();
    $search_result_num = 0;
    $dictionary_type_id_str = $this->input->get('search_dictionary_type_id');
    $type_keyword = $this->input->get('search_type_keyword');
    if ($type_keyword) {
      $type_keyword = urldecode($type_keyword);
    }
    if ('search' == $view_type && !empty($dictionary_type_id_str)) {
      $dictionary_type_id_str = trim($dictionary_type_id_str, ',');
      $dictionary_type_id_arr = explode(',', $dictionary_type_id_str);
      $search_result_num = count($dictionary_type_id_arr);
      if (!empty($dictionary_type_id_str)) {
        $search_dictionary_type_data = $this->dictionary_type_model->get_all_by_dictionary_type_id($dictionary_type_id_str);
      }
    }

    //判断是否有下属门店
    if (is_full_array($search_dictionary_type_data)) {
      foreach ($search_dictionary_type_data as $k => $v) {
        //判断门店下是否有下属门店
        $where_cond = array('dictionary_type_id' => $v['id'], 'status' => '1');
        $is_has_dictionary_type = '0';
        $next_dictionary_type_data = $this->dictionary_type_model->get_all_by($where_cond);
        if (is_full_array($next_dictionary_type_data)) {
          $is_has_dictionary_type = '1';
        }
        $search_dictionary_type_data[$k]['is_has_dictionary_type'] = $is_has_dictionary_type;
      }
    }

    $data['search_result_num'] = $search_result_num;
    $data['view_type'] = $view_type;
    $data['search_dictionary_type_data'] = $search_dictionary_type_data;
    $data['search_type_keyword'] = $type_keyword;
    if (!$b_id) {
      $signatory_id = $this->user_arr['signatory_id'];
    } else {
      $signatory_id = $b_id;
    }
    $data['user_menu'] = $this->user_menu;
    //当前用户的所有信息
    $signatory_info = $this->company_employee_model->get_signatory_by_id($signatory_id);
    //获取当前帐号权限等级身份
    $level = intval($this->user_arr['role_level']);
    $data['level'] = $level;
    $this->department_model->set_select_fields(array('id', 'name', 'photo'));
//    $company = $this->department_model->get_by_id($company_id);//所属总部门的信息
    //print_r($company);
//    $this->dictionary_model->set_select_fields(array('name', 'address', 'id', 'dist_id', 'street_id', 'telno', 'dictionary_type_id'));
    if (empty($dictionary_type_id)) {
      $dictionary_type_id = '0';
    }
    $dictionary_type_info = $this->dictionary_type_model->get_by_id($dictionary_type_id);

    //print_r($dictionary_type_info);
    //获取最高节点的相关联系信息
    $data['store_name'] = $dictionary_type_info['name'];
    $data['dictionary_type_name'] = $dictionary_type_info['name'];
    $data['dictionary_type_id'] = $dictionary_type_id;

    //当前字典类型下所有的子类型数据
    $dictionary_type_info = $this->dictionary_type_model->get_children_by_dictionary_type_id('0');
    //字典数据重构
    //当前字典类型下所有数据，字典数据只有一级和二级，一级类型为根类型 id = 1
    $all_dictionary_type_info = array();
    if (is_full_array($dictionary_type_info)) {

      foreach ($dictionary_type_info as $k => $v) {
        //判断类型下是否有子类型
        $where_cond = array('dictionary_type_id' => $v['id'], 'status' => '1');
        $is_has_dictionary_type = '0';
        $next_dictionary_type_data = $this->dictionary_type_model->get_all_by($where_cond);
        if (is_full_array($next_dictionary_type_data)) {
          $is_has_dictionary_type = '1';
          $dictionary_type_info[$k]['next_dictionary_type_data'] = $next_dictionary_type_data;
        }
        $dictionary_type_info[$k]['is_has_dictionary_type'] = $is_has_dictionary_type;
      }
      foreach ($dictionary_type_info as $k => $v) {
        //一级类型追加
        if (0 == $v['dictionary_type_id']) {
          $all_dictionary_type_info[] = $v;
        }
      }
      //二级类型追加
//      foreach ($dictionary_type_info as $k => $v) {
//        if ($v['id'] != 0) {
//          foreach ($all_dictionary_type_info as $key => $val) {
//            if ($v['dictionary_type_id'] == $val['id']) {
//              $all_dictionary_type_info[$key]['next_dictionary_type_data'][] = $v;
//            }
//          }
//        }
//      }
    }
    $data['all_dictionary_type_info'] = $all_dictionary_type_info;

    //获取类型关联权限节点
//    $dictionary_father_node = $this->dictionary_purview_node_model->get_all_by_modules();
//    $dictionary_children_node = $this->dictionary_purview_node_model->get_all();
//    if (is_full_array($dictionary_father_node) && is_full_array($dictionary_children_node)) {
//      foreach ($dictionary_father_node as $k => $v) {
//        $pid = $v['id'];
//        foreach ($dictionary_children_node as $key => $value) {
//          if ($value['mid'] == $pid) {
//            $dictionary_father_node[$k]['child_node'][] = $value;
//          }
//        }
//      }
//    }
//    $data['dictionary_father_node'] = $dictionary_father_node;

    //根据权限获取部门名下的所有子部门信息
      $new_dictionary_type_info = $all_dictionary_type_info;
      if (in_array($level, array(1, 2, 3, 4, 5, 6))) {
      //总经理，副总经理。看到部门下所有的片区、门店
      $new_dictionary_type_info = $all_dictionary_type_info;
      } else if (in_array($level, array(7))) {
      //区域经理。只能看到其所在的一级门店和二级门店
      $this_dictionary_info = $this->dictionary_model->get_by_id_one($signatory_info['dictionary_type_id']);
      $this_father_dictionary_type_id = $this_dictionary_info[0]['dictionary_type_id'];

      //判断门店下是否有下属门店
      $where_cond = array('dictionary_type_id' => $this_dictionary_info[0]['id']);
      $this_dictionary_info[0]['is_has_dictionary_type'] = '0';
      $next_dictionary_type_data = $this->dictionary_model->get_all_by($where_cond);
      if (is_full_array($next_dictionary_type_data)) {
        $this_dictionary_info[0]['is_has_dictionary_type'] = '1';
      }

      if (isset($this_father_dictionary_type_id) && $this_father_dictionary_type_id > 0) {
        //当前门店为二级门店,找到一级门店
        $father_dictionary_data = $this->dictionary_model->get_by_id_one($this_father_dictionary_type_id);
        $father_dictionary_data[0]['is_has_dictionary_type'] = '1';
        $father_dictionary_data[0]['next_dictionary_data'] = $this_dictionary_info;
        $new_dictionary_type_info = $father_dictionary_data;
      } else {
        //当前门店为一级门店,找到二级门店
        if (is_full_array($next_dictionary_type_data)) {
          $this_dictionary_info[0]['next_dictionary_data'] = $next_dictionary_type_data;
        }
        $new_dictionary_type_info = $this_dictionary_info;
      }
      } else if (in_array($level, array(8, 9))) {
      //店长,店务秘书，只能看到当前门店
      $this_dictionary_info = $this->dictionary_model->get_by_id_one($signatory_info['dictionary_type_id']);
      //判断门店下是否有下属门店
      $where_cond = array('dictionary_type_id' => $this_dictionary_info[0]['id']);
      $is_has_dictionary_type = '0';
      $next_dictionary_type_data = $this->dictionary_model->get_all_by($where_cond);
      if (is_full_array($next_dictionary_type_data)) {
        $is_has_dictionary_type = '1';
      }
      $this_dictionary_info[0]['is_has_dictionary_type'] = $is_has_dictionary_type;
      $new_dictionary_type_info = $this_dictionary_info;
    } else {
      $new_dictionary_type_info = "";
    }
//
//    //创建帐号时按权限显示部门
      if (in_array($level, array(1, 2, 3, 4, 5, 6))) {
      //最大权限
      $dictionary_type_info_dictionary = $new_dictionary_type_info;
      } else if (in_array($level, array(7))) {
      // 部门权限
      $dictionary_type_info_dictionary = $new_dictionary_type_info;
    } else {
      // 默认权限
      $dictionary_type_info_dictionary = $new_dictionary_type_info;
    }
    $data['dictionary_type_info_dictionary'] = $dictionary_type_info_dictionary;

    $data['dictionary_type_info'] = $new_dictionary_type_info;

    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $time = time();
    $this->_init_pagination($page);
    //查询消息的条件
    $cond_where = "status = 1";
    //类型链接传值组成的查询条件
    if ($dictionary_type_id) {
      $now_dictionary_type_id = $dictionary_type_id;
      $cond_where .= " and dictionary_type_id = {$dictionary_type_id} ";
    } else {
      $now_dictionary_type_id = $dictionary_type_id;
      //$cond_where .= " and dictionary_type_id = " . $dictionary_type_info['id'];
    }

    $dictionary_id_str = $this->input->get('search_dictionary_id');
    $keyword = $this->input->get('search_keyword');
    if ($keyword) {
      $keyword = urldecode($keyword);
      $cond_where .= " and (concat(`name`, 'name_abbr', 'key') LIKE '%" . $keyword . "%')";
    }
    if (!empty($dictionary_id_str)) {
      $dictionary_id_str = trim($dictionary_id_str, ',');
      $dictionary_id_str = explode(',', $dictionary_id_str);
      if (!empty($dictionary_id_str)) {
        $cond_where .= ' and id in (' . $dictionary_type_id_str . ')';
      }
    }

    if ($dictoinary_id) {
      $cond_where .= ' and id = ' . $dictoinary_id;
    }

    $data['now_dictionary_type_id'] = $now_dictionary_type_id;
    $data['now_father_dictionary_type_id'] = $dictionary_type_info['dictionary_type_id'];
    $data['search_keyword'] = $keyword;
//    $cond_where_dictionary = $cond_where;

    //符合条件的总行数
    $this->_total_count = $this->dictionary_model->count_by($cond_where);

    //获取数据列表内容
    $signatory_all_info = $this->dictionary_model->get_all_by($cond_where, $this->_offset, $this->_limit, 'dictionary_type_id ', 'ASC');
//    $signatory_all_info_dictionary = $this->dictionary_model->get_all_by($cond_where_dictionary, 0, 0);
    //print_r($signatory_all_info);

    $group_arr_id = array();
//    //获取全部数据字典类型
    foreach ($signatory_all_info as $vo) {
      $group_arr_id[] = $vo['id'];
    }
    $group_arr = $this->dictionary_type_model->get_all_by_dictionary_type_id();
//    //获取门店负责人信息
//    foreach ($signatory_all_info_dictionary as $vo) {
//      $manager_ground_id = $this->purview_dictionary_group_model->get_system_group_id($vo['role_id']);
//      //print_r($manager_ground_id);
//      if ($manager_ground_id == 4) {
//        $data['telno'] = $vo['phone'];
//        $data['linkman'] = $vo['truename'];
//      }
//    }

//    if (is_full_array($group_arr)) {
//      foreach ($group_arr as $k => $v) {
//        //获得角色等级
//        $system_role_data = $this->purview_system_group_model->get_one_by(array('id' => intval($v['system_group_id'])));
//        $group_arr[$k]['level'] = $system_role_data['level'];
//      }
//    }
    //echo "<hr/>";
    $data['group_arr'] = $group_arr;
//    //print_r($signatory_all_info);
    $data['signatory_all_info'] = $signatory_all_info;
//    //获取权限组列表
//    $purview_group = $this->organization_model->get_purview_group();
//    //print_r($purview_group);
//    $data['purview_group'] = $purview_group;
//
//
//    //获取信用等级信息
//    $trust_level_info = $this->organization_model->get_trust_level_info();
//    //print_r($trust_level_info);
//    $data['trust_level_info'] = $trust_level_info;
//
//    //获取认证信息
//    $ident_info = $this->organization_model->get_foreach($signatory_all_info, "type = 1 ");
//
//    $data['ident_info'] = $ident_info;
//    // $data['quali_info'] = $quali_info;
//
//    //添加区属板块
//    $data['district'] = $this->district_model->get_district();
//    $street = $this->district_model->get_street();
//    //print_r($street);
//    $data['street'] = $street;

    //当前类型下的子类型
    $data['dictionary_type_oneleval_data'] = $this->dictionary_type_model->get_dictionary_1_by_dictionary_type_id($dictionary_type_id);
    if (is_full_array($data['dictionary_type_oneleval_data'])) {
      foreach ($data['dictionary_type_oneleval_data'] as $key => $value) {
        if ($value['id'] == $dictionary_type_id) {
          unset($data['dictionary_type_oneleval_data'][$key]);
        }
      }
    }

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');
    //页面标题
    $data['page_title'] = '数据字典';
    $data['page_params'] = $params;

    //需要加载的css
    $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css'
      . ',mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/cal.css'
      . ',mls_guli/css/v1.0/guest_disk.css,mls_guli/css/v1.0/house_new.css'
        . ',mls_guli/images/alphabeta/bank-logo/bank-logo.css'
        . ',mls_guli/css/v1.0/personal_center.css');

    //需要加载的JS
    $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
      . 'mls_guli/js/v1.0/backspace.js,mls_guli/js/v1.0/house_list.js,mls_guli/js/v1.0/scrollPic.js'
    );


    $this->view('dictionary_type/dictionary_type_show', $data);
  }

  //设置门店数据操作范围
  public function set_department_per()
  {
    $company_id = $this->user_arr['company_id'];//部门id
    $main_dictionary_type_id = $this->input->post('main_dictionary_type_id', TRUE);//主门店id
    $sub_dictionary_type_id = $this->input->post('sub_dictionary_type_id', TRUE);//被关联门店id
    $child_node_arr = $this->input->post('child_node_id', TRUE);//权限节点
    //根据权限节点，重构数据，func_auth
    $this->load->model('department_purview_node_base_model');
    $func_auth = $this->department_purview_node_base_model->get_node_serialize_by_child_node($child_node_arr);

    //$is_view_house = $is_house_match = $is_house_share_tasks = $is_house_allocate = $is_view_customer = $is_customer_match = $is_customer_share_tasks =
    $is_customer_allocate = $is_cooperation = $is_key = $is_employee = $is_blacklist = $is_work_count = $is_house_count = $is_customer_count = 0;

    if (is_full_array($child_node_arr)) {
      //是否包含查看房源权限
      if (in_array('1', $child_node_arr)) {
        $is_view_house = 1;
      }
      //是否包含查看保密权限
      if (in_array('2', $child_node_arr)) {
        $is_house_secret = 1;
      }
      //是否包含房源智能匹配权限
      if (in_array('7', $child_node_arr)) {
        $is_house_match = 1;
      }
      //是否包含房源分配任务权限
      if (in_array('8', $child_node_arr)) {
        $is_house_share_tasks = 1;
      }
      //是否包含分配房源权限
      if (in_array('9', $child_node_arr)) {
        $is_house_allocate = 1;
      }
      //是否包含查看客源权限
      if (in_array('10', $child_node_arr)) {
        $is_view_customer = 1;
      }
      //是否包含客源智能匹配权限
      if (in_array('16', $child_node_arr)) {
        $is_customer_match = 1;
      }
      //是否包含客源分配任务权限
      if (in_array('17', $child_node_arr)) {
        $is_customer_share_tasks = 1;
      }
      //是否包含分配客源权限
      if (in_array('18', $child_node_arr)) {
        $is_customer_allocate = 1;
      }
      //是否包含合作方审核权限
      if (in_array('19', $child_node_arr)) {
        $is_cooperation = 1;
      }

      //是否包含钥匙管理权限
      if (in_array('20', $child_node_arr)) {
        $is_key = 1;
      }
      //是否包含通讯录权限
      if (in_array('21', $child_node_arr)) {
        $is_employee = 1;
      }
      //是否包含黑名单权限
      if (in_array('22', $child_node_arr)) {
        $is_blacklist = 1;
      }
      //是否包含工作统计权限
      if (in_array('23', $child_node_arr)) {
        $is_work_count = 1;
      }
      //是否包含房源统计权限
      if (in_array('24', $child_node_arr)) {
        $is_house_count = 1;
      }
      //是否包含客源统计权限
      if (in_array('25', $child_node_arr)) {
        $is_customer_count = 1;
      }
      //是否包含数据转移
      if (in_array('32', $child_node_arr)) {
        $is_data_transfer = 1;
      }
      //是否包含数据转移
      if (in_array('33', $child_node_arr)) {
        $is_customer_secret = 1;
      }
    }

    $update_data = array(
      'company_id' => intval($company_id),
      'main_dictionary_type_id' => intval($main_dictionary_type_id),
      'sub_dictionary_type_id' => intval($sub_dictionary_type_id),
      'func_auth' => $func_auth,
      'is_view_house' => $is_view_house,
      'is_house_secret' => $is_house_secret,
      'is_house_match' => $is_house_match,
      'is_house_share_tasks' => $is_house_share_tasks,
      'is_house_allocate' => $is_house_allocate,
      'is_view_customer' => $is_view_customer,
      'is_customer_match' => $is_customer_match,
      'is_customer_share_tasks' => $is_customer_share_tasks,
      'is_customer_allocate' => $is_customer_allocate,
      'is_cooperation' => $is_cooperation,
      'is_key' => $is_key,
      'is_employee' => $is_employee,
      'is_blacklist' => $is_blacklist,
      'is_work_count' => $is_work_count,
      'is_house_count' => $is_house_count,
      'is_customer_count' => $is_customer_count,
      'is_data_transfer' => $is_data_transfer,
      'is_customer_secret' => $is_customer_secret,
    );
    if (!empty($main_dictionary_type_id) && !empty($sub_dictionary_type_id)) {
      $this->load->model('department_purview_base_model');
      $deal_result = $this->department_purview_base_model->deal_into_data($update_data);
    }
    if (isset($deal_result) && (1 === $deal_result || 0 === $deal_result)) {
      echo '{"status":"success","msg":"关联门店设置成功"}';
    } else {
      echo '{"status":"failed","msg":"关联门店设置失败"}';
    }
    exit;
  }

  //复制权限
  public function copy_department_per()
  {
    $copy_main_dictionary_type_id = $this->input->post('copy_main_dictionary_type_id', TRUE);//主门店id
    $copy_sub_dictionary_type_id = $this->input->post('copy_sub_dictionary_type_id', TRUE);//被关联门店id
    $copy_department_arr = $this->input->post('copy_department', TRUE);
    if (!empty($copy_main_dictionary_type_id) && !empty($copy_sub_dictionary_type_id) && is_full_array($copy_department_arr)) {
      //找到对应的主门店、被关联门店的权限节点
      $main_sub_data = $this->department_purview_model->get_data_by_main_sub_id(intval($copy_main_dictionary_type_id), intval($copy_sub_dictionary_type_id));
      $copy_data = array();
      //判断被复制的主门店和被关联门店是否有权限
      if (is_full_array($main_sub_data)) {
        if (is_full_array($copy_department_arr)) {
          foreach ($copy_department_arr as $key => $value) {
            $copy_data = $main_sub_data;
            //判断所选门店是否有该主门店权限，执行添加or更新操作
            $goal_agnecy_data = $this->department_purview_model->get_data_by_main_sub_id(intval($copy_main_dictionary_type_id), intval($value));
            //更新
            if (is_full_array($goal_agnecy_data)) {
              unset($copy_data['id']);
              unset($copy_data['company_id']);
              unset($copy_data['main_dictionary_type_id']);
              unset($copy_data['sub_dictionary_type_id']);
              $deal_result = $this->department_purview_model->update_by_id(intval($goal_agnecy_data['id']), $copy_data);
              //添加
            } else {
              unset($copy_data['id']);
              $copy_data['sub_dictionary_type_id'] = intval($value);
              $deal_result = $this->department_purview_model->flow_data($copy_data);
            }
          }
        }
      } else {
        if (is_full_array($copy_department_arr)) {
          foreach ($copy_department_arr as $key => $value) {
            //判断所选门店是否有该主门店权限，执行添加or更新操作
            $goal_agnecy_data = $this->department_purview_model->get_data_by_main_sub_id(intval($copy_main_dictionary_type_id), intval($value));
            //更新
            if (is_full_array($goal_agnecy_data)) {
              $update_data = $goal_agnecy_data;
              unset($update_data['id']);
              unset($update_data['company_id']);
              unset($update_data['main_dictionary_type_id']);
              unset($update_data['sub_dictionary_type_id']);
              $update_data['func_auth'] = '';
              foreach ($update_data as $k => $v) {
                $update_data[$k] = 0;
              }
              $this->department_purview_model->update_by_id(intval($goal_agnecy_data['id']), $update_data);
              $deal_result = true;
            } else {
              $deal_result = true;
            }
          }
        } else {
          $deal_result = false;
        }
      }
      if ($deal_result) {
        echo 'success';
      } else {
        echo 'success';
      }
    } else {
      echo 'failed';
    }
  }

  //复制权限_2
  public function copy_department_per_2()
  {
    $copy_main_dictionary_type_id = $this->input->post('copy_main_dictionary_type_id_2', TRUE);//主门店id
    $copy_department_arr = $this->input->post('copy_department_2', TRUE);

    if (!empty($copy_main_dictionary_type_id) && is_full_array($copy_department_arr)) {
      //找到对应的主门店的所有关联权限数据
      $main_sub_data = $this->department_purview_model->get_data_by_main_id(intval($copy_main_dictionary_type_id));
      $copy_data = array();
      //判断被复制的主门店和被关联门店是否有权限
      if (is_full_array($main_sub_data)) {
        if (is_full_array($copy_department_arr)) {
          foreach ($copy_department_arr as $k => $v) {
            if (intval($copy_main_dictionary_type_id) != intval($v)) {
              //删除目标主门店的数据
              $this->department_purview_model->delete_data(array('main_dictionary_type_id' => intval($v)));
              //添加数据
              foreach ($main_sub_data as $key => $value) {
                $add_data = $value;
                unset($add_data['id']);
                $add_data['main_dictionary_type_id'] = intval($v);
                if ($add_data['main_dictionary_type_id'] != intval($add_data['sub_dictionary_type_id'])) {
                  $result = $this->department_purview_model->flow_data($add_data);
                }
              }
            }
          }
        }
      } else {
        //删除目标门店作为主门店的所有关联权限数据
        if (is_full_array($copy_department_arr)) {
          foreach ($copy_department_arr as $k => $v) {
            //删除目标主门店的数据
            $result = $this->department_purview_model->delete_data(array('main_dictionary_type_id' => intval($v)));
          }
        }
      }
      if ($result) {
        echo 'success';
      } else {
        echo 'failed';
      }
    } else {
      echo 'failed';
    }
  }

  //设置是否有效开关
  function set_is_effective()
  {
    $this->load->model('department_purview_base_model');
    $department_level = $this->input->post('department_level', TRUE);
    $type = $this->input->post('type', TRUE);
    $main_dictionary_type_id = $this->input->post('main_dictionary_type_id', TRUE);
    $sub_dictionary_type_id = $this->input->post('sub_dictionary_type_id', TRUE);
    //二级门店
    if ('2' == $department_level) {
      $update_data = array(
        'company_id' => intval($this->user_arr['company_id']),
        'main_dictionary_type_id' => intval($main_dictionary_type_id),
        'sub_dictionary_type_id' => intval($sub_dictionary_type_id),
        'is_effective' => intval($type)
      );
      $deal_result = $this->department_purview_base_model->deal_into_data($update_data);
      if (isset($deal_result) && !empty($deal_result)) {
        echo '{"status":"success","msg":"是否有效开关设置成功"}';
      } else {
        echo '{"status":"failed","msg":"是否有效开关设置失败"}';
      }
    } else if ('1' == $department_level) {
      //一级门店，获得该门店下的所有二级门店
      $update_data = array(
        'company_id' => intval($this->user_arr['company_id']),
        'main_dictionary_type_id' => intval($main_dictionary_type_id),
        'is_effective' => intval($type)
      );
      $level_two_department = $this->department_model->get_department_by_one(intval($sub_dictionary_type_id));
      //二级开关
      if (is_full_array($level_two_department)) {
        foreach ($level_two_department as $k => $v) {
          $update_data['sub_dictionary_type_id'] = intval($v['id']);
          $this->department_purview_base_model->deal_into_data($update_data);
        }
      }
      //一级开关
      $update_data['sub_dictionary_type_id'] = intval($sub_dictionary_type_id);
      $deal_result = $this->department_purview_base_model->deal_into_data($update_data);
      if (isset($deal_result) && !empty($deal_result)) {
        echo '{"status":"success","msg":"是否有效开关设置成功"}';
      } else {
        echo '{"status":"failed","msg":"是否有效开关设置失败"}';
      }

    }
    exit;
  }

  //添加类型
  public function add()
  {

    $name = $this->input->post('name');
    $name_abbr = $this->input->post('name_abbr');
    $desc = $this->input->post('desc');
    $father_dictionary_type_id = $this->input->post('father_dictionary_type_id');
    if ($father_dictionary_type_id > 0) {
      $father_dictionary_type = $this->dictionary_type_model->get_by_id($father_dictionary_type_id);
      if ($father_dictionary_type) {

      } else {
        echo '{"status":"failed","msg":"父类型不正确"}';
        return;
      }
    } else if ($father_dictionary_type_id == 0) {

    } else {
      echo '{"status":"failed","msg":"父类型不正确"}';
      return;
    }
    $dictionary_type_id = $this->dictionary_type_model->add_dictionary_type($name, $name_abbr, $desc, 1, $father_dictionary_type_id);
    if (is_int($dictionary_type_id) && $dictionary_type_id > 0) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['department_id'] = $this->user_arr['department_id'];
      $add_log_param['signatory_id'] = $this->user_arr['signatory_id'];
      $add_log_param['signatory_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 28;
      $add_log_param['text'] = '添加数据字典类型' . $dictionary_type_id . '资料';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->signatory_operate_log_model->add_operate_log($add_log_param);

      //初始化数据字典类型权限数据
      //$this->department_model->init_department_purview($this->user_arr['company_id'], $dictionary_type_id);
      //门店审核表
      // $this->department_review_model->insert(array('department_id' => $department_id, 'signatory_id' => $this->user_arr['signatory_id'], 'action' => 1, 'status' => 1, 'create_time' => time()));

      echo '{"status":"success","msg":"添加类型成功"}';
    } else {
      echo '{"status":"failed","msg":"添加类型失败"}';
    }
  }

  //修改门店板块对应门店id传值
  public function edit()
  {
    $id = $this->input->post("id");
    $dictionary_type_info = $this->dictionary_type_model->get_by_id($id);
    $data[] = array();
    echo json_encode($data);
  }

  //修改类型
  public function modify()
  {

    $dictionary_type_id = $this->input->post("dictionary_type_id");

    $name = $this->input->post('name');
    $name_abbr = $this->input->post('name_abbr');
    $desc = $this->input->post('desc');
    $modify_father_dictionary_type_id = $this->input->post('modify_father_dictionary_type_id');
    $update_data = array('name' => $name, 'name_abbr' => $name_abbr, 'desc' => $desc, 'updatetime' => time(), 'dictionary_type_id' => $modify_father_dictionary_type_id);
    $update_result = $this->dictionary_type_model->update_by_dictionary_type_id($update_data, $dictionary_type_id);

    if ($update_result) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['department_id'] = $this->user_arr['department_id'];
      $add_log_param['signatory_id'] = $this->user_arr['signatory_id'];
      $add_log_param['signatory_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 28;
      $add_log_param['text'] = '修改数据字典类型' . $dictionary_type_id . '资料';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->signatory_operate_log_model->add_operate_log($add_log_param);
      echo '{"status":"success","msg":"修改类型成功"}';
    } else {
      echo '{"status":"error","msg":"未作任何修改"}';
    }
  }

  //添加数据字典
  public function add_dictionary()
  {
    $dictionary_type_id = $this->input->post('dictionary_type_id');//门店ID
    $dictionary_type_id = ($dictionary_type_id == 0) ? $this->user_arr['dictionary_type_id'] : $dictionary_type_id;
    $key = $this->input->post('key');
    $name = $this->input->post('name');
    $name_abbr = $this->input->post('name_abbr');
    $desc = $this->input->post('desc');

    if ($dictionary_type_id > 0) {
      $dictionary_type_info = $this->dictionary_type_model->get_by_id($dictionary_type_id);
      if ($dictionary_type_info) {

      } else {
        echo '{"status":"failed","msg":"类型不正确"}';
        return;
      }
    } else if ($dictionary_type_id == 0) {

    } else {
      echo '{"status":"failed","msg":"类型不正确"}';
      return;
    }

    $dictionary_id = $this->dictionary_model->add_dictionary($key, $name, $name_abbr, $desc, 1, $dictionary_type_id);
    if (is_int($dictionary_id) && $dictionary_id > 0) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['department_id'] = $this->user_arr['department_id'];
      $add_log_param['signatory_id'] = $this->user_arr['signatory_id'];
      $add_log_param['signatory_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 28;
      $add_log_param['text'] = '添加' . $name . '资料';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->signatory_operate_log_model->add_operate_log($add_log_param);

      echo '{"status":"success","msg":"添加数据成功"}';
    } else {
      echo '{"status":"failed","msg":"添加数据失败"}';
    }
  }

  //修改数据字典弹出框 传值dictionary_id
  public function modify_dictionary_pop()
  {
    $dictionary_id = $this->input->post('dictionary_id');
    if ($dictionary_id > 0) {
      $dictionary_info = $this->dictionary_model->get_by_id($dictionary_id);
      echo json_encode($dictionary_info);
    }
  }

  // 修改数据字典
  public function modify_dictionary()
  {

    $dictionary_id = $this->input->post("dictionary_id");

    $key = $this->input->post('key');
    $name = $this->input->post('name');
    $name_abbr = $this->input->post('name_abbr');
    $desc = $this->input->post('desc');
    $dictionary_type_id = $this->input->post('dictionary_type_id');
    $update_data = array('key' => $key, 'name' => $name, 'name_abbr' => $name_abbr, 'desc' => $desc, 'updatetime' => time(), 'dictionary_type_id' => $dictionary_type_id);
    $update_result = $this->dictionary_model->update_by_dictionary_id($update_data, $dictionary_id);

    if ($update_result) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['department_id'] = $this->user_arr['department_id'];
      $add_log_param['signatory_id'] = $this->user_arr['signatory_id'];
      $add_log_param['signatory_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 28;
      $add_log_param['text'] = '修改数据字典数据' . $dictionary_id . '资料';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->signatory_operate_log_model->add_operate_log($add_log_param);
      echo '{"status":"success","msg":"修改数据成功"}';
    } else {
      echo '{"status":"error","msg":"未作任何修改"}';
    }
  }

  //删除数据
  public function delete_dictionary()
  {
    $dictionary_id = $this->input->post('dictionary_id');
    $data_view = array();
    $data_view['deleteResult'] = '';
    $data_view['title'] = '数据字典-删除数据';
    $data_view['conf_where'] = 'index';

    //删除类型
    $deleteResult = $this->dictionary_model->update_by_dictionary_id(array('status' => 2, 'updatetime' => time()), $dictionary_id);
    //1 删除成功 0 删除失败
    //删除门店，与该门店相关联的门店数据范围，设为无效。
    if (1 == $deleteResult) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['department_id'] = $this->user_arr['department_id'];
      $add_log_param['signatory_id'] = $this->user_arr['signatory_id'];
      $add_log_param['signatory_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 28;
      $add_log_param['text'] = '删除数据字典类型' . $dictionary_id . '资料';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->signatory_operate_log_model->add_operate_log($add_log_param);

      $data_view['msg'] = "删除成功";
    }

    echo json_encode($data_view);
  }


  //员工信息编辑页面
  public function organization_edit($signatory_id)
  {
    $signatory_user_id = $this->user_arr['signatory_id'];
    $data['edit_signatory_id'] = $signatory_id;

    //菜单生成
    $this->load->model('purview_tab_model');
    $data['user_menu'] = $this->user_menu = $this->purview_tab_model->get_tab('organization', 'index');
    //当前用户的所有信息
    $signatory_user_info = $this->company_employee_model->get_signatory_by_id($signatory_user_id);
    //获取当前帐号权限等级身份
    $level = intval($this->user_arr['role_level']);
    $data['level'] = $level;

    $this->load->model('district_model');//区属模型类
    //编辑的用户的信息
    $signatory_info = $this->company_employee_model->get_signatory_by_id($signatory_id);
    $data['signatory_info'] = $signatory_info;
    //print_r($signatory_info);
    //echo "<hr/>";
    $system_group_id = $this->organization_model->get_system_group_id_by($signatory_info['role_id'], $signatory_id);
    $data['system_group_id'] = $system_group_id;
    //获取基本配置资料
    $config = $this->house_config_model->get_config();
    $data['work_time'] = $config['work_time'];
    //当前被编辑用户名下房客源信息
    $cond_where = "signatory_id =" . $signatory_id;
    $sell_house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
    $rent_house_num = $this->rent_house_model->get_housenum_by_cond($cond_where);
    $data['house_num'] = $sell_house_num + $rent_house_num;
    //客源
    $buy_customer_num = $this->buy_customer_model->get_buynum_by_cond($cond_where);
    $rent_customer_num = $this->rent_customer_model->get_rentnum_by_cond($cond_where);
    $data['customer_num'] = $buy_customer_num + $rent_customer_num;
    //门店信息
    $dictionary_type_info_account = $this->department_model->get_children_by_company_id($signatory_user_info['company_id']);
    $data['dictionary_type_info_account'] = $dictionary_type_info_account;

    //创建帐号时按权限显示部门
      if (in_array($level, array(1, 2, 3, 4, 5, 6))) {
      //总经理，副总经理,总经理助理，业务总监
      $all_dictionary_type_info = array();
      if (is_full_array($dictionary_type_info_account)) {
        foreach ($dictionary_type_info_account as $k => $v) {
          //判断门店下是否有下属门店
          $where_cond = array('department_id' => $v['id']);
          $is_has_department = '0';
          $next_department_data = $this->department_model->get_all_by($where_cond);
          if (is_full_array($next_department_data)) {
            $is_has_department = '1';
          }
          $dictionary_type_info_account[$k]['is_has_department'] = $is_has_department;
        }
        foreach ($dictionary_type_info_account as $k => $v) {
          //一级门店追加
          if (0 == $v['department_id']) {
            $all_dictionary_type_info[] = $v;
          }
        }
        //二级门店追加
        foreach ($dictionary_type_info_account as $k => $v) {
          if ($v['department_id'] != 0) {
            foreach ($all_dictionary_type_info as $key => $val) {
              if ($v['department_id'] == $val['id']) {
                $all_dictionary_type_info[$key]['next_department_data'][] = $v;
              }
            }
          }
        }
      }
      $dictionary_type_info_account = $all_dictionary_type_info;
      } else if (in_array($level, array(7))) {

      //区域经理。只能看到其所在的一级门店和二级门店
      $this_department_info = $this->department_model->get_by_id_one($signatory_info['department_id']);
      $this_father_department_id = $this_department_info[0]['department_id'];

      //判断门店下是否有下属门店
      $where_cond = array('department_id' => $this_department_info[0]['id']);
      $this_department_info[0]['is_has_department'] = '0';
      $next_department_data = $this->department_model->get_all_by($where_cond);
      if (is_full_array($next_department_data)) {
        $this_department_info[0]['is_has_department'] = '1';
      }

      if (isset($this_father_department_id) && $this_father_department_id > 0) {
        //当前门店为二级门店,找到一级门店
        $father_department_data = $this->department_model->get_by_id_one($this_father_department_id);
        $father_department_data[0]['is_has_department'] = '1';
        $father_department_data[0]['next_department_data'] = $this_department_info;
        $dictionary_type_info_account = $father_department_data;
      } else {
        //当前门店为一级门店,找到二级门店
        if (is_full_array($next_department_data)) {
          $this_department_info[0]['next_department_data'] = $next_department_data;
        }
        $dictionary_type_info_account = $this_department_info;
      }

      } else if (in_array($level, array(8, 9, 10, 11))) {
      $dictionary_type_info_account = $this->department_model->get_by_id_one($signatory_info['department_id']);
    }
    $data['dictionary_type_info_account'] = $dictionary_type_info_account;

    $department_info = $this->department_model->get_by_id($signatory_info['department_id']);
    $data['department_info'] = $department_info;
    $data['department_id'] = $signatory_info['department_id'];
    //print_r($department_info);
    //获取区属板块信息
    $data['district'] = $this->district_model->get_distname_by_id($department_info['dist_id']);
    //print_r($data['district']);
    $data['street_arr'] = $this->district_model->get_streetname_by_id($department_info['street_id']);
    //print_r($data['street_arr']);
    //获取权限组列表
    $purview_group = $this->organization_model->get_purview_group();
    //print_r($purview_group);
    foreach ($purview_group as $k => $vo) {

    }
    $data['purview_group'] = $purview_group;

    //页面标题
    $data['page_title'] = '编辑员工帐号';


    //需要加载的css
    $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css'
      . ',mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/cal.css'
      . ',mls_guli/css/v1.0/guest_disk.css,mls_guli/css/v1.0/house_new.css'
      . ',mls_guli/css/v1.0/system_set.css,mls_guli/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
      . 'mls_guli/js/v1.0/backspace.js,mls_guli/js/v1.0/house_list.js,mls_guli/js/v1.0/scrollPic.js,'
      . 'common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/personal_center.js,'
      . 'mls_guli/js/v1.0/cal.js,mls_guli/js/v1.0/shuifei.js');


    $this->view('organization/organization_edit', $data);
  }

  //门店信息编辑页面
  public function department_edit($department_id)
  {
    $department_info = $this->department_model->get_by_id($department_id);
    $data['department_info'] = $department_info;
    //页面标题
    $data['page_title'] = '编辑门店资料页面';
    //需要加载的css
    $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css'
      . ',mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/notice.css,mls_guli/css/v1.0/cal.css'
      . ',mls_guli/css/v1.0/guest_disk.css,mls_guli/css/v1.0/house_new.css'
      . ',mls_guli/css/v1.0/system_set.css,mls_guli/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
      . 'mls_guli/js/v1.0/backspace.js,mls_guli/js/v1.0/house_list.js,mls_guli/js/v1.0/scrollPic.js,'
      . 'mls_guli/js/v1.0/cal.js,mls_guli/js/v1.0/shuifei.js,mls_guli/js/v1.0/personal_center.js');


    $this->view('organization/department_edit', $data);
  }

  public function modify_department_edit()
  {

    $department_id = $this->input->post("department_id");

    $telno = $this->input->post('telno');
    $service_area = $this->input->post('service_area');
    $update_data = array('telno' => $telno, 'service_area' => $service_area);
    $update_result = $this->department_model->update_department_byid($update_data, $department_id);
    if ($update_result) {
      echo '{"status":"success","msg":"修改门店资料成功"}';
    } else {
      echo '{"status":"error","msg":"未作任何修改"}';
    }
  }

  /*
     * 上传图片
     */
  public function upload_photo()
  {
    $filename = $this->input->post('action');
    //echo "<script>alert('".$filename."')</script>";exit;
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
    //echo "<script>alert('".$fileurl."')</script>";exit;
    if ($filename == 'photofile_add') {
      //$signatory_id = $this->input->post('signatory_id');
      $signatory_id = $this->user_arr['signatory_id'];
      $update_data = array('photo' => $fileurl);
      $this->signatory_info_model->update_by_signatory_id($update_data, $signatory_id);
      $date = array('pic' => $fileurl);
      //$this->newhouse_sync_account_base_model->updatedepartment($date,$signatory_id);
      echo "<script>window.parent.changePhoto('" . $fileurl . "')</script>";
    } elseif ($filename == 'photofile_modify') {
      $signatory_id = $this->input->post('signatory_id');
      $update_data = array('photo' => $fileurl);
      $this->signatory_info_model->update_by_signatory_id($update_data, $signatory_id);
      $date = array('pic' => $fileurl);
      //$this->newhouse_sync_account_base_model->updatedepartment($date,$signatory_id);
      $div_id = $this->input->post('div_id');
      echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
    } else {
      $div_id = $this->input->post('div_id');
      echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
    }

  }

  /*
     * 上传图片
     */
  public function upload_photo_department()
  {
    $filename = $this->input->post('action');
    //echo "<script>alert('".$filename."')</script>";exit;
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
    //echo "<script>alert('".$fileurl."')</script>";exit;
    if ($filename == 'photofile_add') {
      $department_id = $this->input->post('department_id');
      $update_data = array('photo' => $fileurl);
      $this->department_model->update_department_byid($update_data, $department_id);
      $date = array('pic' => $fileurl);
      //$this->newhouse_sync_account_base_model->updatedepartment($date,$department_id);
      echo "<script>window.parent.changePhoto('" . $fileurl . "')</script>";
    } elseif ($filename == 'photofile_modify') {
      $department_id = $this->input->post('department_id');
      $update_data = array('photo' => $fileurl);
      $this->department_model->update_department_byid($update_data, $department_id);
      $date = array('pic' => $fileurl);
      //$this->newhouse_sync_account_base_model->updatedepartment($date,$department_id);
      $div_id = $this->input->post('div_id');
      echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
    } else {
      $div_id = $this->input->post('div_id');
      echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
    }

  }

  /*
     * 提交员工编辑修改信息
     */
  public function update_detail()
  {
    $signatory_id = $this->input->post('signatory_id');
    $truename = $this->input->post('truename');
    $sex = $this->input->post('sex');
    $system_group_id = $this->input->post('system_group_id');
    $idno = $this->input->post('idno');
    $joinjob = $this->input->post('joinjob');
    $address = $this->input->post('address');
    $postcode = $this->input->post('postcode');
    $graduate = $this->input->post('graduate');
    $diploma = $this->input->post('diploma');
    $phone = $this->input->post('phone');
    $qq = $this->input->post('qq');
    $work_time = $this->input->post('work_time');
    $email = $this->input->post('email');
    $remark = $this->input->post('remark');
    $department_id = $this->input->post('department_id');
    $department_id_old = $this->input->post('department_id_old');
    $house_num = $this->input->post('house_num');
    $customer_num = $this->input->post('customer_num');
    $is_show_c = $this->input->post('is_show_c');

    $signatory_info = $this->signatory_info_model->get_by_signatory_id($signatory_id);
    $company_id = $signatory_info['company_id'];
    $role_id = $this->organization_model->get_role_id_by($department_id, $system_group_id);
    //根据角色，获得角色level
    $system_role_data = $this->purview_system_group_model->get_by_id($system_group_id);
    if (is_full_array($system_role_data)) {
      $level = intval($system_role_data['level']);
    }
    //查询部门分店消息的条件
    $time = time();
    $cond_where = "company_id = {$company_id} and expiretime >= {$time} ";
    $cond_where .= " and department_id = {$department_id} ";
    //获取员工role_id列表
    $this->signatory_info_model->set_select_fields(array('role_id'));
    $role_ids = $this->signatory_info_model->get_all_by($cond_where, 0, 0);
    $role_id_arr = array();
    foreach ($role_ids as $vo) {
      $role_id_arr[] = $vo['role_id'];
    }
    $role_id_arr = array_unique($role_id_arr);

    //判断是否店长冲突
    $role_id_dz = $this->organization_model->get_role_id_by($company_id, 4);
    if ($signatory_info['role_id'] != $role_id['id'] && $system_group_id == 4) {
      if (in_array($role_id['id'], $role_id_arr)) {
        echo '{"status":"error","msg":"门店已有店长！"}';
        exit;
      }
    }

    //转移门店时转移房客源
    if ($department_id != $department_id_old) {
      $update_arr = array("department_id" => $department_id);
      //房源管理-出售
      $sell_ids = $this->sell_house_model->get_id_by_signatoryid($signatory_id);
      $sell_ids_arr = array();
      foreach ($sell_ids as $vo) {
        $sell_ids_arr[] = $vo['id'];
      }
      if (is_full_array($sell_ids_arr)) {
        $num_sell_update = $this->sell_house_model->update_info_by_ids($sell_ids_arr, $update_arr);
      }
      //房源管理-出租
      $rent_ids = $this->rent_house_model->get_id_by_signatoryid($signatory_id);
      $rent_ids_arr = array();
      foreach ($rent_ids as $vo) {
        $rent_ids_arr[] = $vo['id'];
      }
      if (is_full_array($rent_ids_arr)) {
        $num_rent_update = $this->rent_house_model->update_info_by_ids($rent_ids_arr, $update_arr);
      }
      //客源管理-求购
      $buy_customer_ids = $this->buy_customer_model->get_id_by_signatoryid($signatory_id);
      $buy_customer_ids_arr = array();
      foreach ($buy_customer_ids as $vo) {
        $buy_customer_ids_arr[] = $vo['id'];
      }
      if (is_full_array($buy_customer_ids_arr)) {
        $this->buy_customer_model->update_info_by_id($buy_customer_ids_arr, $update_arr);
      }
      //客源管理-求租
      $rent_customer_ids = $this->rent_customer_model->get_id_by_signatoryid($signatory_id);
      $rent_customer_ids_arr = array();
      foreach ($rent_customer_ids as $vo) {
        $rent_customer_ids_arr[] = $vo['id'];
      }
      if (is_full_array($rent_customer_ids_arr)) {
        $this->rent_customer_model->update_info_by_id($rent_customer_ids_arr, $update_arr);
      }
    }
    $update_data = array('truename' => $truename, 'sex' => $sex, 'role_id' => $role_id['id'], 'role_level' => $level, 'idno' => $idno, 'joinjob' => $joinjob, 'qq' => $qq, 'email' => $email, 'remark' => $remark, 'address' => $address, 'postcode' => $postcode, 'graduate' => $graduate, 'diploma' => $diploma, 'phone' => $phone, 'department_id' => $department_id, 'is_show_c' => $is_show_c, 'work_time' => $work_time);
    $num = $this->signatory_info_model->update_by_signatory_id($update_data, $signatory_id);

    //帐号同步到新房分销
    if ('0' === $sex) {
      $xffx_sex = 1;
    } else if ('1' === $sex) {
      $xffx_sex = 2;
    }
    //if($num_sell_update==$sell_house_num && $num_rent_update==$rent_house_num && $num ==1){
    if ($num == 1) {
      //操作日志
      $old_department_info = $this->department_model->get_by_id(intval($department_id_old));
      $old_department_name = '';
      if (is_full_array($old_department_info)) {
        $old_department_name = $old_department_info['name'];
      }

      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['department_id'] = $this->user_arr['department_id'];
      $add_log_param['signatory_id'] = $this->user_arr['signatory_id'];
      $add_log_param['signatory_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 26;
      $add_log_param['text'] = '修改"' . $old_department_name . '" "' . $truename . '"资料';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->signatory_operate_log_model->add_operate_log($add_log_param);

      $xffxdata = array(
        'ks_id' => $department_id,
        'kcp_id' => $this->user_arr['company_id'],
        'city' => $_SESSION[USER_SESSION_KEY]["city_spell"],
        // 'ag_name' => $truename,
        'sex' => $xffx_sex,
        'update_time' => time(),
      );
      //提交员工编辑修改信息
      //$this->newhouse_sync_account_base_model->updatedepartment($xffxdata,$signatory_id);
      /*
      $url = 'http://adminxffx.fang100.com/fktdata/department';
      $this->load->library('Curl');
      Curl::fktdata($url, $xffxdata);*/
      echo '{"status":"success","msg":"提交信息成功"}';
    } else {
      echo '{"status":"error","msg":"未做任何修改"}';
    }


  }

  //查看门店对应的关联权限节点
  public function get_department_per_node($main_dictionary_type_id = 0, $sub_dictionary_type_id = 0)
  {
    $main_dictionary_type_id = intval($main_dictionary_type_id);
    $sub_dictionary_type_id = intval($sub_dictionary_type_id);
    $result_arr = array();
    if (!empty($main_dictionary_type_id) && !empty($sub_dictionary_type_id)) {
      $this->department_purview_model->set_department_id($this->user_arr['department_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $func_area = $this->department_purview_model->get_func_area_by_main_sub_id($main_dictionary_type_id, $sub_dictionary_type_id);
    }
    if (isset($func_area) && !empty($func_area)) {
      $func_arr = unserialize($func_area);
      $func_result = array();
      foreach ($func_arr as $k => $v) {
        foreach ($v as $key => $value) {
          $func_result[] = $value;
        }
      }
      $result_arr['func_auth'] = $func_result;
    } else {
      $result_arr['func_auth'] = array();
    }
    echo json_encode($result_arr);
    exit;
  }

  /**
   * 删除类型
   * @param int $dictionary_type_id 类型id
   */
  public function delete()
  {
    $dictionary_type_id = $this->input->post('dictionary_type_id');
    $data_view = array();
    $data_view['deleteResult'] = '';
    $data_view['title'] = '数据字典-删除类型';
    $data_view['conf_where'] = 'index';
    $init_dictionary_count = $this->dictionary_model->count_by_dictionary_type_id($dictionary_type_id);
    if ($init_dictionary_count > 0) {
      $data_view['deleteResult'] = 3; //有字典数据
      $data_view['msg'] = "类型下存在字典数据，请先删除字典数据"; //有字典数据
    } else {
      //查询是否挂子类型
      $child_dictionary_type_count = $this->dictionary_type_model->count_by_dictionary_type_id($dictionary_type_id);
      if ($child_dictionary_type_count > 0) {
        $data_view['deleteResult'] = 2; //存在子类型
        $data_view['msg'] = "类型下存在子类型，请先删除子类型";
      } else {
        //删除类型
        $deleteResult = $this->dictionary_type_model->update_by_dictionary_type_id(array('status' => 2, 'updatetime' => time()), $dictionary_type_id);
        //1 删除成功 0 删除失败
        //删除门店，与该门店相关联的门店数据范围，设为无效。
        if (1 == $deleteResult) {
          //操作日志
          $add_log_param = array();
          $add_log_param['company_id'] = $this->user_arr['company_id'];
          $add_log_param['department_id'] = $this->user_arr['department_id'];
          $add_log_param['signatory_id'] = $this->user_arr['signatory_id'];
          $add_log_param['signatory_name'] = $this->user_arr['truename'];
          $add_log_param['type'] = 28;
          $add_log_param['text'] = '删除数据字典类型' . $dictionary_type_id . '资料';
          $add_log_param['from_system'] = 1;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();

          $this->signatory_operate_log_model->add_operate_log($add_log_param);

          $data_view['msg'] = "删除成功";
        }
      }
    }
    echo json_encode($data_view);
  }

}
