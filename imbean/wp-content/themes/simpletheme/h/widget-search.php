<?php 
/*
Widget Name: Search Form
*/
if (!is_search()) {
$search_text = "搜索 ";
} else {
$search_text = $s;
}
?>
<form method="get" class="searchform" name="searchform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="text" class="search-input hfl" size="24" value="<?php echo $search_text; ?>" name="s" onfocus="if (this.value == '搜索 ') {this.value = '';}" onblur="if (this.value == '') {this.value = '搜索 ';}"/>
<input type="submit" class="search-submit hfr" value=""/>
</form>
