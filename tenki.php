<?php
/*
Plugin Name: show the weather JP
Plugin URI: http://www.kagua.biz/wp/howto-wp-plugin.html
Description: 概要を書きます。名前はWORDPRESS公式サイトのプラグイン検索などで検索して、被らないようにしましょうね！
Author: 作者名
Version: 1.0
Author URI: http://www.kagua.biz/
Description: 説明その２。長くなってもＯＫです。
とりあえず属性地を何日後の指定にして、設定画面では地域をいれます。
［ldtenki date="1"］とかにしてみましょう。ライブドア天気以外にも、写真共有サイトの
フォト蔵さんなども扱いやすいAPIですので、ぜひチャレンジしてみて下さい！
*/

//◆１．メイン部分　◆
//ショートコード定義のメイン部分
function ldtenki1($atts, $content = null) {

//無効な値ならデフォルトを今日（０）にしちゃいます。
extract(shortcode_atts(array('date' => '0'), $atts));

//あさって以上は取得できないので、それ以外は今日にしちゃいます。
if($date=='0'){$date="today";}
elseif($date=='1'){$date="tomorrow";}
elseif($date=='2'){$date="dayaftertomorrow";}
else{$date="dayaftertomorrow";}

//設定画面が空欄なら何を入れておくか。デフォルト値ですね。そうで無ければ読み込みます。
//get_option()で設定値を読み込めます。
if(!get_option('tiiki')){$tiiki="113";}
else{$tiiki=get_option('tiiki');}

//ライブドア天気APIのXMLを取得します。
$req = "http://weather.livedoor.com/forecast/webservice/rest/v1?city=".$tiiki."&day=".$date;

//リクエストURIをシンプルXML命令で読み込みます。
$xml = simplexml_load_file ($req);

//RETURNの部分がショートコードに置き換わります。$xml->image->urlとすると、XML内のURLを取得できます。
//$xml->image->url（天気アイコンのＵＲＬ）、$xml->title（天気のタイトル）
return "<div align=\"center\" class=\"livedoorweather\"><img src='".$xml->image->url."'/><br/>".$xml->title."</div>";
}

//◆２．ショートコード定義　◆
//ショートコード設定を設定します。前述のfunctionと被ってるはずです。
add_shortcode("ldtenki","ldtenki1");


//◆３．設定画面定義　◆
//管理ページの定義をします。admin_menuとするとＯＫです。
add_action('admin_menu', 'ldtenki2');

//前述のメニュー名と被っているはずです。
function ldtenki2() {
  add_options_page('LDweather', '明日の天気', 0, __FILE__, 'ldtenki3');
}

//前述のメニュー追加のfunctionと被ってるはずです。
//一旦PHPが切れますが、タグを書くからです。
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

<!--//最後は閉じます。-->
<?php
 
}

?>