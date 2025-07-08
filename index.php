<?php
//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

//前月・次月リンクが選択された場合は、GETパラメーターから年月を取得
if(isset($_GET['ym'])){
    $ym = $_GET['ym'];
}else{
    //今月の年月を表示
    $ym = date('Y-m');
}

//タイムスタンプを作成し、フォーマットをチェックする
$timestamp = strtotime($ym . '-01');
if($timestamp === false){
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

//今日の日付 Y-m-j形式
$today = date('Y-m-j');

//カレンダーのタイトルを作成 例) 2020年10月
$html_title =date('Y年n月', $timestamp);

//前月・次月の年月を取得 (★ここを修正しました★)
$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));

//該当月の日数を取得
$day_count = date('t', $timestamp);

//1日が何曜日か (0:日〜6:土)
$youbi = date('w', $timestamp);

//カレンダー作成の準備
$weeks = [];
$week = '';

//第1週目：空のセルを追加
$week .= str_repeat('<td></td>', $youbi);

for($day = 1; $day <= $day_count; $day++, $youbi++){
    $date = $ym . '-' . $day; // Y-m-j 形式
    $date_plus = $ym . '-' . sprintf('%02d', $day); // Y-m-d 形式

    // 各日付のクラスと祝日名を初期化
    $td_class = [];
    $holiday_name = '';

    // 今日の日付に 'today' クラスを付与
    if($today == $date){
        $td_class[] = 'today';
    }

    // ★★★ あなたが作成した祝日判定ロジック ★★★
    if(substr($date_plus, -5) == '01-01'){
        $td_class[] = 'holiday';
        $holiday_name = '元日';
    }elseif(substr($date_plus, 0, 7) == date('Y-01', $timestamp) && count($weeks) == 1 && date('w', strtotime($date_plus)) == 1){
        $td_class[] = 'holiday';
        $holiday_name = '成人の日';
    }elseif(substr($date_plus, -5) == '02-11'){
        $td_class[] = 'holiday';
        $holiday_name = '建国記念日';
    }elseif(substr($date_plus, -5) == '02-23'){
        $td_class[] = 'holiday';
        $holiday_name = '天皇誕生日';
    }elseif(substr($date_plus, -5) == '03-20' || substr($date_plus, -5) == '03-21'){ // 春分の日
        if(substr($date_plus, -5) == '03-20'){ // 仮
             $td_class[] = 'holiday';
             $holiday_name = '春分の日';
        }
    }elseif(substr($date_plus, -5) == '04-29'){
        $td_class[] = 'holiday';
        $holiday_name = '昭和の日';
    }elseif(substr($date_plus, -5) == '05-03'){
        $td_class[] = 'holiday';
        $holiday_name = '憲法記念日';
    }elseif(substr($date_plus, -5) == '05-04'){
        $td_class[] = 'holiday';
        $holiday_name = 'みどりの日';
    }elseif(substr($date_plus, -5) == '05-05'){
        $td_class[] = 'holiday';
        $holiday_name = 'こどもの日';
    }elseif(substr($date_plus, 0, 7) == date('Y-07', $timestamp) && count($weeks) == 2 && date('w', strtotime($date_plus)) == 1){
        $td_class[] = 'holiday';
        $holiday_name = '海の日';
    }elseif(substr($date_plus, -5) == '08-11'){
        $td_class[] = 'holiday';
        $holiday_name = '山の日';
    }elseif(substr($date_plus, 0, 7) == date('Y-09', $timestamp) && count($weeks) == 2 && date('w', strtotime($date_plus)) == 1){
        $td_class[] = 'holiday';
        $holiday_name = '敬老の日';
    }elseif(substr($date_plus, -5) == '09-22' || substr($date_plus, -5) == '09-23'){ // 秋分の日
        if(substr($date_plus, -5) == '09-23'){ // 仮
             $td_class[] = 'holiday';
             $holiday_name = '秋分の日';
        }
    }elseif(substr($date_plus, 0, 7) == date('Y-10', $timestamp) && count($weeks) == 1 && date('w', strtotime($date_plus)) == 1){
        $td_class[] = 'holiday';
        $holiday_name = 'スポーツの日';
    }elseif(substr($date_plus, -5) == '11-03'){
        $td_class[] = 'holiday';
        $holiday_name = '文化の日';
    }elseif(substr($date_plus, -5) == '11-23'){
        $td_class[] = 'holiday';
        $holiday_name = '勤労感謝の日';
    }


    // class属性を生成
    $class_str = implode(' ', $td_class);
    $week .= '<td class="' . $class_str . '">';

    // 日付と祝日名を表示
    $week .= '<div class="day-number">' . $day . '</div>';
    if (!empty($holiday_name)) {
        $week .= '<div class="holiday-name">' . htmlspecialchars($holiday_name, ENT_QUOTES) . '</div>';
    }

    // 「追加」「予定」のリンクを表示
    $week .= '<div class="actions">';
    $week .= '<a href="plan.php?date='.$date_plus.'" class="add-plan">追加</a>';
    $week .= '<a href="plan_table.php?date='.$date_plus.'" class="view-plan">予定</a>';
    $week .= '</div>';
    $week .= '</td>';

    // 週終わり、または月終わりの処理
    if($youbi % 7 == 6 || $day == $day_count){
        if($day == $day_count){
            $week .= str_repeat('<td></td>', 6 - ($youbi % 7));
        }
        $weeks[] = '<tr>' . $week . '</tr>';
        $week = '';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHPカレンダー</title>
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-30">
        <div class="calendar-header">
            <a href="?ym=<?php echo $prev; ?>" class="calendar-nav-link">&lt;</a>
            <h3><?php echo $html_title; ?></h3>
            <a href="?ym=<?php echo $next; ?>" class="calendar-nav-link">&gt;</a>
        </div>
        <table class="table table-bordered calendar-table">
            <thead>
                <tr>
                    <th>日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th>土</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach ($weeks as $w) {
                    echo $w;
                }
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>