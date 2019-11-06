<?

// $registry = global array
///////////////////////////
$registry = array();
$registry['allowSymbols'] = '1234567890ghijklmnopqrstuvwxyzGHIJKLMNOPQRSTUVWXYZ';
$registry['mysql'] = new mysqli('localhost', 'aaa', 'aaa', 'aaa');
$registry['SecureLength'] = 32;

// execute query request
////////////////////////
function A(&$query){
    global $registry;
    switch(explode(' ',$query)[0]){
        case 'SELECT':
            $result = array();
            $query_result = $registry['mysql']->query($query);
            $result['num_rows'] = $query_result->num_rows;
            $result['fetch_assoc'] = $query_result->fetch_assoc();
            return $result;
        break;
        default:
            $registry['mysql']->query($query);
        break;
    }
}

// make random string
/////////////////////
function appExt_randString(&$length_string){
    global $registry;
		$result = '';
		$length = mt_rand(($length_string + 6), ($length_string + 36));
		for($i = 0; $i < $length; $i++){
				$result = $result.str_split($registry['allowSymbols'])[mt_rand(0, (count(str_split($registry['allowSymbols'])) - 1))];
		}
		return substr(str_shuffle($result), 0, $length_string);
}

// make encode string
/////////////////////
function e($decode, $static = 'n'){
    global $registry;
    $query = 'SELECT `encode` FROM `aSecure` WHERE `decode` = \''.$decode.'\' AND `is_static` = \''.$static.'\'  LIMIT 1';
    $query_result = A($query);
    if($query_result['num_rows'] == 1){
        $encode = $query_result['fetch_assoc']['encode'];
    }else{
        $encode = appExt_randString($registry['SecureLength']);
        $query = 'INSERT INTO `aSecure` (`encode`, `decode`, `time`, `is_static`) VALUES (\''.$encode.'\', \''.$decode.'\', '.time().', \''.$static.'\')';
        A($query);
    }
		return bin2hex($encode);
}

function appBase_InitialData(){
    global $registry;

    // create secure table
    //////////////////////
    $query = 'CREATE TABLE IF NOT EXISTS `aSecure` (
`encode` varchar('.($registry['SecureLength'] * 2 + 1).') DEFAULT NULL,
`decode` varchar(500) DEFAULT NULL,
`is_static` varchar(1) NOT NULL DEFAULT \'n\',
`time` varchar(15) DEFAULT NULL,
KEY `encode` (`encode`),
KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
    A($query);

    // parse $_POST
    ///////////////
    if(isset($_POST)){
        foreach($_POST as $key => $value){
            if($key != '' && $value != ''){
                $registry['post'][$key] = $value;
            }
        }
    }
    unset($key, $value);

    // decode query string
    // $registry['qsArr'] = array of real vars and values
    //////////////////////////////////////////////
    $registry['qsArr'] = null;

    // delete deprecated query string during last day period
    ////////////////////////////////////////////////////////
    $query= 'DELETE FROM `aSecure` WHERE `time` < '.(time() - 86400).' AND (`is_static` =\'n\' OR `is_static` =\'\')';
    A($query);
    unset($query);

    // extract query string
    ///////////////////////
    $query_string = null;
    if($_SERVER['REQUEST_METHOD'] == 'GET' && 
    isset($_SERVER['QUERY_STRING']) && 
    strlen($_SERVER['QUERY_STRING']) >= ($registry['SecureLength'] * 2)){
        $query_string = substr($_SERVER['QUERY_STRING'], 0, ($registry['SecureLength'] * 2));
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $postKeys = array_keys($_POST);
        for($i = 0; $i <= count($postKeys) - 1; $i++){
            if(strlen($postKeys[$i]) == ($registry['SecureLength'] * 2)){
                $query_string = $postKeys[$i];
                break;
            }
        }
    }
    unset($postKeys, $i);
    

    // check extracted query string
    ///////////////////////////////
    if(!is_null($query_string)){
        $query_string = str_split(hex2bin($query_string));
        for($i = 0; $i <= count($query_string) - 1; $i++){
            if(!in_array($query_string[$i], str_split($registry['allowSymbols']))){
                $query_string = null;
                break;
            }
        }
    }

    // find decode string
    /////////////////////
    if(!is_null($query_string)){
        $query = 'SELECT `decode` FROM `aSecure` WHERE `encode`=\''.implode('', $query_string).'\' LIMIT 1';
        $query_result = A($query);
        if($query_result['num_rows'] == 1){
            $pair = explode('&', $query_result['fetch_assoc']['decode']);
            $pairs = array();

            // parse decode string by key->value
            ////////////////////////////////////
            for($i = 0; $i <= count($pair) - 1; $i++){
                if($pair[$i] != ''){
                    $keyValue = explode('=', $pair[$i]);
                    $pairs[$keyValue[0]] = $keyValue[1];
                }
            }
        $registry['qsArr'] = $pairs;
        }
    }
}

?>
