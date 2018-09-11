<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'db';
$active_record = TRUE;

/**********************************公用主库 mls********************************/
//MLS公用主库
$db['db']['hostname'] = '10.142.62.175';
$db['db']['port'] = '3306';
$db['db']['username'] = 'mlsprd';
$db['db']['password'] = 'MlsO607!23Sql';
$db['db']['database'] = 'mls';
$db['db']['dbdriver'] = 'mysqli';
$db['db']['dbprefix'] = '';
$db['db']['pconnect'] = FALSE;//长连接
$db['db']['db_debug'] = FALSE;//上线关闭,FALSE
$db['db']['cache_on'] = FALSE;
$db['db']['cachedir'] = '';
$db['db']['char_set'] = 'utf8';
$db['db']['dbcollat'] = 'utf8_general_ci';
$db['db']['swap_pre'] = '';
$db['db']['autoinit'] = FALSE;
$db['db']['stricton'] = FALSE;

//MLS公用从库
$db['dbback']['hostname'] = '10.142.62.175';
$db['dbback']['port'] = '3306';
$db['dbback']['username'] = 'mlsprd';
$db['dbback']['password'] = 'MlsO607!23Sql';
$db['dbback']['database'] = 'mls';
$db['dbback']['dbdriver'] = 'mysqli';
$db['dbback']['dbprefix'] = '';
$db['dbback']['pconnect'] = FALSE;//长连接
$db['dbback']['db_debug'] = FALSE;//上线关闭,FALSE
$db['dbback']['cache_on'] = FALSE;
$db['dbback']['cachedir'] = '';
$db['dbback']['char_set'] = 'utf8';
$db['dbback']['dbcollat'] = 'utf8_general_ci';
$db['dbback']['swap_pre'] = '';
$db['dbback']['autoinit'] = FALSE;
$db['dbback']['stricton'] = FALSE;//上线关闭,FALSE
/********************************************************************/
//新开的站使用数组循环来生成db
$open_city = array('nj', 'sz', 'nb', 'huz', 'sx', 'tz', 'jh', 'cd', 'cq', 'hz', 'km', 'xa', 'hf', 'hrb', 'taizhou', 'langfang', 'zhongshan', 'zhuhai', 'huizhou', 'wuxi', 'guiyang', 'wuhan', 'xiamen', 'quanzhou', 'fuzhou', 'songyuan', 'shanghai', 'zhangzhou', 'beijing', 'changchun', 'qingdao', 'jinan', 'taiyuan', 'wulumuqi', 'haikou', 'guangzhou', 'shenzhen', 'tianjin', 'shijiazhuang', 'huhehaote', 'nanning', 'lanzhou', 'changsha', 'nanchang', 'lasa', 'zhengzhou', 'shenyang', 'yinchuan', 'xining', 'huaian', 'ningde', 'wenzhou', 'pingxiang', 'liuzhou', 'weifang', 'zhangjiakou');

foreach ($open_city as $v) {
    $db['db_' . $v]['hostname'] = '10.142.62.175';
  $db['db_' . $v]['port'] = '3306';
  $db['db_' . $v]['username'] = 'mlsprd';
  $db['db_' . $v]['password'] = 'MlsO607!23Sql';
  $db['db_' . $v]['database'] = 'mls_' . $v;
  $db['db_' . $v]['dbdriver'] = 'mysqli';
  $db['db_' . $v]['dbprefix'] = '';
  $db['db_' . $v]['pconnect'] = FALSE;//长连接
  $db['db_' . $v]['db_debug'] = FALSE;//上线关闭,FALSE
  $db['db_' . $v]['cache_on'] = FALSE;
  $db['db_' . $v]['cachedir'] = '';
  $db['db_' . $v]['char_set'] = 'utf8';
  $db['db_' . $v]['dbcollat'] = 'utf8_general_ci';
  $db['db_' . $v]['swap_pre'] = '';
  $db['db_' . $v]['autoinit'] = FALSE;
  $db['db_' . $v]['stricton'] = FALSE;//上线关闭,FALSE

  //MLS城市
    $db['dbback_' . $v]['hostname'] = '10.142.62.175';
  $db['dbback_' . $v]['port'] = '3306';
  $db['dbback_' . $v]['username'] = 'mlsprd';
  $db['dbback_' . $v]['password'] = 'MlsO607!23Sql';
  $db['dbback_' . $v]['database'] = 'mls_' . $v;
  $db['dbback_' . $v]['dbdriver'] = 'mysqli';
  $db['dbback_' . $v]['dbprefix'] = '';
  $db['dbback_' . $v]['pconnect'] = FALSE;//长连接
  $db['dbback_' . $v]['db_debug'] = FALSE;//上线关闭,FALSE
  $db['dbback_' . $v]['cache_on'] = FALSE;
  $db['dbback_' . $v]['cachedir'] = '';
  $db['dbback_' . $v]['char_set'] = 'utf8';
  $db['dbback_' . $v]['dbcollat'] = 'utf8_general_ci';
  $db['dbback_' . $v]['swap_pre'] = '';
  $db['dbback_' . $v]['autoinit'] = FALSE;
  $db['dbback_' . $v]['stricton'] = FALSE;//上线关闭,FALSE
}
/* End of file database.php */
/* Location: ./application/config/database.php */
