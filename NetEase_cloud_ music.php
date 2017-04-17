
<?php
  //error_reporting(0);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <!-- Material Design fonts -->
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">

    <!-- Bootstrap -->
    <link href="http://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Material Design -->
    <link href="http://cdn.bootcss.com/bootstrap-material-design/0.5.10/css/bootstrap-material-design.min.css" rel="stylesheet">

    <title>网易云音乐外链解析下载器</title>

    <style>
    .withripple{position:relative}.ripple-container{position:absolute;top:0;left:0;z-index:1;width:100%;height:100%;overflow:hidden;border-radius:inherit;pointer-events:none}.ripple{position:absolute;width:20px;height:20px;margin-left:-10px;margin-top:-10px;border-radius:100%;background-color:#000;background-color:rgba(0,0,0,.05);-webkit-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transform:scale(1);-webkit-transform-origin:50%;-ms-transform-origin:50%;-o-transform-origin:50%;transform-origin:50%;opacity:0;pointer-events:none}.ripple.ripple-on{-webkit-transition:opacity .15s ease-in 0s,-webkit-transform .5s cubic-bezier(.4,0,.2,1) .1s;-o-transition:opacity .15s ease-in 0s,-o-transform .5s cubic-bezier(.4,0,.2,1) .1s;transition:opacity .15s ease-in 0s,transform .5s cubic-bezier(.4,0,.2,1) .1s;opacity:.1}.ripple.ripple-out{-webkit-transition:opacity .1s linear 0s!important;-o-transition:opacity .1s linear 0s!important;transition:opacity .1s linear 0s!important;opacity:0}
      /*# sourceMappingURL=ripples.min.css.map */
      /* Sticky footer styles
      -------------------------------------------------- */
      html {
        position: relative;
        min-height: 100%;
      }
      body {
        /* Margin bottom by footer height */
        margin-bottom: 60px;
      }
      .footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        /* Set the fixed height of the footer here */
        height: 50px;
        background-color: #009688;
      }     
      

      /* Custom page CSS
      -------------------------------------------------- */
      /* Not required for template or sticky footer method. */      

      body > .container {
        padding: 60px 15px 0;
      }
      .container .text-muted {
        margin: 20px 0;
      }     

      .footer > .container {
        padding-right: 15px;
        padding-left: 15px;
      }     

      code {
        font-size: 80%;
      }
              body{font-family:"Roboto",等线,"Times New Roman",Arial;}
              div.song_cover{
                  position:relative;
                  left:10px;
                  top:-80px;
                  width:20%;
                  height:20%;
              }
              div.song_info{
                  position:relative;
                  left:25%;
                  top:30px;
              }
              div.panel-body{
                  position:relative;
              }
    </style>
  </head>
  <body>
    <div class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="/">网易云音乐外链解析</a>
        </div>
      </div>
    </div>
    <script src="http://cdn.bootcss.com/clipboard.js/1.6.1/clipboard.min.js"></script>
    <style type="text/css">
      .radio label{
        /*color: #333!important;*/
      }
      .checkbox, .radio{
        /*display: inline!important;*/
      }
    </style>
    <div class="container">
      <div class="jumbotron">
        <h3>选择解析ID的类型</h3>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
          <div class="form-group">
            <div class="radio">
              <label>
                <input type="radio" name="type" value="song" <?php if ($_POST['type']=="song" || $_POST['type'] == null) echo "checked";?>>
                歌曲
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="type" value="album" <?php if ($_POST['type']=="album") echo "checked";?>>
                专辑
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="type" value="artist" <?php if ($_POST['type']=="artist") echo "checked";?>>
                艺人
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="type" value="collect" <?php if ($_POST['type']=="collect") echo "checked";?>>
                歌单
              </label>
            </div>
          </div>
            <h3>输入地址中的ID</h3>
            <div class="form-group label-placeholder is-empty">
                <input type="text" class="form-control" id="input" name="id" placeholder="多个id用英文,分隔开" value="<?php echo $_POST["id"] ?>">
                <span class="help-block">URL形如<code>http://music.163.com/#/song?id=22635188</code>ID即为<code>id=</code>后面的数字</span>
            </div>

          <input type="submit" class="btn btn-raised btn-primary">

        </form>
        <br>
        
<?php 
    /**
     * 从netease中获取歌曲信息
     * 
     * @link https://github.com/webjyh/WP-Player/blob/master/include/player.php
     * @param unknown $id 
     * @param unknown $type 获取的id的类型，song:歌曲,album:专辑,artist:艺人,collect:歌单
     */
    function get_netease_music($id, $type = 'song'){
        $return = false;
        switch ( $type ) {
            case 'song': $url = "http://music.163.com/api/song/detail/?ids=[$id]"; $key = 'songs'; break;
            case 'album': $url = "http://music.163.com/api/album/$id?id=$id"; $key = 'album'; break;
            case 'artist': $url = "http://music.163.com/api/artist/$id?id=$id"; $key = 'artist'; break;
            case 'collect': $url = "http://music.163.com/api/playlist/detail?id=$id"; $key = 'result'; break;
            default: $url = "http://music.163.com/api/song/detail/?ids=[$id]"; $key = 'songs';
        }

        if (!function_exists('curl_init')) return false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Cookie: appver=2.0.2' ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, 'http://music.163.com/;');
        $cexecute = curl_exec($ch);
        curl_close($ch);

        if ( $cexecute ) {
            $result = json_decode($cexecute, true);
            if ( $result['code'] == 200 && $result[$key] ){

                switch ( $key ){
                    case 'songs' : $data = $result[$key]; break;
                    case 'album' : $data = $result[$key]['songs']; break;
                    case 'artist' : $data = $result['hotSongs']; break;
                    case 'result' : $data = $result[$key]['tracks']; break;
                    default : $data = $result[$key]; break;
                }

                //列表
                $list = array();
                foreach ( $data as $keys => $data ){

                    $list[$data['id']] = array(
                            'title' => $data['name'],
                            'artist' => $data['artists'][0]['name'],
                            'location' => str_replace('http://m', 'http://p', $data['mp3Url']),
                          
                    );
                }
                //修复一次添加多个id的乱序问题
                if ($type = 'song' && strpos($id, ',')) {
                    $ids = explode(',', $id);
                    $r = array();
                    foreach ($ids as $v) {
                        if (!empty($list[$v])) {
                            $r[] = $list[$v];
                        }
                    }
                    $list = $r;
                }
                //最终播放列表
                $return = $list;
            }
        } else {
            $return = array('status' =>  false, 'message' =>  '非法请求');
        }
        return $return;
    }


$isEchoResult = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = $_POST["id"];
    $resultList = explode(",", $input);
    $result="";
    foreach ($resultList as $key => $value) {
        if(is_numeric($value) == false){
          echo "<span class=\"label label-danger\">输入的ID含有非法字符</span> <br>";
          $isEchoResult = false;
          break;
        }
        $musicList = get_netease_music($value,$type=$_POST["type"]);
        foreach($musicList as $x=>$x_value) {
            $result .= "{";
            foreach ($x_value as $key => $value) {
                if ($key == 'location') {
                    $key = 'mp3';
                }
                if ($key == 'pic') {
                    $key = 'cover';
                }
                if (strpos($value, '"') !== false) {
                    $value = addcslashes($value, '"');
                }
                $result .= "$key:\"". $value."\",";
            }
            $result .= "},<br>";
        }
    }
    if($isEchoResult == true){
      echo "<div class=\"panel panel-default\"><div class=\"panel-heading\">解析地址</div><div id=\"copyme\" class=\"panel-body\">".$result."</div></div><button  data-clipboard-target=\"#copyme\" class=\"btn btn-raised btn-success\" aria-label=\"复制成功！\">复制地址<div class=\"ripple-container\"></div></button>";
    }

}
?>
        
        
      </div>
    </div>

        <footer class="footer">
        <div class="container">
            <div align="center" style="color:rgba(255,255,255, 0.84)">
              <br>
              Copyright @ <?php echo date("Y") ?>
            </div>
        </div>
    </footer>
    <script src="//cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap-material-design/0.5.10/js/material.min.js"></script>
    <script type="text/javascript">
      $.material.init()
      var clipboard = new Clipboard('.btn');
      clipboard.on('success', function(e) {
          console.info('Action:', e.action);
          console.info('Text:', e.text);
          console.info('Trigger:', e.trigger);      

          e.clearSelection();
      });
    </script>
  </body>
</html>

