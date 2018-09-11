<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 添加分门店
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Agency extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('agency_model');
    $this->load->model('user_model');
    $this->load->helper('user_helper');
  }

  //门店管理页
  public function index($company_id = '')
  {
    //批量设置客户经理
    $master = $this->input->post('master');
    $plid = $this->input->post('plid');
    if ($master > 0 && $plid != '') {

      $this->agency_model->set_master($plid, $master);
    }

    //判断当前经纪人是否为客户经理
    $this_user_id = intval($_SESSION[WEB_AUTH]['uid']);
    if ($this_user_id > 0) {
      $this_user_data = $this->user_model->getuserByid($this_user_id);
      if (is_full_array($this_user_data[0])) {
        $am_cityid = intval($this_user_data[0]['am_cityid']);
      }
    }
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $search_where = $this->input->post('search_where');
    $search_value = $this->input->post('search_value');
    $where = 'status = 1';
    $data_view['is_user_manager'] = false;
    if (isset($am_cityid) && $am_cityid > 0) {
      $data_view['is_user_manager'] = true;
      $where .= ' AND master_id = "' . $this_user_id . '" ';
    }
    $where .= $company_id ? ' AND company_id = ' . $company_id : ' AND company_id <> 0';

    $this->load->model('district_model');//区属模型类
    $districtarr = $this->district_model->get_district();
    if (is_full_array($districtarr)) {
      //获取各个门店名下有多少门店
      foreach ($districtarr as $k => $v) {
        $data_view['district'][$v['id']] = $v;
        $data_view['district_name'][$v['district']] = $v['id'];
      }
    }

    if ($search_where == 'name') {
      if ($search_where && $search_value) {
        $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
      }
    } else if ($search_where == 'dist') {
      foreach ($data_view['district_name'] as $district_name => $district_id) {
        if ($district_name == $search_value) {
          $where .= " and dist_id = '" . $district_id . "'";
          break;
        }
      }
    } else if ($search_where == 'companyname') {
      if ($search_where && $search_value) {
        $company_id = $this->agency_model->get_company_id($search_value);
          if (empty($company_id)) {
              $company_id = 0;
          }
          $where .= " and company_id in (" . $company_id . ")";
      }
    }

    //条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value
    );


    //分页开始
    $data_view['count'] = $this->agency_model->count_by($where);
    $data_view['pagesize'] = 50; //设定每一页显示的记录数
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //门店列表
    $data_view['agency'] = $this->agency_model->get_all_by(
      $where, $data_view['offset'], $data_view['pagesize']);


    if (is_full_array($data_view['agency'])) {
      $this->load->model('broker_info_model');
      //获取各个门店名下有多少门店
      foreach ($data_view['agency'] as $k => $v) {
        $company_one = $this->agency_model->get_by_id($v['company_id']);
        $data_view['agency'][$k]['dist_name'] = $data_view['district'][$v['dist_id']]['district'];
        $data_view['agency'][$k]['company_name'] = $company_one['name'];
        $data_view['agency'][$k]['broker_count'] = $this->broker_info_model->count_by_agency_id($v['id']);
      }
    }
    //查询这个城市的客户经理数据
    $this->load->model('user_model');
    $masters = $this->user_model->get_user_by_cityid($_SESSION[WEB_AUTH]["city_id"]);
    $data_view['masters'] = $masters;
    $data_view['title'] = '门店管理';
    $data_view['conf_where'] = 'index';
    $this->load->view('agency/index', $data_view);
  }

  //添加门店功能
  public function add()
  {
    $this->load->model('district_model');//区属模型类
    $data_view = array();
    //查询这个城市的客户经理数据
    $this->load->model('user_model');
    $masters = $this->user_model->get_user_by_cityid($_SESSION[WEB_AUTH]["city_id"]);
    //查找所有总公司信息
    $data_view['company'] = $this->agency_model->get_company_by();
    $data_view['district'] = $this->district_model->get_district();
    $data_view['masters'] = $masters;
    $data_view['title'] = '门店管理-添加门店';
    $data_view['conf_where'] = 'index';
    $data_view['addResult'] = '';
    $submit_flag = $this->input->post('submit_flag');
    if ($submit_flag == 'add') {
      $this->load->library('form_validation');//表单验证
      $this->form_validation->set_rules('company_id', 'Company ID', 'required');
      $this->form_validation->set_rules('dist_id', 'distinct ID', 'required');
      //$this->form_validation->set_rules('streetid', 'street ID', 'required');
      $this->form_validation->set_rules('name', 'Name', 'required');
      //$this->form_validation->set_rules('telno', 'Telno', 'required');
      //$this->form_validation->set_rules('address', 'Address', 'required');
      //获取参数
      $company_id = intval($this->input->post('company_id'));
      $dist_id = intval($this->input->post('dist_id'));
      $street_id = intval($this->input->post('streetid'));
      $name = trim($this->input->post('name'));
      $telno = trim($this->input->post('telno'));
      $address = trim($this->input->post('address'));
      $agency_type = intval($this->input->post('agency_type'));
      $master_id = intval($this->input->post('master_id'));
      if ($this->form_validation->run() === true) {
        //公司名称不能重复
        $is_exist_agency = $this->agency_model->count_by("status = 1 and company_id = $company_id and name = '{$name}'");
        if ($is_exist_agency) //存在此公司
        {
          $data_view['mess_error'] = '门店名称已存在';
        } else {
          $addResult = $this->agency_model->add_agency($dist_id, $street_id,
            $name, $telno, $address, $company_id, 0, 1, '', 0, $agency_type, $master_id);
          //添加门店成功，初始化门店角色权限
          if (is_int($addResult) && $addResult > 0) {
            $permission_num = $this->agency_model->init_agency_permission($company_id, $addResult);
          }
          $data_view['addResult'] = $addResult;
        }
      } else {
        $data_view['mess_error'] = '带 * 为必填字段';
      }
    } else {
      $this->load->helper('common_load_source_helper');
      $data_view['css'] = load_css('mls/css/v1.0/autocomplete.css');
      //需要加载的JS
      $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
        . 'common/third/swf/swfupload.js,'
        . 'mls/js/v1.0/uploadpic.js,'
        . 'mls/js/v1.0/cooperate_common.js,'
        . 'common/third/jquery-ui-1.9.2.custom.min.js');
    }
    $this->load->view('agency/add', $data_view);
  }

  /**
   * 修改门店信息
   * @param int $agency_id 门店编号
   */
  public function modify($agency_id)
  {
    $this->load->model('district_model');//区属模型类
    $data_view = array();
    //查询这个城市的客户经理数据
    $this->load->model('user_model');
    $masters = $this->user_model->get_user_by_cityid($_SESSION[WEB_AUTH]["city_id"]);
    $data_view['masters'] = $masters;
    $data_view['title'] = '门店管理-修改门店';
    $data_view['conf_where'] = 'index';
    $data_view['modifyResult'] = '';
    $data_view['district'] = $this->district_model->get_district();
    //查找所有总公司信息
    $data_view['company'] = $this->agency_model->get_company_by();
    $submit_flag = $this->input->post('submit_flag');
    if ($submit_flag == 'modify') {
      $this->load->library('form_validation');//表单验证
      $this->form_validation->set_rules('company_id', 'Company ID', 'required');
      $this->form_validation->set_rules('dist_id', 'distinct ID', 'required');
      //$this->form_validation->set_rules('streetid', 'street ID', 'required');
      $this->form_validation->set_rules('name', 'Name', 'required');
      //$this->form_validation->set_rules('telno', 'Telno', 'required');
      //$this->form_validation->set_rules('address', 'Address', 'required');

      //获取参数
      $company_id = intval($this->input->post('company_id'));
      $dist_id = intval($this->input->post('dist_id'));
      $street_id = intval($this->input->post('streetid'));
      $name = trim($this->input->post('name'));
      $telno = trim($this->input->post('telno'));
      $address = trim($this->input->post('address'));
      $agency_type = intval($this->input->post('agency_type'));
      $master_id = intval($this->input->post('master_id'));
      $old_master_id = intval($this->input->post('old_master_id'));
      if ($this->form_validation->run() === true) {
        //门店名称不能重复
        $is_exist_agency = $this->agency_model->count_by("status = 1 and company_id = $company_id and id <> $agency_id and name = '{$name}'");
        if ($is_exist_agency) //存在此门店
        {
          $data_view['mess_error'] = '门店名称已存在';
        } else {
          $modifyResult = $this->agency_model->update_agency($agency_id, $dist_id,
            $street_id, $name, $telno, $address, $company_id, $init, 1, $agency_type, $master_id);
          $data_view['modifyResult'] = $modifyResult;

          if ($master_id != $old_master_id) //修改了客户经理，把门店下的经纪人的客户经理刷了
          {
            $this->load->model('broker_info_model');
            $this->broker_info_model->update_master_by_agency_id($agency_id, $master_id);
          }
        }

      } else {
        $data_view['mess_error'] = '带 * 为必填字段';
      }
    } else {
      $this->load->helper('common_load_source_helper');
      $data_view['css'] = load_css('mls/css/v1.0/autocomplete.css');
      //需要加载的JS
      $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
        . 'common/third/swf/swfupload.js,'
        . 'mls/js/v1.0/uploadpic.js,'
        . 'mls/js/v1.0/cooperate_common.js,'
        . 'common/third/jquery-ui-1.9.2.custom.min.js');
    }
    $agency = $this->agency_model->get_by_id($agency_id);
    // 暂时禁用二维码 by alphabeta 20170405
    //$code_img_url = get_qrcode(MLS_URL . '/' . $_SESSION[WEB_AUTH]["city"] . '/broker_info/agency_house/' . $agency['id'], $_SESSION[WEB_AUTH]["city"]);
    if (!empty($code_img_url)) {
      $data_view['code_img_url'] = $code_img_url;
    } else {
      $data_view['code_img_url'] = '';
    }
    if (is_full_array($agency)) {
      $agency['street_arr'] = $this->district_model->get_street_bydist(
        $agency['dist_id']);
    }
    $company_info = $this->agency_model->get_by_id($agency['company_id']);
    $agency['company_name'] = $company_info['name'];
    $data_view['agency'] = $agency;
    $this->load->view('agency/modify', $data_view);
  }

  /**
   * 删除门店
   * @param int $agency_id 门店编号
   */
  public function delete($agency_id)
  {
    $data_view = array();
    $data_view['deleteResult'] = '';
    $data_view['title'] = '门店管理-删除门店';
    $data_view['conf_where'] = 'index';
    $init_agency_count = $this->agency_model->is_exist_init_agency($agency_id);
    if ($init_agency_count > 0) {
      $data_view['deleteResult'] = 3; //有经纪人挂靠
    } else {
      //查询是否挂靠子门店
      $this->load->model('broker_info_model');
      $child_broker_count = $this->broker_info_model->count_by_agency_id($agency_id);
      if ($child_broker_count > 0) {
        $data_view['deleteResult'] = 2; //有经纪人挂靠
      } else {
        //删除门店
        $deleteResult = $this->agency_model->update_agency_byid(array('status' => 2), $agency_id);
        //1 删除成功 0 删除失败
        //删除门店，与该门店相关联的门店数据范围，设为无效。
        if (1 == $deleteResult) {
          $this->load->model('agency_permission_model');
          //该门店作为主门店的关联数据。
          $agency_per_data = $this->agency_permission_model->get_id_by_main_id(intval($agency_id));
          if (is_full_array($agency_per_data)) {
            foreach ($agency_per_data as $k => $v) {
              $update_data = array('is_effective' => 0);
              $this->agency_permission_model->update_by_id(intval($v['id']), $update_data);
            }
          }
          //该门店作为被关联门店的关联数据。
          $agency_per_data_2 = $this->agency_permission_model->get_id_by_sub_id(intval($agency_id));
          if (is_full_array($agency_per_data_2)) {
            foreach ($agency_per_data_2 as $k => $v) {
              $update_data = array('is_effective' => 0);
              $this->agency_permission_model->update_by_id(intval($v['id']), $update_data);
            }
          }
        }
        $data_view['deleteResult'] = $deleteResult ? 1 : 2;
        //删除门店，与该门店相关联的门店数据范围，设为无效。
        if (1 == $deleteResult) {
          $this->load->model('agency_permission_model');
          //该门店作为主门店的关联数据。
          $agency_per_data = $this->agency_permission_model->get_id_by_main_id(intval($agency_id));
          if (is_full_array($agency_per_data)) {
            foreach ($agency_per_data as $k => $v) {
              $update_data = array('is_effective' => 0);
              $this->agency_permission_model->update_by_id(intval($v['id']), $update_data);
            }
          }
          //该门店作为被关联门店的关联数据。
          $agency_per_data_2 = $this->agency_permission_model->get_id_by_sub_id(intval($agency_id));
          if (is_full_array($agency_per_data_2)) {
            foreach ($agency_per_data_2 as $k => $v) {
              $update_data = array('is_effective' => 0);
              $this->agency_permission_model->update_by_id(intval($v['id']), $update_data);
            }
          }
        }
      }
    }
    $this->load->view('agency/del', $data_view);
  }

  /**
   *
   * @param type $company_id
   */
  public function get_agency_ajax($company_id)
  {
    $agency = $this->agency_model->get_children_by_company_id($company_id);
    $new_agency = array();
    if (is_full_array($agency)) {
      foreach ($agency as $v) {
        $new_agency[] = array('id' => $v['id'], 'name' => $v['name']);
      }
    }
    echo json_encode($new_agency);
  }


  /**
   * 导出报表
   */
  public function exportReport($search_where = 0, $search_value = 0)
  {

    ini_set('memory_limit', '-1');
    //表单提交参数组成的查询条件
    $search_where = $this->input->get('search_where');
    $search_value = $this->input->get('search_value');

    $where = 'status = 1 AND company_id <> 0';
    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
    }
    //判断当前经纪人是否为客户经理
    $this_user_id = intval($_SESSION[WEB_AUTH]['uid']);
    if ($this_user_id > 0) {
      $this_user_data = $this->user_model->getuserByid($this_user_id);
      if (is_full_array($this_user_data[0])) {
        $am_cityid = intval($this_user_data[0]['am_cityid']);
      }
    }
    if (isset($am_cityid) && $am_cityid > 0) {
      $where .= ' AND master_id = "' . $this_user_id . '" ';
    }
    $limit = $this->agency_model->count_by($where);//总统计数量1913

    $list = $this->agency_model->get_all_by($where, 0, $limit);

    $this->load->model('sell_house_model');
    $this->load->model('rent_house_model');

    foreach ($list as $k => $v) {
      //获取各个公司名下有多少门店
      $company_one = $this->agency_model->get_by_id($v['company_id']);
      $list[$k]['company_name'] = $company_one['name'];

      //获取各个门店名下有多少经纪人
      $list[$k]['broker_count'] = $this->agency_model->count_childbroker_by_agency_id($v['id']);


      //获取各个门店名下经纪人发了多少房源
      $list[$k]['sell_count'] = $this->sell_house_model->get_sell_house_num_by_cond(array('agency_id' => $v['id']));
      $list[$k]['rent_count'] = $this->rent_house_model->get_rent_house_num_by_cond(array('agency_id' => $v['id']));
    }

    //区属模型类
    $this->load->model('district_model');
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
    //查询这个城市的客户经理数据
    $this->load->model('user_model');
    $masters = $this->user_model->get_user_by_cityid($_SESSION[WEB_AUTH]["city_id"]);
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '序号');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '公司名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '门店名');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '门店类型');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '区属名');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '板块名');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '联系电话');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '地址');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '经纪人数量');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '出售房源数量');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '出租房源数量');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '开通时间');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '客户经理');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {
      $addtime = $list[$i - 2]['add_time'] > 0 ? date('Y-m-d H:i:s', $list[$i - 2]['add_time']) : '';
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['company_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['name']);
      if ($list[$i - 2]['agency_type'] == 1) {
        $agency_type = '直营';
      } elseif ($list[$i - 2]['agency_type'] == 2) {
        $agency_type = '加盟';
      } elseif ($list[$i - 2]['agency_type'] == 3) {
          $agency_type = '合作';
      } else {
        $agency_type = '待定';
      }
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $agency_type);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $data['district'][$list[$i - 2]['dist_id']]['district']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $data['street'][$list[$i - 2]['street_id']]['streetname']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['telno']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['address']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['broker_count']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['sell_count']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 2]['rent_count']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $addtime);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $masters[$list[$i - 2]['master_id']]['truename']);
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('stat_broker_nums');
    $objPHPExcel->setActiveSheetIndex(0);

    //header("Content-type: text/csv");//重要
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');   //excel 2003
    //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');   //excel 2007
    //header('Content-Disposition: attachment;filename="求购客源.xls"');
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
    $objWriter->save('php://output');
    exit;
  }
}

/* End of file agency.php */
/* Location: ./application/mls_admin/controllers/agency.php */
