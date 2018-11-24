<?php
require "db.php";
?>
<!DOCTYPE html>
<html lang="ja">
<!--

    Version :  1.0.0
    Author  Soradore   https://twitter.com/soradore_
    
    -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ext-language_tools.js"></script>
    <style type="text/css">
      #code {
        margin: 30px 60px;
      } 

      html {
        background-color: #6dd15c;
      }
    </style>
<?php if(isset($_GET["id"])) : ?>
<?php 
$id = $_GET["id"];
$db = new DB();
$data = $db->getCode($id);
if($data == DB::NOT_FOUND) header("Location: https://code.mcbe.site/notfound/");
$type = $db->getType($data["type"]);
$code = $data["code"];

?>

    <title><?php echo $data["title"]; ?></title>
</head>
<body>
 <!-- ACE Editor -->
 FontSize: 
 <select name="font-size">
    <option value="5">5</option>
    <option value="10">10</option>
    <option value="15" selected>15</option>
    <option value="20">20</option>
</select>
<a href='https://code.mcbe.site'>サイトトップページへ</a>
 <div id="code" style="height: 500px; width: 80%"><?php echo htmlentities($code); ?></div>
 <script type="text/javascript">
    var editor = ace.edit("code"); 
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: true
    });
    editor.$blockScrolling = Infinity;
    editor.setTheme("ace/theme/monokai");
    editor.setFontSize(15);
    editor.getSession().setMode("ace/mode/<?=$type?>");
    editor.getSession().setTabSize(4);
 </script>
 <script type="text/javascript">
     $("[name=font-size]").change(function(){
        var size = $("[name=font-size]").val();
        editor.setFontSize(parseInt(size));
    });
 </script>
 <input type="hidden" id="id" value="<?=$id?>">
 <input type="password" id="pass" placeholder="password">
 <button type="button" id="delete">削除</button>



 <script type="text/javascript">
    <!--
    $("#delete").click(function(){
        var pass = $("#pass").val();
        var id = $("#id").val();

        //alert(pass + " / " + id);
        //return 0;
        pass = pass.replace(/\s+/g, "");
        if(pass == ""){
            alert("パスワードが未記入です");
            return false;
        }
        if(id == ""){
            alert("不正なID");
            return false;
        }

        $.ajax({
            url: './api.php',
            type: 'POST',
            data: {
                method: "delete",
                pass: pass,
                id: id,
            },
        })

        .done(function(response){
            var res = response;
            switch(res){
                case 'success':
                    alert("削除しました");
                    window.close();
                    break;
                case 'pass_faild':
                    alert("パスワードが正しくありません");
                    break;
            }
        })

        .fail(function(){
            alert("削除できませんでした");
        });

    });
    -->
 </script>
</body>
<?php else : ?>

<head>
    <title>コード共有</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ext-language_tools.js"></script>
</head>
<body>
 
 <!-- ACE Editor -->
 言語選択
 <select name="type">
    <option value="php">PHP</option>
    <option value="java">JAVA</option>
    <option value="yaml">YAML</option>
 </select>
 <div id="info"></div>
 <div id="input">
  <input type="text" id="title" value="title"><button type="button" id="save">保存</button>
 </div>
 <p>* <font color="darkblue">PHPの場合 </font><font color="red">&lt;?php </font><font color="darkblue">をつけるとハイライトされ、見やすくなります</font></p>
 <div id="code" style="height: 500px; width: 80%"></div>
 <script type="text/javascript">
    <!--
    var editor = ace.edit("code");
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: true
    });
    editor.$blockScrolling = Infinity;
    editor.setTheme("ace/theme/monokai");
    editor.setFontSize(15);
    editor.getSession().setMode("ace/mode/php");
    editor.getSession().setTabSize(4);
 </script>
 <script type="text/javascript">
    $("[name=type]").change(function(){
        var type = $("[name=type]").val();
        editor.getSession().setMode("ace/mode/" + type);
        $("#info").html("<font color='green'>" + type + "に変更しました</font>");
    });
    -->
 </script>

 <script type="text/javascript">
    $(function(){

        $("#save").click(function(){
            var type = $("[name=type]").val();
            var title = $("#title").val();
            var code = editor.getValue();
            //code = nl2br(code);
            //code = escape_html(code);
            if(code == ""){
                alert("コードが空です"); 
                return false;
            } 
            $.ajax({
                url: './api.php',
                type: 'POST',
                data: {
                    method: "save",
                    code: code,
                    title: title,
                    type: type,
                },
            }).done(function(response){console.log(response);
                var res = JSON.parse(response);
                var pass = res.pass;
                var id = res.id;
                $("#info").html("<p><font color='green'>保存しました</font></p><p><a href=https://code.mcbe.site/?id=" + id + " target='_blank'> コード </a> (コピーして共有)</p><p>削除用パスワード: <input type='text' value=" + pass + "></p>");
                $("#input").html("");
                
            }).fail(function(XMLHttpRequest, textStatus, errorThrown){
                alert(errorThrown);
            });
        });
    });
 </script>
</body>
<?php endif; ?>
</html> 
