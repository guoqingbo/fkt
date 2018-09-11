<?php

class UploadFile
{
  /**
   * Description: 文件上传
   * @Copyright: HOUSE365 (c) 2008
   * Author: chiwm
   * Create: 2008-6-11
   * Amendment Record:
   * sampel
   * include_once($strBaseSite."common/uploadFile.php");
   * $uf = new UploadFile("upfile");//upfile为上传空间file的name属性
   * $rtnMSG=$uf->upload();
   * var_dump($uf->getSaveFileURL());//图片绝对url地址
   * var_dump($rtnMSG);//$rtnMSG="success" 文件上传成功
   *
   * <input name="upfile" type="file" size="20">
   */
  var $strInputName;//上传的file输入框name属性
  var $intMaxSize = 204800;//200k //允许上传的最大文件大小(单位：B)
  var $strFileType = "image";//允许上传的文件类型（以|线分割）（image代表所有图片文件，office代表办公文件）
  var $strSaveDir;//文件存放路径（请尽量使用默认值：/database/webroot/upload/+当前年+/+当前月+/+当前日;例如：/database/webroot/upload/2008/06/11/）
  var $strUploadType = "!ftp";//文件上传方式，默认为ftp上传，如果需要以程序方式上传，请设置其值为非“ftp”
  var $isShowAsChinese = false;//是否以中文方式返回错误信息

  var $aryFileType;
  var $ftpConn;
  var $strExtention;//上传文件后缀名
  var $strSavaFileName;//服务器保存上传文件名
  var $strYMDDir;
  var $strRelativeSaveDir;//文件相对路径（相对web或ftp站点根目录）
  var $strSaveFileURL = "";//保存文件的URL地址

  //以ftp方式上传时相关参数
  var $strServerSite;//设置ftp上传服务器（为img1.house365.com中的任意机器）
  //var $strServer="172.17.1.184";//ftp服务器主机
  var $strServer = "127.0.0.1";//ftp服务器主机
  var $intPort = 21;//ftp端口
  var $intTimeout = 90;//ftp超时
  var $strUsername = "imguser";//登录ftp用户名
  var $strPassword = "doucaretrx";//登录ftp密码
  var $hasSetedServer = false;//是否设置了服务器主机

  //以下为生成调整图相关参数
  var $needResizeImage = false;//是否生成调整图

  var $intResizeImageSize = 150;//调整图尺寸
  var $intResizeWidth = 0;//调整图宽度
  var $intResizeHeight = 0;//调整图高度
  var $needResizeCut = false;//设置是否删除调整图多余部分
  var $needResizeReality = true;//设置是否保持调整图逼真不变形（按宽高大比例项缩放）
  var $strResizeImagePrefixion;//缩略图前缀
  var $strResizeImageURL;//缩略图地址
  var $strResizeImageArrURL = array();//多缩略图地址

  //以下为生成后台展示图相关参数 by 沙羡 on:2012-4-24
  //var $needAudit=true;//是否生成调整图
  var $needAudit = true;
  var $intAuditSize = 440;//展示图尺寸
  var $intAuditWidth = 440;//展示图宽度
  var $intAuditHeight = 330;//展示图高度
  var $needAuditCut = true;//设置是否删除展示图多余部分
  var $needAuditReality = true;//设置是否保持展示图逼真不变形（按宽高大比例项缩放）
  var $strAuditPrefixion;//展示图前缀
  var $strAuditURL;//展示图地址
  var $AuditNeedWater = true;


  //以下为生成多调整图相关参数
  var $needResizeImageArr = false;//是否生成调整图

  var $intResizeImageArrSize = "300,200,100";//调整图尺寸


  //以下为添加水印相关
  var $needWatermark = false;//是否添加水印
  var $intWatermarkType = 1;//添加水印类型(1为文字,2为图片)
  var $strWatermarkPosition = "rb";//水印位置 lt左上；lb左下；rt右上；rb右下；ct中上；cb中下；lc左中；rc右中；cc中间；sj随机
  var $strWatermarkString = "FANG100.COM"; //水印字符串
  var $strWatermarkImage = "";//水印图片
  var $intWatermarkSize = 24;//文字水印字体大小
  var $strWatermarkFont = "";//文字水印字体

  //2010-11-28
  var $otherstring = '';

  var $strImageType = "jpg|jpeg|png|pjpeg|gif|bmp|dib|x-png|tif|tiff|wmf|dwg|dxf|svg|svgz|emf|emz";
  var $strOfficeType = "doc|dot|rtf|txt|pwi|psw|pwd|wps|wtf|xls|xlt|csv|xlw|wk4|wk3|wk1|wd1|wks|wq1|dbf|prn|dif|slk|xla|mdb|adp|mda|mdw|mde|ade|dbf|tab|asc|ppt|pot|pps|ppa|rtf|mpp|mpt|vsd|vtx|vss|vsx|vst|vtx|vsw|pdf";
  var $strRarType = "rar|zip";

  //2011-9-1 原图截取
  var $intImageWidth;//原图截取的宽度
  var $intImageHeight;//原图截取的高度
  var $needImageCut;//原图是否截取

  function UploadFile($input_name)
  {
    $this->setWatermarkImage();
    $this->setWatermarkFont();
    $this->setServerSite();
    $this->strInputName = $input_name['filename'];
    $this->strYMDDir = date("Y") . "/" . date("m") . "/" . date("d") . "/";
  }

  //获取上传的file输入框name属性
  function getInputName()
  {
    return $this->strInputName;
  }

  //设置上传的file输入框name属性
  function setInputName($strInputName)
  {
    $this->strInputName = $strInputName;
  }

  //获取允许上传的最大文件大小(单位：KB)
  function getMaxSize()
  {
    return $this->intMaxSize / 1024;
  }

  //设置允许上传的最大文件大小(单位：KB)
  function setMaxSize($intMaxSize = 200)
  {
    $this->intMaxSize = round($intMaxSize) * 1024;
  }

  /*-------------------------------------------------------------------------------------------------------*/
  /* Fisher 2011-9-1 增加，用户原图按一定宽高截取*/
  /*-------------------------------------------------------------------------------------------------------*/
  //设置原图宽度
  function setImageWidth($intImageWidth = 0)
  {
    $this->intImageWidth = $intImageWidth;
  }

  //设置原图高度
  function setImageHeight($intImageHeight = 0)
  {
    $this->intImageHeight = $intImageHeight;
  }

  //设置是否删除原图多余部分
  function setImageCut($needImageCut = false)
  {
    $this->needImageCut = $needImageCut;
  }
  /*-------------------------------------------------------------------------------------------------------*/

  //获取允许上传的文件类型
  function getFileType()
  {
    return $this->strFileType;
  }

  //设置允许上传的文件类型
  function setFileType($strFileType = "image")
  {
    $this->strFileType = $strFileType;
  }

  //获得文件扩展名
  function getExtention()
  {
    if ($this->strExtention == "") {
      $strFileName = $_FILES[$this->strInputName]['name'];
      $this->strExtention = strtolower(preg_replace('/.*\.(.*[^\.].*)*/iU', '\\1', $strFileName));
    }
    return $this->strExtention;
  }

  //设置文件扩展名
  function setExtention($strExtention = "")
  {
    if ($strExtention == "") $strExtention = $_FILES[$this->strInputName][name];
    $this->strExtention = strtolower(preg_replace('/.*\.(.*[^\.].*)*/iU', '\\1', $strExtention));
  }

  //获得上传文件大小
  function getFileSize()
  {
    return $_FILES[$this->strInputName][size];
  }

  //获得上传文件名
  function getFileName()
  {
    return $_FILES[$this->strInputName][name];
  }

  //获得服务器保存上传文件名
  function getSaveFileName()
  {
    return $this->strSavaFileName;
  }

  //获取文件存放地址
  function getSaveDir()
  {
    if ($this->strUploadType == "ftp") {
      $this->ftpConn = @ftp_connect($this->strServer);
      if (!$this->ftpConn) {
        return "connError";
      }
      if (!@ftp_login($this->ftpConn, $this->strUsername, $this->strPassword)) {
        ftp_quit($this->ftpConn); //关闭ftp连接
        return "loginError";
      }
      if (!@ftp_chdir($this->ftpConn, $this->strSaveDir)) {
        $aryDirs = explode("/", substr($this->strSaveDir, 0, strlen($this->strSaveDir)));
        $strDir = "";
        foreach ($aryDirs as $value) {
          $strDir = $value . "/";
          if (!@ftp_chdir($this->ftpConn, $strDir)) {
            if (!@ftp_mkdir($this->ftpConn, $strDir)) {
              ftp_quit($this->ftpConn); //关闭ftp连接
              return "mkdirError";
            }
          }
        }
      }
    } else {
      if (!file_exists($this->strSaveDir)) {
        $aryDirs = explode("/", substr($this->strSaveDir, 0, strlen($this->strSaveDir)));
        $strDir = "";
        foreach ($aryDirs as $value) {
          $strDir .= $value . "/";
          if (!@file_exists($strDir)) {
            if (!@mkdir($strDir, DIR_WRITE_MODE, TRUE)) {
              return "mkdirError";
            }
          }
        }
      }
    }
    return $this->strSaveDir;
  }

  //设置文件存放地址
  function setSaveDir($strSaveDir = "", $flag = 0)
  {
    $strPath = str_replace("\\", "/", UPLOADS);
    if ($strSaveDir == "") {
      if ($this->strUploadType == "ftp") {
        $this->strRelativeSaveDir = "/upload/" . $this->strYMDDir;
        $this->strSaveDir = "/img" . $this->strServerSite . "/upload/" . $this->strYMDDir;
      } else {
        $this->strRelativeSaveDir = "/upload/" . $this->strYMDDir;
        $this->strSaveDir = $strPath . "/upload/" . $this->strYMDDir;
      }
    } else {
      if (substr($strSaveDir, 0, 1) != "/")
        $strSaveDir = "/" . $strSaveDir;
      if (substr($strSaveDir, -1, 1) != "/")
        $strSaveDir .= "/";
      if ($this->strUploadType == "ftp") {
        if ($flag == 0) {
          $this->strRelativeSaveDir = $strSaveDir . $this->strYMDDir;
          $this->strSaveDir = "/img" . $this->strServerSite . $strSaveDir . $this->strYMDDir;
        } else {
          $this->strRelativeSaveDir = $strSaveDir . $this->strYMDDir;
          $this->strSaveDir = $strSaveDir . $this->strYMDDir;
        }
      } else {
        $this->strRelativeSaveDir = $strSaveDir . $this->strYMDDir;
        $this->strSaveDir = $strPath . $strSaveDir . $this->strYMDDir;
      }
    }
    if (substr($this->strSaveDir, -1, 1) != "/")
      $this->strSaveDir .= "/";
    if (substr($this->strRelativeSaveDir, -1, 1) != "/")
      $this->strRelativeSaveDir .= "/";
  }

  //获取保存文件url地址
  function getSaveFileURL()
  {
    return $this->strSaveFileURL;
  }

  //当上传为图片时，获取图片宽度、高度等相关信息
  function getImageInfo()
  {
    $objFile = $_FILES[$this->strInputName];
    $aryImageType = explode("|", $this->strImageType);
    $this->getExtention();
    if (!in_array($this->strExtention, $aryImageType)) {
      return "imageTypeError";
    }
    return getimagesize($objFile[tmp_name]);
  }

  //设置是否显示为中文错误信息
  function setShowAsChinese($isShowAsChinese = false)
  {
    $this->isShowAsChinese = $isShowAsChinese;
  }

  //以下为ftp上传相关方法
  //获取文件上传方式
  function getUploadType()
  {
    if ($this->strUploadType == "ftp")
      return "ftp";
    else
      return "normal";
  }

  //设置文件上传方式（需要以ftp方式上传，设置值为“ftp”）
  function setUploadType($strUploadType = "")
  {
    $this->strUploadType = $strUploadType;
  }

  //获取ftp服务器
  function getServer()
  {
    return $this->strServer;
  }

  //设置ftp服务器
  function setServer($strServer = "")
  {
    $this->strServer = $strServer;
    $this->hasSetedServer = true;
  }

  //获取ftp端口
  function getPort()
  {
    return $this->intPort;
  }

  //设置ftp端口
  function setPort($intPort = 21)
  {
    $this->intPort = $intPort;
  }

  //获取ftp超时
  function getTimeout()
  {
    return $this->intTimeout;
  }

  //设置ftp超时
  function setTimeout($intTimeout = 90)
  {
    $this->intTimeout = $intTimeout;
  }

  //获取登录ftp用户名
  function getUsername()
  {
    return $this->strUsername;
  }

  //设置登录ftp用户名
  function setUsername($strUsername = "")
  {
    $this->strUsername = $strUsername;
    $this->hasSetedServer = true;
  }

  //获取登录ftp密码
  function getPassword()
  {
    return $this->strPassword;
  }

  //设置登录ftp密码
  function setPassword($strPassword = "")
  {
    $this->strPassword = $strPassword;
    $this->hasSetedServer = true;
  }

  //获取ftp上传服务器站
  function getServerSite()
  {
    return $this->strServerSite;
  }

  //设置ftp上传服务器站
  function setServerSite($strServerSite = "")
  {
    if ($strServerSite != "") {
      $this->strServerSite = $strServerSite;
    } elseif ($this->strServerSite == "") {
      $this->strServerSite = rand(11, 30);
    }
    //$this->strServerSite = 1;
    if (!$this->hasSetedServer) {
      switch ($this->strServerSite) {
        case 1:
          $this->strServer = "172.17.1.184";
          $this->strUsername = "imguser";
          $this->strPassword = "doucaretrx";
          break;
        case 11:
        case 12:
        case 13:
        case 14:
        case 15:
          $this->strServer = "172.17.1.169";
          $this->strUsername = "imguser";
          $this->strPassword = "doucaretrx";
          break;
        case 16:
        case 17:
        case 18:
        case 19:
        case 20:
          $this->strServer = "172.17.1.170";
          $this->strUsername = "imguser";
          $this->strPassword = "doucaretrx";
          break;
        case 21:
        case 22:
        case 23:
        case 24:
        case 25:
          $this->strServer = "172.17.1.99";
          $this->strUsername = "imguser";
          $this->strPassword = "doucaretrx";
          break;
        case 26:
        case 27:
        case 28:
        case 29:
        case 30:
          $this->strServer = "172.17.1.98";
          $this->strUsername = "imguser";
          $this->strPassword = "doucaretrx";
          break;
        default:
          $this->strServer = "172.17.1.184";
          $this->strUsername = "imguser";
          $this->strPassword = "doucaretrx";
          break;
      }
    }
  }


  //以下为生成多调整图相关方法
  function getResizeImageArrSize()
  {
    return $temp_arr = explode(",", $this->intResizeImageArrSize);
  }

  function setResizeImageArr($needResizeImageArr = false)
  {
    $this->needResizeImageArr = $needResizeImageArr;
  }

  function setResizeImageArrSize($intResizeImageArrSize = "300,200,100")
  {
    $this->intResizeImageArrSize = $intResizeImageArrSize;
  }

  //获取保存多缩略图片url地址
  function getResizeImageArrURL()
  {
    return $this->strResizeImageArrURL;
  }

  //以下为生成调整图相关方法
  //获取调整图尺寸
  //设置是否生成调整图
  function setResizeImage($needResizeImage = false)
  {
    $this->needResizeImage = $needResizeImage;
  }

  function getResizeImageSize()
  {
    return $this->intResizeImageSize;
  }


  //设置调整图尺寸
  function setResizeImageSize($intResizeImageSize = 150)
  {
    $this->intResizeImageSize = $intResizeImageSize;
  }


  //设置调整图宽度
  function setResizeWidth($intResizeWidth = 0)
  {
    $this->intResizeWidth = $intResizeWidth;
  }

  //设置调整图高度
  function setResizeHeight($intResizeHeight = 0)
  {
    $this->intResizeHeight = $intResizeHeight;
  }

  //设置是否删除调整图多余部分
  function setResizeCut($needResizeCut = false)
  {
    $this->needResizeCut = $needResizeCut;
  }

  //设置是否保持调整图逼真不变形（按宽高大比例项缩放）
  function setResizeReality($needResizeReality = true)
  {
    $this->needResizeReality = $needResizeReality;
  }

  //设置生成调整图名前缀
  function setResizeImagePrefixion($strResizeImagePrefixion = "")
  {
    $this->strResizeImagePrefixion = $strResizeImagePrefixion;
    //设置前缀为空
  }

  //获取保存缩略图片url地址
  function getResizeImageURL()
  {
    return $this->strResizeImageURL;
  }


  //==============后台审核用的缩略图  =============//
  function setAudit($needAudit = false)
  {
    $this->needAudit = $needAudit;
  }

  function getAuditSize()
  {
    return $this->intAuditSize;
  }

  //设置展示图尺寸
  function setAuditSize($intAuditSize = 150)
  {
    $this->intAuditSize = $intAuditSize;
  }

  //设置展示图宽度
  function setAuditWidth($intAuditWidth = 0)
  {
    $this->intAuditWidth = $intAuditWidth;
  }

  //设置展示图高度
  function setAuditHeight($intAuditHeight = 0)
  {
    $this->intAuditHeight = $intAuditHeight;
  }

  //获取保存展示图片url地址
  function getAuditURL()
  {
    return $this->strAuditURL;
  }

  //设置是否删除调整图多余部分
  function setAuditCut($needAuditCut = false)
  {
    $this->needAuditCut = $needAuditCut;
  }

  //设置是否保持调整图逼真不变形（按宽高大比例项缩放）
  function setAuditReality($needAuditReality = true)
  {
    $this->needAuditReality = $needAuditReality;
  }

  //设置展示图是否添加水印
  function setAuditNeedWater($need = false)
  {

    $this->AuditNeedWater = $need;
  }
  //=====================================//


  //以下为添加水印相关
  //是否添加水印
  function setWatermark($needWatermark = false)
  {
    $this->needWatermark = $needWatermark;
  }

  //添加水印类型(1为文字,2为图片)
  function setWatermarkType($intWatermarkType = 1)
  {
    $this->intWatermarkType = $intWatermarkType;
  }

  //设置水印类型(1为文字,2为图片)
  function setWatermarkPosition($strWatermarkPosition = "rb")
  {
    $this->strPosition = $strWatermarkPosition;
  }

  //设置水印字符串
  function setWatermarkString($strWatermarkString = "FANG100.COM")
  {
    $this->strWatermarkString = $strWatermarkString;
  }

  //设置水印图片
  function setWatermarkImage($strWatermarkImage = "")
  {
    if ($strWatermarkImage == "") $strWatermarkImage = BASEPATH . "/logo.png";
    $this->strWatermarkImage = $strWatermarkImage;
  }

  //设置文字水印字体
  function setWatermarkFont($strWatermarkFont = "")
  {
    if ($strWatermarkFont == "") $strWatermarkFont = BASEPATH . "/fonts/msyhbd.ttf";
    $this->strWatermarkFont = $strWatermarkFont;
  }

  //设置文字水印字体大小
  function setWatermarkSize($intWatermarkSize = 24)
  {
    $this->intWatermarkSize = $intWatermarkSize;
  }

  //check
  function checkFile($objFile)
  {
    //check文件是否存在
    if (!is_uploaded_file($objFile['tmp_name'])) {
      if ($objFile[name] != "") return "sysSizeLimit";
      return "noneFile";
    }

    //check文件大小
    if ($objFile['size'] > $this->intMaxSize) {
      return "sizeLimit";
    }
    if ($objFile['size'] == 0) {
      return "sizeZero";
    }

    //文件类型处理
    $this->strFileType = strtolower($this->strFileType);
    $this->strFileType = str_replace("image", $this->strImageType, $this->strFileType);
    $this->strFileType = str_replace("office", $this->strOfficeType, $this->strFileType);
    $this->strFileType = str_replace("rar", $this->strRarType, $this->strFileType);
    $this->aryFileType = explode("|", $this->strFileType);
    //check文件类型
    if (!in_array($this->strExtention, $this->aryFileType)) {
      return "typeLimit";
    }

    //设置文件存放地址
    if ($this->strSaveDir == "") {
      $this->setSaveDir();
    }
    $strSaveDir = $this->getSaveDir();
    if (substr($strSaveDir, -5) == "Error") return $strSaveDir;
  }

  function upload_initial_img($objFile)
  {

    if (!@ftp_chdir($this->ftpConn, $this->strSaveDir . 'initial/')) {
      if (!@ftp_mkdir($this->ftpConn, $this->strSaveDir . 'initial/')) {
        ftp_quit($this->ftpConn); //关闭ftp连接
      }
    }
    if (@ftp_put($this->ftpConn, $this->strSaveDir . 'initial/' . $this->strSavaFileName, $objFile['tmp_name'], FTP_BINARY)) {
      ($this->ftpConn); //关闭ftp连接
    }
  }

  //文件上传
  function upload($pic = '')
  {

    $exten = $this->getExtention();

    $objFile = $_FILES[$this->strInputName];
    $strRtn = $this->checkFile($objFile);
    if ($pic != '') {
      $this->strSavaFileName = $pic;
    } else {
      $this->strSavaFileName = uniqid(time()) . "." . $this->strExtention;
    }

    //先把原图传上去
    $this->upload_initial_img($objFile);

    if ($exten == 'jpg' || $exten == 'png') {
      $file = $_FILES[$this->strInputName]["tmp_name"];
      list($intWidth, $intHeight) = getimagesize($file);//获得上传图片的长宽

      $new_width = $intWidth;
      $new_height = $intHeight;

      if ($this->needImageCut) {
        //图片宽度大于需求最大宽度，设置宽度为需求宽度，设置高度为缩放等比高度
        if ($this->intImageWidth < $intWidth) {
          $new_width = $this->intImageWidth;
          $new_height = round($new_width * $intHeight / $intWidth);
        }
        //图片高度大于需求最大高度，设置宽度为缩放等比宽度，设置高度为缩放等比高度
        if ($this->intImageHeight < $new_height) {
          $new_width = round($intWidth * $this->intImageHeight / $intHeight);
          $new_height = $this->intImageHeight;
        }
      }

      $img = ImageCreateTrueColor($new_width, $new_height);
      $white = imagecolorallocate($img, 255, 255, 255);
      imagecolortransparent($img, $white);

      if ($exten == 'jpg') {
        imagecopyresampled($img, imagecreatefromjpeg($_FILES[$this->strInputName]["tmp_name"]), 0, 0, 0, 0, $new_width, $new_height, $intWidth, $intHeight);
        imagejpeg($img, $_FILES[$this->strInputName]["tmp_name"]);
      } else if ($exten == 'png') {
        //重采样拷贝部分图像并调整大小
        //imagecopyresampled($img,imagecreatefrompng($_FILES[$this->strInputName]["tmp_name"]),0,0,0,0,$new_width,$new_height,$intWidth,$intHeight);
        //以 PNG 格式将图像输出到浏览器或文件
        //imagePNG($img, $_FILES[$this->strInputName]["tmp_name"]);

        $formpng = imagecreatefrompng($_FILES[$this->strInputName]["tmp_name"]);

        imagecopyresampled($img, $formpng, 0, 0, 0, 0, $new_width, $new_height, $intWidth, $intHeight);

        imagepng($img, $_FILES[$this->strInputName]["tmp_name"]);
      }
      imagedestroy($img);
    }

    if ($strRtn != "") {
      if ($this->isShowAsChinese) {
        $strRtn = $this->getChineseReturn($strRtn);
      }
      return $strRtn;
      exit;
    }

    //生成后台审核图片
    if ($this->needAudit) {
      $strRtn = $this->AuditImage($objFile);
      if ($strRtn == "success") {
        $strAuditDir = "audit/";
        if ($this->strUploadType == "ftp") {
          $this->strAuditURL = "http://img" . $this->strServerSite . ".house365.com" . $this->strRelativeSaveDir . $strAuditDir . $this->strAuditPrefixion . $this->strSavaFileName;
        } else {
          $this->strAuditURL = $this->strRelativeSaveDir . $strAuditDir . $this->strAuditPrefixion . $this->strSavaFileName;
        }
      } elseif ($strRtn == "noNeedAudit") {
        $this->strAuditwURL = &$this->strSaveFileURL;
      } else {
        $this->strAuditURL = "";
        if ($this->isShowAsChinese) {
          $strRtn = $this->getChineseReturn($strRtn);
        }
        return $strRtn;
        exit;
      }
    }

    //为图片加水印
    if ($this->needWatermark) {
      $strRtn = $this->watermark($objFile);
      if ($strRtn != "success") {
        if ($this->isShowAsChinese) {
          //获得中文返回提示
          $strRtn = $this->getChineseReturn($strRtn);
        }
        return $strRtn;
        exit;
      }
    }

    //生成缩略图
    if ($this->needResizeImage) {

      $strRtn = $this->resizeImage($objFile);
      if ($strRtn == "success") {
        $strResizeDir = "thumb/";
        if ($this->strUploadType == "ftp")
          $this->strResizeImageURL = "http://img" . $this->strServerSite . ".house365.com" . $this->strRelativeSaveDir .
            $strResizeDir . $this->strResizeImagePrefixion . $this->strSavaFileName;
        else
          $this->strResizeImageURL = $this->strRelativeSaveDir . $strResizeDir . $this->strResizeImagePrefixion . $this->strSavaFileName;
      } elseif ($strRtn == "noNeedResize") {
        $this->strResizeImageURL = &$this->strSaveFileURL;
      } else {
        $this->strResizeImageURL = "";
        if ($this->isShowAsChinese) {
          $strRtn = $this->getChineseReturn($strRtn);
        }
        return $strRtn;
        exit;
      }
    }

////////////////////////////下面用来多生/////////////////////////////////
    if ($this->needResizeImageArr) {
      foreach ($this->getResizeImageArrSize() as $key => $value) {
        $this->setResizeImagePrefixion($value . "_");
        $this->setResizeImageSize($value);
        //生成缩略图
        if ($this->needResizeImage) {
          $strRtn = $this->resizeImage($objFile);
          if ($strRtn == "success") {
            $strResizeDir = "";
            if ($this->strUploadType == "ftp")
              $this->strResizeImageArrURL[] = "http://img" . $this->strServerSite . ".house365.com" . $this->strRelativeSaveDir . $strResizeDir . $this->strResizeImagePrefixion . $this->strSavaFileName;
            else
              $this->strResizeImageArrURL[] = $this->strRelativeSaveDir . $strResizeDir . $this->strResizeImagePrefixion . $this->strSavaFileName;
          } elseif ($strRtn == "noNeedResize")
            $this->strResizeImageArrURL[] = &$this->strSaveFileURL;
          else {
            $this->strResizeImageArrURL[] = "";
            if ($this->isShowAsChinese) {
              $strRtn = $this->getChineseReturn($strRtn);
            }
            return $strRtn;
            exit;
          }
        }

      }
    }
////////////////////////////下面用来多生/////////////////////////////////


    //上传图片
    if ($this->strUploadType == "ftp") {
      $strRtn = $this->ftpUpload($objFile);
      if ($strRtn == "success")
        $this->strSaveFileURL = "http://img" . $this->strServerSite . ".house365.com" . $this->strRelativeSaveDir . $this->strSavaFileName;
      else
        $this->strSaveFileURL = "";
    } else {
      $strRtn = $this->normalUpload($objFile);
      if ($strRtn == "success")
        $this->strSaveFileURL = $this->strRelativeSaveDir . $this->strSavaFileName;
      else
        $this->strSaveFileURL = "";
    }
    /*if($strUploadRtn=="success")
        {
            $conn=new ConnDB("njhouse","NJDOMAIN");
            $strSql="insert into uploadfile(channel,username,uploadtime,savedir,filename,savefilename,fileurl,resizeimage,description) values('".
                ."','".
                ."','".
                ."','".
                ."','".
                ."','".
                ."','".
                ."','".
                ."','".
                ."','".
                ."','".
                ."','".
                ."','".
                ."')";
        }*/
    if ($strRtn != "success" && $this->isShowAsChinese) {
      $strRtn = $this->getChineseReturn($strRtn);
    }
    return $strRtn;
  }

  //房源打印。根据资源图片，产生新的水印图片，并上传服务器。
  //type(1，出售横排；2，出售纵排；3，出租横排；4，出租纵排）
  function house_pic_deal($insert_str_arr = array(), $insert_pic_arr = array(), $house_id, $type = 0)
  {
    if (1 == $type) {
      $img_src = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'mls' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'v1.0' . DIRECTORY_SEPARATOR . 'sell_house_info1_1.jpg';
    } else if (2 == $type) {
      $img_src = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'mls' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'v1.0' . DIRECTORY_SEPARATOR . 'sell_house_info2_1.jpg';
    } else if (3 == $type) {
      $img_src = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'mls' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'v1.0' . DIRECTORY_SEPARATOR . 'rent_house_info1_1.jpg';
    } else if (4 == $type) {
      $img_src = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'mls' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'v1.0' . DIRECTORY_SEPARATOR . 'rent_house_info2_1.jpg';
    } else {
      $strRtn = 'error';
      return $strRtn;
    }
    $objFile['tmp_name'] = $img_src;
    $this->strExtention = 'jpg';
    $this->strSavaFileName = uniqid(time()) . "." . $this->strExtention;
    //为图片加水印(以横排纵排区分)
    if (1 == $type || 3 == $type) {
      $strRtn = $this->watermark2($objFile, $insert_str_arr, $insert_pic_arr, $house_id);
    } else if (2 == $type || 4 == $type) {
      $strRtn = $this->watermark3($objFile, $insert_str_arr, $insert_pic_arr, $house_id);
    } else {
      $strRtn = 'error';
      return $strRtn;
    }
    if ($strRtn != "success") {
      if ($this->isShowAsChinese) {
        //获得中文返回提示
        $strRtn = $this->getChineseReturn($strRtn);
      }
      return $strRtn;
      exit;
    }

    if ($strRtn != "success" && $this->isShowAsChinese) {
      $strRtn = $this->getChineseReturn($strRtn);
    }
    return $strRtn;
  }

  //个人中心，门店打印。根据资源图片，产生新的水印图片，并上传服务器。
  function agency_pic_deal($insert_pic_str = '', $agency_id)
  {
    $img_src = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'mls' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'v1.0' . DIRECTORY_SEPARATOR . 'agency_scode.jpg';
    $objFile['tmp_name'] = $img_src;
    $this->strExtention = 'jpg';
    $this->strSavaFileName = uniqid(time()) . "." . $this->strExtention;
    //为图片加水印
    $strRtn = $this->watermark_agency($objFile, $insert_pic_str, $agency_id);
    if ($strRtn != "success") {
      if ($this->isShowAsChinese) {
        //获得中文返回提示
        $strRtn = $this->getChineseReturn($strRtn);
      }
      return $strRtn;
      exit;
    }

    if ($strRtn != "success" && $this->isShowAsChinese) {
      $strRtn = $this->getChineseReturn($strRtn);
    }
    return $strRtn;
  }

  //普通文件上传
  function normalUpload($objFile)
  {
    if (move_uploaded_file($objFile[tmp_name], $this->strSaveDir . $this->strSavaFileName))
      return "success";
    else
      return "error";
  }

  //以ftp方式文件上传
  function ftpUpload($objFile)
  {
//        var_dump($this->ftpConn);
//        var_dump($this->strSaveDir.$this->strSavaFileName);
//        var_dump($objFile['tmp_name']);
    if (@ftp_put($this->ftpConn, $this->strSaveDir . $this->strSavaFileName, $objFile['tmp_name'], FTP_BINARY)) {
      ftp_quit($this->ftpConn); //关闭ftp连接
      return "success";
    } else {
      ftp_quit($this->ftpConn); //关闭ftp连接
      return "error";
    }
  }

  //调整图片大小
  function resizeImage($objFile)
  {
    $aryImageType = explode("|", $this->strImageType);
    if (!in_array($this->strExtention, $aryImageType)) {
      return "imageTypeError";
    }

    if ($this->strResizeImagePrefixion == "")
      $strResizeDir = "thumb/";
    if ($this->strUploadType == "ftp") {
      list($intWidth, $intHeight) = getimagesize($objFile['tmp_name']);//获得上传图片的长宽

      $intResizeWidth = $this->intResizeWidth;
      $intResizeHeight = $this->intResizeHeight;

      if ($intResizeWidth > 0 && $intResizeHeight <= 0)
        $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
      elseif ($intResizeHeight > 0 && $intResizeWidth <= 0)
        $intResizeWidth = $intWidth * ($intResizeHeight / $intHeight);
      elseif ($intResizeWidth <= 0 && $intResizeHeight <= 0 && $this->intResizeImageSize > 0) {
        if ($intWidth > $intHeight)//规定产生的调整图大小
        {
          $intResizeWidth = $this->intResizeImageSize;
          $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
        } else {
          $intResizeHeight = $this->intResizeImageSize;
          $intResizeWidth = $intWidth * ($intResizeHeight / $intHeight);
        }
      } elseif ($intResizeWidth <= 0) {
        $intResizeWidth = 150;
        $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
      } elseif ($this->needResizeCut) {
        if ($intWidth / $intResizeWidth > $intHeight / $intResizeHeight)
          $intWidth = $intResizeWidth * $intHeight / $intResizeHeight;
        else
          $intHeight = $intResizeHeight * $intWidth / $intResizeWidth;
      } elseif ($this->needResizeReality) {
        if ($intWidth / $intResizeWidth > $intHeight / $intResizeHeight)
          $intResizeHeight = $intHeight * $intResizeWidth / $intWidth;
        else
          $intResizeWidth = $intWidth * $intResizeHeight / $intHeight;
      }

      //check the image size
      if ($intWidth <= $intResizeWidth && $intHeight <= $intResizeHeight) {
        return "noNeedResize";
      }

      $image1 = imagecreatetruecolor($intResizeWidth, $intResizeHeight); //生成一张调整图
      imagealphablending($image1, false);//取消默认的混色模式
      imagesavealpha($image1, true);//设定保存完整的 alpha 通道信息
      $backgroundColor = imagecolorallocatealpha($image1, 255, 255, 255, 127);
      imageFilledRectangle($image1, 0, 0, $intResizeWidth - 1, $intResizeHeight - 1, $backgroundColor);

      $aryImageInfo = getimagesize($objFile['tmp_name'], $aryImageInfo);
      switch ($aryImageInfo[2]) {
        case 1:
          $image2 = imagecreatefromgif($objFile['tmp_name']);//将上传图片赋值给image2
          break;
        case 2:
          $image2 = imagecreatefromjpeg($objFile['tmp_name']);
          break;
        case 3:
          $image2 = imagecreatefrompng($objFile['tmp_name']);
          break;
        case 6:
          $image2 = imagecreatefromwbmp($objFile['tmp_name']);
          break;
        default: {
          ftp_quit($this->ftpConn); //关闭ftp连接
          return "imageTypeError";
        }
      }
      //判断是否图片复制成功
      if (!$image2) {
        ftp_quit($this->ftpConn); //关闭ftp连接
        return "imageTypeError";
      }

      imagecopyresampled($image1, $image2, 0, 0, 0, 0, $intResizeWidth, $intResizeHeight, $intWidth, $intHeight); //全图拷贝

      if (!@ftp_chdir($this->ftpConn, $this->strSaveDir . $strResizeDir)) {
        if (!@ftp_mkdir($this->ftpConn, $this->strSaveDir . $strResizeDir)) {
          ftp_quit($this->ftpConn); //关闭ftp连接
          return "mkdirError";
        }
      }

      switch ($aryImageInfo[2]) {
        case 1:
          //$isOK=@imagegif($image1,"retemp_".$this->strSavaFileName);//保存调整图
          $isOK = @imagepng($image1, "retemp_" . $this->strSavaFileName);//保存调整图
          break;
        case 2:
          $isOK = @imagejpeg($image1, "retemp_" . $this->strSavaFileName);//保存调整图
          break;
        case 3:
          $isOK = @imagepng($image1, "retemp_" . $this->strSavaFileName);//保存调整图
          break;
        case 6:
          $isOK = @imagewbmp($image1, "retemp_" . $this->strSavaFileName);//保存调整图
          break;
        default: {
          return "imageTypeError";
        }
      }
      $isOK = @ftp_put($this->ftpConn, $this->strSaveDir . $strResizeDir . $this->strResizeImagePrefixion . $this->strSavaFileName, "retemp_" . $this->strSavaFileName, FTP_BINARY);
      @unlink("retemp_" . $this->strSavaFileName);
      if ($isOK) {
        return "success";
      } else {
        ftp_quit($this->ftpConn); //关闭ftp连接
        return "error";
      }
    } else {
      if (!@file_exists($objFile[tmp_name])) {
        return "noneFileError";
      }
      list($intWidth, $intHeight) = getimagesize($objFile[tmp_name]);//获得上传图片的长宽

      $intResizeWidth = $this->intResizeWidth;
      $intResizeHeight = $this->intResizeHeight;

      if ($intResizeWidth > 0 && $intResizeHeight <= 0)
        $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
      elseif ($intResizeHeight > 0 && $intResizeWidth <= 0)
        $intResizeWidth = $intWidth * ($intResizeHeight / $intHeight);
      elseif ($intResizeWidth <= 0 && $intResizeHeight <= 0 && $this->intResizeImageSize > 0) {
        if ($intWidth > $intHeight)//规定产生的调整图大小
        {
          $intResizeWidth = $this->intResizeImageSize;
          $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
        } else {
          $intResizeHeight = $this->intResizeImageSize;
          $intResizeWidth = $intWidth * ($intResizeHeight / $intHeight);
        }
      } elseif ($intResizeWidth <= 0) {
        $intResizeWidth = 150;
        $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
      } elseif ($this->needResizeCut) {
        if ($intWidth / $intResizeWidth > $intHeight / $intResizeHeight)
          $intWidth = $intResizeWidth * $intHeight / $intResizeHeight;
        else
          $intHeight = $intResizeHeight * $intWidth / $intResizeWidth;
      } elseif ($this->needResizeReality) {
        if ($intWidth / $intResizeWidth > $intHeight / $intResizeHeight)
          $intResizeHeight = $intHeight * $intResizeWidth / $intWidth;
        else
          $intResizeWidth = $intWidth * $intResizeHeight / $intHeight;
      }

      //check the image size
      if ($intWidth <= $intResizeWidth && $intHeight <= $intResizeHeight) {
        return "noNeedResize";
      }

      $image1 = imagecreatetruecolor($intResizeWidth, $intResizeHeight); //生成一张调整图
      imagealphablending($image1, false);//取消默认的混色模式
      imagesavealpha($image1, true);//设定保存完整的 alpha 通道信息
      $backgroundColor = imagecolorallocatealpha($image1, 255, 255, 255, 127);
      imageFilledRectangle($image1, 0, 0, $intResizeWidth - 1, $intResizeHeight - 1, $backgroundColor);

      $aryImageInfo = getimagesize($objFile[tmp_name], $aryImageInfo);
      switch ($aryImageInfo[2]) {
        case 1:
          $image2 = imagecreatefromgif($objFile['tmp_name']);//将上传图片赋值给image2
          break;
        case 2:
          $image2 = imagecreatefromjpeg($objFile['tmp_name']);
          break;
        case 3:
          $image2 = imagecreatefrompng($objFile['tmp_name']);
          break;
        case 6:
          $image2 = imagecreatefromwbmp($objFile['tmp_name']);
          break;
        default: {
          return "imageTypeError";
        }
      }
      //判断是否图片复制成功
      if (!$image2) {
        return "imageTypeError";
      }

      imagecopyresampled($image1, $image2, 0, 0, 0, 0, $intResizeWidth, $intResizeHeight, $intWidth, $intHeight); //全图拷贝

      if (!@file_exists($this->strSaveDir . $strResizeDir)) {

        if (!@mkdir($this->strSaveDir . $strResizeDir, 0777))
          return "mkdirError";
      }

      switch ($aryImageInfo[2]) {
        case 1:
          //$isOK=@imagegif($image1,$this->strSaveDir.$strResizeDir.$this->strResizeImagePrefixion.$this->strSavaFileName);//保存调整图
          $isOK = @imagepng($image1, $this->strSaveDir . $strResizeDir . $this->strResizeImagePrefixion . $this->strSavaFileName);//保存调整图
          break;
        case 2:
          $isOK = @imagejpeg($image1, $this->strSaveDir . $strResizeDir . $this->strResizeImagePrefixion . $this->strSavaFileName);//保存调整图
          break;
        case 3:
          $isOK = @imagepng($image1, $this->strSaveDir . $strResizeDir . $this->strResizeImagePrefixion . $this->strSavaFileName);//保存调整图
          break;
        case 6:
          $isOK = @imagewbmp($image1, $this->strSaveDir . $strResizeDir . $this->strResizeImagePrefixion . $this->strSavaFileName);//保存调整图
          break;
        default: {
          return "imageTypeError";
        }
      }
      if ($isOK)
        return "success";
      else
        return "error";
    }
  }


  //生成后台审核图片
  function AuditImage($objFile)
  {
    $aryImageType = explode("|", $this->strImageType);
    if (!in_array($this->strExtention, $aryImageType)) {
      return "imageTypeError";
    }

    if ($this->strAuditPrefixion == "") $strResizeDir = "audit/";
    if ($this->strUploadType == "ftp") {
      list($intWidth, $intHeight) = getimagesize($objFile['tmp_name']);//获得上传图片的长宽

      $intResizeWidth = $this->intAuditWidth;
      $intResizeHeight = $this->intAuditHeight;

      if ($intResizeWidth > 0 && $intResizeHeight <= 0) {
        $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
      } elseif ($intResizeHeight > 0 && $intResizeWidth <= 0) {
        $intResizeWidth = $intWidth * ($intResizeHeight / $intHeight);
      } elseif ($intResizeWidth <= 0 && $intResizeHeight <= 0 && $this->intAuditSize > 0) {
        if ($intWidth > $intHeight)//规定产生的调整图大小
        {
          $intResizeWidth = $this->intAuditSize;
          $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
        } else {
          $intResizeHeight = $this->intAuditSize;
          $intResizeWidth = $intWidth * ($intResizeHeight / $intHeight);
        }
      } elseif ($intResizeWidth <= 0) {
        $intResizeWidth = 150;
        $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
      } elseif ($this->needAuditCut) {
        if ($intWidth / $intResizeWidth > $intHeight / $intResizeHeight) {
          $intWidth = $intResizeWidth * $intHeight / $intResizeHeight;
        } else {
          $intHeight = $intResizeHeight * $intWidth / $intResizeWidth;
        }
      } elseif ($this->needAuditReality) {
        if ($intWidth / $intResizeWidth > $intHeight / $intResizeHeight) {
          $intResizeHeight = $intHeight * $intResizeWidth / $intWidth;
        } else {
          $intResizeWidth = $intWidth * $intResizeHeight / $intHeight;
        }
      }

      //check the image size
      if ($intWidth <= $intResizeWidth && $intHeight <= $intResizeHeight) {
        //原图尺寸小于展示图大小依然保存到bigshow文件夹中
        copy($objFile['tmp_name'], "retemp_" . $this->strSavaFileName);
        // 判断是否加水印
        if ($this->AuditNeedWater) {
          $strRtn = $this->watermark(array("tmp_name" => "retemp_" . $this->strSavaFileName));
          if ($strRtn != "success") {
            if ($this->isShowAsChinese) {
              $strRtn = $this->getChineseReturn($strRtn);
            }
            @unlink("retemp_" . $this->strSavaFileName);
            return $strRtn;
            exit;
          }
        }
        //原图尺寸小于展示图大小
        if (!@ftp_chdir($this->ftpConn, $this->strSaveDir . $strResizeDir)) {
          if (!@ftp_mkdir($this->ftpConn, $this->strSaveDir . $strResizeDir)) {
            ftp_quit($this->ftpConn); //关闭ftp连接
            return "mkdirError";
          }
        }
        @ftp_put($this->ftpConn, $this->strSaveDir . $strResizeDir . $this->strAuditPrefixion . $this->strSavaFileName, "retemp_" . $this->strSavaFileName, FTP_BINARY);
        @unlink("retemp_" . $this->strSavaFileName);
        return "success";
      }

      $image1 = imagecreatetruecolor($intResizeWidth, $intResizeHeight); //生成一张调整图
      imagealphablending($image1, false);//取消默认的混色模式
      imagesavealpha($image1, true);//设定保存完整的 alpha 通道信息
      $backgroundColor = imagecolorallocatealpha($image1, 255, 255, 255, 127);
      imageFilledRectangle($image1, 0, 0, $intResizeWidth - 1, $intResizeHeight - 1, $backgroundColor);

      $aryImageInfo = getimagesize($objFile['tmp_name'], $aryImageInfo);
      switch ($aryImageInfo[2]) {
        case 1:
          $image2 = imagecreatefromgif($objFile['tmp_name']);//将上传图片赋值给image2
          break;
        case 2:
          $image2 = imagecreatefromjpeg($objFile['tmp_name']);
          break;
        case 3:
          $image2 = imagecreatefrompng($objFile['tmp_name']);
          break;
        case 6:
          $image2 = imagecreatefromwbmp($objFile['tmp_name']);
          break;
        default:
          ftp_quit($this->ftpConn); //关闭ftp连接
          return "imageTypeError";
          break;
      }
      //判断是否图片复制成功
      if (!$image2) {
        ftp_quit($this->ftpConn); //关闭ftp连接
        return "imageTypeError";
      }

      imagecopyresampled($image1, $image2, 0, 0, 0, 0, $intResizeWidth, $intResizeHeight, $intWidth, $intHeight); //全图拷贝

      if (!@ftp_chdir($this->ftpConn, $this->strSaveDir . $strResizeDir)) {
        if (!@ftp_mkdir($this->ftpConn, $this->strSaveDir . $strResizeDir)) {
          ftp_quit($this->ftpConn); //关闭ftp连接
          return "mkdirError";
        }
      }

      switch ($aryImageInfo[2]) {
        case 1:
          //$isOK=@imagegif($image1,"retemp_".$this->strSavaFileName);//保存调整图
          $isOK = @imagepng($image1, "retemp_" . $this->strSavaFileName);//保存调整图
          break;
        case 2:
          $isOK = @imagejpeg($image1, "retemp_" . $this->strSavaFileName);//保存调整图
          break;
        case 3:
          $isOK = @imagepng($image1, "retemp_" . $this->strSavaFileName);//保存调整图
          break;
        case 6:
          $isOK = @imagewbmp($image1, "retemp_" . $this->strSavaFileName);//保存调整图
          break;
        default:
          return "imageTypeError";
          break;
      }

      // 判断是否加水印
      if ($this->AuditNeedWater) {
        $strRtn = $this->watermark(array("tmp_name" => "retemp_" . $this->strSavaFileName));
        if ($strRtn != "success") {
          if ($this->isShowAsChinese) {
            $strRtn = $this->getChineseReturn($strRtn);
          }
          return $strRtn;
          exit;
        }
      }

      $isOK = @ftp_put($this->ftpConn, $this->strSaveDir . $strResizeDir . $this->strAuditPrefixion . $this->strSavaFileName, "retemp_" . $this->strSavaFileName, FTP_BINARY);
      @unlink("retemp_" . $this->strSavaFileName);
      if ($isOK) {
        return "success";
      } else {
        ftp_quit($this->ftpConn); //关闭ftp连接
        return "error";
      }
    } else {
      if (!@file_exists($objFile['tmp_name'])) {
        return "noneFileError";
      }
      list($intWidth, $intHeight) = getimagesize($objFile['tmp_name']);//获得上传图片的长宽

      $intResizeWidth = $this->intAuditWidth;
      $intResizeHeight = $this->intAuditHeight;

      if ($intResizeWidth > 0 && $intResizeHeight <= 0) {
        $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
      } elseif ($intResizeHeight > 0 && $intResizeWidth <= 0) {
        $intResizeWidth = $intWidth * ($intResizeHeight / $intHeight);
      } elseif ($intResizeWidth <= 0 && $intResizeHeight <= 0 && $this->intAuditSize > 0) {
        if ($intWidth > $intHeight)//规定产生的调整图大小
        {
          $intResizeWidth = $this->intAuditSize;
          $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
        } else {
          $intResizeHeight = $this->intAuditSize;
          $intResizeWidth = $intWidth * ($intResizeHeight / $intHeight);
        }
      } elseif ($intResizeWidth <= 0) {
        $intResizeWidth = 150;
        $intResizeHeight = $intHeight * ($intResizeWidth / $intWidth);
      } elseif ($this->needAuditCut) {
        if ($intWidth / $intResizeWidth > $intHeight / $intResizeHeight) {
          $intWidth = $intResizeWidth * $intHeight / $intResizeHeight;
        } else {
          $intHeight = $intResizeHeight * $intWidth / $intResizeWidth;
        }
      } elseif ($this->needAuditReality) {
        if ($intWidth / $intResizeWidth > $intHeight / $intResizeHeight) {
          $intResizeHeight = $intHeight * $intResizeWidth / $intWidth;
        } else {
          $intResizeWidth = $intWidth * $intResizeHeight / $intHeight;
        }
      }

      //check the image size
      if ($intWidth <= $intResizeWidth && $intHeight <= $intResizeHeight) {
        if (!@file_exists($this->strSaveDir . $strResizeDir)) {
          if (!@mkdir($this->strSaveDir . $strResizeDir))
            return "mkdirError";
        }
        if (!copy($objFile["tmp_name"], $this->strSaveDir . $strResizeDir . $this->strAuditPrefixion . $this->strSavaFileName)) {
          return 'CopyFileError';
        } else {
          return 'success';
        }
      }

      $image1 = imagecreatetruecolor($intResizeWidth, $intResizeHeight); //生成一张调整图
      imagealphablending($image1, false);//取消默认的混色模式
      imagesavealpha($image1, true);//设定保存完整的 alpha 通道信息
      $backgroundColor = imagecolorallocatealpha($image1, 255, 255, 255, 127);
      imageFilledRectangle($image1, 0, 0, $intResizeWidth - 1, $intResizeHeight - 1, $backgroundColor);

      $aryImageInfo = getimagesize($objFile['tmp_name'], $aryImageInfo);
      switch ($aryImageInfo[2]) {
        case 1:
          $image2 = imagecreatefromgif($objFile['tmp_name']);//将上传图片赋值给image2
          break;
        case 2:
          $image2 = imagecreatefromjpeg($objFile['tmp_name']);
          break;
        case 3:
          $image2 = imagecreatefrompng($objFile['tmp_name']);
          break;
        case 6:
          $image2 = imagecreatefromwbmp($objFile['tmp_name']);
          break;
        default:
          return "imageTypeError";
          break;
      }
      //判断是否图片复制成功
      if (!$image2) return "imageTypeError";

      imagecopyresampled($image1, $image2, 0, 0, 0, 0, $intResizeWidth, $intResizeHeight, $intWidth, $intHeight); //全图拷贝

      if (!@file_exists($this->strSaveDir . $strResizeDir)) {
        if (!@mkdir($this->strSaveDir . $strResizeDir, 0777))
          return "mkdirError";
      }

      switch ($aryImageInfo[2]) {
        case 1:
          $isOK = @imagepng($image1, $this->strSaveDir . $strResizeDir . $this->strAuditPrefixion . $this->strSavaFileName);//保存调整图
          break;
        case 2:
          $isOK = @imagejpeg($image1, $this->strSaveDir . $strResizeDir . $this->strAuditPrefixion . $this->strSavaFileName, 100);//保存调整图
          break;
        case 3:
          $isOK = @imagepng($image1, $this->strSaveDir . $strResizeDir . $this->strAuditPrefixion . $this->strSavaFileName);//保存调整图
          break;
        case 6:
          $isOK = @imagewbmp($image1, $this->strSaveDir . $strResizeDir . $this->strAuditPrefixion . $this->strSavaFileName);//保存调整图
          break;
        default:
          return "imageTypeError";
          break;
      }
      if ($isOK) {
        // 判断是否加水印
        if ($this->resizeNeedWater) {
          $strRtn = $this->watermark($this->strSaveDir . $strResizeDir . $this->strAuditPrefixion . $this->strSavaFileName);
          if ($strRtn != "success") {
            if ($this->isShowAsChinese) {
              $strRtn = $this->getChineseReturn($strRtn);
            }
            return $strRtn;
            exit;
          }
        }
        return "success";
      } else {
        return "error";
      }
    }
  }

  //添加图片水印
  function watermark($objFile)
  {
    $aryImageInfo = getimagesize($objFile['tmp_name'], $aryImageInfo);
    //根据图像名，获得资源
    switch ($aryImageInfo[2]) {
      case 1:
        $sourceImage = imagecreatefromgif($objFile['tmp_name']);
        break;
      case 2:
        $sourceImage = imagecreatefromjpeg($objFile['tmp_name']);
        break;
      case 3:
        $sourceImage = imagecreatefrompng($objFile['tmp_name']);
        break;
      case 6:
        $sourceImage = imagecreatefromwbmp($objFile['tmp_name']);
        break;
      default:
        return "imageTypeError";
        exit;
    }
    //判断是否图片复制成功
    if (!$sourceImage)
      return "imageTypeError";

    //设置水印位置
    if ($this->intWatermarkType != 2)//文字水印
    {
      $ary = imagettfbbox(ceil($this->intWatermarkSize), 0, $this->strWatermarkFont, $this->strWatermarkString);//取得使用 TrueType 字体的文本的范围
      $intWaterWidth = $ary[4] - $ary[6];
      $intWaterHeight = $ary[7] - $ary[1];
      unset($ary);
    } else//图片水印
    {
      $aryWaterImageInfo = getimagesize($this->strWatermarkImage, $aryWaterImageInfo);
      $intWaterWidth = $aryWaterImageInfo[0];
      $intWaterHeight = $aryWaterImageInfo[1];
    }
    //水印是否超过图片大小
    if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
      return "success";
    }

    switch ($this->strPosition) {
      case 'sj':
        $posX = rand(0, ($aryImageInfo[0] - $intWaterWidth));
        $posY = rand(50, ($aryImageInfo[1] - $intWaterHeight));
        break;
      case 'lt':
        $posX = 0;
        $posY = 50;
        break;
      case 'rt':
        $posX = $aryImageInfo[0] - $intWaterWidth;
        $posY = 50;
        break;
      case 'lb':
        $posX = 0;
        $posY = $aryImageInfo[1] - $intWaterHeight;
        break;
      case 'ct':
        $posX = ($aryImageInfo[0] - $intWaterWidth) / 2;
        $posY = 50;
        break;
      case 'cb':
        $posX = ($aryImageInfo[0] - $intWaterWidth) / 2;
        $posY = $aryImageInfo[1] - $intWaterHeight;
        break;
      case 'lc':
        $posX = 0;
        $posY = ($aryImageInfo[1] - $intWaterHeight) / 2;
        break;
      case 'rc':
        $posX = $aryImageInfo[0] - $intWaterWidth;
        $posY = ($aryImageInfo[1] - $intWaterHeight) / 2;
        break;
      case 'cc':
        $posX = ($aryImageInfo[0] - $intWaterWidth) / 2;
        $posY = ($aryImageInfo[1] - $intWaterHeight) / 2;
        break;
      case 'rb':
      default:
        //2010-11-24
        $posX = $aryImageInfo[0] - $intWaterWidth - 10;
        $posY = $aryImageInfo[1] - $intWaterHeight;
        break;
    }

    if ($this->intWatermarkType != 2)//文字水印
    {
      $white = imagecolorallocatealpha($sourceImage, 255, 255, 255, 60);
      imagettftext($sourceImage, $this->intWatermarkSize, 0, $posX, $posY, $white, $this->strWatermarkFont, $this->strWatermarkString);

      //2010-11--29
      if ($this->otherstring != '') {
        $Y = $posY + 18;
        $X = $posX + 18;
        imagettftext($sourceImage, '8', 0, $X, $Y, $white, "./simhei.ttf", $this->otherstring);
      }
    } else//加水印图片
    {
      switch ($aryWaterImageInfo[2]) {
        case 1:
          $waterImage = imagecreatefromgif($this->strWatermarkImage);
          break;
        case 2:
          $waterImage = imagecreatefromjpeg($this->strWatermarkImage);
          break;
        case 3:
          $waterImage = imagecreatefrompng($this->strWatermarkImage);
          break;
        case 6:
          $waterImage = imagecreatefromwbmp($this->strWatermarkImage);
          break;
        default:
          return "typeError";
          exit;
      }
      //判断是否图片复制成功
      if (!$waterImage)
        return "imageTypeError";

      imagealphablending($sourceImage, true);
      imagecopy($sourceImage, $waterImage, $posX, $posY, 0, 0, $aryWaterImageInfo[0], $aryWaterImageInfo[1]);
      imagedestroy($waterImage);
    }

    switch ($aryImageInfo[2]) {
      case 1:
        //imagegif($sourceImage, $objFile['tmp_name']);
        imagejpeg($sourceImage, $objFile['tmp_name']);
        break;
      case 2:
        imagejpeg($sourceImage, $objFile['tmp_name']);
        break;
      case 3:
        imagepng($sourceImage, $objFile['tmp_name']);
        break;
      case 6:
        imagewbmp($sourceImage, $objFile['tmp_name']);
        //imagejpeg($sourceImage, $objFile['tmp_name']);
        break;
    }

    //覆盖原上传文件
    imagedestroy($sourceImage);
    return "success";
  }

  //添加图片水印(横向)
  function watermark2($objFile, $insert_str_arr = array(), $insert_pic_arr = array(), $house_id)
  {
    //1.根据原始图片路径,测定图片类型、大小。
    $aryImageInfo = getimagesize($objFile['tmp_name'], $aryImageInfo);
    switch ($aryImageInfo[2]) {
      //创建一个新图像
      case 1:
        $sourceImage = imagecreatefromgif($objFile['tmp_name']);
        break;
      case 2:
        $sourceImage = imagecreatefromjpeg($objFile['tmp_name']);
        break;
      case 3:
        $sourceImage = imagecreatefrompng($objFile['tmp_name']);
        break;
      case 6:
        $sourceImage = imagecreatefromwbmp($objFile['tmp_name']);
        break;
      default:
        return "imageTypeError";
        exit;
    }
    //判断是否图片复制成功
    if (!$sourceImage)
      return "imageTypeError";

    //设置水印位置
    //取得使用 TrueType 字体的文本的范围
    $ary = imagettfbbox(ceil($this->intWatermarkSize), 0, $this->strWatermarkFont, $this->strWatermarkString);
    $intWaterWidth = $ary[4] - $ary[6];
    $intWaterHeight = $ary[7] - $ary[1];
    unset($ary);

    //水印是否超过图片大小
    if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
      return "success";
    }

    //分配颜色
    $black = imagecolorallocatealpha($sourceImage, 0, 0, 0, 0);//黑色
    $red = imagecolorallocatealpha($sourceImage, 250, 0, 0, 0);//红色
    $white = imagecolorallocatealpha($sourceImage, 255, 255, 255, 0);//白色
    //用 TrueType 字体向图像写入文本
    //1）文字写入
    //门店名
    if (!empty($insert_str_arr['agency_name'])) {
      imagettftext($sourceImage, 55, 0, 70, 125, $white, $this->strWatermarkFont, $insert_str_arr['agency_name']);
    }
    //楼盘名
    if (!empty($insert_str_arr['cmt_name'])) {
      $cmt_name_lenth = mb_strlen($insert_str_arr['cmt_name']);
      $size = 74;
      switch ($cmt_name_lenth) {
        case 1:
          $start_x = 550;
          break;
        case 2:
          $start_x = 500;
          break;
        case 3:
          $start_x = 450;
          break;
        case 4:
          $start_x = 400;
          break;
        case 5:
          $start_x = 350;
          break;
        case 6:
          $start_x = 300;
          break;
        case 7:
          $start_x = 250;
          break;
        case 8:
          $start_x = 200;
          break;
        case 9:
          $start_x = 150;
          break;
        case 10:
          $start_x = 70;
          break;
        case 11:
          $start_x = 90;
          $size = 68;
          break;
        case 12:
          $start_x = 110;
          $size = 60;
          break;
        case 13:
          $start_x = 90;
          $size = 52;
          break;
        case 14:
          $start_x = 90;
          $size = 52;
          break;
        case 15:
          $start_x = 50;
          $size = 52;
          break;
        default:
          $start_x = 30;
          $size = 40;
      }
      imagettftext($sourceImage, $size, 0, $start_x, 350, $black, $this->strWatermarkFont, $insert_str_arr['cmt_name']);
    }
    //价格
    if (!empty($insert_str_arr['price'])) {
      $price_lenth = mb_strlen($insert_str_arr['price']);
      switch ($price_lenth) {
        case 1:
          $start_x = 625;
          break;
        case 2:
          $start_x = 575;
          break;
        case 3:
          $start_x = 500;
          break;
        case 4:
          $start_x = 455;
          break;
        case 5:
          $start_x = 425;
          break;
        case 6:
          $start_x = 375;
          break;
        case 7:
          $start_x = 325;
          break;
        case 8:
          $start_x = 275;
          break;
        case 9:
          $start_x = 225;
          break;
        case 10:
          $start_x = 175;
          break;
        default:
          $start_x = 0;
      }
      imagettftext($sourceImage, 74, 0, $start_x, 500, $red, $this->strWatermarkFont, $insert_str_arr['price']);
    }
    //设置字体
    $this->setWatermarkFont(BASEPATH . "/fonts/msyh.ttf");
    //面积
    if (!empty($insert_str_arr['area'])) {
      imagettftext($sourceImage, 45, 0, 350, 650, $black, $this->strWatermarkFont, $insert_str_arr['area']);
    }
    //户型
    if (!empty($insert_str_arr['apartment'])) {
      imagettftext($sourceImage, 45, 0, 830, 650, $black, $this->strWatermarkFont, $insert_str_arr['apartment']);
    }
    //装修
    if (!empty($insert_str_arr['fitment'])) {
      imagettftext($sourceImage, 45, 0, 350, 780, $black, $this->strWatermarkFont, $insert_str_arr['fitment']);
    }
    //朝向
    if (!empty($insert_str_arr['forward'])) {
      imagettftext($sourceImage, 45, 0, 830, 780, $black, $this->strWatermarkFont, $insert_str_arr['forward']);
    }
    //备注
    if (!empty($insert_str_arr['remark'])) {
      imagettftext($sourceImage, 45, 0, 350, 910, $black, $this->strWatermarkFont, $insert_str_arr['remark']);
    }
    //经纪人
    if (!empty($insert_str_arr['broker'])) {
      $broker_length = strlen($insert_str_arr['broker']);
      $_size = 45;
      $_x = 150;
      if ($broker_length > 30) {
        $_size = 40;
        $_x = 140;
      }
      imagettftext($sourceImage, $_size, 0, $_x, 1050, $black, $this->strWatermarkFont, $insert_str_arr['broker']);
    }

    if (is_full_array($insert_pic_arr)) {
      //2）图片写入
      if (isset($insert_pic_arr['shinei']) && !empty($insert_pic_arr['shinei'])) {
        //获得室内图类型、大小
        $aryWaterImageInfo_1 = getimagesize($insert_pic_arr['shinei'], $aryWaterImageInfo_1);
        $intWaterWidth = $aryWaterImageInfo_1[0];
        $intWaterHeight = $aryWaterImageInfo_1[1];
        //水印是否超过图片大小
        if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
          return "success";
        }
        switch ($aryWaterImageInfo_1[2]) {
          case 1:
            $waterImage_1 = imagecreatefromgif($insert_pic_arr['shinei']);
            break;
          case 2:
            $waterImage_1 = imagecreatefromjpeg($insert_pic_arr['shinei']);
            break;
          case 3:
            $waterImage_1 = imagecreatefrompng($insert_pic_arr['shinei']);
            break;
          case 6:
            $waterImage_1 = imagecreatefromwbmp($insert_pic_arr['shinei']);
            break;
          default:
            return "typeError";
            exit;
        }
        //判断是否图片复制成功
        if (!$waterImage_1)
          return "imageTypeError";

        imagealphablending($sourceImage, true);
        imagecopyresized($sourceImage, $waterImage_1, 1113, 250, 0, 0, 595, 390, $aryWaterImageInfo_1[0], $aryWaterImageInfo_1[1]);
        imagedestroy($waterImage_1);
      }

      if (isset($insert_pic_arr['huxing']) && !empty($insert_pic_arr['huxing'])) {
        //获得户型图类型、大小
        $aryWaterImageInfo_2 = getimagesize($insert_pic_arr['huxing'], $aryWaterImageInfo_2);
        $intWaterWidth = $aryWaterImageInfo_2[0];
        $intWaterHeight = $aryWaterImageInfo_2[1];
        //水印是否超过图片大小
        if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
          return "success";
        }
        switch ($aryWaterImageInfo_2[2]) {
          case 1:
            $waterImage_2 = imagecreatefromgif($insert_pic_arr['huxing']);
            break;
          case 2:
            $waterImage_2 = imagecreatefromjpeg($insert_pic_arr['huxing']);
            break;
          case 3:
            $waterImage_2 = imagecreatefrompng($insert_pic_arr['huxing']);
            break;
          case 6:
            $waterImage_2 = imagecreatefromwbmp($insert_pic_arr['huxing']);
            break;
          default:
            return "typeError";
            exit;
        }
        //判断是否图片复制成功
        if (!$waterImage_2)
          return "imageTypeError";

        imagealphablending($sourceImage, true);
        imagecopyresized($sourceImage, $waterImage_2, 1115, 672, 0, 0, 595, 390, $aryWaterImageInfo_2[0], $aryWaterImageInfo_2[1]);
        imagedestroy($waterImage_2);
      }

      //二维码图片
      if (isset($insert_pic_arr['qrcode']) && !empty($insert_pic_arr['qrcode'])) {
        //获得户型图类型、大小
        $aryWaterImageInfo_2 = getimagesize($insert_pic_arr['qrcode'], $aryWaterImageInfo_2);
        $intWaterWidth = $aryWaterImageInfo_2[0];
        $intWaterHeight = $aryWaterImageInfo_2[1];
        //水印是否超过图片大小
        if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
          return "success";
        }
        switch ($aryWaterImageInfo_2[2]) {
          case 1:
            $waterImage_2 = imagecreatefromgif($insert_pic_arr['qrcode']);
            break;
          case 2:
            $waterImage_2 = imagecreatefromjpeg($insert_pic_arr['qrcode']);
            break;
          case 3:
            $waterImage_2 = imagecreatefrompng($insert_pic_arr['qrcode']);
            break;
          case 6:
            $waterImage_2 = imagecreatefromwbmp($insert_pic_arr['qrcode']);
            break;
          default:
            return "typeError";
            exit;
        }
        //判断是否图片复制成功
        if (!$waterImage_2)
          return "imageTypeError";

        imagealphablending($sourceImage, true);
        imagecopyresized($sourceImage, $waterImage_2, 925, 960, 0, 0, 160, 160, $aryWaterImageInfo_2[0], $aryWaterImageInfo_2[1]);
        imagedestroy($waterImage_2);
      }
    }
    $file_name = 'sell_' . $house_id;

    //创建图像
    switch ($aryImageInfo[2]) {
      case 1:
        imagejpeg($sourceImage, 'source/mls/images/v1.0/house_print/' . $file_name . '.jpg');
        break;
      case 2:
        imagejpeg($sourceImage, 'source/mls/images/v1.0/house_print/' . $file_name . '.jpg');
        break;
      case 3:
        imagepng($sourceImage, 'source/mls/images/v1.0/house_print/' . $file_name . '.jpg');
        break;
      case 6:
        imagewbmp($sourceImage, 'source/mls/images/v1.0/house_print/' . $file_name . '.jpg');
        break;
    }

    //覆盖原上传文件
    imagedestroy($sourceImage);
    return "success";
  }

  //添加图片水印(纵向)
  function watermark3($objFile, $insert_str_arr = array(), $insert_pic_arr = array(), $house_id)
  {
    //1.根据原始图片路径,测定图片类型、大小。
    $aryImageInfo = getimagesize($objFile['tmp_name'], $aryImageInfo);
    switch ($aryImageInfo[2]) {
      //创建一个新图像
      case 1:
        $sourceImage = imagecreatefromgif($objFile['tmp_name']);
        break;
      case 2:
        $sourceImage = imagecreatefromjpeg($objFile['tmp_name']);
        break;
      case 3:
        $sourceImage = imagecreatefrompng($objFile['tmp_name']);
        break;
      case 6:
        $sourceImage = imagecreatefromwbmp($objFile['tmp_name']);
        break;
      default:
        return "imageTypeError";
        exit;
    }
    //判断是否图片复制成功
    if (!$sourceImage)
      return "imageTypeError";

    //设置水印位置
    //取得使用 TrueType 字体的文本的范围
    $ary = imagettfbbox(ceil($this->intWatermarkSize), 0, $this->strWatermarkFont, $this->strWatermarkString);
    $intWaterWidth = $ary[4] - $ary[6];
    $intWaterHeight = $ary[7] - $ary[1];
    unset($ary);

    //水印是否超过图片大小
    if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
      return "success";
    }

    //分配颜色
    $black = imagecolorallocatealpha($sourceImage, 0, 0, 0, 0);//黑色
    $red = imagecolorallocatealpha($sourceImage, 250, 0, 0, 0);//红色
    $white = imagecolorallocatealpha($sourceImage, 255, 255, 255, 0);//白色
    //用 TrueType 字体向图像写入文本
    //1）文字写入
    //门店名
    if (!empty($insert_str_arr['agency_name'])) {
      imagettftext($sourceImage, 55, 0, 30, 125, $white, $this->strWatermarkFont, $insert_str_arr['agency_name']);
    }
    //楼盘名
    if (!empty($insert_str_arr['cmt_name'])) {
      $cmt_name_lenth = mb_strlen($insert_str_arr['cmt_name']);
      $size = 74;
      switch ($cmt_name_lenth) {
        case 1:
          $start_x = 550;
          break;
        case 2:
          $start_x = 500;
          break;
        case 3:
          $start_x = 450;
          break;
        case 4:
          $start_x = 400;
          break;
        case 5:
          $start_x = 350;
          break;
        case 6:
          $start_x = 300;
          break;
        case 7:
          $start_x = 250;
          break;
        case 8:
          $start_x = 200;
          break;
        case 9:
          $start_x = 150;
          break;
        case 10:
          $start_x = 100;
          break;
        case 11:
          $start_x = 150;
          $size = 68;
          break;
        case 12:
          $start_x = 190;
          $size = 60;
          break;
        case 13:
          $start_x = 170;
          $size = 52;
          break;
        case 14:
          $start_x = 150;
          $size = 52;
          break;
        case 15:
          $start_x = 130;
          $size = 52;
          break;
        default:
          $start_x = 130;
          $size = 50;
      }
      imagettftext($sourceImage, $size, 0, $start_x, 350, $black, $this->strWatermarkFont, $insert_str_arr['cmt_name']);
    }
    //价格
    if (!empty($insert_str_arr['price'])) {
      $price_lenth = mb_strlen($insert_str_arr['price']);
      switch ($price_lenth) {
        case 1:
          $start_x = 650;
          break;
        case 2:
          $start_x = 600;
          break;
        case 3:
          $start_x = 525;
          break;
        case 4:
          $start_x = 500;
          break;
        case 5:
          $start_x = 450;
          break;
        case 6:
          $start_x = 400;
          break;
        case 7:
          $start_x = 350;
          break;
        case 8:
          $start_x = 300;
          break;
        case 9:
          $start_x = 250;
          break;
        case 10:
          $start_x = 200;
          break;
        default:
          $start_x = 0;
      }
      imagettftext($sourceImage, 74, 0, $start_x, 500, $red, $this->strWatermarkFont, $insert_str_arr['price']);
    }
    //设置字体
    $this->setWatermarkFont(BASEPATH . "/fonts/msyh.ttf");
    //面积
    if (!empty($insert_str_arr['area'])) {
      imagettftext($sourceImage, 45, 0, 375, 700, $black, $this->strWatermarkFont, $insert_str_arr['area']);
    }
    //户型
    if (!empty($insert_str_arr['apartment'])) {
      imagettftext($sourceImage, 45, 0, 860, 700, $black, $this->strWatermarkFont, $insert_str_arr['apartment']);
    }
    //装修
    if (!empty($insert_str_arr['fitment'])) {
      imagettftext($sourceImage, 45, 0, 375, 820, $black, $this->strWatermarkFont, $insert_str_arr['fitment']);
    }
    //朝向
    if (!empty($insert_str_arr['forward'])) {
      imagettftext($sourceImage, 45, 0, 860, 820, $black, $this->strWatermarkFont, $insert_str_arr['forward']);
    }
    //备注
    if (!empty($insert_str_arr['remark'])) {
      imagettftext($sourceImage, 45, 0, 375, 955, $black, $this->strWatermarkFont, $insert_str_arr['remark']);
    }
    //经纪人
    if (!empty($insert_str_arr['broker'])) {
      imagettftext($sourceImage, 45, 0, 150, 1100, $black, $this->strWatermarkFont, $insert_str_arr['broker']);
    }

    if (is_full_array($insert_pic_arr)) {
      //2）图片写入
      if (isset($insert_pic_arr['shinei']) && !empty($insert_pic_arr['shinei'])) {
        //获得室内图类型、大小
        $aryWaterImageInfo_1 = getimagesize($insert_pic_arr['shinei'], $aryWaterImageInfo_1);
        $intWaterWidth = $aryWaterImageInfo_1[0];
        $intWaterHeight = $aryWaterImageInfo_1[1];
        //水印是否超过图片大小
        if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
          return "success";
        }
        switch ($aryWaterImageInfo_1[2]) {
          case 1:
            $waterImage_1 = imagecreatefromgif($insert_pic_arr['shinei']);
            break;
          case 2:
            $waterImage_1 = imagecreatefromjpeg($insert_pic_arr['shinei']);
            break;
          case 3:
            $waterImage_1 = imagecreatefrompng($insert_pic_arr['shinei']);
            break;
          case 6:
            $waterImage_1 = imagecreatefromwbmp($insert_pic_arr['shinei']);
            break;
          default:
            return "typeError";
            exit;
        }
        //判断是否图片复制成功
        if (!$waterImage_1)
          return "imageTypeError";

        imagealphablending($sourceImage, true);
        imagecopyresized($sourceImage, $waterImage_1, 45, 1178, 0, 0, 550, 400, $aryWaterImageInfo_1[0], $aryWaterImageInfo_1[1]);
        imagedestroy($waterImage_1);
      }

      if (isset($insert_pic_arr['huxing']) && !empty($insert_pic_arr['huxing'])) {
        //获得户型图类型、大小
        $aryWaterImageInfo_2 = getimagesize($insert_pic_arr['huxing'], $aryWaterImageInfo_2);
        $intWaterWidth = $aryWaterImageInfo_2[0];
        $intWaterHeight = $aryWaterImageInfo_2[1];
        //水印是否超过图片大小
        if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
          return "success";
        }
        switch ($aryWaterImageInfo_2[2]) {
          case 1:
            $waterImage_2 = imagecreatefromgif($insert_pic_arr['huxing']);
            break;
          case 2:
            $waterImage_2 = imagecreatefromjpeg($insert_pic_arr['huxing']);
            break;
          case 3:
            $waterImage_2 = imagecreatefrompng($insert_pic_arr['huxing']);
            break;
          case 6:
            $waterImage_2 = imagecreatefromwbmp($insert_pic_arr['huxing']);
            break;
          default:
            return "typeError";
            exit;
        }
        //判断是否图片复制成功
        if (!$waterImage_2)
          return "imageTypeError";

        imagealphablending($sourceImage, true);
        imagecopyresized($sourceImage, $waterImage_2, 645, 1178, 0, 0, 550, 400, $aryWaterImageInfo_2[0], $aryWaterImageInfo_2[1]);
        imagedestroy($waterImage_2);
      }

      //二维码图片
      if (isset($insert_pic_arr['qrcode']) && !empty($insert_pic_arr['qrcode'])) {
        //获得户型图类型、大小
        $aryWaterImageInfo_2 = getimagesize($insert_pic_arr['qrcode'], $aryWaterImageInfo_2);
        $intWaterWidth = $aryWaterImageInfo_2[0];
        $intWaterHeight = $aryWaterImageInfo_2[1];
        //水印是否超过图片大小
        if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
          return "success";
        }
        switch ($aryWaterImageInfo_2[2]) {
          case 1:
            $waterImage_2 = imagecreatefromgif($insert_pic_arr['qrcode']);
            break;
          case 2:
            $waterImage_2 = imagecreatefromjpeg($insert_pic_arr['qrcode']);
            break;
          case 3:
            $waterImage_2 = imagecreatefrompng($insert_pic_arr['qrcode']);
            break;
          case 6:
            $waterImage_2 = imagecreatefromwbmp($insert_pic_arr['qrcode']);
            break;
          default:
            return "typeError";
            exit;
        }
        //判断是否图片复制成功
        if (!$waterImage_2)
          return "imageTypeError";

        imagealphablending($sourceImage, true);
        imagecopyresized($sourceImage, $waterImage_2, 1000, 1015, 0, 0, 160, 160, $aryWaterImageInfo_2[0], $aryWaterImageInfo_2[1]);
        imagedestroy($waterImage_2);
      }

    }
    $file_name = 'sell_' . $house_id;

    //创建图像
    switch ($aryImageInfo[2]) {
      case 1:
        imagejpeg($sourceImage, 'source/mls/images/v1.0/house_print/' . $file_name . '.jpg');
        break;
      case 2:
        imagejpeg($sourceImage, 'source/mls/images/v1.0/house_print/' . $file_name . '.jpg');
        break;
      case 3:
        imagepng($sourceImage, 'source/mls/images/v1.0/house_print/' . $file_name . '.jpg');
        break;
      case 6:
        imagewbmp($sourceImage, 'source/mls/images/v1.0/house_print/' . $file_name . '.jpg');
        break;
    }

    //覆盖原上传文件
    imagedestroy($sourceImage);
    return "success";
  }


  //添加图片水印(个人中心，门店二维码)
  function watermark_agency($objFile, $insert_pic_str = '', $agency_id)
  {
    //1.根据原始图片路径,测定图片类型、大小。
    $aryImageInfo = getimagesize($objFile['tmp_name'], $aryImageInfo);
    switch ($aryImageInfo[2]) {
      //创建一个新图像
      case 1:
        ini_set("memory_limit", "60M");
        $sourceImage = imagecreatefromgif($objFile['tmp_name']);
        break;
      case 2:
        ini_set("memory_limit", "60M");
        $sourceImage = imagecreatefromjpeg($objFile['tmp_name']);
        break;
      case 3:
        ini_set("memory_limit", "60M");
        $sourceImage = imagecreatefrompng($objFile['tmp_name']);
        break;
      case 6:
        ini_set("memory_limit", "60M");
        $sourceImage = imagecreatefromwbmp($objFile['tmp_name']);
        break;
      default:
        return "imageTypeError";
        exit;
    }
    //判断是否图片复制成功
    if (!$sourceImage)
      return "imageTypeError";

    //设置水印位置
    //取得使用 TrueType 字体的文本的范围
    $ary = imagettfbbox(ceil($this->intWatermarkSize), 0, $this->strWatermarkFont, $this->strWatermarkString);
    $intWaterWidth = $ary[4] - $ary[6];
    $intWaterHeight = $ary[7] - $ary[1];
    unset($ary);

    //水印是否超过图片大小
    if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
      return "success";
    }

    if (!empty($insert_pic_str)) {
      //二维码图片
      if (isset($insert_pic_str) && !empty($insert_pic_str)) {
        //获得户型图类型、大小
        $aryWaterImageInfo_2 = getimagesize($insert_pic_str, $aryWaterImageInfo_2);
        $intWaterWidth = $aryWaterImageInfo_2[0];
        $intWaterHeight = $aryWaterImageInfo_2[1];
        //水印是否超过图片大小
        if (($aryImageInfo[0] < $intWaterWidth) || ($aryImageInfo[1] < $intWaterHeight)) {
          return "success";
        }
        switch ($aryWaterImageInfo_2[2]) {
          case 1:
            $waterImage_2 = imagecreatefromgif($insert_pic_str);
            break;
          case 2:
            $waterImage_2 = imagecreatefromjpeg($insert_pic_str);
            break;
          case 3:
            $waterImage_2 = imagecreatefrompng($insert_pic_str);
            break;
          case 6:
            $waterImage_2 = imagecreatefromwbmp($insert_pic_str);
            break;
          default:
            return "typeError";
            exit;
        }
        //判断是否图片复制成功
        if (!$waterImage_2)
          return "imageTypeError";

        imagealphablending($sourceImage, true);
        imagecopyresized($sourceImage, $waterImage_2, 500, 650, 0, 0, 800, 800, $aryWaterImageInfo_2[0], $aryWaterImageInfo_2[1]);

        imagecopyresized($sourceImage, $waterImage_2, 2220, 650, 0, 0, 800, 800, $aryWaterImageInfo_2[0], $aryWaterImageInfo_2[1]);
        imagedestroy($waterImage_2);
      }

    }
    $file_name = 'scode_' . $agency_id;

    //创建图像
    switch ($aryImageInfo[2]) {
      case 1:
        imagejpeg($sourceImage, 'source/mls/images/v1.0/agency_wei/' . $file_name . '.jpg');
        break;
      case 2:
        imagejpeg($sourceImage, 'source/mls/images/v1.0/agency_wei/' . $file_name . '.jpg');
        break;
      case 3:
        imagepng($sourceImage, 'source/mls/images/v1.0/agency_wei/' . $file_name . '.jpg');
        break;
      case 6:
        imagewbmp($sourceImage, 'source/mls/images/v1.0/agency_wei/' . $file_name . '.jpg');
        break;
    }

    //覆盖原上传文件
    imagedestroy($sourceImage);
    return "success";
  }

  //删除已上传文件
  function remove($strSaveFileURL, $strBaseDir = "/database/webroot")
  {
    if ($this->strUploadType == "ftp") {
      preg_match_all("/http:\/\/img(\d+).house365.com\/(.*)/i", $strSaveFileURL, $ary);
      $strFilePath = "img" . $ary[1][0] . "/" . $ary[2][0];
      if ($this->hasSetedServer)
        $this->strServerSite = $ary[1][0];
      else
        $this->setServerSite($ary[1][0]);
      //ftp登录
      $this->ftpConn = @ftp_connect($this->strServer);
      if (!$this->ftpConn) {
        $strRtn = "connError";
      } elseif (!@ftp_login($this->ftpConn, $this->strUsername, $this->strPassword)) {
        ftp_quit($this->ftpConn); //关闭ftp连接
        $strRtn = "loginError";
      } elseif (@ftp_size($this->ftpConn, $strFilePath) == -1)
        $strRtn = "noneFile";
      elseif (@ftp_delete($this->ftpConn, $strFilePath)) {
        $intRPos = strrpos($strFilePath, "/");
        $strResizeImageURL = substr($strFilePath, 0, $intRPos + 1) . "thumb/" . substr($strFilePath, $intRPos + 1);
        @ftp_delete($this->ftpConn, $strResizeImageURL);
        //删除后台展示
        $strAuditURL = substr($strFilePath, 0, $intRPos + 1) . "audit/" . substr($strFilePath, $intRPos + 1);
        @ftp_delete($this->ftpConn, $strAuditURL);
        $strRtn = "success";
      } else
        $strRtn = "error";
      ftp_quit($this->ftpConn); //关闭ftp连接
    } else {
      if (@file_exists($strBaseDir . $strSaveFileURL)) {
        unlink($strBaseDir . $strSaveFileURL);
        $intRPos = strrpos($strSaveFileURL, "/");
        $strResizeImageURL = substr($strSaveFileURL, 0, $intRPos + 1) . "thumb/" . substr($strSaveFileURL, $intRPos + 1);
        if (@file_exists($strBaseDir . $strResizeImageURL))
          unlink($strBaseDir . $strResizeImageURL);
        $strRtn = "success";
      } else
        $strRtn = "noneFile";
    }

    if ($strRtn != "success" && $this->isShowAsChinese) {
      $strRtn = $this->getChineseReturn($strRtn);
    }
    return $strRtn;
  }

  //获得中文返回值
  function getChineseReturn($strRtn)
  {
    switch ($strRtn) {
      case "noneFile":
        return "文件不存在";
        break;
      case "sizeLimit":
        return "文件大小超过限制";
        break;
      case "sysSizeLimit":
        return "文件大小超过系统限制";
        break;
      case "typeLimit":
        return "文件类型不正确";
        break;
      case "mkdirError":
        return "创建文件夹出错，可能原因为输入的文件夹或路径非法，或系统权限限制";
        break;
      case "connError":
        return "ftp连接失败";
        break;
      case "loginError":
        return "ftp登录失败";
        break;
      case "sizeZero":
        return "文件大小为0，或文件不存在";
        break;
      case "imageTypeError":
        if ($this->needWatermark)
          $rtnM = "，在添加水印时出错";
        elseif ($this->needResizeImage)
          $rtnM = "，在生成缩略图时出错";
        return "不是有效的图片格式" . $rtnM;
        break;
      case "noneFileError":
        return "待做调整的图片不存在";
        break;
      default:
        return "未知错误";
        break;
    }
  }
}
