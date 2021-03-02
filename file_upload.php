<?php

require_once "./dbc.php";

$file = $_FILES['img'];

$filename = basename($file['name']);
$tmp_path = $file['tmp_name'];
$file_err = $file['error'];
$filesize = $file['size'];
$upload_dir = './images/';

$save_filename = date('YmdHis').$filename;
$err_msgs = array();
$save_path = $upload_dir.$save_filename;

$caption = filter_input(INPUT_POST,'caption',
FILTER_SANITIZE_SPECIAL_CHARS);

//キャプションのバリデーション
//未入力

if(empty($caption)){
    array_push($err_msgs,'キャプションを入力して下さい');
    echo'<br>';
}

if(strlen($caption)>140){
    array_push($err_msgs,'キャプションは140文字以内で入力してください');
    echo'<br>';
}

//ファイルのバリデーション
//ファイルサイズが1MB未満か
if($filesize > 1048576 || $file_err == 2){
   echo 'ファイルサイズは1MB未満にしてください';
   array_push($err_msgs,'キャプションを入力して下さい');
   echo'<br>';
}

//拡張は画像形式か
$allow_ext = array('jpg','jpeg','png');
$file_ext = pathinfo($filename,PATHINFO_EXTENSION);

if(!in_array(strtolower($file_ext),$allow_ext)){
    array_push($err_msgs,'キャプションを入力して下さい');
    echo '画像ファイルを添付してください';
    echo'<br>';
}

if(count($err_msgs)===0){
//ファイルはあるかどうか?

if(is_uploaded_file($tmp_path)){
    if(move_uploaded_file($tmp_path,$save_path)){
        echo $filename.'を'.$upload_dir.'アップしました';

        //DBに保存(ファイル名,ファイルパス,キャプション)
        $result = fileSave($filename,$save_path,$caption);

        if($result){
            echo 'データベースに保存しました';
        }else{
            echo 'データベースへの保存が失敗しました';
        }
    }else{
        echo 'ファイルが保存できませんでした';
    }
     
}else{
    echo'ファイルが選択されていません';
    echo'<br>';
}
}else{
    foreach($err_msgs as $msg){
        echo $msg;
    }
}

?>
<a href="./upload_form.php">戻る</a>
