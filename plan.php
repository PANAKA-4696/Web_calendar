<?php
$date = isset($_GET['date']) ? htmlspecialchars($_GET['date'], ENT_QUOTES, 'UTF-8') : '';
session_start();

// セッションから値を取得、なければ空文字
$before_time = isset($_SESSION["before_time"]) ? htmlspecialchars($_SESSION["before_time"], ENT_QUOTES, 'UTF-8') : '';
$after_time = isset($_SESSION["after_time"]) ? htmlspecialchars($_SESSION["after_time"], ENT_QUOTES, 'UTF-8') : '';
$content = isset($_SESSION["content"]) ? htmlspecialchars($_SESSION["content"], ENT_QUOTES, 'UTF-8') : '';
$place = isset($_SESSION["place"]) ? htmlspecialchars($_SESSION["place"], ENT_QUOTES, 'UTF-8') : '';
$url = isset($_SESSION["url"]) ? htmlspecialchars($_SESSION["url"], ENT_QUOTES, 'UTF-8') : '';
$file_info = isset($_SESSION["file"]) ? $_SESSION["file"] : ''; // ファイル情報はHTMLエスケープしない
$memo = isset($_SESSION["memo"]) ? htmlspecialchars($_SESSION["memo"], ENT_QUOTES, 'UTF-8') : '';
$errors = isset($_SESSION["errors"]) ? $_SESSION["errors"] : [];

// カレンダーページに戻る処理
if(isset($_GET["back"]) && $_GET["back"] == 1){
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location:index.php");
    exit();
}

// フォーム送信後に戻ってきた場合のためにセッションデータをクリア
unset($_SESSION["before_time"], $_SESSION["after_time"], $_SESSION["content"], $_SESSION["place"], $_SESSION["url"], $_SESSION["file"], $_SESSION["memo"], $_SESSION["errors"]);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $date; ?> 予定フォーム</title>
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <div class="container mt-50">
            <p class="back-link"><a href="index.php?back=1">カレンダーページに戻る</a></p>
            <h1><?php echo $date; ?></h1>
            <h3>予定フォーム</h3>

            <div class="form-container">
                <?php
                if(count($errors) > 0){
                    echo '<div class="error-messages-container">';
                    foreach($errors as $error){
                        echo '<p class="error-message">'.htmlspecialchars($error, ENT_QUOTES, 'UTF-8').'</p>';
                    }
                    echo '</div>';
                }
                ?>
                <form method="POST" action="confirm.php" enctype="multipart/form-data">
                    <table class="form-table center-block">
                        <tr>
                            <td>
                                <ul>
                                    <li><label for="before_time" class="text-danger">開始時間<small>必須</small></label></li>
                                    <li><input type="time" name="before_time" value="<?php echo $before_time; ?>" id="before_time"></li>
                                </ul>
                                <ul>
                                    <li><label for="after_time" class="text-info">終了時間</label></li>
                                    <li><input type="time" name="after_time" value="<?php echo $after_time; ?>" id="after_time"></li>
                                </ul>
                                <ul>
                                    <li><label for="content" class="text-danger">予定内容<small>必須</small></label></li>
                                    <li><input type="text" name="content" placeholder="内容を入力" value="<?php echo $content; ?>" id="content"></li>
                                </ul>
                            </td>
                            <td style="width: 50px;"></td> <td>
                                <ul>
                                    <li><label for="place" class="text-info">場所</label></li>
                                    <li><input type="text" name="place" placeholder="例: 日本工学院専門学校" value="<?php echo $place; ?>" id="place"></li>
                                </ul>
                                <ul>
                                    <li><label for="url" class="text-info">URL</label></li>
                                    <li><input type="url" name="url" placeholder="例: https://www.neec.ac.jp/" value="<?php echo $url; ?>" id="url"></li>
                                </ul>
                                <ul>
                                    <li><label for="file" class="text-info">ファイル</label></li>
                                    <li><input type="file" name="file" id="file"></li>
                                    </ul>
                                <ul>
                                    <li><label for="memo" class="text-info">メモ</label></li>
                                    <li><textarea name="memo" placeholder="メモの内容を記載" id="memo" rows="3"><?php echo $memo; ?></textarea></li>
                                </ul>
                                <input type="hidden" name="date" value="<?php echo $date; ?>">
                            </td>
                        </tr>
                    </table>
                    <div class="page-actions text-right">
                        <input type="submit" value="確認画面へ">
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>