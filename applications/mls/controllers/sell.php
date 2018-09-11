<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 城市控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class Sell extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
    protected $_city = 'hz';


  /**
   * 录入经纪人id
   *
   * @access private
   * @var int
   */
  private $_broker_id = 0;

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
  private $_limit = 100;

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
    //加载区属模型类
    $this->load->model('district_model');
    //加载楼盘模型类
    $this->load->model('community_model');
    //表单验证
    $this->load->library('form_validation');
    //加载客户MODEL
    $this->load->model('sell_house_model');
    //加载房源标题模板类
    $this->load->model('house_title_template_model');
    $this->load->model('house_content_template_model');
    $this->load->model('rent_house_model');
    $this->load->model('broker_model');
    $this->load->model('broker_info_model');
    $this->load->model('house_collect_model');
    $this->load->model('sell_model');
    $this->load->model('api_broker_model');
    $this->load->model('agency_model');
    $this->load->model('permission_system_group_model');
    $this->load->model('agency_basic_setting_model');
    $this->load->model('cooperate_friends_base_model');
    $this->load->model('collections_model_new');
    $this->load->library('Verify');
    $this->load->model('operate_log_model');
    $this->load->model('house_modify_history_model');
      $this->load->model('cooperate_district_model');
      $this->load->model('hidden_call_model');

    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
    }

  }


  /**
   * 发布出售
   * @access public
   * @return void
   */
  public function publish()
  {
    //模板使用数据
    $data = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $is_property_publish = $company_basic_data['is_property_publish'];
    //录入数据，是否黑名单校验
    $is_blacklist_check = $company_basic_data['is_blacklist_check'];
    $data['is_property_publish'] = $is_property_publish;
    //是否开启合作中心
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    //是否开启合作审核
    $data['check_cooperate'] = $company_basic_data['check_cooperate'];
    //新增房源是否默认私盘
    $data['is_house_private'] = $company_basic_data['is_house_private'];
    //房源必须同步
    $data['is_fang100_insert'] = $company_basic_data['is_fang100_insert'];

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');

    //获取出售信息基本配置资料
    $house_config = $this->house_config_model->get_config();
    //基本信息‘状态’数据处理
    if (!empty($house_config['status']) && is_array($house_config['status'])) {
      foreach ($house_config['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $house_config['status'][$k] = '暂不售';
        }
      }
    }
    $data['config'] = $house_config;
    //获取区属
    $data['district'] = $this->district_model->get_district();
    //页面标题
    $data['page_title'] = '出售房源发布';

    //获取当前登录人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //获取当前登录人挂靠公司id
    $data['company_id'] = $broker_info['company_id'];
    //加载当前登录人的房源描述模板类
    $this->load->model('house_tmp_model');
    //得出当前登录经纪人的模板
    $where = "broker_id = " . $broker_id . " AND type = 1";   //type=1为出售
    $data['tmps'] = $this->house_tmp_model->get_tmps($where);
    $data['temp_num'] = count($data['tmps']);

    $data['group_id'] = $broker_info['group_id'];

    $user = $this->user_arr;
    $agency_id = isset($user['agency_id']) ? $user['agency_id'] : '';
    if (empty($agency_id)) {
        echo '经纪人门店不存在';
        exit;
    }
      $this->load->model('sell_house_field_agency_model');
      $db_city = $this->sell_house_field_agency_model->get_db_city();
      $fieldList = $db_city->from('sell_house_field_agency')->where("agency_id = $agency_id")->order_by('id', 'asc')->get()->result_array();
      $defaultList = $db_city->from('sell_house_field_agency')->where("agency_id = 0")->order_by('id', 'asc')->get()->result_array();
      $lists = [];
      if (!empty($fieldList)) {
          foreach ($fieldList as $v) {
              $lists[$v['sell_type']][$v['field_name']] = $v;
          }
      }
      foreach ($defaultList as $v) {
          if (!isset($lists[$v['sell_type']][$v['field_name']])) {
              $lists[$v['sell_type']][$v['field_name']] = $v;
          }
      }
      ksort($lists);
      //print_r($lists);
      $data['lists'] = json_encode($lists);

    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic2.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/group_publish.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification.js,mls/js/v1.0/radio_checkbox_mod2.js,mls/js/v1.0/backspace.js,mls/js/v1.0/house_title_template.js,mls/js/v1.0/house_content_template.js');

    //加载发布页面模板
    $this->view('house/sell_house_publish', $data);
  }

  //页面添加模板iframe中的方法
  public function house_temp()
  {
    $data = array();

    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic2.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification.js,mls/js/v1.0/backspace.js,mls/js/v1.0/radio_checkbox_mod.js,mls/js/v1.0/house_title_template.js');

    $this->view('house/house_public_temp', $data);
  }


  //页面修改模板iframe中的方法
  public function house_modify_temp($id)
  {
    $data = array();

    //获取当前登录人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    //加载当前登录人的房源描述模板类
    $this->load->model('house_tmp_model');
    //得出当前登录经纪人的模板
    $where = "type = 1 AND id = " . $id . " AND broker_id = " . $broker_id;//type=1为出售

    $rows = $this->house_tmp_model->get_tmps($where);
    $data['temp'] = $rows[0];

    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic2.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification.js,mls/js/v1.0/backspace.js,mls/js/v1.0/radio_checkbox_mod.js,mls/js/v1.0/house_title_template.js');

    $this->view('house/house_public_modify_temp', $data);
  }

  //查询模板的remark
  public function search_temp()
  {
    $json = array();

    $id = $this->input->post("id");
    //获取当前登录人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    //加载当前登录人的房源描述模板类
    $this->load->model('house_tmp_model');
    //得出当前登录经纪人的模板
    $where = "type = 1 AND id = " . $id . " AND broker_id = " . $broker_id;//type=1为出售

    $rows = $this->house_tmp_model->get_tmps($where);
    $json['remark'] = $rows[0]['remark'];

    echo json_encode($json);
  }

  //录入出售里面的新建模板
  public function save_new_tmp()
  {
    //使用数据
    $data = array();
    $json = array();
    //加载当前登录人的房源描述模板类
    $this->load->model('house_tmp_model');

    //获取当前登录人ID
    $broker_info = $this->user_arr;
    $data['broker_id'] = intval($broker_info['broker_id']);
    //去除HTML标签过滤
    $data['template_name'] = $this->input->post('template_name', TRUE);
    $data['remark'] = $this->input->post('remark');
    $data['type'] = 1;  //表示出售
    $data['createtime'] = time();

    $rs = $this->house_tmp_model->insert_data('house_template', $data);
    if ($rs) {
      $json['status'] = 1;
      $json['template_id'] = $rs;
      $json['template_name'] = $data['template_name'];
      $json['remark'] = $data['remark'];
    } else {
      $json['status'] = 2;
    }

    echo json_encode($json);
  }

  //录入出售里面的修改模板
  public function save_tmp()
  {
    //使用数据
    $data = array();
    $json = array();
    //加载当前登录人的房源描述模板类
    $this->load->model('house_tmp_model');

    $data['template_name'] = $this->input->post('template_name', TRUE);
    $data['remark'] = $this->input->post('remark', TRUE);
    $id = $this->input->post('id', TRUE);

    $where = "id = " . $id;
    $rs = $this->house_tmp_model->modify_data_rows('house_template', $data, $where);

    if ($rs !== false) {
      $json['status'] = 1;
      $json['template_id'] = $id;
      $json['template_name'] = $data['template_name'];
      //$json['remark'] = $data['remark'];
    } else {
      $json['status'] = 2;
    }

    echo json_encode($json);
  }

  //删除模板
  public function del_tmp()
  {
    $json = array();
    //加载当前登录人的房源描述模板类
    $this->load->model('house_tmp_model');
    $id = $this->input->post('id');

    $where = "id = " . $id;
    $rs = $this->house_tmp_model->delete_data($where, 'house_template');
    if ($rs !== false) {
      $json['status'] = 1;
      $json['template_id'] = $id;
    } else {
      $json['status'] = 2;
    }

    echo json_encode($json);
  }

  //录入出售里面的判断模板个数
  public function judge_tmp_num()
  {
    $json = array();
    //加载当前登录人的房源描述模板类
    $this->load->model('house_tmp_model');
    //获取当前登录人ID
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    $where = "broker_id = " . $broker_id . " AND type = 1";
    $count = $this->house_tmp_model->get_count($where, 'house_template');

    if ($count == 10) {
      $json['status'] = 1;
    } else {
      $json['status'] = 2;
    }

    echo json_encode($json);
  }

  /**
   * 发布出售
   * @access public
   * @return void
   */
  public function collect_publish($cid)
  {
    //根据房源house_id去查询房源详情
    if (!empty($cid)) {
      $where_cond = array('id' => $cid);
    }
    //获取当前登录人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //模板使用数据
    $data = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //楼盘名称只能选择录入
    $is_property_publish = $company_basic_data['is_property_publish'];
    $data['is_property_publish'] = $is_property_publish;
    //是否开启合作审核
    $data['check_cooperate'] = $company_basic_data['check_cooperate'];
    //是否开启合作中心
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    //新增房源是否默认私盘
    $data['is_house_private'] = $company_basic_data['is_house_private'];
    //$this->load->model('collections_model_new');//采集模型类
    $house_info = $this->collections_model_new->get_housesell_byid($where_cond);
    $house_info = $house_info[0];
    //引入标题和描述
    $house_info['title'] = $house_info['house_title'];
    $house_info['content'] = $house_info['remark'];
    $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    //获取采集下载图片时水印设置
    $watermark = $this->collections_model_new->get_watermark_set($broker_id);
    if (isset($watermark) && !empty($watermark)) {
      $entry = $watermark[0]['entry'];
    } else {
      $entry = 0;
    }
    if ($entry == 1 && $house_info['pic_cut'] == 1) {
      $house_info['picurl'] = str_replace('mls/', 'mls_cutout/', $house_info['picurl']);
    }
    $data['house_info'] = $house_info;
    $block_name = $house_info['house_name'];

    $result = $this->community_model->get_cmtinfo_by_cmtname_from_official($block_name);
    if ($result) {
      $result = $result[0];
      $result['district_name'] = $this->district_model->get_distname_by_id($result['dist_id']);
      $result['street_name'] = $this->district_model->get_streetname_by_id($result['streetid']);
    }
    $data['result'] = $result;

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');

    //获取出售信息基本配置资料
    $house_config = $this->house_config_model->get_config();
    //基本信息‘状态’数据处理
    if (!empty($house_config['status']) && is_array($house_config['status'])) {
      foreach ($house_config['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $house_config['status'][$k] = '暂不售';
        }
      }
    }
    $data['config'] = $house_config;
    //获取区属
    $data['district'] = $this->district_model->get_district();

    $data['group_id'] = $broker_info['group_id'];
    $data['company_id'] = $broker_info['company_id'];
    //加载当前登录人的房源描述模板类
    $this->load->model('house_tmp_model');
    //得出当前登录经纪人的模板
    $where = "broker_id = " . $broker_id . " AND type = 1";   //type=1为出售
    $data['tmps'] = $this->house_tmp_model->get_tmps($where);
    $data['temp_num'] = count($data['tmps']);

    //页面标题
    $data['page_title'] = '出售房源发布';
    //需要加载的css

    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic2.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/group_publish.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification.js,mls/js/v1.0/backspace.js,mls/js/v1.0/radio_checkbox_mod2.js,mls/js/v1.0/house_title_template.js,mls/js/v1.0/house_content_template.js');

    //加载发布页面模板
    $this->view('house/sell_house_collect_publish', $data);
  }

  //判断重复业主电话
  public function check_unique_telno()
  {
    $msg = 0;
    $house_num = 0;

    $telno = $this->input->get('telno', TRUE);
    $house_id = $this->input->get('house_id', TRUE);
    if (!empty($telno)) {
      //经纪人信息
      $broker_info = $this->user_arr;
      //根据经济人总公司编号获取全部分店信息
      $company_id = intval($broker_info['company_id']);//获取总公司编号
      $agency_id = intval($broker_info['agency_id']);//门店编号
      //判断经纪人当前门店类型，直营or加盟
      $this->agency_model->set_select_fields(array('id', 'agency_type'));
      $this_agency_data = $this->agency_model->get_by_id($agency_id);
      if (is_full_array($this_agency_data)) {
        $agency_type = $this_agency_data['agency_type'];
      }
      //加盟店，去重范围只在自己门店。
      if (isset($agency_type) && '2' == $agency_type) {
        $agency_ids = $agency_id;
        //直营店，去重范围，当前公司下的所有直营店。
      } else {
        //获取当前公司下的所有直营店
        $agency_type_1_list = $this->api_broker_model->get_type_1_agencys_by_company_id($company_id);
        if (is_full_array($agency_type_1_list)) {
          $arr_agency_id = array();
          foreach ($agency_type_1_list as $key => $val) {
            $arr_agency_id[] = $val['agency_id'];
          }
          $agency_ids = implode(',', $arr_agency_id);
        } else {
          $agency_ids = $agency_id;
        }
      }
      if ($house_id) {
        $cond_where = "status != 5 and agency_id in (" . $agency_ids . ") and (telno1 = '$telno' or telno2 = '$telno'  or telno3 = '$telno')";
      } else {
        $cond_where = "id <> '$house_id' and status != 5 and agency_id in (" . $agency_ids . ") and (telno1 = '$telno' or telno2 = '$telno'  or telno3 = '$telno')";
      }
      $house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
    }

    $msg = $house_num > 0 ? 1 : 0;

    echo $msg;
  }

  //判断重复房源
  public function check_unique_house()
  {
    $msg = 0;
    $house_num = 0;

    $block_id = $this->input->get('block_id', TRUE);
    $door = $this->input->get('door', TRUE);
    $unit = $this->input->get('unit', TRUE);
    $dong = $this->input->get('dong', TRUE);

    if (!empty($block_id) && !empty($door) && !empty($unit) && !empty($dong)) {
      //经纪人信息
      $broker_info = $this->user_arr;
      //根据经济人总公司编号获取全部分店信息
      $company_id = intval($broker_info['company_id']);//获取总公司编号
      $agency_id = intval($broker_info['agency_id']);//门店编号
      //判断经纪人当前门店类型，直营or加盟
      $this->agency_model->set_select_fields(array('id', 'agency_type'));
      $this_agency_data = $this->agency_model->get_by_id($agency_id);
      if (is_full_array($this_agency_data)) {
        $agency_type = $this_agency_data['agency_type'];
      }
      //加盟店，去重范围只在自己门店。
      if (isset($agency_type) && '2' == $agency_type) {
        $agency_ids = $agency_id;
        //直营店，去重范围，当前公司下的所有直营店。
      } else {
        //获取当前公司下的所有直营店
        $agency_type_1_list = $this->api_broker_model->get_type_1_agencys_by_company_id($company_id);
        if (is_full_array($agency_type_1_list)) {
          $arr_agency_id = array();
          foreach ($agency_type_1_list as $key => $val) {
            $arr_agency_id[] = $val['agency_id'];
          }
          $agency_ids = implode(',', $arr_agency_id);
        } else {
          $agency_ids = $agency_id;
        }
      }
      $cond_where = "status != 5 and agency_id in (" . $agency_ids . ") and block_id = '$block_id' and door = '$door' and unit = '$unit' and dong = '$dong' ";
      $house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
    }

    $msg = $house_num > 0 ? 1 : 0;

    echo $msg;
  }

  //判断重复房源(用于房源修改)
  public function check_unique_house_modify()
  {
    $msg = 0;
    $house_num = 0;

    $block_id = $this->input->get('block_id', TRUE);
    $door = $this->input->get('door', TRUE);
    $unit = $this->input->get('unit', TRUE);
    $dong = $this->input->get('dong', TRUE);
    $house_id = $this->input->get('house_id', TRUE);

    if (!empty($block_id) && !empty($door) && !empty($unit) && !empty($dong) && !empty($house_id)) {
      //经纪人信息
      $broker_info = $this->user_arr;
      //根据经济人总公司编号获取全部分店信息
      $company_id = intval($broker_info['company_id']);//获取总公司编号
      $agency_id = intval($broker_info['agency_id']);//门店编号
      //判断经纪人当前门店类型，直营or加盟
      $this->agency_model->set_select_fields(array('id', 'agency_type'));
      $this_agency_data = $this->agency_model->get_by_id($agency_id);
      $agency_type = 0;
      if (is_full_array($this_agency_data)) {
        $agency_type = $this_agency_data['agency_type'];
      }

      //加盟店，去重范围只在自己门店。
      if (isset($agency_type) && '2' == $agency_type) {
        $agency_ids = $agency_id;
        //直营店，去重范围，当前公司下的所有直营店。
      } else {
        //获取当前公司下的所有直营店
        $agency_type_1_list = $this->api_broker_model->get_type_1_agencys_by_company_id($company_id);
        if (is_full_array($agency_type_1_list)) {
          $arr_agency_id = array();
          foreach ($agency_type_1_list as $key => $val) {
            $arr_agency_id[] = $val['agency_id'];
          }
          $agency_ids = implode(',', $arr_agency_id);
        } else {
          $agency_ids = $agency_id;
        }
      }

      $cond_where = "status != 5 and agency_id in (" . $agency_ids . ") and block_id = '$block_id' and door = '$door' and unit = '$unit' and dong = '$dong' ";
      $this->sell_house_model->set_search_fields(array('id'));
      $house_data = $this->sell_house_model->get_list_by_cond($cond_where);
      $house_id_arr = array();
      if (is_full_array($house_data)) {
        foreach ($house_data as $key => $value) {
          $house_id_arr[] = $value['id'];
        }
      }
      if (is_full_array($house_id_arr)) {
        if (in_array($house_id, $house_id_arr) && 1 == count($house_id_arr)) {
          $msg = 0;
        } else {
          $msg = 1;
        }
      } else {
        $msg = 0;
      }
    }

    echo $msg;
  }


  //判断房源是否重复(用于修改房源)
  public function check_house_modify($block_id, $door, $unit, $dong, $house_id)
  {
    //经纪人信息
    $broker_info = $this->user_arr;
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $agency_id = intval($broker_info['agency_id']);//门店编号
    //判断经纪人当前门店类型，直营or加盟
    $this->agency_model->set_select_fields(array('id', 'agency_type'));
    $this_agency_data = $this->agency_model->get_by_id($agency_id);
    $agency_type = 0;
    if (is_full_array($this_agency_data)) {
      $agency_type = $this_agency_data['agency_type'];
    }

    //加盟店，去重范围只在自己门店。
    if (isset($agency_type) && '2' == $agency_type) {
      $agency_ids = $agency_id;
      //直营店，去重范围，当前公司下的所有直营店。
    } else {
      //获取当前公司下的所有直营店
      $agency_type_1_list = $this->api_broker_model->get_type_1_agencys_by_company_id($company_id);
      if (is_full_array($agency_type_1_list)) {
        $arr_agency_id = array();
        foreach ($agency_type_1_list as $key => $val) {
          $arr_agency_id[] = $val['agency_id'];
        }
        $agency_ids = implode(',', $arr_agency_id);
      } else {
        $agency_ids = $agency_id;
      }
    }

    $cond_where = "status != 5 and agency_id in (" . $agency_ids . ") and block_id = '$block_id' and door = '$door' and unit = '$unit' and dong = '$dong' ";
    $tbl = "sell_house";
    $this->sell_house_model->set_tbl($tbl);
    $this->sell_house_model->set_search_fields(array('id'));
    $house_data = $this->sell_house_model->get_list_by_cond($cond_where);
    $house_id_arr = array();
    if (is_full_array($house_data)) {
      foreach ($house_data as $key => $value) {
        $house_id_arr[] = $value['id'];
      }
    }
    if (is_full_array($house_id_arr)) {
      if (in_array($house_id, $house_id_arr) && 1 == count($house_id_arr)) {
        $result = true;
      } else {
        $result = false;
      }
    } else {
      $result = true;
    }
    return $result;
  }

  //判断房源是否重复
  public function check_house($block_id, $door, $unit, $dong)
  {
    //经纪人信息
    $broker_info = $this->user_arr;
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $agency_id = intval($broker_info['agency_id']);//门店编号
    //判断经纪人当前门店类型，直营or加盟
    $this->agency_model->set_select_fields(array('id', 'agency_type'));
    $this_agency_data = $this->agency_model->get_by_id($agency_id);
    if (is_full_array($this_agency_data)) {
      $agency_type = $this_agency_data['agency_type'];
    }
    //加盟店，去重范围只在自己门店。
    if (isset($agency_type) && '2' == $agency_type) {
      $agency_ids = $agency_id;
      //直营店，去重范围，当前公司下的所有直营店。
    } else {
      //获取当前公司下的所有直营店
      $agency_type_1_list = $this->api_broker_model->get_type_1_agencys_by_company_id($company_id);
      if (is_full_array($agency_type_1_list)) {
        $arr_agency_id = array();
        foreach ($agency_type_1_list as $key => $val) {
          $arr_agency_id[] = $val['agency_id'];
        }
        $agency_ids = implode(',', $arr_agency_id);
      } else {
        $agency_ids = $agency_id;
      }
    }
    $cond_where = "status != 5 and agency_id in (" . $agency_ids . ") and block_id = '$block_id' and door = '$door' and unit = '$unit' and dong = '$dong' ";
    $tbl = "sell_house";
    $this->sell_house_model->set_tbl($tbl);
    $house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
    return $house_num;
  }



  //判断房源是否重复
  public function check_house_lists()
  {
    $block_id = $this->input->get('block_id', TRUE);
    $door = $this->input->get('door', TRUE);
    $unit = $this->input->get('unit', TRUE);
    $dong = $this->input->get('dong', TRUE);
    //经纪人信息
    $broker_info = $this->user_arr;
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $agency_id = intval($broker_info['agency_id']);//门店编号
    //判断经纪人当前门店类型，直营or加盟
    $this->agency_model->set_select_fields(array('id', 'agency_type'));
    $this_agency_data = $this->agency_model->get_by_id($agency_id);
    $agency_type = 0;
    if (is_full_array($this_agency_data)) {
      $agency_type = $this_agency_data['agency_type'];
    }

    //加盟店，去重范围只在自己门店。
    if (isset($agency_type) && '2' == $agency_type) {
      $agency_ids = $agency_id;
      //直营店，去重范围，当前公司下的所有直营店。
    } else {
      //获取当前公司下的所有直营店
      $agency_type_1_list = $this->api_broker_model->get_type_1_agencys_by_company_id($company_id);
      if (is_full_array($agency_type_1_list)) {
        $arr_agency_id = array();
        foreach ($agency_type_1_list as $key => $val) {
          $arr_agency_id[] = $val['agency_id'];
        }
        $agency_ids = implode(',', $arr_agency_id);
      } else {
        $agency_ids = $agency_id;
      }
    }

    $cond_where = "status != 5 and agency_id in (" . $agency_ids . ") and block_id = $block_id and door = '$door' and unit = '$unit' and dong = '$dong' ";
    $tbl = "sell_house";
    $this->sell_house_model->set_tbl($tbl);
    $house_num = '';
    if (!empty($block_id) && $block_id > 0) {
      $house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
    }
    echo $house_num;
  }

  //判断输入号码是否为黑名单
  public function check_blacklist()
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //录入数据，是否黑名单校验
    $is_blacklist_check = $company_basic_data['is_blacklist_check'];

    $telno = $this->input->get('telno', TRUE);
    $int_telno = trim($telno);
    $this->load->model('blacklist_model');
    $where_sql = ' where tel = "' . $int_telno . '"';
    $result_arr = $this->blacklist_model->get_all_by($where_sql);
    if ('1' == $is_blacklist_check && count($result_arr) > 0) {
      echo 'success';
    } else {
      echo 'failed';
    }
    exit;
  }

  //发布房源
  public function add()
  {
    $this->_add();
  }

  public function update()
  {
    //当前经纪人是否认证
    $this_broker_group_id = $this->user_arr['group_id'];
    $house_id = $this->input->post('house_id', TRUE);
    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    $house_modify_per = $this->broker_permission_model->check('8', $owner_arr);
    //修改房源关联门店权限
    $agency_house_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '4');
      if (!$house_modify_per['auth'] || $this_broker_group_id != '2') {
      $this->redirect_permission_none();
      exit();
    } else {
          if (!$agency_house_modify_per || $this_broker_group_id != '2') {
        $this->redirect_permission_none();
        exit();
      }
    }
    //注销房源权限
    $house_status_per = $this->broker_permission_model->check('133', $owner_arr);
    $status = $this->input->post('status', TRUE);
    if ('5' == $status && !$house_status_per['auth'] && $house_status_per != '1') {
      $this->redirect_permission_none();
      exit();
    }
    $this->_add();
  }

  /**
   * 添加出售信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  private function _add()
  {
    //添加出售信息
    $datainfo = array();

    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $broker_name = strip_tags($broker_info['truename']);
    $agency_id = intval($broker_info['agency_id']);
    $company_id = intval($broker_info['company_id']);
    $credit_score = '';
    $level_score = '';
    //获取当前经济人所在门店的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $house_customer_system = intval($company_basic_data['house_customer_system']);
      $sell_house_private_num = intval($company_basic_data['sell_house_private_num']);
    } else {
      $house_customer_system = $sell_house_private_num = 0;
    }

    if ($broker_id == 0) {
      //遗留 退出系统
    }

    $house_id = $this->input->post('house_id', TRUE);
    if (empty($house_id)) {
      $datainfo['broker_id'] = $broker_id;
      $datainfo['broker_name'] = $broker_name;
      $datainfo['agency_id'] = $agency_id;
      $datainfo['company_id'] = $company_id;
      $datainfo['createtime'] = time();
      $datainfo['ip'] = get_ip();
    }

    $action_type = $this->input->post('action_type', TRUE);//区分发布、修改页面
    $datainfo['sell_type'] = $this->input->post('sell_type', TRUE);
    $datainfo['block_name'] = $this->input->post('block_name', TRUE);
    $datainfo['block_id'] = $this->input->post('block_id', TRUE);
    $datainfo['district_id'] = $this->input->post('district_id', TRUE);
    $datainfo['street_id'] = $this->input->post('street_id', TRUE);
    $datainfo['address'] = $this->input->post('address', TRUE);
    $datainfo['dong'] = $this->input->post('dong', TRUE);
    $datainfo['unit'] = $this->input->post('unit', TRUE);
    $datainfo['door'] = $this->input->post('door', TRUE);
    $datainfo['owner'] = $this->input->post('owner', TRUE);
    $datainfo['idcare'] = $this->input->post('idcare', TRUE);
    $datainfo['telno1'] = $this->input->post('telno1', TRUE);
    $datainfo['telno2'] = $this->input->post('telno2', TRUE);
    $datainfo['telno3'] = $this->input->post('telno3', TRUE);
    $datainfo['house_grade'] = $this->input->post('house_grade', TRUE);
    if ('2' == $datainfo['house_grade']) {
      $datainfo['is_sticky'] = 1;
    } else {
      $datainfo['is_sticky'] = 0;
    }
    $datainfo['house_structure'] = $this->input->post('house_structure', TRUE);
    $datainfo['read_time'] = $this->input->post('read_time', TRUE);
    //$datainfo['proof'] = $this->input->post('proof' , TRUE);
    //$datainfo['mound_num'] = $this->input->post('mound_num' , TRUE);
    //$datainfo['record_num'] = $this->input->post('record_num' , TRUE);
    $datainfo['status'] = $this->input->post('status', TRUE);
    $datainfo['nature'] = $this->input->post('nature', TRUE);
    //酒店式公寓和别墅相同处理逻辑
    if ($datainfo['sell_type'] > 2 && $datainfo['sell_type'] != 8) {
      $datainfo['room'] = '';
      $datainfo['hall'] = '';
      $datainfo['toilet'] = '';
    } else {
      $datainfo['room'] = $this->input->post('room', TRUE);
      $datainfo['hall'] = $this->input->post('hall', TRUE);
      $datainfo['toilet'] = $this->input->post('toilet', TRUE);
    }
    $datainfo['kitchen'] = $this->input->post('kitchen', TRUE);
    $datainfo['balcony'] = $this->input->post('balcony', TRUE);
    $datainfo['isshare'] = $this->input->post('isshare', TRUE);
    $datainfo['is_outside'] = $this->input->post('is_outside', TRUE);
    if ('1' == $datainfo['is_outside']) {
      $datainfo['is_outside_time'] = time();
    }

    $datainfo['isshare_friend'] = $this->input->post('isshare_friend', TRUE);
    $isshare_back = $this->input->post('isshare_back', TRUE);
    $reward_type = $this->input->post('reward_type', TRUE);//奖励方式

    //修改页面
    if ('modify' == $action_type) {
      if (intval($isshare_back) > 0) {
        $datainfo['isshare'] = $isshare_back;
      }
    } else if ('add' == $action_type) {
      //未开启合作审核，选择设置奖金，合作状态改成3
      if ('1' == $datainfo['isshare'] && '2' == $reward_type) {
        $datainfo['isshare'] = 3;
      }
    }
    //设置合作时间
    if ('1' == $datainfo['isshare'] || '2' == $datainfo['isshare'] || '3' == $datainfo['isshare']) {
      $datainfo['set_share_time'] = time();
    }

    if (isset($datainfo['isshare']) && !empty($datainfo['isshare'])) {
      if ('2' == $reward_type) {
        $datainfo['reward_type'] = 2;
        $datainfo['cooperate_reward'] = $this->input->post('shangjin', TRUE);
        //设置悬赏经纪人id
        $datainfo['set_reward_broker_id'] = $broker_id;
      } else if ('1' == $reward_type) {
        $datainfo['reward_type'] = 1;
        $datainfo['commission_ratio'] = $this->input->post('commission_ratio', TRUE);
        $datainfo['cooperate_reward'] = 0;
        $datainfo['set_reward_broker_id'] = 0;
      }
    } else {
      $datainfo['cooperate_reward'] = 0;
      $datainfo['set_reward_broker_id'] = 0;
    }

    $datainfo['is_publish'] = $this->input->post('is_publish', TRUE);
    $datainfo['floor_type'] = $this->input->post('floor_type', TRUE);
    $datainfo['floor'] = $this->input->post('floor', TRUE);
    $datainfo['title'] = $this->input->post('title', TRUE);
    $datainfo['bewrite'] = $this->input->post('bewrite');
    if ($datainfo['floor_type'] == 2) {
      $datainfo['floor'] = $this->input->post('floor2', TRUE);
    }
    $datainfo['subfloor'] = $this->input->post('subfloor', TRUE);
    $datainfo['totalfloor'] = $this->input->post('totalfloor', TRUE);
    if (!empty($datainfo['totalfloor'])) {
      $datainfo['floor_scale'] = $datainfo['floor'] / $datainfo['totalfloor'];
    }
    //酒店式公寓和别墅相同处理逻辑
    if ($datainfo['sell_type'] < 5 || $datainfo['sell_type'] == 8) {
      $datainfo['forward'] = $this->input->post('forward', TRUE);
      $datainfo['fitment'] = $this->input->post('fitment', TRUE);
    }
    $datainfo['buildyear'] = $this->input->post('buildyear', TRUE);
    $datainfo['buildarea'] = $this->input->post('buildarea', TRUE);
    $datainfo['usage_area'] = $this->input->post('usage_area', TRUE);

    $datainfo['loft_area'] = $this->input->post('loft_area', TRUE);
    $datainfo['garage_area'] = $this->input->post('garage_area', TRUE);
    $datainfo['price'] = $this->input->post('price', TRUE);
    $datainfo['lowprice'] = $this->input->post('lowprice', TRUE);
    $datainfo['avgprice'] = $this->input->post('avgprice', TRUE);
    $datainfo['taxes'] = $this->input->post('taxes', TRUE);
    $datainfo['keys'] = $this->input->post('keys', TRUE);
    if ($datainfo['keys']) {
      $datainfo['key_number'] = $this->input->post('key_number', TRUE);
    }
    //$datainfo['pact'] = $this->input->post('pact' , TRUE);
    $datainfo['entrust'] = $this->input->post('entrust', TRUE);
    $datainfo['house_type'] = $this->input->post('house_type', TRUE);
    //$datainfo['struct'] = $this->input->post('struct' , TRUE);
    //$datainfo['pay_type'] = $this->input->post('pay_type' , TRUE);
    $datainfo['property'] = $this->input->post('property', TRUE);
    //$datainfo['rebate_type'] = $this->input->post('rebate_type' , TRUE);
    //$datainfo['look'] = $this->input->post('look' , TRUE);
    $datainfo['current'] = $this->input->post('current', TRUE);
    $datainfo['infofrom'] = $this->input->post('infofrom', TRUE);
    //$datainfo['paperwork'] = $this->input->post('paperwork' , TRUE);
    $equipment = $this->input->post('equipment', TRUE);
    if ($equipment) {
      $datainfo['equipment'] = implode(',', $equipment);
    } else {
      $datainfo['equipment'] = '';
    }
    $setting = $this->input->post('setting', TRUE);
    if ($setting) {
      $datainfo['setting'] = implode(',', $setting);
    } else {
      $datainfo['setting'] = '';
    }
    //标签
    $sell_tag = $this->input->post('sell_tag', TRUE);
    if ($sell_tag) {
      $datainfo['sell_tag'] = implode(',', $sell_tag);
    } else {
      $datainfo['sell_tag'] = '';
    }
    $datainfo['strata_fee'] = $this->input->post('strata_fee', TRUE);
    $datainfo['costs_type'] = $this->input->post('costs_type', TRUE);
    $datainfo['pay_date'] = $this->input->post('pay_date', TRUE);
    $datainfo['remark'] = $this->input->post('remark', TRUE);
    $datainfo['updatetime'] = time();

    //别墅
    //酒店式公寓和别墅相同处理逻辑
    if ($datainfo['sell_type'] == 2 || $datainfo['sell_type'] == 8) {
      $datainfo['villa_type'] = $this->input->post('villa_type', TRUE);
      $datainfo['hall_struct'] = $this->input->post('hall_struct', TRUE);
      $datainfo['park_num'] = $this->input->post('park_num', TRUE);
      $datainfo['garden_area'] = $this->input->post('garden_area', TRUE);
      $datainfo['floor_area'] = $this->input->post('floor_area', TRUE);
      $datainfo['light_type'] = $this->input->post('light_type', TRUE);
    }

    //商铺
    if ($datainfo['sell_type'] == 3) {
      $datainfo['shop_type'] = $this->input->post('shop_type', TRUE);
      $shop_trade = $this->input->post('shop_trade', TRUE);
      if ($shop_trade) {
        $datainfo['shop_trade'] = implode(',', $shop_trade);
      } else {
        $datainfo['shop_trade'] = '';
      }
      $datainfo['division'] = $this->input->post('division', TRUE);
    }

    //写字楼
    if ($datainfo['sell_type'] == 4) {
      $datainfo['division'] = $this->input->post('division2', TRUE);
      $datainfo['office_trade'] = $this->input->post('office_trade', TRUE);
      $datainfo['office_type'] = $this->input->post('office_type', TRUE);
    }

    $block_id = $datainfo['block_id'];
    $door = $datainfo['door'];
    $unit = $datainfo['unit'];
    $dong = $datainfo['dong'];

    //录入房源唯一性验证
    if (isset($datainfo['sell_type']) && intval($datainfo['sell_type']) < 5) {
      $house_num = $this->check_house($block_id, $door, $unit, $dong);
    } else {
      $house_num = 0;
    }

    $housee_id = '';
    $house_add_arr = array();

    //获取当前经纪人发布悬赏房源的数量
    $reward_where_cond = 'set_reward_broker_id = "' . $broker_id . '"' . ' and isshare !=0 and status = 1 and cooperate_reward > 0';
    $cooperate_reward_num = $this->sell_house_model->get_housenum_by_cond($reward_where_cond);

    if (empty($house_id) && $house_num == 0) {
      $house_num_check = true;

      //基本设置，房客源制判断
      $house_private_check = true;
      //公盘私客制
      if (2 == $house_customer_system) {
        if ('1' == $datainfo['nature']) {
          $house_private_check = false;
          $house_private_check_text = '当前门店基本设置为公盘私客制';
        }
      } else if (3 == $house_customer_system) {
        //公盘制 获得当前经纪人的私盘数量
        $private_where_cond = 'broker_id = "' . $broker_id . '"' . ' and status = 1 and nature = 1';
        $private_num = $this->sell_house_model->get_housenum_by_cond($private_where_cond);
        if ('1' == $datainfo['nature'] && $private_num >= $sell_house_private_num) {
          $house_private_check = false;
          $house_private_check_text = '当前门店基本设置为公盘制';
        }
      } else {
        $house_private_check = true;
      }

      //发布悬赏房源个数限制
      if (isset($datainfo['isshare']) && 3 == $datainfo['isshare'] && '2' == $reward_type) {
        if (is_int($cooperate_reward_num) && $cooperate_reward_num < 5) {
          $is_reward = true;
        } else {
          $is_reward = false;
        }
      } else {
        $is_reward = true;
      }
      $is_reward = true;//去除悬赏限制提示

      //委托协议书、卖家身份证、房产证
      $pics['p_filename3'] = $this->input->post('p_filename3', TRUE);
      $pics['p_filename4'] = $this->input->post('p_filename4', TRUE);
      $pics['p_filename5'] = $this->input->post('p_filename5', TRUE);

      //根据合作资料，判断是否发送审核
      //if(is_full_array($pics['p_filename3']) && is_full_array($pics['p_filename4'])){
      //    $datainfo['cooperate_check'] = 2;
      //}

      $coo_ziliao_check_3 = true;
      if (intval($datainfo['isshare']) > 0) {
        $coo_ziliao_check_1 = true;
        $coo_ziliao_check_2 = true;
        //委托协议书、卖家身份证、房产证验证 $coo_ziliao_check_1：悬赏合作必须三证齐全。$coo_ziliao_check_2：佣金悬赏必须传两证或者三证齐全或者不传。
        if ('2' == $reward_type) {
          $coo_ziliao_check_1 = true;
          $datainfo['isshare'] = 1;
        } else if ('1' == $reward_type) {
          $datainfo['house_degree'] = 1;
          if (is_full_array($pics['p_filename4']) && is_full_array($pics['p_filename5'])) {
            $coo_ziliao_check_2 = true;
            $datainfo['cooperate_check'] = 2;
          } else {
            if (empty($pics['p_filename3']) && empty($pics['p_filename4']) && empty($pics['p_filename5'])) {
              $coo_ziliao_check_2 = true;
            } else {
              $coo_ziliao_check_2 = false;
            }
          }
        }
      } else {
        $coo_ziliao_check_1 = true;
        $coo_ziliao_check_2 = true;
      }

      if ($is_reward && $coo_ziliao_check_1 && $coo_ziliao_check_2 && $house_private_check) {
        $housee_id = $this->sell_house_model->add_sell_house_info($datainfo);
      }

      if ($housee_id > 0) {
        //操作日志
        $add_log_param = array();
        $add_log_param['company_id'] = $broker_info['company_id'];
        $add_log_param['agency_id'] = $broker_info['agency_id'];
        $add_log_param['broker_id'] = $broker_id;
        $add_log_param['broker_name'] = $broker_info['truename'];
        $add_log_param['type'] = 2;
        $add_log_param['text'] = '出售房源 ' . 'CS' . $housee_id;
        $add_log_param['from_system'] = 1;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = '127.0.0.1';
        $add_log_param['from_host_name'] = '127.0.0.1';
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();

        $this->operate_log_model->add_operate_log($add_log_param);

        $msg = '房源录入成功.';
        //添加钥匙
        if ($datainfo['keys'] && $datainfo['key_number']) {
          $this->add_key($housee_id, $datainfo['key_number'], 'add');
          //出售房源录入成功记录工作统计日志-钥匙提交
          $this->info_count($housee_id, 6);
        }

        //设置合作添加佣金比例
        if ($datainfo['isshare'] == 1) {
          $a_ratio = $this->input->post('a_ratio', TRUE);//甲方佣金分成比例
          $b_ratio = $this->input->post('b_ratio', TRUE);//已方佣金分成比例
          $buyer_ratio = $this->input->post('buyer_ratio', TRUE);//买方支付佣金比例
          $seller_ratio = $this->input->post('seller_ratio', TRUE);//卖方支付佣金比例

          $this->load->model('sell_house_share_ratio_model');
          $this->sell_house_share_ratio_model->add_house_cooperate_ratio($housee_id,
            $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio);
          //增加积分
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
          $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $housee_id), 1);
          //判断积分是否增加成功
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $credit_score += $credit_result['score'];
          }
          //增加等级分值
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
          $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $housee_id), 1);
          //判断成长值是否增加成功
          if (is_full_array($level_result) && $level_result['status'] == 1) {
            $level_score += $level_result['score'];
          }
        }
        $need__info = $this->user_arr;
        //添加房源日志录入
        $this->load->model('follow_model');
        $needarr = array();
        $needarr['broker_id'] = $broker_id;
        $needarr['type'] = 1;
        $needarr['agency_id'] = $need__info['agency_id'];//门店ID
        $needarr['company_id'] = $need__info['company_id'];//总公司id
        $needarr['house_id'] = $housee_id;
        $bool = $this->follow_model->house_inster($needarr);
        //判断该房源是否设置了合作
        if ('1' == $datainfo['isshare'] || '2' == $datainfo['isshare']) {
          $follow_text = '';
          if ('1' == $datainfo['isshare']) {
            $follow_text = '是否合作:否>>是';
          } else if ('2' == $datainfo['isshare']) {
            $follow_text = '是否合作:否>>审核中';
          }
          $needarrt = array();
          $needarrt['broker_id'] = $broker_id;
          $needarrt['type'] = 1;
          $needarrt['agency_id'] = $need__info['agency_id'];//门店ID
          $needarrt['company_id'] = $need__info['company_id'];//总公司id
          $needarrt['house_id'] = $housee_id;
          $needarrt['text'] = $follow_text;
          $boolt = $this->follow_model->house_inster_share($needarrt);
        }

        $url_manage = '/sell/lists/';
        $page_text = '发布成功';

        $cid = $this->input->post('cid', TRUE);
        if ($cid > 0) {
          //$this->load->model('collections_model_new');
          $this->collections_model_new->change_house_status_byid($cid, $broker_id, 'sell_house_collect');
          $this->collections_model_new->add_sell_house_sub($housee_id, $cid);
        }

        //出售房源录入成功记录工作统计日志
        $this->info_count($housee_id, 1);
      }
      $house_add_arr['modify'] = 0;
    } else {
      $house_num_check = false;
      $url_manage = '/sell/publish/';
      $page_text = '发布失败,该房源已经存在';
    }

    $result = '';
    $sell_backinfo = array();
    $sell_dataifno = array();

    if (!empty($house_id)) {
      //修改房源唯一性验证
      $house_check = $this->check_house_modify($block_id, $door, $unit, $dong, $house_id);

      if ($house_check) {
        $house_num_check = true;
        $this->sell_house_model->set_search_fields(array());
        $this->sell_house_model->set_id($house_id);
        $sell_backinfo = $this->sell_house_model->get_info_by_id();//修改前的信息
        //判断原来的是否为合作
        if ('1' == $sell_backinfo['isshare'] || '2' == $sell_backinfo['isshare']) {
          $datainfo['set_share_time'] = $sell_backinfo['set_share_time'];
        }

        //基本设置，房客源制判断
        $house_private_check = true;
        //公盘私客制
        if (2 == $house_customer_system) {
          if ('2' == $sell_backinfo['nature'] && '1' == $datainfo['nature']) {
            $house_private_check = false;
            $house_private_check_text = '当前门店基本设置为公盘私客制';
          }
        } else if (3 == $house_customer_system) {
          //公盘制 获得当前经纪人的私盘数量
          $private_where_cond = 'broker_id = "' . $broker_id . '"' . ' and status = 1 and nature = 1';
          $private_num = $this->sell_house_model->get_housenum_by_cond($private_where_cond);
          if ('2' == $sell_backinfo['nature'] && '1' == $datainfo['nature'] && $private_num >= $sell_house_private_num) {
            $house_private_check = false;
            $house_private_check_text = '当前门店基本设置为公盘制';
          }
        } else {
          $house_private_check = true;
        }

        $is_reward = true;  //发布悬赏房源个数限制
        $is_reward_plus = true; //悬赏增幅限制

        if ('2' == $reward_type && $datainfo['cooperate_reward'] > 0 && $sell_backinfo['cooperate_reward'] != $datainfo['cooperate_reward']) {
          //旧值是否为空
          if (empty($sell_backinfo['cooperate_reward'])) {
            if (is_int($cooperate_reward_num) && $cooperate_reward_num < 5) {
              $is_reward = true;
            } else {
              $is_reward = false;
            }
          } else {
            $reward_add = intval($datainfo['cooperate_reward']) - intval($sell_backinfo['cooperate_reward']);
            if (is_int($reward_add) && $reward_add < 100) {
              $is_reward_plus = false;
            }
          }
        }
        $is_reward = true;  //发布悬赏房源个数限制去除

        $this->load->model('pic_model');
        $data['picinfo'] = $this->pic_model->find_house_pic_by_ids($sell_backinfo['pic_tbl'], $sell_backinfo['pic_ids']);
        $id_str = trim($sell_backinfo['pic_ids'], ',');
        $arr = explode(',', $id_str);
        $old_pic_inside_room = array();//室内图+户型图
        $picinfo3 = array();#委托协议书
        $picinfo4 = array();#身份证
        $picinfo5 = array();#房产证

        //房源图片数据重构
        foreach ($arr as $k => $v) {
          if (is_full_array($data['picinfo'])) {
            foreach ($data['picinfo'] as $key => $value) {
              if ($value['id'] == $v && ($value['type'] == 1 || $value['type'] == 2)) {
                $old_pic_inside_room[] = $value['url'];
              } else if ($value['id'] == $v && $value['type'] == 3) {
                $picinfo3[] = $value;
              } else if ($value['id'] == $v && $value['type'] == 4) {
                $picinfo4[] = $value;
              } else if ($value['id'] == $v && $value['type'] == 5) {
                $picinfo5[] = $value;
              }
            }
          }
        }
        //委托协议书、卖家身份证
        $pics['p_filename3'] = $this->input->post('p_filename3', TRUE);
        $pics['p_filename4'] = $this->input->post('p_filename4', TRUE);
        $pics['p_filename5'] = $this->input->post('p_filename5', TRUE);
        $pic3_back_str_0 = '';
        $pic3_back_str_1 = '';
        $pic3_back_str_2 = '';

        $pic4_back_str = '';
        $pic5_back_str = '';
        if (is_full_array($picinfo3[0])) {
          $pic3_back_str_0 = $picinfo3[0]['url'];
        }
        if (is_full_array($picinfo3[1])) {
          $pic3_back_str_1 = $picinfo3[1]['url'];
        }
        if (is_full_array($picinfo3[2])) {
          $pic3_back_str_2 = $picinfo3[2]['url'];
        }

        if (is_full_array($picinfo4[0])) {
          $pic4_back_str = $picinfo4[0]['url'];
        }
        if (is_full_array($picinfo5[0])) {
          $pic5_back_str = $picinfo5[0]['url'];
        }
        if (is_full_array($pics['p_filename3'])) {
          $pic3_str_0 = $pics['p_filename3'][0];
          $pic3_str_1 = '';
          $pic3_str_2 = '';
          if (isset($pics['p_filename3'][1]) && !empty($pics['p_filename3'][1])) {
            $pic3_str_1 = $pics['p_filename3'][1];
          }
          if (isset($pics['p_filename3'][2]) && !empty($pics['p_filename3'][2])) {
            $pic3_str_2 = $pics['p_filename3'][2];
          }
        }
        if (is_full_array($pics['p_filename4'])) {
          $pic4_str = $pics['p_filename4'][0];
        }
        if (is_full_array($pics['p_filename5'])) {
          $pic5_str = $pics['p_filename5'][0];
        }

        //根据合作资料，判断是否发送审核
        if (($pic3_back_str_0 != $pic3_str_0) || ($pic3_back_str_1 != $pic3_str_1) || ($pic3_back_str_2 != $pic3_str_2) || ($pic4_back_str != $pic4_str) || ($pic5_back_str != $pic5_str)) {
          $is_pic_change = true;
          $datainfo['cooperate_check'] = 2;
          if (1 == $datainfo['isshare'] && '2' == $reward_type) {
            $datainfo['isshare'] = 3;
          }
        } else {
          $is_pic_change = false;
        }

        //奖金方式，合作状态从否变成是、资料不变，提示重新上传资料。
        if ('0' == $sell_backinfo['isshare'] && intval($datainfo['isshare']) > 0 && 2 == intval($sell_backinfo['reward_type']) && 2 == intval($datainfo['reward_type']) && !$is_pic_change) {
          $coo_ziliao_check_3 = false;
        } else {
          $coo_ziliao_check_3 = true;
        }

        if (intval($datainfo['isshare']) > 0) {
          $coo_ziliao_check_1 = true;
          $coo_ziliao_check_2 = true;
          //委托协议书、卖家身份证、房产证验证 $coo_ziliao_check_1：悬赏合作必须三证齐全。$coo_ziliao_check_2：佣金悬赏必须传两证或者三证齐全。
          if ('2' == $reward_type) {
            if (is_full_array($pics['p_filename3']) && is_full_array($pics['p_filename4']) && is_full_array($pics['p_filename5'])) {
              $coo_ziliao_check_1 = true;
            } else {
              $coo_ziliao_check_1 = false;
            }
          } else if ('1' == $reward_type) {
            if (is_full_array($pics['p_filename4']) && is_full_array($pics['p_filename5'])) {
              $coo_ziliao_check_2 = true;
            } else {
              if (empty($pics['p_filename3']) && empty($pics['p_filename4']) && empty($pics['p_filename5'])) {
                $coo_ziliao_check_2 = true;
              } else {
                $coo_ziliao_check_2 = false;
              }
            }
            //审核失败状态，未修改资料图片状态，验证不通过
            //if('4'==$sell_backinfo['cooperate_check'] && $pic3_back_str==$pic3_str && //$pic4_back_str==$pic4_str){
            //    $coo_ziliao_check_1 = false;
            //}
          }
        } else {
          $coo_ziliao_check_1 = true;
          $coo_ziliao_check_2 = true;
        }

        //价格变动改变状态
        if ($datainfo['price'] && $sell_backinfo['price'] != $datainfo['price']) {
          if ($sell_backinfo['price'] < $datainfo['price']) {
            $datainfo['price_change'] = 1;
          } else {
            $datainfo['price_change'] = 2;
          }
        }

        if ($is_reward && $is_reward_plus && $coo_ziliao_check_1 && $coo_ziliao_check_2 && $coo_ziliao_check_3 && $house_private_check) {
          $old_bewrite = trim(strip_tags($sell_backinfo['bewrite']));
          //正则匹配，去掉‘&nbsp;’和空格
          $pattern = '/(\s|&nbsp;)+/';
          $old_bewrite2 = preg_replace($pattern, '', $old_bewrite);
          if (!empty($old_bewrite2)) {
            $sell_backinfo['bewrite'] = mb_substr($old_bewrite2, 0, 20) . '...';
          } else {
            $sell_backinfo['bewrite'] = '';
          }
          $result = $this->sell_house_model->update_info_by_id($datainfo);
          $sell_dataifno = $this->sell_house_model->get_info_by_id();//修改过后信息

          $new_bewrite = trim(strip_tags($sell_dataifno['bewrite']));
          //正则匹配，去掉‘&nbsp;’和空格
          $pattern = '/(\s|&nbsp;)+/';
          $new_bewrite_2 = preg_replace($pattern, '', $new_bewrite);
          if (!empty($new_bewrite_2)) {
            $sell_dataifno['bewrite'] = mb_substr($new_bewrite_2, 0, 20) . '...';
          } else {
            $sell_dataifno['bewrite'] = '';
          }

          //添加钥匙
          if (!$sell_backinfo['key_number'] && $sell_dataifno['keys'] && $sell_dataifno['key_number']) {
            $this->add_key($house_id, $sell_dataifno['key_number'], 'update');
            //出售房源钥匙提交记录工作统计日志
            $this->info_count($house_id, 6);
          }

          /***从有效状态改成其它状态，终止房源合作***/
          $current_status = $this->input->post('current_status', TRUE);
          if ($current_status == 1 && $datainfo['status'] != 1) {
            $stop_reason = '';

            switch ($datainfo['status']) {
              case '2':
                $stop_reason = 'reserve_house';
                break;
              case '3':
                $stop_reason = 'deal_house';
                break;
              case '4':
                $stop_reason = 'invalid_house';
                break;
            }

            $this->load->model('cooperate_model');
            $this->cooperate_model->stop_cooperate($house_id, 'sell', $stop_reason);
          }
          /***终止房源合作***/

          $msg = '房源修改成功！';
          $aa = '';
          //设置合作添加佣金比例
          if ($datainfo['isshare'] == 1) {
            $a_ratio = $this->input->post('a_ratio', TRUE);//甲方佣金分成比例
            $b_ratio = $this->input->post('b_ratio', TRUE);//已方佣金分成比例
            $buyer_ratio = $this->input->post('buyer_ratio', TRUE);//买方支付佣金比例
            $seller_ratio = $this->input->post('seller_ratio', TRUE);//卖方支付佣金比例

            $this->load->model('sell_house_share_ratio_model');
            $sell_backinfo_ratio = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
            $sell_backinfo['a_ratio'] = $sell_backinfo_ratio['a_ratio'];
            $sell_backinfo['b_ratio'] = $sell_backinfo_ratio['b_ratio'];
            $sell_backinfo['buyer_ratio'] = $sell_backinfo_ratio['buyer_ratio'];
            $sell_backinfo['seller_ratio'] = $sell_backinfo_ratio['seller_ratio'];
            $this->sell_house_share_ratio_model->update_house_ratio_by_rowid($house_id, $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio);
            $sell_dataifno_ratio = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
            $sell_dataifno['a_ratio'] = $sell_dataifno_ratio['a_ratio'];
            $sell_dataifno['b_ratio'] = $sell_dataifno_ratio['b_ratio'];
            $sell_dataifno['buyer_ratio'] = $sell_dataifno_ratio['buyer_ratio'];
            $sell_dataifno['seller_ratio'] = $sell_dataifno_ratio['seller_ratio'];
            //增加积分
            if ($sell_backinfo['isshare'] != $datainfo['isshare']) {
              //增加积分
              $this->load->model('api_broker_credit_model');
              $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
              $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $house_id), 1);
              //判断积分是否增加成功
              if (is_full_array($credit_result) && $credit_result['status'] == 1) {
                $credit_score += $credit_result['score'];
              }
              //增加等级分值
              $this->load->model('api_broker_level_model');
              $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
              $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $house_id), 1);
              //判断成长值是否增加成功
              if (is_full_array($level_result) && $level_result['status'] == 1) {
                $level_score += $level_result['score'];
              }
            }
          }
          //记录房源修改前的图片 比较图片的改过情况
          $new_inside = $this->input->post("p_filename2");
          $new_room = $this->input->post("p_filename1");
          if (!$new_inside) {
            $new_inside = array();
          }
          if (!$new_room) {
            $new_room = array();
          }
          $new_pic_inside_room = array_merge($new_inside, $new_room);
          $sell_backinfo['pic_inside_room'] = $old_pic_inside_room;
          $sell_dataifno['pic_inside_room'] = $new_pic_inside_room;
          $sell_cont = $this->insetmatch($sell_backinfo, $sell_dataifno);
          //修改房源日志录入
          $need__info = $this->user_arr;
          $this->load->model('follow_model');
          $needarrt = array();
          $needarrt['broker_id'] = $broker_id;
          $needarrt['type'] = 1;
          $needarrt['agency_id'] = $need__info['agency_id'];//门店ID
          $needarrt['company_id'] = $need__info['company_id'];//总公司id
          $needarrt['house_id'] = $house_id;
          $needarrt['text'] = $sell_cont;
          if (!empty($sell_cont)) {
            $boolt = $this->follow_model->house_save($needarrt);
            if (is_int($boolt) && $boolt > 0) {
              //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
              //获得基本设置房源跟进的天数
              //获取当前经济人所在公司的基本设置信息
              $this->load->model('house_customer_sub_model');
              $company_basic_data = $this->company_basic_arr;
              $house_follow_day = intval($company_basic_data['house_follow_spacing_time']);

              $select_arr = array('id', 'house_id', 'date');
              $this->follow_model->set_select_fields($select_arr);
              $where_cond = 'house_id = "' . $house_id . '" and follow_type != 2 and type = 1';
              $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
              if (count($last_follow_data) == 2) {
                $time1 = $last_follow_data[0]['date'];
                $time2 = $last_follow_data[1]['date'];
                $date1 = date('Y-m-d', strtotime($time1));
                $date2 = date('Y-m-d', strtotime($time2));
                $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
                if ($differ_day > $house_follow_day) {
                  $this->house_customer_sub_model->add_sell_house_sub($house_id, 1);
                } else {
                  $this->house_customer_sub_model->add_sell_house_sub($house_id, 0);
                }
              }
            }
          }
          $refer = $this->input->post('refer', TRUE);
          $pos = strpos($refer, 'group_publish');

          if ($pos) {
            $url_manage = $refer;
          } else {
            $url_manage = '/sell/lists';
          }

          if ($result) {
            //操作日志
            $add_log_param = array();
            $add_log_param['company_id'] = $broker_info['company_id'];
            $add_log_param['agency_id'] = $broker_info['agency_id'];
            $add_log_param['broker_id'] = $broker_id;
            $add_log_param['broker_name'] = $broker_info['truename'];
            $add_log_param['type'] = 3;
            $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id . ' ' . $sell_cont;
            $add_log_param['from_system'] = 1;
            $add_log_param['from_ip'] = get_ip();
            $add_log_param['mac_ip'] = '127.0.0.1';
            $add_log_param['from_host_name'] = '127.0.0.1';
            $add_log_param['hardware_num'] = '测试硬件序列号';
            $add_log_param['time'] = time();

            $this->operate_log_model->add_operate_log($add_log_param);

            $page_text = "修改成功";

            //添加价格变动
            if ($sell_backinfo['price'] != $sell_dataifno['price']) {
              $add_price_data = array(
                'house_id' => $sell_backinfo['id'],
                'price' => $sell_backinfo['price'],
                'createtime' => time()
              );
              $this->house_modify_history_model->add_sell_house_modify_history($add_price_data);
            }
          } else {
            $page_text = "修改失败";
          }

          $house_add_arr['modify'] = 1;

          //出售房源修改工作统计日志
          if ($sell_cont) {
            $this->info_count($house_id, 2);
          }
        }
      } else {
        $house_num_check = false;
        $url_manage = '/sell/publish/';
        $page_text = '修改失败,该房源已经存在';
      }


    }

    if ($house_id > 0 || $housee_id > 0) {
      $house_id = $house_id > 0 ? $house_id : $housee_id;
      $this->sell_house_model->set_id($house_id);
      $pics = $picinfo = array();
      //室内图、户型图
      $pics['p_filename2'] = $this->input->post('p_filename2', TRUE);
      $pics['p_fileids2'] = $this->input->post('p_fileids2', TRUE);
      $pics['add_pic'] = $this->input->post('add_pic', TRUE);
      $pics['p_filename1'] = $this->input->post('p_filename1', TRUE);
      $pics['p_fileids1'] = $this->input->post('p_fileids1', TRUE);

      //委托协议书、卖家身份证、房产证
      $pics['p_filename3'] = $this->input->post('p_filename3', TRUE);
      $pics['p_fileids3'] = $this->input->post('p_fileids3', TRUE);
      $pics['p_filename4'] = $this->input->post('p_filename4', TRUE);
      $pics['p_fileids4'] = $this->input->post('p_fileids4', TRUE);
      $pics['p_filename5'] = $this->input->post('p_filename5', TRUE);
      $pics['p_fileids5'] = $this->input->post('p_fileids5', TRUE);


      //根据上传图片情况，分类房源等级
      if (is_full_array($pics['p_filename2']) && is_full_array($pics['p_filename1'])) {
        $house_level = count($pics['p_filename2']) >= 3 ? 3 : 2;
      } else if (!is_full_array($pics['p_filename2']) && !is_full_array($pics['p_filename1'])) {
        $house_level = 0;
      } else {
        $house_level = 1;
      }

      if ($coo_ziliao_check_1 && $coo_ziliao_check_2 && $coo_ziliao_check_3) {
        $this->sell_house_model->set_id($house_id);
        $this->sell_house_model->update_info_by_id(array('house_level' => $house_level));

        $picinfo = $this->sell_house_model->insert_house_pic($pics, 'sell_house', $house_id, $datainfo['block_id']);

        if (is_full_array($pics['p_fileids2'])) {
          foreach ($pics['p_fileids2'] as $value) {
            //出售房源图片上传记录工作统计日志
            if ($value == 0) {
              $this->info_count($house_id, 3);
            }
          }
        }
        if (is_full_array($pics['p_fileids1'])) {
          foreach ($pics['p_fileids1'] as $value) {
            //出售房源图片上传记录工作统计日志
            if ($value == 0) {
              $this->info_count($house_id, 3);
            }
          }
        }
        if (is_full_array($pics['p_fileids3'])) {
          foreach ($pics['p_fileids3'] as $value) {
            //出售房源图片上传记录工作统计日志
            if ($value == 0) {
              $this->info_count($house_id, 3);
            }
          }
        }
        if (is_full_array($pics['p_fileids4'])) {
          foreach ($pics['p_fileids4'] as $value) {
            //出售房源图片上传记录工作统计日志
            if ($value == 0) {
              $this->info_count($house_id, 3);
            }
          }
        }
        if (is_full_array($pics['p_fileids5'])) {
          foreach ($pics['p_fileids5'] as $value) {
            //出售房源图片上传记录工作统计日志
            if ($value == 0) {
              $this->info_count($house_id, 3);
            }
          }
        }

        //删除 修改去掉的图片
        $pic_ids = $this->input->post('pic_ids', TRUE);
        if ($pic_ids != $picinfo['pic_ids']) {
          if ($pic_ids) {
            $before_arr = explode(',', trim($pic_ids, ','));
            $after_arr = explode(',', trim($picinfo['pic_ids'], ','));
            $left = '';

            foreach ($before_arr as $val) {
              if (!in_array($val, $after_arr)) {
                $left .= $val . ',';
              }
            }
            $this->load->model('pic_model');
            $this->pic_model->del_pic_by_ids($left, $picinfo['pic_tbl']);
          }
        }

        //设置封面
        if (is_full_array($pics['p_filename1']) || is_full_array($pics['p_filename2']) || is_full_array($pics['p_filename3']) || is_full_array($pics['p_filename4']) || is_full_array($pics['p_filename5'])) {
          if ($pics['add_pic']) {
            $picinfo['pic'] = $pics['add_pic'];
          } elseif ($pics['p_filename2']) //无选择，默认第一张为封面
          {
            $picinfo['pic'] = $pics['p_filename2'][0];
          }
          $this->sell_house_model->update_info_by_id($picinfo);
        }
      }
    }
    if ($datainfo['is_outside'] == 1) {
      $datainfo['id'] = $house_id;
      $city_spell = $this->user_arr['city_spell'];
      /*if($city_spell == 'cd'){
                $this->load->model('pic_model');
                //统计室内图的数量
                $where = array('tbl'=>'sell_house','type'=>1,'rowid'=>$house_id);
                $num1 = $this->pic_model->count_house_pic_by_cond($where);
                //统计户型图的数量
                $where = array('tbl'=>'sell_house','type'=>2,'rowid'=>$house_id);
                $num2 = $this->pic_model->count_house_pic_by_cond($where);
                if($num1 >= 5 && $num2 >= 1){
                    $this->load->model('pinganhouse_model');
                    $add_data = array('house_id'=>$house_id,'outside_time'=>time());
                    $this->pinganhouse_model->add_house($add_data);
                }
            }*/
      $this->load->model('api_broker_credit_model');
      $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
      $credit_result = $this->api_broker_credit_model->rsync_fang100($datainfo, 1);
      /*if($city_spell =='sz' || $city_spell =='km'){
                $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
                $credit_result1 = $this->api_broker_credit_model->fang100_activity($datainfo, 1);
                //判断积分是否增加成功
                if (is_full_array($credit_result1) && $credit_result1['status'] == 1)
                {
                    $credit_score +=$credit_result1['score'];
                }
            }*/
      $this->load->model('api_broker_level_model');
      $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
      $level_result = $this->api_broker_level_model->rsync_fang100($datainfo, 1);
      //判断积分是否增加成功
      if (is_full_array($credit_result) && $credit_result['status'] == 1) {
        $credit_score += $credit_result['score'];
      }
      //判断成长值是否增加成功
      if (is_full_array($level_result) && $level_result['status'] == 1) {
        $level_score += $level_result['score'];
      }
    }
    if ($credit_score) {
      $msg .= '+' . $credit_score . '积分';
    }
    if ($level_score) {
      $msg .= '+' . $level_score . '成长值';
    }
    $house_add_arr['msg'] = $msg;
    $house_add_arr['hosue_id'] = $housee_id;
    $house_add_arr['result'] = $result;
    $house_add_arr['is_reward'] = $is_reward;
    $house_add_arr['is_reward_plus'] = $is_reward_plus;
    $house_add_arr['house_num_check'] = $house_num_check;
    $house_add_arr['coo_ziliao_check_1'] = $coo_ziliao_check_1;
    $house_add_arr['coo_ziliao_check_2'] = $coo_ziliao_check_2;
    $house_add_arr['coo_ziliao_check_3'] = $coo_ziliao_check_3;
    $house_add_arr['house_private_check'] = $house_private_check;
    $house_add_arr['house_private_check_text'] = $house_private_check_text;
    echo json_encode($house_add_arr);
  }

  /**
   * 当用户点击要发布的时候
   *更改 sell / rent 表中 is_publish 字段值为 1
   * @access  public
   * @param  void
   * @return  void
   * author  angel_in_us
   * date 2015-06-10
   */
  public function change_is_pub($type, $house_id)
  {
    $result = $this->sell_model->update_ispub_by_houseid($type, $house_id);
    if ($type == 'sell') {
      echo '<script>location.href="/sell/lists/"</script>';
    } else if ($type == 'rent') {
      echo '<script>location.href="/rent/lists/"</script>';
    } else {
      echo '<script>location.href="/sell/lists/"</script>';
    }
  }


  /**
   * 修改权限判断
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function modify_per_check($modify_id)
  {
    $result_arr = array();
    $group_id = $this->user_arr['group_id'];
    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'is_seal', 'seal_broker_id', 'seal_start_time', 'seal_end_time'));
    $this->sell_house_model->set_id($modify_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    //是否封盘
    if ('1' == $owner_arr['is_seal']) {
      $seal_broker_id = $owner_arr['seal_broker_id'];
      $this->load->model('broker_info_model');
      $seal_broker_data = $this->broker_info_model->get_one_by(array('broker_id' => $seal_broker_id));
      if (is_full_array($seal_broker_data)) {
        $seal_broker_name = $seal_broker_data['truename'];
      }
      $result_arr['result'] = 'is_seal';
      $result_arr['seal_msg'] = '该房源已由' . $seal_broker_name . '封盘，<br>时间：' . date('Y.m.d', $owner_arr['seal_start_time']) . '-' . date('Y.m.d', $owner_arr['seal_end_time']);
    } else {
      //修改房源权限
      $house_modify_per = $this->broker_permission_model->check('8', $owner_arr);
      //修改房源关联门店权限
      $agency_house_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '4');
      if ('1' == $group_id) {
        $result_str = 'yes_per_modify';
      } else {
        if ($house_modify_per['auth']) {
          if ($agency_house_modify_per) {
            $result_str = 'yes_per_modify';
          } else {
            $result_str = 'no_per_modify';
          }
        } else {
          $result_str = 'no_per_modify';
        }
      }
      $result_arr['result'] = $result_str;
    }
    echo json_encode($result_arr);
    exit;
  }

  /**
   * 修改出售信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function modify($modify_id, $app_id = 0)
  {
    //模板使用数据
    $data = array();
    //获取当前登录人信息
    $broker_info = $this->user_arr;
    $this_broker_group_id = $this->user_arr['group_id'];
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //是否开启合作中心
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    //是否开启合作审核
    $data['check_cooperate'] = $company_basic_data['check_cooperate'];
    //添加楼盘
    $data['is_property_publish'] = $company_basic_data['is_property_publish'];
    //房源必须同步
    $data['is_fang100_insert'] = $company_basic_data['is_fang100_insert'];
    //查看保密信息必须写跟进
    $data['is_secret_follow'] = $company_basic_data['is_secret_follow'];

    //获取区属
    $data['district'] = $this->district_model->get_district();

    $modify_id = intval($modify_id);
    if (!$modify_id) {
      echo "<script>alert('参数有误!');history.go(-1);</script>";
      return;
    }

    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'is_seal', 'nature'));
    $this->sell_house_model->set_id($modify_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    //修改房源权限
    $house_modify_per = $this->broker_permission_model->check('8', $owner_arr);
    //修改房源关联门店权限
    $agency_house_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '4');
    //公私盘转换权限
    $property_type_per = $this->broker_permission_model->check('3', $owner_arr);

    //是否封盘
    if ('1' == $owner_arr['is_seal']) {
      $this->redirect_permission_none();
      exit();
    }
      if (!$house_modify_per['auth'] || $this_broker_group_id != '2') {
      $this->redirect_permission_none();
      exit();
    } else {
          if (!$agency_house_modify_per || $this_broker_group_id != '2') {
        $this->redirect_permission_none();
        exit();
      }
    }

    $this->sell_house_model->set_search_fields(array());

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');

    //获取出售信息基本配置资料
    $house_config = $this->house_config_model->get_config();
    //基本信息‘状态’数据处理
    if (!empty($house_config['status']) && is_array($house_config['status'])) {
      foreach ($house_config['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $house_config['status'][$k] = '暂不售';
        }
      }
    }
    $data['config'] = $house_config;

    $broker_id = intval($broker_info['broker_id']);
    //加载当前登录人的房源描述模板类
    $this->load->model('house_tmp_model');
    //得出当前登录经纪人的模板
    $where = "broker_id = " . $broker_id . " AND type = 1";   //type=1为出售
    $data['tmps'] = $this->house_tmp_model->get_tmps($where);
    $data['temp_num'] = count($data['tmps']);
    $data['group_id'] = $broker_info['group_id'];
    if ($property_type_per['auth']) {
      $data['property_type_per'] = 1;
    } else {
      $data['property_type_per'] = 2;
    }

    //查找有无此房源记录，没有则转到列表页
    $this->sell_house_model->set_id($modify_id);
    $house_detail = $this->sell_house_model->get_info_by_id();

    if (!$house_detail) {
      $this->jump('/sell/lists/', '没有发现您查询的记录');
      exit;
    }

    //获得当前经纪人的角色等级，判断店长以上or店长以下
    $role_level = intval($broker_info['role_level']);
    //店长跨店不可以查看私盘的保密信息
    if ($house_detail['nature'] == 1 && $data['view_secret_per']) {
      if ($role_level < 6 || ($role_level == 6 && $broker_info['agency_id'] == $house_detail['agency_id'])
        || $broker_info['broker_id'] == $house_detail['broker_id']
      ) //店长以上、本店的店长 及本人
      {
        $data['view_secret_per'] = true;
      } else {
        $data['view_secret_per'] = false;
      }
    }
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($owner_arr['broker_id'] != $broker_info['broker_id'] && $house_detail['nature'] == '1') {
        $this->redirect_permission_none();
        exit();
      }
    }

    $house_detail['district_name'] = $this->district_model->get_distname_by_id($house_detail['district_id']);
    $house_detail['street_name'] = $this->district_model->get_streetname_by_id($house_detail['street_id']);
    $house_detail['setting_arr'] = explode(',', $house_detail['setting']);
    $house_detail['sell_tag_arr'] = explode(',', $house_detail['sell_tag']);
    $house_detail['equipment_arr'] = explode(',', $house_detail['equipment']);
    $house_detail['shop_trade_arr'] = explode(',', $house_detail['shop_trade']);
    $house_detail['refer'] = $_SERVER['HTTP_REFERER'];
    $house_detail['commission_ratio'] = $this->sell_house_model->get_commission_ratio_id($house_detail['commission_ratio']);
    $data['house_detail'] = $house_detail;
    $block_id = intval($house_detail['block_id']);
    $is_lock = 0;
    $all_dong = array();
    $all_unit = array();
    $all_door = array();
    if (!empty($block_id)) {
      $community_info = $this->community_model->find_cmt($block_id);
      if (is_full_array($community_info)) {
        $is_lock = $community_info[0]['is_lock'];
      }

      $this_dong = $house_detail['dong'];
      $this_unit = $house_detail['unit'];
      $this_door = $house_detail['door'];
      $is_dong_in = false;
      $is_unit_in = false;
      $is_door_in = false;
      $this_dong_id = 0;
      $this_unit_id = 0;
      $this_door_id = 0;

      //获得该楼盘的栋座号
      $all_dong = $this->community_model->get_all_dong_by_cmtid($block_id);
      //判断当前栋座，是否属于楼盘栋座中
      if (is_full_array($all_dong)) {
        foreach ($all_dong as $k => $v) {
          if ($v['name'] == $this_dong) {
            $is_dong_in = true;
            $this_dong_id = $v['id'];
          }
        }
      }
      //如果栋座判断成功，查找对应的单元，并判断
      if ($is_dong_in) {
        //获得该栋座的单元号
        $all_unit = $this->community_model->get_all_unit_by_dongid($this_dong_id);
        if (is_full_array($all_unit)) {
          foreach ($all_unit as $k => $v) {
            if ($v['name'] == $this_unit) {
              $is_unit_in = true;
              $this_unit_id = $v['id'];
            }
          }
        }
      }
      //如果单元判断成功，查找对应的门牌，并判断
      if ($is_unit_in) {
        //获得该栋座的门牌号
        $all_door = $this->community_model->get_all_door_by_unitid($this_unit_id);
        if (is_full_array($all_door)) {
          foreach ($all_door as $k => $v) {
            if ($v['name'] == $this_door) {
              $is_door_in = true;
              $this_door_id = $v['id'];
            }
          }
        }
      }

      $data['is_dong_in'] = $is_dong_in;
      $data['is_unit_in'] = $is_unit_in;
      $data['is_door_in'] = $is_door_in;
      $data['all_unit'] = $all_unit;
      $data['all_door'] = $all_door;

    }
    $data['is_lock'] = $is_lock;
    $data['all_dong'] = $all_dong;

    //房源佣金分成数据
    $this->load->model('sell_house_share_ratio_model');
    $data['ratio_info'] = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($modify_id);

    $this->load->model('pic_model');
    //$data['picinfo'] = $this->pic_model->find_house_pic_by('sell_house',$modify_id);
    $data['picinfo'] = $this->pic_model->find_house_pic_by_ids($house_detail['pic_tbl'], $house_detail['pic_ids']);
    //按照原 sell_house 表中的 pic_ids 字段来展示房源图片
    $id_str = substr($house_detail['pic_ids'], 0, strlen($house_detail['pic_ids']) - 1);
    $arr = explode(',', $id_str);
    $picinfo1 = array();#室内图
    $picinfo2 = array();#户型图
    $picinfo3 = array();#委托协议书
    $picinfo4 = array();#身份证
    $picinfo5 = array();#房产证
    //房源图片数据重构
    foreach ($arr as $k => $v) {
      if (is_full_array($data['picinfo'])) {
        foreach ($data['picinfo'] as $key => $value) {
          if ($value['id'] == $v && $value['type'] == 1) {
            $picinfo1[] = $value;
          } else if ($value['id'] == $v && $value['type'] == 2) {
            $picinfo2[] = $value;
          } else if ($value['id'] == $v && $value['type'] == 3) {
            $picinfo3[] = $value;
          } else if ($value['id'] == $v && $value['type'] == 4) {
            $picinfo4[] = $value;
          } else if ($value['id'] == $v && $value['type'] == 5) {
            $picinfo5[] = $value;
          }
        }
      }
    }
    $data['picinfo1'] = $picinfo1;
    $data['picinfo2'] = $picinfo2;
    $data['picinfo3'] = $picinfo3;
    $data['picinfo4'] = $picinfo4;
    $data['picinfo5'] = $picinfo5;
    $data['app_id'] = $app_id;

    //跳回 来的 界面：群发管理:wjy
    $data['comdict'] = $this->input->get('comdict');
    $data['nopublish'] = $this->input->get('nopublish');
    //页面标题
    $data['page_title'] = '出售房源修改';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic2.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification.js,mls/js/v1.0/radio_checkbox_mod2.js,mls/js/v1.0/backspace.js,mls/js/v1.0/house_title_template.js,mls/js/v1.0/house_content_template.js');

    //加载发布页面模板
    $this->view('house/sell_house', $data);
  }


  /**
   * 出售房源列表页
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function lists($page = 1)
  {
    //遗留 判断是否登录
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $role_id = intval($broker_info['role_id']);

    //模板使用数据
    $data = array();
    $data['city_id'] = intval($broker_info['city_id']);
      //判断门店是否加入区域公盘
      $agency_id = $broker_info['agency_id'];
      $agency_indistrict = $this->cooperate_district_model->get_one_by_agency_id($agency_id);//门店所在区域公盘
      if (is_array($agency_indistrict)) {
          $data['is_join_district'] = 1;
      } else {
          $data['is_join_district'] = 0;
      }
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      //获取房源默认排序字段
      $house_list_order_field = $company_basic_data['house_list_order_field'];
      //获取默认查询时间
      $sell_house_query_time = $company_basic_data['sell_house_query_time'];
      //获取房源跟进无堪房红色警告时间
      $sell_house_check_time = $company_basic_data['sell_house_check_time'];
      //两次房源跟进红色警告时间
      $house_follow_spacing_time = $company_basic_data['house_follow_spacing_time'];
      //是否开启合作中心
      $open_cooperate = $company_basic_data['open_cooperate'];
      //是否开启合作审核
      $check_cooperate = $company_basic_data['check_cooperate'];
      //出售房源最后跟进天数
      $sell_house_follow_last_time1 = $company_basic_data['sell_house_follow_last_time1'];
      $sell_house_follow_last_time2 = $company_basic_data['sell_house_follow_last_time2'];
      //房源列表页字段
      $sell_house_field = $company_basic_data['sell_house_field'];
      //是否开启查看保密信息必须写跟进
      $is_secret_follow = $company_basic_data['is_secret_follow'];
      //楼盘名称只能选择录入
      $is_property_publish = $company_basic_data['is_property_publish'];
    } else {
      $is_property_publish = $sell_house_follow_last_time1 = $sell_house_follow_last_time2 = $check_cooperate = $open_cooperate = $house_follow_spacing_time = $sell_house_check_time = $sell_house_query_time = $house_list_order_field = $sell_house_field = $rent_house_field = $is_secret_follow = '';
        //房源列表字段
        if ('10' == $broker_info['role_level']) {
            $sell_house_field = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,19,20,21,22,23';
        }
    }
    $data['sell_house_follow_last_time1'] = $sell_house_follow_last_time1;//绿色
    $data['sell_house_follow_last_time2'] = $sell_house_follow_last_time2;//紫色
    $data['sell_house_check_time'] = $sell_house_check_time;//红色
    $data['house_follow_spacing_time'] = $house_follow_spacing_time;//橙色

    //房源列表字段
    if ('11' == $broker_info['role_level']) {
      $sell_house_field = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,19,20,21,22,23';
    }
    $sell_house_field_arr = array();
    if (!empty($sell_house_field)) {
      $sell_house_field_arr = explode(',', $sell_house_field);
    }
    $data['sell_house_field_arr'] = $sell_house_field_arr;

    $data['open_cooperate'] = $open_cooperate;
    $data['check_cooperate'] = $check_cooperate;
    $data['is_property_publish'] = $is_property_publish;

    //页面菜单
    $data['user_menu'] = $this->user_menu;
    $data['broker_id'] = $broker_id;
    $data['truename'] = $broker_info['truename'];
    $data['group_id'] = $broker_info['group_id'];

    //新权限
    //范围（1公司2门店3个人）
    $view_other_per_data = $this->broker_permission_model->check('1');
    $view_other_per = $view_other_per_data['auth'];
    $data['view_other_per'] = $view_other_per;

    $data['agency_id'] = $broker_info['agency_id'];//经纪人门店编号
    $data['agency_name'] = $broker_info['agency_name'];//获取经纪人所对应门店的名称

    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $data['company_id'] = $company_id;
    //获取当前经纪人在官网注册时的公司和门店名
    $this->load->model('broker_info_model');
    $register_info = $this->broker_info_model->get_register_info_by_brokerid(intval($broker_info['id']));
    $data['register_info'] = $register_info;


    $imadmin = $this->input->get('imadmin', TRUE);
    $data['imadmin'] = intval($imadmin);

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //是否提交了表单数据
    $is_submit_form = false;
    if (is_full_array($post_param)) {
      $is_submit_form = true;
    }
    $blockname = $this->input->post('blockname', true);
    //默认状态为有效
    if (!isset($post_param['status'])) {
      $post_param['status'] = 1;
    }
    //发布朋友圈筛选项和是否合作关系
    if ($post_param['isshare'] == '0') {
      $post_param['isshare_friend'] = 0;
    }

    //获取当前经纪人所在门店的数据范围
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_view_house');
    $all_access_agency_ids = '';
    if (is_full_array($access_agency_ids_data)) {
      foreach ($access_agency_ids_data as $k => $v) {
        $all_access_agency_ids .= $v['sub_agency_id'] . ',';
      }
      $all_access_agency_ids .= $broker_info['agency_id'];
      $all_access_agency_ids = trim($all_access_agency_ids, ',');
    } else {
      $all_access_agency_ids = $broker_info['agency_id'];
    }

    //查询房源条件
    $cond_where = "id > 0 ";
    //基本设置默认查询时间
    if ($post_param['create_time_range'] == 0) {
      //半年
      if ('1' == $sell_house_query_time) {
        $half_year_time = intval(time() - 365 * 0.5 * 24 * 60 * 60);
        $cond_where .= " AND createtime>= '" . $half_year_time . "' ";
      }
      //一年
      if ('2' == $sell_house_query_time) {
        $one_year_time = intval(time() - 365 * 24 * 60 * 60);
        $cond_where .= " AND createtime>= '" . $one_year_time . "' ";
      }
    }
    //默认公司
    $post_param['post_company_id'] = $this->user_arr['company_id'];
    if ($view_other_per) {
      //如果有权限，赋予初始查询条件
      if (!isset($post_param['post_agency_id']) && $company_basic_data['sell_house_indication_range'] > 1) {
        //这一句是初始门店
        $post_param['post_agency_id'] = $this->user_arr['agency_id'];
      }
      if (!isset($post_param['post_broker_id']) && $company_basic_data['sell_house_indication_range'] > 2) {
        //初始经纪人
        $post_param['post_broker_id'] = $this->user_arr['broker_id'];
      }

      $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

      if ($post_param['post_agency_id']) {
        $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($post_param['post_agency_id']);
      }

    } else {
      //本人
      $post_param['post_broker_id'] = $this->user_arr['broker_id'];
    }

    //判断是否提交表单,设置本页搜索条件cookie
    if ($is_submit_form) {
      $sell_list = array(
        'district' => $post_param['district'],
        'street' => $post_param['street'],
        'block_name' => $post_param['block_name'],
        'block_id' => $post_param['block_id'],
        'areamin' => $post_param['areamin'],
        'areamax' => $post_param['areamax'],
        'pricemin' => $post_param['pricemin'],
        'pricemax' => $post_param['pricemax'],
        'post_agency_id' => $post_param['post_agency_id'],
        'post_broker_id' => $post_param['post_broker_id'],
        'sell_type' => $post_param['sell_type'],
        'room' => $post_param['room'],
        'yearmin' => $post_param['yearmin'],
        'yearmax' => $post_param['yearmax'],
        'nature' => $post_param['nature'],
        'status' => $post_param['status'],
        'fitment' => $post_param['fitment'],
        'forward' => $post_param['forward'],
        'isshare' => $post_param['isshare'],
        'isshare_friend' => $post_param['isshare_friend'],
        'is_outside' => $post_param['is_outside'],
        'orderby_id' => $post_param['orderby_id'],
        'page' => $post_param['page'],
        'dong' => $post_param['dong'],
        'unit' => $post_param['unit'],
        'door' => $post_param['door'],
        'telno' => $post_param['telno'],
        'house_id' => $post_param['house_id'],
        'post_company_id' => $post_param['post_company_id'],
        'create_time_range' => $post_param['create_time_range'],
        'floormin' => $post_param['floormin'],
        'floormax' => $post_param['floormax'],
        'story_type' => $post_param['story_type'],
        'keys' => $post_param['keys'],
        'entrust' => $post_param['entrust'],
        'sell_tag' => $post_param['sell_tag'],
      );
      setcookie('sell_list', serialize($sell_list), time() + 3600 * 24 * 7, '/');
    } else {
      $sell_list_search = unserialize($_COOKIE['sell_list']);
      if (is_full_array($sell_list_search)) {
        $post_param['district'] = $sell_list_search['district'];
        $post_param['street'] = $sell_list_search['street'];
        $post_param['block_name'] = $sell_list_search['block_name'];
        $post_param['block_id'] = $sell_list_search['block_id'];
        $post_param['areamin'] = $sell_list_search['areamin'];
        $post_param['areamax'] = $sell_list_search['areamax'];
        $post_param['pricemin'] = $sell_list_search['pricemin'];
        $post_param['pricemax'] = $sell_list_search['pricemax'];
        $post_param['post_agency_id'] = $sell_list_search['post_agency_id'];
        $post_param['post_broker_id'] = $sell_list_search['post_broker_id'];
        $post_param['sell_type'] = $sell_list_search['sell_type'];
        $post_param['room'] = $sell_list_search['room'];
        $post_param['yearmin'] = $sell_list_search['yearmin'];
        $post_param['yearmax'] = $sell_list_search['yearmax'];
        $post_param['nature'] = $sell_list_search['nature'];
        $post_param['status'] = $sell_list_search['status'];
        $post_param['fitment'] = $sell_list_search['fitment'];
        $post_param['forward'] = $sell_list_search['forward'];
        $post_param['isshare'] = $sell_list_search['isshare'];
        $post_param['isshare_friend'] = $sell_list_search['isshare_friend'];
        $post_param['is_outside'] = $sell_list_search['is_outside'];
        $post_param['orderby_id'] = $sell_list_search['orderby_id'];
        $post_param['page'] = $sell_list_search['page'];
        $post_param['dong'] = $sell_list_search['dong'];
        $post_param['unit'] = $sell_list_search['unit'];
        $post_param['door'] = $sell_list_search['door'];
        $post_param['telno'] = $sell_list_search['telno'];
        $post_param['house_id'] = $sell_list_search['house_id'];
        $post_param['create_time_range'] = $sell_list_search['create_time_range'];
        $post_param['floormin'] = $sell_list_search['floormin'];
        $post_param['floormax'] = $sell_list_search['floormax'];
        $post_param['story_type'] = $sell_list_search['story_type'];
        $post_param['keys'] = $sell_list_search['keys'];
        $post_param['entrust'] = $sell_list_search['entrust'];
        $post_param['sell_tag'] = $sell_list_search['sell_tag'];
      }
    }

    if (empty($post_param['post_agency_id'])) {
      if (!empty($all_access_agency_ids)) {
        //查询房源条件
        $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";
      }
    }

    $post_param['block_name'] = trim($post_param['block_name']);
    $cond_or_like = array();
    if (!empty($post_param['block_name']) && $is_property_publish != '1') {
      $cond_or_like['like_key'] = array('block_name');
      $cond_or_like['like_value'] = $post_param['block_name'];
    }

    $post_param['sell_tag'] = trim($post_param['sell_tag']);
    $tag_or_like = array();
    if (!empty($post_param['sell_tag']) && $is_property_publish != '1') {
      $tag_or_like['like_key'] = array('sell_tag');
      $tag_or_like['like_value'] = $post_param['sell_tag'];
    }

    $cond_or_like = array_merge($cond_or_like,$tag_or_like);
    // print_r($cond_or_like);

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    //分页每页限制数(特定房源 合作 群发 采集 列表页需求)
    if ($post_param['limit_page']) {
      setcookie('limit_page', $post_param['limit_page'], time() + 3600 * 24 * 30, '/');
      $limit_page = $post_param['limit_page'];
    } elseif ($_COOKIE['limit_page']) {
      $limit_page = $_COOKIE['limit_page'];
    } else {
      $limit_page = $this->_limit;
    }
    $this->_init_pagination($page, $limit_page);

    if ($is_property_publish != '1') {
      unset($post_param['block_id']);
    }
    $data['post_param'] = $post_param;

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

//无公司门店时
      if ($this->user_arr['company_id'] <= 0 && $this->user_arr['agency_id'] <= 0) {
          $cond_where .= " AND broker_id = '" . $this->user_arr['broker_id'] . "'";
      }
    //设置默认排序字段
    if ('1' == $house_list_order_field) {
      $default_order = 13;
    } else if ('2' == $house_list_order_field) {
      $default_order = 7;
    } else {
      $default_order = 0;
    }
    $roomorder = (isset($post_param['orderby_id']) && $post_param['orderby_id'] != '') ? intval($post_param['orderby_id']) : $default_order;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
      $this->_total_count = $this->sell_house_model->get_count_by_cond($cond_where, $cond_or_like);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    if ($post_param['page'] > $pages) {
      $this->_init_pagination($pages, $limit_page);
    }
    $data['pages'] = $pages;
    //获取列表内容
    if (24 == $roomorder || 25 == $roomorder) {
      $list = $this->sell_house_model->get_list_by_cond_or_like($cond_where, $cond_or_like, $this->_offset, $this->_limit, 'is_sticky', 'desc', $order_arr['order_key'], $order_arr['order_by'], 'totalfloor', 'asc');
    } else {
      $list = $this->sell_house_model->get_list_by_cond_or_like($cond_where, $cond_or_like, $this->_offset, $this->_limit, 'is_sticky', 'desc', $order_arr['order_key'], $order_arr['order_by']);
    }
    //房源id数组

      $house_id_arr = array();
    //提醒加亮房源id
    $remind_house_id = array();

      //一段时间内跟进方式无‘堪房’，房源id
    $follow_no_kanfang_house_id = array();

    $this->load->model('api_broker_model');
    $brokeridstr = '';
    $remind_house_id = $follow_yes_kanfang_house_id2 = $yellow_house_id2 = array();
    if ($list) {
      foreach ($list as $key => $val) {
        $house_id_arr[] = $val['id'];
        $brokeridstr .= $val['broker_id'] . ',';
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $list[$key]['telno'] = $brokerinfo['phone'];
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
        // 最新跟进时间
        $list[$key]['genjintime'] = $val['updatetime'] > 0 ? date('Y-m-d H:i', $val['updatetime']) : '';
        //判断房源所属的门店基本设置，楼栋单元门牌归属保密信息。
        $agency_id = $val['agency_id'];
        $company_basic_data = $this->agency_basic_setting_model->get_data_by_agency_id($agency_id);
          if (is_full_array($company_basic_data)) {
          $is_secrecy_information = $company_basic_data[0]['is_secrecy_information'];
        } else {
          $company_basic_default_data = $this->agency_basic_setting_model->get_default_data();
              $is_secrecy_information = $company_basic_default_data[0]['is_secrecy_information'];
        }
          //房源所在区域与门店加入的区域公盘是否一致
          if ($val['district_id'] == $agency_indistrict['district_id']) {
              $list[$key]['house_in_district'] = 1;
          } else {
              $list[$key]['house_in_district'] = 0;
          }
          //检查该房源是否上传房产证
//          $this->load->model('pic_model');
//          $picinfo = $this->pic_model->get_house_pic_by('sell_house', $val['id'], 5,"district");
//          if($picinfo){
//              $list[$key]['is_have_certificate'] = 1;
//          }else{
//              $list[$key]['is_have_certificate'] = 0;
//          }
          //检查该房源是否已经发送到区域公盘
          if ($val['isshare_district'] == 1) {
              $list[$key]['is_send_district'] = 1;
          } else {
              $list[$key]['is_send_district'] = 0;
          }
        $list[$key]['is_secrecy_information'] = $is_secrecy_information;

      }
      //获得当前页面中需要提醒的房源
      $this->load->model('remind_model');
      $remind_where_cond = array(
        'broker_id' => $broker_id,
        'tbl' => 1,
        'status' => 0
      );
      $remind_house = $this->remind_model->get_remind($remind_where_cond, array('row_id', $house_id_arr));
      if (!empty($remind_house) && is_array($remind_house)) {
        foreach ($remind_house as $k => $v) {
          $remind_house_id[] = $v['row_id'];
        }
      }
      //获得当前页面中不需要红色警告的房源（有堪房跟进方式）
      $sell_house_check_day = intval($sell_house_check_time);
      $data['sell_house_check_day'] = $sell_house_check_day;
      $_where_cond_red = 'id > 0 ';
      $_where_cond_red .= 'and type = 1 and follow_way = 1 ';
      $_where_in_red = array('house_id', $house_id_arr);
      $this->load->model('follow_model');
      $follow_yes_kanfang_house_id = $this->follow_model->get_follow_house($_where_cond_red, $_where_in_red);
      $follow_yes_kanfang_house_id2 = array();
      if (is_array($follow_yes_kanfang_house_id) && !empty($follow_yes_kanfang_house_id)) {
        foreach ($follow_yes_kanfang_house_id as $k => $v) {
          $follow_yes_kanfang_house_id2[] = $v['house_id'];
        }
      }
      //获得当前页面中需要橙色警告的房源（最近的跟进明细间隔超过基本设置的天数）
      $this->load->model('house_customer_sub_model');
      $_where_in_rellow = array('id', $house_id_arr);
      $yellow_house_id = $this->house_customer_sub_model->get_sell_house_by_arrids($_where_in_rellow);
      $yellow_house_id2 = array();
      if (is_array($yellow_house_id) && !empty($yellow_house_id)) {
        foreach ($yellow_house_id as $k => $v) {
          $yellow_house_id2[] = $v['id'];
        }
      }
      //获得当前页面中，每条房源的最后跟进日期。（绿色和紫色）
      $_where_cond_last_follow = 'id > 0 ';
      $_where_cond_last_follow .= 'and type = 1 ';
      $_where_in_last_follow = array('house_id', $house_id_arr);
      $this->load->model('follow_model');
      $all_follow_data = $this->follow_model->get_follow_house_order_by_date($_where_cond_last_follow, $_where_in_last_follow);
      //房源id去重
      $all_last_follow_data = array('house_id' => array(), 'data' => array());
      if (is_full_array($all_follow_data)) {
        foreach ($all_follow_data as $k => $v) {
          if (!in_array($v['house_id'], $all_last_follow_data['house_id'])) {
            $all_last_follow_data['house_id'][] = $v['house_id'];
            $all_last_follow_data['data'][] = $v;
          }
        }
      }
      //出售房源最后跟进日期超过天数（绿色）
      $green_status = false;
      $follow_green_house_id = array();
      if (intval($sell_house_follow_last_time1) > 0 && is_full_array($all_last_follow_data['data'])) {
        $green_status = true;
        foreach ($all_last_follow_data['data'] as $k => $v) {
          $follow_date_time = strtotime($v['date']);
          if (time() - $follow_date_time > intval($sell_house_follow_last_time1) * 24 * 3600 && time() - $follow_date_time < intval($sell_house_follow_last_time2) * 24 * 3600) {
            $follow_green_house_id[] = $v['house_id'];
          }
        }
      }
      //出售房源最后跟进日期超过天数（紫色）
      $zi_status = false;
      $follow_zi_house_id = array();
      if (intval($sell_house_follow_last_time2) > 0 && is_full_array($all_last_follow_data['data'])) {
        $zi_status = true;
        foreach ($all_last_follow_data['data'] as $k => $v) {
          $follow_date_time = strtotime($v['date']);
          if (time() - $follow_date_time > intval($sell_house_follow_last_time2) * 24 * 3600) {
            $follow_zi_house_id[] = $v['house_id'];
          }
        }
      }
    }

    //判断是否有未结束的保密信息与跟进进程。
    $this->load->model('secret_follow_process_model');
    $where_cond_process = array(
      'broker_id' => $broker_info['broker_id'],
      'status' => 1
    );
    $process_query_result = $this->secret_follow_process_model->get($where_cond_process);
    $alert_house_id = 0;
    if ('1' == $is_secret_follow && is_full_array($process_query_result) && $broker_info['group_id'] != '1') {
      $alert_house_id = $process_query_result[0]['row_id'];
    }
    $data['alert_house_id'] = $alert_house_id;

    $data['list'] = $list;

    $data['remind_house_id'] = $remind_house_id;
    $data['follow_red_house_id'] = $follow_yes_kanfang_house_id2;
    $data['yellow_house_id'] = $yellow_house_id2;
    $data['green_status'] = $green_status;
    $data['follow_green_house_id'] = $follow_green_house_id;
    $data['zi_status'] = $zi_status;
    $data['follow_zi_house_id'] = $follow_zi_house_id;

    //底部最小化菜单
    $this->load->model('broker_info_min_log_model');
    $where_cond = array(
      'broker_id' => $broker_id
    );
    $query_result = $this->broker_info_min_log_model->get_log($where_cond);
    $sell_list_min_str = $query_result[0]['sell_house_list'];
    $sell_list_min_arr = array();
    $sell_list_min_arr2 = array();
    if (!empty($sell_list_min_str)) {
      $sell_list_min_arr = explode(',', trim($sell_list_min_str, ','));
    }
    if (is_full_array($sell_list_min_arr)) {
      foreach ($sell_list_min_arr as $k => $v) {
        $this->sell_house_model->set_search_fields(array('block_name', 'price', 'buildarea'));
        $this->sell_house_model->set_id(intval($v));
        $info = $this->sell_house_model->get_info_by_id();
        $name = '';
        $name = $info['block_name'] . '-' . intval($info['price']) . '万-' . intval($info['buildarea']) . '平米';
        $sell_list_min_arr2[] = array(
          'house_id' => $v,
          'name' => $name
        );
      }
    }
    //print_r($sell_list_min_arr2);
    $data['sell_list_min_arr'] = $sell_list_min_arr2;

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $config_data = $this->house_config_model->get_config();
    if (isset($config_data['status']) && !empty($config_data['status'])) {
      foreach ($config_data['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $config_data['status'][$k] = '暂不售';
        }
      }
    }
    $data['config'] = $config_data;

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
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
    $data['page_list'] = $this->page_list->show('5');//(特定房源 合作 群发 采集 列表页需求)


    //导出数据翻页处理
    $data['myoffset'] = intval($data['myoffset']);
    $data['mylimit'] = intval($data['mylimit']);
    $data['myoffset'] = $data['myoffset'] > 0 ? $data['myoffset'] : 1000;

    //页面标题
    $data['page_title'] = '出售房源列表页';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/house_manage.css');

//    //需要加载的JS
//    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,' . 'mls/js/v1.0/group_publish.js');
    //底部JS
      //需要加载的JS
      $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
          . 'mls/js/v1.0/group_publish.js,'
          . 'common/third/swf/swfupload.js,'
          . 'mls/js/v1.0/uploadpic2.js');
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js');
    //加载发布页面模板
    $this->view('house/sell_list', $data);
  }

  function del_search_cookie($type = '')
  {
    $result = false;
    if (!empty($type)) {
      $result = setcookie($type, '', time() - 1, '/');
    }
      $res = array();
      if ($result) {
          $res['status'] = 'success';
      } else {
          $res['status'] = 'failed';
      }
      echo json_encode($res);
    exit;
  }


  /**
   * 出售房源详情页
   *
   * @access  public
   * @param  void
   * @return  void
   */
  //小区详情
  public function details_district($house_id, $is_pub, $tab = 4, $hide_btn = 0, $app_id = 0)
  {
    $this->details($house_id, $is_pub, $tab, $hide_btn, $app_id);
  }

  //小区地图
  public function details_map($house_id, $is_pub, $tab = 4, $hide_btn = 0, $app_id = 0)
  {
    $this->details($house_id, $is_pub, $tab, $hide_btn, $app_id);
  }


  //房源图片
  public function details_image($house_id, $is_pub, $tab = 3, $hide_btn = 0, $app_id = 0)
  {
    $this->details($house_id, $is_pub, $tab, $hide_btn, $app_id);
  }


  //合作统计
  public function details_hezuo($house_id, $is_pub, $tab = 5, $hide_btn = 0, $app_id = 0)
  {
    $this->details($house_id, $is_pub, $tab, $hide_btn, $app_id);
  }


  //房源详情
  public function details_house($house_id, $is_pub, $tab = 1, $hide_btn = 0, $app_id = 0)
  {
    $this->details($house_id, $is_pub, $tab, $hide_btn, $app_id);
  }


  //保密信息
  public function details_secret($house_id, $is_pub, $tab = 2, $hide_btn = 0, $app_id = 0)
  {
    $this->details($house_id, $is_pub, $tab, $hide_btn, $app_id);
  }

  //视频房源
  public function details_video($house_id, $is_pub, $tab = 7, $hide_btn = 0, $app_id = 0)
  {
    $this->details($house_id, $is_pub, $tab, $hide_btn, $app_id);
  }

  /* 出售详情页面
     * @param $house_id int 房源编号
     * @param $is_pub 查看来源1房源列表、2合作房源列表，3合作中心合作列表页,4合同管理查看房源，5智能匹配查看房源(非合作)，6智能匹配查看房源(合作) 7区域公盘房源
     * @param $tab int TAB排序
     * @param $tab int 是否隐藏按钮
     */
  public function details($house_id, $is_pub = 1, $tab = 1, $hide_btn = 0, $app_id = 0)
  {
    $data['hide_btn'] = $hide_btn;
    //新权限 判断是否明文显示业主电话
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    //$is_phone_per = $this->broker_permission_model->check('9',$owner_arr);
    //$data['is_phone_per'] = $is_phone_per['auth'];

    $broker_id = $this->user_arr['broker_id'];
    $data['broker_id'] = $broker_id;
    $this_broker_group_id = $this->user_arr['group_id'];
    $is_pub = intval($is_pub);
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //合作中心列表和收藏列表
    if (2 == $is_pub) {
      if (is_full_array($company_basic_data)) {
        //是否开启合作中心
        $open_cooperate = $company_basic_data['open_cooperate'];
      } else {
        $open_cooperate = '';
      }
      $data['open_cooperate'] = $open_cooperate;

      //是否显示经纪人电话。如果当前经纪人未参与到该房源的合作，不显示。
      $this->load->model('cooperate_model');
      $where_cond = 'tbl = "sell" and rowid = "' . $house_id . '" and apply_type = 1';
      $this_house_cooperate_num = $this->cooperate_model->get_cooperate_num_apply($broker_id, $where_cond);
      //是自己的房源，展示电话号码
      if ($owner_arr['broker_id'] == $broker_id) {
        $is_phone_show = true;
      } else {
        if (is_int($this_house_cooperate_num) && $this_house_cooperate_num > 0) {
          $is_phone_show = true;
        } else {
          $is_phone_show = false;
        }
      }
      //检测是否已经合作
      $data['check_coop_reulst'] = $this->cooperate_model->check_is_cooped_by_houseid($house_id, 'sell', $broker_id);
    } else {
      //判断房源所属的门店基本设置，楼栋单元门牌归属保密信息。
      $agency_id = $owner_arr['agency_id'];
      $this->load->model('agency_basic_setting_model');
      $company_basic_data = $this->agency_basic_setting_model->get_data_by_agency_id($agency_id);
      if (!is_full_array($company_basic_data)) {
        $is_secrecy_information = $company_basic_data[0]['is_secrecy_information'];
      } else {
        $company_basic_default_data = $this->agency_basic_setting_model->get_default_data();
        $is_secrecy_information = $company_basic_data[0]['is_secrecy_information'];
      }
      $data['is_secrecy_information'] = $is_secrecy_information;
      $is_phone_show = true;
    }
    $data['is_phone_show'] = $is_phone_show;

    $house_id = intval($house_id);
    $data['house_id'] = $house_id;
    $data['is_pub'] = $is_pub;
    $data['tab'] = $tab;
    if ($is_pub == 1 || $is_pub == 4) //4为合同管理查看房源
    {
      //新权限
      //范围（1公司2门店3个人）
      //获得当前数据所属的经纪人id和门店id
      $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
      $this->sell_house_model->set_id($house_id);
      $owner_arr = $this->sell_house_model->get_info_by_id();
      $view_other_per = $this->broker_permission_model->check('1', $owner_arr);
        if (!$view_other_per['auth'] || $this_broker_group_id != '2') {
        $this->redirect_permission_none_iframe('js_pop_box_g');
        exit();
      }

    }
    $house_info = array();
    $this->sell_house_model->set_search_fields(array());
    $this->sell_house_model->set_id($house_id);
    $house_info = $this->sell_house_model->get_info_by_id();
    $house_info['nature_per'] = 1;
    if (is_full_array($house_info)) {
      //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
      $role_level = intval($this->user_arr['role_level']);
      //店长以下的经纪人不允许操作他人的私盘
      if (is_int($role_level) && $role_level > 6) {
        if ($owner_arr['broker_id'] != $this->user_arr['broker_id'] && $house_info['nature'] == '1') {
          $house_info['nature_per'] = 0;
        }
      }

      //房源合作佣金分配
      $this->load->model('sell_house_share_ratio_model');
      $house_money = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
      $data['house_money'] = $house_money;
      //获取经纪人联系方式
      $this->load->model('api_broker_model');
      $broker_agency_name = $this->api_broker_model->get_baseinfo_by_broker_id($house_info['broker_id']);
      $data['broker_agency_name'] = $broker_agency_name;
      //获取门店所属公司名
      $company_name = '';
      if (isset($broker_agency_name['company_id']) && !empty($broker_agency_name['company_id'])) {
        $company_where_cond = array(
          'id' => $broker_agency_name['company_id'],
          'company_id' => 0
        );
        $company_data = $this->agency_model->get_one_by($company_where_cond);
        if (is_full_array($company_data)) {
          $company_name = $company_data['name'];
        }
      }
      $data['company_name'] = $company_name;

      //加载出售基本配置MODEL
      $this->load->model('house_config_model');
      //获取出售信息基本配置资料
      $data['config'] = $this->house_config_model->get_config();
      //佣金分配
      if ($house_info['reward_type'] != 2) {
        $commission_ratio = $this->sell_house_model->get_commission_ratio_id($house_info['commission_ratio']);
        $house_info['commission_ratio_arr'] = $this->sell_house_model->get_commission_ratio($data['config']['commission_ratio'][$commission_ratio]);
      }
      if ($tab == 1 || $tab == 2 || $tab == 4) {
        $house_info['district_name'] = $this->district_model->get_distname_by_id($house_info['district_id']);
        $house_info['street_name'] = $this->district_model->get_streetname_by_id($house_info['street_id']);

        $house_info['setting_arr'] = explode(',', $house_info['setting']);
        $sell_tag_arr = explode(',', $house_info['sell_tag']);
        //判断标签个数，超过三个取前三个。
        if (is_full_array($sell_tag_arr)) {
//          $house_info['sell_tag_arr'] = array_slice($sell_tag_arr, 0, 3);
          $house_info['sell_tag_arr'] = $sell_tag_arr;
        }
        $house_info['equipment_arr'] = explode(',', $house_info['equipment']);
        $house_info['shop_trade_arr'] = explode(',', $house_info['shop_trade']);

        $house_info['telnos'] = $house_info['telno1'];
        $house_info['telnos'] .= !empty($house_info['telno2']) ? ', ' . $house_info['telno2'] : '';
        $house_info['telnos'] .= !empty($house_info['telno3']) ? ', ' . $house_info['telno3'] : '';
      }

      if ($tab == 2) {
        //保密信息编辑权限
        //获得当前数据所属的经纪人id和门店id
        $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'nature'));
        $this->sell_house_model->set_id($house_id);
        $owner_arr = $this->sell_house_model->get_info_by_id();
        //判断公私盘
        if ('1' == $owner_arr['nature']) {
          $get_secret_per = $this->broker_permission_model->check('139', $owner_arr);
        } else if ('2' == $owner_arr['nature']) {
          $get_secret_per = $this->broker_permission_model->check('137', $owner_arr);
        }
        //保密信息关联门店权限
        if ('1' == $owner_arr['nature']) {
          $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '38');
        } else if ('2' == $owner_arr['nature']) {
          $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '36');
        }
        $modify_secret_per = 1;
          if (!$get_secret_per['auth'] || $this_broker_group_id != '2') {
          $modify_secret_per = 0;
        } else {
              if (!$agency_secret_per || $this_broker_group_id != '2') {
            $modify_secret_per = 0;
          }
        }
        $data['modify_secret_per'] = $modify_secret_per;

        $data['where_cond'] = array('house_id' => $house_id);
        //今天
        $data['where_cond2'] = array('house_id' => $house_id, 'browertime >=' => strtotime(date('Y-m-d', time()) . ' 00:00:00'), 'browertime <= ' => strtotime(date('Y-m-d', time()) . ' 23:59:59'));
        //分组字段
        $group_by = 'broker_id';
        //分页开始
        $data['user_num'] = $this->sell_house_model->get_brower_log_sell_num($data['where_cond']);
        //浏览总数
        $data['group_by_num'] = $this->sell_house_model->get_brower_log_group_num($house_id);//分组总数
        $data['today_num'] = $this->sell_house_model->get_brower_log_sell_num($data['where_cond2']);//今天浏览总数
        $data['pagesize'] = 4; //设定每一页显示的记录数
        $data['pages'] = $data['group_by_num'] ? ceil($data['group_by_num'] / $data['pagesize']) : 1;
        //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
        //排序字段
        $order_by_array = array('browertime', 'desc');
        //房源浏览日志数据
        $brower_list = $this->sell_house_model->get_brower_log($data['where_cond'], $data['offset'], $data['pagesize'], $order_by_array, $group_by);
        $brower_list2 = array();

        //数据重构
        foreach ($brower_list as $k => $v) {
          if (!empty($v['browertime'])) {
            $where = array('house_id' => $house_id, 'broker_id' => $v['broker_id']);
            $today_browertime = array(strtotime(date('Y-m-d')), strtotime(date('Y-m-d', strtotime('+1 day'))));//今天的时间戳范围
            $v['browerdate'] = date('Y-m-d H:i:s', $v['browertime']);
            $v['brower_num'] = $this->sell_house_model->get_brower_log_sell_num($where);//总查阅次数
            $v['today_brower_num'] = $this->sell_house_model->get_today_brower_log_num($house_id, $v['broker_id'], $today_browertime);//今日查阅次数
            $first_brower = $this->sell_house_model->get_brower_log($where, 0, 0, array('browertime', 'asc'));//初次浏览记录
            $recent_brower = $this->sell_house_model->get_brower_log($where, 0, 0, array('browertime', 'desc'));//最近浏览记录
            $v['first_brower'] = $first_brower[0]['browertime'];
            $v['recent_brower'] = $recent_brower[0]['browertime'];
          }

          $brower_list2[] = $v;
        }
        $data['brower_list2'] = $brower_list2;
      }

      if ($tab == 3) {
        $this->load->model('pic_model');
        $picinfo = $this->pic_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
        $id_str = substr($house_info['pic_ids'], 0, strlen($house_info['pic_ids']) - 1);
        $arr = $id_str != '' ? explode(',', $id_str) : array();
        $picinfo1 = array();#室内图
        $picinfo2 = array();#户型图
        //房源图片数据重构
        if (is_full_array($arr) && is_full_array($picinfo)) {
          foreach ($arr as $k => $v) {
            foreach ($picinfo as $key => $value) {
              if ($value['id'] == $v && $value['type'] == 1) {
                $picinfo1[] = $value;
              } else if ($value['id'] == $v && $value['type'] == 2) {
                $picinfo2[] = $value;
              }
            }
          }
        }
        $data['shineipic'] = $picinfo1;
        $data['shineipic_count'] = count($picinfo1);
        $data['huxingpic'] = $picinfo2;
        $data['huxingpic_count'] = count($picinfo2);
      }
      $data['data_info'] = $house_info;

      $community_info = $this->community_model->find_cmt($house_info['block_id']);
      if ($community_info) {
        $data['xiaoquflag'] = 1;
        $data['xiaoqumapflag'] = $community_info[0]['b_map_x'] > 0 && $community_info[0]['b_map_y'] > 0 ? 1 : 0;
      } else {
        $data['xiaoquflag'] = 0;
        $data['xiaoqumapflag'] = 0;
      }
      if ($tab == 4) {
        //获取小区信息

        $community_arr = array();
        foreach ($community_info as $key => $val) {
          if (!empty($val['id'])) {
            $community_arr['id'] = $val['id'];
          }//id}
          $community_arr['address'] = $val['address'];//楼盘地址
          $community_arr['build_type'] = $val['build_type'];//物业类型
          $community_arr['build_date'] = $val['build_date'];//建筑年代
          $community_arr['property_year'] = $val['property_year'];//产权年限
          $community_arr['buildarea'] = $val['buildarea'];//建筑面积
          $community_arr['coverarea'] = $val['coverarea'];//	占地面积
          $community_arr['property_company'] = $val['property_company'];//物业公司
          $community_arr['developers'] = $val['developers'];//开发商
          $community_arr['parking'] = $val['parking'];//车位
          $community_arr['green_rate'] = $val['green_rate'];//绿化率
          $community_arr['plot_ratio'] = $val['plot_ratio'];//容积率
          $community_arr['property_fee'] = $val['property_fee'];//物业费
          $community_arr['build_num'] = $val['build_num'];//总栋数
          $community_arr['total_room'] = $val['total_room'];//总户数
          $community_arr['floor_instruction'] = $val['floor_instruction'];//楼层情况
          $community_arr['introduction'] = $val['introduction'];//楼盘介绍
          $community_arr['facilities'] = $val['facilities'];//设施
        }
        //获取楼盘图片
        $this->load->model('cmt_correction_base_model');
        $cmt_arr = $this->cmt_correction_base_model->find_cmt_pic_by($house_info['block_id']);
        $data['cmt_arr'] = $cmt_arr;
        $data['build_type'] = str_replace('#', '，', $community_arr['build_type']);
        $data['facilities'] = $community_arr['facilities'];
        $data['community_info'] = $community_arr;
      }

      if ($tab == 5) {
        //房源合作日志
        $this->load->model('cooperate_model');
        $brower_list3 = array();
        //分页开始
        $cond_where_cp = '';
        $brower_list3['cooperate_num'] = $this->cooperate_model->get_cooperate_num_by_houseid($house_id, 'sell');//浏览总数
        $brower_list3['pagesize'] = 2; //设定每一页显示的记录数
        $brower_list3['cooperate_pages'] = $brower_list3['cooperate_num'] ? ceil($brower_list3['cooperate_num'] / $brower_list3['pagesize']) : 0;  //计算总页数
        $brower_list3['cooperate_page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $brower_list3['cooperate_page'] = ($brower_list3['cooperate_page'] > $brower_list3['cooperate_pages'] && $brower_list3['cooperate_pages'] != 0) ? $brower_list3['cooperate_pages'] : $brower_list3['cooperate_page'];  //判断跳转页数
        $brower_list3['offset'] = $brower_list3['pagesize'] * ($brower_list3['cooperate_page'] - 1);   //计算记录偏移量
        $cooperate_log_list = $this->cooperate_model->get_cooperate_lists_by_houseid($house_id, 'sell', $cond_where_cp, $brower_list3['offset'], $brower_list3['pagesize']);
        if (!empty($cooperate_log_list)) {
          $ids = array();
          foreach ($cooperate_log_list as $key => $val) {
            $ids[] = $val['agentid_b'];
          }

          $agency_name = $this->cooperate_model->get_agency_att_by_aid($ids);
          if (!empty($agency_name) && is_array($agency_name)) {
            foreach ($cooperate_log_list as $key => $val) {
              $cooperate_log_list[$key]['agency_name_b'] = $agency_name[$val['agentid_b']];
            }
          }
        }
        //合作记录
        $brower_list3['cooperate_log_list'] = $cooperate_log_list;
        //合作基础配置文件
        $data['cooperate_conf'] = $this->cooperate_model->get_base_conf();
        $data['brower_list3'] = $brower_list3;

        $this->load->model('view_log_model');
        $cond_where = "h_id = '" . $house_id . "'";
        $this->_total_count = $this->view_log_model->get_view_log_num_by_hid('sell', $house_id);

        //分页开始
        $brower_list4['log_num'] = $this->view_log_model->get_view_log_num_by_hid('sell', $house_id);//浏览总数
        $brower_list4['pagesize'] = 2; //设定每一页显示的记录数
        $brower_list4['pages'] = $brower_list4['log_num'] ? ceil($brower_list4['log_num'] / $brower_list4['pagesize']) : 0;  //计算总页数
        $brower_list4['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $brower_list4['page'] = ($brower_list4['page'] > $brower_list4['pages'] && $brower_list4['pages'] != 0) ? $brower_list4['pages'] : $brower_list4['page'];  //判断跳转页数
        $brower_list4['offset'] = $brower_list4['pagesize'] * ($brower_list4['page'] - 1);   //计算记录偏移量
        $view_log_list = $this->view_log_model->get_view_log_list_by_hid('sell', $house_id, $brower_list4['offset'], $brower_list4['pagesize']);
        $brower_list4['view_log_list'] = $view_log_list;
        $view_log_list_all = $this->view_log_model->get_view_log_list_by_hid('sell', $house_id);
        $brower_list4['view_num'] = 0;
        $brower_list4['view_people'] = 0;
        if (is_array($view_log_list_all) && !empty($view_log_list_all)) {
          //查看总人数
          $brower_list4['view_people'] = count($view_log_list_all);

          //查看总次数
          for ($i = 0; $i < $brower_list4['view_people']; $i++) {
            $brower_list4['view_num'] += $view_log_list_all[$i]['num'];
          }
        }

        $data['brower_list4'] = $brower_list4;
      }

      if ($tab == 6) {
        $data['b_map_x'] = $community_info[0]['b_map_x'];
        $data['b_map_y'] = $community_info[0]['b_map_y'];
      }

      if (($is_pub == 2 || $is_pub == 3) && $tab == 1) {

        $broker_id_v = intval($this->user_arr['broker_id']);
        if ($house_id > 0 && is_array($house_info) && !empty($house_info)) {
          //记录访问日志
          $agency_id = $house_info['agency_id'];
          $broker_id = $house_info['broker_id'];
          $agency_id_v = intval($this->user_arr['agency_id']);
          $agency_name_v = strip_tags($this->user_arr['agency_name']);
          $broker_name_v = strip_tags($this->user_arr['truename']);
          $broker_telno_v = strip_tags($this->user_arr['phone']);
          $this->load->model('view_log_model');
          $this->view_log_model->add_house_view_log('sell', $house_id, $agency_id, $broker_id,
            $agency_id_v, $agency_name_v, $broker_id_v, $broker_name_v, $broker_telno_v);
        }
      }
      //获取收藏过的房源信息
      $status = '';
      $type = 'sell_house';
      $num = $this->house_collect_model->get_collect_ids_by_bid($this->user_arr['broker_id'], $type, 1);
      $arr = array();

      foreach ($num as $key => $val) {
        $arr[] = $val['rows_id'];
      }

      $data['num_id'] = $arr;
      $data['app_id'] = $app_id;

      if (is_full_array($company_basic_data)) {
        //是否开启保密信息必须写跟进
        $is_secret_follow = $company_basic_data[0]['is_secret_follow'];
      } else {
        $is_secret_follow = '';
      }
      //判断是否有未结束的保密信息与跟进进程。
      $this->load->model('secret_follow_process_model');
      $where_cond_process = array(
        'broker_id' => $broker_id,
        'status' => 1
      );
      $process_query_result = $this->secret_follow_process_model->get($where_cond_process);
      $alert_house_id = 0;
      if ('1' == $is_secret_follow && is_full_array($process_query_result)) {
        $alert_house_id = $process_query_result[0]['row_id'];
      }
      $data['alert_house_id'] = $alert_house_id;

      //页面标题
      $data['page_title'] = '出售信息详情页';
      //需要加载的css
      $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
          . ',mls/third/iconfont-ext/iconfont.css'
        . ',mls/css/v1.0/house_manage.css'
        . ',mls/css/v1.0/myStyle.css');
      //需要加载的JS
//      $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
        $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
            . 'common/third/jquery-ui-1.9.2.custom.min.js'
        );

      //底部JS
      $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/house.js,'
        . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic_0630.js');
      //加载详情页面模板
      $this->view('house/sell_info', $data);
    } else {
      //页面标题
      $data['page_title'] = '出售信息详情页';
      //需要加载的css
      $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
        . ',mls/css/v1.0/house_manage.css'
        . ',mls/css/v1.0/myStyle.css');
      //需要加载的JS
      $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');

      //底部JS
      $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/house.js,'
        . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic_0630.js');
      //加载详情页面模板
      $this->view('house/house_openwin', $data);
    }
  }

  //房源打印
  public function house_print($house_id = 0)
  {
    $house_details = array();
    if (intval($house_id) > 0) {
      $this->sell_house_model->set_search_fields(array('id', 'broker_id', 'agency_id', 'block_name', 'price', 'buildarea', 'room', 'hall', 'toilet', 'fitment', 'forward', 'broker_name', 'pic_tbl', 'pic_ids'));
      $this->sell_house_model->set_id($house_id);
      $house_details = $this->sell_house_model->get_info_by_id();
      //门店名
      $agency_data = $this->agency_model->get_by_id(intval($house_details['agency_id']));
      $agency_name = '';
      if (is_full_array($agency_data)) {
        $agency_name = $agency_data['name'];
      }
      $house_details['agency_name'] = $agency_name;
      //经纪人号码
      $this->load->model('api_broker_model');
      $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id(intval($house_details['broker_id']));
      $broker_phone = '';
      if (is_full_array($broker_info)) {
        $broker_phone = $broker_info['phone'];
      }
      $house_details['broker_phone'] = $broker_phone;
      //室内图、户型图
      $this->load->model('pic_model');
      $picinfo = $this->pic_model->find_house_pic_by_ids($house_details['pic_tbl'], $house_details['pic_ids']);
      $id_str = substr($house_details['pic_ids'], 0, strlen($house_details['pic_ids']) - 1);
      $arr = $id_str != '' ? explode(',', $id_str) : array();
      $picinfo1 = array();#室内图
      $picinfo2 = array();#户型图
      //房源图片数据重构
      if (is_full_array($arr) && is_full_array($picinfo)) {
        foreach ($arr as $k => $v) {
          foreach ($picinfo as $key => $value) {
            if ($value['id'] == $v && $value['type'] == 1) {
              $picinfo1 = $value;
              break 2;
            }
          }
        }
        foreach ($arr as $k => $v) {
          foreach ($picinfo as $key => $value) {
            if ($value['id'] == $v && $value['type'] == 2) {
              $picinfo2 = $value;
              break 2;
            }
          }
        }
      }
      $house_details['shineipic'] = $picinfo1['url'];
      $house_details['huxingpic'] = $picinfo2['url'];
      // 暂时禁用二维码 by alphabeta 20170405
      //$house_details['qrcode'] = get_qrcode(MLS_URL . '/' . $this->user_arr['city_spell'] . '/broker_info/broker_details/' . $this->user_arr['broker_id'], $this->user_arr['city_spell']);
    }
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $house_config = $this->house_config_model->get_config();
    $data['fitment_config'] = $house_config['fitment'];
    $data['forward_config'] = $house_config['forward'];
    $data['house_details'] = $house_details;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css'
      . ',mls/css/v1.0/house_new.css'
      . ',mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //加载详情页面模板
    $this->view('house/sell_house_print', $data);
  }


  /**
   * 添加出售房源浏览记录
   */
  public function add_brower_log()
  {
    $return_data = array();
    $user = $this->user_arr;
    $house_id = $this->input->get('house_id');
      $flag = $this->input->get('flag');
      $myself = $this->input->get('myself');//是否为我自己的房源 1：是 0：否
    $param_list = array(
      'house_id' => $house_id,
      'broker_id' => $user['broker_id'],
      'broker_name' => $user['truename'],
      'agency_id' => $user['agency_id'],
      'agency_name' => $user['agency_name'],
      'ip' => $_SERVER['REMOTE_ADDR'],
      'browertime' => time(),
    );

    $this->load->model('sell_house_model');
    $this->sell_house_model->add($param_list);

    //根据基本设置，处理查看保密信息与写跟进进程。
    $company_basic_data = $this->company_basic_arr;
      if ($flag != 'district' && is_full_array($company_basic_data)) {
      //是否开启查看保密信息必须写跟进
      $is_secret_follow = $company_basic_data['is_secret_follow'];
    } else {
      $is_secret_follow = '';
    }
      if ('1' == $is_secret_follow && $myself != 1) {//自己的房源不必强制写保密信息
      $this->load->model('secret_follow_process_model');
      $where_cond = array(
        'broker_id' => $user['broker_id'],
        'row_id' => $house_id,
        'type' => 1
      );
      $query_result = $this->secret_follow_process_model->get($where_cond);
      if (is_full_array($query_result)) {
        $id = intval($query_result[0]['id']);
        if (is_int($id) && $id > 0) {
          $update_arr = array(
            'status' => 1
          );
          $result = $this->secret_follow_process_model->update($id, $update_arr);
        }
      } else {
        $add_arr = array(
          'broker_id' => $user['broker_id'],
          'agency_id' => $user['agency_id'],
          'row_id' => $house_id,
          'type' => 1,
          'status' => 1
        );
        $result = $this->secret_follow_process_model->add($add_arr);
      }
      if (is_int($result) && $result > 0) {
        $return_data['msg'] = 'add_success';
      } else {
        $return_data['msg'] = 'add_failed';
      }
    } else {
      $return_data['msg'] = 'add_failed';
    }
    echo json_encode($return_data);
  }

  /**
   * 出售房源访问记录
   *
   * @access  public
   * @param  int $house_id 客源编号
   * @param  int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_brower_log($house_id = '1')
  {
    //加载客源浏览日志MODEL
    $this->load->model('sell_house_model');
    $data['where_cond'] = array('house_id' => $house_id);
    //分组字段
    $group_by = 'broker_id';
    //分页开始
    $data['user_num'] = $this->sell_house_model->get_brower_log_sell_num($data['where_cond']);
    $data['group_by_num'] = $this->sell_house_model->get_brower_log_group_num($house_id);
    $data['pagesize'] = 4; //设定每一页显示的记录数
    $data['pages'] = $data['group_by_num'] ? ceil($data['group_by_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    //排序字段
    $order_by_array = array('browertime', 'desc');
    //客源浏览日志数据
    $brower_list = $this->sell_house_model->get_brower_log($data['where_cond'], $data['offset'],
      $data['pagesize'], $order_by_array, $group_by);

    $brower_list2 = array();
    //数据重构
    foreach ($brower_list as $k => $v) {
      if (!empty($v['browertime'])) {
        $where = array('house_id' => $house_id, 'broker_id' => $v['broker_id']);
        $today_browertime = array(strtotime(date('Y-m-d')), strtotime(date('Y-m-d', strtotime('+1 day'))));//今天的时间戳范围
        $v['browerdate'] = date('Y-m-d H:i:s', $v['browertime']);
        $v['brower_num'] = $this->sell_house_model->get_brower_log_sell_num($where);//总查阅次数
        $v['today_brower_num'] = $this->rent_house_model->get_today_brower_log_num($customer_id, $v['broker_id'], $today_browertime);//今日查阅次数
        $first_brower = $this->sell_house_model->get_brower_log($where, 0, 0, array('browertime', 'asc'));//初次浏览记录
        $recent_brower = $this->sell_house_model->get_brower_log($where, 0, 0, array('browertime', 'desc'));//最近浏览记录
        $v['first_brower'] = date('Y-m-d H:i:s', $first_brower[0]['browertime']);
        $v['recent_brower'] = date('Y-m-d H:i:s', $recent_brower[0]['browertime']);
      }
      $brower_list2[] = $v;
    }
    echo json_encode($brower_list2);

  }

  /**
   * 查看出售房源访问记录
   *
   * @access  public
   * @param   int $house_id 房源编号
   * @param   int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_view_log($house_id = '1')
  {
    //客源访问日志信息
    $type = 'sell';
    $this->load->model('view_log_model');
    $cond_where = "h_id = '" . $house_id . "'";
    $this->_total_count = $this->view_log_model->get_view_log_num_by_hid($type, $house_id);

    //分页开始
    $data['log_num'] = $this->view_log_model->get_view_log_num_by_hid($type, $house_id);//浏览总数
    $data['pagesize'] = 2; //设定每一页显示的记录数
    $data['pages'] = $data['log_num'] ? ceil($data['log_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $view_log_list = $this->view_log_model->get_view_log_list_by_hid($type, $house_id, $data['offset'], $data['pagesize']);
    $view_log_list2 = array();
    foreach ($view_log_list as $k => $v) {
      $v['datetime'] = date('Y-m-d H:i:s', $v['datetime']);
      $view_log_list2[] = $v;
    }
    echo json_encode($view_log_list2);
  }

  /**
   * 房源申请合作分页请求
   *
   * @access  public
   * @param   int $house_id 房源编号
   * @param   int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_cooperate_log($house_id = '1')
  {
    //客源合作日志
    $this->load->model('cooperate_model');
    //分页开始
    $cond_where_cp = '';
    $data['cooperate_num'] = $this->cooperate_model->get_cooperate_num_by_houseid($house_id, 'sell');//浏览总数
    $data['pagesize'] = 2; //设定每一页显示的记录数
    $data['cooperate_pages'] = $data['cooperate_num'] ? ceil($data['cooperate_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['cooperate_page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['cooperate_page'] = ($data['cooperate_page'] > $data['cooperate_pages'] && $data['cooperate_pages'] != 0) ? $data['cooperate_pages'] : $data['cooperate_page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['cooperate_page'] - 1);   //计算记录偏移量

    $cooperate_log_list = $this->cooperate_model->get_cooperate_lists_by_houseid($house_id, 'sell', $cond_where_cp, $data['offset'], $data['pagesize']);
    $cooperate_log_list2 = array();
    //合作基础配置文件
    $cooperate_conf = $this->cooperate_model->get_base_conf();
    foreach ($cooperate_log_list as $k => $v) {
      $v['creattime'] = date('Y-m-d H:i:s', $v['creattime']);
      $v['esta'] = $cooperate_conf['esta'][$v['esta']];
      $cooperate_log_list2[] = $v;
    }
    //分店名称
    if (!empty($cooperate_log_list)) {
      $ids = array();
      foreach ($cooperate_log_list as $key => $val) {
        $ids[] = $val['agentid_b'];
      }

      $agency_name = $this->cooperate_model->get_agency_att_by_aid($ids);
      if (!empty($agency_name) && is_array($agency_name)) {
        foreach ($cooperate_log_list as $key => $val) {
          $cooperate_log_list2[$key]['agency_name_b'] = $agency_name[$val['agentid_b']];
        }
      }
    }
    echo json_encode($cooperate_log_list2);
  }


  /**
   * 出售匹配
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function match($house_id, $is_public = 0)
  {
    $house_id = intval($house_id);
    $is_public = intval($is_public);
    $data['is_public'] = $is_public;
    $house_info = array();
    $data['broker_id'] = $this->user_arr['broker_id'];
    if ($house_id <= 0) {
      $this->jump('/sell/lists/', '没有发现您查询的记录');
      return;
    }

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $house_config = $this->house_config_model->get_config();
    if (!empty($house_config['status']) && is_array($house_config['status'])) {
      foreach ($house_config['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $house_config['status'][$k] = '暂不售';
        }
      }
    }
    $data['config'] = $house_config;

    $this->sell_house_model->set_id($house_id);
    $house_info = $this->sell_house_model->get_info_by_id();
    $house_info['district_name'] = $this->district_model->get_distname_by_id($house_info['district_id']);
    $house_info['street_name'] = $this->district_model->get_streetname_by_id($house_info['street_id']);
    $this->load->model('api_broker_model');
    $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($house_info['broker_id']);
    $house_info['phone'] = $brokerinfo['phone'];
    $house_info['broker_name'] = $brokerinfo['truename'];
    $house_info['agency_name'] = $brokerinfo['agency_name'];
    $data['house_info'] = $house_info;

    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }

    //区属数据
    $arr_district = $this->district_model->get_district();
    $district_num = count($arr_district);

    for ($i = 0; $i < $district_num; $i++) {
      $temp_dist_arr[$arr_district[$i]['id']] = $arr_district[$i];
    }

    $data['district_arr'] = $temp_dist_arr;
    $dist_id = intval($house_info['district_id']);
    $street_id = intval($house_info['street_id']);

    if ($dist_id > 0) {
      $select_info['street_info'] =
        $this->district_model->get_street_bydist($dist_id);
      $data['select_info'] = $select_info;
    }

    //板块数据
    $arr_street = $this->district_model->get_street();
    $street_num = count($arr_street);
    for ($i = 0; $i < $street_num; $i++) {
      $temp_street_arr[$arr_street[$i]['id']] = $arr_street[$i];
    }
    $data['street_arr'] = $temp_street_arr;

    $cond_where = '';

    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    if (empty($post_param)) {
      $post_param['searchrange'] = 2;
      $post_param['searchtime'] = 3;

      //物业类型
      $post_param['sell_type'] = $house_info['sell_type'];

      //户型条件
      $post_param['room'] = $house_info['room'];

      //区属
      $post_param['district_id'] = $house_info['district_id'];

      //板块
      $post_param['street_id'] = 0;
    }

    //排序字段
    $house_time = 0;
    $roomorder = intval($house_time);
    $order_arr = $this->_get_orderby_arr($roomorder);
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_init_pagination($page, 5);

    $buildarea = $house_info['buildarea'];
    $price = $house_info['price'];
    //查询房源条件
    $cond_where = "status = 1 and $buildarea >= area_min and $buildarea <= area_max and $price >= price_min and $price <= price_max ";

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cusmoter_cond_str($post_param);
    $cond_where .= $cond_where_ext;
    if ($is_public) {
      $cond_where .= ' and is_share = 1';
    }
    //加载客源MODEL
    $this->load->model('buy_customer_model');
    $cond_where_range1 = $this->get_customer_range(1);
    $cond_where_range2 = $this->get_customer_range(2);
    $cond_where_range3 = $this->get_customer_range(3);
    $cond_where_range4 = $this->get_customer_range(4);

    $_cond_where[1] = $cond_where . $cond_where_range1;
    $matchcount[1] = $this->buy_customer_model->get_buynum_by_cond($_cond_where[1]);
    $_cond_where[2] = $cond_where . $cond_where_range2;
    $matchcount[2] = $this->buy_customer_model->get_buynum_by_cond($_cond_where[2]);
    $_cond_where[3] = $cond_where . $cond_where_range3;
    $matchcount[3] = $this->buy_customer_model->get_buynum_by_cond($_cond_where[3]);
    $_cond_where[4] = $cond_where . $cond_where_range4;
    $matchcount[4] = $this->buy_customer_model->get_buynum_by_cond($_cond_where[4]);
    $data['matchcount'] = $matchcount;

    //符合条件的总行数
    $this->_total_count = $matchcount[$post_param['searchrange']];
    $data['total_count'] = $this->_total_count;

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $customer_list =
      $this->buy_customer_model->get_buylist_by_cond($_cond_where[$post_param['searchrange']], $this->_offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    //循环获取经纪人姓名和门店信息
    if (count($customer_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();
      foreach ($customer_list as $key => $value) {
        $customer_list[$key]['genjintime'] = date('Y-m-d H:i', $value['updatetime']);
        $broker_id = intval($value['broker_id']);
        if ($broker_id > 0 && !in_array($broker_id, $broker_id_arr)) {
          array_push($broker_id_arr, $broker_id);
        }
      }

      //经纪人MODEL
      $this->load->model('api_broker_model');
      $broker_num = count($broker_id_arr);
      for ($i = 0; $i < $broker_num; $i++) {
        $broker_arr = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id_arr[$i]);
        $customer_broker_info[$broker_id_arr[$i]] = $broker_arr;
      }

      $data['customer_broker_info'] = $customer_broker_info;
    }

    $data['customer_list'] = $customer_list;

    //加载求购客户MODEL
    $this->load->model('buy_customer_model');
    //获取求购信息基本配置资料
    $data['conf_customer'] = $this->buy_customer_model->get_base_conf();

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
    $data['page_title'] = '出售房源匹配';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    //加载发布页面模板
    $this->view('house/sell_match', $data);
  }


  /**
   * 根据范围提交参数，获取查询条件
   */
  private function get_customer_range($form_param)
  {
    $this->load->model('api_broker_model');
    $this_user = $this->user_arr;
    $company_id = $this_user['company_id'];
    $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
    $agency_id = array();//该公司下所有的门店号
    foreach ($agencys as $k => $v) {
      $agency_id[] = $v['agency_id'];
    }
    $this_agency_id = array($this_user["agency_id"]);//当前经纪人门店号
    $other_agency_id = array_diff($agency_id, $this_agency_id);//当前公司其它门店号

    //$cond_where = 'is_share = 1 ';//合作
    //$cond_where = 'public_type = 2 ';//公盘
    if (isset($form_param) && !empty($form_param)) {
      if ('4' == $form_param) {
        $cond_where = " and broker_id ='" . $this_user["broker_id"] . "' ";//本人
      } else if ('3' == $form_param) {
          //  $cond_where = " and public_type = 2 and agency_id ='" . $this_user["agency_id"] . "' ";//所在门店
          $cond_where = " and agency_id ='" . $this_user["agency_id"] . "' ";//所在门店
      } else if ('2' == $form_param) {
        //根据数据范围，获得门店数据
        $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_house_match');
        $all_access_agency_ids = '';
        if (is_full_array($access_agency_ids_data)) {
          foreach ($access_agency_ids_data as $k => $v) {
            $all_access_agency_ids .= $v['sub_agency_id'] . ',';
          }
          $all_access_agency_ids .= $this_user['agency_id'];
          $all_access_agency_ids = trim($all_access_agency_ids, ',');
        } else {
          $all_access_agency_ids = $this_user['agency_id'];
        }
        $in_str = "(" . $all_access_agency_ids . ")";
          // $cond_where = " and public_type = 2 and agency_id in " . $in_str;
          $cond_where = " and agency_id in " . $in_str;
      } else if ('1' == $form_param) {
        $cond_where = ' and is_share = 1 ';//全网公盘
      }
    }
    return $cond_where;
  }


  /**
   * 出售匹配条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cusmoter_cond_str($form_param)
  {
    $cond_where = '';

    //楼盘条件
    $block_id = !empty($form_param['search_block_id']) ? intval($form_param['search_block_id']) : 0;
    if ($block_id > 0) {
      $cond_where .= " AND ( cmt_id1 = '" . $block_id . "' OR cmt_id2 = '" . $block_id . "' OR cmt_id3 = '" . $block_id . "' ) ";
    }

    //户型条件
    $room = intval($form_param['room']);
    if ($room) {
      $cond_where .= " AND room_max >= '" . $room . "' AND room_min <= '" . $room . "' ";
    }
    //区属
    $district_id = intval($form_param['district_id']);
    //板块
    $street_id = intval($form_param['street_id']);
    if ($street_id) {
      $cond_where .= " AND ( street_id1 = '" . $street_id . "' OR street_id2 = '" . $street_id . "' OR  street_id3 = '" . $street_id . "' ) ";
    } else if ($district_id) {
      $cond_where .= " AND ( dist_id1 = '" . $district_id . "' OR dist_id2 = '" . $district_id . "' OR  dist_id3 = '" . $district_id . "' ) ";
    }

    //物业类型
    $sell_type = intval($form_param['sell_type']);
    if ($sell_type) {
      $cond_where .= " AND property_type = '" . $sell_type . "' ";
    }

    //时间范
    $now_time = time();
    if (!empty($form_param['searchtime'])) {
      $searchtime = intval($form_param['searchtime']);
      switch ($searchtime) {
        case '1':
          $creattime = $now_time - 86400 * 30;
          break;

        case '2':
          $creattime = $now_time - 86400 * 90;
          break;

        case '3':
          $creattime = $now_time - 86400 * 180;
          break;

        case '4':
          $creattime = $now_time - 86400 * 365;
          break;

        default :
          $creattime = $now_time - 86400 * 180;
      }
      $cond_where .= " AND creattime>= '" . $creattime . "' ";
    }
    return $cond_where;
  }


  /**
   * 删除 出售
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function del($del_id = 0)
  {
    //遗留 判断有无删除此房源权限
    $isajax = $this->input->get('isajax', TRUE);

    if ($isajax) {
      $str = $this->input->get('str', TRUE);
    } else {
      $str = $del_id;
    }
    $this_broker_group_id = $this->user_arr['group_id'];

    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'status', 'company_id'));
    $this->sell_house_model->set_id($str);
    $house_data = $this->sell_house_model->get_info_by_id();
    $owner_arr = array(
      'broker_id' => $house_data['broker_id'],
      'agency_id' => $house_data['agency_id'],
      'company_id' => $house_data['company_id']
    );
    //修改房源权限
    $house_modify_per = $this->broker_permission_model->check('133', $owner_arr);
    //修改房源关联门店权限
    $agency_house_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '4');
      if (!$house_modify_per['auth'] || $this_broker_group_id != '2') {
      $this->redirect_permission_none();
      exit();
    }

    $arr = array(
      'status' => 5,
      'isshare' => 0,
      'isshare_friend' => 0
    );
    $cond_where = "id IN (0," . $str . ")";
    $up_num = $this->sell_house_model->update_info_by_cond($arr, $cond_where);

    if ($up_num > 0) {
      //操作日志
      $this->sell_house_model->set_search_fields(array('block_name', 'address', 'dong', 'unit', 'door'));
      $this->sell_house_model->set_id(intval($str));
      $datainfo = $this->sell_house_model->get_info_by_id();

      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 4;
      $add_log_param['text'] = '出售房源 ' . 'CS' . $str;
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);

      //删除房源，终止与房源有关系的合作
      $arr_houseid = explode(',', $str);
      $house_num = count($arr_houseid);
      $stop_reason = 'delete_house';
      $this->load->model('cooperate_model');
      for ($i = 0; $i < $house_num; $i++) {
        $this->cooperate_model->stop_cooperate($arr_houseid[$i], 'sell', $stop_reason);
      }
      //添加跟进记录
      $old_data = array('status' => $house_data['status']);
      $new_data = array('status' => 5);
      $follow_str = $this->insetmatch($old_data, $new_data);
      if (!empty($follow_str)) {
        $follow_add_data = array();
        $follow_add_data['broker_id'] = $this->user_arr['broker_id'];
        $follow_add_data['type'] = 1;
        $follow_add_data['agency_id'] = $this->user_arr['agency_id'];//门店ID
        $follow_add_data['company_id'] = $this->user_arr['company_id'];//总公司id
        $follow_add_data['house_id'] = $str;
        $follow_add_data['text'] = $follow_str;
        $this->load->model('follow_model');
        $add_result = $this->follow_model->house_save($follow_add_data);
      }
    }

    if ($isajax) {
      echo json_encode(array('result' => 'ok'));
    } else {
      $this->jump('/sell/lists/', '删除成功');
    }
  }


  /**
   * 页面ajax请求保密信息
   * @access  public
   * @param  int id
   * @return  array
   */
  public function get_secret_info()
  {
    $house_id = intval($this->input->get('house_id'));
      $flag = $this->input->get('district');//区域公盘房源暂不检查权限

    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $this_broker_group_id = $this->user_arr['group_id'];

    $data_info = array();

    //新权限
    //获得当前数据所属的经纪人id和门店id
      $this->sell_house_model->set_search_fields(array('broker_id', 'district_broker_id', 'agency_id', 'company_id', 'nature'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    //判断公私盘
      if ($flag !== 'district' && '1' == $owner_arr['nature']) {
      $get_secret_per = $this->broker_permission_model->check('138', $owner_arr);
      } else if ($flag !== 'district' && '2' == $owner_arr['nature']) {
      $get_secret_per = $this->broker_permission_model->check('136', $owner_arr);
    }

    //保密信息关联门店权限
      if ($flag !== 'district' && '1' == $owner_arr['nature']) {
        $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '37');
//        $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '2');
      } else if ($flag !== 'district' && '2' == $owner_arr['nature']) {
        $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '35');
//        $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '2');
    }
      if ($flag !== 'district') {
          if (!$get_secret_per['auth'] || $this_broker_group_id != '2') {
              $this->redirect_permission_none_iframe();
              exit();
          } else {
              if (!$agency_secret_per || $this_broker_group_id != '2') {
                  $this->redirect_permission_none_iframe();
                  exit();
              }
          }
      }


    if ($house_id > 0) {
      $this->sell_house_model->set_id($house_id);
      $select_feilds = array('id', 'broker_id', 'agency_id', 'dong', 'unit', 'door', 'owner', 'telno1',
        'telno2', 'telno3', 'lowprice', 'isshare', 'lock', 'idcare', 'proof', 'mound_num', 'record_num', 'nature', 'is_seal', 'seal_broker_id', 'seal_start_time', 'seal_end_time');
      $this->sell_house_model->set_search_fields($select_feilds);
      $data_info = $this->sell_house_model->get_info_by_id();
        $data_info['hidden_call_able'] = 0;//是否启用隐号拨打 否
        $data_info['join_district'] = 0; //是否处于区域公盘 否
        $data_info['myself'] = 0;//是否为自己的房源 否

        //判断是否是自己的房源
        if ($flag !== 'district') {//不是在区域公盘查看
            if ($owner_arr['broker_id'] == $this->user_arr['broker_id']) {
                $data_info['myself'] = 1;//是
            }
        } else {//是在区域公盘查看
            if ($owner_arr['district_broker_id'] == $this->user_arr['broker_id']) {
                $data_info['myself'] = 1;//是
            }
            //门店所在区域公盘
            $agency_indistrict = $this->cooperate_district_model->get_one_by_agency_id($broker_info['agency_id']);//门店所在区域公盘
            if (!empty($agency_indistrict)) {
                $data_info['join_district'] = 1;//是
                //判断区域是否开通隐号拨打
                $district = $this->cooperate_district_model->get_district("id = {$agency_indistrict['cooperate_district_id']}");
                if (!empty($district)) {
                    $data_info['hidden_call_able'] = $district['hidden_call_able'];//否
                }
            }
        }

        //判断门店是否可使用隐号拨打
        $phone = $this->hidden_call_model->get_phone_num_by_agencyid($broker_info['agency_id']);//获取门店虚拟号使用情况
        if (!empty($phone) && $phone['phone_num'] > 0 || $flag === 'district') {
            if ($data_info['hidden_call_able'] == 1 && $data_info['myself'] !== 1) {//当前房源不属于登录经纪人，则业主号码显示为引号拨打图标你
                //隐号拨打按钮
                $hidden_call_button = '<a id="hidden_call_button" onClick="hidden_call_button(' . $house_id . ',' . $data_info['telno1'] . ',0)" href="javascript:void(0)"><span class="iconfont">&#xe66d;</span> 联系业主</a>';
                $data_info['telno1'] = $hidden_call_button;
                $data_info['telno2'] = "";
                $data_info['telno3'] = "";
            }
        }
      //获得当前经纪人的角色等级，判断店长以上or店长以下
      $role_level = intval($broker_info['role_level']);
      //是否封盘
      if ($data_info['is_seal'] == '1') {
        $seal_broker_id = $data_info['seal_broker_id'];
        $this->load->model('broker_info_model');
        $seal_broker_data = $this->broker_info_model->get_one_by(array('broker_id' => $seal_broker_id));
        if (is_full_array($seal_broker_data)) {
          $seal_broker_name = $seal_broker_data['truename'];
        }
        $result_arr = array('errorCode' => 'is_seal');
        $result_arr['seal_msg'] = '该房源已由' . $seal_broker_name . '封盘，时间：' . date('Y.m.d', $data_info['seal_start_time']) . '-' . date('Y.m.d', $data_info['seal_end_time']);
        echo json_encode($result_arr);
        exit;
      }

      //判断是否锁定，有无权限查看（锁定状态下，发布人和锁定人可以查看）
        if ($flag == 'district' || !empty($data_info) && ($data_info['lock'] == 0 || in_array($broker_id, array($data_info['broker_id'], $data_info['lock'])))) {
        $data_info['telnos'] = $data_info['telno1'];
        $data_info['telnos'] .= !empty($data_info['telno2']) ? ', ' . $data_info['telno2'] : '';
        $data_info['telnos'] .= !empty($data_info['telno3']) ? ', ' . $data_info['telno3'] : '';
        $data_info['lowprice'] = strip_end_0($data_info['lowprice']);
        $this->info_count($house_id, 8);//记录查看保密信息的记录

        //操作日志
        $add_log_param = array();
        $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['agency_id'];
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $this->user_arr['truename'];
        $add_log_param['type'] = 45;
        $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id;
        $add_log_param['from_system'] = 1;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = '127.0.0.1';
        $add_log_param['from_host_name'] = '127.0.0.1';
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();

        $this->operate_log_model->add_operate_log($add_log_param);
      } else {
        $data_info = array();
      }
    }
    echo json_encode($data_info);
  }


  /**
   * 页面ajax请求根据属区获得对应板块
   * @access  public
   * @param  int 区属id
   * @return  array
   */
  public function find_street_bydis($districtID)
  {
    if (!empty($districtID)) {
      $districtID = intval($districtID);
      $street = $this->district_model->get_street_bydist($districtID);
      echo json_encode($street);
    } else {
      echo json_encode(array('result' => 'no result'));
    }
  }

  /**
   * 根据房源id，获得所在楼盘、公司、门店、经纪人数据
   * @access  public
   * @param  int 区属id
   * @return  array
   */
  public function get_data_by_house_id()
  {
    $result_arr = array();
    $house_id = intval($this->input->get('house_id'));
    $this->sell_house_model->set_id($house_id);
    $select_feilds = array();
    $this->sell_house_model->set_search_fields($select_feilds);
    $data_info = $this->sell_house_model->get_info_by_id();
    if (is_full_array($data_info)) {
      //楼盘数据
      $cmt_id = intval($data_info['block_id']);
      $cmt_data = $this->community_model->get_cmtinfo_longitude($cmt_id);
      if (is_full_array($cmt_data)) {
        //获得区属、板块名
        $dist_name = $this->district_model->get_distname_by_id(intval($cmt_data['dist_id']));
        $street_name = $this->district_model->get_streetname_by_id(intval($cmt_data['streetid']));
        $dist_name = '武侯';
        $street_name = '川音';
        //根据区属板块名，获得对应id
        $this->load->library('Pinganfang');
        $return_id_arr = $this->pinganfang->get_district_street_id_by_name($dist_name, $street_name);

        $result_arr['cmt_data'] = array(
          'id' => $cmt_data['id'],
          'name' => $cmt_data['cmt_name'],
          'city_id' => 816,
          'region_id' => $return_id_arr['district_id'],
          'sub_region_id' => $return_id_arr['street_id'],
          'address' => $cmt_data['address']
        );
      }
      //公司数据
      $company_id = intval($data_info['company_id']);
      $company_data = $this->agency_model->get_by_id($company_id);
      if (is_full_array($company_data)) {
        $result_arr['company_data'] = array(
          'company_id' => $company_data['id'],
          'city_id' => 816,
          'company_name' => $company_data['name'],
          'company_full_name' => $company_data['name']
        );
      }
      //门店数据
      $agency_id = intval($data_info['agency_id']);
      $agency_data = $this->agency_model->get_by_id($agency_id);
      if (is_full_array($agency_data)) {
        $result_arr['agency_data'] = array(
          'dept_id' => $agency_data['id'],
          'name' => $agency_data['name'],
          'dept_address' => $agency_data['address'],
          'company_id' => $agency_data['company_id'],
          'parent_dept_id' => 0
        );
      }
      //经纪人数据
      $broker_id = intval($data_info['broker_id']);
      //获得经纪人身份证号
      $this->load->model('auth_review_model');
      $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id, 0, 1);
      $idcard = 0;
      if (is_full_array($ident_info)) {
        $idcard = $ident_info['idcard'];
      }
      $broker_data = $this->broker_info_model->get_one_by(array('broker_id' => $broker_id));
      if (is_full_array($broker_data)) {
        $result_arr['broker_data'] = array(
          'user_id' => $broker_id,
          'user_name' => $broker_data['truename'],
          'user_mobile' => $broker_data['phone'],
          'user_card_no' => $idcard,
          'city_id' => 816,
          'area_id' => $return_id_arr['district_id'],
          'block_id' => $return_id_arr['street_id'],
          'company_id' => $broker_data['company_id'],
          'store_id' => $broker_data['agency_id']
        );
      }

      //房源数据
      if (is_full_array($broker_data)) {
        $house_config_pa = $this->pinganfang->get_house_config();
        $this->load->model('house_config_model');
        $house_config_mls = $this->house_config_model->get_config();
        //房源类型 默认其它
        $house_type = 8;
        $house_config_type = $house_config_mls['house_type'];
        if (!empty($data_info['house_type'])) {
          foreach ($house_config_pa['secondhand_housetype'] as $key => $value) {
            if ($value == $house_config_type[$data_info['house_type']]) {
              $house_type = $key;
            }
          }
        }
        //装修风格
        $house_config_fitment = $house_config_mls['fitment'];
        if ('毛坯' == $house_config_fitment[$data_info['house_type']]) {
          $fitment = 1;
        } else if ('简装' == $house_config_fitment[$data_info['house_type']]) {
          $fitment = 2;
        } else if ('中装' == $house_config_fitment[$data_info['house_type']]) {
          $fitment = 3;
        } else if ('精装' == $house_config_fitment[$data_info['house_type']]) {
          $fitment = 4;
        } else if ('豪装' == $house_config_fitment[$data_info['house_type']]) {
          $fitment = 5;
        } else {
          $fitment = 6;
        }

        //朝向类型 默认其它
        $house_config_forward = $house_config_mls['forward'];
        if (!empty($data_info['forward'])) {
          foreach ($house_config_pa['secondhand_toward'] as $key => $value) {
            if ($value == $house_config_forward[$data_info['forward']]) {
              $forward = $key;
            }
          }
        }

        $result_arr['house_data'] = array(
          'id' => $data_info['id'],//房源id
          'loupan_id' => $data_info['block_id'],//楼盘id
          'user_id' => $broker_id,//经纪人id
          //'unique_id' => $data_info['truename'],
          'title' => $data_info['title'],//房源标题
          'desc' => $data_info['bewrite'],//房源描述
          'price' => $data_info['price'],//价格
          'room_num' => $data_info['room'],//室
          'hall_num' => $data_info['hall'],//厅
          'toilet_num' => $data_info['toilet'],//卫
          'current_floor' => $data_info['floor'],//当前楼层
          'total_floor' => $data_info['totalfloor'],//总楼层
          'space' => $data_info['buildarea'],//面积
          'house_type' => $house_type,//房型
          'decoration' => $fitment,//装修风格
          'toward' => $forward,//朝向
          'building_year' => $data_info['buildyear'],//建筑年代
        );
      }
      //房源图片数据
      if (!empty($data_info['pic_ids'])) {
        $pic_ids = trim($data_info['pic_ids'], ',');
        $this->load->model('pic_model');
        $pic_info = $this->pic_model->find_house_pic_by_ids('upload', $pic_ids);
        if (is_full_array($pic_info)) {
          $pic_info_2 = array();
          foreach ($pic_info as $key => $value) {
            $arr = array(
              'image_id' => $value['id'],
              'pic_type' => $value['type'],
              'url' => $value['url'],
              'default' => $value['is_top']
            );
            $pic_info_2[] = $arr;
          }
          $result_arr['pic_data'] = array(
            'house_id' => $data_info['id'],
            'urls' => $pic_info_2
          );
        }
      }

    }
    echo json_encode($result_arr);
    exit;
  }

  /**
   * 新增楼盘
   *
   * @access  public
   * @param  void
   * @return  string
   */
  public function add_community()
  {
    $build_type_str = '';
    //物业类型
    $build_type_arr = $this->input->get('build_type');
    $districtname = $this->input->get('districtname');
    $streetname = $this->input->get('streetname');
    //物业费
    $property_fee = $this->input->get('property_fee');
    //绿化率
    $green_rate = $this->input->get('green_rate');
    if (!empty($build_type_arr) && is_array($build_type_arr)) {
      $build_type_str = implode('#', $build_type_arr);
    }
    $userInfo = $this->community_model->get_user_session();
    if (!empty($userInfo['company_id'])) {
        $companyInfo = $this->agency_model->get_by_id($userInfo['company_id']);
        if (!empty($companyInfo)) {
            $userInfo['company_name'] = $companyInfo['name'];
        }
    }
    $paramArray = array(
      'cmt_name' => trim($this->input->get('cmt_name')),//楼盘名称
      'dist_id' => trim($this->input->get('dist_id')),//区属
      'streetid' => trim($this->input->get('streetid')),//板块
      'address' => trim($this->input->get('address')),//地址
      'build_date' => $this->input->get('build_date'),//建筑年代
      'property_year' => $this->input->get('property_year'),//产权年限
      'buildarea' => $this->input->get('buildarea'),//建筑面积
      'coverarea' => $this->input->get('coverarea'),//占地面积
      'property_company' => $this->input->get('property_company'),//物业公司
      'developers' => $this->input->get('developers'),//开发商
      'parking' => $this->input->get('parking'),//车位
      'green_rate' => $green_rate / 100,//绿化率
      'plot_ratio' => $this->input->get('plot_ratio'),//容积率
      'property_fee' => $property_fee / 100,//物业费
      'build_num' => $this->input->get('build_num'),//总栋数
      'total_room' => $this->input->get('total_room'),//总户数
      'floor_instruction' => $this->input->get('floor_instruction'),//楼层情况
      'introduction' => $this->input->get('introduction'),//楼盘介绍
      'facilities' => $this->input->get('facilities'),//周边配套
      'build_type' => $build_type_str, //物业类型
      'status' => 3,
        'add_broker_id' => $userInfo['broker_id'],
        'add_broker_name' => $userInfo['truename'],
        'add_broker_phone' => $userInfo['phone'],
        'add_agency_name' => $userInfo['agency_name'],
        'add_company_name' => $userInfo['company_name'],
    );
    $return_data = '';
    if (empty($paramArray['cmt_name']) || empty($paramArray['dist_id']) || empty($paramArray['streetid'])
      || empty($paramArray['address'])
    ) {
      $return_data = '{"status":"100","msg":"楼盘名不能为空"}';
      //判断哪个必填字段为空，返回相关提示
      $preg_null_params = array();
      if (empty($paramArray['cmt_name'])) {
        $preg_null_params[] = array('name' => 'js_cmt_name', 'msg' => '楼盘名不能为空');
      }
      if (empty($paramArray['dist_id'])) {
        $preg_null_params[] = array('name' => 'district', 'msg' => '区属不能为空');
      }
      if (empty($paramArray['streetid'])) {
        $preg_null_params[] = array('name' => 'street', 'msg' => '板块不能为空');
      }
      if (empty($paramArray['address'])) {
        $preg_null_params[] = array('name' => 'com_address', 'msg' => '地址不能为空');
      }

      $return_array = array('status' => 100, 'list' => $preg_null_params);
      $return_data = json_encode($return_array);
    } else {
      //部分字段验证
      //数字正则(物业费、建筑面积、占地面积、绿化率、容积率、总栋数、总户数)
      $pattern = "/^[0-9]*(\.){0,1}[0-9]*$/";
      //数字、汉字正则(车位字段)
      $pattern2 = "/^[0-9]*[\x{4e00}-\x{9fa5}]*$/u";
      //数字、汉字、字母（物业公司、开发商）
      $pattern3 = "/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]*$/u";
      $property_fee_result = preg_match($pattern, $property_fee);//物业费
      $buildarea_result = preg_match($pattern, $paramArray['buildarea']);//建筑面积
      $coverarea_result = preg_match($pattern, $paramArray['coverarea']);//占地面积
      $green_rate_result = preg_match($pattern, $green_rate);//绿化率
      $plot_ratio_result = preg_match($pattern, $paramArray['plot_ratio']);//容积率
      $build_num_result = preg_match($pattern, $paramArray['build_num']);//总栋数
      $total_room_result = preg_match($pattern, $paramArray['total_room']);//总户数
      $parking_result = preg_match($pattern2, $paramArray['parking']);//停车位
      $property_company_result = preg_match($pattern3, $paramArray['property_company']);//物业公司
      $developers_result = preg_match($pattern3, $paramArray['developers']);//开发商

      if ($property_fee_result && $buildarea_result && $coverarea_result && $green_rate_result && $plot_ratio_result && $build_num_result && $total_room_result && $parking_result && $property_company_result && $developers_result) {
        $add_result = $this->community_model->add_community($paramArray);//楼盘数据入库
        if (!empty($add_result) && is_int($add_result)) {
          $return_data = '{"status":"1","id":"' . $add_result . '","cmt_name":"' . $paramArray['cmt_name'] . '","dist_id":"' . $paramArray['dist_id'] . '","districtname":"' . $districtname . '","streetid":"' . $paramArray['streetid'] . '","streetname":"' . $streetname . '","address":"' . $paramArray['address'] . '","build_date":"' . $paramArray['build_date'] . '"}';
          //外景图
          $location_pic_arr = $this->input->get('location_pic');
          if (!empty($location_pic_arr) && is_array($location_pic_arr)) {
            //封面
            $surface = $this->input->get('surface');
            $cmt_img_arr = array();
            //外景图数据重构
            foreach ($location_pic_arr as $k => $v) {
              $img_arr = array();
              $img_arr['cmt_id'] = intval($add_result);
              $img_arr['image'] = $v;
              $img_arr['pic_type'] = 3;
              $img_arr['creattime'] = time();
              $img_arr['ip'] = $_SERVER['REMOTE_ADDR'];
              if ($surface == $v) {
                $img_arr['is_surface'] = 1;
              } else {
                $img_arr['is_surface'] = 0;
              }
              $cmt_img_arr[] = $img_arr;
            }
            foreach ($cmt_img_arr as $k => $v) {
              $this->community_model->add_cmt_image($v);//楼盘图片入库
            }
          }
        } else {
          $return_data = '{"status":"0","msg":"操作失败"}';
        }
      } else {
        //判断哪个字段验证不通过，返回相关提示
        $preg_fail_params = array();
        if (!$property_fee_result) {
          $preg_fail_params[] = array('name' => 'property_fee', 'msg' => '物业费只能为数字');
        }
        if (!$buildarea_result) {
          $preg_fail_params[] = array('name' => 'buildarea2', 'msg' => '建筑面积只能为数字');
        }
        if (!$coverarea_result) {
          $preg_fail_params[] = array('name' => 'coverarea', 'msg' => '占地面积只能为数字');
        }
        if (!$green_rate_result) {
          $preg_fail_params[] = array('name' => 'green_rate', 'msg' => '绿化率只能为数字');
        }
        if (!$plot_ratio_result) {
          $preg_fail_params[] = array('name' => 'plot_ratio', 'msg' => '容积率只能为数字');
        }
        if (!$build_num_result) {
          $preg_fail_params[] = array('name' => 'build_num', 'msg' => '总栋数只能为数字');
        }
        if (!$total_room_result) {
          $preg_fail_params[] = array('name' => 'total_room', 'msg' => '总户数只能为数字');
        }
        if (!$parking_result) {
          $preg_fail_params[] = array('name' => 'parking', 'msg' => '停车位只能为数字、汉字');
        }
        if (!$property_company_result) {
          $preg_fail_params[] = array('name' => 'property_company', 'msg' => '物业公司只能为数字、汉字、字母');
        }
        if (!$developers_result) {
          $preg_fail_params[] = array('name' => 'developers', 'msg' => '开发商只能为数字、汉字、字母');
        }
        $return_array = array('status' => 600, 'list' => $preg_fail_params);
        $return_data = json_encode($return_array);
      }

    }
    echo $return_data;
    exit;
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
   * 出售列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';

    //房源编号
    if (isset($form_param['house_id']) && !empty($form_param['house_id'])) {
        $house_id = trim($form_param['house_id']);
        if ('CS' == substr($house_id, 0, 2)) {
            $house_id = substr($house_id, 2);
        }
        $house_id = intval($house_id);
        $cond_where .= " AND id = '" . $house_id . "'";
    }

    //是否公共数据
    if (isset($form_param['is_public']) && $form_param['is_public'] != '') {
      $cond_where .= " AND is_public = '" . $form_param['is_public'] . "'";
    }
      //是否区域公盘公共数据
      if (isset($form_param['is_district_public']) && $form_param['is_district_public'] != '') {
          $cond_where .= " AND is_district_public = '" . $form_param['is_district_public'] . "'";
      }
    //是否合作
    if (isset($form_param['isshare']) && $form_param['isshare'] != '') {
      $cond_where .= " AND isshare = '" . $form_param['isshare'] . "'";
    }

    //是否发布到朋友圈
    if (isset($form_param['isshare_friend']) && $form_param['isshare_friend'] != '') {
      $cond_where .= " AND isshare = 1 AND isshare_friend = '" . $form_param['isshare_friend'] . "'";
    }

    //栋座号
    if (isset($form_param['dong']) && $form_param['dong'] != '') {
      $cond_where .= " AND dong = '" . $form_param['dong'] . "'";
    }

    //单元号
    if (isset($form_param['unit']) && $form_param['unit'] != '') {
      $cond_where .= " AND unit = '" . $form_param['unit'] . "'";
    }

    //门牌号
    if (isset($form_param['door']) && $form_param['door'] != '') {
      $cond_where .= " AND door = '" . $form_param['door'] . "'";
    }

    //业主电话
    if (isset($form_param['telno']) && $form_param['telno'] != '') {
        $cond_where .= " AND (telno1 like '%" . $form_param['telno'] . "%' or telno2 like '%" . $form_param['telno'] . "%' or telno3 like '%" . $form_param['telno'] . "%') ";
    }

    //是否同步
    if (isset($form_param['is_outside']) && $form_param['is_outside'] != '') {
      $cond_where .= " AND is_outside = '" . $form_param['is_outside'] . "'";
    }

    //板块 ，区属
    $street = isset($form_param['street']) ? intval($form_param['street']) : 0;
    $district = isset($form_param['district']) ? intval($form_param['district']) : 0;
    if ($street) {
      $cond_where .= " AND street_id = '" . $street . "'";
    } elseif ($district) {
      $cond_where .= " AND district_id = '" . $district . "'";
    }

    //楼盘ID
    if (!empty($form_param['block_name']) && $form_param['block_id'] > 0) {
      $cond_where .= " AND block_id = '" . $form_param['block_id'] . "'";
    }

    //面积条件
    if (!empty($form_param['areamin'])) {
      $areamin = $form_param['areamin'];
      $cond_where .= " AND buildarea >= '" . $areamin . "'";
    }

    if (!empty($form_param['areamax'])) {
      $areamax = $form_param['areamax'];
      $cond_where .= " AND buildarea <= '" . $areamax . "'";
    }

    //视频条件
    if (!empty($form_param['is_video'])) {
      if ('1' == $form_param['is_video']) {
        $cond_where .= " AND (video_id is not null and video_id <>'' and video_id <>'0') ";
      } else if ('2' == $form_param['is_video']) {
        $cond_where .= " AND (video_id = '0' OR video_id = '') ";
      }
    }

    //价格条件
    if (!empty($form_param['pricemin'])) {
      $pricemin = $form_param['pricemin'];
      $cond_where .= " AND price >= '" . $pricemin . "'";
    }

    if (!empty($form_param['pricemax'])) {
      $pricemax = $form_param['pricemax'];
      $cond_where .= " AND price <= '" . $pricemax . "'";
    }

    //楼层floor
    $floor_min = isset($form_param['floormin']) ? intval($form_param['floormin']) : 0;
    $floor_max = isset($form_param['floormax']) ? intval($form_param['floormax']) : 0;
    if ($floor_min || $floor_max) {
      $cond_where .= " AND floor >= '" . $floor_min . "'";
      $cond_where .= " AND floor <= '" . $floor_max . "'";
    }

    //物业类型条件
    if (isset($form_param['sell_type']) && !empty($form_param['sell_type']) && $form_param['sell_type'] > 0) {
      $sell_type = intval($form_param['sell_type']);
      $cond_where .= " AND sell_type = '" . $sell_type . "'";
    }

    //户型条件
    if (isset($form_param['room']) && !empty($form_param['room']) && $form_param['room'] > 0) {
      $room = intval($form_param['room']);
      if ($room <= 6) {
        $cond_where .= " AND room = '" . $room . "'";
      } else if ($room > 6) {
        $cond_where .= " AND room >= '" . $room . "'";
      }
    }

    //状态条件
    if (isset($form_param['status']) && !empty($form_param['status']) && $form_param['status'] > 0) {
      $status = intval($form_param['status']);
      $cond_where .= " AND status = '" . $status . "'";
    }

    //房龄条件
    if (!empty($form_param['yearmin'])) {
      $yearmin = $form_param['yearmin'];
      $cond_where .= " AND buildyear >= '" . $yearmin . "'";
    }

    if (!empty($form_param['yearmax'])) {
      $yearmax = $form_param['yearmax'];
      $cond_where .= " AND buildyear <= '" . $yearmax . "'";
    }

    //楼层比例高中低
    if (!empty($form_param['floor_scale'])) {
      $floor_scale = intval($form_param['floor_scale']);
      if (is_int($floor_scale) && $floor_scale > 0) {
        if (1 == $floor_scale) {
          $cond_where .= " AND floor_scale > 0.7";
        } else if (2 == $floor_scale) {
          $cond_where .= " AND floor_scale >= 0.4 AND floor_scale <= 0.7";
        } else if (3 == $floor_scale) {
          $cond_where .= " AND floor_scale < 0.4";
        }
      }
    }

    //性质条件
    if (isset($form_param['nature']) && !empty($form_param['nature']) && $form_param['nature'] > 0) {
      $nature = intval($form_param['nature']);
      $cond_where .= " AND nature = '" . $nature . "'";
    }

    //装修条件
    if (isset($form_param['fitment']) && !empty($form_param['fitment']) && $form_param['fitment'] > 0) {
      $fitment = intval($form_param['fitment']);
      $cond_where .= " AND fitment = '" . $fitment . "'";
    }

    //朝向条件
    if (isset($form_param['forward']) && !empty($form_param['forward']) && $form_param['forward'] > 0) {
      $forward = intval($form_param['forward']);
      $cond_where .= " AND forward = '" . $forward . "'";
    }

    //是否悬赏
    if (isset($form_param['is_cooperate_reward']) && !empty($form_param['is_cooperate_reward']) && $form_param['is_cooperate_reward'] > 0) {
      $is_cooperate_reward = intval($form_param['is_cooperate_reward']);
      if ($is_cooperate_reward === 1) {
        $cond_where .= " AND cooperate_reward > 0 ";
      } else if ($is_cooperate_reward === 2) {
        $cond_where .= " AND cooperate_reward = 0 ";
      }

    }

    //悬赏方式
    if (isset($form_param['reward_type']) && !empty($form_param['reward_type']) && $form_param['reward_type'] > 0) {
      $reward_type = intval($form_param['reward_type']);
      if (1 == $reward_type || 2 == $reward_type) {
        $cond_where .= " AND reward_type = " . $reward_type . " ";
      } else if (0 == $reward_type) {
        $cond_where .= " AND reward_type = 0";
      }

    }

    //房源特色
    if (isset($form_param['house_degree'])) {
      $house_degree = intval($form_param['house_degree']);
      if (1 == $house_degree) {
        $cond_where .= " AND house_degree = 1";
      } else if (2 == $house_degree) {
        $cond_where .= " AND house_degree = 2";
      } else if (3 == $house_degree) {
        $cond_where .= " AND house_degree = 3";
      }

    }

    //房源创建时间范围
    if (!empty($form_param['create_time_range'])) {
      $searchtime = intval($form_param['create_time_range']);
      $now_time = time();
      switch ($searchtime) {
        case '1':
          $creattime = $now_time - 86400 * 1;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '2':
          $creattime = $now_time - 86400 * 7;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '3':
          $creattime = $now_time - 86400 * 30;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '4':
          $creattime = $now_time - 86400 * 90;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '5':
          $creattime = $now_time - 86400 * 180;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;
        default:
      }
    }

    //设置合作时间
    if (!empty($form_param['set_share_time'])) {
      $searchtime = intval($form_param['set_share_time']);
      $now_time = time();
      switch ($searchtime) {
        case '1':
          $creattime = $now_time - 86400 * 30;
          $cond_where .= " AND set_share_time >=  '" . $creattime . "' ";
          break;

        case '2':
          $creattime = $now_time - 86400 * 90;
          $cond_where .= " AND set_share_time >=  '" . $creattime . "' ";
          break;

        case '3':
          $creattime = $now_time - 86400 * 180;
          $cond_where .= " AND set_share_time >=  '" . $creattime . "' ";
          break;

        case '4':
          $creattime = $now_time - 86400 * 360;
          $cond_where .= " AND set_share_time >=  '" . $creattime . "' ";
          break;

        case '5':
          $creattime = $now_time - 86400 * 360;
          $cond_where .= " AND set_share_time <  '" . $creattime . "' ";
          break;

        default :
          $creattime = $now_time - 86400 * 180;
          $cond_where .= " AND set_share_time >=  '" . $creattime . "' ";
      }
    }
      //设置发到区域公盘时间
      if (!empty($form_param['set_district_share_time'])) {
          $searchtime = intval($form_param['set_district_share_time']);
          $now_time = time();
          switch ($searchtime) {
              case '1':
                  $creattime = $now_time - 86400 * 30;
                  $cond_where .= " AND set_district_share_time >=  '" . $creattime . "' ";
                  break;

              case '2':
                  $creattime = $now_time - 86400 * 90;
                  $cond_where .= " AND set_district_share_time >=  '" . $creattime . "' ";
                  break;

              case '3':
                  $creattime = $now_time - 86400 * 180;
                  $cond_where .= " AND set_district_share_time >=  '" . $creattime . "' ";
                  break;

              case '4':
                  $creattime = $now_time - 86400 * 360;
                  $cond_where .= " AND set_district_share_time >=  '" . $creattime . "' ";
                  break;

              case '5':
                  $creattime = $now_time - 86400 * 360;
                  $cond_where .= " AND set_district_share_time <  '" . $creattime . "' ";
                  break;

              default :
                  $creattime = $now_time - 86400 * 180;
                  $cond_where .= " AND set_district_share_time >=  '" . $creattime . "' ";
          }
      }
    //楼层-单层和跃层
    if (isset($form_param['story_type']) && !empty($form_param['story_type']) && $form_param['story_type'] > 0) {
      $story_type = intval($form_param['story_type']);
      if ($story_type == 1) //单层
      {
        $cond_where .= " AND subfloor = 0";
      } else {
        $cond_where .= " AND subfloor > 0";
      }
    }

    //钥匙
    if (isset($form_param['keys']) && !empty($form_param['keys']) && $form_param['keys'] > 0) {
      $keys = intval($form_param['keys']);
      $cond_where .= " AND `keys` = '" . ($keys - 1) . "'";

    }

    //委托类型
    if (isset($form_param['entrust']) && !empty($form_param['entrust']) && $form_param['entrust'] > 0) {
      $entrust = intval($form_param['entrust']);
      $cond_where .= " AND entrust = '" . $entrust . "'";
    }

    //经纪人
    if (!empty($form_param['post_broker_id']) && $form_param['post_broker_id'] != '') {
      $broker_id = intval($form_param['post_broker_id']);
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    if (!empty($form_param['post_agency_id']) && $form_param['post_agency_id'] != '') {
      $agency_id = intval($form_param['post_agency_id']);
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    if (!empty($form_param['post_company_id']) && $form_param['post_company_id'] != '') {
      $company_id = intval($form_param['post_company_id']);
      $cond_where .= " AND company_id = '" . $company_id . "'";
    }
    return $cond_where;
  }

  /**
   * 获取排序参数
   *
   * @access private
   * @param  int $order_val
   * @return void
   */
  private function _get_orderby_arr($order_val)
  {
    $arr_order = array();

    switch ($order_val) {
      case 1:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 2:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'ASC';
        break;
      case 3:
        $arr_order['order_key'] = 'buildyear';
        $arr_order['order_by'] = 'DESC';
        break;
      case 4:
        $arr_order['order_key'] = 'buildyear';
        $arr_order['order_by'] = 'ASC';
        break;
      case 5:
        $arr_order['order_key'] = 'buildarea';
        $arr_order['order_by'] = 'ASC';
        break;
      case 6:
        $arr_order['order_key'] = 'buildarea';
        $arr_order['order_by'] = 'DESC';
        break;
      case 7:
        $arr_order['order_key'] = 'price';
        $arr_order['order_by'] = 'ASC';
        break;
      case 8:
        $arr_order['order_key'] = 'price';
        $arr_order['order_by'] = 'DESC';
        break;
      case 9:
        $arr_order['order_key'] = 'avgprice';
        $arr_order['order_by'] = 'ASC';
        break;
      case 10:
        $arr_order['order_key'] = 'avgprice';
        $arr_order['order_by'] = 'DESC';
        break;
      case 11:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'ASC';
        break;
      case 12:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 13:
        $arr_order['order_key'] = 'createtime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 14:
        $arr_order['order_key'] = 'cooperate_reward';
        $arr_order['order_by'] = 'DESC';
        break;
      case 15:
        $arr_order['order_key'] = 'set_share_time';
        $arr_order['order_by'] = 'ASC';
        break;
      case 16:
        $arr_order['order_key'] = 'set_share_time';
        $arr_order['order_by'] = 'DESC';
        break;
      case 17:
        $arr_order['order_key'] = 'house_degree';
        $arr_order['order_by'] = 'DESC';
        break;
      case 18:
        $arr_order['order_key'] = 'dong';
        $arr_order['order_by'] = 'ASC';
        break;
      case 19:
        $arr_order['order_key'] = 'dong';
        $arr_order['order_by'] = 'DESC';
        break;
      case 20:
        $arr_order['order_key'] = 'unit';
        $arr_order['order_by'] = 'ASC';
        break;
      case 21:
        $arr_order['order_key'] = 'unit';
        $arr_order['order_by'] = 'DESC';
        break;
      case 22:
        $arr_order['order_key'] = 'door';
        $arr_order['order_by'] = 'ASC';
        break;
      case 23:
        $arr_order['order_key'] = 'door';
        $arr_order['order_by'] = 'DESC';
        break;
      case 24:
        $arr_order['order_key'] = 'floor';
        $arr_order['order_by'] = 'ASC';
        break;
      case 25:
        $arr_order['order_key'] = 'floor';
        $arr_order['order_by'] = 'DESC';
        break;
      case 26:
        $arr_order['order_key'] = 'avgprice';
        $arr_order['order_by'] = 'ASC';
        break;
      case 27:
        $arr_order['order_key'] = 'avgprice';
        $arr_order['order_by'] = 'DESC';
        break;
      case 28:
        $arr_order['order_key'] = 'createtime';
        $arr_order['order_by'] = 'ASC';
        break;
      case 29:
        $arr_order['order_key'] = 'createtime';
        $arr_order['order_by'] = 'DESC';
        break;
      default:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
    }

    return $arr_order;
  }

  //取消合作
  public function cancel_share()
  {
    $str = $this->input->get('str', TRUE);
    //新权限
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->sell_house_model->set_id($str);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    $cooperate_per = $this->broker_permission_model->check('2', $owner_arr);
    if (!$cooperate_per['auth']) {
      $this->redirect_permission_none_iframe();
      exit();
    }
    $flag = $this->input->get('flag', TRUE);
    $friend_log = $this->input->get('friend', TRUE);
      if ($friend_log === 'friend' || $friend_log === 'district') {
      $this->change_share($str, $flag, 0, $friend_log);
    } else {
      $this->change_share($str, $flag);
    }

  }

  //设置合作
  public function set_share()
  {
      $str = $this->input->get('str', TRUE); //房源的id字符串
    //新权限
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->sell_house_model->set_id($str);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    $cooperate_per = $this->broker_permission_model->check('2', $owner_arr);
    if (!$cooperate_per['auth']) {
      $this->redirect_permission_none_iframe();
      exit();
    }
    $flag = $this->input->get('flag', TRUE);
    $friend = $this->input->get('friend', TRUE);
    $commission_ratio = $this->input->get('commission_ratio', TRUE);
    $this->change_share($str, $flag, $friend, 0, $commission_ratio);
  }


  //合作房源
  public function change_share($str, $flag, $friend = 0, $friend_log = 0, $commission_ratio = 0)
  {
    $flag = intval($flag);
    $str = trim($str);
    $str = trim($str, ',');

    if ($str && $flag <= 1 && $flag >= 0) {
        $this->sell_house_model->set_search_fields(array("id", "district_id", "street_id", "buildarea", "block_name", "price", 'pic_tbl', 'pic_ids', 'broker_id', 'broker_name'));
        $this->sell_house_model->set_id(intval($str));
        $house_detail = $this->sell_house_model->get_info_by_id();


        if ($friend != 2 && $friend_log !== 'district') {
            $arr = array('isshare' => $flag);
        }
        if ($friend == 1) {
            $arr['isshare_friend'] = 1;
        } elseif ($friend == 2) {
            $arr['isshare_district'] = 1;
            $arr['set_district_share_time'] = time();
            $arr['district_broker_id'] = $house_detail['broker_id'];
            $arr['district_broker_name'] = $house_detail['broker_name'];
        }
        if ($friend != 2 && $flag === 1) {
            $arr['reward_type '] = 1;
            $arr['set_share_time'] = time();
            $arr['commission_ratio'] = $commission_ratio;
        }
        if ($friend_log === 'district' && $flag === 0) {
            $arr['isshare_district '] = 0;
            $arr['set_district_share_time'] = "";
            $arr['district_broker_id'] = "";
            $arr['district_broker_name'] = "";
        }
        if ($friend_log !== 'district' && $flag === 0) {
        $arr['isshare_friend'] = 0;
        $arr['cooperate_reward'] = 0;
        $arr['set_reward_broker_id'] = 0;
        $arr['commission_ratio'] = 0;
        //取消合作的时候删除合作资料图片
        $arr['reward_type'] = 1;
        $arr['cooperate_check'] = 1;
        $arr['house_degree'] = 0;

        if ($house_detail['pic_ids'] && $house_detail['pic_tbl']) {
          $this->load->model('pic_model');
          $house_detail['picinfo'] = $this->pic_model->find_house_pic_by_ids($house_detail['pic_tbl'], $house_detail['pic_ids']);
          $pic_str_del = '';
          $pic_str_liu = '';
          if (is_full_array($house_detail['picinfo'])) {
            foreach ($house_detail['picinfo'] as $k => $v) {
                if (3 == $v['type'] || 4 == $v['type'] || 5 == $v['type']) {
                $pic_str_del .= $v['id'] . ',';
              }
                if (1 == $v['type'] || 2 == $v['type']) {
                $pic_str_liu .= $v['id'] . ',';
              }
            }
          }

          if (!empty($pic_str_liu)) {
            $arr['pic_ids'] = $pic_str_liu;
          }
          $this->pic_model->del_pic_by_ids($pic_str_del, $house_detail['pic_tbl']);
        }

        }

        $cond_where = "id IN (0," . $str . ") AND isshare <> {$flag}";
        if ($friend_log === 'district' || $friend == 2) {
            $cond_where = "id IN (0," . $str . ")";
        }
      //跟进
      $this->load->model('follow_model');
      $ids_arr = array();
      $this->sell_house_model->set_search_fields(array("id", "isshare"));
      $list = $this->sell_house_model->get_list_by_cond($cond_where);

      $text = $flag ? "是否合作:否>>是" : "是否合作:是>>否";
        if ($friend_log === 'district' || $friend == 2) {
            $text = $flag ? "是否发到区域公盘:否>>是" : "是否发到区域公盘:是>>否";
        }

      foreach ($list as $key => $val) {
          if ($friend_log !== 'district' && $friend != 2) {
              if ('2' == $val['isshare']) {
                  $text = "是否合作:审核中>>否";
              }
          }

        $needarr = array();
        $needarr['broker_id'] = $this->user_arr['broker_id'];
        $needarr['house_id'] = $val['id'];
        $needarr['agency_id'] = $this->user_arr['agency_id'];//门店ID
        $needarr['company_id'] = $this->user_arr['company_id'];//总公司id
        $needarr['type'] = 1;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_save($needarr);
        $ids_arr[] = $val['id'];
        $follow_date_info = array();
        $follow_date_info['updatetime'] = time();
        $this->sell_house_model->set_id($val['id']);
        $result = $this->sell_house_model->update_info_by_id($follow_date_info);
      }
      $up_num = $this->sell_house_model->update_info_by_cond($arr, $cond_where);
    }
    if ($up_num > 0) {
      if ($flag == 1) {
          if ($friend == 2) {

              $reslult = array('result' => 'ok', 'arr' => $ids_arr, 'msg' => '房源发到区域公盘成功！');
          } else {
              $reslult = array('result' => 'ok', 'arr' => $ids_arr, 'msg' => '房源设置合作成功！');
          }

        //增加积分
//        $this->load->model('api_broker_credit_model');
//        $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
//        $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $str), 1);
        //判断积分是否增加成功
//        if (is_full_array($credit_result) && $credit_result['status'] == 1) {
//          $reslult['msg'] .= '+' . $credit_result['score'] . '积分';
//        }
        //增加等级分值
//        $this->load->model('api_broker_level_model');
//        $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
//        $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $str), 1);
        //判断成长值是否增加成功
//        if (is_full_array($level_result) && $level_result['status'] == 1) {
//          $reslult['msg'] .= '+' . $level_result['score'] . '成长值';
//        }
      } else {
        //取消合作后，终止与房源有关系的合作
          if (!($friend_log == 'district' || $friend == '2')) {
              $arr_houseid = explode(',', $str);
              $house_num = count($arr_houseid);
              $stop_reason = 'cencel_house';
              $this->load->model('cooperate_model');
              for ($i = 0; $i < $house_num; $i++) {
                  $this->cooperate_model->stop_cooperate($arr_houseid[$i], 'sell', $stop_reason);
              }
          }
        $reslult = array('result' => 'ok', 'arr' => $ids_arr);
          if ($friend_log == 'friend' || $friend_log == 'district') {
          //朋友圈下架增加操作日志
          $broker_info = $this->user_arr;
          $add_log_param = array();
          $add_log_param['company_id'] = $broker_info['company_id'];
          $add_log_param['agency_id'] = $broker_info['agency_id'];
          $add_log_param['broker_id'] = $broker_info['broker_id'];
          $add_log_param['broker_name'] = $broker_info['truename'];
              $add_log_param['type'] = $friend_log == 'district' ? 49 : 34;
          $district_name = $this->district_model->get_distname_by_id($house_detail['district_id']);
          $street_name = $this->district_model->get_streetname_by_id($house_detail['street_id']);

              if ($friend_log == 'district') {
                  $add_log_param['text'] = "从区域公盘下架 " . $district_name . " " . $street_name . " " . $house_detail['block_name'] . " " . $house_detail['buildarea'] . "平" . " " . $house_detail['price'] . "万的出售房源CS" . $house_detail['id'];

              } else {
                  $add_log_param['text'] = "从朋友圈下架 " . $district_name . " " . $street_name . " " . $house_detail['block_name'] . " " . $house_detail['buildarea'] . "平" . " " . $house_detail['price'] . "万的出售房源CS" . $house_detail['id'];

              }

          $add_log_param['from_system'] = 1;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();
          $this->operate_log_model->add_operate_log($add_log_param);
        }
      }
    } else {
      $reslult = array('lt' => 'no', "msg" => "设置失败,该房源已经是非合作房源");
    }

    echo json_encode($reslult);
  }

  //设置合作审核
  public function set_is_share_2()
  {
    $result = array();
    $result['msg'] = 'failed';
    $house_id = intval($this->input->get('str', TRUE));
    $flag = intval($this->input->get('flag', TRUE));
    $friend = intval($this->input->get('friend', TRUE));
    $commission_ratio = intval($this->input->get('commission_ratio', TRUE));
    if (!empty($house_id) && !empty($flag)) {
      $cond_where = array('id' => $house_id);

        if ($friend == 2) {
            $update_arr = array(
                'isshare' => $flag,
                'set_share_time' => time(),
                'isshare_district' => 1,
                'commission_ratio' => $commission_ratio
            );
        } else {
            $update_arr = array(
                'isshare' => $flag,
                'set_share_time' => time(),
                'isshare_friend' => $friend,
                'commission_ratio' => $commission_ratio
            );
        }
      $update_result = $this->sell_house_model->update_info_by_cond($update_arr, $cond_where);
      if (is_int($update_result) && $update_result > 0) {
        //添加跟进信息
        $this->load->model('follow_model');
        $needarr = array();
        $needarr['broker_id'] = $this->user_arr['broker_id'];
        $needarr['house_id'] = $house_id;
        $needarr['agency_id'] = $this->user_arr['agency_id'];//门店ID
        $needarr['company_id'] = $this->user_arr['company_id'];//总公司id
        $needarr['type'] = 1;
        $needarr['text'] = "是否合作:否>>审核中";
        $bool = $this->follow_model->house_save($needarr);
        $result['msg'] = 'success';
      }
    }
    echo json_encode($result);
    exit;
  }

  /**
   * 设为私盘
   * @access private
   * @return void
   */
  public function set_private()
  {
    $str = $this->input->get('str', TRUE);
    $new_str = $str;
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_nature($new_str, $flag);
  }


  /**
   * 设为公盘
   * @access private
   * @return void
   */
  public function set_public()
  {
    $str = $this->input->get('str', TRUE);
    $new_str = $str;
    //过滤没有权限的房源id
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_nature($new_str, $flag);
  }

  /**
   * 设为公盘、私盘
   * @access private
   * @return void
   */
  public function _change_nature($str, $flag)
  {
    $up_num = 0;
    if ($str && $flag <= 2 && $flag >= 1) {
      $arr = array('nature' => $flag);
      $cond_where = "id IN (0," . $str . ") AND nature <> {$flag}";

      //跟进
      $this->load->model('follow_model');
      $ids_arr = array();

      $this->sell_house_model->set_search_fields(array("id"));
      $list = $this->sell_house_model->get_list_by_cond($cond_where);
      $text = $flag > 1 ? "设置公私盘:私盘>>公盘" : "设置公私盘:公盘>>私盘";

      foreach ($list as $key => $val) {
        $needarr = array();
        $needarr['broker_id'] = $this->user_arr['broker_id'];
        $needarr['house_id'] = $val['id'];
        $needarr['agency_id'] = $this->user_arr['agency_id'];//门店ID
        $needarr['company_id'] = $this->user_arr['company_id'];//总公司id
        $needarr['type'] = 1;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_nature($needarr);
        $ids_arr[] = $val['id'];
        $follow_date_info = array();
        $follow_date_info['updatetime'] = time();
        $this->sell_house_model->set_id($val['id']);
        $result = $this->sell_house_model->update_info_by_id($follow_date_info);
      }

      $up_num = $this->sell_house_model->update_info_by_cond($arr, $cond_where);
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    if ($up_num > 0) {
      $reslult = array('result' => 'ok', "arr" => $ids_arr, "msg" => "设置成功，共设置{$up_num}条数据");
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    echo json_encode($reslult);
  }


  /**
   * 设为锁定
   * @access private
   * @return void
   */
  public function set_lock()
  {
    $str = $this->input->get('str', TRUE);
    $new_str = $str;
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_lock($new_str, $flag);
  }

  /**
   * 设为解锁
   * @access private
   * @return void
   */
  public function set_unlock()
  {
    $str = $this->input->get('str', TRUE);
    $new_str = $str;
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_lock($new_str, $flag);
  }


  /**
   * 锁定、解锁
   * @access private
   * @return void
   */
  public function _change_lock($str, $flag)
  {
    $up_num = 0;
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $str = trim($str);
    $str = trim($str, ',');
    if ($str && $flag <= 1 && $flag >= 0) {
      $broker_id = $this->user_arr['broker_id'];
      if ($flag == 0) {
        //解锁
        $arr = array('lock' => $flag);
        $cond_where = "id in (0," . $str . ") and `lock` = {$broker_id}";
      } else if ($flag == 1) {
        //锁定
        $arr = array('lock' => $broker_id);
        $cond_where = "id in (0," . $str . ") and `lock` = 0";
      }
      //跟进
      $this->load->model('follow_model');
      $ids_arr = array();

      $this->sell_house_model->set_search_fields(array("id"));
      $list = $this->sell_house_model->get_list_by_cond($cond_where);
      $text = $flag ? "是否锁定:否>>是" : "是否锁定:是>>否";
      foreach ($list as $key => $val) {
        $needarr = array();
        $needarr['broker_id'] = $broker_id;
        $needarr['house_id'] = $val['id'];
        $needarr['agency_id'] = $this->user_arr['agency_id'];//门店ID
        $needarr['company_id'] = $this->user_arr['company_id'];//总公司id
        $needarr['type'] = 1;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_lock($needarr);
        $ids_arr[] = $val['id'];
      }
      $up_num = $this->sell_house_model->update_info_by_cond($arr, $cond_where);
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    if ($up_num > 0) {
      $reslult = array('result' => 'ok', "arr" => $ids_arr, "msg" => "设置成功，共设置{$up_num}条数据");
    } else {
      $msg = ($flag == 1) ? "该房源已被锁定" : "该房源已被解锁";
      $reslult = array('result' => 'no', "msg" => $msg);
    }
    echo json_encode($reslult);
  }

  //合作朋友圈
  public function friend_lists_pub()
  {

    $this->lists_pub(1, 'friend');
  }

  //管理朋友圈
  public function friend_lists_pub_manage()
  {

    $this->lists_pub(1, 'manage');
  }

//区域公盘
    public function district_lists_pub()
    {

        $this->lists_pub(1, 'district');
    }

    //管理区域够昂盘
    public function district_lists_pub_manage()
    {

        $this->lists_pub(1, 'district_manage');
    }
  public function lists_pub($page = 1, $friend = '')
  {
    // 判断是否登录
//    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
//    $type = 'sell_house';

    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      //是否开启合作中心
      $open_cooperate = $company_basic_data['open_cooperate'];
    } else {
      $open_cooperate = '';
    }

    //模板使用数据
    $data = array();
    $data['open_cooperate'] = $open_cooperate;
    $data['friend'] = $friend;

    $data['broker_id'] = $broker_id;
    $data['truename'] = $broker_info['truename'];
    $data['agency_id'] = $broker_info['agency_id'];//经纪人门店编号
    $data['agency_name'] = $broker_info['agency_name'];//获取经纪人所对应门店的名称

    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号

    //获取全部分公司信息
    $data['agency_list'] = $this->api_broker_model->get_agencys_by_company_id($company_id);

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //是否提交了表单数据
    $is_submit_form = false;
    if (is_full_array($post_param)) {
      $is_submit_form = true;
    }
//    $blockname = $this->input->post('blockname', true);
      if ($friend !== 'district' && $friend !== 'manage' && empty($post_param['set_share_time'])) {
      $post_param['set_share_time'] = $this->user_arr['city_spell'] == "sz" ? '1' : '3';
    }
    //查询房源条件
    if ($friend == 'friend') {
      $time = time();
      $cond_where_friend = "broker_info.expiretime >= {$time} and broker_info.status = 1 and cooperate_friends.status = 1 and cooperate_friends.broker_id =" . $broker_id;
      $friends_arr = $this->cooperate_friends_base_model->get_friends_list_by_cond($cond_where_friend, 0, 0);
      foreach ($friends_arr as $key => $vo) {
        $broker_ids_arr[] = $vo['broker_id'];
      }
      $broker_ids_arr[] = $broker_id;
      $broker_ids = implode(',', $broker_ids_arr);
      $cond_where = "isshare = 1 AND status = 1 AND isshare_friend = 1 AND broker_id in (" . $broker_ids . ")";
    } elseif ($friend == 'manage') {
      $this->broker_info_model->set_select_fields(array('broker_id'));
      $time = time();
      $where = "company_id > 0 and expiretime >= {$time} and status = 1 and company_id =" . $broker_info['company_id'];
      $broker_id_arr = $this->broker_info_model->get_all_by($where, 0, 0);
      $broker_ids = '';
      foreach ($broker_id_arr as $key => $vo) {
        $broker_ids_arr[] = $vo['broker_id'];
      }
      $broker_ids = implode(',', $broker_ids_arr);
      $cond_where = "isshare = 1 AND status = 1 AND isshare_friend = 1 AND broker_id in (" . $broker_ids . ")";
    } elseif ($friend == 'district') {
        //获取门店说在区域
//        $this->agency_model->set_select_fields(array('dist_id'));
//        $agency = $this->agency_model->get_by_id($this->user_arr['agency_id']);
//        $district_id = $agency['dist_id'];

        //获取门店参加的公盘
        $this->cooperate_district_model->set_tbl("cooperate_district_join");//选择表
        $district_public_where = "status = 1 and agency_id =" . $this->user_arr['agency_id'];
        $district_public_arr = $this->cooperate_district_model->get_all_by($district_public_where);
        $cooperate_district_ids = '';
        foreach ($district_public_arr as $key => $vo) {
            $district_public_id_arr[] = $vo['cooperate_district_id'];
        }
        $district_public_id_arr[] = 0;
        $cooperate_district_ids = implode(',', $district_public_id_arr);
        //获取公盘所有门店
        $district_agency_where = "status = 1 and cooperate_district_id  in (" . $cooperate_district_ids . ")";
        $district_agency_arr = $this->cooperate_district_model->get_all_by($district_agency_where);
//        $agency_ids = '';
        foreach ($district_agency_arr as $key => $vo) {
            $agency_id_arr[] = $vo['agency_id'];
        }
        $agency_id_arr[] = 0;
        $agency_ids = implode(',', $agency_id_arr);

        $cond_where = "status = 1 AND isshare_district = 1 AND agency_id in (" . $agency_ids . ") ";

    } elseif ($friend == 'district_manage') {
        $cond_where = "status = 1 AND isshare_district = 1 AND company_id = {$this->user_arr['company_id']}";
    } else {
        $cond_where = "isshare = 1 AND status = 1 AND isshare_friend = 0";
    }

    if ($is_submit_form) {
      $sell_lists_pub = array(
        'sell_type' => $post_param['sell_type'],
        'district' => $post_param['district'],
        'street' => $post_param['street'],
        'block_name' => $post_param['block_name'],
        'block_id' => $post_param['block_id'],
        'room' => $post_param['room'],
        'areamin' => $post_param['areamin'],
        'areamax' => $post_param['areamax'],
        'pricemin' => $post_param['pricemin'],
        'pricemax' => $post_param['pricemax'],
        'reward_type' => $post_param['reward_type'],
        'is_cooperate_true' => $post_param['is_cooperate_true'],
        'orderby_id' => $post_param['orderby_id'],
        'house_degree' => $post_param['house_degree'],
          'page' => $post_param['page'],
      );
        if ($friend !== 'district' && $friend !== 'manage') {
            $sell_lists_pub['set_share_time'] = $post_param['set_share_time'];
        } else {
            $sell_lists_pub['set_district_share_time'] = $post_param['set_district_share_time'];
        }
      setcookie('sell_lists_pub', serialize($sell_lists_pub), time() + 3600 * 24 * 7, '/');
    } else {
      $sell_lists_pub_search = unserialize($_COOKIE['sell_lists_pub']);
      if (is_full_array($sell_lists_pub_search)) {
        $post_param['sell_type'] = $sell_lists_pub_search['sell_type'];
        $post_param['district'] = $sell_lists_pub_search['district'];
        $post_param['street'] = $sell_lists_pub_search['street'];
        $post_param['block_name'] = $sell_lists_pub_search['block_name'];
        $post_param['block_id'] = $sell_lists_pub_search['block_id'];
        $post_param['room'] = $sell_lists_pub_search['room'];
        $post_param['areamin'] = $sell_lists_pub_search['areamin'];
        $post_param['areamax'] = $sell_lists_pub_search['areamax'];
        $post_param['pricemin'] = $sell_lists_pub_search['pricemin'];
        $post_param['pricemax'] = $sell_lists_pub_search['pricemax'];
        $post_param['reward_type'] = $sell_lists_pub_search['reward_type'];
        $post_param['is_cooperate_true'] = $sell_lists_pub_search['is_cooperate_true'];
        $post_param['orderby_id'] = $sell_lists_pub_search['orderby_id'];
        $post_param['house_degree'] = $sell_lists_pub_search['house_degree'];
        $post_param['page'] = $sell_lists_pub_search['page'];
          if ($friend !== 'district' && $friend !== 'manage') {
              $post_param['set_share_time'] = $sell_lists_pub_search['set_share_time'];
          } else {
              $post_param['set_district_share_time'] = $sell_lists_pub_search['set_district_share_time'];
          }
      }
    }

    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    //分页每页限制数(特定房源 合作 群发 采集 列表页需求)
    if ($post_param['limit_page']) {
      setcookie('limit_page', $post_param['limit_page'], time() + 3600 * 24 * 7, '/');
      $limit_page = $post_param['limit_page'];
    } elseif ($_COOKIE['limit_page']) {
      $limit_page = $_COOKIE['limit_page'];
    } else {
      $limit_page = $this->_limit;
    }
    $this->_init_pagination($page, $limit_page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);

    $broker_arr = array();
    if (isset($post_param['post_agency_id'])) {
      if ($post_param['post_agency_id'] > 0) {
        $broker_arr = $this->api_broker_model->get_brokers_agency_id($post_param['post_agency_id']);
      }
    } else {
      $broker_arr = $this->api_broker_model->get_brokers_agency_id($broker_info['agency_id']);
    }

    $data['broker_list'] = $broker_arr;

    $cond_where .= $cond_where_ext;

    $post_param['block_name'] = trim($post_param['block_name']);
    $cond_or_like = array();
    if (!empty($post_param['block_name'])) {
        if ($friend === 'district' || $friend === 'manage') {
            $cond_or_like['like_key'] = array('address', 'block_name', 'district_broker_name');
      } else {
        $cond_or_like['like_key'] = array('address', 'block_name', 'title');
      }
      $cond_or_like['like_value'] = $post_param['block_name'];
    }

    //排序字段
    $roomorder = intval($post_param['orderby_id']);
    if (empty($roomorder)) {
      $roomorder = 17;
    }
    $order_arr = $this->_get_orderby_arr($roomorder);
    //符合条件的总行数
    $this->_total_count =
      $this->sell_house_model->get_count_by_cond($cond_where, $cond_or_like);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    if ($post_param['page'] > $pages) {
      $this->_init_pagination($pages, $limit_page);
    }
      if ($friend !== 'district' && $friend !== 'manage') {
          $set_time = 'set_share_time';
      } else {
          $set_time = 'set_district_share_time';
      }


    //获取列表内容
      $list = $this->sell_house_model->get_list_by_cond_or_like($cond_where, $cond_or_like, $this->_offset, $this->_limit, $order_arr['order_key'], $order_arr['order_by'], $set_time, 'desc');

    $this->load->model('api_broker_model');
    $this->load->model('api_broker_sincere_model');
    //合作成功率MODEL
    $this->load->model('cooperate_suc_ratio_base_model');
    $brokeridstr = '';
    $rowid_arr = array();
    if ($list) {
      foreach ($list as $key => $val) {
        $brokeridstr .= $val['broker_id'] . ',';
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $list[$key]['telno'] = $brokerinfo['phone'];
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
        $list[$key]['cop_suc_ratio'] = $brokerinfo['cop_suc_ratio'];

        //遗留 最新跟进时间
        $list[$key]['genjintime'] = date('Y-m-d H:i', $val['updatetime']);

        //获取经纪人好评率
//        $appraise_count = array();
        $appraise_count = $this->api_broker_sincere_model->get_trust_appraise_count($val['broker_id']);
        $list[$key]['good_rate'] = !empty($appraise_count) ? $appraise_count['good_rate'] : 0;

        //经济人合作成功率
//        $cop_succ_ratio_info = array();
        $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($val['broker_id']);
        $list[$key]['cop_succ_ratio_info'] = !empty($cop_succ_ratio_info) ? $cop_succ_ratio_info : array();

        //悬赏金额处理
        if (!empty($val['cooperate_reward'])) {
          $cooperate_reward = intval($val['cooperate_reward']);
          if ($cooperate_reward > 10000) {
            $reuslt_reward = strip_end_0($cooperate_reward / 10000, 1);
            $list[$key]['cooperate_reward'] = $reuslt_reward . '万';
          }
        }
        //判断和经纪人之间的朋友关系
        //好友信息
        if ($broker_id == $val['broker_id']) {
          $list[$key]['status_friend'] = 0;
        } else {
          $friend_info = $this->cooperate_friends_base_model->get_friend_by_broker_id($broker_id, $val['broker_id']);
          if (is_full_array($friend_info)) {
            $list[$key]['status_friend'] = 1;
          } else {
            //申请信息
            $apply_info = $this->cooperate_friends_base_model->get_apply_by_broker_id($broker_id, $val['broker_id']);
            if (is_full_array($apply_info)) {
              $list[$key]['status_friend'] = 2;
            } else {
              $list[$key]['status_friend'] = 3;
            }
          }
        }

        if ($list[$key]['reward_type'] != 0) {
//          $xbxtest1 = $list[$key]['commission_ratio'];
          $list[$key]['commission_ratio'] = $this->sell_house_model->get_commission_ratio_id($list[$key]['commission_ratio']);
//          $xbxtest2 = $list[$key]['commission_ratio'];
        }
        $rowid_arr[] = $val['id'];
      }
    }
    $data['list'] = $list;
    //检测是否已经合作
    $this->load->model('cooperate_model');
    $data['check_coop_reulst'] = $this->cooperate_model->check_is_cooped_by_houseid($rowid_arr, 'sell', $broker_id);

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');

    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }

    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }

    //获取收藏过的房源信息
//    $status = '';
    $type = 'sell_house';
    $num = $this->house_collect_model->get_collect_ids_by_bid($broker_id, $type, 1);
    $arr = array();

    foreach ($num as $key => $val) {
      $arr[] = $val['rows_id'];
    }

    $data['num_id'] = $arr;

    //获取是否举报过
    $follow_house_id = array();
    $this->load->model('report_model');
    $follow_where = "type  in (1,3,4) ";
    $follow_where .= " AND broker_id = '$broker_id'";
    $follow_where .= " AND style = 1 ";
    $follow_house = $this->report_model->get_report_house_bid($follow_where);
    foreach ($follow_house as $key => $val) {
      $follow_house_id[] = $val['number'];
    }
    $follow_number = array_count_values($follow_house_id);
    $follow_house_num = array();
    foreach ($follow_number as $key => $val) {
      if ($val == 3) {
        $follow_house_num[] = $key;
      }
    }

    $data['follow_house_num'] = $follow_house_num;

    //页面菜单
    $data['user_menu'] = $this->user_menu;

    //三级功能菜单
    $data['user_func_menu'] = $this->user_func_menu;

    //底部最小化菜单
    $this->load->model('broker_info_min_log_model');
    $where_cond = array(
      'broker_id' => $broker_id
    );
    $query_result = $this->broker_info_min_log_model->get_log($where_cond);
    $sell_list_min_str = $query_result[0]['sell_house_list_pub'];
    $sell_list_min_arr = array();
    $sell_list_min_arr2 = array();
    if (!empty($sell_list_min_str)) {
      $sell_list_min_arr = explode(',', trim($sell_list_min_str, ','));
    }
    if (is_full_array($sell_list_min_arr)) {
      foreach ($sell_list_min_arr as $k => $v) {
        $this->sell_house_model->set_search_fields(array('block_name', 'price', 'buildarea'));
        $this->sell_house_model->set_id(intval($v));
        $info = $this->sell_house_model->get_info_by_id();
//        $name = '';
        $name = $info['block_name'] . '-' . intval($info['price']) . '万-' . intval($info['buildarea']) . '平米';
        $sell_list_min_arr2[] = array(
          'house_id' => $v,
          'name' => $name
        );
      }
    }
    //print_r($sell_list_min_arr2);
    $data['sell_list_min_arr'] = $sell_list_min_arr2;

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
    //页面标题
    $data['page_title'] = '公盘出售列表页';
    $data['page_list'] = $this->page_list->show('5');//(特定房源 合作 群发 采集 列表页需求)
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'mls/js/v1.0/broker_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js');

    //加载发布页面模板
    $this->view('house/sell_pub_lists', $data);
  }

  //房源跟进
    public function house_follow($house_id, $num = 1, $task = 0, $flag)
  {
    //新权限
    //获得当前数据所属的经纪人id和门店id
      $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'is_seal', 'keys', 'key_number', 'isshare_district'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
      if ($flag != "district") {//如果是区域公盘的跟进，暂不检查权限
          $house_follow_per = $this->broker_permission_model->check('10', $owner_arr);
          //出售房源跟进关联门店权限
          $agency_house_follow_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '5');
          if (!$house_follow_per['auth']) {
              $this->redirect_permission_none_iframe('js_genjin');
              exit();
          } else {
              if (!$agency_house_follow_per) {
                  $this->redirect_permission_none_iframe('js_genjin');
                  exit();
              }
          }
      }
    $data = array();

    //根据数据范围，获得门店数据
    $this->load->model('agency_permission_model');
    $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_key');
    $all_access_agency_ids = '';
    if (is_full_array($access_agency_ids_data)) {
      foreach ($access_agency_ids_data as $k => $v) {
        $all_access_agency_ids .= $v['sub_agency_id'] . ',';
      }
      $all_access_agency_ids .= $this->user_arr['agency_id'];
      $all_access_agency_ids = trim($all_access_agency_ids, ',');
    } else {
      $all_access_agency_ids = $this->user_arr['agency_id'];
    }
    $data['agencys'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

    //$num区分三个tab页面
    $num = intval($num);
    $data['num'] = $num;
    if (1 == $num) {
      //操作日志
      $this->sell_house_model->set_search_fields(array('block_name', 'address', 'dong', 'unit', 'door'));
      $this->sell_house_model->set_id(intval($house_id));
      $datainfo = $this->sell_house_model->get_info_by_id();

      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 5;
      $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id;
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);
    }

    $broker_info = $this->user_arr;
    $company_id = intval($broker_info['company_id']);
    $role_level = intval($broker_info['role_level']);
    $data['role_level'] = $role_level;

    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if ($company_basic_data['follow_text_num'] > 0) {
      $data['follow_text_num'] = $company_basic_data['follow_text_num'];
    } else {
      $data['follow_text_num'] = 10;
    }

    //房源id
    $house_id = intval($house_id);
    $data['house_id'] = $house_id;
    //是否封盘
    $data['is_seal'] = $owner_arr['is_seal'];
    //房源类型
    $data['house_type'] = 1;
    //经纪人姓名
    $data['broker_name'] = $broker_info['truename'];

    //提醒明细
    if ($num == 3) {
      $where_cond = array(
        'tbl' => 1,
        'row_id' => $house_id
      );
      $this->load->model('remind_model');
      $remind_list = $this->remind_model->get_remind_order($where_cond, 'create_time');
      $data['data_lists'] = $remind_list;
    } else {
      //获取跟进方式
      $this->load->model('follow_model');
      $type_tbl = 'follow_up';
      $this->follow_model->set_tbl($type_tbl);
      $follow_config = $this->follow_model->get_config();
      $data['follow_config'] = $follow_config['follow_way'];

      $follow_tbl = 'detailed_follow';
      $this->follow_model->set_tbl($follow_tbl);

      //跟进明细
      if ($num == 1) {
        $where_arr = "type = 1 AND house_id = '" . $house_id . "'";
        $where_arr .= " AND (follow_type = 1 OR follow_type = 3)";
      } else if ($num == 2) {
        $where_arr = "type = 1 AND house_id = '" . $house_id . "' AND follow_way = 5";
        $where_arr .= " AND (follow_type = 1 OR follow_type = 3)";
      }

      $follow_lists = $this->follow_model->get_lists($where_arr);
      //数据重构，获得跟进人和带看客户姓名
      $this->load->model('broker_info_model');
      $this->load->model('buy_customer_model');
      $follow_lists2 = array();
      foreach ($follow_lists as $k => $v) {
        $broker_data = $this->broker_info_model->get_one_by(array('broker_id' => $v['broker_id']));
        $v['broker_name'] = $broker_data['truename'];
        if (!empty($v['customer_id'])) {
          $customer_data = $this->buy_customer_model->get_all_customer_by_ids($v['customer_id']);
          $v['customer_name'] = $customer_data[0]['truename'];
        }
        //商谈经纪人
        if (isset($v['broker_id_f']) && !empty($v['broker_id_f'])) {
          $broker_f_info = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id_f']);
          if (is_full_array($broker_f_info)) {
            $v['broker_f_name'] = $broker_f_info['truename'];
          }
        }
        //商谈客户
        if (isset($v['customer_id_f']) && !empty($v['customer_id_f'])) {
          $this->load->model('buy_customer_model');
          $customer_f_info = $this->buy_customer_model->get_all_customer_by_ids($v['customer_id_f']);
          if (is_full_array($customer_f_info)) {
            $v['customer_f_name'] = $customer_f_info[0]['truename'];
          }
        }
        //封盘开始时间、结束时间
        if (isset($v['seal_start_time']) && !empty($v['seal_start_time'])) {
          $v['seal_start_date'] = date('Y.m.d', $v['seal_start_time']);
        }
        if (isset($v['seal_end_time']) && !empty($v['seal_end_time'])) {
          $v['seal_end_date'] = date('Y.m.d', $v['seal_end_time']);
        }
        $follow_lists2[] = $v;
      }

      $data['data_lists'] = $follow_lists2;
    }
    $data['task_id'] = $task;

    //钥匙模块
    $data['key_id'] = '';
    $data['key_number'] = '';
    $data['key_status'] = '';
    if ($owner_arr['keys'] && $owner_arr['key_number']) {
      $this->load->model('key_model');
      $key_info = $this->key_model->get_one_by(array('type' => 1, 'house_id' => $house_id, 'number' => $owner_arr['key_number']));
      if (is_full_array($key_info)) {
        $data['key_id'] = $key_info['id'];
        $data['key_status'] = $key_info['status'];
        $data['key_number'] = $owner_arr['key_number'];
      }
    }

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'css/v1.0/house_new.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/upload_wei.js,mls/js/v1.0/cooperate_common.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/backspace.js,'
      . 'mls/js/v1.0/house.js');
    $this->view('house/sell_house_follow', $data);
  }

  //添加跟进记录和提醒
  public function add_follow_remind()
  {
    $broker_info = $this->user_arr;
    $follow_arr = array();
    $follow_arr['house_id'] = $this->input->get('house_id', TRUE);//房源id
    $task_id = $this->input->get('task_id', TRUE);//任务id
    $status = $this->input->get('status', TRUE);//状态id
    $follow_arr['broker_id'] = $broker_info['broker_id'];//经纪人的ID
    $follow_arr['agency_id'] = $broker_info['agency_id'];//门店ID
    $follow_arr['company_id'] = $broker_info['company_id'];//总公司id
    $follow_arr['follow_way'] = $this->input->get('follow_type', TRUE);//跟进方式
    $follow_arr['customer_id'] = $this->input->get('customer_id', TRUE);//客户id
    $follow_arr['customer_id_f'] = $this->input->get('customer_id_f', TRUE);//商谈客户id
    $follow_arr['broker_id_f'] = $this->input->get('broker_id_f', TRUE);//商谈经纪人id
    $follow_arr['follow_type'] = $this->input->get('foll_type', TRUE);//跟进类型
    $follow_arr['text'] = $this->input->get('text', TRUE);//跟进内容
    $follow_arr['date'] = date('Y-m-d H:i:s');//跟进时间
    $follow_arr['type'] = 1;//类型
    //封盘开始时间，结束时间
    $follow_arr['seal_start_time'] = time();
    $seal_end_date = $this->input->get('seal_end_time', TRUE);
    $seal_end_time = strtotime($seal_end_date) + 24 * 3600 - 1;
    $follow_arr['seal_end_time'] = $seal_end_time;

    $follow_date_info = array();
    $follow_date_info['updatetime'] = time();
    $this->sell_house_model->set_id($follow_arr['house_id']);
    //1.房源数据更新update字段
    $result = $this->sell_house_model->update_info_by_id($follow_date_info);
    //2.添加房源跟进
    $this->load->model('follow_model');
    $tbl = 'detailed_follow';
    $this->follow_model->set_tbl($tbl);
    $follow_id = $this->follow_model->add_follow($follow_arr);
    if ($follow_id > 0) {
      //带看跟进，同时增加对应客源跟进
      if (5 == $follow_arr['follow_way']) {
        $customer_follow_arr = array();
        $customer_follow_arr['customer_id'] = $follow_arr['customer_id'];//客源id
        $customer_follow_arr['broker_id'] = $broker_info['broker_id'];//经纪人的ID
        $customer_follow_arr['agency_id'] = $broker_info['agency_id'];//门店ID
        $customer_follow_arr['company_id'] = $broker_info['company_id'];//总公司id
        $customer_follow_arr['follow_way'] = 5;//跟进方式
        $customer_follow_arr['house_id'] = $this->input->get('house_id', TRUE);//房源id
        $customer_follow_arr['follow_type'] = 2;//跟进类型
        $customer_follow_arr['text'] = $follow_arr['text'];//跟进内容
        $customer_follow_arr['date'] = $follow_arr['date'];//跟进时间
        $customer_follow_arr['type'] = 3;//类型

        //1.客源数据更新update字段
        $where_cond = array('id' => $customer_follow_arr['customer_id']);
        $this->load->model('buy_customer_model');
        $result = $this->buy_customer_model->update_customerinfo_by_cond($follow_date_info, $where_cond);
        //2.添加客源跟进
        $customer_follow_id = $this->follow_model->add_follow($customer_follow_arr);
        if ($customer_follow_id > 0) {
          //操作日志
          $add_log_param = array();
          $follow_way_str = '带看跟进';
          $add_log_param['company_id'] = $this->user_arr['company_id'];
          $add_log_param['agency_id'] = $this->user_arr['agency_id'];
          $add_log_param['broker_id'] = $this->user_arr['broker_id'];
          $add_log_param['broker_name'] = $this->user_arr['truename'];
          $add_log_param['type'] = 48;
          $add_log_param['text'] = '求购客源 ' . 'QG' . $customer_follow_arr['customer_id'] . ' ' . $follow_way_str . ' ' . $customer_follow_arr['text'];
          $add_log_param['from_system'] = 1;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();
          $this->operate_log_model->add_operate_log($add_log_param);

            //判断该客源是否是公共数据，如果是，变成非公共，重新归属经纪人
          $this->buy_customer_model->set_search_fields(array('id', 'is_public'));
          $this->buy_customer_model->set_id(intval($customer_follow_arr['customer_id']));
          $datainfo = $this->buy_customer_model->get_info_by_id();
          if ('1' == $datainfo['is_public']) {
            $update_arr = array();
            $update_arr['is_public'] = 0;
            $update_arr['broker_id'] = $this->user_arr['broker_id'];
            $update_arr['agency_id'] = $this->user_arr['agency_id'];
            $update_arr['company_id'] = $this->user_arr['company_id'];
            $update_arr['broker_name'] = $this->user_arr['truename'];
            $update_result = $this->buy_customer_model->update_info_by_id($customer_follow_arr['customer_id'], $update_arr);
            if ($update_result) {
              $customer_follow_arr['follow_type'] = 3;
              $customer_follow_arr['follow_way'] = 12;
              $customer_follow_arr['text'] = '委托人从 无 >> ' . $this->user_arr['truename'];
              $this->follow_model->set_tbl($tbl);
              $this->follow_model->add_follow($customer_follow_arr);
            }
          }

          //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
          //获得基本设置房源跟进的天数
          //获取当前经济人所在公司的基本设置信息
          $this->load->model('house_customer_sub_model');
          $company_basic_data = $this->company_basic_arr;
          $customer_follow_day = intval($company_basic_data['customer_follow_spacing_time']);

          $select_arr = array('id', 'house_id', 'date');
          $this->follow_model->set_select_fields($select_arr);
          $where_cond = 'customer_id = "' . $customer_follow_arr['customer_id'] . '" and follow_type != 1 and type = 3';
          $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
          if (count($last_follow_data) == 2) {
            $time1 = $last_follow_data[0]['date'];
            $time2 = $last_follow_data[1]['date'];
            $date1 = date('Y-m-d', strtotime($time1));
            $date2 = date('Y-m-d', strtotime($time2));
            $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
            if ($differ_day > $customer_follow_day) {
              $result = $this->house_customer_sub_model->add_buy_customer_sub($customer_follow_arr['customer_id'], 1);
            } else {
              $result = $this->house_customer_sub_model->add_buy_customer_sub($customer_follow_arr['customer_id'], 0);
            }
          } else {
            $result = $this->house_customer_sub_model->add_buy_customer_sub($customer_follow_arr['customer_id'], 0);
          }

        }
      }

      //封盘操作
      if (19 == $follow_arr['follow_way']) {
        $seal_end_date = $this->input->get('seal_end_time', TRUE);//封盘结束时间
        $seal_end_time = strtotime($seal_end_date) + 24 * 3600 - 1;
        $update_arr = array(
          'is_seal' => 1,
          'seal_broker_id' => $broker_info['broker_id'],
          'seal_start_time' => time(),
          'seal_end_time' => $seal_end_time
        );
        $this->sell_house_model->set_id($follow_arr['house_id']);
        $this->sell_house_model->update_info_by_id($update_arr);
      } else if (20 == $follow_arr['follow_way']) {
        $update_arr = array(
          'is_seal' => 2,
          'seal_broker_id' => 0,
          'seal_start_time' => 0,
          'seal_end_time' => 0
        );
        $this->sell_house_model->set_id($follow_arr['house_id']);
        $this->sell_house_model->update_info_by_id($update_arr);
      }

      //出售房源堪房-带看记录工作统计日志
      if ($follow_arr['follow_way'] == 1) {
        $this->info_count($follow_arr['house_id'], 4);
      } elseif ($follow_arr['follow_way'] == 5) {
        $this->info_count($follow_arr['house_id'], 5, $follow_arr['customer_id']);
      } else {
        $this->info_count($follow_arr['house_id'], 9);
      }
      //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
      //获得基本设置房源跟进的天数
      //获取当前经济人所在公司的基本设置信息
      $this->load->model('house_customer_sub_model');
      $company_basic_data = $this->company_basic_arr;
      $house_follow_day = intval($company_basic_data['house_follow_spacing_time']);

      $select_arr = array('id', 'house_id', 'date');
      $this->follow_model->set_select_fields($select_arr);
      $where_cond = 'house_id = "' . $follow_arr['house_id'] . '" and follow_type != 2 and type = 1';
      $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
      if (count($last_follow_data) == 2) {
        $time1 = $last_follow_data[0]['date'];
        $time2 = $last_follow_data[1]['date'];
        $date1 = date('Y-m-d', strtotime($time1));
        $date2 = date('Y-m-d', strtotime($time2));
        $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
        if ($differ_day > $house_follow_day) {
          $result = $this->house_customer_sub_model->add_sell_house_sub($follow_arr['house_id'], 1);
        } else {
          $result = $this->house_customer_sub_model->add_sell_house_sub($follow_arr['house_id'], 0);
        }
      }

      //操作日志
      $add_log_param = array();
      $follow_way_str = '';
      if ('1' == $follow_arr['follow_way']) {
        $follow_way_str = '堪房跟进';
      } else if ('3' == $follow_arr['follow_way']) {
        $follow_way_str = '电话跟进';
      } else if ('4' == $follow_arr['follow_way']) {
        $follow_way_str = '磋商跟进';
      } else if ('5' == $follow_arr['follow_way']) {
        $follow_way_str = '带看跟进';
      } else {
        $follow_way_str = '其它跟进';
      }
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 46;
      $add_log_param['text'] = '出售房源 ' . 'CS' . $follow_arr['house_id'] . ' ' . $follow_way_str . ' ' . $follow_arr['text'];
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);

      //判断该房源是否是公共数据，如果是，变成非公共，重新归属经纪人
        $this->sell_house_model->set_search_fields(array('id', 'is_public', 'is_district_public', 'isshare_district'));
      $this->sell_house_model->set_id(intval($follow_arr['house_id']));
      $datainfo = $this->sell_house_model->get_info_by_id();
      if ('1' == $datainfo['is_public']) {
        $update_arr = array();
        $update_arr['is_public'] = 0;
        $update_arr['broker_id'] = $this->user_arr['broker_id'];
        $update_arr['agency_id'] = $this->user_arr['agency_id'];
        $update_arr['company_id'] = $this->user_arr['company_id'];
        $update_arr['broker_name'] = $this->user_arr['truename'];
        $this->sell_house_model->set_id($follow_arr['house_id']);
        $update_result = $this->sell_house_model->update_info_by_id($update_arr);
        if ($update_result) {
          $follow_arr['follow_type'] = 3;
          $follow_arr['follow_way'] = 8;
          $follow_arr['text'] = '委托人从 无 >> ' . $this->user_arr['truename'];
          $this->follow_model->set_tbl($tbl);
          $this->follow_model->add_follow($follow_arr);
        }
      }
        if ('1' == $datainfo['is_district_public'] && '1' == $datainfo['is_district_public']) {
            $update_arr = array();
            $update_arr['is_district_public'] = 0;
            $update_arr['district_broker_id'] = $this->user_arr['broker_id'];
            $update_arr['district_broker_name'] = $this->user_arr['truename'];
            $this->sell_house_model->set_id($follow_arr['house_id']);
            $update_result = $this->sell_house_model->update_info_by_id($update_arr);
            if ($update_result) {
                $follow_arr['follow_type'] = 3;
                $follow_arr['follow_way'] = 8;
                $follow_arr['text'] = '区域公盘委托人从 无 >> ' . $this->user_arr['truename'];
                $this->follow_model->set_tbl($tbl);
                $this->follow_model->add_follow($follow_arr);
            }
        }
        echo json_encode(array('result'=>'success'));
    } else {
        echo json_encode(array('result'=>'failed'));
    }

    //2.添加事件提醒
    $ti_arr = array();
    $ti_arr['title'] = '房源跟进';
    $ti_arr['contents'] = $this->input->get('ti_text', TRUE);
    $ti_arr['agency_id'] = $broker_info['agency_id'];
    $ti_arr['broker_id'] = $broker_info['broker_id'];
    $ti_arr['broker_name'] = $broker_info['truename'];
    $ti_arr['create_time'] = strtotime(date('Y-m-d H:i:s'));
    $ti_arr['notice_time'] = strtotime($this->input->get('ti_time', TRUE));
    $ti_arr['tbl'] = 1;
    $ti_arr['row_id'] = $this->input->get('house_id', TRUE);
    if (!empty($follow_arr['text']) && !empty($ti_arr['contents'])) {
      $ti_arr['detail_id'] = $follow_id;
    }
    if (!empty($ti_arr['notice_time']) && !empty($ti_arr['contents'])) {
      $this->load->model('remind_model');
      //事件提醒表
      $add_result = $this->remind_model->add_remind($ti_arr);
      //事情接受者表
      $receiver_data = array();
      $receiver_data['receiver_id'] = $broker_info['broker_id'];
      $receiver_data['event_id'] = $add_result;
      $this->load->model('event_receiver_model');
      $add_result2 = $this->event_receiver_model->add_receiver($receiver_data);
    }
    if ($task_id) {
      $this->load->model('task_model');
      $this->task_model->update_by_id(array('start_date' => time(), 'status' => $status), $task_id);
    }

    //保密信息与跟进进程
    $this->load->model('secret_follow_process_model');
    $where_cond = array(
      'broker_id' => $broker_info['broker_id'],
      'row_id' => $this->input->get('house_id', TRUE),
      'type' => 1
    );
    $query_result = $this->secret_follow_process_model->get($where_cond);
    if (is_full_array($query_result)) {
      foreach ($query_result as $key => $value) {
        $id = intval($value['id']);
        if (is_int($id) && $id > 0) {
          $update_arr = array(
            'status' => 2
          );
          $this->secret_follow_process_model->update($id, $update_arr);
        }
      }
    }

  }

  //添加事件提醒
  function add_remind()
  {
    $broker_info = $this->user_arr;
    $ti_arr = array();
    $ti_arr['title'] = '房源跟进';
    $ti_arr['contents'] = $this->input->get('ti_text', TRUE);
    $ti_arr['agency_id'] = $broker_info['agency_id'];
    $ti_arr['broker_id'] = $broker_info['broker_id'];
    $ti_arr['broker_name'] = $broker_info['truename'];
    $ti_arr['create_time'] = strtotime(date('Y-m-d H:i:s'));
    $ti_arr['notice_time'] = strtotime($this->input->get('ti_time', TRUE));
    $ti_arr['tbl'] = 1;
    $ti_arr['row_id'] = $this->input->get('house_id', TRUE);
    if (!empty($ti_arr['notice_time']) && !empty($ti_arr['contents'])) {
      $this->load->model('remind_model');
      //事件提醒表
      $add_result = $this->remind_model->add_remind($ti_arr);
      if (!empty($add_result) && is_int($add_result)) {
        //事情接受者表
        $receiver_data = array();
        $receiver_data['receiver_id'] = $broker_info['broker_id'];
        $receiver_data['event_id'] = $add_result;
        $this->load->model('event_receiver_model');
        $add_result2 = $this->event_receiver_model->add_receiver($receiver_data);
      }
    }
    if (isset($add_result2) && !empty($add_result2)) {
      echo 'success';
    } else {
      echo 'failed';
    }
  }


  //根据经纪人id查询所有的客源
  public function source($type = 1)
  {
    //模板使用数据
    $data = array();
    $data['type'] = $type;
    $post_param = $this->input->post(NULL, TRUE);
    $broker_info = $this->user_arr;

    //经纪人的ID
    $broker_id = $broker_info['broker_id'];
    //加载客源MODEL
    $this->load->model('buy_customer_model');
    // 分页参数
    $pagee = $this->input->get('page', TRUE);
    $page = isset($pagee) ? intval($pagee) : intval(1);
    $this->_init_pagination($page, 5);
    //求购信息表名
    $tbl = 'buy_customer';
    //获取当前经纪人所在门店的数据范围
    //查询条件
    //$cond_where = "status = 1 and broker_id = '".$broker_id."'";
    $cond_where = "status = 1 ";
    $view_other_per_data = $this->broker_permission_model->check('33');
    if ($view_other_per_data) {
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_customer_secret');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $broker_info['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $broker_info['agency_id'];
      }
      if (!empty($all_access_agency_ids)) {
        //查询房源条件
        $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";
      }
    } else {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //表单提交参数组成的查询条件
    if (isset($post_param['cname']) && !empty($post_param['cname'])) {
      $cond_where_ext = $post_param['cname'];
      if ($cond_where_ext == '%') {
        $cond_where_ext = '\%';
      }
      $cond_where_extt = "AND truename LIKE '%" . $cond_where_ext . "%' OR telno1 LIKE '%" . $cond_where_ext . "%' OR telno2 LIKE '%" . $cond_where_ext . "%' OR telno3 LIKE '%" . $cond_where_ext . "%' ";
      $cond_where .= $cond_where_extt;
    }
    $data['cname'] = $post_param['cname'];

    $this->buy_customer_model->set_tbl($tbl);

    //符合条件的总行数
    $this->_total_count = $this->buy_customer_model->get_buynum_by_cond($cond_where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $list = $this->buy_customer_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'get', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    $data['page_list'] = $this->page_list->show('jump');
    //页面标题
    $data['list'] = $list;
    $data['page_title'] = '客源信息';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');
    //加载发布页面模板
    $this->view('house/sell_my_source', $data);
  }

  //根据经纪人id查询所有的客源
  public function broker_source()
  {
    //模板使用数据
    $data = array();
    $post_param = $this->input->post(NULL, TRUE);
    $broker_info = $this->user_arr;
    //门店数据
    //店长以上。所有门店
    if (intval($broker_info['role_level']) < 6) {
      $data['agency_list'] = $this->agency_model->get_children_by_company_id(intval($broker_info['company_id']));
    } else {
      //店长当前门店
      $data['agency_list'] = $this->agency_model->get_all_by_agency_id(intval($broker_info['agency_id']));
    }

    //经纪人的ID
    $broker_id = $broker_info['broker_id'];
    // 分页参数
    $pagee = $this->input->get('page', TRUE);
    $page = isset($pagee) ? intval($pagee) : intval($page);
    $this->_init_pagination($page, 5);
    //求购信息表名
    $tbl = 'buy_customer';
    //获取当前经纪人所在门店的数据范围
    //查询条件
    $cond_where = "status = 1 ";

    //表单提交参数组成的查询条件
    //门店条件
    if (isset($post_param['post_agency_id']) && !empty($post_param['post_agency_id'])) {
      $cond_where .= "AND agency_id = '" . intval($post_param['post_agency_id']) . "' ";
    }
    if (isset($post_param['cname']) && !empty($post_param['cname']) && $post_param['cname'] != '可搜索姓名') {
      $cond_where_ext = $post_param['cname'];
      if ($cond_where_ext == '%') {
        $cond_where_ext = '\%';
      }
      $cond_where_extt = "AND truename LIKE '%" . $cond_where_ext . "%' ";
      $cond_where .= $cond_where_extt;
    }
    $data['post_agency_id'] = $post_param['post_agency_id'];
    $data['cname'] = $post_param['cname'];

    //符合条件的总行数
    $this->_total_count = $this->broker_info_model->count_all_broker($cond_where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    $this->broker_info_model->set_select_fields(array('id', 'broker_id', 'phone', 'truename', 'agency_id'));
    $list = $this->broker_info_model->get_all_by($cond_where, $this->_offset, $this->_limit);

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'get', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    $data['page_list'] = $this->page_list->show('jump');
    //页面标题
    $data['list'] = $list;
    $data['page_title'] = '经纪人信息';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');
    //加载发布页面模板
    $this->view('house/sell_my_broker_source', $data);
  }

  //修改房源的日志录入匹配
  public function insetmatch($backinfo, $datainfo)
  {
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    //基本信息‘状态’数据处理
    if (!empty($config['status']) && is_array($config['status'])) {
      foreach ($config['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $config['status'][$k] = '暂不售';
        }
      }
    }

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $dis[$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $stred[$val['id']] = $val;
    }

    $constr = '';
    foreach ($backinfo as $key => $val) {
      if ($val != $datainfo[$key]) {
        switch ($key) {
//				    case 'a_ratio'://甲方佣金分成比例
//				        $constr .= '甲方佣金分成比例:'.strip_end_0($val).'%>>'.strip_end_0($datainfo[$key]).'%,';
//		                break;
//
//			        case 'b_ratio'://已方佣金分成比例
//			            $constr .= '已方佣金分成比例:'.strip_end_0($val).'%>>'.strip_end_0($datainfo[$key]).'%,';
//	                    break;
//
//		            case 'buyer_ratio'://买方支付佣金比例
//		                $constr .= '买方支付佣金比例:'.strip_end_0($val).'%>>'.strip_end_0($datainfo[$key]).'%,';
//	                    break;
//
//	                case 'seller_ratio'://卖方支付佣金比例
//	                    $constr .= '卖方支付佣金比例:'.strip_end_0($val).'%>>'.strip_end_0($datainfo[$key]).'%,';
//                        break;

          case 'sell_type':
            $constr .= '物业类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'dong':
            $constr .= '栋座:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'block_name':
            $constr .= '小区名字:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'district_id':
            $constr .= '区属：' . $dis[$val]['district'] . '>>' . $dis[$datainfo[$key]]['district'] . ',';
            break;

          case 'street_id':
            $constr .= '板块：' . $stred[$val]['streetname'] . '>>' . $stred[$datainfo[$key]]['streetname'] . ',';
            break;

          case 'address':
            $constr .= '地址:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'unit':
            $constr .= '单元:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'door':
            $constr .= '门牌:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'title':
            $constr .= '标题:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'bewrite':
            if (empty($val)) {
              $val = '空';
            }
            if (empty($datainfo[$key])) {
              $datainfo[$key] = '空';
            }
            $constr .= '描述:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'owner':
            $constr .= '业主姓名:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'idcare':
            if (empty($val)) {
              $val = '空';
            }
            if ($datainfo[$key]) {
              $datainfo[$key] = '空';
            }
            $constr .= '身份证:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'telno1':
            $constr .= '电话1:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'telno2':
            if (empty($val)) {
              $val = '空';
            }
            if (!$datainfo[$key]) {
              $datainfo[$key] = '空';
            }
            $constr .= '电话2:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'telno3':
            if (empty($val)) {
              $val = '空';
            }
            if (!$datainfo[$key]) {
              $datainfo[$key] = '空';
            }
            $constr .= '电话3:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'proof':
            if (empty($val)) {
              $val = '空';
            }
            if ($datainfo[$key]) {
              $datainfo[$key] = '空';
            }
            $constr .= '证书号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'mound_num':
            if (empty($val)) {
              $val = '空';
            }
            if ($datainfo[$key]) {
              $datainfo[$key] = '空';
            }
            $constr .= '丘地号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'record_num':
            if (empty($val)) {
              $val = '空';
            }
            if ($datainfo[$key]) {
              $datainfo[$key] = '空';
            }
            $constr .= '备案号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'status':
            $constr .= '状态:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'nature':
            $constr .= '房源性质:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'room':
            $constr .= '室:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'hall':
            $constr .= '厅:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'toilet':
            $constr .= '卫:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'balcony':
            $constr .= '阳台:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'floor_type':
            if ($val == 1) {
              $val = '单层';

            } else {
              $val = '跃层';

            }
            if ($datainfo[$key] == 1) {
              $datainfo[$key] = '单层';

            } else {
              $datainfo[$key] = '跃层';
            }
            $constr .= '楼层类型:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'floor':
            $constr .= '楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'totalfloor':
            $constr .= '总楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'subfloor':
            $constr .= '跃层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'forward':
            $constr .= '朝向:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'totalfloor':
            $constr .= '总楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'fitment':
            $constr .= '装修:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'buildarea':
            $constr .= '面积:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'buildyear':
            $constr .= '房龄:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'price':
            $constr .= '售价:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'avgprice':
            $constr .= '单价:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'lowprice':
            if (empty($config[$key][$val])) {
              $config[$key][$val] = '空';
            }
            if (empty($config[$key][$datainfo[$key]])) {
              $config[$key][$datainfo[$key]] = '空';
            }
            $constr .= '最低售价:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'taxes':
            $constr .= '税费:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'keys':
            if ($val == 1) {
              $val = '有';
            } else {
              $val = '无';
            }
            if ($datainfo[$key] == 1) {
              $datainfo[$key] = '有';
            } else {
              $datainfo[$key] = '无';
            }
            $constr .= '钥匙:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'isshare':
            if ($val == 1) {
              $val = '是';
            } else if ($val == 2) {
              $val = '审核中';
            } else {
              $val = '否';
            }
            if ($datainfo[$key] == 1) {
              $datainfo[$key] = '是';
            } else if ($datainfo[$key] == 2) {
              $datainfo[$key] = '审核中';
            } else {
              $datainfo[$key] = '否';
            }
            $constr .= '是否合作:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'key_number':
            $constr .= '钥匙编号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'division':
            if ($val == 1) {
              $ver = '是';
            } elseif ($val == 2) {
              $ver = '否';
            }
            if ($datainfo[$key] == 1) {
              $data = '是';
            } elseif ($datainfo[$key] == 2) {
              $data = '否';
            }
            $constr .= '是否分割:' . $ver . '>>' . $data . ',';
            break;

          case 'property':
            if (empty($config[$key][$val])) {
              $config[$key][$val] = '空';
            }
            if (empty($config[$key][$datainfo[$key]])) {
              $config[$key][$datainfo[$key]] = '空';
            }
            $constr .= '产权:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'current':
            if (empty($config[$key][$val])) {
              $config[$key][$val] = '空';
            }
            if (empty($config[$key][$datainfo[$key]])) {
              $config[$key][$datainfo[$key]] = '空';
            }
            $constr .= '现状:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'infofrom':
            if (empty($config[$key][$val])) {
              $config[$key][$val] = '空';
            }
            if (empty($config[$key][$datainfo[$key]])) {
              $config[$key][$datainfo[$key]] = '空';
            }
            $constr .= '信息来源:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'villa_type':
            if (empty($config[$key][$val])) {
              $config[$key][$val] = '空';
            }
            if (empty($config[$key][$datainfo[$key]])) {
              $config[$key][$datainfo[$key]] = '空';
            }
            $constr .= '别墅类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'entrust':
            if (empty($config[$key][$val])) {
              $config[$key][$val] = '空';
            }
            if (empty($config[$key][$datainfo[$key]])) {
              $config[$key][$datainfo[$key]] = '空';
            }
            $constr .= '委托类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'remark':
            if (empty($val)) {
              $val = '空';
            }
            if (empty($datainfo[$key])) {
              $datainfo[$key] = '空';
            }
            $constr .= '备注:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'house_type':
            $constr .= '住宅类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'equipment':
            $gg = explode(',', $val);
            $tt = explode(',', $datainfo[$key]);
            $constr .= '房屋设施:';
            if ($val) {
              foreach ($gg as $keyy) {
                $constr .= $config[$key][$keyy] . ',';
              }
            } else {
              $constr .= '空';
            }
            $constr .= '>>';
            if ($datainfo[$key]) {
              foreach ($tt as $tty) {
                $constr .= $config[$key][$tty] . ',';
              }
            } else {
              $constr .= '空';
            }
            break;

          case 'sell_tag':
            $gg = explode(',', $val);
            $tt = explode(',', $datainfo[$key]);
            $constr .= '标签:';
            if ($val) {
              foreach ($gg as $keyy) {
                $constr .= $config[$key][$keyy] . ',';
              }
            } else {
              $constr .= '空';
            }
            $constr .= '>>';
            if ($datainfo[$key]) {
              foreach ($tt as $tty) {
                $constr .= $config[$key][$tty] . ',';
              }
            } else {
              $constr .= '空';
            }
            break;

          case 'strata_fee':
            if (empty($val)) {
              $val = '空';
            }
            if (empty($datainfo[$key])) {
              $datainfo[$key] = '空';
            }
            $constr .= '物业费:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'costs_type':
            if ($val == 1) {
              $val = '元/月/㎡';
            } else {
              $val = '元/月';
            }
            if ($datainfo[$key] == 1) {
              $datainfo[$key] = '元/月/㎡';
            } else {
              $datainfo[$key] = '元/月';
            }
            $constr .= '物业费类型:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'setting':
            $gg = explode(',', $val);
            $tt = explode(',', $datainfo[$key]);
            $constr .= '周边配套:';
            if ($val) {
              foreach ($gg as $keyy) {
                $constr .= $config[$key][$keyy] . ',';
              }
            } else {
              $constr .= '空';
            }
            $constr .= '>>';
            if ($datainfo[$key]) {
              foreach ($tt as $tty) {
                $constr .= $config[$key][$tty] . ',';
              }
            } else {
              $constr .= '空';
            }
            break;

          case 'house_grade':
            if (empty($config[$key][$val])) {
              $config[$key][$val] = '空';
            }
            if (empty($config[$key][$datainfo[$key]])) {
              $config[$key][$datainfo[$key]] = '空';
            }
            $constr .= '房源等级:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'shop_trade':
            $gg = explode(',', $val);
            $tt = explode(',', $datainfo[$key]);
            $constr .= '目标业态:';
            if ($val) {
              foreach ($gg as $keyy) {
                $constr .= $config[$key][$keyy] . ',';
              }
            } else {
              $constr .= '空';
            }
            $constr .= '>>';
            if ($datainfo[$key]) {
              foreach ($tt as $tty) {
                $constr .= $config[$key][$tty] . ',';
              }
            } else {
              $constr .= '空';
            }
            break;

          case 'house_structure':
            if (empty($config[$key][$val])) {
              $config[$key][$val] = '空';
            }
            if (empty($config[$key][$datainfo[$key]])) {
              $config[$key][$datainfo[$key]] = '空';
            }
            $constr .= '房源结构:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'read_time':
            if (empty($config[$key][$val])) {
              $config[$key][$val] = '空';
            }
            if (empty($config[$key][$datainfo[$key]])) {
              $config[$key][$datainfo[$key]] = '空';
            }
            $constr .= '看房时间:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;
          case 'pic_inside_room': //记录图片上传跟进记录
            //旧的图片不在新的图片中表示删除
            $delpic = 0;
            foreach ($val as $v) {
              if (!in_array($v, $datainfo[$key])) {
                $delpic++;
              }
            }
            //新的图片不在旧的图片中表示新增
            $addpic = 0;
            foreach ($datainfo[$key] as $v) {
              if (!in_array($v, $val)) {
                $addpic++;
              }
            }
            if ($addpic > 0 && $delpic > 0) {
              $constr .= '上传了' . $addpic . '张照片,删除了' . $delpic . '张照片,';
            } else if ($addpic > 0) {
              $constr .= '上传了' . $addpic . '张照片,';
            } else if ($delpic > 0) {
              $constr .= '删除了' . $delpic . '张照片,';
            }
            break;
        }
      }
    }
    return $constr;
  }


  //举报页面加载
  public function report($house_id, $broker_id)
  {
    $data = array();
    $broker_id = intval($broker_id);//被举报经纪人id
    $house_id = intval($house_id);//房源id
    $data['house_id'] = $house_id;
    $data['broker_id'] = $broker_id;
    $this->load->model('pic_model');
    $data['picinfo'] = $this->pic_model->find_house_pic_by('r_house', $house_id);

    //页面标题
    $data['page_title'] = '我要举报';

    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadimg.js,'
      . 'mls/js/v1.0/cooperate_common.js'
    );
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification.js,mls/js/v1.0/backspace.js');

    //加载发布页面模板
    $this->view('house/sell_report', $data);

  }
  //添加钥匙
  public function add_key($house_id, $key_number, $method)
  {
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //房源信息

      $this->sell_house_model->set_id($house_id);
    $house_info = $this->sell_house_model->get_info_by_id();

    $this->load->model('key_model');

    $datainfo['number'] = $key_number;
    $datainfo['type'] = 1;
    $datainfo['house_id'] = $house_id;
    $datainfo['block_id'] = $house_info['block_id'];
    $datainfo['block_name'] = $house_info['block_name'];
    $datainfo['dong'] = $house_info['dong'];
    $datainfo['unit'] = $house_info['unit'];
    $datainfo['door'] = $house_info['door'];
    $datainfo['broker_id'] = $broker_id;
    $datainfo['agency_id'] = $this->user_arr['agency_id'];
    $datainfo['company_id'] = $this->user_arr['company_id'];
    $datainfo['add_time'] = time();
    $this->key_model->add_info($datainfo);
  }


  //导入报表
  public function import()
  {
    if (!empty($_POST['sub'])) {

      $config['upload_path'] = str_replace("\\", "/", UPLOADS . DIRECTORY_SEPARATOR . 'temp');

      //目录不存在则创建目录
      if (!file_exists($config['upload_path'])) {
        $aryDirs = explode("/", substr($config['upload_path'], 0, strlen($config['upload_path'])));
        $strDir = "";
        foreach ($aryDirs as $value) {
          $strDir .= $value . "/";
          if (!@file_exists($strDir)) {
            if (!@mkdir($strDir, 0777)) {
              return "mkdirError";
            }
          }
        }
      }

      $config['file_name'] = date('YmdHis', time()) . rand(1000, 9999);
      $config['allowed_types'] = 'xlsx|xls';
      $config['max_size'] = "2000";
      $this->load->library('upload', $config);
      //打印成功或错误的信息
      if ($this->upload->do_upload('upfile')) {
        $data = array("upload_data" => $this->upload->data());
        //新权限
        //范围（1公司2门店3个人）
        $view_import_house = $this->broker_permission_model->check('127');
        //上传的文件名称
        $broker_info = $this->user_arr;
        $this->load->model('read_model');
        $result = $this->read_model->read('sell_model', $broker_info, $data['upload_data'], 7, 1, $view_import_house);
        unlink($data['upload_data']['full_path']); //删除文件
      } else {
        $result = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css"></head><body style="background:#F2F2F2;"><p class="up_m_b_date_up" style="text-align: center;"><span class="up_e">上传失败</span>，请选择文件上传</p></body></html>';
      }
      echo $result;

    }
  }

  /**
   * 确定导入
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function sure()
  {
    $data = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();

    $sell_type = array();
    foreach ($data['config']['sell_type'] as $key => $k) { //物业类型
      $sell_type[$k] = $key;
    }
    $status = array();
    foreach ($data['config']['status'] as $key => $k) { //状态类型
      $status[$k] = $key;
    }
    $nature = array();
    foreach ($data['config']['nature'] as $key => $k) { //性质类型
      $nature[$k] = $key;
    }
    $forward = array();
    foreach ($data['config']['forward'] as $key => $k) { //朝向类型
      $forward[$k] = $key;
    }
    $fitment = array();
    foreach ($data['config']['fitment'] as $key => $k) { //装修类型
      $fitment[$k] = $key;
    }
    $taxes = array();
    foreach ($data['config']['taxes'] as $key => $k) { //税费类型
      $taxes[$k] = $key;
    }
    $entrust = array();
    foreach ($data['config']['entrust'] as $key => $k) { //委托类型
      $entrust[$k] = $key;
    }
    $id = $this->input->post('id', true);
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    $data['where']['id'] = $id;
    $data['where']['broker_id'] = $broker_id;
    $result = $this->sell_model->get_tmp($data['where'], '', '', '');
    $content = unserialize($result[0]['content']);
    //print_r($content);exit;
    $res = array();
    $i = 0;
    $fail_num = '';
    $content_count = count($content);
    $this->load->model('broker_info_model');
    foreach ($content as $key => $k) {
      //通过经纪人电话号码查底所属的基本信息
      $broker = $this->broker_info_model->get_one_by(array('phone' => $k[23]));
      /**
       * $res['broker_id'] = $broker_id;
       * $res['broker_name'] = trim($broker_info['truename']);
       * $res['agency_id'] = trim($broker_info['agency_id']); //门店ID
       * $res['company_id'] = intval($broker_info['company_id']);//获取总公司编号
       ***/
      $res['broker_id'] = $broker['broker_id'];
      $res['broker_name'] = trim($broker['truename']);
      $res['agency_id'] = trim($broker['agency_id']); //门店ID
      $res['company_id'] = intval($broker['company_id']);//获取总公司编号
      $where['cmt_name'] = $k[0];
      $community_info = $this->sell_model->community_info($where);
      if (!$community_info[0]['id']) {
        //$k[20]$k[21]需要判断为空？
        $dist_arr = $this->district_model->get_district_id($k[20]);
        $street_arr = $this->district_model->get_street_id($k[21]);
        $paramArray = array(
          'cmt_name' => $k[0],//楼盘名称
          'dist_id' => trim($dist_arr['id']),//区属
          'streetid' => trim($street_arr['id']),//板块
          'address' => $k[22],//地址
          'status' => 3,
        );
        $add_result = $this->community_model->add_community($paramArray);//楼盘数据入库
        if (!empty($add_result) && is_int($add_result)) {
          $community_info = $this->sell_model->community_info($where);
        }
      }
      $res['block_id'] = $community_info[0]['id'];
      $res['block_name'] = $community_info[0]['cmt_name'];
      $res['district_id'] = $community_info[0]['dist_id'];
      $res['street_id'] = $community_info[0]['streetid'];
      $res['address'] = $community_info[0]['address'];
      $res['sell_type'] = $sell_type[$k[1]];  //物业类型
      $res['dong'] = $k[2];
      $res['unit'] = $k[3];
      $res['door'] = $k[4];
      $res['owner'] = $k[5];
      foreach (explode("/", $k[6]) as $vo => $v) {
        $res['telno' . ($vo + 1)] = $v;
      }
      $res['status'] = $status[$k[7]];
      $res['nature'] = $nature[$k[8]];
      $res['isshare'] = 0; //默认为不合作
      $house = explode("/", $k[9]);
      $res['room'] = $house[0] ? $house[0] : 0;
      $res['hall'] = $house[1] ? $house[1] : 0;
      $res['toilet'] = $house[2] ? $house[2] : 0;
      if (!in_array($res['sell_type'], array(5, 6, 7))) {
        $res['forward'] = $forward[$k[10]]; //朝向类型
        $floor = explode("/", $k[11]);
        if (strpos($floor[0], "-") !== false) { //存在
          $res['floor_type'] = 2;
          $floor2 = explode("-", $floor[0]);
          $res['floor'] = $floor2[0];
          $res['subfloor'] = $floor2[1];
        } else {
          $res['floor_type'] = 1;
          $res['floor'] = $floor[0];
        }
        $res['totalfloor'] = $floor[1];
        $res['fitment'] = $fitment[$k[12]]; //装修类型
      }
      $res['buildyear'] = $k[13];
      $res['buildarea'] = $k[14];
      $res['price'] = $k[15];
      $res['avgprice'] = intval($res['price'] * 10000 / $res['buildarea']);
      $res['taxes'] = $taxes[$k[16]];
      $res['keys'] = $k[17] == '有' ? 1 : 0;
      $res['entrust'] = $entrust[$k[18]]; //委托类型
      $res['title'] = $k[19]; //标题
      $res['createtime'] = time();
      $res['updatetime'] = time();
      $res['ip'] = get_ip();
      $res['is_publish'] = 1; //默认群发房源
      //导入数据的唯一性判断
      $house_num = $this->check_house($res['block_id'], $res['door'], $res['unit'], $res['dong']);
      if ($house_num == 0) {
        if (($this->sell_model->add_data($res, 'db_city', 'sell_house')) > 0) {
          $i++;
        }
      } else {
        $fail_num .= ($key + 6) . ',';
      }
      unset($res);
    }
    $fail_num = substr($fail_num, 0, -1);
    $fail_num .= '。';
    if ($i > 0 && $i == $content_count) {
      $res = array('broker_id' => $broker_id);
      $this->sell_model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '房源导入成功！<br>成功录入房源' . $i . '条。';

      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 8;
      $add_log_param['text'] = '导入出售房源' . $i . '条';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
    } else if ($i > 0 && $i != $content_count) {
      $res = array('broker_id' => $broker_id);
      $this->sell_model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '房源导入成功！<br>成功录入房源' . $i . '条。<br>重复录入房源' . ($content_count - $i) . '条。<br>重复录入表格行数为：' . $fail_num;

      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 8;
      $add_log_param['text'] = '导入出售房源' . $i . '条';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
      $result['status'] = 'error';
      $result['error'] = '房源导入失败！再试一次吧！<br>可能失败的原因：1.网络连接超时；2.重复导入房源。';
    }
    echo json_encode($result);
  }

  /**
   * 出售房源报表导出
   * @author    kang
   */
  public function exportReport($page = 1)
  {
    //遗留 判断是否登录
    /*$broker_info = array();
        $broker_info = $this->user_arr;
        $broker_id = intval($broker_info['broker_id']);*/

    //模板使用数据
    $data = array();

    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();

    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      //获取房源默认排序字段
      $house_list_order_field = $company_basic_data['house_list_order_field'];
      //获取默认查询时间
      $sell_house_query_time = $company_basic_data['sell_house_query_time'];
      //获取房源跟进无堪房红色警告时间
      $sell_house_check_time = $company_basic_data['sell_house_check_time'];
      //两次房源跟进红色警告时间
      $house_follow_spacing_time = $company_basic_data['house_follow_spacing_time'];
      //是否开启合作中心
      $open_cooperate = $company_basic_data['open_cooperate'];
      //是否开启合作审核
      $check_cooperate = $company_basic_data['check_cooperate'];
      //出售房源最后跟进天数
      $sell_house_follow_last_time1 = $company_basic_data['sell_house_follow_last_time1'];
      $sell_house_follow_last_time2 = $company_basic_data['sell_house_follow_last_time2'];
      //房源列表页字段
      $sell_house_field = $company_basic_data['sell_house_field'];
      //是否开启查看保密信息必须写跟进
      $is_secret_follow = $company_basic_data['is_secret_follow'];
    } else {
      $sell_house_follow_last_time1 = $sell_house_follow_last_time2 = $check_cooperate = $open_cooperate = $house_follow_spacing_time = $sell_house_check_time = $sell_house_query_time = $house_list_order_field = $sell_house_field = $rent_house_field = $is_secret_follow = '';
    }

    //新权限
    //范围（1公司2门店3个人）
    $view_other_per_data = $this->broker_permission_model->check('1');
    $view_other_per = $view_other_per_data['auth'];
    $data['view_other_per'] = $view_other_per;
    //post参数
    $posts = $this->input->post(NULL, FALSE);
    //print_r($posts);exit;

    //判断是否有final_data数据
    $arr = explode('&', addslashes($posts['final_data']));
    for ($i = 0; $i < count($arr); $i++) {
      $l_arr = explode('=', $arr[$i]);
      $post_param[$l_arr[0]] = $l_arr[1];
    }
    if (!empty($post_param['block_name'])) {
      $post_param['block_name'] = urldecode($post_param['block_name']);
    }
    $data['post_param'] = $post_param;

    //是否提交了表单数据
    $is_submit_form = false;
    if (is_full_array($post_param)) {
      $is_submit_form = true;
    }
    $blockname = $this->input->post('blockname', true);
    //默认状态为有效
    if (!isset($post_param['status'])) {
      $post_param['status'] = 1;
    }
    //发布朋友圈筛选项和是否合作关系
    if ($post_param['isshare'] == '0') {
      $post_param['isshare_friend'] = 0;
    }

    //获取当前经纪人所在门店的数据范围
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_view_house');
    $all_access_agency_ids = '';
    if (is_full_array($access_agency_ids_data)) {
      foreach ($access_agency_ids_data as $k => $v) {
        $all_access_agency_ids .= $v['sub_agency_id'] . ',';
      }
      $all_access_agency_ids .= $this->user_arr['agency_id'];
      //$all_access_agency_ids .= $broker_info['agency_id'];
      $all_access_agency_ids = trim($all_access_agency_ids, ',');
    } else {
      //$all_access_agency_ids = $broker_info['agency_id'];
      $all_access_agency_ids = $this->user_arr['agency_id'];
    }

    //查询房源条件
    $cond_where = "id > 0 ";
    //基本设置默认查询时间
    if ($post_param['create_time_range'] == 0) {
      //半年
      if ('1' == $sell_house_query_time) {
        $half_year_time = intval(time() - 365 * 0.5 * 24 * 60 * 60);
        $cond_where .= " AND createtime>= '" . $half_year_time . "' ";
      }
      //一年
      if ('2' == $sell_house_query_time) {
        $one_year_time = intval(time() - 365 * 24 * 60 * 60);
        $cond_where .= " AND createtime>= '" . $one_year_time . "' ";
      }
    }
    //默认公司
    $post_param['post_company_id'] = $this->user_arr['company_id'];
    if ($view_other_per) {
      //如果有权限，赋予初始查询条件
      if (!isset($post_param['post_agency_id']) && $company_basic_data['sell_house_indication_range'] > 1) {
        $post_param['post_agency_id'] = $this->user_arr['agency_id'];
      }
      if (!isset($post_param['post_broker_id']) && $company_basic_data['sell_house_indication_range'] > 2) {
        $post_param['post_broker_id'] = $this->user_arr['broker_id'];
      }

      $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

      if ($post_param['post_agency_id']) {
        $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($post_param['post_agency_id']);
      }

    } else {
      //本人
      $post_param['post_broker_id'] = $this->user_arr['broker_id'];
    }

    //判断是否提交表单,设置本页搜索条件cookie
    if ($is_submit_form) {
      $sell_list = array(
        'district' => $post_param['district'],
        'street' => $post_param['street'],
        'block_name' => $post_param['block_name'],
        'block_id' => $post_param['block_id'],
        'areamin' => $post_param['areamin'],
        'areamax' => $post_param['areamax'],
        'pricemin' => $post_param['pricemin'],
        'pricemax' => $post_param['pricemax'],
        'post_agency_id' => $post_param['post_agency_id'],
        'post_broker_id' => $post_param['post_broker_id'],
        'sell_type' => $post_param['sell_type'],
        'room' => $post_param['room'],
        'yearmin' => $post_param['yearmin'],
        'yearmax' => $post_param['yearmax'],
        'nature' => $post_param['nature'],
        'status' => $post_param['status'],
        'fitment' => $post_param['fitment'],
        'forward' => $post_param['forward'],
        'isshare' => $post_param['isshare'],
        'isshare_friend' => $post_param['isshare_friend'],
        'is_outside' => $post_param['is_outside'],
        'orderby_id' => $post_param['orderby_id'],
        'page' => $post_param['page'],
        'dong' => $post_param['dong'],
        'unit' => $post_param['unit'],
        'door' => $post_param['door'],
        'telno' => $post_param['telno'],
        'house_id' => $post_param['house_id'],
        'post_company_id' => $post_param['post_company_id'],
        'create_time_range' => $post_param['create_time_range'],
        'floormin' => $post_param['floormin'],
        'floormax' => $post_param['floormax'],
      );
      setcookie('sell_list', serialize($sell_list), time() + 3600 * 24 * 7, '/');
    } else {
      $sell_list_search = unserialize($_COOKIE['sell_list']);
      if (is_full_array($sell_list_search)) {
        $post_param['district'] = $sell_list_search['district'];
        $post_param['street'] = $sell_list_search['street'];
        $post_param['block_name'] = $sell_list_search['block_name'];
        $post_param['block_id'] = $sell_list_search['block_id'];
        $post_param['areamin'] = $sell_list_search['areamin'];
        $post_param['areamax'] = $sell_list_search['areamax'];
        $post_param['pricemin'] = $sell_list_search['pricemin'];
        $post_param['pricemax'] = $sell_list_search['pricemax'];
        $post_param['post_agency_id'] = $sell_list_search['post_agency_id'];
        $post_param['post_broker_id'] = $sell_list_search['post_broker_id'];
        $post_param['sell_type'] = $sell_list_search['sell_type'];
        $post_param['room'] = $sell_list_search['room'];
        $post_param['yearmin'] = $sell_list_search['yearmin'];
        $post_param['yearmax'] = $sell_list_search['yearmax'];
        $post_param['nature'] = $sell_list_search['nature'];
        $post_param['status'] = $sell_list_search['status'];
        $post_param['fitment'] = $sell_list_search['fitment'];
        $post_param['forward'] = $sell_list_search['forward'];
        $post_param['isshare'] = $sell_list_search['isshare'];
        $post_param['isshare_friend'] = $sell_list_search['isshare_friend'];
        $post_param['is_outside'] = $sell_list_search['is_outside'];
        $post_param['orderby_id'] = $sell_list_search['orderby_id'];
        $post_param['page'] = $sell_list_search['page'];
        $post_param['dong'] = $sell_list_search['dong'];
        $post_param['unit'] = $sell_list_search['unit'];
        $post_param['door'] = $sell_list_search['door'];
        $post_param['telno'] = $sell_list_search['telno'];
        $post_param['house_id'] = $sell_list_search['house_id'];
        $post_param['post_company_id'] = $sell_list_search['post_company_id'];
        $post_param['create_time_range'] = $sell_list_search['create_time_range'];
        $post_param['floormin'] = $sell_list_search['floormin'];
        $post_param['floormax'] = $sell_list_search['floormax'];
      }
    }

    if (empty($post_param['post_agency_id'])) {
      if (!empty($all_access_agency_ids)) {
        //查询房源条件
        $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";
      }
    }

    $post_param['block_name'] = trim($post_param['block_name']);
    $cond_or_like = array();
    if (!empty($post_param['block_name'])) {
      $cond_or_like['like_key'] = array('block_name');
      $cond_or_like['like_value'] = $post_param['block_name'];
    }

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

    $data['post_param'] = $post_param;


    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //设置默认排序字段
    if ('1' == $house_list_order_field) {
      $default_order = 13;
    } else if ('2' == $house_list_order_field) {
      $default_order = 7;
    } else {
      $default_order = 0;
    }
    $roomorder = (isset($post_param['orderby_id']) && $post_param['orderby_id'] != '') ? intval($post_param['orderby_id']) : $default_order;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count =
      $this->sell_house_model->get_count_by_cond($cond_where, $cond_or_like);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $data['pages'] = $pages;
    //获取列表内容
    if (24 == $roomorder || 25 == $roomorder) {
      $list = $this->sell_house_model->get_list_by_cond_or_like_export($cond_where, $cond_or_like, 'is_sticky', 'desc', $order_arr['order_key'], $order_arr['order_by'], 'floor', 'asc', $post_param['mylimit'], $post_param['myoffset']);
    } else {
      $list = $this->sell_house_model->get_list_by_cond_or_like_export($cond_where, $cond_or_like, 'is_sticky', 'desc', $order_arr['order_key'], $order_arr['order_by'], '', '', $post_param['mylimit'], $post_param['myoffset']);
    }

    $this->load->model('api_broker_model');
    $this->load->model('key_model');
    $brokeridstr = '';
    if ($list) {
      foreach ($list as $key => $val) {
        $brokeridstr .= $val['broker_id'] . ',';
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $list[$key]['telno'] = $brokerinfo['phone'];
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
        // 最新跟进时间
        $list[$key]['genjintime'] = date('Y-m-d H:i', $val['updatetime']);
        //房源钥匙
        $key_where_cond = array(
          'type' => 1,
          'house_id' => intval($val['id'])
        );
        $key_data = $this->key_model->get_one_by($key_where_cond);
        if (is_full_array($key_data)) {
          $list[$key]['key_num'] = $key_data['number'];
          $list[$key]['key_broker_id'] = $key_data['broker_id'];
        }
      }
    }
    $data['list'] = $list;

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }

    //调用PHPExcel第三方类库
    $this->load->library('PHPExcel.php');
    $this->load->library('PHPExcel/IOFactory');
    //创建phpexcel对象
    $objPHPExcel = new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // 用于 2007 格式
    $objWriter->setOffice2003Compatibility(true);

    //设置phpexcel文件内容
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
      ->setLastModifiedBy("Maarten Balliauw")
      ->setTitle("Office 2007 XLSX Test Document")
      ->setSubject("Office 2007 XLSX Test Document")
      ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
      ->setKeywords("office 2007 openxml php")
      ->setCategory("Test result file");

    //设置表格导航属性
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '房源编号');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '楼盘');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '区域');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '物业类型');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '栋座');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '单元');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '门牌');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '业主姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '业主电话');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '现状');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '户型');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '朝向');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '楼层');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '总楼层');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', '装修');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', '房龄');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', '建筑面积');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', '底价');
    $objPHPExcel->getActiveSheet()->setCellValue('S1', '售价');
    $objPHPExcel->getActiveSheet()->setCellValue('T1', '钥匙id');
    $objPHPExcel->getActiveSheet()->setCellValue('U1', '收取钥匙人id');
    $objPHPExcel->getActiveSheet()->setCellValue('V1', '委托类型');
    $objPHPExcel->getActiveSheet()->setCellValue('W1', '委托签订人');
    $objPHPExcel->getActiveSheet()->setCellValue('X1', '房源登记人');
    $objPHPExcel->getActiveSheet()->setCellValue('Y1', '登记日期');

    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {
      //是否合作
      $is_share = "";
      if ($list[$i - 2]['isshare']) {
        $is_share = "是";
      } else {
        $is_share = "否";
      }

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "CS" . $list[$i - 2]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['block_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $data['district'][$list[$i - 2]['district_id']]['district']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['sell_type'] > 0 ? $data['config']['sell_type'][$list[$i - 2]['sell_type']] : "");
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['dong']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['unit']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['door']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['owner']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['telno1']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['status'] > 0 ? $data['config']['status'][$list[$i - 2]['status']] : "");
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 2]['room'] . "-" . $list[$i - 2]['hall'] . "-" . $list[$i - 2]['toilet']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $list[$i - 2]['forward'] > 0 ? $data['config']['forward'][$list[$i - 2]['forward']] : "");
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $list[$i - 2]['floor'] . ($list[$i - 2]['subfloor'] == 0 ? '' : '-' . $list[$i - 2]['subfloor']));
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $list[$i - 2]['totalfloor']);
      $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $list[$i - 2]['fitment'] > 0 ? $data['config']['fitment'][$list[$i - 2]['fitment']] : "");
      $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $list[$i - 2]['buildyear']);
      $objPHPExcel->getActiveSheet()->setCellValue('Q' . $i, $list[$i - 2]['buildarea'] . '平方米');
      $objPHPExcel->getActiveSheet()->setCellValue('R' . $i, $list[$i - 2]['lowprice'] . '万元');
      $objPHPExcel->getActiveSheet()->setCellValue('S' . $i, $list[$i - 2]['price'] . '万元');
      $objPHPExcel->getActiveSheet()->setCellValue('T' . $i, $list[$i - 2]['key_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('U' . $i, $list[$i - 2]['key_broker_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('V' . $i, $list[$i - 2]['entrust'] > 0 ? $data['config']['entrust'][$list[$i - 2]['entrust']] : "");
      $objPHPExcel->getActiveSheet()->setCellValue('W' . $i, '');
      $objPHPExcel->getActiveSheet()->setCellValue('X' . $i, $list[$i - 2]['broker_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('Y' . $i, date('Y-m-d', $list[$i - 2]['updatetime']));
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    $objPHPExcel->getActiveSheet()->setTitle('sell_house_report');
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');
    header("Content-Disposition: attachment;filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
    // print_r($data);exit;
    $objWriter->save('php://output');
    exit;
  }

  /** 渲染打印方式一的数据 */
  public function print_hid_one()
  {
    /*$data = array();
        $list = $this->input->post('hid_data');
        $data['list'] = json_decode($list,1);*/
    $this->load->library('My_memcached');
    $memcached = new My_memcached();
    $data['list'] = $memcached->get('print_one');

    $this->load->view('house/print_temp_one', $data);
  }

  /** 渲染打印方式二的数据 */
  public function print_hid_two()
  {
    /*$data = array();
        $list = $this->input->post('hid_data1');
        $data['list'] = json_decode($list,1);
        $data['totalnum'] = count($data['list']);*/
    $this->load->library('My_memcached');
    $memcached = new My_memcached();
    $list = $memcached->get('print_two');
    $data['list'] = $list;
    $data['totalnum'] = count($list);

    $this->load->view('house/print_temp_two', $data);
  }


  /** 渲染打印方式三的数据 */
  public function print_hid_three()
  {
    /*$data = array();
        $list = $this->input->get('hid_data2');
        $data['list'] = json_decode($list,1);*/
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    $this->load->library('My_memcached');
    $memcached = new My_memcached();
    $data['list'] = $memcached->get('print_three');

    $this->load->view('house/print_temp_three', $data);
  }


  public function share_tasks($house_id, $num)
  {
    $data = array();
    $num = intval($num);
    $data['sell_number'] = 'CS';
    $house_id = str_replace('%7C', ',', $house_id);
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();
    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }
    if ($house_id) {
      //新权限
      //范围（1公司2门店3个人）
      //获得当前数据所属的经纪人id和门店id
      $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
      $this->sell_house_model->set_id($house_id);
      $owner_arr = $this->sell_house_model->get_info_by_id();
      $share_tasks_per = $this->broker_permission_model->check('4', $owner_arr);
      //房源分配任务关联门店权限
      $agency_share_tasks_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '8');
      if (!$share_tasks_per['auth']) {
        $this->redirect_permission_none_iframe('js_fenpeirenwu');
        exit();
      } else {
        if (!$agency_share_tasks_per) {
          $this->redirect_permission_none_iframe('js_fenpeirenwu');
          exit();
        }
      }
      $sell_list = $this->sell_house_model->get_all_house($house_id);
    }
    $data['house_id'] = $house_id;
    $data['num'] = $num;
    $data['sell_list'] = $sell_list;
    //根据总公司编号获取分店信息
    $broker_info = $this->user_arr;
    $agency_id = intval($broker_info['agency_id']);//经纪人门店编号
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $data['broker_name'] = $broker_info['truename'];
    $data['broker_id'] = $broker_info['broker_id'];
    //操作加密字符串
    $secret_param = array('house_id' => $data['house_id'], 'broker_id' => $data['broker_id']);
    $data['secret_key'] = $this->verify->user_enrypt($secret_param);
    $this->load->model('api_broker_model');
    $agency_name = $this->api_broker_model->get_by_agency_id($agency_id);
    $data['agency_name'] = $agency_name['name'];

    //根据权限role_id获得当前经纪人的角色，判断角色是否店长以上
    $role_level = intval($broker_info['role_level']);
    if (is_int($role_level) && $role_level < 6) {
      //根据数据范围，获得门店数据
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_house_share_tasks');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $broker_info['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $broker_info['agency_id'];
      }
      $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    } else {
      $this_agency_data = $this->agency_model->get_by_id($agency_id);
      if (is_full_array($this_agency_data)) {
        $data['agency_list'] = array(
          array(
            'agency_id' => $this_agency_data['id'],
            'agency_name' => $this_agency_data['name']
          )
        );
      }
    }

    //根据门店id获取所在门店下的所有经纪人
    $broker_arr = $this->api_broker_model->get_brokers_agency_id($agency_id);
    $data['broker_list'] = $broker_arr;
    $data['page_title'] = '分配任务';
    $data['type'] = 'sell';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');

    //加载任务页面模板
    $this->view('house/sell_tasks', $data);
  }


  //根据门店id获取经纪人
  public function broker_list()
  {
    $broker_info = $this->user_arr;
    //获取当前经纪人所在门店的数据范围
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_view_house');
    $all_access_agency_ids = array();
    if (is_full_array($access_agency_ids_data)) {
      foreach ($access_agency_ids_data as $k => $v) {
        $all_access_agency_ids[] = $v['sub_agency_id'];
      }
      $all_access_agency_ids[] = $broker_info['agency_id'];
    } else {
      $all_access_agency_ids[] = $broker_info['agency_id'];
    }

    $agency_id = $this->input->get('agency_id', TRUE);
    if (0 == $agency_id) {
      $agency_id = $all_access_agency_ids;
    } else {
      $agency_id = intval($agency_id);
    }
    $this->load->model('api_broker_model');
    $agency_arr = $this->api_broker_model->get_brokers_agency_id($agency_id);
    echo json_encode($agency_arr);
  }


  //判断是否分配了给自己
  public function check_broker()
  {
    $broker_id = $this->input->get('broker_id', TRUE);
    $broker_id = intval($broker_id);
    $broker_info = $this->user_arr;
    $brokered_id = intval($broker_info['broker_id']);
    if ($broker_id == $brokered_id) {
      echo json_encode(array('msg' => true));
    }
  }

  //判断是否存在某房源
  public function check_is_exist_house()
  {
    $house_id = $this->input->get('house_id', TRUE);
    $house_id = intval($house_id);
    $result = array();
    if (is_int($house_id) && !empty($house_id)) {
      $this->sell_house_model->set_id($house_id);
      $house_detail = $this->sell_house_model->get_info_by_id();
      if (isset($house_detail['status']) && $house_detail['status'] != '5') {
        $result['msg'] = 'success';
      }
    }
    echo json_encode($result);
    exit;
  }

  //判断多个房源是否存在
  public function check_is_exist_house_str()
  {
    $house_id_str = $this->input->get('house_id_str', TRUE);
      $friend = $this->input->get('friend', TRUE);

    $result = array(
      'msg' => '',
      'exist_ids' => ''
    );
    $exist_id_arr = array();
    $house_id_arr = explode(',', $house_id_str);
    if (is_array($house_id_arr) && !empty($house_id_arr)) {
      //筛选出没有被删除的房源id
      foreach ($house_id_arr as $k => $v) {
        $this->sell_house_model->set_id(intval($v));
        $house_detail = $this->sell_house_model->get_info_by_id();
        if (isset($house_detail['status']) && $house_detail['status'] != '5') {
          $exist_id_arr[] = $v;
        }
      }
    }
    if (is_array($exist_id_arr) && !empty($exist_id_arr)) {
        if ($friend === 'district') {//从区域公盘下架，判断区域公盘内所属经纪人与当前经纪人门店是否一致
            $this->broker_info_model->set_select_fields(array('agency_id'));
            $owner_info = $this->broker_info_model->get_one_by(array('broker_id' => $house_detail['district_broker_id']));
            if ($owner_info['agency_id'] == $this->user_arr['agency_id']) {
                $result['msg'] = 'success';
                $result['exist_ids'] = implode(',', $exist_id_arr);
            } else {
                $result['msg'] = 'turn_other_house';
            }
        } else {
            $result['msg'] = 'success';
            $result['exist_ids'] = implode(',', $exist_id_arr);
        }
    }
    echo json_encode($result);
    exit;
  }

  //合作房客源数据是否合格（未删除、有效、合作）
  public function check_is_qualified_house()
  {
    $house_id = $this->input->get('house_id', TRUE);
    $house_id = intval($house_id);
    $result = array();
    if (is_int($house_id) && !empty($house_id)) {
      $this->sell_house_model->set_id($house_id);
      $house_detail = $this->sell_house_model->get_info_by_id();
      if (is_array($house_detail) && !empty($house_detail)) {
        if ($house_detail['isshare'] === '1' && $house_detail['status'] === '1') {
          $result['msg'] = 'success';
        }
      }
    }
    echo json_encode($result);
    exit;
  }


  //	添加分配任务
  public function add_tasks()
  {
    $house_id = $this->input->get('house_id', TRUE);//房源id
    $tasks_secret_key = $this->input->get('secret_key', TRUE);//secret_key
    $task_auth = $this->user_func_permission;
    $secret_param = array('house_id' => $house_id, 'broker_id' => $this->user_arr['broker_id']);
    $secret_key = $this->verify->user_enrypt($secret_param);
    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    $share_tasks_per = $this->broker_permission_model->check('4', $owner_arr);
    //房源分配任务关联门店权限
    $agency_share_tasks_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '8');
    if (!$share_tasks_per['auth']) {
      $this->redirect_permission_none_iframe('js_genjin');
      exit();
    } else {
      if (!$agency_share_tasks_per) {
        $this->redirect_permission_none_iframe('js_genjin');
        exit();
      }
    }
    $task_type = $this->input->get('task_type', TRUE);//任务类型
    $task_style = $this->input->get('task_style', TRUE);//任务方式
    $broker_info = $this->user_arr;//加载经纪人session
    $allot_broker_id = $broker_info['broker_id'];//分配人id
    $run_broker_id = $this->input->get('run_broker_id', TRUE);//执行人id
    $insert_date = time();//录入时间
    $over_date = strtotime($this->input->get('over_date', TRUE));//执行时间
    $content = $this->input->get('content', TRUE);//具体内容
    $house = explode(',', $house_id);
    $return_id = '';
    $this->load->model('api_broker_model');
    $broker = $this->api_broker_model->get_baseinfo_by_broker_id($run_broker_id);

    $this->load->model('task_model');
    if ($house) {
      foreach ($house as $val) {
        $add_arr = array(
          'task_type' => $task_type,
          'task_style' => $task_style,
          'house_id' => $val,
          'allot_broker_id' => $allot_broker_id,
          'run_broker_id' => $run_broker_id,
          'insert_date' => $insert_date,
          'over_date' => $over_date,
          'status' => 2,
          'content' => $content
        );
        $return_id = '';

        if ($run_broker_id && $allot_broker_id && $house_id && $run_broker_id != $allot_broker_id) {
          $return_id .= $this->task_model->insert($add_arr) . ',';
        }
        if ($return_id) {
          $this->load->model('message_base_model');
          $allot_broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($allot_broker_id);

          $run_broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($run_broker_id);
          if ($task_style == 1) {
            $val = 'CS' . $val;
            $params['type'] = 'f';
          } else {
            $val = 'CZ' . $val;
          }
          $params['id'] = $val;
          $params['name'] = $allot_broker_info['truename'];
          // $result = $this->message_base_model->pub_message('1a',$run_broker_info['broker_id'],
          // $run_broker_info['truename'],$allot_broker_info['truename'],$val,'/my_task/');//
          //33
          $result = $this->message_base_model->add_message('7-40-1', $run_broker_info['broker_id'], $run_broker_info['truename'], '/my_task/', $params);
        }
      }
    }
    if ($return_id) {
        echo json_encode(array('result' => '1'));
      //操作日志
      $this->sell_house_model->set_search_fields(array('block_name', 'address', 'dong', 'unit', 'door'));
      $this->sell_house_model->set_id(intval($house_id));
      $datainfo = $this->sell_house_model->get_info_by_id();

      $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($run_broker_id);

      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 6;
      $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id . ' 分配任务 ' . $broker_info['agency_name'] . ' ' . $broker_info['truename'];
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
        echo json_encode(array('result' => '2'));
    }
  }


  //分配房源
  public function allocate_house($house_id)
  {
    $data = array();
    $house_id = str_replace('_', ',', $house_id);
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();
    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }
    if ($house_id) {
      //新权限
      //范围（1公司2门店3个人）
      //获得当前数据所属的经纪人id和门店id
      $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
      $this->sell_house_model->set_id($house_id);
      $owner_arr = $this->sell_house_model->get_info_by_id();
      $allocate_house_per = $this->broker_permission_model->check('5', $owner_arr);
      //分配房源关联门店权限
      $agency_allocate_house_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '9');
      if (!$allocate_house_per['auth']) {
        $this->redirect_permission_none_iframe('js_allocate_house');
        exit();
      } else {
        if (!$agency_allocate_house_per) {
          $this->redirect_permission_none_iframe('js_allocate_house');
          exit();
        }
      }

      $house_list = $this->sell_house_model->get_all_house($house_id);
    }
    $data['house_id'] = $house_id;
    $data['house_list'] = $house_list;
    //根据总公司编号获取分店信息
    $broker_info = $this->user_arr;
    $agency_id = intval($broker_info['agency_id']);//经纪人门店编号
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $data['broker_name'] = $broker_info['truename'];
    $data['broker_id'] = $broker_info['broker_id'];
    $agency_name = $this->api_broker_model->get_by_agency_id($agency_id);
    $data['agency_name'] = $agency_name['name'];
    //操作加密字符串
    $secret_param = array('house_id' => $data['house_id'], 'broker_id' => $broker_info['broker_id']);
    $data['secret_key'] = $this->verify->user_enrypt($secret_param);

    //根据权限role_id获得当前经纪人的角色，判断角色是否店长以上
    $role_level = intval($broker_info['role_level']);
    if (is_int($role_level) && $role_level < 6) {
      //根据数据范围，获得门店数据
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_house_allocate');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $broker_info['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $broker_info['agency_id'];
      }
      $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    } else {
      $this_agency_data = $this->agency_model->get_by_id($agency_id);
      if (is_full_array($this_agency_data)) {
        $data['agency_list'] = array(
          array(
            'agency_id' => $this_agency_data['id'],
            'agency_name' => $this_agency_data['name']
          )
        );
      }
    }

    $data['page_title'] = '分配房源';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');
    $data['type'] = 'sell';
    //加载任务页面模板
    $this->view('house/allocate_house', $data);
  }


  //添加分配房源
  public function add_allocate_house()
  {
    $house_id = $this->input->post('house_id', TRUE);//房源id
    $allocate_secret_key = $this->input->post('secret_key', TRUE);//secret_key
    $allocate_auth = $this->user_func_permission;
    $secret_param = array('house_id' => $house_id, 'broker_id' => $this->user_arr['broker_id']);
    $secret_key = $this->verify->user_enrypt($secret_param);
    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    $allocate_house_per = $this->broker_permission_model->check('5', $owner_arr);
    //分配房源关联门店权限
    $agency_allocate_house_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '9');
    if (!$allocate_house_per['auth']) {
      $this->redirect_permission_none_iframe('js_allocate_house');
      exit();
    } else {
      if (!$agency_allocate_house_per) {
        $this->redirect_permission_none_iframe('js_allocate_house');
        exit();
      }
    }
    //分配给谁
    $run_broker_id = $this->input->post('run_broker_id', TRUE);
    $this->load->model('api_broker_model');
    $broker = $this->api_broker_model->get_baseinfo_by_broker_id($run_broker_id);

    $cond_where = "id IN(" . $house_id . ")";
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $return_id = '';
    if ($run_broker_id != $broker_id) {
        $return_id = $this->sell_house_model->update_info_by_cond(array('broker_id' => $run_broker_id, 'broker_name' => $broker['truename']), $cond_where);
    }

    if (intval($return_id) > 0) {
      //发送站内信通知经纪人接受收房源
        //$broker = $this->api_broker_model->get_baseinfo_by_broker_id($run_broker_id);
      $this->load->model('message_base_model');
      $params['name'] = $this->user_arr['truename'];
      $params['type'] = 'f';
      $params['id'] = 'CS' . $house_id;
      //33
      $this->message_base_model->add_message('7-48-1', $run_broker_id, $broker['truename'], '/sell/lists/', $params);

      //操作日志
      $this->sell_house_model->set_search_fields(array('block_name', 'address', 'dong', 'unit', 'door'));
      $this->sell_house_model->set_id(intval($house_id));
      $datainfo = $this->sell_house_model->get_info_by_id();

      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 7;
      $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id . ' 分配房源 ' . $broker['agency_name'] . ' ' . $broker['truename'];
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);
      echo '1';
    } else {
      echo '2';
    }
  }

  public function blank()
  {
    $data = array();
    $this->load->view('house/blank', $data);
  }

  //获取所有房源标题模板
  public function get_all_title_template()
  {
    $result = $this->house_title_template_model->get_sell_title_template_by_cond();
    echo json_encode($result);
    exit;
  }

  //获取所有房源内容模板
  public function get_all_content_template()
  {
    $result = $this->house_content_template_model->get_sell_content_template_by_cond();
    echo json_encode($result);
    exit;
  }

  //筛选房源标题模板
  public function get_title_template_by_cond()
  {
    $title_category = $this->input->get('title_category');
    if (!empty($title_category)) {
      $title_category_arr = explode(',', $title_category);
      $where_cond = array();
      if (is_int(array_search('all', $title_category_arr))) {
        $where_cond = array();
      } else {
        if (is_int(array_search('name', $title_category_arr))) {
          $where_cond['is_name'] = 1;
        }
        if (is_int(array_search('fitment', $title_category_arr))) {
          $where_cond['is_fitment'] = 1;
        }
        if (is_int(array_search('area', $title_category_arr))) {
          $where_cond['is_area'] = 1;
        }
        if (is_int(array_search('room', $title_category_arr))) {
          $where_cond['is_room'] = 1;
        }
        if (is_int(array_search('price', $title_category_arr))) {
          $where_cond['is_price'] = 1;
        }
      }
      $result = $this->house_title_template_model->get_sell_title_template_by_cond($where_cond);
    } else {
      $result = array('result' => 'nodata');
    }
    echo json_encode($result);
    exit;
  }

  //同步房源
  public function fang100($house_id = 0, $flag = 0)
  {

    $result_data = array();

    $credit_score = 0;//积分
    $level_score = 0;//成长值
    $msg = '';
    //当前经纪人id
    $this_broker_id = $this->user_arr['broker_id'];
    //当前经纪人是否认证
    $this_broker_group_id = $this->user_arr['group_id'];

    $house_id = $this->input->get('house_id', TRUE);//房源id
    $flag = $this->input->get('flag', TRUE);//类型

    //获得当前数据所属的经纪人id
    $this->sell_house_model->set_search_fields(array('id', 'broker_id', 'is_outside', 'status', 'pic_tbl', 'pic_ids'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();

    //未认证，提示不能使用此功能
    if (!($this_broker_group_id === '2')) {
      $result_data['msg'] = 'group_id_1';
      echo json_encode($result_data);
      exit;
    }
    //不是本人房源，提示没有权限
    if ($this_broker_id != $owner_arr['broker_id']) {
      $result_data['msg'] = 'no_permission';
      echo json_encode($result_data);
      exit;
    }
    //判断是否已同步
    if ($flag == $owner_arr['is_outside']) {
      if ('1' == $flag) {
        $result_data['msg'] = 'is_outside_1';
        echo json_encode($result_data);
        exit;
      } else if ('0' == $flag) {
        $result_data['msg'] = 'is_outside_0';
        echo json_encode($result_data);
        exit;
      }
    }
    //判断房源状态是否有效
    if ($owner_arr['status'] != '1') {
      $result_data['msg'] = 'status_failed';
      echo json_encode($result_data);
      exit;
    }

    if ('1' == $flag) {
      //判断是否已超过上限20条
//            $this_broker_outside_nums = $this->get_this_broker_outside_num();
//            if(is_int($this_broker_outside_nums) && $this_broker_outside_nums>499){
//                echo 'exce_upper_limit';exit;
//            }
      $is_outside_time = time();
    } else if ('0' == $flag) {
      $is_outside_time = 0;
    }

    $update_info = array('is_outside' => $flag, 'is_outside_time' => $is_outside_time);
    $this->sell_house_model->set_id($house_id);
    $result = $this->sell_house_model->update_info_by_id($update_info);
    if ($result === 1) {
      $msg = 'fang100_success';
      //房源符合至少5张室内图，1张户型图，成都同步平安好房
      $city_spell = $this->user_arr['city_spell'];
      if ($city_spell == 'cd') {
        $this->load->model('pic_model');
        //统计室内图的数量
        $where = array('tbl' => 'sell_house', 'type' => 1, 'rowid' => $house_id);
        $num1 = $this->pic_model->count_house_pic_by_cond($where);
        //统计户型图的数量
        $where = array('tbl' => 'sell_house', 'type' => 2, 'rowid' => $house_id);
        $num2 = $this->pic_model->count_house_pic_by_cond($where);
        if ($num1 >= 5 && $num2 >= 1) {
          $this->load->model('pinganhouse_model');
          $add_data = array('house_id' => $house_id, 'outside_time' => time());
          $this->pinganhouse_model->add_house($add_data);
        }
      }
      if ($flag == 1) //同步
      {
        $this->load->model('api_broker_credit_model');
        $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
        $credit_result = $this->api_broker_credit_model->rsync_fang100($owner_arr, 1);
        if ($city_spell == 'sz' || $city_spell == 'km') {
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
          $credit_result1 = $this->api_broker_credit_model->fang100_activity($owner_arr, 1);
          if (is_full_array($credit_result1) && $credit_result1['status']) {
            $credit_score += $credit_result1['score'];
          }
        }
        $this->load->model('api_broker_level_model');
        $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
        $level_result = $this->api_broker_level_model->rsync_fang100($owner_arr, 1);
        if (is_full_array($credit_result) && $credit_result['status']) {
          $credit_score += $credit_result['score'];
        }
        if (is_full_array($level_result) && $level_result['status']) {
          $level_score += $level_result['score'];
        }
        if ($credit_score) {
          $msg .= '-' . $credit_score;
        }
        if ($level_score) {
          $msg .= '-' . $level_score;
        }
      }
      $result_data['msg'] = $msg;
      echo json_encode($result_data);
      exit;
    } else {
      $result_data['msg'] = 'fang100_failed';
      echo json_encode($result_data);
      exit;
    }
  }

  public function get_this_broker_outside_num()
  {
    $result_num = 0;
    $this_broker_id = intval($this->user_arr['broker_id']);
    $where_cond = array(
      'broker_id' => $this_broker_id,
      'is_outside' => 1
    );
    $result_num = $this->sell_house_model->get_housenum_by_cond($where_cond);
    return $result_num;
  }

  public function change_house_is_outside()
  {
    $house_id = $this->input->get('house_id', TRUE);//房源id
    $is_outside = $this->input->get('is_outside', TRUE);//变更值

    $update_info = array('is_outside' => $is_outside);
    $this->sell_house_model->set_id($house_id);
    $result = $this->sell_house_model->update_info_by_id($update_info);
    echo json_encode(array());
  }

  /*工作统计日志
     * type:1出售2出租3求购4求租
     * $state：1信息录入2信息修改3图片上传4堪房5带看6钥匙提交
     */
  private function info_count($house_id, $state, $customer_id = 0)
  {
    $this->load->model('count_log_model');
    $this->load->model('count_num_model');
    $broker_info = $this->user_arr;
    $insert_log_data = array(
      'company_id' => $broker_info['company_id'],
      'agency_id' => $broker_info['agency_id'],
      'broker_id' => $broker_info['broker_id'],
      'dateline' => time(),
      'YMD' => date('Y-m-d'),
      'state' => $state,
      'type' => 1,
      'house_id' => $house_id,
      'customer_id' => $customer_id
    );
    $insert_id = $this->count_log_model->insert($insert_log_data);
    if ($insert_id) {
      $count_num_info = $this->count_num_model->get_one_by('broker_id = ' . $broker_info['broker_id'] . ' and YMD = ' . "'" . date('Y-m-d') . "'");
      if (is_full_array($count_num_info)) {
        //修改数据
        switch ($state) {
          case 1://信息录入
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'insert_num' => $count_num_info['insert_num'] + 1
            );
            break;
          case 2://信息修改
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'modify_num' => $count_num_info['modify_num'] + 1
            );
            break;
          case 3://图片上传
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'upload_num' => $count_num_info['upload_num'] + 1
            );
            break;
          case 4://堪房
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'look_num' => $count_num_info['look_num'] + 1
            );
            break;
          case 5://带看
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'looked_num' => $count_num_info['looked_num'] + 1
            );
            break;
          case 6://钥匙提交
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'key_num' => $count_num_info['key_num'] + 1
            );
            break;
          case 7://视频上传数
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'video_num' => $count_num_info['video_num'] + 1
            );
            break;
          case 8://查看保密信息
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'secret_num' => $count_num_info['secret_num'] + 1
            );
            break;
          case 9://普通跟进
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'follow_num' => $count_num_info['follow_num'] + 1
            );
            break;
        }
        $row = $this->count_num_model->update_by_id($update_data, $count_num_info['id']);
        if ($row) {
          return 'success';
        } else {
          return 'error';
        }
      } else {
        //添加数据
        switch ($state) {
          case 1://信息录入
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'insert_num' => 1
            );
            break;
          case 2://信息修改
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'modify_num' => 1
            );
            break;
          case 3://图片上传
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'upload_num' => 1
            );
            break;
          case 4://堪房
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'look_num' => 1
            );
            break;
          case 5://带看
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'looked_num' => 1
            );
            break;
          case 6://钥匙提交
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'key_num' => 1
            );
            break;
          case 7://视频上传数
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'video_num' => 1
            );
            break;
          case 8://查看保密信息
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'secret_num' => 1
            );
            break;
          case 9://普通跟进
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'follow_num' => 1
            );
            break;
        }
        $insert_num_id = $this->count_num_model->insert($insert_num_data);
        if ($insert_num_id) {
          return 'success';
        } else {
          return 'error';
        }
      }
    } else {
      return 'error';
    }
  }

  public function pic_ceshi()
  {
    $data = array();
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/cooperate_common.js');
    $this->view('house/pic_print', $data);
  }

  public function pic_deal()
  {
    //楼盘名
    $cmt_name = $this->input->post('cmt_name', TRUE);
    $cmt_name = mb_substr($cmt_name, 0, 15);

    $price = $this->input->post('price', TRUE);
    $buildarea = $this->input->post('buildarea', TRUE);
    $room_pic = $this->input->post('room_pic', TRUE);
    $fitment = $this->input->post('fitment', TRUE);
    $forward = $this->input->post('forward', TRUE);
    $broker_name = $this->input->post('broker_name', TRUE);
    //备注
    $remark = $this->input->post('remark', TRUE);
    $remark = mb_substr($remark, 0, 12);
    //门店名
    $agency_name = $this->input->post('agency_name', TRUE);
    $shinei = $this->input->post('shinei', TRUE);
    $huxing = $this->input->post('huxing', TRUE);
    $house_id = $this->input->post('house_id', TRUE);
    $type = $this->input->post('type', TRUE);
    //是否直接调用打印机
    $is_print = $this->input->post('is_print', TRUE);
    //是否展示微店二维码
    $is_qrcode = $this->input->post('is_qrcode', TRUE);
    //横向
    if (1 == $type || 3 == $type) {
      $agency_name = mb_substr($agency_name, 0, 18);
    } else {
      //纵向
      $agency_name = mb_substr($agency_name, 0, 13);
    }
    $shinei_big = '';
    $huxing_big = '';
    if (!empty($shinei)) {
      $shinei_big = str_replace('thumb/', '', $shinei);
    }
    if (!empty($huxing)) {
      $huxing_big = str_replace('thumb/', '', $huxing);
    }
    //二维码图片
    $qrcode = '';
    if ('1' == $is_qrcode) {
      $qrcode = $this->input->post('qrcode', TRUE);
    }

    $this->load->model('pic_model');
    $fileurl = $this->pic_model->house_print($cmt_name, $price, $buildarea, $room_pic, $fitment, $forward, $remark, $broker_name, $shinei_big, $huxing_big, $house_id, intval($type), $agency_name, $qrcode);
    //下载图片
    $pic_file_name = 'sell_' . $house_id . '.jpg';
    if ('success' == $fileurl) {
      echo "<img src='" . MLS_SOURCE_URL . "/mls/images/v1.0/house_print/" . $pic_file_name . "'>";
      if ($is_print == 1) {
        echo "<script>window.print();</script>";
      }
    } else {
      Header("Location: " . MLS_URL);
    }
    exit;
  }

  public function min_log_replace()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('broker_info_min_log_model');
    $window_min_id_arr = $this->input->get('window_min_id', TRUE);
    $is_pub = $this->input->get('is_pub', TRUE);
    $type = 'sell_house_list';
    if ('1' == $is_pub) {
      $type = 'sell_house_list_pub';
    }
    $last_result = 0;
    if (is_full_array($window_min_id_arr)) {
      $window_min_id_str = '';
      foreach ($window_min_id_arr as $k => $v) {
        $window_min_id_str .= $v . ',';
      }
      //判断当前经纪人是否已经有日志记录
      $where_cond = array('broker_id' => $broker_id);
      $query_result = $this->broker_info_min_log_model->get_log($where_cond);
      if (is_full_array($query_result)) {
        $update_data = array(
          $type => $window_min_id_str
        );
        $update_result = $this->broker_info_min_log_model->update_log($broker_id, $update_data);
        $last_result = $update_result;
      } else {
        $add_data = array(
          'broker_id' => $broker_id,
          $type => $window_min_id_str
        );
        $add_result = $this->broker_info_min_log_model->add_log($add_data);
        $last_result = $add_result;
      }

      if (is_int($last_result) && $last_result > 0) {
        echo 'success';
      } else {
        echo 'failed';
      }
      exit;
    }
  }

  public function min_log_del()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('broker_info_min_log_model');
    $window_min_id_arr = $this->input->get('window_min_id', TRUE);
    $is_pub = $this->input->get('is_pub', TRUE);
    $type = 'sell_house_list';
    if ('1' == $is_pub) {
      $type = 'sell_house_list_pub';
    }
    $last_result = 0;
    $window_min_id_str = '';
    if (is_full_array($window_min_id_arr)) {
      foreach ($window_min_id_arr as $k => $v) {
        $window_min_id_str .= $v . ',';
      }
    }
    //判断当前经纪人是否已经有日志记录
    $where_cond = array('broker_id' => $broker_id);
    $query_result = $this->broker_info_min_log_model->get_log($where_cond);
    if (is_full_array($query_result)) {
      $update_data = array(
        $type => $window_min_id_str
      );
      $update_result = $this->broker_info_min_log_model->update_log($broker_id, $update_data);
      $last_result = $update_result;
    }

    if (is_int($last_result) && $last_result > 0) {
      echo 'success';
    } else {
      echo 'failed';
    }
    exit;
  }


  /**
   * 出售房源详情页面图片保存到本地
   * 2016.5.20
   * cc
   */
  public function download_pic($house_id)
  {
    $house_info = array();
    $this->sell_house_model->set_search_fields(array());
    $this->sell_house_model->set_id($house_id);
    $house_info = $this->sell_house_model->get_info_by_id();
    $this->load->model('pic_model');
    $picinfo = $this->pic_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
    $id_str = substr($house_info['pic_ids'], 0, strlen($house_info['pic_ids']) - 1);
    $arr = $id_str != '' ? explode(',', $id_str) : array();
    $pics_arr = array();
    //房源图片数据重构
    if (is_full_array($arr) && is_full_array($picinfo)) {
      foreach ($arr as $k => $v) {
        foreach ($picinfo as $key => $value) {
          if ($value['id'] == $v && $value['type'] == 1) {
            $pics_arr[] = $value['url'];
          } else if ($value['id'] == $v && $value['type'] == 2) {
            $pics_arr[] = $value['url'];
          }
        }
      }
    }
    //print_r($pics_arr);die;
    $path = iconv("UTF-8", "GBK", "CS_" . $house_info['id']);
    if (!is_dir($path)) {
        //目录不存在则创建目录
        if (!@mkdir($path, 0777, true)) {
            return "mkdirError";
        }
    }
    foreach ($pics_arr as $val) {
      $val = changepic($val);
      $filename = basename($val);
      $localfile = $path . "/" . $filename;
      $img = $this->get_html($val);
      if (!$img) {
        return false;
      }
      $fp = @fopen($localfile, "w");
      fwrite($fp, $img);
      fclose($fp);
      if (true) {//去水印
        $urllist = getimagesize($localfile);
        $new_width = $urllist[0];
//        $new_height = $urllist[1] - 70;
          $new_height = $urllist[1];
        $myimg = imagecreatetruecolor($new_width, $new_height);
        if ($urllist['mime'] == 'image/jpeg') {
          imagecopyresampled($myimg, imagecreatefromjpeg($localfile), 0, 0, 0, 0, $new_width, $new_height, $new_width, $new_height);
          imagejpeg($myimg, $localfile);
        } else if ($urllist['mime'] == 'image/png') {
          imagecopyresampled($myimg, imagecreatefrompng($localfile), 0, 0, 0, 0, $new_width, $new_height, $new_width, $new_height);
          imagepng($myimg, $localfile);
        }
      }
    }
    //压缩
    $zip = new ZipArchive();
    if ($zip->open($path . '.zip', ZipArchive::OVERWRITE) === TRUE) {
      $this->addFileToZip($path, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
      $zip->close(); //关闭处理的zip文件
      $get_url = $path . '.zip';
      ob_end_clean();
//            header("Content-Type: application/force-download");
//            header("Content-Transfer-Encoding: binary");
      header('Content-Type: application/zip');
      header('Content-Disposition: attachment; filename=' . $get_url);
      header('Content-Length: ' . filesize($get_url));
      error_reporting(0);
      readfile($get_url);
      flush();
      ob_flush();
    }
    @unlink($path . '.zip');//删除压缩包
    $res = $this->deldir($path);//递归法删除文件夹
    exit;
  }

  public function get_html($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
  }

  public function deldir($dir)
  {
    //先删除目录下的文件：
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
      if ($file != "." && $file != "..") {
        $fullpath = $dir . "/" . $file;
        if (!is_dir($fullpath)) {
          unlink($fullpath);
        } else {
          deldir($fullpath);
        }
      }
    }
    @closedir($dh);
    //删除当前文件夹：
    if (rmdir($dir)) {
      return true;
    } else {
      return false;
    }
  }

  public function addFileToZip($path, $zip)
  {
    $handler = opendir($path); //打开当前文件夹由$path指定。
    while (($filename = readdir($handler)) !== false) {
      if ($filename != "." && $filename != "..") {//文件夹文件名字为'.'和‘..’，不要对他们进行操作
        if (is_dir($path . "/" . $filename)) {// 如果读取的某个对象是文件夹，则递归
          addFileToZip($path . "/" . $filename, $zip);
        } else { //将文件加入zip对象
          $zip->addFile($path . "/" . $filename);
        }
      }
    }
    @closedir($path);
  }

  //设置公共房源
  public function set_public_house()
  {
    $house_id = $this->input->get('house_id');
    if (intval($house_id) <= 0) {
      return false;
    }
    //当前经纪人是否认证
    $this_broker_group_id = $this->user_arr['group_id'];
    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    //$this->sell_house_model->set_search_fields(array('broker_id','agency_id','status','company_id'));
    $this->sell_house_model->set_id($house_id);
    $house_data = $this->sell_house_model->get_info_by_id();
    $owner_arr = array(
      'broker_id' => $house_data['broker_id'],
      'agency_id' => $house_data['agency_id'],
      'company_id' => $house_data['company_id']
    );
    //设置公共房源权限
    $set_public_house_per = $this->broker_permission_model->check('130', $owner_arr);
    //设置公共房源关联门店权限
    $agency_set_public_house_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '26');
    if (!$set_public_house_per['auth']) {
      $this->redirect_permission_none();
      exit();
    } else {
      if (!$agency_set_public_house_per) {
        $this->redirect_permission_none();
        exit();
      }
    }
    $this->sell_house_model->change_is_public_by_agency_id(array($house_id));
    echo json_encode(array('result' => 'ok'));
  }

  public function submit_secret_info()
  {

    $result = array();

    $house_id = intval($this->input->get("house_id"));
    $dong = strval($this->input->get("dong"));
    $unit = strval($this->input->get("unit"));
    $door = strval($this->input->get("door"));
    $owner = strval($this->input->get("owner"));
    $telno1 = strval($this->input->get("telno1"));
    $telno2 = strval($this->input->get("telno2"));
    $telno3 = strval($this->input->get("telno3"));
    $idcare = strval($this->input->get("idcare"));

    if (isset($house_id) && !empty($house_id)) {
        $this->sell_house_model->set_search_fields(array('block_id', 'dong', 'unit', 'door', 'owner', 'telno1', 'telno2', 'telno3', 'idcare'));
      $this->sell_house_model->set_id($house_id);
      $sell_backinfo = $this->sell_house_model->get_info_by_id();//修改前的信息
        $block_id = $sell_backinfo['block_id'];
        $house_check = $this->check_house_modify($block_id, $door, $unit, $dong, $house_id);
        if ($house_check) {
            $sell_dataifno = $update_info = array(
                'dong' => $dong,
                'unit' => $unit,
                'door' => $door,
                'owner' => $owner,
                'telno1' => $telno1,
                'telno2' => $telno2,
                'telno3' => $telno3,
                'idcare' => $idcare,
            );
            $this->sell_house_model->set_id($house_id);
            $update_result = $this->sell_house_model->update_info_by_id($update_info);
            if (1 == $update_result) {
                $result['msg'] = 'success';
                //房源跟进
                $sell_cont = $this->insetmatch($sell_backinfo, $sell_dataifno);
                //修改房源日志录入
                $need__info = $this->user_arr;
                $this->load->model('follow_model');
                $needarrt = array();
                $needarrt['broker_id'] = intval($need__info['broker_id']);
                $needarrt['type'] = 1;
                $needarrt['agency_id'] = $need__info['agency_id'];//门店ID
                $needarrt['company_id'] = $need__info['company_id'];//总公司id
                $needarrt['house_id'] = $house_id;
                $needarrt['text'] = $sell_cont;
                if (!empty($sell_cont)) {
                    $boolt = $this->follow_model->house_save($needarrt);
                    if (is_int($boolt) && $boolt > 0) {
                        //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
                        //获得基本设置房源跟进的天数
                        //获取当前经济人所在公司的基本设置信息
                        $this->load->model('house_customer_sub_model');
                        $company_basic_data = $this->company_basic_arr;
                        $house_follow_day = intval($company_basic_data['house_follow_spacing_time']);

                        $select_arr = array('id', 'house_id', 'date');
                        $this->follow_model->set_select_fields($select_arr);
                        $where_cond = 'house_id = "' . $house_id . '" and follow_type != 2 and type = 1';
                        $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
                        if (count($last_follow_data) == 2) {
                            $time1 = $last_follow_data[0]['date'];
                            $time2 = $last_follow_data[1]['date'];
                            $date1 = date('Y-m-d', strtotime($time1));
                            $date2 = date('Y-m-d', strtotime($time2));
                            $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
                            if ($differ_day > $house_follow_day) {
                                $this->house_customer_sub_model->add_sell_house_sub($house_id, 1);
                            } else {
                                $this->house_customer_sub_model->add_sell_house_sub($house_id, 0);
                            }
                        }
                    }
                }
            } else {
                $result['msg'] = '修改失败';
            }
        } else {
            $result['msg'] = '已有该房源，不能重复录入';
        }
    } else {
        $result['msg'] = '未选择房源';
    }
    echo json_encode($result);
  }
//发布到区域公盘检查重复，房源区域,是否参加公盘
    public function check_district_house()
    {
        $result = array();
        $house_id = intval($this->input->get('house_id', TRUE));
        $agency_id = $this->user_arr['agency_id'];
        $agency_indistrict = $this->cooperate_district_model->get_one_by_agency_id($agency_id);//门店所在区域公盘
        if (is_array($agency_indistrict) && !empty($agency_indistrict)) {
            if (is_int($house_id) && !empty($house_id)) {
                $this->sell_house_model->set_id($house_id);
                $house_detail = $this->sell_house_model->get_info_by_id();
                $house_district = $house_detail['district_id'];
                if ($house_detail['district_id'] == $agency_indistrict['district_id']) {//检查房源是否在对应区域
                    if ($house_detail['agency_id'] == $agency_id) {//房源所在门店与当前经纪人所在门店是否一致
                        //检查是否与公盘内的房源重复
                        $block_id = $house_detail['block_id'];
                        $door = $house_detail['door'];
                        $unit = $house_detail['unit'];
                        $dong = $house_detail['dong'];
                        $cond_where = "status != 5 and block_id = '$block_id' and door = '$door' and unit = '$unit' and dong = '$dong' and isshare_district = 1 and district_id = '$house_district'";
                        $this->sell_house_model->set_search_fields(array("id"));
                        $list = $this->sell_house_model->get_list_by_cond($cond_where);//检查区域公盘内房源是否重复
                        if ($list) { //检查是否重复
                            $result['msg'] = '区域公盘已有该房源';
                        } else {////检查是否上传户型图，室内图  类型1:室内2:户型;3委托协议书;4  卖家身份证 5 房产证
                            $this->load->model('pic_model');
                            $rom_pic = $this->pic_model->get_house_pic_by('sell_house', $house_id, 1);
                            $house_pic = $this->pic_model->get_house_pic_by('sell_house', $house_id, 2);

                            if (empty($rom_pic) || empty($house_pic) || count($rom_pic) < 3) {
                                $result['msg'] = '需要上传一张户型图和三张室内图，请到房源编辑页面下方上传户型图和室内图';
                                $result['is_have_certificate'] = 0;
                            } else {
                                $result['is_have_certificate'] = 1;
                                $result['msg'] = 'success';
                            }
                        }
                    } else {
                        $result['msg'] = "该房源不在您的门店下，无法发到区域公盘";
                    }
                } else {
                    $result['msg'] = "只能发送{$agency_indistrict['district_name']}区的房源到区域公盘";
                }
            }
        } else {
            $result['msg'] = '该门店尚未加入区域公盘';
        }
        echo json_encode($result);
        exit;
    }

    //保存上传的房产证
    function save_cetificate()
    {
        $url = $this->input->post('p_filename5', TRUE);
//        $pics['p_fileids5'] = $this->input->post('p_fileids5', TRUE);
        $house_id = $this->input->post('house_id', TRUE);
        $block_id = $this->input->post('block_id', TRUE);
        $insert_data_house = array(
            'tbl' => 'sell_house',
            'type' => '5',
            'rowid' => $house_id,
            'url' => $url,
            'block_id' => $block_id,
            'createtime' => time()
        );
        $this->load->model('pic_model');
        $picid = $this->pic_model->insert_house_pic($insert_data_house, "upload");
        if ($picid) {
            $result['msg'] = '房产证上传成功，请重新发送该房源到区域公盘';
        } else {
            $result['msg'] = '房产证上传失败';
        }
        echo json_encode($result);
    }

//房源信息
    public function house_info()
    {
        $house_id = $this->input->post('house_id', true);
        //房源信息
        $this->sell_house_model->set_search_fields(array('id', 'isshare', 'isshare_friend', 'isshare_district'));
        $this->sell_house_model->set_id($house_id);
        $house_info = $this->sell_house_model->get_info_by_id();
        echo json_encode($house_info);
    }
}

/* End of file sell.php */
/* Location: ./application/mls/controllers/sell.php */
