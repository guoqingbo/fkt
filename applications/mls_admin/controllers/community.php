<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Community extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('common_load_source_helper');
    $this->load->helper('page_helper');
    $this->load->helper('community_helper');
    $this->load->model('community_model');//楼盘模型类
    $this->load->model('district_model');//区属模型类
    $this->load->model('sell_house_model');//二手房源模型类
    $this->load->model('rent_house_model');//二手房源模型类
    $this->load->model('floor_model');//楼栋模型类
    $this->load->library('form_validation');//表单验证
    $this->load->model('derive_model');//excel导出类
  }

  /**
   * 根据关键词获取楼盘信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_cmtinfo_by_kw()
  {
    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('community_model');
    $select_fields = array('id', 'cmt_name', 'dist_id', 'streetid', 'address', 'averprice', 'status', 'build_date', 'type');
    $this->community_model->set_select_fields($select_fields);
    $cmt_info = $this->community_model->auto_cmtname($keyword, 10);

    foreach ($cmt_info as $key => $value) {
      $cmt_info[$key]['label'] = $value['cmt_name'];
    }

    if (empty($cmt_info)) {
      $cmt_info[0]['id'] = 0;
      $cmt_info[0]['label'] = '暂无小区';
      $cmt_info[0]['averprice'] = 0.00;
      $cmt_info[0]['address'] = '暂无地址';
      $cmt_info[0]['status'] = -1;
      $cmt_info[0]['type'] = -1;
      $cmt_info[0]['districtname'] = '暂无信息';
      $cmt_info[0]['streetname'] = '暂无信息';
    }

    echo json_encode($cmt_info);
  }


  /**
   * 根据关键词获取门店信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_cmtinfo_by_md()
  {
    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('agency_model');
    $select_fields = array('id', 'name', 'status');
    $this->agency_model->set_select_fields($select_fields);
    $cmt_info = $this->agency_model->auto_cmtname($keyword, 10);

    foreach ($cmt_info as $key => $value) {
      $cmt_info[$key]['label'] = $value['name'];
    }

    if (empty($cmt_info)) {
      $cmt_info[0]['id'] = 0;
      $cmt_info[0]['label'] = '暂无门店';
      $cmt_info[0]['averprice'] = 0.00;
      $cmt_info[0]['address'] = '暂无地址';
      $cmt_info[0]['status'] = -1;
      $cmt_info[0]['type'] = -1;
      $cmt_info[0]['districtname'] = '暂无信息';
      $cmt_info[0]['streetname'] = '暂无信息';
    }
    echo json_encode($cmt_info);
  }

  /**
   * 楼盘列表页面
   */
  public function index()
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['city_id'] = $_SESSION['esfdatacenter']['city_id'];
    $data['conf_where'] = 'index';
    $data['district'] = $this->district_model->get_district();
    //楼盘状态
    $status_arr = array(
      '1' => '临时小区',
      '2' => '正式小区'
    );
    $data['status'] = $status_arr;
    //是否热门
    $is_hot_arr = array(
      '1' => '是',
      '2' => '否'
    );
    $data['is_hot'] = $is_hot_arr;
    //筛选条件
    $data['where_cond'] = array();
    $district_id = $this->input->post('district');
    $street_id = $this->input->post('street');
    $status = $this->input->post('status');
    $is_hot = $this->input->post('is_hot');
    if ($district_id) {
      $data['where_cond']['dist_id'] = intval($district_id);
      $street_arr = $this->find_street_bydis_arr($district_id);
      $data['street_arr'] = $street_arr;
    }
    if ($street_id) {
      $data['where_cond']['streetid'] = intval($street_id);
    }
    //物业状态
    if ($status) {
      $data['where_cond']['status'] = intval($status);
    }
    //热门状态
    if ($is_hot) {
        switch ($is_hot){
            case 1:
                $is_hots = 1;
                break;
            case 2:
                $is_hots = 0;
                break;
            default:
                $is_hots = 0;
        }
      $data['where_cond']['is_hot'] = intval($is_hots);
    }
    //楼盘名称、类型模糊查询
    $data['like_code'] = array();

    $condition = $this->input->post('condition');
    $strcode = $this->input->post('strcode');//包含
    $data['strcode'] = $strcode;
    if (!empty($condition) && !empty($strcode)) {
      $data["like_code"][$condition] = $strcode;
    }
    /*$build_type = $this->input->post('build_type');//物业类型
    if(!empty($build_type)){
        $data['like_code']['build_type'] = trim($build_type);
    }*/
    $type = $this->input->post('type');//楼盘类型
    if (!empty($type)) {
      $data['like_code']['type'] = trim($type);
    }
    //分页开始
    $data['user_num'] = $this->community_model->getcommunitynum($data['where_cond'], $data['like_code']);
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['user_num'] ? ceil($data['user_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $data['community'] = $this->community_model->getcommunity($data['where_cond'], $data['like_code'], $data['offset'], $data['pagesize']);
    //print_r($data['community']);
    //初始化所选中的热门下拉列表
    if ($is_hot == 2) {
      $data['where_cond']['is_hot'] = 2;
    }
    //楼盘数据重构
    foreach ($data['community'] as $k => $v) {
      $v['dist_name'] = $this->district_model->get_distname_by_id($v['dist_id']);
      $v['street_name'] = $this->district_model->get_streetname_by_id($v['streetid']);
      $v['image_data'] = $this->community_model->get_cmt_image_by_cmtid($v['id']);
      $v['status'] = !empty($v['status']) ? $status_arr[$v['status']] : '';
      $v['is_hot'] = !empty($v['is_hot']) ? $status_arr[$v['is_hot']] : '';
      $v['type'] = !empty($v['type']) ? $v['type'] : '';
      //print_r($v['type']);
      //当前楼盘的楼栋号
      $floor_data = $this->floor_model->get_floor(array('cmt_id' => $v['id']));
      $floor_str = '';
      if (is_array($floor_data) && !empty($floor_data)) {
        foreach ($floor_data as $key => $value) {
          $floor_str .= $value['num'] . '、';
        }
      }
      $v['floor'] = $floor_str;
      $data['community2'][] = $v;
    }
//    print_r($data['community2']);
    //print_r($data);
    $this->load->view('community/index', $data);
  }

  public function add_dong_unit_door($cmt_id = 0)
  {
    $login_city = !empty($_SESSION[WEB_AUTH]['city']) ? $_SESSION[WEB_AUTH]['city'] : 'sh';
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';
    $cmt_id = intval($cmt_id);
    $data['cmt_id'] = $cmt_id;
    if ($cmt_id > 0) {
      $data['iframe_src'] = MLS_ADMIN_URL . '/community/admin_dong_door_unit/' . $cmt_id . '/' . $login_city;
    }
    $this->load->view('community/dong_unit_door', $data);
  }

  /**
   * 审核列表页面
   */
  public function check_list()
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';
    $data['district'] = $this->district_model->get_district();
    //楼盘状态
    $status_arr = array(
      '2' => '通过',
      '3' => '待审核',
      '4' => '未通过'
    );
    $data['status'] = $status_arr;
    //筛选条件
    $data['where_cond'] = array();
    $district_id = $this->input->post('district');
    $street_id = $this->input->post('street');
    $status = $this->input->post('status');
    $type = $this->input->post('type');
    $creattime = $this->input->post('creattime');
    if ($district_id) {
      $data['where_cond']['dist_id'] = intval($district_id);
      $street_arr = $this->find_street_bydis_arr($district_id);
      $data['street_arr'] = $street_arr;
    }
    if ($street_id) {
      $data['where_cond']['streetid'] = intval($street_id);
    }
    //楼盘状态
    if (is_string($status) && !empty($status)) {
      $data['where_cond']['status'] = intval($status);
    }
    //提交时间
    if (!empty($creattime)) {
      $data['where_cond']['creattime'] = $creattime;
    }

    $where_str = 'id != 0 ';
    if (is_array($data['where_cond']) && !empty($data['where_cond'])) {
      foreach ($data['where_cond'] as $k => $v) {
        if ($k != 'creattime') {
          $where_str .= 'and ' . $k . ' = "' . $v . '"';
        }
      }
    }
    if (!empty($data['where_cond']['creattime'])) {
      if ('1' == $data['where_cond']['creattime']) {
        $_time = time() - 24 * 60 * 60;
        $where_str .= ' and creattime > "' . $_time . '"';
      } else if ('3' == $data['where_cond']['creattime']) {
        $_time = time() - 3 * 24 * 60 * 60;
        $where_str .= ' and creattime > "' . $_time . '"';
      } else if ('7' == $data['where_cond']['creattime']) {
        $_time = time() - 7 * 24 * 60 * 60;
        $where_str .= ' and creattime > "' . $_time . '"';
      }
    }

    //楼盘名称、类型模糊查询
    $data['like_code'] = array();
    $condition = $this->input->post('condition');
    $strcode = trim($this->input->post('strcode'));
    $data['strcode'] = $strcode;
    if (!empty($condition) && !empty($strcode)) {
      $data["like_code"][$condition] = $strcode;
    }
    $build_type = $this->input->post('build_type');
    if (!empty($build_type)) {
      $data['like_code']['build_type'] = trim($build_type);
    }
    //分页开始
    $data['user_num'] = $this->community_model->getcommunitynum($where_str, $data['like_code']);
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['user_num'] ? ceil($data['user_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $data['community'] = $this->community_model->getcommunity($where_str, $data['like_code'], $data['offset'], $data['pagesize']);
    //楼盘数据重构
    foreach ($data['community'] as $k => $v) {
      $v['dist_name'] = $this->district_model->get_distname_by_id($v['dist_id']);
      $v['street_name'] = $this->district_model->get_streetname_by_id($v['streetid']);
      $v['image_data'] = $this->community_model->get_cmt_image_by_cmtid($v['id']);
      //$v['status'] = !empty($v['status'])?$status_arr[$v['status']]:'待审核小区';
      $data['community2'][] = $v;
    }
    $this->load->view('community/check_list', $data);
  }

  public function export()
  {
    //楼盘状态
    $status_arr = array(
      '2' => '通过',
      '3' => '待审核',
      '4' => '未通过'
    );
    //筛选条件
    $data['where_cond'] = array();
    $district_id = $this->input->post('district');
    $street_id = $this->input->post('street');
    $status = $this->input->post('status');
    $type = $this->input->post('type');
    $creattime = $this->input->post('creattime');
    if ($district_id) {
      $data['where_cond']['dist_id'] = intval($district_id);
      $street_arr = $this->find_street_bydis_arr($district_id);
      $data['street_arr'] = $street_arr;
    }
    if ($street_id) {
      $data['where_cond']['streetid'] = intval($street_id);
    }
    //楼盘状态
    if (is_string($status) && !empty($status)) {
      $data['where_cond']['status'] = intval($status);
    }
    //提交时间
    if (!empty($creattime)) {
      $data['where_cond']['creattime'] = $creattime;
    }

    $where_str = 'id != 0 ';
    if (is_array($data['where_cond']) && !empty($data['where_cond'])) {
      foreach ($data['where_cond'] as $k => $v) {
        if ($k != 'creattime') {
          $where_str .= 'and ' . $k . ' = "' . $v . '"';
        }
      }
    }
    if (!empty($data['where_cond']['creattime'])) {
      if ('1' == $data['where_cond']['creattime']) {
        $_time = time() - 24 * 60 * 60;
        $where_str .= ' and creattime > "' . $_time . '"';
      } else if ('3' == $data['where_cond']['creattime']) {
        $_time = time() - 3 * 24 * 60 * 60;
        $where_str .= ' and creattime > "' . $_time . '"';
      } else if ('7' == $data['where_cond']['creattime']) {
        $_time = time() - 7 * 24 * 60 * 60;
        $where_str .= ' and creattime > "' . $_time . '"';
      }
    }

    //楼盘名称、类型模糊查询
    $data['like_code'] = array();
    $condition = $this->input->post('condition');
    $strcode = $this->input->post('strcode');
    $data['strcode'] = $strcode;
    if (!empty($condition) && !empty($strcode)) {
      $data["like_code"][$condition] = $strcode;
    }
    $build_type = $this->input->post('build_type');
    if (!empty($build_type)) {
      $data['like_code']['build_type'] = trim($build_type);
    }
    //分页开始
    $cmt_num = $this->community_model->getcommunitynum($where_str, $data['like_code']);
    $cmt_list = $this->community_model->getcommunity($where_str, $data['like_code'], 0, $cmt_num);
    $list = array();
    if (is_full_array($cmt_list)) {
      foreach ($cmt_list as $k => $v) {
        $list[$k]['id'] = $v['id'];
        $list[$k]['cmt_name'] = $v['cmt_name'];
        $list[$k]['dist_id'] = $this->district_model->get_distname_by_id($v['dist_id']);
        $list[$k]['streetid'] = $this->district_model->get_streetname_by_id($v['streetid']);
        $list[$k]['address'] = $v['address'];
        $list[$k]['status'] = $status_arr[$v['status']];
      }
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '楼盘ID');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '楼盘名称');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '区属');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '板块');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '楼盘地址');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '楼盘状态');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['cmt_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['dist_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['streetid']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['address']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['status']);
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

  /**
   * 导出楼盘数据
   */
  public function derive()
  {
    ini_set('memory_limit', '-1');
    $limit = $this->input->post('limit', TRUE);
    $offset = $this->input->post('offset', TRUE);
    $data['community'] = $this->community_model->getcommunity(array(), array(), $limit, $offset, 'dbback_city');
    $this->derive_model->getExcel($data);
    /*$num = $this->community_model->getcommunitynum();
        $page = $num ? ceil($num / 100) : 0;
        //echo $page;die();

        for($i=1;$i<$page;$i++){
            $start = $i*100;
            $end = $start+100;
            if($end > $num){
                $end = $num;
            }
            $data['community'] = $this->community_model->getcommunity(array(),array(),$start,100,'dbback_city');
            $this->derive_model->getExcel($start,$data);
            //print_r($data);
            //die();
        }
        //echo $start;die();*/
  }

  /**
   * 导入楼盘
   */
  public function import()
  {
    $data = array();
    $this->load->view('community/import', $data);
  }

  public function importExcel()
  {
    //不为空
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
        /*
        //新权限
        //范围（1公司2门店3个人）
        $view_import_house = $this->broker_permission_model->check('127');
        //上传的文件名称
        $broker_info = $this->user_arr;
        */
        $this->load->model('read_model');
        $result = $this->read_model->community_read($data['upload_data']['file_name']);
        unlink($data['upload_data']['full_path']); //删除文件
      } else {
        $result = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css"></head><body style="background:#F2F2F2;"><p class="up_m_b_date_up" style="text-align: center;"><span class="up_e">上传失败</span>，请选择文件上传</p></body></html>';
      }
      echo $result;

    }
  }

  /**
   * 添加楼盘
   */
  public function add()
  {
    $data['title'] = '添加楼盘';
    $data['conf_where'] = 'index';
    $data['district'] = $this->district_model->get_district();
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,mls/js/v1.0/uploadpic.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->form_validation->set_rules('cmt_name', 'Community Name', 'required');
    $this->form_validation->set_rules('dist_id', 'distinct ID', 'required');
    $this->form_validation->set_rules('streetid', 'street ID', 'required');
    $this->form_validation->set_rules('address', 'Address', 'required');
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    $img_arr = $this->input->post('p_filename1');
    $surface_img = $img_arr[0];
    if ('add' == $submit_flag) {
      //物业类型
      $buildtype_arr = $this->input->post('build_type');
      if (!empty($buildtype_arr) && is_array($buildtype_arr)) {
        $buildtype_str = implode('#', $buildtype_arr);
      } else {
        $buildtype_str = '';
      }

      $paramArray = array(
        'cmt_name' => trim($this->input->post('cmt_name')),//楼盘名称
        'name_spell' => trim($this->input->post('name_spell')),//拼音
        'alias' => trim($this->input->post('alias')),//楼盘别名
        'alias_spell' => trim($this->input->post('alias_spell')),//别名拼音
        'type' => intval($this->input->post('type')),//楼盘类型
        'dist_id' => intval($this->input->post('dist_id')),//区属
        'streetid' => intval($this->input->post('streetid')),//板块
        'address' => trim($this->input->post('address')),//楼盘地址
        'build_type' => $buildtype_str,//物业类型
        'build_date' => trim($this->input->post('build_date')),//建筑年代
        'deliver_date' => trim($this->input->post('deliver_date')),//交付日期
        'averprice' => trim($this->input->post('averprice')),//均价
        'buildarea' => intval($this->input->post('buildarea')),//建筑面积
        'coverarea' => intval($this->input->post('coverarea')),//占地面积
        'property_year' => intval($this->input->post('property_year')),//产权年限
        'property_company' => trim($this->input->post('property_company')),//物业公司
        'developers' => trim($this->input->post('developers')),//开发商
        'parking' => trim($this->input->post('parking')),//停车位
        'green_rate' => trim($this->input->post('green_rate')) / 100,//绿化率
        'plot_ratio' => trim($this->input->post('plot_ratio')),//容积率
        'property_fee' => trim($this->input->post('property_fee')),//物业费
        'build_num' => intval($this->input->post('build_num')),//总栋数
        'total_room' => intval($this->input->post('total_room')),//总户数
        'floor_instruction' => trim($this->input->post('floor_instruction')),//楼层状况
        'introduction' => trim($this->input->post('introduction')),//楼盘简介
        'facilities' => trim($this->input->post('facilities')),//周边配套
        'bus_line' => trim($this->input->post('bus_line')),//公交
        'subway' => trim($this->input->post('subway')),//地铁
        'b_map_x' => trim($this->input->post('b_map_x')),//百度X
        'b_map_y' => trim($this->input->post('b_map_y')),//百度Y
        'primary_school' => trim($this->input->post('primary_school')),//对应小学
        'high_school' => trim($this->input->post('high_school')),//对应中学
        'status' => intval($this->input->post('status')),//楼盘状态
        'creattime' => time(),//录入时间
        'ip' => $_SERVER['REMOTE_ADDR'],//录入IP
        'is_upload_pic' => intval($this->input->post('is_upload_pic')), //前台是否显示上传图片按钮
      );
      if (isset($surface_img) && !empty($surface_img)) {
        $paramArray['surface_img'] = $surface_img;//封面图
      }
      //名称拼音首字母
      if (!empty($paramArray['cmt_name'])) {
        $name_spell_s = '';
        for ($i = 0; $i < strlen($paramArray['cmt_name']); $i = $i + 3) {
          $strone = substr($paramArray['cmt_name'], $i, 3);
          $name_spell_s .= getFirstCharter($strone);
        }
        $paramArray['name_spell_s'] = $name_spell_s;
      }
      //别名拼音首字母
      if (!empty($paramArray['alias'])) {
        $alias_spell_s = '';
        for ($i = 0; $i < strlen($paramArray['alias']); $i = $i + 3) {
          $strone = substr($paramArray['alias'], $i, 3);
          $alias_spell_s .= getFirstCharter($strone);
        }
        $paramArray['alias_spell_s'] = $alias_spell_s;
      }

      if ($this->form_validation->run() === true) {
        if (empty($paramArray['cmt_name']) || empty($paramArray['dist_id'])
          || empty($paramArray['streetid']) || empty($paramArray['address'])
        ) {
          die();
        }
        $is_exist = $this->community_model->getcommunity(array('cmt_name' => $paramArray['cmt_name']));
        if (is_array($is_exist) && !empty($is_exist)) {
          $data['mess_error'] = '已存在同名楼盘';
        } else {
          $addResult = $this->community_model->addcommunity($paramArray, 'db_city');//楼盘表数据入库
          if (isset($surface_img) && !empty($surface_img) && is_int($addResult) && !empty($addResult)) {
            $add_img_param = array(
              'cmt_id' => $addResult,
              'image' => $surface_img,
              'is_surface' => 1,
              'creattime' => time(),
              'ip' => $_SERVER['REMOTE_ADDR'],
            );
            $add_img_result = $this->community_model->add_cmt_image($add_img_param);//楼盘图片入库
          }
        }
      } else {
        $data['mess_error'] = '带 * 为必填字段';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('community/add', $data);
  }

  /**
   * 检查楼盘名字
   */
  public function check_cmt()
  {
    $cmt_name = $this->input->get('cmt_name');
    $is_exist = $this->community_model->getcommunity(array('cmt_name' => $cmt_name));
    if (is_array($is_exist) && !empty($is_exist)) {
      echo json_encode(array('result' => 1));
    } else {
      echo json_encode(array('result' => 0));
    }
  }

  /**
   * 修改楼盘
   */
  public function modify($commid)
  {
    $data['title'] = '修改楼盘';
    $data['conf_where'] = 'index';
    $data['district'] = $this->district_model->get_district();
    $this->form_validation->set_rules('cmt_name', 'Community Name', 'required');
    $this->form_validation->set_rules('dist_id', 'distinct ID', 'required');
    $this->form_validation->set_rules('streetid', 'street ID', 'required');
    $this->form_validation->set_rules('address', 'Address', 'required');

    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($commid)) {
      $commData = $this->community_model->get_comm_by_id($commid);
      if (!empty($commData[0]) && is_array($commData[0])) {
        $street_arr = $this->find_street_bydis_arr($commData[0]['dist_id']);
        $commData[0]['street_arr'] = $street_arr;
        $data['comm'] = $commData[0];
      }
    }
    if ('modify' == $submit_flag) {
      //物业类型
      $buildtype_arr = $this->input->post('build_type');
      if (!empty($buildtype_arr) && is_array($buildtype_arr)) {
        $buildtype_str = implode('#', $buildtype_arr);
      } else {
        $buildtype_str = '';
      }
      $paramArray = array(
        'cmt_name' => trim($this->input->post('cmt_name')),//楼盘名称
        'name_spell' => trim($this->input->post('name_spell')),//拼音
        'alias' => trim($this->input->post('alias')),//楼盘别名
        'alias_spell' => trim($this->input->post('alias_spell')),//别名拼音
        'type' => intval($this->input->post('type')),//楼盘类型
        'dist_id' => intval($this->input->post('dist_id')),//区属
        'streetid' => intval($this->input->post('streetid')),//板块
        'address' => trim($this->input->post('address')),//楼盘地址
        'build_type' => $buildtype_str,//物业类型
        'build_date' => trim($this->input->post('build_date')),//建筑年代
        'deliver_date' => trim($this->input->post('deliver_date')),//交付日期
        'averprice' => trim($this->input->post('averprice')),//均价
        'buildarea' => intval($this->input->post('buildarea')),//建筑面积
        'coverarea' => intval($this->input->post('coverarea')),//占地面积
        'property_year' => intval($this->input->post('property_year')),//产权年限
        'property_company' => trim($this->input->post('property_company')),//物业公司
        'developers' => trim($this->input->post('developers')),//开发商
        'parking' => trim($this->input->post('parking')),//停车位
        'green_rate' => trim($this->input->post('green_rate')) / 100,//绿化率
        'plot_ratio' => trim($this->input->post('plot_ratio')),//容积率
        'property_fee' => trim($this->input->post('property_fee')),//物业费
        'build_num' => intval($this->input->post('build_num')),//总栋数
        'total_room' => intval($this->input->post('total_room')),//总户数
        'floor_instruction' => trim($this->input->post('floor_instruction')),//楼层状况
        'introduction' => trim($this->input->post('introduction')),//楼盘简介
        'facilities' => trim($this->input->post('facilities')),//周边配套
        'bus_line' => trim($this->input->post('bus_line')),//公交
        'subway' => trim($this->input->post('subway')),//地铁
        'b_map_x' => trim($this->input->post('b_map_x')),//百度X
        'b_map_y' => trim($this->input->post('b_map_y')),//百度Y
        'primary_school' => trim($this->input->post('primary_school')),//对应小学
        'high_school' => trim($this->input->post('high_school')),//对应中学
        'status' => intval($this->input->post('status')),//楼盘状态
        'updatetime' => time(),//更新时间
        'ip' => $_SERVER['REMOTE_ADDR'],//录入IP
        'is_upload_pic' => intval($this->input->post('is_upload_pic')), //前台是否显示上传图片按钮
      );
      //名称拼音首字母
      if (!empty($paramArray['cmt_name'])) {
        $name_spell_s = '';
        for ($i = 0; $i < strlen($paramArray['cmt_name']); $i = $i + 3) {
          $strone = substr($paramArray['cmt_name'], $i, 3);
          $name_spell_s .= getFirstCharter($strone);
        }
        $paramArray['name_spell_s'] = $name_spell_s;
      }
      //别名拼音首字母
      if (!empty($paramArray['alias'])) {
        $alias_spell_s = '';
        for ($i = 0; $i < strlen($paramArray['alias']); $i = $i + 3) {
          $strone = substr($paramArray['alias'], $i, 3);
          $alias_spell_s .= getFirstCharter($strone);
        }
        $paramArray['alias_spell_s'] = $alias_spell_s;
      }

      if ($this->form_validation->run() === true) {
        if (empty($paramArray['cmt_name']) || empty($paramArray['dist_id'])
          || empty($paramArray['streetid']) || empty($paramArray['address'])
        ) {
          die();
        }
        $modifyResult = $this->community_model->modifycommunity($commid, $paramArray);
        //基本设置修改楼盘名称同步修改相关房源楼盘名
        if ($modifyResult) {
          $sell_house_id_arr = array();
          $rent_house_id_arr = array();
          $old_cmt_name = $commData[0]['cmt_name'];
          $new_cmt_name = $paramArray['cmt_name'];
          if ($old_cmt_name != $new_cmt_name) {
            //出售房源
            $where_cond = 'a.block_id = "' . $commid . '" and b.is_community_modify_house > 0';
            $sell_house_arr = $this->sell_house_model->get_id_by_basic_companyid_cmtid($where_cond);
            if (is_array($sell_house_arr) && !empty($sell_house_arr)) {
              foreach ($sell_house_arr as $k => $v) {
                $sell_house_id_arr[] = $v['id'];
              }
            }
            if (!empty($sell_house_id_arr)) {
              $update_arr = array('block_name' => $new_cmt_name);
              $update_house_result = $this->sell_house_model->update_house_where_in($update_arr, $sell_house_id_arr);
            }
            //出租房源
            $rent_house_arr = $this->rent_house_model->get_id_by_basic_companyid_cmtid($where_cond);
            if (is_array($rent_house_arr) && !empty($rent_house_arr)) {
              foreach ($rent_house_arr as $k => $v) {
                $rent_house_id_arr[] = $v['id'];
              }
            }
            if (!empty($rent_house_id_arr)) {
              $update_arr = array('block_name' => $new_cmt_name);
              $update_house_result = $this->rent_house_model->update_house_where_in($update_arr, $rent_house_id_arr);
            }
          }
        }
      } else {
        $data['mess_error'] = '带 * 为必填字段';
      }
    }
    $this->load->model('city_model');
    $city = $this->city_model->get_by_id($_SESSION['esfdatacenter']['city_id']);

    $data['city_name'] = $city['cityname'];
    $data['lng'] = $city['b_map_x'];
    $data['lat'] = $city['b_map_y'];
    $data['modifyResult'] = $modifyResult;
    $this->load->view('community/modify', $data);
  }

  /**
   * 删除楼盘
   */
  public function del($commid)
  {
    $data['title'] = '删除楼盘';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($commid)) {
      $commData = $this->community_model->delcommunity($commid);
      if ($commData == 1) {
        $delResult = 1; //删除成功
      } else {
        $delResult = 0; //删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('community/del', $data);
  }

  /**
   * 审核楼盘页面展示
   */
  public function check($commid)
  {
    $data['title'] = '楼盘审核';
    $data['conf_where'] = 'index';
    if (!empty($commid)) {
      $commData = $this->community_model->get_comm_by_id($commid);
      if (!empty($commData[0]) && is_array($commData[0])) {
        $data['comm'] = $commData[0];
        $data['comm']['dist_name'] = $this->district_model->get_distname_by_id($data['comm']['dist_id']);
        $data['comm']['street_name'] = $this->district_model->get_streetname_by_id($data['comm']['streetid']);
      }
    }
    $this->load->view('community/check', $data);
  }

  /**
   * 审核楼盘操作
   */
  public function checkaction($method = '', $esta = '')
  {
    //所有勾选的楼盘
    $comm_id = $this->input->post('comm_id');
    //正式楼盘id
    $main_comm_id = $this->input->post('main_comm_id');
    //当前待审核楼盘id
    $this_comm_id = intval($this->input->post('this_comm_id'));

    /* 审核小区 */
    if ("check" == $method && is_int($this_comm_id) && $this_comm_id > 0) {
      switch ($esta) {
        case '0':
          //删除小区信息,更新房源blockid
          $result = $this->community_model->modifycommunity($this_comm_id, array('status' => 4));
          if ($result) {
            //$flg = $this->community_model->add_cmtimage($blockid);
            $flg = true;
            if ($flg) {
              echo "3";
            } else {
              echo "10";
            }
          } else {
            echo "400";
          }
          break;
        case '1':
          //该变小区为临时小区
          $result = $this->community_model->modifycommunity($this_comm_id, array('status' => 1));
          if ($result) {
            //$flg = $this->community_model->add_cmtimage($blockid);
            $flg = true;
            if ($flg) {
              echo "1";
            } else {
              echo "10";
            }
          } else {
            echo "400";
          }
          break;
        case '2':
          //改变小区为正式小区
          $result = $this->community_model->modifycommunity($this_comm_id, array('status' => 2));
          if ($result) {
            //$flg = $this->community_model->add_cmtimage($blockid);
            $flg = true;
            if ($flg) {
              echo "2";
            } else {
              echo "10";
            }
          } else {
            echo "400";
          }
          break;
        default :
          echo "400";
      }

    } else if ("merge" == $method) {
      //从小区
      $bak_id_arr = array_diff($comm_id, $main_comm_id);
      //追加当前待审核小区
      $bak_id_arr[] = $this_comm_id;
      if (is_array($comm_id) && !empty($comm_id)) {
        $main_id = $main_comm_id[0];
        $bak_id = $comm_id[0];
        $merge_result = $this->merge($main_id, $bak_id_arr);
        if ('5' == $merge_result) {
          echo '5';
        } else {
          echo '400';
        }
      } else {
        echo '400';
      }
    }
    exit;
  }


  /**
   * 审核页面查询操作
   */
  public function searchaction($method = '', $param = '', $this_cmt_id = 0)
  {
    if ('search_name' == $method) {
      $comm_arr = $this->community_model->find_like_by_commname(urldecode($param));
      $comm_arr2 = array();
      if (!empty($comm_arr) && is_array($comm_arr)) {
        foreach ($comm_arr as $k => $v) {
          if ($v['status'] == 0 || $v['status'] == 3 || $v['status'] == 1) {
            if ($this_cmt_id != $v['id']) {
              $v['dist_name'] = $this->district_model->get_distname_by_id($v['dist_id']);
              $v['street_name'] = $this->district_model->get_streetname_by_id($v['streetid']);
              $comm_arr2['bak'][] = $v;
            }
          } else if ($v['status'] == 2) {
            $v['dist_name'] = $this->district_model->get_distname_by_id($v['dist_id']);
            $v['street_name'] = $this->district_model->get_streetname_by_id($v['streetid']);
            $comm_arr2['main'][] = $v;
          }
        }
      }
      if (empty($comm_arr2['main'])) {
        $comm_arr2['main'] = 'nodata';
      }
      if (empty($comm_arr2['bak'])) {
        $comm_arr2['bak'] = 'nodata';
      }
      if (!empty($comm_arr2) && is_array($comm_arr2)) {
        $comm_json = json_encode($comm_arr2);
      } else {
        $comm_json = json_encode(array());
      }
    } else if ('search_address' == $method) {
      $comm_arr = $this->community_model->find_like_by_address(urldecode($param));
      $comm_arr2 = array();
      if (!empty($comm_arr) && is_array($comm_arr)) {
        foreach ($comm_arr as $k => $v) {
          if ($v['status'] == 0 || $v['status'] == 3 || $v['status'] == 1) {
            if ($this_cmt_id != $v['id']) {
              $v['dist_name'] = $this->district_model->get_distname_by_id($v['dist_id']);
              $v['street_name'] = $this->district_model->get_streetname_by_id($v['streetid']);
              $comm_arr2['bak'][] = $v;
            }
          } else if ($v['status'] == 2) {
            $v['dist_name'] = $this->district_model->get_distname_by_id($v['dist_id']);
            $v['street_name'] = $this->district_model->get_streetname_by_id($v['streetid']);
            $comm_arr2['main'][] = $v;
          }
        }
      }
      if (empty($comm_arr2['main'])) {
        $comm_arr2['main'] = 'nodata';
      }
      if (empty($comm_arr2['bak'])) {
        $comm_arr2['bak'] = 'nodata';
      }
      if (!empty($comm_arr2) && is_array($comm_arr2)) {
        $comm_json = json_encode($comm_arr2);
      } else {
        $comm_json = json_encode(array());
      }
    }
    echo $comm_json;
  }


  /**
   * 楼盘合并
   */
  public function merge($mainblock_id = '', $bak_id_arr = '')
  {
    $merge_result = '';
    if (!empty($mainblock_id)) {
      $mainblock = $this->community_model->get_comm_by_id($mainblock_id);
    }
    if (is_array($bak_id_arr) && !empty($bak_id_arr)) {
      foreach ($bak_id_arr as $key => $value) {
        $bakblock = $this->community_model->get_comm_by_id($value);
        if (count($mainblock) > 0 && count($bakblock) > 0) {
          $main_block_data = $mainblock[0];
          //1、从小区的房源全部转移到主小区下
          $bak_house = $this->sell_house_model->get_hosue_by_blockid($value);
          $update_arr = array('block_id' => $main_block_data['id'], 'block_name' => $main_block_data['cmt_name'], 'district_id' => $main_block_data['dist_id'], 'street_id' => $main_block_data['streetid'], 'address' => $main_block_data['address']);
          if (!empty($bak_house)) {
            foreach ($bak_house as $k => $v) {
              $where_code = array('id' => $v['id']);
              $update_house_result = $this->sell_house_model->update_house($update_arr, $where_code);
            }
          } else {
            $update_house_result = 1;
          }
          //2、小区图库转移
          $main_img_data = $this->community_model->get_all_cmt_image_by_cmtid(array('cmt_id' => $mainblock_id));
          $bak_img_data = $this->community_model->get_all_cmt_image_by_cmtid(array('cmt_id' => $value));
          if (is_array($bak_img_data) && !empty($bak_img_data)) {
            $update_img_param = array(
              'cmt_id' => $mainblock_id,
              'is_surface' => 1
            );
            foreach ($bak_img_data as $k => $v) {
              $update_img_result = $this->community_model->modify_cmt_image($v['id'], $update_img_param);
            }
          } else {
            $update_img_result = 1;
          }
          //3、更新小区图库统计字段
          //4、从小区删除,从小区资料
          $del_cmt_result = $this->community_model->delcommunity($value);
          if ($update_house_result === 1 && $update_img_result === 1 && $del_cmt_result === 1) {
            $merge_result = '5';
          } else {
            $merge_result = '400';
          }
        } else {
          $merge_result = '400';
        }
      }
    } else {
      $merge_result = '400';
    }

    return $merge_result;
  }


  /**
   * 页面ajax请求根据属区获得对应板块
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
   * 根据属区获得对应板块
   */
  public function find_street_bydis_arr($districtID)
  {
    if (!empty($districtID)) {
      $districtID = intval($districtID);
      $street = $this->district_model->get_street_bydist($districtID);
      return $street;
    } else {
      return FALSE;
    }
  }

  /**
   * 设置热门小区状态
   */
  public function change_community_hot($commid)
  {
      if (!empty($commid)) {
          $commid = intval($commid);
          $commData = $this->community_model->get_comm_by_id($commid);
//          print_r($commData[0]['is_hot']);
          if ($commData[0]['is_hot']){
              $result = $this->community_model->modifycommunity($commid, array('is_hot' => 0));
              $is_hot = 0;
          }else{
              $data['where_cond']['is_hot'] = 1;
              $communityData = $this->community_model->getcommunity($data['where_cond']);
              $communityData_count = count($communityData);
//            print_r( $communityData_count);
              if ($communityData_count >= 3){
                  echo 2;
                  return false;
              }
              $result = $this->community_model->modifycommunity($commid, array('is_hot' => 1));
              $is_hot = 1;
          }
          if ($result){
              echo $is_hot;
              return false;
          }else{
              return false;
          }
      } else {
          return FALSE;
      }
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
