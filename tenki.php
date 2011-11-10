<?php
/*
Plugin Name: show the weather JP
Plugin URI: http://www.kagua.biz/wp/howto-wp-plugin.html
Description: If you want to display the weather for your entry in Japan, then this plugin is very useful. Set the area code, just use a short code, an icon appears in tomorrow's weather in Japan.
Author: Yoshihiko Yoshida
Version: 1.0
Author URI: http://www.kagua.biz/
*/

function ldtenki1($atts, $content = null) {

extract(shortcode_atts(array('date' => '0'), $atts));

if($date=='0'){$date="today";}
elseif($date=='1'){$date="tomorrow";}
elseif($date=='2'){$date="dayaftertomorrow";}
else{$date="dayaftertomorrow";}

if(!get_option('tiiki')){$tiiki="113";}
else{$tiiki=get_option('tiiki');}

$req = "http://weather.livedoor.com/forecast/webservice/rest/v1?city=".$tiiki."&day=".$date;

$xml = simplexml_load_file ($req);

return "<div align=\"center\" class=\"livedoorweather\"><img src='".$xml->image->url."'/><br/>".$xml->title."</div>";
}

add_shortcode("ldtenki","ldtenki1");

add_action('admin_menu', 'ldtenki2');

function ldtenki2() {
  add_options_page('LDweather', '明日の天気', 0, __FILE__, 'ldtenki3');
}

function ldtenki3() {

?>
<div class="wrap">
<h2>ライブドア天気情報</h2>
<p>地域コードを入れてください。詳しくは以下URLまで。※地域をたどっていき天気情報が表示されたURLの末尾の数字です。<br/>
<a href="http://weather.livedoor.com/" target="_blank"><strong>天気予報 きょうの気象情報 - livedoor 天気情報</strong></a></p>
<!--FORM文はこのまま使います-->
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<table>
<tr valign="top">
<th scope="row">地域ＩＤ</th>
<!--INPUT文のNAME属性を前述の変数と合わせます。-->
<td><input type="text" name="tiiki" value="<?php echo get_option('tiiki'); ?>" /></td>
</tr>
</table>
<!--ここのhiddenも必ず入れてください。複数あるときは、page_optionsは半角カンマで区切って記述。a,b,c　など-->
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="tiiki" />
<p class="submit">
<!--SUBMITは英語で表記。_eで翻訳されるんです。便利！-->
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
</div>

<!--//
<?php
 
}

?>