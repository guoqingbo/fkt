<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * lk
 *
 * 读取
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 * @filesource
 */
class Read_model extends MY_Model
{

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();

  }

//    读取文件
  public function read($model, $broker_info, $upload, $i)
  {
    $this->load->model($model);
    $filename = 'temp/' . $upload['file_name'];
    $broker_id = intval($broker_info['broker_id']);
    $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    $objReader = IOFactory::createReaderForFile($filename);
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filename);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow();
    $highestColumn = $objWorksheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $excelData = array();
    for ($row = $i; $row <= $highestRow; $row++) {
      for ($col = 0; $col < $highestColumnIndex; $col++) {
        $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
      }
    }

    //print_r($excelData);exit;

    $datas = array();
    foreach ($excelData as $array => $arr) {
      if ($this->$model->checkarr($arr) == true) {
        $datas[] = $arr;
      }
    }
    if (!empty($datas)) {
      $res = array('broker_id' => $broker_id);
      $this->$model->del($res, 'db_city', 'tmp_uploads');
      $data = array('broker_id' => $broker_id,
        'content' => serialize($datas),
        'createtime' => time()
      );
      $id = $this->$model->add_data($data, 'db_city', 'tmp_uploads');
      /*return '<link type="text/css" rel="stylesheet" href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源</p>'
      .'<input type="hidden" id=tmp_id value='.$id.'>';*/
      /*return '<link type="text/css" rel="stylesheet" href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源。</p>'
      .'<input type="hidden" id=tmp_id value='.$id.'>';*/

      return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: #f2f2f2 !important;}</style></head><body style="background: #f2f2f2"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_s">上传成功</span>，共上传' . count($datas) . '条信息。</p>'
      . '<input type="hidden" id=tmp_id value=' . $id . '></body></html>';
    } else {
      /*return '<link type="text/css" rel="stylesheet" href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
      /*return '<link type="text/css" rel="stylesheet" href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
      return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: #f2f2f2 !important;}</style></head><body style="background: #f2f2f2"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，请按照标准模板重新上传</p></body></html>';
    }
  }

}

?>
