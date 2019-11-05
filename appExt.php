function A(&$query){
    global $a;
    switch(explode(' ',$query)[0]){
        case 'SELECT':
            $result = array();
            $qr = $a['mysql']->query($query);
            $result['num_rows'] = $qr->num_rows;
            $result['fetch_assoc'] = $qr->fetch_assoc();
            return $result;
        break;
        default:
            $a['mysql']->query($query);
        break;
    }
}

function AMS(&$query){
    global $a;
    $result = array();
    $qr = $a['mysql']->query($query);
    while($temp = $qr->fetch_assoc()){
        $result[] = $temp;
    }
    return $result;
}

function appExt_randString(&$l){
    global $a;
		$result = '';
		$allow = str_split($a['allowSymbols']);
		$length = mt_rand(($l + 6), ($l + 36));
		for($i = 0; $i < $length; $i++){
				$result = $result.$allow[mt_rand(0, (count($allow) - 1))];
		}
		return substr(str_shuffle($result), 0, $l);
}

function e($in, $static = 'n'){
    global $a;
    $query = 'SELECT `encode` FROM `aSecure` WHERE `decode` = \''.$in.'\' AND `userID` = '.$a['user']['id'].' AND `is_static` = \''.$static.'\'  LIMIT 1';
    $qr = A($query);
    if($qr['num_rows'] == 1){
        $out = $qr['fetch_assoc']['encode'];
    }else{
        $out = appExt_randString($a['SecureLength']);
        $query = 'INSERT INTO `aSecure` (`userID`, `encode`, `decode`, `time`, `is_static`) VALUES ('.$a['user']['id'].', \''.$out.'\', \''.$in.'\', '.time().', \''.$static.'\')';
        A($query);
    }
		return bin2hex($out);
}

function appExt_clearCookie(){
    global $a;
    $q = str_split(substr(hex2bin(substr($a['cookie']['cookie'], 0, ($a['CookieLength'] * 2))), 0, $a['CookieLength']));
    $allow = str_split($a['allowSymbols']);
    $checked = true;
    for($i = 0; $i <= count($q) - 1; $i++){
        if(!in_array($q[$i], $allow)){
            $checked = false;
        }
    }
    if($checked){
        $a['cookie']['cookie'] = implode('', $q);
        return true;
    }else{
        return false;
    }
}
