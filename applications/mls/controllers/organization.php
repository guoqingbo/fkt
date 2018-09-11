<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Organization extends MY_Controller
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
    $this->load->model('agency_model');
    $this->load->model('broker_info_model');
    $this->load->model('company_employee_model');
    $this->load->model('agency_review_model');
    $this->load->model('organization_model');
    $this->load->model('auth_review_model');
    $this->load->model('api_broker_sincere_model');//信用模块
    $this->load->model('broker_model');
    $this->load->model('permission_company_role_model');
    $this->load->model('district_model');//区属模型类
    $this->load->model('sell_house_model');//房源模型
    $this->load->model('rent_house_model');//客源模型
    $this->load->model('buy_customer_model');//求购模型
    $this->load->model('rent_customer_model');//求租模型
    $this->load->model('permission_company_group_model');//权限组模型
    $this->load->model('permission_system_group_model');//系统角色
    $this->load->model('agency_permission_node_model');//门店权限节点
    $this->load->model('agency_permission_model');//门店关联权限
    //$this->load->model('newhouse_sync_account_base_model');
    $this->load->model('house_config_model');
    $this->load->model('operate_log_model');

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


  public function index($agency_id = "", $b_id = '')
  {
    $view_type = 'index';//用于区分首页还是搜索页
    if ($this->input->get('view_type')) {
      $view_type = $this->input->get('view_type');
    }
    $search_agency_data = array();
    $search_result_num = 0;
    $agency_id_str = $this->input->get('search_agency_id');
    if ('search' == $view_type && !empty($agency_id_str)) {
      $agency_id_str = trim($agency_id_str, ',');
      $agency_id_arr = explode(',', $agency_id_str);
      $search_result_num = count($agency_id_arr);
      if (!empty($agency_id_str)) {
        $search_agency_data = $this->agency_model->get_all_by_agency_id($agency_id_str);
      }
    }
    //判断是否有下属门店
    if (is_full_array($search_agency_data)) {
      foreach ($search_agency_data as $k => $v) {
        //判断门店下是否有下属门店
        $where_cond = array('agency_id' => $v['agency_id']);
        $is_has_agency = '0';
        $next_agency_data = $this->agency_model->get_all_by($where_cond);
        if (is_full_array($next_agency_data)) {
          $is_has_agency = '1';
        }
        $search_agency_data[$k]['is_has_agency'] = $is_has_agency;
      }
    }

    $data['search_result_num'] = $search_result_num;
    $data['view_type'] = $view_type;
    $data['search_agency_data'] = $search_agency_data;
    $data['search_agency_id_str'] = $agency_id_str;
    if (!$b_id) {
      $broker_id = $this->user_arr['broker_id'];
    } else {
      $broker_id = $b_id;
    }
    $data['user_menu'] = $this->user_menu;
    //当前用户的所有信息
    $broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
    //获取当前帐号权限等级身份
    $level = intval($this->user_arr['role_level']);
    $data['level'] = $level;
    $company_id = $broker_info['company_id'];//所属公司id
    $this->agency_model->set_select_fields(array('id', 'name', 'photo'));
    $company = $this->agency_model->get_by_id($company_id);//所属总公司的信息
    //print_r($company);
    $this->agency_model->set_select_fields(array('name', 'address', 'id', 'dist_id', 'street_id', 'telno', 'agency_id'));
    if (empty($agency_id)) {
      $agency_id = $broker_info['agency_id'];
    }
    $agency_info = $this->agency_model->get_by_id($agency_id);

    //print_r($agency_info);
    //获取当前所属等级最高公司的相关联系信息
    $data['store_name'] = $agency_info['name'];
    //$data['telno'] = $agency_info['telno'];
    $data['address'] = $agency_info['address'];
    //$data['linkman'] = $agency_info['linkman'];
    $data['photo'] = $company['photo'];
    $data['company_name'] = $company['name'];
    $data['agency_id'] = $agency_id;

    //当前公司下所有的一级二级门店
    $company_info = $this->agency_model->get_children_by_company_id($company_id);
    //门店数据重构，一级门店下面排列所属于它的二级门店
    //当前公司下所有门店
    $all_company_info = array();
    if (is_full_array($company_info)) {
      //查找当前操作门店，对其他门店关联权限是否有效
      $this->load->model('agency_permission_base_model');
      foreach ($company_info as $k => $v) {
        $per_data = $this->agency_permission_base_model->get_data_by_main_sub_id(intval($agency_id), intval($v['id']));
        if (is_full_array($per_data)) {
          $company_info[$k]['is_effective'] = intval($per_data['is_effective']);
        } else {
          $company_info[$k]['is_effective'] = 0;
        }
      }

      foreach ($company_info as $k => $v) {
        //判断门店下是否有下属门店
        $where_cond = array('agency_id' => $v['id']);
        $is_has_agency = '0';
        $next_agency_data = $this->agency_model->get_all_by($where_cond);
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

    //获取门店关联权限节点
    $agency_father_node = $this->agency_permission_node_model->get_all_by_modules();
    $agency_children_node = $this->agency_permission_node_model->get_all();
    if (is_full_array($agency_father_node) && is_full_array($agency_children_node)) {
      foreach ($agency_father_node as $k => $v) {
        $pid = $v['id'];
        foreach ($agency_children_node as $key => $value) {
          if ($value['mid'] == $pid) {
            $agency_father_node[$k]['child_node'][] = $value;
          }
        }
      }
    }
    $data['agency_father_node'] = $agency_father_node;

    //根据权限获取公司名下的所有子公司信息
    if (in_array($level, array(1, 2, 3, 4))) {
      //总经理，副总经理。看到公司下所有的片区、门店
      $new_company_info = $all_company_info;
    } else if (in_array($level, array(5))) {
      //区域经理。只能看到其所在的一级门店和二级门店
      $this_agency_info = $this->agency_model->get_by_id_one($broker_info['agency_id']);
      $this_father_agency_id = $this_agency_info[0]['agency_id'];

      //判断门店下是否有下属门店
      $where_cond = array('agency_id' => $this_agency_info[0]['id']);
      $this_agency_info[0]['is_has_agency'] = '0';
      $next_agency_data = $this->agency_model->get_all_by($where_cond);
      if (is_full_array($next_agency_data)) {
        $this_agency_info[0]['is_has_agency'] = '1';
      }

      if (isset($this_father_agency_id) && $this_father_agency_id > 0) {
        //当前门店为二级门店,找到一级门店
        $father_agency_data = $this->agency_model->get_by_id_one($this_father_agency_id);
        $father_agency_data[0]['is_has_agency'] = '1';
        $father_agency_data[0]['next_agency_data'] = $this_agency_info;
        $new_company_info = $father_agency_data;
      } else {
        //当前门店为一级门店,找到二级门店
        if (is_full_array($next_agency_data)) {
          $this_agency_info[0]['next_agency_data'] = $next_agency_data;
        }
        $new_company_info = $this_agency_info;
      }
    } else if (in_array($level, array(6, 7))) {
      //店长,店务秘书，只能看到当前门店
      $this_agency_info = $this->agency_model->get_by_id_one($broker_info['agency_id']);
      //判断门店下是否有下属门店
      $where_cond = array('agency_id' => $this_agency_info[0]['id']);
      $is_has_agency = '0';
      $next_agency_data = $this->agency_model->get_all_by($where_cond);
      if (is_full_array($next_agency_data)) {
        $is_has_agency = '1';
      }
      $this_agency_info[0]['is_has_agency'] = $is_has_agency;
      $new_company_info = $this_agency_info;
    } else {
      $new_company_info = "";
    }

    //创建帐号时按权限显示公司
    if (in_array($level, array(1, 2, 3, 4))) {
      //总经理，副总经理
      $company_info_account = $new_company_info;
    } else if (in_array($level, array(5))) {
      //片区经理
      $company_info_account = $new_company_info;
    } else {
      $company_info_account = $this->agency_model->get_by_id_one($broker_info['agency_id']);
    }
    $data['company_info_account'] = $company_info_account;

    $data['company_info'] = $new_company_info;

    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $this->_init_pagination($page);
    $time = time();
    //查询消息的条件
    $cond_where = " where company_id = {$company_id} and expiretime >= {$time} and status = 1 ";
    //分店链接传值组成的查询条件
    if ($agency_id) {
      $now_agency_id = $agency_id;
      $cond_where .= " and agency_id = {$agency_id} ";
    } else {
      $now_agency_id = $agency_id;
      //$cond_where .= " and agency_id = " . $agency_info['id'];
    }
    $data['now_agency_id'] = $now_agency_id;
    $data['now_father_agency_id'] = $agency_info['agency_id'];
    $cond_where_agency = $cond_where;
    if ($b_id) {
      $cond_where .= ' and broker_id = ' . $broker_id;
    }
    //符合条件的总行数
    $this->_total_count = $this->organization_model->count_by($cond_where);
    //排序
    $cond_where = $cond_where . " order by id DESC ";

    //获取员工列表内容
    $broker_all_info = $this->organization_model->get_all_by($cond_where, $this->_offset, $this->_limit);
    $broker_all_info_agency = $this->organization_model->get_all_by($cond_where_agency, 0, 0);
    //print_r($broker_all_info);

    //获取全部权限等级
    foreach ($broker_all_info as $vo) {
      $group_arr[] = $this->organization_model->get_system_group_id_by($vo['role_id'], $vo['broker_id']);
    }
    //获取门店负责人信息
    foreach ($broker_all_info_agency as $vo) {
      $manager_ground_id = $this->permission_agency_group_model->get_system_group_id($vo['role_id']);
      //print_r($manager_ground_id);
      if ($manager_ground_id == 4) {
        $data['telno'] = $vo['phone'];
        $data['linkman'] = $vo['truename'];
      }
    }

    if (is_full_array($group_arr)) {
      foreach ($group_arr as $k => $v) {
        //获得角色等级
        $system_role_data = $this->permission_system_group_model->get_one_by(array('id' => intval($v['system_group_id'])));
        $group_arr[$k]['level'] = $system_role_data['level'];
      }
    }
    //echo "<hr/>";
    $data['group_arr'] = $group_arr;
    //print_r($broker_all_info);
    $data['broker_all_info'] = $broker_all_info;
    //获取权限组列表
    $permission_group = $this->organization_model->get_permission_group();
    //print_r($permission_group);
    $data['permission_group'] = $permission_group;


    //获取信用等级信息
    $trust_level_info = $this->organization_model->get_trust_level_info();
    //print_r($trust_level_info);
    $data['trust_level_info'] = $trust_level_info;

    //获取认证信息
    $ident_info = $this->organization_model->get_foreach($broker_all_info, "type = 1 ");

    $data['ident_info'] = $ident_info;
    // $data['quali_info'] = $quali_info;

    //添加区属板块
    $data['district'] = $this->district_model->get_district();
    $street = $this->district_model->get_street();
    //print_r($street);
    $data['street'] = $street;

    //当前公司下的一级门店
    $data['agency_oneleval_data'] = $this->agency_model->get_agency_1_by_company_id($company_id);
    if (is_full_array($data['agency_oneleval_data'])) {
      foreach ($data['agency_oneleval_data'] as $key => $value) {
        if ($value['id'] == $agency_id) {
          unset($data['agency_oneleval_data'][$key]);
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
    $data['page_title'] = '组织架构';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/cal.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/house_new.css'
      . ',mls/css/v1.0/personal_center.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js'
    );


    $this->view('organization/organization_show', $data);
  }

  //设置门店数据操作范围
  public function set_agency_per()
  {
    $company_id = $this->user_arr['company_id'];//公司id
    $main_agency_id = $this->input->post('main_agency_id', TRUE);//主门店id
    $sub_agency_id = $this->input->post('sub_agency_id', TRUE);//被关联门店id
    $child_node_arr = $this->input->post('child_node_id', TRUE);//权限节点
    //根据权限节点，重构数据，func_auth
    $this->load->model('agency_permission_node_base_model');
    $func_auth = $this->agency_permission_node_base_model->get_node_serialize_by_child_node($child_node_arr);

    $is_view_house = $is_house_match = $is_house_share_tasks = $is_house_allocate = $is_view_customer = $is_customer_match = $is_customer_share_tasks =
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
      'main_agency_id' => intval($main_agency_id),
      'sub_agency_id' => intval($sub_agency_id),
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
    if (!empty($main_agency_id) && !empty($sub_agency_id)) {
      $this->load->model('agency_permission_base_model');
      $deal_result = $this->agency_permission_base_model->deal_into_data($update_data);
    }
    if (isset($deal_result) && (1 === $deal_result || 0 === $deal_result)) {
      echo '{"status":"success","msg":"关联门店设置成功"}';
    } else {
      echo '{"status":"failed","msg":"关联门店设置失败"}';
    }
    exit;
  }

  //复制权限
  public function copy_agency_per()
  {
    $copy_main_agency_id = $this->input->post('copy_main_agency_id', TRUE);//主门店id
    $copy_sub_agency_id = $this->input->post('copy_sub_agency_id', TRUE);//被关联门店id
    $copy_agency_arr = $this->input->post('copy_agency', TRUE);
    if (!empty($copy_main_agency_id) && !empty($copy_sub_agency_id) && is_full_array($copy_agency_arr)) {
      //找到对应的主门店、被关联门店的权限节点
      $main_sub_data = $this->agency_permission_model->get_data_by_main_sub_id(intval($copy_main_agency_id), intval($copy_sub_agency_id));
      $copy_data = array();
      //判断被复制的主门店和被关联门店是否有权限
      if (is_full_array($main_sub_data)) {
        if (is_full_array($copy_agency_arr)) {
          foreach ($copy_agency_arr as $key => $value) {
            $copy_data = $main_sub_data;
            //判断所选门店是否有该主门店权限，执行添加or更新操作
            $goal_agnecy_data = $this->agency_permission_model->get_data_by_main_sub_id(intval($copy_main_agency_id), intval($value));
            //更新
            if (is_full_array($goal_agnecy_data)) {
              unset($copy_data['id']);
              unset($copy_data['company_id']);
              unset($copy_data['main_agency_id']);
              unset($copy_data['sub_agency_id']);
              $deal_result = $this->agency_permission_model->update_by_id(intval($goal_agnecy_data['id']), $copy_data);
              //添加
            } else {
              unset($copy_data['id']);
              $copy_data['sub_agency_id'] = intval($value);
              $deal_result = $this->agency_permission_model->replace_data($copy_data);
            }
          }
        }
      } else {
        if (is_full_array($copy_agency_arr)) {
          foreach ($copy_agency_arr as $key => $value) {
            //判断所选门店是否有该主门店权限，执行添加or更新操作
            $goal_agnecy_data = $this->agency_permission_model->get_data_by_main_sub_id(intval($copy_main_agency_id), intval($value));
            //更新
            if (is_full_array($goal_agnecy_data)) {
              $update_data = $goal_agnecy_data;
              unset($update_data['id']);
              unset($update_data['company_id']);
              unset($update_data['main_agency_id']);
              unset($update_data['sub_agency_id']);
              $update_data['func_auth'] = '';
              foreach ($update_data as $k => $v) {
                $update_data[$k] = 0;
              }
              $this->agency_permission_model->update_by_id(intval($goal_agnecy_data['id']), $update_data);
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
  public function copy_agency_per_2()
  {
    $copy_main_agency_id = $this->input->post('copy_main_agency_id_2', TRUE);//主门店id
    $copy_agency_arr = $this->input->post('copy_agency_2', TRUE);

    if (!empty($copy_main_agency_id) && is_full_array($copy_agency_arr)) {
      //找到对应的主门店的所有关联权限数据
      $main_sub_data = $this->agency_permission_model->get_data_by_main_id(intval($copy_main_agency_id));
      $copy_data = array();
      //判断被复制的主门店和被关联门店是否有权限
      if (is_full_array($main_sub_data)) {
        if (is_full_array($copy_agency_arr)) {
          foreach ($copy_agency_arr as $k => $v) {
            if (intval($copy_main_agency_id) != intval($v)) {
              //删除目标主门店的数据
              $this->agency_permission_model->delete_data(array('main_agency_id' => intval($v)));
              //添加数据
              foreach ($main_sub_data as $key => $value) {
                $add_data = $value;
                unset($add_data['id']);
                $add_data['main_agency_id'] = intval($v);
                if ($add_data['main_agency_id'] != intval($add_data['sub_agency_id'])) {
                  $result = $this->agency_permission_model->replace_data($add_data);
                }
              }
            }
          }
        }
      } else {
        //删除目标门店作为主门店的所有关联权限数据
        if (is_full_array($copy_agency_arr)) {
          foreach ($copy_agency_arr as $k => $v) {
            //删除目标主门店的数据
            $result = $this->agency_permission_model->delete_data(array('main_agency_id' => intval($v)));
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
    $this->load->model('agency_permission_base_model');
    $agency_level = $this->input->post('agency_level', TRUE);
    $type = $this->input->post('type', TRUE);
    $main_agency_id = $this->input->post('main_agency_id', TRUE);
    $sub_agency_id = $this->input->post('sub_agency_id', TRUE);
    //二级门店
    if ('2' == $agency_level) {
      $update_data = array(
        'company_id' => intval($this->user_arr['company_id']),
        'main_agency_id' => intval($main_agency_id),
        'sub_agency_id' => intval($sub_agency_id),
        'is_effective' => intval($type)
      );
      $deal_result = $this->agency_permission_base_model->deal_into_data($update_data);
      if (isset($deal_result) && !empty($deal_result)) {
        echo '{"status":"success","msg":"是否有效开关设置成功"}';
      } else {
        echo '{"status":"failed","msg":"是否有效开关设置失败"}';
      }
    } else if ('1' == $agency_level) {
      //一级门店，获得该门店下的所有二级门店
      $update_data = array(
        'company_id' => intval($this->user_arr['company_id']),
        'main_agency_id' => intval($main_agency_id),
        'is_effective' => intval($type)
      );
      $level_two_agency = $this->agency_model->get_agency_by_one(intval($sub_agency_id));
      //二级开关
      if (is_full_array($level_two_agency)) {
        foreach ($level_two_agency as $k => $v) {
          $update_data['sub_agency_id'] = intval($v['id']);
          $this->agency_permission_base_model->deal_into_data($update_data);
        }
      }
      //一级开关
      $update_data['sub_agency_id'] = intval($sub_agency_id);
      $deal_result = $this->agency_permission_base_model->deal_into_data($update_data);
      if (isset($deal_result) && !empty($deal_result)) {
        echo '{"status":"success","msg":"是否有效开关设置成功"}';
      } else {
        echo '{"status":"failed","msg":"是否有效开关设置失败"}';
      }

    }
    exit;
  }

  //添加门店
  public function add()
  {
    $city = $_SESSION[USER_SESSION_KEY]["city_spell"];//城市
    //获得当前经纪人所属公司名
    $agency_data = $this->agency_model->get_one_by(array('id' => $this->user_arr['agency_id']));
    if (is_full_array($agency_data)) {
      $company_id = $agency_data['company_id'];
      $company_data = $this->agency_model->get_one_by(array('id' => $company_id));
      $company_name = $company_data['name'];
    }

    $name = $this->input->post('name');
    //$agency_info = $this->agency_model->get_one_by('name = "'.$name.'"');
    //if(is_full_array($agency_info)){
    //    echo '{"status":"failed","msg":"门店名称已存在，请重新输入"}';exit;
    //}
    $telno = $this->input->post('telno');
    $address = $this->input->post('address');
    $dist_id = $this->input->post('dist_id');
    $street_id = $this->input->post('street_id');
    $agency_type = $this->input->post('agency_type');
    $father_agency_id = $this->input->post('father_agency_id');
    $agency_id = $this->agency_model->add_agency($dist_id, $street_id, $name, $telno, $address, $this->user_arr['company_id'], 0, 1, $city, $father_agency_id, $agency_type);
    if (is_int($agency_id) && $agency_id > 0) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 28;
      $add_log_param['text'] = '添加' . $name . '资料';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);

      //初始化门店权限数据
      $this->agency_model->init_agency_permission($this->user_arr['company_id'], $agency_id);
      //门店审核表
      $this->agency_review_model->insert(array('agency_id' => $agency_id, 'broker_id' => $this->user_arr['broker_id'], 'action' => 1, 'status' => 1, 'create_time' => time()));

      echo '{"status":"success","msg":"添加门店成功"}';
    }
  }

  //修改门店板块对应门店id传值
  public function edit()
  {
    $id = $this->input->post("id");
    $agency_info = $this->agency_model->get_by_id($id);
    $street = $this->district_model->get_street_bydist($agency_info['dist_id']);
    $data['street'] = $street;
    $data['street_id'] = $agency_info['street_id'];
    echo json_encode($data);
  }

  //修改门店
  public function modify()
  {

    $agency_id = $this->input->post("agency_id");

    $name = $this->input->post('name');
    $telno = $this->input->post('telno');
    $address = $this->input->post('address');
    $dist_id = $this->input->post('dist_id');
    $street_id = $this->input->post('street_id');
    $modify_father_agency_id = $this->input->post('modify_father_agency_id');
    $update_data = array('dist_id' => $dist_id, 'street_id' => $street_id, 'name' => $name, 'telno' => $telno, 'address' => $address, 'agency_id' => $modify_father_agency_id);
    $update_result = $this->agency_model->update_agency_byid($update_data, $agency_id);
    if ($update_result) {
      //帐号同步到新房分销
      $xffxdata = array(
        'city' => $_SESSION[USER_SESSION_KEY]["city_spell"],
        'store_id' => $agency_id,
        'storeName' => $name,
        'area_id' => $dist_id,
        'address' => $address,
        'special' => 0,
        'create_time' => time(),
        'update_time' => time(),
        'isdel' => 0,
      );
      //11
      $area = $this->district_model->get_distname_by_id($dist_id);
      //$this->newhouse_sync_account_base_model->updatestore($xffxdata,$area);
      /*
      $url = 'http://adminxffx.fang100.com/fktdata/update_store';
      $this->load->library('Curl');
      Curl::fktdata($url, $xffxdata);*/

      echo '{"status":"success","msg":"修改门店成功"}';
    } else {
      echo '{"status":"error","msg":"未作任何修改"}';
    }
  }

  //添加帐号
  public function add_account()
  {
    $agency_id = $this->input->post('agency_id');//门店ID
    $agency_id = ($agency_id == 0) ? $this->user_arr['agency_id'] : $agency_id;
    $truename = $this->input->post('truename');
    $phone = $this->input->post('phone');
    $password = $this->input->post('password');
    $code = $this->input->post('code');

    $this->load->model('broker_sms_model');
    $this->broker_sms_model->type = 'register';
    $code_id = $this->broker_sms_model->get_by_phone_validcode($phone, $code);//判断验证码是否输入正确
    if (!$code_id && $code != '0000') {
      echo '{"status":"failed","msg":"验证码输入不正确"}';
      exit;
    }

    $city_id = $this->user_arr['city_id'];

    $insert_id = $this->broker_model->add_user($city_id, $phone, $password);//插入公表返回id
    if (is_int($insert_id) && $insert_id > 0) {
      //获取权限
      $this->load->model('permission_agency_group_model');
      $per_where_cond = array('agency_id' => $agency_id);
      $per_where_cond['system_group_id'] = 8;
      $role_info = $this->permission_agency_group_model->get_one_by($per_where_cond);
      $role_id = $role_info['id'] > 0 ? $role_info['id'] : 1;
      //根据角色，获得角色level
      $system_role_data = $this->permission_system_group_model->get_by_id($per_where_cond['system_group_id']);
      if (is_full_array($system_role_data)) {
        $level = intval($system_role_data['level']);
      }
      $city = $this->user_arr['city_spell'];
      $this->broker_info_model->new_init_broker($insert_id, $phone, $agency_id, $role_id, $level, $truename, $city);
      $this->broker_sms_model->validcode_set_esta($code_id);//把验证过后的验证设为已验证状态
      /*
         //帐号同步到新房分销
         $xffxdata = array(
             'ag_id' => $insert_id,
             'ks_id' => $agency_id,
             'kcp_id' => $this->user_arr['company_id'],
             'ag_name' => $truename,
             'ag_phone' => $phone,
             'city' => $this->user_arr['city_spell'],
             'password' => md5($password),
             'sex' => 0,
             'ag_status' => 1,
             'addtime' => time(),
             'update_time' => time(),
         );

          'ag_dist' => $area_id

         $this->newhouse_sync_account_base_model->agency($xffxdata);

         $url = 'http://adminxffx.fang100.com/fktdata/agency';
         $this->load->library('Curl');
         Curl::fktdata($url, $xffxdata);*/

      echo '{"status":"success","msg":"添加经纪人成功"}';
    } else {
      echo '{"status":"failed","msg":"添加经纪人失败"}';
    }

  }

  //重置密码弹出框传值broker_id
  public function modify_password_pop()
  {
    $broker_id = $this->input->post('broker_id');
    echo $broker_id;
  }

  //重置密码
  public function modify_password()
  {
    //$this_user = $this->user_arr;
    //$broker_id = $this_user['broker_id'];
      $broker_id = intval($this->input->post('broker_id'));
    //$old_password = $this->input->post('old_password');
    $new_password = $this->input->post('new_password');
    $equal_password = $this->input->post('equal_password');
    $modify_data = $this->organization_model->modify_password($broker_id, $new_password, $equal_password);
    //操作日志
    $broker_info = $this->broker_info_model->get_by_broker_id(intval($broker_id));
    $old_agency_info = $this->agency_model->get_by_id(intval($broker_info['agency_id']));
    $old_agency_name = '';
    if (is_full_array($old_agency_info)) {
      $old_agency_name = $old_agency_info['name'];
    }
    $add_log_param = array();
    $add_log_param['company_id'] = $this->user_arr['company_id'];
    $add_log_param['agency_id'] = $this->user_arr['agency_id'];
    $add_log_param['broker_id'] = $this->user_arr['broker_id'];
    $add_log_param['broker_name'] = $this->user_arr['truename'];
    $add_log_param['type'] = 44;
    $add_log_param['text'] = '修改"' . $old_agency_name . '" "' . $broker_info['truename'] . '"密码';
    $add_log_param['from_system'] = 1;
    $add_log_param['from_ip'] = get_ip();
    $add_log_param['mac_ip'] = '127.0.0.1';
    $add_log_param['from_host_name'] = '127.0.0.1';
    $add_log_param['hardware_num'] = '测试硬件序列号';
    $add_log_param['time'] = time();

    $this->operate_log_model->add_operate_log($add_log_param);
      echo json_encode(array("result" => $modify_data));
  }

  //注销帐号
  public function cancel_account()
  {
    $broker_id = $this->input->post('broker_id');
    $broker_id_pop = $this->input->post('broker_id_pop');
    if ($broker_id_pop) {
      $cond_where = "broker_id =" . $broker_id_pop;//echo $cond_where;die();
      $broker_info = $this->broker_info_model->get_by_broker_id($broker_id_pop);
      //房源数量
      $sell_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
      $rent_num = $this->rent_house_model->get_housenum_by_cond($cond_where);
      $house_num = intval($sell_num) + intval($rent_num);
      //客源数量
      $buy_customer_num = $this->buy_customer_model->get_buynum_by_cond($cond_where);
      $rent_customer_num = $this->rent_customer_model->get_rentnum_by_cond($cond_where);
      $customer_num = intval($buy_customer_num) + intval($rent_customer_num);
      $data['id'] = $broker_id_pop;
      $data['house_num'] = $house_num;
      $data['customer_num'] = $customer_num;
      $data['cancel_name'] = $broker_info['truename'];
      echo json_encode($data);
    } else if ($broker_id) {
        //注销账号，推送金品生活app同步注销;
        $this->load->library('Curl');
        //生成加密签名
        $this->load->library('DES3');
        $time = time();
        $sign = $this->des3->encrypt($broker_id . $time);

        $url = JINPIN_URL . '/user/brokerLogout';
        $params = [
            'brokerId' => $broker_id,
            'time' => $time,
            'sign' => $sign
        ];
        $output = $this->curl->httpRequstPost($url, http_build_query($params));
        $output = json_decode($output, true);
        $text = '金品生活同步注销失败';
        if ($output['success']) {
            //添加推送日志
            $text = '金品生活同步注销成功 ' . $output;
        } else {
            echo $text;
            return false;
        }
      //认证失效
      $auth_review_info = $this->auth_review_model->get_new("broker_id =" . $broker_id, 0, 1);
      $this->auth_review_model->update_by_id(array('status' => 4), $auth_review_info['id']);
      //个人挂靠公司及权限修改
      $broker_info_update_data = array('agency_id' => 0, 'company_id' => 0, 'role_id' => 1, 'group_id' => 1);
      $house_info_update_data = array('agency_id' => 0, 'company_id' => 0);
      $cond_where = "broker_id =" . $broker_id;
      //名下房客源查询修改
      $sell_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
      $rent_num = $this->rent_house_model->get_housenum_by_cond($cond_where);
      if ($sell_num != 0) {
        $sell_ids = $this->sell_house_model->get_id_by_brokerid($broker_id);
        //print_r($sell_ids);
        $sell_ids_arr = array();
        foreach ($sell_ids as $vo) {
          $sell_ids_arr[] = $vo['id'];
        }
        $num_sell_update = $this->sell_house_model->update_info_by_ids($sell_ids_arr, $house_info_update_data);
      }
      if ($rent_num != 0) {
        $rent_ids = $this->rent_house_model->get_id_by_brokerid($broker_id);
        //print_r($rent_ids);
        $rent_ids_arr = array();
        foreach ($rent_ids as $vo) {
          $rent_ids_arr[] = $vo['id'];
        }
        $num_rent_update = $this->rent_house_model->update_info_by_ids($rent_ids_arr, $house_info_update_data);
      }
      $num = $this->broker_info_model->update_by_broker_id($broker_info_update_data, $broker_id);
      echo $num;



      //操作日志
      $broker_info = $this->broker_info_model->get_by_broker_id(intval($broker_id));
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 27;
      $add_log_param['text'] = '注销"' . $broker_info['truename'] . '"帐号';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);

//      $xffxdata = array(
//        'ks_id' => 0,
//        'kcp_id' => 0,
//        'ag_status' => 1,
//        'update_time' => time()
//      );
      //22
      //$this->newhouse_sync_account_base_model->updateagency($xffxdata,$broker_id);
      /*
      $url = 'http://adminxffx.fang100.com/fktdata/logoutagency';
      $this->load->library('Curl');
      Curl::fktdata($url, $xffxdata);*/
    }
  }


  //员工信息编辑页面
  public function organization_edit($broker_id)
  {
    $broker_user_id = $this->user_arr['broker_id'];
    $data['edit_broker_id'] = $broker_id;

    //菜单生成
    $this->load->model('permission_tab_model');
    $data['user_menu'] = $this->user_menu = $this->permission_tab_model->get_tab('organization', 'index');
    //当前用户的所有信息
    $broker_user_info = $this->company_employee_model->get_broker_by_id($broker_user_id);
    //获取当前帐号权限等级身份
    $level = intval($this->user_arr['role_level']);
    $data['level'] = $level;

    $this->load->model('district_model');//区属模型类
    //编辑的用户的信息
    $broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
    $data['broker_info'] = $broker_info;
    //print_r($broker_info);
    //echo "<hr/>";
    $system_group_id = $this->organization_model->get_system_group_id_by($broker_info['role_id'], $broker_id);
    $data['system_group_id'] = $system_group_id;
    //获取基本配置资料
    $config = $this->house_config_model->get_config();
    $data['work_time'] = $config['work_time'];
    //当前被编辑用户名下房客源信息
    $cond_where = "broker_id =" . $broker_id;
    $sell_house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
    $rent_house_num = $this->rent_house_model->get_housenum_by_cond($cond_where);
    $data['house_num'] = $sell_house_num + $rent_house_num;
    //客源
    $buy_customer_num = $this->buy_customer_model->get_buynum_by_cond($cond_where);
    $rent_customer_num = $this->rent_customer_model->get_rentnum_by_cond($cond_where);
    $data['customer_num'] = $buy_customer_num + $rent_customer_num;
    //门店信息
    $company_info_account = $this->agency_model->get_children_by_company_id($broker_user_info['company_id']);
    $data['company_info_account'] = $company_info_account;

    //创建帐号时按权限显示公司
    if (in_array($level, array(1, 2, 3, 4))) {
      //总经理，副总经理,总经理助理，业务总监
      $all_company_info = array();
      if (is_full_array($company_info_account)) {
        foreach ($company_info_account as $k => $v) {
          //判断门店下是否有下属门店
          $where_cond = array('agency_id' => $v['id']);
          $is_has_agency = '0';
          $next_agency_data = $this->agency_model->get_all_by($where_cond);
          if (is_full_array($next_agency_data)) {
            $is_has_agency = '1';
          }
          $company_info_account[$k]['is_has_agency'] = $is_has_agency;
        }
        foreach ($company_info_account as $k => $v) {
          //一级门店追加
          if (0 == $v['agency_id']) {
            $all_company_info[] = $v;
          }
        }
        //二级门店追加
        foreach ($company_info_account as $k => $v) {
          if ($v['agency_id'] != 0) {
            foreach ($all_company_info as $key => $val) {
              if ($v['agency_id'] == $val['id']) {
                $all_company_info[$key]['next_agency_data'][] = $v;
              }
            }
          }
        }
      }
      $company_info_account = $all_company_info;
    } else if (in_array($level, array(5))) {

      //区域经理。只能看到其所在的一级门店和二级门店
      $this_agency_info = $this->agency_model->get_by_id_one($broker_info['agency_id']);
      $this_father_agency_id = $this_agency_info[0]['agency_id'];

      //判断门店下是否有下属门店
      $where_cond = array('agency_id' => $this_agency_info[0]['id']);
      $this_agency_info[0]['is_has_agency'] = '0';
      $next_agency_data = $this->agency_model->get_all_by($where_cond);
      if (is_full_array($next_agency_data)) {
        $this_agency_info[0]['is_has_agency'] = '1';
      }

      if (isset($this_father_agency_id) && $this_father_agency_id > 0) {
        //当前门店为二级门店,找到一级门店
        $father_agency_data = $this->agency_model->get_by_id_one($this_father_agency_id);
        $father_agency_data[0]['is_has_agency'] = '1';
        $father_agency_data[0]['next_agency_data'] = $this_agency_info;
        $company_info_account = $father_agency_data;
      } else {
        //当前门店为一级门店,找到二级门店
        if (is_full_array($next_agency_data)) {
          $this_agency_info[0]['next_agency_data'] = $next_agency_data;
        }
        $company_info_account = $this_agency_info;
      }

    } else if (in_array($level, array(6, 7, 8, 9, 10, 11))) {
      $company_info_account = $this->agency_model->get_by_id_one($broker_info['agency_id']);
    }
    $data['company_info_account'] = $company_info_account;

    $agency_info = $this->agency_model->get_by_id($broker_info['agency_id']);
    $data['agency_info'] = $agency_info;
    $data['agency_id'] = $broker_info['agency_id'];
    //print_r($agency_info);
    //获取区属板块信息
    $data['district'] = $this->district_model->get_distname_by_id($agency_info['dist_id']);
    //print_r($data['district']);
    $data['street_arr'] = $this->district_model->get_streetname_by_id($agency_info['street_id']);
    //print_r($data['street_arr']);
    //获取权限组列表
    $permission_group = $this->organization_model->get_permission_group();
    //print_r($permission_group);
    foreach ($permission_group as $k => $vo) {

    }
    $data['permission_group'] = $permission_group;

    //页面标题
    $data['page_title'] = '编辑员工帐号';


    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/cal.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/house_new.css'
      . ',mls/css/v1.0/system_set.css,mls/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js,'
      . 'common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/personal_center.js,'
      . 'mls/js/v1.0/cal.js,mls/js/v1.0/shuifei.js');


    $this->view('organization/organization_edit', $data);
  }

  //门店信息编辑页面
  public function agency_edit($agency_id)
  {
    $agency_info = $this->agency_model->get_by_id($agency_id);
    $data['agency_info'] = $agency_info;
    //页面标题
    $data['page_title'] = '编辑门店资料页面';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/notice.css,mls/css/v1.0/cal.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/house_new.css'
      . ',mls/css/v1.0/system_set.css,mls/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js,'
      . 'mls/js/v1.0/cal.js,mls/js/v1.0/shuifei.js,mls/js/v1.0/personal_center.js');


    $this->view('organization/agency_edit', $data);
  }

  public function modify_agency_edit()
  {

    $agency_id = $this->input->post("agency_id");

    $telno = $this->input->post('telno');
    $service_area = $this->input->post('service_area');
    $update_data = array('telno' => $telno, 'service_area' => $service_area);
    $update_result = $this->agency_model->update_agency_byid($update_data, $agency_id);
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
      //$broker_id = $this->input->post('broker_id');
      $broker_id = $this->user_arr['broker_id'];
      $update_data = array('photo' => $fileurl);
      $this->broker_info_model->update_by_broker_id($update_data, $broker_id);
      $date = array('pic' => $fileurl);
      //$this->newhouse_sync_account_base_model->updateagency($date,$broker_id);
      echo "<script>window.parent.changePhoto('" . $fileurl . "')</script>";
    } elseif ($filename == 'photofile_modify') {
      $broker_id = $this->input->post('broker_id');
      $update_data = array('photo' => $fileurl);
      $this->broker_info_model->update_by_broker_id($update_data, $broker_id);
      $date = array('pic' => $fileurl);
      //$this->newhouse_sync_account_base_model->updateagency($date,$broker_id);
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
    public function upload_head()
    {
        $filename = $this->input->post('action');
        $fileurl = $this->input->post('fileurl');
        //echo "<script>alert('".$fileurl."')</script>";exit;
        echo "<script>window.parent.changePhoto('" . $fileurl . "')</script>";

    }

  /*
     * 上传图片
     */
  public function upload_photo_agency()
  {
    $filename = $this->input->post('action');
    //echo "<script>alert('".$filename."')</script>";exit;
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
    //echo "<script>alert('".$fileurl."')</script>";exit;
    if ($filename == 'photofile_add') {
      $agency_id = $this->input->post('agency_id');
      $update_data = array('photo' => $fileurl);
      $this->agency_model->update_agency_byid($update_data, $agency_id);
      $date = array('pic' => $fileurl);
      //$this->newhouse_sync_account_base_model->updateagency($date,$agency_id);
      echo "<script>window.parent.changePhoto('" . $fileurl . "')</script>";
    } elseif ($filename == 'photofile_modify') {
      $agency_id = $this->input->post('agency_id');
      $update_data = array('photo' => $fileurl);
      $this->agency_model->update_agency_byid($update_data, $agency_id);
      $date = array('pic' => $fileurl);
      //$this->newhouse_sync_account_base_model->updateagency($date,$agency_id);
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
    $photo = $this->input->post('photo');
    $broker_id = $this->input->post('broker_id');
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
    $agency_id = $this->input->post('agency_id');
    $agency_id_old = $this->input->post('agency_id_old');
    $house_num = $this->input->post('house_num');
    $customer_num = $this->input->post('customer_num');
    $is_show_c = $this->input->post('is_show_c');

    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);
    $company_id = $broker_info['company_id'];
    $role_id = $this->organization_model->get_role_id_by($agency_id, $system_group_id);
    //根据角色，获得角色level
    $system_role_data = $this->permission_system_group_model->get_by_id($system_group_id);
    if (is_full_array($system_role_data)) {
      $level = intval($system_role_data['level']);
    }
    //查询公司分店消息的条件
    $time = time();
    $cond_where = "company_id = {$company_id} and expiretime >= {$time} ";
    $cond_where .= " and agency_id = {$agency_id} ";
    //获取员工role_id列表
    $this->broker_info_model->set_select_fields(array('role_id'));
    $role_ids = $this->broker_info_model->get_all_by($cond_where, 0, 0);
    $role_id_arr = array();
    foreach ($role_ids as $vo) {
      $role_id_arr[] = $vo['role_id'];
    }
    $role_id_arr = array_unique($role_id_arr);

    //判断是否店长冲突
    $role_id_dz = $this->organization_model->get_role_id_by($company_id, 4);
    if ($broker_info['role_id'] != $role_id['id'] && $system_group_id == 4) {
      if (in_array($role_id['id'], $role_id_arr)) {
        echo '{"status":"error","msg":"门店已有店长！"}';
        exit;
      }
    }

    //转移门店时转移房客源
    if ($agency_id != $agency_id_old) {
      $update_arr = array("agency_id" => $agency_id);
      //房源管理-出售
      $sell_ids = $this->sell_house_model->get_id_by_brokerid($broker_id);
      $sell_ids_arr = array();
      foreach ($sell_ids as $vo) {
        $sell_ids_arr[] = $vo['id'];
      }
      if (is_full_array($sell_ids_arr)) {
        $num_sell_update = $this->sell_house_model->update_info_by_ids($sell_ids_arr, $update_arr);
      }
      //房源管理-出租
      $rent_ids = $this->rent_house_model->get_id_by_brokerid($broker_id);
      $rent_ids_arr = array();
      foreach ($rent_ids as $vo) {
        $rent_ids_arr[] = $vo['id'];
      }
      if (is_full_array($rent_ids_arr)) {
        $num_rent_update = $this->rent_house_model->update_info_by_ids($rent_ids_arr, $update_arr);
      }
      //客源管理-求购
      $buy_customer_ids = $this->buy_customer_model->get_id_by_brokerid($broker_id);
      $buy_customer_ids_arr = array();
      foreach ($buy_customer_ids as $vo) {
        $buy_customer_ids_arr[] = $vo['id'];
      }
      if (is_full_array($buy_customer_ids_arr)) {
        $this->buy_customer_model->update_info_by_id($buy_customer_ids_arr, $update_arr);
      }
      //客源管理-求租
      $rent_customer_ids = $this->rent_customer_model->get_id_by_brokerid($broker_id);
      $rent_customer_ids_arr = array();
      foreach ($rent_customer_ids as $vo) {
        $rent_customer_ids_arr[] = $vo['id'];
      }
      if (is_full_array($rent_customer_ids_arr)) {
        $this->rent_customer_model->update_info_by_id($rent_customer_ids_arr, $update_arr);
      }
    }
    $update_data = array('photo' => $photo,'truename' => $truename, 'sex' => $sex, 'role_id' => $role_id['id'], 'role_level' => $level, 'idno' => $idno, 'joinjob' => $joinjob, 'qq' => $qq, 'email' => $email, 'remark' => $remark, 'address' => $address, 'postcode' => $postcode, 'graduate' => $graduate, 'diploma' => $diploma, 'phone' => $phone, 'agency_id' => $agency_id, 'is_show_c' => $is_show_c, 'work_time' => $work_time);
    $num = $this->broker_info_model->update_by_broker_id($update_data, $broker_id);

    //帐号同步到新房分销
    if ('0' === $sex) {
      $xffx_sex = 1;
    } else if ('1' === $sex) {
      $xffx_sex = 2;
    }
    //if($num_sell_update==$sell_house_num && $num_rent_update==$rent_house_num && $num ==1){
    if ($num == 1) {
      //操作日志
      $old_agency_info = $this->agency_model->get_by_id(intval($agency_id_old));
      $old_agency_name = '';
      if (is_full_array($old_agency_info)) {
        $old_agency_name = $old_agency_info['name'];
      }

      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 26;
      $add_log_param['text'] = '修改"' . $old_agency_name . '" "' . $truename . '"资料';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);

      $xffxdata = array(
        'ks_id' => $agency_id,
        'kcp_id' => $this->user_arr['company_id'],
        'city' => $_SESSION[USER_SESSION_KEY]["city_spell"],
        // 'ag_name' => $truename,
        'sex' => $xffx_sex,
        'update_time' => time(),
      );
      //提交员工编辑修改信息
      //$this->newhouse_sync_account_base_model->updateagency($xffxdata,$broker_id);
      /*
      $url = 'http://adminxffx.fang100.com/fktdata/agency';
      $this->load->library('Curl');
      Curl::fktdata($url, $xffxdata);*/
      echo '{"status":"success","msg":"提交信息成功"}';
    } else {
      echo '{"status":"error","msg":"未做任何修改"}';
    }


  }

  //查看门店对应的关联权限节点
  public function get_agency_per_node($main_agency_id = 0, $sub_agency_id = 0)
  {
    $main_agency_id = intval($main_agency_id);
    $sub_agency_id = intval($sub_agency_id);
    $result_arr = array();
    if (!empty($main_agency_id) && !empty($sub_agency_id)) {
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $func_area = $this->agency_permission_model->get_func_area_by_main_sub_id($main_agency_id, $sub_agency_id);
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

}
