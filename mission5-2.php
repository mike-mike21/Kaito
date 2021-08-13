   <meta charset="UTF-8">
    <title>mission</title>
</head>

<?php
 //データベース接続//
    $dsn = 'データーベース名';
    $user = 'ユーザー名';
    $password = 'パスワ';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
//テーブル作成//
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql = "CREATE TABLE IF NOT EXISTS tbtest2"//もしまだこのテーブルが存在しないなら//
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"//id ・自動で登録されていうナンバリング。//
    . "name char(32),"//名前を入れる。文字列、半角英数で32文字//
    . "message TEXT,"//コメントを入れる。文字列、長めの文章も入る。//
    . "date TEXT,"
    . "password char(32)"
    .");";
    $stmt = $pdo->query($sql);
    
   
    
    //新規投稿と編集//
    if(isset($_POST["number"],$_POST["name"],$_POST["message"],$_POST["password"])){
        
        $number1 = $_POST["number"];
        $name1 = $_POST["name"];
        $message1 = $_POST["message"];
        $password1 = $_POST["password"];
        $date1 = date("Y/m/d/　H:i:s");
        
        //新規投稿//
        if($number1=="" && $name1!="" && $message1!="" && $password1!=""){
            
            //データベースに投稿内容を書き込み//
            $sql = $pdo -> prepare("INSERT INTO tbtest2 (name,message,password,date) VALUES (:name,:message,:password,:date)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':message', $message, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $number = $number1;
            $name = $name1;
            $message = $message1;
            $password = $password1;
            $date = $date1;
            $sql -> execute();
            
            
        //編集の時//
        }elseif($number1!="" && $name1!="" && $message1!="" && $password1!=""){
            //UPDATE文で編集する。//
            $id = $number1; //変更する投稿番号
            $name = $name1;//変更したい名前//
            $message = $message1; //変更したいコメント//
            $password = $password1;
            $sql = 'UPDATE tbtest2 SET name=:name,message=:message,password=:password WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':message', $message, PDO::PARAM_STR);
            $stmt->bindParam(':password',$password1,PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    //編集//
    if(isset($_POST["editNumber"],$_POST["editPassword"])){
        $editNumber = $_POST["editNumber"];
        $editPassword = $_POST["editPassword"];
        
        //データを取得する//
        $sql = 'SELECT * FROM tbtest2';//WHERE id=:idなどと付け加えると条件を絞れる//
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        //パスワードが一致したら//
        foreach ($results as $row){
            if($row['id']==$editNumber && $row['password']==$editPassword){
                $editName = $row['name'];
                $editMessage = $row['message'];
                $editPass = $row['password'];
            }
        }
    
    }
    
    //削除//
    
    if(isset($_POST["delete"],$_POST["deletePassword"])){
        
        $delete = $_POST["delete"];
        $deletePassword = $_POST["deletePassword"];
        
        //フォームに投稿番号がとパスワードが入力された時//
        if($delete!="" && $deletePassword!=""){

            //データを取得する//
            $sql = 'SELECT * FROM tbtest2';//WHERE id=:idなどと付け加えると条件を絞れる//
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            //パスワードがあってたら//
            foreach($results as $row){
                if($row['id']==$delete && $row['password']==$deletePassword){
                     //DELETE文で削除する。//
                    $id = $delete;
                    $sql = 'delete from tbtest where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $deleteAnnounce = $delete;
                }
            }
        }  
    }
    
    
     
    
    ?>

<body>
    "投稿はこちら"<br>
    <form action="" method="post">
        <input type="hidden" name="number" value="<?php if(isset($editNumber)){echo $editNumber;} ?>" >
        <input type="text" name="name" placeholder="名前" value="<?php if(isset($editName)){echo $editName;} ?>" >
        <input type="text" name="message" placeholder="内容" value="<?php if(isset($editMessage)){echo $editMessage;} ?>">
        <input type="text" name="password" placeholder="パスワード" value="<?php if(isset($editPassword)){echo $editPassword;} ?>"><br>
        <input type="submit" name="submit" value="送信">
    </form>
    "削除はこちら"<br>
    <form action="" method="post">
        <input type="number" name="delete" placeholder="削除番号"><br>
        <input type="text" name="deletePassword" placeholder="パスワード"> <br>
        <input type="submit" name="submit" value="送信">
    </form>
    "編集はこちら"<br>
    <form action="" method="post">
        <input type="number" name="editNumber" placeholder="編集する投稿番号"> <br>
        <input type="text" name="editPassword" placeholder="パスワード" >  <br>
        <input type="submit" name="submit" value="編集">
    </form>
    
    <?php
    //ファイルを取得//
    
    //アナウンス（新規投稿と編集）//
    if(isset($_POST["number"],$_POST["name"],$_POST["message"],$_POST["password"])){
        //新規投稿//
        if($number1==""){
            if($name1!="" && $message1!="" && $password1!=""){
                echo "投稿されました！<br><br>";
            }elseif($name1=="" && $message1!="" && $password1!=""){
                echo "名前が入力されていません。<br><br>";
            }elseif($name1!="" && $message1=="" && $password1!=""){
                echo "投稿内容が入力されていません。<br><br>";
            }elseif($name1!="" && $message1!="" && $password1==""){
                echo "パスワードが入力されていません。<br><br>";
            }elseif($name1=="" && $message1=="" && $password1!=""){
                echo "名前と投稿内容が入力されていません。<br><br>";
            }elseif($name1=="" && $message1!="" && $password1==""){
                echo "名前とパスワードが入力されていません。<br><br>";
            }elseif($name1!="" && $message1=="" && $password1==""){
                echo "投稿内容とパスワードが入力されていません。<br><br>";
            }elseif($name1=="" && $message1=="" && $password1==""){
                echo "フォームに入力してください。<br><br>";
            }
        //編集//
        }elseif($number1!=""){
            if($name1!="" && $message1!="" && $password1!=""){
                echo $number1."の投稿が編集されました！<br><br>";
            }elseif($name1=="" && $message1!="" && $password1!=""){
                echo "名前が入力されていません。<br><br>";
            }elseif($name1!="" && $message1=="" && $password1!=""){
                echo "投稿内容が入力されていません。<br><br>";
            }elseif($name1!="" && $message1!="" && $password1==""){
                echo "パスワードが入力されていません。<br><br>";
            }elseif($name1=="" && $message1=="" && $password1!=""){
                echo "名前と投稿内容が入力されていません。<br><br>";
            }elseif($name1=="" && $message1!="" && $password1==""){
                echo "名前とパスワードが入力されていません。<br><br>";
            }elseif($name1!="" && $message1=="" && $password1==""){
                echo "投稿内容とパスワードが入力されていません。<br><br>";
            }elseif($name1=="" && $message1=="" && $password1==""){
                echo "フォームに入力してください。<br><br>";
            }
        }
    }
    
    
    //アナウンス（削除）//
    if(isset($_POST["delete"],$_POST["deletePassword"])){
        
        if($delete!="" && $deletePassword!=""){
            
            $sql = 'SELECT * FROM tbtest2';//WHERE id=:idなどと付け加えると条件を絞れる//
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if(isset($deleteAnnounce)){
                    echo $deleteAnnounce."の投稿を削除しました。<br><br>";
                }elseif($row['id']==$delete && $row['password']!=$deletePassword){
                    echo "パスワードが間違っています。<br><br>";
                }
            }
            
        }elseif($delete!="" && $deletePassword==""){
            echo "パスワードを入力してください。<br><br>";
        }elseif($delete=="" && $deletePassword!=""){
            echo "削除したい投稿番号を入力してください。<br><br>";
        }else{
            echo "フォームに入力してください。<br><br>";
        }
    }
    //アナウンス（編集）//
    if(isset($_POST["editNumber"],$_POST["editPassword"])){
        
        if($editNumber!="" && $editPassword!=""){
            
            $sql = 'SELECT * FROM tbtest2';//WHERE id=:idなどと付け加えると条件を絞れる//
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if($row['id']==$editNumber && $row['password']==$editPassword){
                    echo $editNumber."の投稿を編集します。<br><br>";
                }elseif($row['id']==$editNumber && $row['password']!=$editPassword){
                    echo "パスワードが間違っています。<br><br>";
                }
            }
            
        }elseif($editNumber!="" && $editPassword==""){
            echo "パスワードを入力してください。<br><br>";
        }elseif($editNumber=="" && $editPassword!=""){
            echo "編集したい投稿番号を入力してください。<br><br>";
        }else{
            echo "フォームに入力してください。<br><br>";
        }
    }
  
    //ファイルの内容を表示// 
    //データを取得して表示する//
    $sql = 'SELECT * FROM tbtest2';//WHERE id=:idなどと付け加えると条件を絞れる//
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].'<br>';
    echo $row['name'].'<br>';
    echo $row['message'].'<br>';
    echo $row['date'].'<br>';
    echo "<hr>";
}
?>
    

</body>
</html>