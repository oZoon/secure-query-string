<?
include_once 'settings.php';
appBase_InitialData();

echo '<a href="/'.e('var1=1&var2=2&var3=3', 'y').'">test</a><br>';

echo '
<form method="post" action="/">
<input type="hidden" name="'.e('var4=4&var5=5&var6=6').'">
<input type="text" name="text">
<button>test</button>
</form>';

echo '<plaintext>';
if(isset($registry['qsArr'])){
    var_dump($registry['qsArr']);
}
if(isset($registry['post'])){
    var_dump($registry['post']);
}
?>
