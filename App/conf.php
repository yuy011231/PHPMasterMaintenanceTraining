<?php
//CONTROLL
$gets = [
    'i' => ''
];

$posts = [];

$H['data'] = [
    'staff_id' => '',
    'staff_name' => '',
    'sex_div' => '',
    'staff_tel' => '',
    'prefecture_cd' => '',
    'staff_address' => '',
    'staff_birthday' => '',
    'staff_password' => '',
    'staff_note' => ''
];

$back_list = 'list.php';
$back_edit = 'edit.php';

require_once('common.php');

if (!empty($gets['i'])) {
    if (getDataById($gets['i']) === NULL) {
        moveList();
    }
    $H['data'] = getDataById($gets['i']);
    $H['temp']['staff_id'] = $gets['i'];
    setSessionEdit($H['temp']);
    $H['back_url'] = $back_list;
    $H['title'] = '削除';
} else {
    if (getSession($H['data'],'edit') != NULL) {
        $H['data'] = getSession($H['data'],'edit');
        $H['back_url'] = $back_edit;
        $H['title'] = '登録';
    } else {
        moveList();
    }
}

if (!empty($H['data']['sex_div'])) {
    $H['data']['sex_div'] = getSex($H['data']['sex_div']);
}
if (!empty($H['data']['prefecture_cd'])) {
    $H['data']['prefecture_cd'] = getPrefectureName($H['data']['prefecture_cd']);
}

$H = escape($H);
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>都道府県一覧</title>
        <link rel = "stylesheet" href = "common.css">
    </head>
    <body>
        <header>
            <label class="page_hedder">都道府県<?= $H['title'] ?>確認</label>
        </header>
        <main>
            <section>
                <div class = "horizontal_rayout_sp">
                    <label class = "space_e">社員ID</label>
                    <label class = "space_e"><?= $H['data']['staff_id'] ?></label>
                </div>
                <div class = "horizontal_rayout_sp">
                    <label class = "space_e">社員名</label>
                    <label class = "space_e"><?= $H['data']['staff_name'] ?></label>
                </div>
                <div class = "horizontal_rayout_sp">
                    <label class = "space_e">性別</label>
                    <label class = "space_e"><?= $H['data']['sex_div'] ?></label>
                </div>
                <div class = "horizontal_rayout_sp">
                    <label class = "space_e">社員電話番号</label>
                    <label class = "space_e"><?= $H['data']['staff_tel'] ?></label>
                </div>
                <div class = "horizontal_rayout_sp">
                    <label class = "space_e">都道府県コード</label>
                    <label class = "space_e"><?= $H['data']['prefecture_cd'] ?></label>
                </div>
                <div class = "horizontal_rayout_sp">
                    <label class = "space_e">社員住所</label>
                    <label class = "space_e"><?= $H['data']['staff_address'] ?></label>
                </div>
                <div class = "horizontal_rayout_sp">
                    <label class = "space_e">社員誕生日</label>
                    <label class = "space_e"><?= $H['data']['staff_birthday'] ?></label>
                </div>
                <div class = "horizontal_rayout_sp">
                    <label class = "space_e">社員パスワード</label>
                    <label class = "space_e"><?= $H['data']['staff_password'] ?></label>
                </div>
                <div class = "horizontal_rayout_sp">
                    <label class = "space_e">備考</label>
                    <label class = "space_e"><?= $H['data']['staff_note'] ?></label>
                </div>
            </section>
            <section class = "horizontal_rayout">
                <div>
                    <input class = "b_style" type="button" id="btn_back" value="戻る" onclick="location.href = '<?= $H['back_url'] ?>'">
                    <input class = "b_style" type="submit" id="btn_comp" value=<?= $H['title'] ?> onclick="location.href='comp.php'">
                </div>
            </section>
        </main>
    </body>
</html>