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
