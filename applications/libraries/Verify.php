<?php

class Verify
{

  //请勿随意改动
  private static $verify_code = 'MLSMOBILE-LTspbateb07#@fwpm25*=FOQXhnxiy';

  /**
   * 生成用户登录唯一验证码
   * @param array 加密数据数组
   * @return string
   */
  function user_enrypt($param = array())
  {
    $A = implode(',', $param);
    $B = self::$verify_code;
    $C = md5($A . "," . $B);
    $D = base64_encode($A . "-" . $C);
    return $D;
  }

  /**
   * 验证用户登录验证码是否合法
   * @param string $verify_code 用户验证登录码
   **/
  function user_decrypt($verify_code)
  {
    $result = array();
    $AC = base64_decode($verify_code);
    $userInfo = explode("-", $AC);
    if (isset($userInfo[0])) {
      $A = $userInfo[0];
    }
    $A = isset($userInfo[0]) ? $userInfo[0] : '';
    $C = isset($userInfo[1]) ? $userInfo[1] : '';
    //验证用户登录是否合法
    if (md5($A . "," . self::$verify_code) === $C) {
      $result['result'] = 1;
      $result['data'] = explode(',', $A);
    } else {
      $result['result'] = 0;
    }
    return $result;
  }

  public function getCode($num, $w, $h)
  {
    $code = "";
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    for ($i = 0; $i < $num; $i++) {
      $code .= $pattern{mt_rand(0, 35)};    //生成php随机数
    }
    //将生成的验证码写入session，备验证时用
    $_SESSION["register_code"] = $code;
    //创建图片，定义颜色值
    header("Content-type: image/PNG");
    $im = imagecreate($w, $h);
    $black = imagecolorallocate($im, 0, 0, 0);
    $gray = imagecolorallocate($im, 200, 200, 200);
    $bgcolor = imagecolorallocate($im, 255, 255, 255);
    //填充背景
    imagefill($im, 0, 0, $gray);

    //画边框
    imagerectangle($im, 0, 0, $w - 1, $h - 1, $black);

    //随机绘制两条虚线，起干扰作用
    $style = array($black, $black, $black, $black, $black,
      $gray, $gray, $gray, $gray, $gray
    );
    imagesetstyle($im, $style);
    $y1 = rand(0, $h);
    $y2 = rand(0, $h);
    $y3 = rand(0, $h);
    $y4 = rand(0, $h);
    imageline($im, 0, $y1, $w, $y3, IMG_COLOR_STYLED);
    imageline($im, 0, $y2, $w, $y4, IMG_COLOR_STYLED);

    //在画布上随机生成大量黑点，起干扰作用;
    for ($i = 0; $i < 80; $i++) {
      imagesetpixel($im, rand(0, $w), rand(0, $h), $black);
    }
    //将数字随机显示在画布上,字符的水平间距和位置都按一定波动范围随机生成
    $strx = 20;
    for ($i = 0; $i < $num; $i++) {
      imagettftext($im, 20, 0, $strx, 25, $black, BASEPATH . '/fonts/msyhbd.ttf', substr($code, $i, 1));
      $strx += 20;
    }
    imagepng($im);//输出图片
    imagedestroy($im);//释放图片所占内存
  }

}

?>
