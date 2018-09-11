<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 房源导入类
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Upload_house_excel extends MY_Controller
{
  /**
   * 每页条目数
   *
   * @access private
   * @var int
   */
  private $_limit = 5;

  /**
   * 偏移
   *
   * @access private
   * @var int
   */
  private $_offset = 0;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('broker_info_model');
    $this->load->model('auth_review_model');
    $this->load->helper('user_helper');
    //$this->load->model('newhouse_sync_account_base_model');
    $this->load->model('district_model');
    $this->load->model('community_model');
    $this->load->model('api_broker_level_base_model');

  }

  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $data_view['title'] = '经纪人管理';
    $data_view['conf_where'] = 'index';
    $nowtime = time();
    //设置查询条件
    $search_where = $this->input->post('search_where');
    $search_value = $this->input->post('search_value');
    $search_status = $this->input->post('search_status');
    if (!$search_status || $search_status == 99) {
      $search_status = 99;
      $where = 'status <> 0';
    } else if ($search_status == 1) {
      $where = 'status = ' . $search_status . ' and expiretime >= ' . $nowtime;
    } else {
      $where = 'status = ' . $search_status . ' or expiretime < ' . $nowtime;
    }
    //引入经纪人基本类库
    $this->load->model('broker_info_model');

    $search_broker_base = false;
    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
      $search_broker_base = true;
    }
    //设置时间条件
    $search_time = $this->input->post('search_time');
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');

    if ($search_time && $start_time && $end_time) {
      $start_time_format = strtotime($start_time);
      $end_time_format = strtotime($end_time) + 86399;
      $where .= ' and ' . "$search_time >= $start_time_format and " . "$search_time <= $end_time_format ";
      $search_broker_base = true;
    }

    //用户组
    $group_id = $this->input->post('group_id');
    if ($group_id) {
      $where .= ' and group_id = ' . $group_id;
    }
    //套餐
    $package_id = $this->input->post('package_id');
    if ($package_id) {
      $where .= ' and package_id = ' . $package_id;
    }
    //公司和门店
    $company_id = $this->input->post('company_id');
    $company_name = $this->input->post('company_name');
    $agency_id = $this->input->post('agency_id');
    if ($company_id || $agency_id) {
      $this->load->model('agency_model');
      $agencys = $this->agency_model->get_children_by_company_id($company_id);
    }
    if ($agency_id) {
      $where .= ' and agency_id = ' . $agency_id;
      $data_view['agencys'] = $agencys;
    } else if ($company_id) {
      if (is_full_array($agencys)) {
        $agency_id = array();
        foreach ($agencys as $v) {
          $agency_id[] = $v['id'];
        }
        $agency_ids = implode(',', $agency_id);
        $where .= ' and agency_id in(' . $agency_ids . ')';
      }
    }

    //查询这个城市的客户经理数据
    $this->load->model('user_model');
    $masters = $this->user_model->get_user_by_cityid($_SESSION[WEB_AUTH]["city_id"]);
    $masters['-1'] = array('uid' => -1, 'truename' => '未指定');
    ksort($masters);
    $data_view['masters'] = $masters;
    //判断当前经纪人是否为客户经理
    $this_user_id = intval($_SESSION[WEB_AUTH]['uid']);
    $data_view['this_user_id'] = $this_user_id;
    $data_view['this_user_name'] = $_SESSION[WEB_AUTH]['truename'];
    if ($this_user_id > 0) {
      $this_user_data = $this->user_model->getuserByid($this_user_id);
      if (is_full_array($this_user_data[0])) {
        $am_cityid = intval($this_user_data[0]['am_cityid']);
      }
    }
    if (isset($am_cityid) && $am_cityid > 0) {
      $data_view['is_user_manager'] = true;
      $where .= ' and master_id = ' . $this_user_id;
    } else {
      $data_view['is_user_manager'] = false;
      //客户经理
      $master_id = $this->input->post('master_id', true);
      if ($master_id == -1) {
        $where .= ' and master_id = 0';
      } else if ($master_id) {
        $where .= ' and master_id = ' . $master_id;
      }
    }

    //记录搜索过的条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value,
      'search_time' => $search_time, 'start_time' => $start_time,
      'end_time' => $end_time, 'group_id' => $group_id,
      'package_id' => $package_id, 'company_id' => $company_id,
      'agency_id' => $agency_id, 'search_status' => $search_status,
      'company_name' => $company_name, 'master_id' => $master_id
    );
    //分页开始
    $data_view['count'] = 10;
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['count'] = $this->broker_info_model->count_by($where);
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //经纪人列表
    $broker_info = $this->broker_info_model->get_all_by($where, $data_view['offset'], $data_view['pagesize']);
    //echo "<pre>";
    //print_r($broker_info);
    //die;
    //搜索配置信息
    //var_dump($broker_info);exit;
    $data_view['where_config'] = $this->broker_info_model->get_where_config();

    if (is_full_array($broker_info)) {
      $group = $data_view['where_config']['group'];
      $package = $data_view['where_config']['package'];
      foreach ($broker_info as $key => $value) {
        $broker_info[$key]['group_str'] = $group[$value['group_id']];
        $broker_info{$key}['package_str'] = $package[$value['package_id']];
        //身份资质认证信息

        $ident_info = $this->auth_review_model->get_new("type = 1 and broker_id = " . $value['broker_id'], 0, 1);
        if (is_full_array($ident_info)) {
          $broker_info[$key]['auth_ident_status'] = $ident_info['status'];
        } else {
          $broker_info[$key]['auth_ident_status'] = '';
        }
        $broker_info[$key]['level'] = $this->api_broker_level_base_model->get_level($value['level']);


      }
    }

    $this->load->helper('common_load_source_helper');
    //$data_view['css'] = load_css('mls/css/v1.0/autocomplete.css');
    $data_view['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/autocomplete.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/openWin.js,'
      . 'mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');
    $data_view['broker_info'] = $broker_info;
    $this->load->view('upload_house_excel/index', $data_view);
  }


  //导入报表
  public function import($type)
  {
    if (!empty($_POST['sub'])) {
      $broker_id = $this->input->post('broker_id', true);
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
      $config['max_size'] = "200000";
      $this->load->library('upload', $config);
      //打印成功或错误的信息
      if ($this->upload->do_upload('upfile')) {
        $data = array("upload_data" => $this->upload->data());
        //上传的文件名称
        $broker_info = $this->broker_info_model->get_one_by(array('broker_id' => $broker_id));
        $this->load->model('read_model');
        if ($type == 1) {
          $result = $this->read_model->read_house_taizhou('sell_model', $broker_info, $data['upload_data'], 2, 1);
          unlink($data['upload_data']['full_path']); //删除文件
        } elseif ($type == 2) {
          $result = $this->read_model->read_house_taizhou('rent_house_model', $broker_info, $data['upload_data'], 2, 1);
          unlink($data['upload_data']['full_path']); //删除文件
        } elseif ($type == 3) {
          $result = $this->read_model->read_house_taizhou('buy_customer_model', $broker_info, $data['upload_data'], 2, 2);
          unlink($data['upload_data']['full_path']); //删除文件
        } elseif ($type == 4) {
          $result = $this->read_model->read_house_taizhou('rent_customer_model', $broker_info, $data['upload_data'], 2, 2);
          unlink($data['upload_data']['full_path']); //删除文件
        }
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
  public function sure($type)
  {
    if ($type == 1 || $type == 2) {
      $this->_sure($type);
    } elseif ($type == 3 || $type == 4) {
      $this->_sure_customer($type);
    }
  }

  private function _sure($type)
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
    if ($type == 1) {
      foreach ($data['config']['entrust'] as $key => $k) { //委托类型
        $entrust[$k] = $key;
      }
    } elseif ($type == 2) {
      foreach ($data['config']['rententrust'] as $key => $k) { //委托类型
        $entrust[$k] = $key;
      }
    }
    $id = $this->input->post('id', true);
    $broker_id = $this->input->post('broker_id', true);
    $broker_info = $this->broker_info_model->get_one_by(array('broker_id' => $broker_id));

    $data['where']['id'] = $id;
    $data['where']['broker_id'] = $broker_id;
    //print_r($data['where']);die;
    if ($type == 1) {
      $model = 'sell_model';
    } elseif ($type == 2) {
      $model = 'rent_house_model';
    }
    $this->load->model($model);
    $result = $this->$model->get_tmp($data['where'], '', '', '');
    $content = unserialize($result[0]['content']);
    //print_r($content);die;
    $res = array();
    $i = 0;
    $fail_num = '';
    $content_count = count($content);
    foreach ($content as $key => $k) {
      $res['broker_id'] = $broker_id;
      $res['broker_name'] = trim($broker_info['truename']);
      $res['agency_id'] = trim($broker_info['agency_id']); //门店ID
      $res['company_id'] = intval($broker_info['company_id']);//获取总公司编号
      $where['cmt_name'] = $k[0];
      if ($type == 1) {
        $community_info = $this->$model->community_info($where);
      } elseif ($type == 2) {
        $community_info = $this->$model->community_info_new($where);
      }
      if (!$community_info[0]['id'] && $k[0]) {
        //$k[20]$k[21]需要判断为空？
        $dist_arr = $this->district_model->get_district_id($k[22]);
        $street_arr = $this->district_model->get_street_id($k[23]);
        $paramArray = array(
          'cmt_name' => $k[0],//楼盘名称
          'dist_id' => trim($dist_arr['id']),//区属
          'streetid' => trim($street_arr['id']),//板块
          'address' => $k[24],//地址
          'status' => 3,
        );
        $add_result = $this->community_model->addcommunity($paramArray);//楼盘数据入库
        if (!empty($add_result) && is_int($add_result)) {
          if ($type == 1) {
            $community_info = $this->$model->community_info($where);
          } elseif ($type == 2) {
            $community_info = $this->$model->community_info_new($where);
          }
        }
      }
      $res['block_id'] = $community_info[0]['id'] ? $community_info[0]['id'] : 0;
      $res['block_name'] = $community_info[0]['cmt_name'] ? $community_info[0]['cmt_name'] : '';
      $res['district_id'] = $community_info[0]['dist_id'] ? $community_info[0]['dist_id'] : 0;
      $res['street_id'] = $community_info[0]['streetid'] ? $community_info[0]['streetid'] : 0;
      $res['address'] = $community_info[0]['address'];
      $res['sell_type'] = $sell_type[$k[1]] ? $sell_type[$k[1]] : 0;  //物业类型
      $res['dong'] = $k[2];
      $res['door'] = $k[4];
      $res['owner'] = $k[5];
      foreach (explode("/", $k[6]) as $vo => $v) {
        $res['telno' . ($vo + 1)] = $v;
      }
      $res['status'] = $status[$k[7]];
      $res['nature'] = $nature[$k[8]] ? $nature[$k[8]] : 2;
      $res['isshare'] = 0; //默认为不合作
      $house = explode("/", $k[9]);
      $res['room'] = intval($house[0]) ? $house[0] : 0;
      $res['hall'] = intval($house[1]) ? $house[1] : 0;
      $res['toilet'] = intval($house[2]) ? $house[2] : 0;
      $res['kitchen'] = intval($house[3]) ? $house[3] : 0;
      $res['balcony'] = intval($house[4]) ? $house[4] : 0;
      if (!in_array($res['sell_type'], array(5, 6, 7))) {
        $res['forward'] = $forward[$k[10]] ? $forward[$k[10]] : 0; //朝向类型
        $floor = explode("/", $k[11]);
        if (strpos($floor[0], "-") !== false) { //存在
          $res['floor_type'] = 2;
          $floor2 = explode("-", $floor[0]);
          $res['floor'] = $floor2[0];
          $res['subfloor'] = $floor2[1];
        } else {
          $res['floor_type'] = 1;
          $res['floor'] = $floor[0] ? $floor[0] : 0;
        }
        $res['totalfloor'] = $floor[1] ? $floor[1] : 0;
        $res['fitment'] = $fitment[$k[13]] ? $fitment[$k[13]] : 0; //装修类型
      }
      $res['buildyear'] = $k[14] ? $k[14] : 0;
      $res['buildarea'] = $k[16] ? $k[16] : 1;
      $res['price'] = $k[17] ? $k[17] : 0;
      if ($type == 1) {
        $res['garage_area'] = $k[15] ? $k[15] : 0;
        $res['avgprice'] = intval($res['price'] * 10000 / $res['buildarea']);
        $res['lowprice'] = $k[32] ? $k[32] : 0;
        $res['taxes'] = $taxes[$k[18]] ? $taxes[$k[18]] : 0;//税费
        $res['entrust'] = $entrust[$k[20]] ? $entrust[$k[20]] : 0; //委托类型
      } elseif ($type == 2) {
        $res['garage_area'] = $k[15] ? $k[15] : 0;
        $res['rententrust'] = $entrust[$k[20]] ? $entrust[$k[20]] : 0; //委托类型
      }
      $res['keys'] = ($k[19] == '是') ? 1 : 0;

      $res['title'] = $k[25]; //标题
      $res['createtime'] = $k[31];
      $res['updatetime'] = time();
      $res['ip'] = get_ip();
      $res['is_publish'] = 1; //默认群发房源
      //导入数据的唯一性判断
      //$house_num = $this->check_house($res['block_id'] , $res['door'] , $res['broker_id'] ,$type);
      //if($house_num == 0){
      //print_r($res);exit;
      if ($type == 1) {
        if (($this->$model->add_data($res, 'db_city', 'sell_house')) > 0) {
          $i++;
        }
      } elseif ($type == 2) {
        if (($this->$model->add_data($res, 'db_city', 'rent_house')) > 0) {
          $i++;
        }
      }
      /*}else{
          $fail_num .= ($key+2).',';
      }*/
      unset($res);
    }
    $fail_num = substr($fail_num, 0, -1);
    $fail_num .= '。';
    if ($i > 0 && $i == $content_count) {
      $res = array('broker_id' => $broker_id);
      $this->$model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '房源导入成功！<br>成功录入房源' . $i . '条。';
    } else if ($i > 0 && $i != $content_count) {
      $res = array('broker_id' => $broker_id);
      $this->$model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '房源导入成功！<br>成功录入房源' . $i . '条。<br>重复录入房源' . ($content_count - $i) . '条。<br>重复录入表格行数为：' . $fail_num;
    } else {
      $result['status'] = 'error';
      $result['error'] = '房源导入失败！再试一次吧！<br>可能失败的原因：1.网络连接超时；2.重复导入房源。';
    }
    echo json_encode($result);
  }

  private function _sure_customer($type)
  {
    $data = array();
    //加载求购、求租基本配置MODEL
    $this->load->model('customer_base_model');
    $data['config'] = $this->customer_base_model->get_base_conf();
    $status = array();
    foreach ($data['config']['status'] as $key => $k) { //状态类型
      $status[$k] = $key;
    }
    $public_type = array();
    foreach ($data['config']['public_type'] as $key => $k) { //性质类型
      $public_type[$k] = $key;
    }
    $property_type = array();
    foreach ($data['config']['property_type'] as $key => $k) { //物业类型
      $property_type[$k] = $key;
    }
    /*
    $share = array();
    foreach($data['config']['is_share'] as $key => $k){ //是否合作
        $share[$k] = $key;
    }
    */
    $id = $this->input->post('id', true);
    $broker_id = $this->input->post('broker_id', true);
    $broker_info = $this->broker_info_model->get_one_by(array('broker_id' => $broker_id));
    if ($type == 3) {
      $model = 'buy_customer_model';
    } elseif ($type == 4) {
      $model = 'rent_customer_model';
    }
    $broker_id = intval($broker_info['broker_id']);
    $data['where']['id'] = $id;
    $data['where']['broker_id'] = $broker_id;
    $this->load->model('sell_model');
    $this->load->model($model);
    $result = $this->sell_model->get_tmp($data['where'], '', '', '');//print_r($result);exit;
    $content = unserialize($result[0]['content']);
    $res = array();
    $i = 0;
    $fail_num = '';
    $content_count = count($content);
    //print_r($content);exit;
    foreach ($content as $key => $k) {
      $res['broker_id'] = $broker_id;
      $res['broker_name'] = trim($broker_info['truename']);
      $res['agency_id'] = trim($broker_info['agency_id']); //门店ID
      $res['company_id'] = trim($broker_info['company_id']); //公司ID
      $res['truename'] = $k[5];  //客户姓名
      $res['telno1'] = "";
      $res['telno2'] = "";
      $res['telno3'] = "";
      foreach (explode("/", $k[6]) as $vo => $v) {
        $res['telno' . ($vo + 1)] = $v;
      }

      $res['status'] = $status[$k[7]];
      $res['public_type'] = 1;
      $res['property_type'] = $property_type[$k[1]];
      $res['is_share'] = 0;
      if (in_array($res['property_type'], array(1, 2))) {
        $res['room_min'] = $k[9];
        $res['room_max'] = $k[9];
      }
      $res['area_min'] = $k[26] ? $k[26] : 0;
      $res['area_max'] = $k[27] ? $k[27] : 0;
      $res['price_min'] = $k[28] ? $k[28] : 0;
      $res['price_max'] = $k[29] ? $k[29] : 0;
      foreach (explode("/", $k[0]) as $vo => $v) {

        $where['cmt_name'] = $v;
        $community_info = $this->$model->community_info($where);
        if (!$community_info[0]['id'] && $v) {
          //$k[20]$k[21]需要判断为空？
          $dist_arr = $this->district_model->get_district_id($k[22]);
          $street_arr = $this->district_model->get_street_id($k[23]);
          $paramArray = array(
            'cmt_name' => $v,//楼盘名称
            'dist_id' => trim($dist_arr['id']),//区属
            'streetid' => trim($street_arr['id']),//板块
            'address' => $k[24],//地址
            'status' => 3,
          );
          $add_result = $this->community_model->addcommunity($paramArray);//楼盘数据入库
          if (!empty($add_result) && is_int($add_result)) {
            $community_info = $this->$model->community_info($where);
          }
        }
        //print_r($dist_arr);exit;
        if (!$community_info[0]['id']) {
          $dist_arr = $this->district_model->get_district_id($k[22]);
          $street_arr = $this->district_model->get_street_id($k[23]);
          $res['cmt_id' . ($vo + 1)] = 0;
          $res['cmt_name' . ($vo + 1)] = $v;
          $res['dist_id' . ($vo + 1)] = intval($dist_arr['id']);
          $res['street_id' . ($vo + 1)] = intval($street_arr['id']);
        } else {
          $res['cmt_id' . ($vo + 1)] = $community_info[0]['id'];
          $res['cmt_name' . ($vo + 1)] = $community_info[0]['cmt_name'];
          $res['dist_id' . ($vo + 1)] = $community_info[0]['dist_id'];
          $res['street_id' . ($vo + 1)] = $community_info[0]['streetid'];
        }
      }

      $res['creattime'] = $k[31];
      $res['updatetime'] = time();
      $res['ip'] = get_ip();
      //导入数据的唯一性判断
      $customer_num = $this->_get_customer_num_by_telno($res['telno1'], $res['telno2'], $res['telno3'], '', $type);
      if ($customer_num == 0) {
        if ($type == 3) {
          if (($this->$model->add_data($res, 'db_city', 'buy_customer')) > 0) {
            $i++;
          }
        } elseif ($type == 4) {
          if (($this->$model->add_data($res, 'db_city', 'rent_customer')) > 0) {
            $i++;
          }
        }
      } else {
        $fail_num .= ($key + 8) . ',';
      }
      unset($res);
    }
    $fail_num = substr($fail_num, 0, -1);
    $fail_num .= '。';
    if ($i > 0 && $i == $content_count) {
      $res = array('broker_id' => $broker_id);
      $this->sell_model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '客源导入成功！<br />成功录入客源' . $i . '条。';
    } else if ($i > 0 && $i != $content_count) {
      $res = array('broker_id' => $broker_id);
      $this->sell_model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '客源导入成功！<br>成功录入客源' . $i . '条。<br>重复录入客源' . ($content_count - $i) . '条。<br>重复录入表格行数为：' . $fail_num;
    } else {
      $result['status'] = 'error';
      $result['error'] = '客源导入失败！再试一次吧！<br />可能失败的原因：1.网络连接超时；2.重复导入客源。';
    }

    echo json_encode($result);
  }

  //判断房源是否重复
  public function check_house($block_id, $door, $broker_id, $type)
  {
    //经纪人信息
    $broker_info = $this->broker_info_model->get_one_by(array('broker_id' => $broker_id));
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    //获取全部分公司信息
    $this->load->model('api_broker_model');
    $agency_list = $this->api_broker_model->get_agencys_by_company_id($company_id);
    $arr_agency_id = array();
    foreach ($agency_list as $key => $val) {
      $arr_agency_id[] = $val['agency_id'];
    }
    $agency_ids = implode(',', $arr_agency_id);
    $cond_where = "status != 5 and block_id = '$block_id' and door = '$door' ";
    if ($agency_ids) {
      $cond_where .= " and agency_id in (" . $agency_ids . ")";
    }
    if ($type == 1) {
      $tbl = "sell_house";
      $this->load->model('sell_house_model');
      $this->sell_house_model->set_tbl($tbl);
      $house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
    } elseif ($type == 2) {
      $tbl = "rent_house";
      $this->load->model('rent_house_model');
      $this->rent_house_model->set_tbl($tbl);
      $house_num = $this->rent_house_model->get_housenum_by_cond($cond_where);
    }
    return $house_num;
  }

  /**
   * 根据电话号码获取客源个数
   *
   * @access  public
   * @param  string $telno1 电话号码
   * @param  string $telno2 电话号码
   * @param  string $telno3 电话号码
   * @param   int $cid 客源编号
   * @return  int 客源条数
   */
  private function _get_customer_num_by_telno($telno1, $telno2 = '', $telno3 = '', $cid = 0, $type)
  {
    $customer_num = 0;

    if (!empty($telno1)) {
      $cond_telno_str = "'" . $telno1 . "'";
      $cond_telno_str .= isset($telno2) && $telno2 != '' ? ",'" . $telno2 . "'" : '';
      $cond_telno_str .= isset($telno3) && $telno3 != '' ? ",'" . $telno3 . "'" : '';

      //经纪人信息
      $broker_id = intval($this->user_arr['broker_id']);
      $agency_id = intval($this->user_arr['agency_id']);
      $cid = intval($cid);

      if ($cid > 0) {
        $cond_where = "agency_id = '" . $agency_id . "' AND id != '" . $cid . "'";
      } else {
        $cond_where = "agency_id = '" . $agency_id . "' ";
      }

      $cond_where .= " AND ( telno1 IN ($cond_telno_str) OR telno2 IN ($cond_telno_str) OR telno3 IN ($cond_telno_str)) ";


      if ($type == 3) {
        $this->load->model('buy_customer_model');
        $customer_num = $this->buy_customer_model->get_buynum_by_cond($cond_where);
      } elseif ($type == 4) {
        $this->load->model('rent_customer_model');
        $customer_num = $this->rent_customer_model->get_rentnum_by_cond($cond_where);
      }
    }

    return $customer_num;
  }

}

/* End of file Broker_info.php */
/* Location: ./application/mls_admin/controllers/Broker_info.php */
