<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * lk
 *
 * 读取excel文件
 *
 * @package         mls-admin
 * @copyright       Copyright (c) 2006 - 2014
 * @since           Version 1.0
 * @author          ccy
 * @filesource
 */
class Derive_model extends MY_Model
{

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('district_model');
  }
  //导出excel文件

  /*  public function getExcel($column,$data){
		$headArr = array("楼盘名称","楼盘别名","楼盘类型","区属","板块","楼盘地址","建筑年代","建筑面积","交付日期","占地面积","物业公司","开发商","停车位","绿化率","容积率","物业费","总栋数","总户数","百度地图");
		$this->load->library ('PHPExcel');
		$this->load->library ('PHPExcel/IOFactory');
		//$this->load->library ('PHPExcel/Writer/Excel5');
		//ini_set("memory_limit", "1024M"); //excel文件大小
		if(empty($data) || !is_array($data)){
			die("data must be a array");
		}

		$date = date("Y_m_d",time());
		$fileName = "message.xls";

		//创建新的PHPExcel对象
		$objPHPExcel = new PHPExcel();
		//$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // 用于 2007 格式
        $objWriter->setOffice2003Compatibility(true);
		//$objProps = $objPHPExcel->getProperties();

		//设置表头
		if($column == 0){
			$key = ord("A");
			foreach($headArr as $v){
				$colum = chr($key);
				$objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
				$key += 1;
			}
		}
		$column = $column +2;
		$objActSheet = $objPHPExcel->getActiveSheet();
		foreach($data as $key => $rows){ //行写入
			$span = ord("A");
			foreach($rows as $keyName=>$value){// 列写入
				$j = chr($span);
				$objActSheet->setCellValue($j.$column, $value);
				$span++;
			}
			$column++;
		}

	   // $fileName = iconv("utf-8", "gb2312", $fileName);
		//重命名表
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		//设置活动单指数到第一个表,所以Excel打开这是第一个表
		$objPHPExcel->setActiveSheetIndex(0);
		//将输出重定向到一个客户端web浏览器(Excel5)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$fileName\"");
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		if(!empty($_GET['excel'])){
			$objWriter->save('php://output'); //文件通过浏览器下载
		}else{
		  $objWriter->save($fileName); //脚本方式运行，保存在当前目录
		}
	}*/
  //$headArr = array("楼盘名称","楼盘别名","楼盘类型","区属","板块","楼盘地址","建筑年代","建筑面积","交付日期","占地面积","物业公司","开发商","停车位","绿化率","容积率","物业费","总栋数","总户数","百度地图");
  public function getExcel($data)
  {
    //print_r($data);die();
    //echo $i;die();
    //调用PHPExcel第三方类库
    $data = $data['community'];
    $this->load->library('PHPExcel.php');
    $this->load->library('PHPExcel/IOFactory');
    ini_set('memory_limit', '-1');
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '楼盘id');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '楼盘名称');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '楼盘别名');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '楼盘类型');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '区属');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '板块');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '楼盘地址');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '建筑年代');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '建筑面积');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '交付日期');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '占地面积');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '物业公司');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '开发商');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '停车位');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', '绿化率');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', '容积率');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', '物业费');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', '总栋数');
    $objPHPExcel->getActiveSheet()->setCellValue('S1', '总户数');
    $objPHPExcel->getActiveSheet()->setCellValue('T1', '百度地图');

    //设置表格的值
    $i = $i + 2;
    // for ($i; $i <= count($data)+1 ; $i++ ){
    for ($i; $i <= count($data) + 1; $i++) {
      $data[$i - 2]['dist_id'] = $this->district_model->get_distname_by_id($data[$i - 2]['dist_id']);
      $data[$i - 2]['streetid'] = $this->district_model->get_streetname_by_id($data[$i - 2]['streetid']);
      //echo $data[$i-2]['type'];die();
      if ($data[$i - 2]['type'] == 1) {
        $data[$i - 2]['type'] = '住宅';
      } elseif ($data[$i - 2]['type'] == 2) {
        $data[$i - 2]['type'] = '别墅';
      } elseif ($data[$i - 2]['type'] == 3) {
        $data[$i - 2]['type'] = '商铺';
      } elseif ($data[$i - 2]['type'] == 4) {
        $data[$i - 2]['type'] = '写字楼';
      } elseif ($data[$i - 2]['type'] == 5) {
        $data[$i - 2]['type'] = '厂房';
      } elseif ($data[$i - 2]['type'] == 6) {
        $data[$i - 2]['type'] = '仓库';
      } elseif ($data[$i - 2]['type'] == 7) {
        $data[$i - 2]['type'] = '车库';
      }
      $data[$i - 2]['green_rate'] = $data[$i - 2]['green_rate'] * 100;
      if ($data[$i - 2]['b_map_x'] > 0 || $data[$i - 2]['b_map_y'] > 0) {
        $data[$i - 2]['b_map'] = '有';
      } else {
        $data[$i - 2]['b_map'] = '无';
      }
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $data[$i - 2]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $data[$i - 2]['cmt_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $data[$i - 2]['alias']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $data[$i - 2]['type']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $data[$i - 2]['dist_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $data[$i - 2]['streetid']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $data[$i - 2]['address']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $data[$i - 2]['build_date'] > 0 ? $data[$i - 2]['build_date'] . '年' : '暂无资料');
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $data[$i - 2]['buildarea'] > 0 ? $data[$i - 2]['buildarea'] . '平方米' : '暂无资料');
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $data[$i - 2]['deliver_date'] > 0 ? $data[$i - 2]['deliver_date'] : '暂无资料');
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $data[$i - 2]['coverarea'] > 0 ? $data[$i - 2]['coverarea'] . '平方米' : '暂无资料');
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $data[$i - 2]['property_company']);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $data[$i - 2]['developers']);
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $data[$i - 2]['parking']);
      $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $data[$i - 2]['green_rate'] > 0 ? $data[$i - 2]['green_rate'] . '%' : '暂无资料');
      $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $data[$i - 2]['plot_ratio'] > 0 ? $data[$i - 2]['plot_ratio'] : '暂无资料');
      $objPHPExcel->getActiveSheet()->setCellValue('Q' . $i, $data[$i - 2]['property_fee'] > 0 ? $data[$i - 2]['property_fee'] . '元/平方米·月' : '暂无资料');
      $objPHPExcel->getActiveSheet()->setCellValue('R' . $i, $data[$i - 2]['build_num'] > 0 ? $data[$i - 2]['build_num'] . '栋' : '暂无资料');
      $objPHPExcel->getActiveSheet()->setCellValue('S' . $i, $data[$i - 2]['total_room'] > 0 ? $data[$i - 2]['total_room'] . '户' : '暂无资料');
      $objPHPExcel->getActiveSheet()->setCellValue('T' . $i, $data[$i - 2]['b_map']);
      /*
                  $objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$data[$i-2]['cmt_name']==''?$data[$i-2]['cmt_name']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data[$i-2]['alias']>0?$data[$i-2]['alias']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data[$i-2]['type'] >0?$data[$i-2]['type']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data[$i-2]['dist_id']>0?$data[$i-2]['dist_id']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data[$i-2]['streetid']>0?$data[$i-2]['streetid']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data[$i-2]['address']>0?$data[$i-2]['address']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data[$i-2]['build_date']>0?$data[$i-2]['build_date'].'年':"");
                  $objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$data[$i-2]['buildarea']>0?$data[$i-2]['buildarea'].'平方米':"");
                  $objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data[$i-2]['deliver_date']>0?$data[$i-2]['deliver_date']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data[$i-2]['coverarea']>0?$data[$i-2]['coverarea'].'平方米':"");
                  $objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data[$i-2]['property_company']>0?$data[$i-2]['property_company']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$data[$i-2]['developers']>0?$data[$i-2]['developers']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$data[$i-2]['parking']>0?$data[$i-2]['parking']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$data[$i-2]['green_rate']>0?$data[$i-2]['green_rate'].'%':"");
                  $objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$data[$i-2]['plot_ratio']>0?$data[$i-2]['plot_ratio']:"");
                  $objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$data[$i-2]['property_fee']>0?$data[$i-2]['property_fee'].'元/平方米·月':"");
                  $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,$data[$i-2]['build_num']>0?$data[$i-2]['build_num'].'户':"");
                  $objPHPExcel->getActiveSheet()->setCellValue('R'.$i,$data[$i-2]['total_room']>0?$data[$i-2]['total_room'].'户':"");
                  $objPHPExcel->getActiveSheet()->setCellValue('S'.$i,$data[$i-2]['b_map']>0?$data[$i-2]['b_map']:"");
                  */
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
  }


  public function getExcel_effect($data)
  {
    //print_r($data);die();
    //echo $i;die();
    //调用PHPExcel第三方类库
    $data = $data['effect'];//print_r($data);die();
    $this->load->library('PHPExcel.php');
    $this->load->library('PHPExcel/IOFactory');
    ini_set('memory_limit', '-1');
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '');

    $objPHPExcel->getActiveSheet()->setCellValue('A2', '序号');
    $objPHPExcel->getActiveSheet()->setCellValue('B2', '城市');
    $objPHPExcel->getActiveSheet()->setCellValue('C2', '合同编号');
    $objPHPExcel->getActiveSheet()->setCellValue('D2', '公司');
    $objPHPExcel->getActiveSheet()->setCellValue('E2', '门店');
    $objPHPExcel->getActiveSheet()->setCellValue('F2', '经纪人');
    $objPHPExcel->getActiveSheet()->setCellValue('G2', '手机');
    $objPHPExcel->getActiveSheet()->setCellValue('H2', '公司');
    $objPHPExcel->getActiveSheet()->setCellValue('I2', '门店');
    $objPHPExcel->getActiveSheet()->setCellValue('J2', '经纪人');
    $objPHPExcel->getActiveSheet()->setCellValue('K2', '手机');
    $objPHPExcel->getActiveSheet()->setCellValue('L2', '合作生效时间');

    //设置表格的值
    $i = $i + 3;
    for ($i; $i <= count($data) + 2; $i++) {
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $data[$i - 3]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $data[$i - 3]['cityname']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $data[$i - 3]['order_sn']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, ($data[$i - 3]['broker_id'] == $data[$i - 3]['operate_broker_id']) ? $data[$i - 3]['company_name'] : '');
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, ($data[$i - 3]['broker_id'] == $data[$i - 3]['operate_broker_id']) ? $data[$i - 3]['agency_name'] : '');
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, ($data[$i - 3]['broker_id'] == $data[$i - 3]['operate_broker_id']) ? $data[$i - 3]['broker_name'] : '');
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, ($data[$i - 3]['broker_id'] == $data[$i - 3]['operate_broker_id']) ? $data[$i - 3]['phone'] : '');
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, ($data[$i - 3]['broker_id'] != $data[$i - 3]['operate_broker_id']) ? $data[$i - 3]['company_name'] : '');
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, ($data[$i - 3]['broker_id'] != $data[$i - 3]['operate_broker_id']) ? $data[$i - 3]['agency_name'] : '');
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, ($data[$i - 3]['broker_id'] != $data[$i - 3]['operate_broker_id']) ? $data[$i - 3]['broker_name'] : '');
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, ($data[$i - 3]['broker_id'] != $data[$i - 3]['operate_broker_id']) ? $data[$i - 3]['phone'] : '');
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, date('Y-m-d', $data[$i - 3]['create_time']));
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    $objPHPExcel->getActiveSheet()->setTitle('sell_house_report');
    $objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');
    header("Content-Disposition: attachment;filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
    // print_r($data);exit;
    $objWriter->save('php://output');
  }
}

?>
