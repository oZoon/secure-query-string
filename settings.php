define ('D', DIRECTORY_SEPARATOR);
define ('INIT_URI', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].D);

$a = array();
$a['allowSymbols'] = '1234567890ghijklmnopqrstuvwxyzGHIJKLMNOPQRSTUVWXYZ';
$a['mysql'] = new mysqli('localhost', 'aaa', 'aaa', 'aaa');

include_once 'appExt.php';
include_once 'appBase.php';
