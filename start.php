include_once 'settings.php';

// all of $var1...$var3 places into $a['qsArr'] array, like this $a['qsArr']['var1']
echo '<a href="/'.e('var1=1&var2=2&var3=3', 'y').'">test &lt;a&gt;</a>';
