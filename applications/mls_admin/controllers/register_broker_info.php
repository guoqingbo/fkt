<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用户详细信息类
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Register_broker_info extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('broker_info_model');
    $this->load->model('auth_review_model');
    $this->load->helper('user_helper');
  }

  //门店管理页
  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $data_view['title'] = '待审核注册经纪人';
    $data_view['conf_where'] = 'index';
    $nowtime = time();

    //设置查询条件
    $search_phone = $this->input->post('search_phone');
    $search_name = $this->input->post('search_name');
    $search_status = $this->input->post('search_status');

    $search_status = $search_status > 0 ? $search_status : $pg > 0 ? $search_status : 1;
    if (!$search_status) {
      $search_status = 0;
      $where = 'r.status <> 0';
    } else if ($search_status == 1) {
      $where = 'r.status = ' . $search_status;
    } else {
      $where = 'r.status = ' . $search_status;
    }
    if ($search_name) {
      $where .= ' and b.truename like ' . "'%$search_name%'";
    }
    if ($search_phone) {
      $where .= ' and b.phone like ' . "'%$search_phone%'";
    }
    //引入经纪人基本类库
    $this->load->model('broker_info_model');

    $call = $this->input->post('call');
    if ($call > 0) {
      $this->broker_info_model->update_register_broker_status($call, 3);
    }

    //设置时间条件
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');

    if ($start_time && $end_time) {
      $start_time_format = strtotime($start_time);
      $end_time_format = strtotime($end_time);
      $where .= ' and b.register_time >= ' . $start_time_format . ' and b.register_time <= ' . $end_time_format;
    }

    //记录搜索过的条件
    $data_view['where_cond'] = array(
      'search_phone' => $search_phone, 'search_name' => $search_name,
      'search_status' => $search_status, 'start_time' => $start_time,
      'end_time' => $end_time
    );
    //分页开始
    $data_view['count'] = 10;
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['count'] = $this->broker_info_model->web_count_by($where);
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //经纪人列表
    $broker_info = $this->broker_info_model->web_get_all_by($where, $data_view['offset'], $data_view['pagesize']);
    //echo '<pre>';print_r($broker_info);die;
    //搜索配置信息
    $data_view['broker_info'] = $broker_info;
    $this->load->view('register_broker_info/index', $data_view);
  }

  /*   public function modify($id,$register_id)
     {
         $data_view = array();
         $data_view['title'] = '用户管理-修改用户';
         $data_view['conf_where'] = 'index';

         $submit_flag = $this->input->post('submit_flag');
         $broker_info = $this->broker_info_model->get_by_id($id);

         //初始化经纪人类
         $this->load->model('broker_model');
         $broker = $this->broker_model->get_by_id($broker_info['broker_id']);
         if ($submit_flag == 'modify')
         {
             //获取参数
             $phone = $this->input->post('phone');
             $password = $this->input->post('password');
             $truename = $this->input->post('truename');
             $birthday = $this->input->post('birthday');
             $qq = $this->input->post('qq');
             $idno = $this->input->post('idno');
             $expiretime = $this->input->post('expiretime') . ' 23:59:59';
             $company_id = $this->input->post('company_id');
             $agency_id = $this->input->post('agency_id');
             $package_id = $this->input->post('package_id');
             $status = $this->input->post('status');
             $email = $this->input->post('email');
             $group_id = $this->input->post('group_id');
             $area_id = $this->input->post('area_id');
             if(!$phone || !$password || !$truename || !$expiretime
                 || !$company_id || !$agency_id){
                 echo '带 * 为必填字段';exit;
             }
             $headpic = $this->input->post('headpic');
             $idnopic = $this->input->post('idnopic');
             $cardpic = $this->input->post('cardpic');
             $agencypic = $this->input->post('agencypic');
             if(($headpic&&!$idnopic) or (!$headpic&&$idnopic)){
                 echo '身份认证照片上传不完全';exit;
             }
             if(($cardpic&&!$agencypic) or (!$cardpic&&$agencypic)){
                 echo '资质认证照片上传不完全';exit;
             }
             if($headpic&&$idnopic){
                 $auth_info = $this->auth_review_model->get_new("type = 1 and broker_id = " . $broker_info['broker_id'],0,1);
                 if(is_full_array($auth_info)){
                     $this->auth_review_model->update_by_id(array('status'=>2,'photo'=>$headpic,'photo2'=>$idnopic,'updatetime'=>time()), $auth_info['id']);
                 }else{
                     $this->auth_review_model->insert(array('broker_id'=>$broker_info['broker_id'],'photo'=>$headpic,'photo2'=>$idnopic,'type'=>1,'status'=>2,'updatetime'=>time()));
                 }
             }

             if($cardpic&&$agencypic){
                 $auth_info1 = $this->auth_review_model->get_new("type = 2 and broker_id = " . $broker_info['broker_id'],0,1);
                 if(is_full_array($auth_info1)){
                     $this->auth_review_model->update_by_id(array('status'=>2,'photo'=>$cardpic,'photo2'=>$agencypic,'updatetime'=>time()), $auth_info1['id']);
                 }else{
                     $this->auth_review_model->insert(array('broker_id'=>$broker_info['broker_id'],'photo'=>$cardpic,'photo2'=>$agencypic,'type'=>2,'status'=>2,'updatetime'=>time()));
                 }
             }

             $broker_update_data = array(
                 'phone' => $phone, 'expiretime' => strtotime($expiretime),
                 'status' => $status
             );
             //判断密码是否相同
             if ($broker['password'] != $password)
             {
                 $broker_update_data['password'] = md5($password);
             }
             $this->broker_model->update_by_id($broker_update_data,
                     $broker_info['broker_id']);
             //获取权限
             $this->load->model('permission_company_role_model');
             $this->permission_company_role_model->set_select_fields(array('id'));
             $role_info = $this->permission_company_role_model->get_by_company_id_package_id($company_id,$package_id);
             $role_id = $role_info['id'];
             $photo = $this->input->post('photopic');
             $broker_info_update_data = array(
                 'phone' => $phone, 'truename' => $truename,
                 'birthday' => $birthday, 'qq' => $qq, 'status' => $status,
                 'company_id' => $company_id, 'idno' => $idno,
                 'agency_id' => $agency_id, 'package_id' => $package_id,
                 'role_id' => $role_id, 'expiretime' => strtotime($expiretime),
                 'email' => $email, 'group_id' => $group_id, 'area_id' => $area_id
             );
             if ($photo)
             {
                 $broker_info_update_data['photo'] = $photo;
             }
             $this->broker_info_model->update_by_id($broker_info_update_data, $id);
             $this->broker_info_model->update_register_broker_status($register_id);
             if($broker_info['agency_id'] != $agency_id ){
                 //出售
                 $this->load->model('sell_house_model');
                 $this->sell_house_model->change_agency_id_by_borker_id($broker_info['broker_id'],$agency_id);
                 //出租
                 $this->load->model('rent_house_model');
                 $this->rent_house_model->change_agency_id_by_borker_id($broker_info['broker_id'],$agency_id);
                 //求购
                 $this->load->model('buy_customer_model');
                 $this->buy_customer_model->update_private_customer_info_by_brokerid($broker_info['broker_id'],$agency_id);
                 //求租
                 $this->load->model('rent_customer_model');
                 $this->rent_customer_model->update_private_customer_info_by_brokerid($broker_info['broker_id'],$agency_id);
             }

             echo '修改成功！';
         }
         else
         {
             //根据分店找总公司id
             if ($broker_info['agency_id'])
             {
                 //查找身份认证 - 资质认证
                 $this->load->model('agency_model');
                 $agency_info = $this->agency_model->get_by_id(
                         $broker_info['agency_id']);
                 $company_children = $this->agency_model->
                         get_children_by_company_id($agency_info['company_id']);
                 $broker_info['company_id'] = $agency_info['company_id'];
                 $company_info = $this->agency_model->get_by_id($broker_info['company_id']);
                 $broker_info['company_name'] = $company_info['name'];
                 $broker_info['agencys'] = $company_children;
             }
             else
             {
                 $broker_info['company_name'] = '';
                 $broker_info['company_id'] = 0;
             }
             //身份认证信息
             $ident_info = $this->auth_review_model->get_new("type = 1 and broker_id = ".$broker_info['broker_id'],0,1);
             if(is_full_array($ident_info)){
                 $data_view['auth_ident_status'] = $ident_info['status'];
                 $data_view['headshots_photo'] = $ident_info['photo'];
                 $data_view['idno_photo'] = $ident_info['photo2'];
             }else{
                 $data_view['auth_ident_status'] = '';
                 $data_view['headshots_photo'] = '';
                 $data_view['idno_photo'] = '';
             }
             //资质认证信息
             $quali_info = $this->auth_review_model->get_new("type = 2 and broker_id = ".$broker_info['broker_id'],0,1);
             if(is_full_array($quali_info)){
                 $data_view['auth_quali_status'] = $quali_info['status'];
                 $data_view['card_photo'] = $quali_info['photo'];
                 $data_view['agency_photo'] = $quali_info['photo2'];
             }else{
                 $data_view['auth_quali_status'] = '';
                 $data_view['card_photo'] = '';
                 $data_view['agency_photo'] = '';
             }
             //获取权限组列表
             $permission_group = $this->broker_info_model->get_permission_group();
             $data_view['permission_group'] = $permission_group;
             $group_arr= $this->broker_info_model->get_system_group_id_by($broker_info['role_id'],$broker_info['broker_id']);
             $data_view['group_arr'] = $group_arr;
             $data_view['register_id'] = $register_id;
             $data_view['register_info'] = $this->broker_info_model->get_register_info($register_id);
             //配置信息
             $data_view['where_config'] = $this->broker_info_model->get_where_config();
             $data_view['broker_info'] = $broker_info;
             $data_view['broker'] = $broker;
             //需要加载的JS
             $this->load->helper('common_load_source_helper');
             $data_view['css'] = load_css('mls/css/v1.0/autocomplete.css');
             //需要加载的JS
             $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
                     . 'common/third/swf/swfupload.js,'
                     . 'mls/js/v1.0/uploadpic.js,'
                     . 'mls/js/v1.0/cooperate_common.js,'
                     . 'common/third/jquery-ui-1.9.2.custom.min.js');
             $this->load->view('broker_info/modify', $data_view);
         }

     }*/

  /*
   * 上传图片
   */
  public function upload_photo()
  {
    $filename = $this->input->post('action');
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
    //echo "<script>alert('".$fileurl."')</script>";exit;

    $div_id = $this->input->post('div_id');
    echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";


  }

  /**
   * 导出待审核注册经纪人数据
   * @author   wang
   */
  public function exportReport($search_phone = 0, $search_name = 0, $search_status = 0, $start_time = 0, $end_time = 0)
  {

    ini_set('memory_limit', '-1');
    //表单提交参数组成的查询条件
    $search_phone = $this->input->get('search_phone', TRUE);
    $search_name = $this->input->get('search_name', TRUE);
    $search_status = $this->input->get('search_status', TRUE);

    //设置时间条件
    $start_time = strtotime($this->input->get('start_time', TRUE));
    $end_time = strtotime($this->input->get('end_time', TRUE));

    if (!$search_status) {
      $search_status = 0;
      $where = 'r.status <> 0';
    } else if ($search_status == 1) {
      $where = 'r.status = ' . $search_status;
    } else {
      $where = 'r.status = ' . $search_status;
    }
    if ($search_name) {
      $where .= ' and b.truename like ' . "'%$search_name%'";
    }
    if ($search_phone) {
      $where .= ' and b.phone like ' . "'%$search_phone%'";
    }
    if ($start_time && $end_time) {
      $where .= ' and b.register_time >= ' . $start_time . ' and b.register_time <= ' . $end_time;
    }
    $limit = $this->broker_info_model->count_data_by_cond($where);

    $brokerlist = $this->broker_info_model->get_data_by_cond($where, 0, $limit);
    //echo '<pre>';print_r($brokerlist);die;
    $list = array();
    if (is_full_array($brokerlist)) {
      foreach ($brokerlist as $key => $value) {
        $list[$key]['phone'] = $value['phone'];
        $list[$key]['truename'] = $value['truename'];
        $list[$key]['corpname'] = $value['corpname'];
        $list[$key]['storename'] = $value['storename'];

        if ($value['status'] == 1) {
          $list[$key]['status'] = '待处理';
        } elseif ($value['status'] == 2) {
          $list[$key]['status'] = '已处理';
        } else {
          $list[$key]['status'] = '已电联';
        }
      }
      $list = array_values($list);
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '手机号码');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '真实姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '公司名称');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '分店名称');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '状态');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['phone']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['corpname']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['storename']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['status']);
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('broker_nums');
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

/* End of file Broker_info.php */
/* Location: ./application/mls_admin/controllers/Broker_info.php */
