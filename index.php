<?php
@header('Content-type: text/html;charset=UTF-8');

//ini_set("memory_limit","80M");
/*
 *-------------------------------------------------------------
 *系统停机维护时开启
 *-------------------------------------------------------------
 *$server_array          维护中的域名
 *$maintenance_starttime 维护开始时间
 *$maintenance_endtime   维护结束时间
 */
/*$server_array = array('mls.house365.com');
$nowtime = date('YmdHis');
$maintenance_starttime = 201501261600;
$maintenance_endtime = 201501261700;
if(!empty($server_array) && in_array($_SERVER['SERVER_NAME'], $server_array)
    && ($maintenance_starttime < $nowtime && $nowtime < $maintenance_endtime))
{
    exit('server_maintenance');
}*/


/*
 * ---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 * ---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
define('ENVIRONMENT', 'development');
/*
 * ---------------------------------------------------------------
 * ERROR REPORTING
 * ---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT')) {
  switch (ENVIRONMENT) {
    case 'development':
      error_reporting(5);
      ini_set("display_errors", true);
      break;

    case 'testing':
    case 'production':
      error_reporting(0);
      break;

    default:
      exit('The application environment is not set correctly.');
  }
}

define('SMS_SEND', true);

define('MLS_TEL_400', '400-018-5180');

//短信接口
//define('SMS_URL', 'http://sapi.253.com/msg/HttpBatchSendSM');
//define('SMS_URL','http://101.37.27.156:7080/sms/sendSimpleSms');//金品房产测试用
define('SMS_URL','http://10.29.113.176:7082/sms/sendSimpleSms');//金品房产生产用

//金品url JINPING_URL
define('JINPIN_URL', 'http://101.37.27.156:7081');

//内网访问外网的代理服务器ip(本地环境无需设代理)
//define('PROXY_URL', '');

define('PROTOCOL', 'http://');

//经纪人平台 PC端URL
define('MLS_NAME', 'fang.cd121.com');

//管理平台 PC端URL
define('MLS_ADMIN_NAME', 'fang-admin.cd121.com');

//签约平台 PC端URL
define('MLS_SIGN_NAME', 'fang-sign.cd121.com');

//对外接口API URL
define('MLS_API_NAME', 'fang-api.cd121.com');

//JOBAPI URL
define('MLS_JOB_NAME', 'fang-job.cd121.com');

//移动端API URL
define('MLS_MOBILE_NAME', 'fang-mobile.cd121.com');

//静态资源URL
define('MLS_SOURCE_NAME', 'fang-source.cd121.com');

//文件上传URL
define('MLS_FILE_SERVER_NAME', 'fang-fileserver.cd121.com');

//java接口文件上传URL
define('JAVA_FILE_UPLOAD_URL', 'http://101.37.27.156:7081/house/uploadPic');

//金融端URL
define('MLS_FINANCE_NAME', 'fang-finance-api.cd121.com');


/*
 * ---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 * ---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same  directory
 * as this file.
 *
 */
$system_path = 'system';

/*
 * ---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 * ---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder then the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server.  If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 *
 */
if (MLS_ADMIN_NAME == $_SERVER['SERVER_NAME']) {
  $app = 'mls_admin';
} else if (MLS_MOBILE_NAME == $_SERVER['SERVER_NAME']) {
  $app = 'mls_mobile';
} else if (MLS_API_NAME == $_SERVER['SERVER_NAME']) {
  $app = 'mls_api';
} else if (MLS_JOB_NAME == $_SERVER['SERVER_NAME']) {
  $app = 'mls_job';
} else if (MLS_FILE_SERVER_NAME == $_SERVER['SERVER_NAME']) {
  $app = 'mls_fileserver';
} else if (MLS_SIGN_NAME == $_SERVER['SERVER_NAME']) {
  $app = 'mls_guli';
} else if (MLS_NAME == $_SERVER['SERVER_NAME']) {
  $app = 'mls';
}

define('APP', $app);

$application_folder = 'applications/' . APP;

/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here.  For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT:  If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller.  Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 *
 */
// The directory name, relative to the "controllers" folder.  Leave blank
// if your controller is not in a sub-folder within the "controllers" folder
// $routing['directory'] = '';
// The controller class file name.  Example:  Mycontroller
// $routing['controller'] = '';
// The controller function you wish to be called.
// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 *
 */
// $assign_to_config['name_of_config_item'] = 'value of config item';
// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

// Set the current directory correctly for CLI requests
if (defined('STDIN')) {
  chdir(dirname(__FILE__));
}

if (realpath($system_path) !== FALSE) {
  $system_path = realpath($system_path) . '/';
}

// ensure there's a trailing slash
$system_path = rtrim($system_path, '/') . '/';

// Is the system path correct?
if (!is_dir($system_path)) {
  exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: " . pathinfo(__FILE__, PATHINFO_BASENAME));
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// The PHP file extension
// this global constant is deprecated.
define('EXT', '.php');

// Path to the system folder
define('BASEPATH', str_replace("\\", "/", $system_path));

// Path to the front controller (this file)
define('FCPATH', str_replace(SELF, '', __FILE__));

// Name of the "system folder"
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


// The path to the "application" folder
if (is_dir($application_folder)) {
  define('APPPATH', $application_folder . '/');
} else {
  if (!is_dir(BASEPATH . $application_folder . '/')) {
    exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: " . SELF);
  }

  define('APPPATH', BASEPATH . $application_folder . '/');
}


/*
 * ---------------------------------------------------------------
 * PUBLIC ASSER FOLDER NAME
 * ---------------------------------------------------------------
 * 共用资源文件目录，CI是可以覆盖system的类的，先加载应用的类再加载system的类，
 * 那么继承系统的 Config.php与Loader.php 修改加载流程，加载过程：应用-公共-系统，
 * 那么各个独立的应该就可以共享libraries,models,views,helpers,config等文件，这里
 * 共享哪些文件可以在继承类中定义）
 */
define('PUBLICPATH', 'applications/');


/*
 * ---------------------------------------------------------------
 * PUBLIC MODEL FOLDER NAME
 * ---------------------------------------------------------------
 * 共用MODEL文件目录
 *
 */
define('PUBLIC_MODEL_PATH', PUBLICPATH . 'models/');

/*
 * ---------------------------------------------------------------
 * PUBLIC LIBRARIES FOLDER NAME
 * ---------------------------------------------------------------
 * 共用LIBRARIES文件目录
 *
 */
define('PUBLIC_LIBRARIES_PATH', PUBLICPATH . 'libraries/');

/*
 *--------------------------------------------------------------
 * PUBLIC LIBRARIES FOLDER NAME
 * --------------------------------------------------------------
 * 上传文件基础路径
 */
define('DOCROOT', $_SERVER['DOCUMENT_ROOT']);
define('temp', $_SERVER['DOCUMENT_ROOT']);

define('SERVERROOT', str_replace("\\", "/", dirname(DOCROOT)));

define('SERVERLOG', SERVERROOT . '/logs/' . APP . '/');

define('UPLOADS', SERVERROOT . '/uploads');

//FISHER 用于调试页面SQL
$gb_sql_num = 0;
$gb_sql_arr = array();
//FISHER 用于调试页面SQL


/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
require_once BASEPATH . 'core/CodeIgniter.php';


//FISHER 用于调试页面SQL
//$CI->session->set_userdata(array("query_sql"=>$gb_sql_arr));
//FISHER 用于调试页面SQL


/* End of file index.php */
/* Location: ./index.php */
