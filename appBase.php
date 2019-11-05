function appBase_InitialData(){
    global $a;

    // $_POST & $_COOKIE
    if(isset($_POST)){
        foreach($_POST as $key => $value){
            if($key != '' && $value != ''){
                $a['post'][$key] = $value;
            }
        }
    }

    // $a['user']['id'], $a['user']['name'], query string
    $a['user']['id'] = null;
    if(isset($a['cookie']['cookie'])){
        if(appExt_clearCookie()){
            $query = 'SELECT `userID`, `cookieTime` FROM `aCookies` WHERE `cookie` = \''.$a['cookie']['cookie'].'\' LIMIT 1';
            $qr = A($query);
            if($qr['num_rows'] == 1 && ($qr['fetch_assoc']['cookieTime'] + $a['CookieLive']) > time()){
                $a['user']['id'] = $qr['fetch_assoc']['userID'];
                $query = 'UPDATE `aCookies` SET `cookieTime` = \''.time().'\' WHERE `userID` = '.$a['user']['id'].' AND `cookie` = \''.$a['cookie']['cookie'].'\' LIMIT 1';
                A($query);
                setcookie('cookie', bin2hex($a['cookie']['cookie']), (time() + $a['CookieLive']), D, $_SERVER['HTTP_HOST']);
            }
        }
    }
    if(isset($a['post']['password']) && is_null($a['user']['id'])){
        $query = 'SELECT `userID`, `password` FROM `aUsers`';
        $qr_AMS = AMS($query);
        for($i = 0; $i <= count($qr_AMS) - 1; $i++){
            if(password_verify($a['post']['password'], $qr_AMS[$i]['password'])){
                $a['user']['id'] = $qr_AMS[$i]['userID'];
                $cookie = appExt_randString($a['CookieLength']);
                $query = 'INSERT INTO `aCookies`(`userID`, `cookie`, `cookieTime`) VALUES ('.$a['user']['id'].', \''.$cookie.'\', \''.time().'\')';
                A($query);
                setcookie('cookie', bin2hex($cookie), (time() + $a['CookieLive']), D, $_SERVER['HTTP_HOST']);
                header('Location: '.INIT_URI);
                break;
            }
        }
    }
    if(!is_null($a['user']['id'])){
        $a['qsArr'] = null;
        $qs = null;
        $query= 'DELETE FROM `aSecure` WHERE `time` < '.(time() - 86400).' AND (`is_static` =\'n\' OR `is_static` =\'\')';
        A($query);
        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) >= ($a['SecureLength'] * 2)){
            $qs = $_SERVER['QUERY_STRING'];
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $postKeys = array_keys($_POST);
            for($i = 0; $i <= count($postKeys) - 1; $i++){
                if(strlen($postKeys[$i]) >= ($a['SecureLength'] * 2)){
                    $qs = $postKeys[$i];
                    break;
                }
            }
        }
        if(!is_null($qs)){
            $qs = str_split(hex2bin(substr($qs, 0, ($a['SecureLength'] * 2))));
            $allow = str_split($a['allowSymbols']);
            $checked = null;
            for($i = 0; $i <= count($qs) - 1; $i++){
                if(!in_array($qs[$i], $allow)){
                    $checked = 1;
                }
            }
            if(is_null($checked)){
                $en = implode('', $qs);
                $query = 'SELECT `decode` FROM `aSecure` WHERE `encode`=\''.$en.'\' AND `userID` = '.$a['user']['id'].' LIMIT 1';
                $qr = A($query);
                if($qr['num_rows'] == 1){
                    $de = $qr['fetch_assoc']['decode'];
                    $pair = explode('&', $qr['fetch_assoc']['decode']);
                    $pairs = array();
                    for($i = 0; $i <= count($pair) - 1; $i++){
                        if($pair[$i] != ''){
                            $keyValue = explode('=', $pair[$i]);
                            $pairs[$keyValue[0]] = $keyValue[1];
                        }
                    }
                $a['qsArr'] = $pairs;
                }
            }
        }
    }
}
