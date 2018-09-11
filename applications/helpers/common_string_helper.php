<?php

/**
* 统计中文字符串长度的函数
* @param $str 要计算长度的字符串
* @return int 字符长度
*/
function abslength( $str , $encod = 'utf-8' )
{   
    if(empty($str))
    {
        return 0;
    }
    
    if(function_exists('mb_strlen'))
    {
        return mb_strlen( $str , $encod );
    }
    else 
    {
        preg_match_all("/./u", $str, $ar);
        
        return count($ar[0]);
    }
}



/**
* 验证是否是电话号码
* @param string $tel 需要验证的号码
* @param $type 需要验证的类型，不提供类型则都验证
* @return boolean 是否是手机号码
*/
function is_tel( $tel , $type = '')  
{  
    $regxArr = array(  
    'sj'  =>  '/^(\+?86-?)?(18|15|13)[0-9]{9}$/',  
    'tel' =>  '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',  
    '400' =>  '/^400(-\d{3,4}){2}$/',  
    );
    
    if($type && isset($regxArr[$type]))  
    {  
        return preg_match($regxArr[$type], $tel) ? true:false;  
    }  
    
    foreach($regxArr as $regx)  
    {  
      if(preg_match($regx, $tel ))  
      {  
        return true;  
      }  
    }
    
    return false;  
}

//GBK截取
function csubstr2($str_cut , $length = 30)
{ 
    if (strlen($str_cut) > $length)
    {
        for($i=0; $i < $length; $i++)
            if (ord($str_cut[$i]) > 128)   $i++;
        
        $str_cut = substr($str_cut,0,$i);
    }
    return $str_cut;
}

//UFT-8截取
function utf8Substr($str, $from, $len)
{
	return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
		'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s','$1',$str);
}

/**
 * 去除小数点后的0
 * @param   float  $s 数字参数 
 * @param   int  $num 保留小数点后几位 默认两位
 * @return  stirng  截取后的字符串
 * @update    2014/05/30 xz
 */
function strip_end_0($s,$num=2)
{   
    $s = trim(strval($s));
    if($num){
        $s = round($s,$num);
    }
    
    if (preg_match('#^-?\d+?\.0+$#', $s)) 
    {  
        return preg_replace('#^(-?\d+?)\.0+$#','$1',$s);  
    }
    
    if (preg_match('#^-?\d+?\.[0-9]+?0+$#', $s)) 
    {  
        return preg_replace('#^(-?\d+\.[0-9]+?)0+$#','$1',$s);  
    }
    
    return $s;  
}


/**
 * 格式化房源、客源编号信息
 * @param   int  $id 房源、客源编号ID 
 * @return  stirng  $type 客源、房源类型
 * @update    2014/05/30 xz
 */
function format_info_id($id , $type)
{   
    $format_str = '';
    
    switch (strtolower($type))
    {
        case 'sell':
            $format_str = 'CS'.$id;
        break;
        case 'rent':
            $format_str = 'CZ'.$id;
        break;
        case 'buy_customer':
            $format_str = 'QG'.$id;
        break;
        case 'rent_customer':
            $format_str = 'QZ'.$id;
        break;
    }
    
    return $format_str;
}

/* End of file common_string_helper.php */
/* Location: ./applications/common_string_helper.php */