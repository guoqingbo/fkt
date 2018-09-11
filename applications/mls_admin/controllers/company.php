<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 添加总公司
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Company extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('agency_model');
    $this->load->helper('user_helper');
  }

  //公司管理页
  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $search_where = $this->input->post('search_where');
    $search_value = $this->input->post('search_value');
    $where = 'company_id = 0 and status = 1';
    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
    }
    //条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value
    );
    //分页开始
    $data_view['count'] = $this->agency_model->count_by($where);
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //公司列表
    $data_view['company'] = $this->agency_model->get_all_by(
      $where, $data_view['offset'], $data_view['pagesize']);

    if ($data_view) {
      //获取各个公司名下有多少门店
      foreach ($data_view['company'] as $k => $v) {
        $data_view['company'][$k]['agency_count'] = $this->agency_model
          ->count_childrea_by_company_id($v['id']);

      }
    }

    //查询公司权限是否初始化成功
    if ($data_view['company']) {
      foreach ($data_view['company'] as $k => $v) {
        $data_view['company'][$k]['is_permission_initialize_success'] = $this->agency_model->is_permission_initialize_success($v['id']);
      }
    }
    $data_view['title'] = '公司管理';
    $data_view['conf_where'] = 'index';
    $this->load->view('company/index', $data_view);
  }

  //添加公司功能
  public function add()
  {
    $this->load->model('district_model');//区属模型类
    $data_view = array();
    $data_view['district'] = $this->district_model->get_district();
    $data_view['title'] = '公司管理-添加公司';
    $data_view['conf_where'] = 'index';
    $data_view['addResult'] = '';
    $submit_flag = $this->input->post('submit_flag');
    if ($submit_flag == 'add') {
      $this->load->library('form_validation');//表单验证
      $this->form_validation->set_rules('dist_id', 'distinct ID', 'required');
      //$this->form_validation->set_rules('streetid', 'street ID', 'required');
      $this->form_validation->set_rules('name', 'Name', 'required');
      //$this->form_validation->set_rules('telno', 'Telno', 'required');
      //$this->form_validation->set_rules('address', 'Address', 'required');
      //获取参数
      $dist_id = intval($this->input->post('dist_id'));
      $street_id = intval($this->input->post('streetid'));
      $name = trim($this->input->post('name'));
      $telno = trim($this->input->post('telno'));
      $address = trim($this->input->post('address'));
      $linkman = trim($this->input->post('linkman'));
      $zip_code = intval($this->input->post('zip_code'));
      $fax = trim($this->input->post('fax'));
      $email = trim($this->input->post('email'));
      $website = trim($this->input->post('website'));

      $agency = trim($this->input->post('agency'));//添加门店为1
      $agency_type = trim($this->input->post('agency_type'));
      $agency_name = trim($this->input->post('agency_name'));

      if ($this->form_validation->run() === true) {
        //公司名称不能重复
        $is_exist_company = $this->agency_model->count_by("status = 1 and company_id = 0 and name = '{$name}'");
        if ($is_exist_company) //存在此公司
        {
          $data_view['mess_error'] = '公司名称已存在';
        } else {
          $company_id = $this->agency_model->add_company($dist_id, $street_id,
            $name, $telno, $address, $linkman, $zip_code, $fax, $email, $website);
          if ($company_id) //公司添加成功后初始化相应的角色和角色权限
          {
            //角色权限
            $permission_num = $this->agency_model->init_company_permission2($company_id);
            if ($permission_num == 11) {
              $data_view['addResult'] = $company_id;
            } else {
              $data_view['addResult'] = "no";
            }
            //考勤基本设置
            /***
             * $this->load->model('attendance_config_model');
             * $datainfo=array(
             * 'company_id' => $company_id,
             * 'start_time' => "09:00:00",
             * 'end_time' => "17:00:00"
             * );
             * $this->attendance_config_model->add_info($datainfo);***/
            if ($agency == 1) {//创建门店
              $data_view['agency_mess_error'] = '';
              if ($agency_name) {
                $agency_addResult = $this->agency_model->add_agency($dist_id, $street_id,
                  $agency_name, $telno, $address, $company_id, 0, 1, '', 0, $agency_type);
                //添加门店成功，初始化门店角色权限
                if (is_int($agency_addResult) && $agency_addResult > 0) {
                  $permission_num = $this->agency_model->init_agency_permission($company_id, $agency_addResult);
                }
                $data_view['agency_mess_error'] = ',门店添加成功';
              } else {
                $data_view['agency_mess_error'] = ',门店添加失败';
              }
            }
          }
        }
      } else {
        $data_view['mess_error'] = '带 * 为必填字段';
      }
    }
    $this->load->view('company/add', $data_view);
  }

  /**
   * 修改公司信息页面
   * @param int $company_id 公司编号
   */
  public function modify($company_id)
  {
    $this->load->model('district_model');//区属模型类
    $data_view = array();
    $data_view['title'] = '公司管理-修改公司';
    $data_view['conf_where'] = 'index';
    $data_view['modifyResult'] = '';
    $data_view['district'] = $this->district_model->get_district();
    $company = $this->agency_model->get_by_id($company_id);
    if (is_full_array($company)) {
      $company['street_arr'] = $this->district_model->get_street_bydist(
        $company['dist_id']);
    }
    $data_view['company'] = $company;

    $this->load->view('company/modify', $data_view);
  }

  //编辑信息处理
  public function edit()
  {
    $this->load->library('form_validation');//表单验证
    $this->form_validation->set_rules('dist_id', 'distinct ID', 'required');
    //$this->form_validation->set_rules('streetid', 'street ID', 'required');
    $this->form_validation->set_rules('name', 'Name', 'required');
    //$this->form_validation->set_rules('telno', 'Telno', 'required');
    //$this->form_validation->set_rules('address', 'Address', 'required');
    //获取参数
    $dist_id = intval($this->input->post('dist_id'));
    $street_id = intval($this->input->post('streetid'));
    $name = trim($this->input->post('name'));
    $telno = trim($this->input->post('telno'));
    $address = trim($this->input->post('address'));
    $linkman = trim($this->input->post('linkman'));
    $zip_code = intval($this->input->post('zip_code'));
    $fax = trim($this->input->post('fax'));
    $email = trim($this->input->post('email'));
    $website = trim($this->input->post('website'));
    $company_id = trim($this->input->post('company_id'));
    if ($this->form_validation->run() === true) {
      //公司名称不能重复
      $is_exist_company = $this->agency_model->count_by("status = 1 and id <> $company_id  and 
					company_id = 0 and name = '{$name}'");
      if ($is_exist_company) //存在此公司
      {
        echo '{"status":"repeat","msg":"公司名称已存在"}';
        exit;
      } else {
        $effected_rows = $this->agency_model->update_company($company_id, $dist_id,
          $street_id, $name, $telno, $address, $linkman, $zip_code, $fax, $email, $website);
        echo '{"status":"success","msg":"修改成功"}';
      }

    } else {
      echo '{"status":"missing","msg":"带 * 为必填字段"}';
      exit;
    }
  }

  /**
   * 删除公司
   * @param int $company_id 公司编号
   */
  public function delete($company_id)
  {
    $data_view = array();
    $data_view['deleteResult'] = '';
    $data_view['title'] = '公司管理-删除公司';
    $data_view['conf_where'] = 'index';
    //查询是否挂靠子公司
    $child_company_count = $this->agency_model->count_childrea_by_company_id($company_id);
    if ($child_company_count > 0) {
      $data_view['deleteResult'] = 2; //有子公司
    } else {
      //删除公司
      $this->agency_model->update_agency_byid(array('status' => 2), $company_id);
      $data_view['deleteResult'] = 1; //删除成功
    }
    $this->load->model('permission_company_role_model');
    $this->permission_company_role_model->delete_by_company_id($company_id);
    $this->load->view('company/del', $data_view);
  }

  /**
   * 根据关键词获取公司信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_company_by_kw()
  {
    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('agency_model');
    $select_fields = array('id', 'name');
    $this->agency_model->set_select_fields($select_fields);
    $company_info = $this->agency_model->auto_companyname($keyword, 10);
    foreach ($company_info as $key => $value) {
      $company_info[$key]['label'] = $value['name'];
    }

    if (empty($company_info)) {
      $company_info[0]['id'] = 0;
      $company_info[0]['label'] = '暂无公司';
    }
    echo json_encode($company_info);
  }

  //上传图片显示页面
  public function uploadphoto($company_id)
  {
    $company = $this->agency_model->get_by_id($company_id);
    $data_view['company'] = $company;
    $this->load->view('company/uploadphoto', $data_view);

  }

  /*
   * 上传图片
   */
  public function upload_logo()
  {
    $filename = $this->input->post('action');
    $fileurl = $this->input->post('fileurl');
    $company_id = $this->input->post('company_id');
    //echo "<script>alert('".$company_id."')</script>";exit;
    if ($filename == 'photofile_add') {
//            $company_id = $this->input->post('company_id');
      //$broker_id = $this->user_arr['broker_id'];
      $update_data = array('photo' => $fileurl);
      $this->agency_model->update_company_logo($fileurl, $company_id);
      echo "<script>window.parent.changePhoto('" . $fileurl . "')</script>";
    } elseif ($filename == 'photofile_modify') {
      $update_data = array('photo' => $fileurl);
      $this->agency_model->update_company_logo($fileurl, $company_id);
      $div_id = $this->input->post('div_id');
//            echo "<script>window.parent.changePhotoTest('" . $fileurl . "')</script>";
      echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
    } else {
      $div_id = $this->input->post('div_id');
      echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
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
    $fileurl = MLS_FILE_SERVER_URL . $this->pic_model->common_upload();
    //echo "<script>alert('".$fileurl."')</script>";exit;
    $company_id = $this->input->post('company_id');
    //echo "<script>alert('".$company_id."')</script>";exit;
    if ($filename == 'photofile_add') {
      $company_id = $this->input->post('company_id');
      //$broker_id = $this->user_arr['broker_id'];
      $update_data = array('photo' => $fileurl);
      $this->agency_model->update_company_logo($fileurl, $company_id);
      echo "<script>window.parent.changePhoto('" . $fileurl . "')</script>";
    } elseif ($filename == 'photofile_modify') {
      $company_id = $this->input->post('company_id');
      $update_data = array('photo' => $fileurl);
      $this->agency_model->update_company_logo($fileurl, $company_id);
      $div_id = $this->input->post('div_id');
      echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
    } else {
      $div_id = $this->input->post('div_id');
      echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
    }

  }

  /**
   * 公司管理--重新初始化权限
   */
  public function update_company_permission()
  {
    $company_id = $this->input->post('id');
    //echo $company_id;die();
    $result = $this->agency_model->update_company_permission($company_id);
    echo json_encode(array('result' => $result));
  }
  /*public function update_company_permission($company_id){
      $data['title'] = '公司管理--重新初始化权限';
      $data['result'] = $this->agency_model->update_company_permission($company_id);
      $this->load->view('company/reinitialize', $data);
  }*/
  /**
   * 导出公司管理报表
   * @author   wang
   */
  public function exportReport($search_where = 0, $search_value = 0)
  {

    ini_set('memory_limit', '-1');
    //表单提交参数组成的查询条件
    $search_where = $this->input->get('search_where');
    $search_value = $this->input->get('search_value');

    $where = 'company_id = 0 and status = 1';

    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
    }


    $limit = $this->agency_model->count_by($where);//总统计数量1913

    $list = $this->agency_model->get_all_by($where, 0, $limit);


    foreach ($list as $k => $v) {
      //获取各个公司名下有多少门店
      $list[$k]['agency_count'] = $this->agency_model
        ->count_childrea_by_company_id($v['id']);

      //获取各个公司名下有多少经纪人
      $list[$k]['broker_count'] = $this->agency_model
        ->count_childbroker_by_company_id($v['id']);


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
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '区属名');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '板块名');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '联系电话');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '联系人');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '地址');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '门店数量');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '经纪人数量');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {


      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['name']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $data['district'][$list[$i - 2]['dist_id']]['district']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $data['street'][$list[$i - 2]['street_id']]['streetname']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['telno']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['linkman']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['address']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['agency_count']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['broker_count']);
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

  //公司权限表，刷入新添的角色数据
  function update_all_company_permission()
  {
    //获得所有的公司
    $this->load->model('permission_company_group_model');
    $all_company = $this->permission_company_group_model->get_all_company_id();
    if (is_full_array($all_company)) {
      foreach ($all_company as $k => $v) {
        $company_id = intval($v['company_id']);
        if (is_int($company_id) && !empty($company_id)) {
          $count = $this->agency_model->update_company_permission($company_id);
          echo "公司id:$company_id ,";
          echo "更新数:$count";
          echo '---<br>';
        }
      }
    }
  }

  //门店权限表，根据系统权限，刷入所有的默认权限数据
  function get_all_agency_permission()
  {
    ini_set("memory_limit", "-1");
    //获得所有的公司
    $this->load->model('permission_company_group_model');
    $this->load->model('agency_model');
    $this->load->model('permission_agency_group_model');
    $this->load->model('permission_system_group_model');

    //获取系统角色权限
    $system_per_data = $this->permission_system_group_model->get_role();
    $all_company = $this->agency_model->get_company_by();
    if (is_full_array($all_company)) {
      foreach ($all_company as $k => $v) {
        $company_id = intval($v['id']);
        if (is_int($company_id) && !empty($company_id)) {
          //根据公司id，获得公司下的所有门店id
          $agency_data = $this->agency_model->get_children_by_company_id($company_id);
          if (is_full_array($agency_data)) {
            $insert_data = array();
            foreach ($agency_data as $key => $value) {
              foreach ($system_per_data as $key2 => $value2) {
                $insert_data = array(
                  'company_id' => $company_id,
                  'agency_id' => intval($value['id']),
                  'system_group_id' => $value2['id'],
                  'func_auth' => $value2['func_auth']
                );

                $insert_result = $this->permission_agency_group_model->insert($insert_data);
                if ($insert_result) {
                  echo '公司id:' . $company_id . '，公司名：' . $v['name'] . '，插入成功---<br>';
                } else {
                  echo '公司id:' . $company_id . '，公司名：' . $v['name'] . '，插入失败----------------------------------------<br>';
                }
              }
            }
          }
        }
      }
    }
  }

  //处理经纪人表role_id
  function deal_broker_info_role_id()
  {
    //获取所有的经纪人role_id，agency_id
    $this->load->model('broker_info_model');
    $this->load->model('permission_company_group_model');
    $this->load->model('permission_agency_group_model');
    $where_str = 'id > 0';
    $select_arr = array('role_id', 'agency_id', 'broker_id');
    $this->broker_info_model->set_select_fields($select_arr);
    $broker_data = $this->broker_info_model->get_all_by($where_str, -1);
    if (is_full_array($broker_data)) {
      foreach ($broker_data as $k => $v) {
        //经纪人id
        $broker_id = $v['broker_id'];
        //门店id
        $agency_id = $v['agency_id'];
        $role_id = $v['role_id'];
        //根据roleid，去公司权限表找到角色
        if (!empty($role_id) && intval($role_id) > 1) {
          $company_role_data = $this->permission_company_group_model->get_one_by(array('id' => $role_id));
          if (is_full_array($company_role_data)) {
            //角色id
            $system_group_id = $company_role_data['system_group_id'];
            //根据门店id，角色id，在门店权限中找到权限id，赋值给经纪人中的role_id
            $where_cond = array(
              'agency_id' => $agency_id,
              'system_group_id' => $system_group_id
            );
            $agency_group_data = $this->permission_agency_group_model->get_one_by($where_cond);

            if (is_full_array($agency_group_data)) {
              $agency_group_id = $agency_group_data['id'];
              $update_broker_data = array('role_id' => intval($agency_group_id));
              $update_success = $this->broker_info_model->update_by_broker_id($update_broker_data, $broker_id);
              if ($update_success) {
                echo "经纪人id:$broker_id ,修改成功<br>";
              } else {
                echo "经纪人id:$broker_id ,未修改<br>";
              }
            }
          } else {
            echo '未在原公司权限表中找到对应的id<br>';
          }
        }
      }
    }
  }

  function insert_role_level()
  {
    //获取所有的经纪人role_id，agency_id
    $this->load->model('broker_info_model');
    $this->load->model('permission_agency_group_model');
    $this->load->model('permission_system_group_model');
    $where_str = 'id > 0';
    $select_arr = array('id', 'role_id');
    $this->broker_info_model->set_select_fields($select_arr);
    $broker_data = $this->broker_info_model->get_all_by($where_str, -1);
    if (is_full_array($broker_data)) {
      foreach ($broker_data as $k => $v) {
        $role_id = $v['role_id'];
        $id = $v['id'];
        //根据roleid，去门店权限表找到角色
        if (!empty($role_id)) {
          $agency_role_data = $this->permission_agency_group_model->get_one_by(array('id' => $role_id));
          if (is_full_array($agency_role_data)) {
            $system_group_id = $agency_role_data['system_group_id'];
            //找到级别
            if (!empty($system_group_id)) {
              $system_role_data = $this->permission_system_group_model->get_one_by(array('id' => intval($system_group_id)));
              if (is_full_array($system_role_data)) {
                $level = intval($system_role_data['level']);
                $update_arr = array('role_level' => $level);
                $update_result = $this->broker_info_model->update_by_id($update_arr, $id);
                if ($update_result) {
                  echo 'id:' . $id . '，等级：' . $level . ',修改成功---<br>';
                } else {
                  echo 'id:' . $id . '，等级：' . $level . ',修改失败---<br>';
                }
              } else {
                echo '根据system_id未在系统权限表中找到级别数据--<br>';
              }
            }
          } else {
            echo '根据role_id未在公司权限表中找到数据--<br>';
          }
        } else {
          echo 'role_id等于0--<br>';
        }
      }
    }
  }

  //根据公司基本设置数据，刷入门店基本设置数据
  function get_agency_base_setting()
  {
    $this->load->model('basic_setting_model');
    $this->load->model('agency_basic_setting_model');
    $all_company_base_data = $this->basic_setting_model->get_all_company_data_not_default();
    if (is_full_array($all_company_base_data)) {
      foreach ($all_company_base_data as $k => $v) {
        $company_id = intval($v['company_id']);
        //根据公司id,获得该公司下的所有门店
        $this->agency_model->set_select_fields(array('company_id', 'id'));
        $children_agency_data = $this->agency_model->get_children_by_company_id($company_id);
        if (is_full_array($children_agency_data)) {
          foreach ($children_agency_data as $key => $value) {
            $insert_data = $v;
            $insert_data['id'] = null;
            $insert_data['agency_id'] = intval($value['id']);
            $insert_result = $this->agency_basic_setting_model->insert($insert_data);
            if ($insert_result) {
              echo '插入成功---<br>';
            }
          }
        }
      }
    }
  }

}

/* End of file company.php */
/* Location: ./application/mls_admin/controllers/company.php */
