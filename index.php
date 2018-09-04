<?php
require "db.php";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <link type="text/css" rel="stylesheet" href="./syntaxhighlighter/styles/shCore.css" />
    <link type="text/css" rel="stylesheet" href="./syntaxhighlighter/styles/shThemeDefault.css" />

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="./syntaxhighlighter/scripts/shCore.js"></script>
    <script type="text/javascript" src="./syntaxhighlighter/scripts/shBrushPhp.js"></script>
    <script type="text/javascript" src="./syntaxhighlighter/scripts/shBrushJava.js"></script>
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
if($data == DB::NOT_FOUND) exit("IDが見つかりませんでした");
$type = $db->getType($data["type"]);
$code = $data["code"];

?>

    <title><?php echo $data["title"]; ?></title>
</head>
<body>
 <!-- ACE Editor -->
 <div class="brush: <?=$type?>">
    <?php echo "<pre>" . htmlentities($code) . "</pre>";?>
 </div>

 <input type="hidden" id="id" value="<?=$id?>">
 <input type="password" id="pass" placeholder="password">
 <button type="button" id="delete">削除</button>
 <!-- <script>
     $('#code').prepend('<pre class="brush: <?=$type?>">' + '<?=$code?>' + '</pre>');
 </script> -->



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
                    //location.replace("http://code.pocketmp.xyz");
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.0/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.0/ext-language_tools.js"></script>
</head>
<body>
 
 <!-- ACE Editor -->
 言語選択
 <select name="type">
    <option value="php">PHP</option>
    <option value="java">JAVA</option>
 </select><div id="info"></div>
 <input type="text" id="title" placeholder="title"><button type="button" id="save">保存</button>
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
    editor.setFontSize(14);
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
   function escape_html (string) {
      if(typeof string !== 'string') {
        return string;
    }
    return string.replace(/[&'`"<>]/g, function(match) {
        return {
          '&': '&amp;',
          "'": '&#x27;',
          '`': '&#x60;',
          '"': '&quot;',
          '<': '&lt;',
          '>': '&gt;',
      }[match]
  });
}

 function nl2br(str) {
    str = str.replace(/\r\n/g, "<br />");
    str = str.replace(/(\n|\r)/g, "<br />");
    return str;
}
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
                $("#info").html("<p><font color='green'>保存しました</font></p><p><a href=http://localhost:8080/?id=" + id + "> コード </a>password: <input type='text' value=" + pass + " readonly></p>");
                
            }).fail(function(XMLHttpRequest, textStatus, errorThrown){
                alert(errorThrown);
            });
        });
    });
 </script>
</body>
<?php endif; ?>
</html> 