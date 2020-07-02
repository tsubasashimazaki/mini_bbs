<?php 
session_start();
require('dbconnect.php');
// login.phpでログインしていればセッション変数に値をいれているので、それがあるかで判断
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) { //1時間後にはログアウト // $_SESSION['id']はlogin.phpを参照
  $_SESSION['time'] = time(); //投稿すれば変数を上書きしてログイン時間を伸ばせる

  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));// IDはセッションの中に記録されているので会員情報を引っ張り出す。
  $member = $members->fetch(); // ログインしているユーザーの情報
} else {
  header('Location:login.php');
  exit();
}

if(!empty($_POST)) {
  if($_POST['message'] !== '') {
    $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, created=NOW()');
    $message->execute(array(
      $member['id'], // $_SESSION['id']と同じだがデータベースから取得した方が正確性が上がる
      $_POST['message'], // 投稿したメッセージ
    ));

    header('Location:index.php'); // 投稿して再読み込みすると$_POSTが投稿データを持ち続けた状態になるので強制的に自身の画面を呼び出してリセットをかける
  }
}

// 投稿を取得するプログラム(ユーザーが入力した値ではないのでqueryメソッド)
/*
m.name mはエイリアスでmembersテーブルのname(ニックネーム)
m.picture mはエイリアスでmembersテーブルのpictureのパス名
p.* 投稿された全てのデータ
m.id=p.member_idでリレーション。　投稿者と投稿されるデータは1対多の関係になる為リレーションすること
*/
$posts = $db->query('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC'); //mやpはテーブルのショートカット名


?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
  	<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post">
      <dl>
        <!-- fetch()でログインしてる名前を表示 -->
        <dt><?php echo(htmlspecialchars($member['name'], ENT_QUOTES)) ?>さん、メッセージをどうぞ</dt>
        <dd>
          <textarea name="message" cols="50" rows="5"></textarea>
          <input type="hidden" name="reply_post_id" value="" />
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>

<?php foreach($posts as $post): //リレーションされている投稿データを一件ずつ表示 ?>

    <div class="msg">
    <img src="member_picture/<?php echo(htmlspecialchars($post['picture'], ENT_QUOTES)); //echoしたものはファイル名のみなのでディレクトリも必要?>" width="48" height="48" alt="<?php echo(htmlspecialchars($post['name'], ENT_QUOTES)); ?>" />
    <p><?php echo(htmlspecialchars($post['message'], ENT_QUOTES)); ?><span class="name">（<?php echo(htmlspecialchars($post['name'], ENT_QUOTES)); ?>）</span>[<a href="index.php?res=">Re</a>]</p>
    <p class="day"><a href="view.php?id="><?php echo(htmlspecialchars($post['created'], ENT_QUOTES)); ?></a>
<a href="view.php?id=">
返信元のメッセージ</a>
[<a href="delete.php?id="
style="color: #F33;">削除</a>]
    </p>
    </div>
<?php endforeach; ?>
<ul class="paging">
<li><a href="index.php?page=">前のページへ</a></li>
<li><a href="index.php?page=">次のページへ</a></li>
</ul>
  </div>
</div>
</body>
</html>
