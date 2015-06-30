<?php
/*
Plugin Name: show the weather JP
Plugin URI: http://www.kagua.biz/wp/howto-wp-plugin.html
Description: If you want to display the weather for your entry in Japan, then this plugin is very useful. Set the area code, just use a short code, an icon appears in tomorrow's weather in Japan.
Author: Yoshihiko Yoshida
Version: 1.0
Author URI: http://www.kagua.biz/
*/

function ldtenki1($atts) {

extract(shortcode_atts(array('date' => '0'), $atts));

if(!get_option('tiiki')){$tiiki="140010";}
else{$tiiki=get_option('tiiki');}

if(!get_option('memo')){$memo="";}
else{$memo=get_option('memo');}

$base_url = "http://weather.livedoor.com/forecast/webservice/json/v1?city=$tiiki";
$json = file_get_contents($base_url);
$json = mb_convert_encoding($json, 'UTF-8');
$obj = json_decode($json, true);

return $memo.$obj['location']['city'].'の'.$obj['forecasts'][$date]['dateLabel'].'の天気は<img src="'.$obj['forecasts'][$date]['image']['url'].'"/>';

}

add_shortcode("ldtenki","ldtenki1");

add_action('admin_menu', 'ldtenki2');

function ldtenki2() {
  add_options_page('LDweather', '天気設定', 0, __FILE__, 'ldtenki3');
}

function ldtenki3() {

?>
<div class="wrap">
<h2>ライブドア天気情報</h2>
<p>地域コードを入れてください。詳しくは以下URLまで。※地域をたどっていき天気情報が表示されたURLの末尾の数字です。<br/>
記事本文に、[ldtenki date=2]と入れると指定地域の[2=明後日]の天気に変換されます。（0:今日、1:明日）<br/>
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
<tr valign="top">
<th scope="row">表題</th>
<td><input type="text" name="memo" value="<?php echo get_option('memo'); ?>" />　例：＜天気＞</td>
</tr>
</table>
<!--ここのhiddenも必ず入れてください。複数あるときは、page_optionsは半角カンマで区切って記述。a,b,c　など-->
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="tiiki,memo," />
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