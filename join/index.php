<?php 
session_start();
// リクエスト送信された時にが空だったらエラー
if(!empty($_POST)){

	if($_POST['name'] === ''){
		$error['name'] = 'blank';
	}
	if($_POST['email'] === ''){
		$error['email'] = 'blank';
	}
	if(strlen($_POST['password']) <= 8){
		$error['password'] = 'length';
	}
	if($_POST['password'] === ''){
		$error['password'] = 'blank';
	}
	
	
	$fileName = $_FILES['image']['name']; // アップロードされたファイル名
	if (!empty($fileName)) {
		// ファイルの拡張子を3文字切り取り
		$ext = substr($fileName, -3);
		if ( $ext != 'jpg' && $ext != 'png' && $ext != 'gif') {
			$error['image'] = 'type';
		}
	}
	
	if(empty($error)){
		// アップロード時にファイル名が被らないようにする
		$image = date('YmdHis') . $_FILES['image']['name'];
		// $_FILES(グローバル変数,inputのファイルから得られたデータ) 第二引数に保存先
		move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/' . $image);
		$_SESSION['join'] = $_POST;
		$_SESSION['join']['image'] = $image;
		// check.phpにジャンプする命令
		header('Location: check.php');
		exit();
	}
}

	// check.phpのhistory.back時
if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])) {

	// check.phpで$_SESSEIONで保存された内容を$_POSTに保管 → $_POSTは''ではないのでエラーは出ないし、valueで値が出力される
	$_POST = $_SESSION['join'];
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>次のフォームに必要事項をご記入ください。</p>
<!-- ファイルのアップロードにはecctype...決まり文句 -->
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="name" size="35" maxlength="255" value="<?php echo(htmlspecialchars($_POST['name'],ENT_QUOTES)); ?>" />
			<?php if($error['name'] === 'blank'): ?>
			<p class="error">＊ニックネームは必須です！</p>
			<?php endif; ?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="email" size="35" maxlength="255" value="<?php echo(htmlspecialchars($_POST['email'],ENT_QUOTES)); ?>" />
			<?php if($error['email'] === 'blank'): ?>
				<p class="error">*メールアドレスは必須です。</p>
			<?php endif; ?>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
        	<input type="password" name="password" size="10" maxlength="20" value="<?php echo (htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />
			<?php if($error['password'] === 'blank'): ?>
			<p class="error">*パスワードは必須です。</p>
			<?php endif; ?>
			<?php if($error['password'] === 'length'): ?>
			<p class="error">*パスワードは8文字以上で入力してください。</p>
			<?php endif; ?>
        </dd>
		<dt>写真など</dt>
		<dd>
        	<input type="file" name="image" size="35" value="test"  />
			<?php if($error['image'] === 'type'): ?>
				<p class="error">*写真などは「.gif」または「.png, jpg」の画像を指定してください。</p>
			<?php endif; ?>
			<?php if(!empty($error)):  ?>
			<p class="error">恐れ入りますがもう一度画像を指定してください。</p>
			<?php endif; ?>
			<?php 
			if ($_REQUEST['action'] === 'rewrite'): ?>
				<p class="error">恐れ入りますがもう一度画像を指定してください。</p>
			<?php endif;?>
			
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
