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

//    读取文件1
  public function read($model, $broker_info, $upload, $i, $type, $view_import_data)
  {
    $this->load->model($model);
    $filename = $upload['full_path'];
    $broker_id = intval($broker_info['broker_id']);
    $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    $objReader = IOFactory::createReaderForFile($filename);
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filename);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow();
    //算出有效数据总行数
    $valid_num = intval($highestRow) - intval($i) + 1;
    if ($valid_num <= 1000) {
      $highestColumn = $objWorksheet->getHighestColumn();
      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
      $excelData = array();
      for ($row = $i - 1; $row <= $highestRow; $row++) {
        for ($col = 0; $col < $highestColumnIndex; $col++) {
          $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
        }
      }

      //print_r($excelData);exit;
      //获取文件标题数组 $type=1为房源 ，2为客源
      $excelTitle = array();
      if ($type == 1) {
        $excelTitle = $excelData[6];
        unset($excelData[6]);
      } else if ($type == 2) {
        $excelTitle = $excelData[8];
        unset($excelData[8]);
      }
      //print_r($excelTitle);exit;

      $datas = array();
      $datas_fail = array();
      foreach ($excelData as $array => $arr) {
        if ($this->$model->checkarr($arr, $broker_info, $view_import_data) == 'pass') {
          $datas[] = $arr;
        } else {
          $datas_fail[$array] = $this->$model->checkarr($arr, $broker_info, $view_import_data);
        }
      }
      //print_r($datas_fail);exit;
      if (!empty($datas) && empty($datas_fail)) {
        $res = array('broker_id' => $broker_id);
        $this->$model->del($res, 'db_city', 'tmp_uploads');
        $data = array('broker_id' => $broker_id,
          'content' => serialize($datas),
          'createtime' => time()
        );
        $id = $this->$model->add_data($data, 'db_city', 'tmp_uploads');
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源</p>'
        .'<input type="hidden" id=tmp_id value='.$id.'>';*/
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源。</p>'
        .'<input type="hidden" id=tmp_id value='.$id.'>';*/

        return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_s">上传成功</span>，共上传' . count($datas) . '条信息。</p>'
        . '<input type="hidden" id=tmp_id value=' . $id . '></body></html>';
      } else {
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
        $fail_html = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，<a href="javascript:void(0);" onclick="window.parent.see_reason();" color="#227ac6">点击查看失败原因</a></p>';
        if ($type == 1) {
          $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符、楼盘名称在楼盘表中搜索不到、房源标题超过30字、新增楼盘请按照标准填写(区属、板块和地址)。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
        } else if ($type == 2) {
          $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
        }
        foreach ($datas_fail as $key => $vo) {
          $fail_html .= '<tr><td class="up_m_b_date_up" style="text-align: center">第' . $key . '行</td><td class="up_m_b_date_up" style="text-align:left;padding:0px 15px">';
          foreach ($vo as $kk => $vv) {
            foreach ($excelTitle as $k => $v) {
              if ($vv == $k) {
                $fail_html .= '<span class="up_e">' . $v . '</span>,';
              }
            }
          }
          $fail_html = substr($fail_html, 0, -1);
          $fail_html .= '</td></tr>';
        }
        $fail_html .= '</table></body></html>';
        return $fail_html;
      }
    } else {
      return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，一次最多只能导入1000条记录哦！</p></body></html>';
    }

  }


  public function read_you($model, $broker_info, $upload, $i, $type)
  {
    $this->load->model($model);
    $filename = 'tmp/' . $upload['file_name'];
    $broker_id = intval($broker_info['broker_id']);
    $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    $objReader = IOFactory::createReaderForFile($filename);
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filename);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow();
    //算出有效数据总行数
    $valid_num = intval($highestRow) - intval($i) + 1;
    if ($valid_num <= 1000) {
      $highestColumn = $objWorksheet->getHighestColumn();
      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
      $excelData = array();
      for ($row = $i - 1; $row <= $highestRow; $row++) {
        for ($col = 0; $col < $highestColumnIndex; $col++) {
          $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
        }
      }

      //print_r($excelData);exit;
      //获取文件标题数组 $type=1为房源 ，2为客源
      $excelTitle = array();
      if ($type == 1) {
        $excelTitle = $excelData[5];
        unset($excelData[5]);
      } else if ($type == 2) {
        $excelTitle = $excelData[7];
        unset($excelData[7]);
      }
      //print_r($excelTitle);exit;


      $datas = array();
      $datas_fail = array();
      foreach ($excelData as $array => $arr) {
        if ($arr[7] == '已租' || $arr[7] == '已售') {
          $arr[7] = '成交';
        } elseif ($arr[7] == '我租' || $arr[7] == '我售') {
          $arr[7] = '有效';
        } elseif ($arr[7] == '暂缓') {
          $arr[7] = '有效';
        }
        $arr[9] = str_replace('-', '/', $arr[9]);
        if ($arr[12] == '清水') {
          $arr[12] = '毛坯';
        }
        if ($model == 'sell_model') {
          $arr[15] = str_replace('万元', ' ', $arr[15]);
          if (!$arr[21]) {
            $arr[21] = '其他';
          }
          if ($arr[21] == '汽博') {
            $arr[21] = '汽博中心';
          }
        } elseif ($model == 'rent_house_model') {
          $arr[15] = str_replace('元/月', ' ', $arr[15]);
          if (!$arr[20]) {
            $arr[20] = '其他';
          }
          if ($arr[20] == '汽博') {
            $arr[20] = '汽博中心';
          }
        }

        //print_r($arr);exit;
        if ($this->$model->checkarr_you($arr) == 'pass') {
          $datas[] = $arr;
        } else {
          $datas_fail[$array] = $this->$model->checkarr_you($arr);
        }
      }
      //print_r($datas_fail);exit;
      if (!empty($datas) && empty($datas_fail)) {
        $res = array('broker_id' => $broker_id);
        $this->$model->del($res, 'db_city', 'tmp_uploads');
        $data = array('broker_id' => $broker_id,
          'content' => serialize($datas),
          'createtime' => time()
        );
        $id = $this->$model->add_data($data, 'db_city', 'tmp_uploads');
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源</p>'
        .'<input type="hidden" id=tmp_id value='.$id.'>';*/
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源。</p>'
        .'<input type="hidden" id=tmp_id value='.$id.'>';*/

        return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_s">上传成功</span>，共上传' . count($datas) . '条信息。</p>'
        . '<input type="hidden" id=tmp_id value=' . $id . '></body></html>';
      } else {
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
        $fail_html = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，<a href="javascript:void(0);" onclick="window.parent.see_reason();" color="#227ac6">点击查看失败原因</a></p>';
        if ($type == 1) {
          $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符、楼盘名称在楼盘表中搜索不到、房源标题超过30字、新增楼盘请按照标准填写(区属、板块和地址)。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
        } else if ($type == 2) {
          $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
        }
        foreach ($datas_fail as $key => $vo) {
          $fail_html .= '<tr><td class="up_m_b_date_up" style="text-align: center">第' . $key . '行</td><td class="up_m_b_date_up" style="text-align:left;padding:0px 15px">';
          foreach ($vo as $kk => $vv) {
            foreach ($excelTitle as $k => $v) {
              if ($vv == $k) {
                $fail_html .= '<span class="up_e">' . $v . '</span>,';
              }
            }
          }
          $fail_html = substr($fail_html, 0, -1);
          $fail_html .= '</td></tr>';
        }
        $fail_html .= '</table></body></html>';
        return $fail_html;
      }
    } else {
      return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，一次最多只能导入1000条记录哦！</p></body></html>';
    }

  }


    //    读取文件1
    public function read_my($model, $broker_info, $upload, $i, $type, $view_import_data)
    {
        $this->load->model($model);
        $filename = $upload['full_path'];
        $broker_id = intval($broker_info['broker_id']);
        $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
        $objReader = IOFactory::createReaderForFile($filename);
        $objReader->setReadDataOnly(true);
//        $objPHPExcel = new PHPExcel();
        ini_set("memory_limit", "1024M"); // 不够继续加大
        set_time_limit(0);

        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        //算出有效数据总行数
        $valid_num = intval($highestRow) - intval($i) + 1;
        if ($valid_num <= 3000) {
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $excelData = array();
            for ($row = $i - 1; $row <= $highestRow; $row++) {
                for ($col = 0; $col < $highestColumnIndex; $col++) {
                    $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                }
            }

            //print_r($excelData);exit;
            //获取文件标题数组 $type=1为房源 ，2为客源
            $excelTitle = array();
            if ($type == 1) {
                $excelTitle = $excelData[6];
                unset($excelData[2]);
            } else if ($type == 2) {
                $excelTitle = $excelData[8];
                unset($excelData[8]);
            }
            //print_r($excelTitle);exit;

            $datas = array();
            $datas_fail = array();
            foreach ($excelData as $array => $arr) {
                if ($this->$model->guli_checkarr($arr, $broker_info, $view_import_data) == 'pass') {
                    $datas[] = $arr;
                } else {
                    $datas_fail[$array] = $this->$model->checkarr($arr, $broker_info, $view_import_data);
                }
            }
            //print_r($datas_fail);exit;
            if (!empty($datas) && empty($datas_fail)) {
                $res = array('broker_id' => $broker_id);
                $this->$model->del($res, 'db_city', 'tmp_uploads');
                $data = array('broker_id' => $broker_id,
                    'content' => serialize($datas),
                    'createtime' => time()
                );
                $id = $this->$model->add_data($data, 'db_city', 'tmp_uploads');
                /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源</p>'
                .'<input type="hidden" id=tmp_id value='.$id.'>';*/
                /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源。</p>'
                .'<input type="hidden" id=tmp_id value='.$id.'>';*/

                return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_s">上传成功</span>，共上传' . count($datas) . '条信息。</p>'
                    . '<input type="hidden" id=tmp_id value=' . $id . '></body></html>';
            } else {
                /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
                /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
                $fail_html = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，<a href="javascript:void(0);" onclick="window.parent.window.see_reason();" color="#227ac6">点击查看失败原因</a></p>';
                if ($type == 1) {
                    $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符、楼盘名称在楼盘表中搜索不到、房源标题超过30字、新增楼盘请按照标准填写(区属、板块和地址)。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
                } else if ($type == 2) {
                    $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
                }
                foreach ($datas_fail as $key => $vo) {
                    $fail_html .= '<tr><td class="up_m_b_date_up" style="text-align: center">第' . $key . '行</td><td class="up_m_b_date_up" style="text-align:left;padding:0px 15px">';
                    foreach ($vo as $kk => $vv) {
                        foreach ($excelTitle as $k => $v) {
                            if ($vv == $k) {
                                $fail_html .= '<span class="up_e">' . $v . '</span>,';
                            }
                        }
                    }
                    $fail_html = substr($fail_html, 0, -1);
                    $fail_html .= '</td></tr>';
                }
                $fail_html .= '</table></body></html>';
                return $fail_html;
            }
        } else {
            return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，一次最多只能导入1000条记录哦！</p></body></html>';
        }

    }



}

?>
