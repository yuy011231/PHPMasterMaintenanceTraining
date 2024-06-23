<?php
//CONTROLLER
$gets = [
    'r' => '',
    'p' => 1
];

$posts = [
    'staff_id' => '',
    'staff_name' => '',
    'sex_div' => '',
    'staff_tel' => '',
    'prefecture_cd' => '',
    'staff_address' => ''
];

$H['search'] = [
    'staff_id' => '',
    'staff_name' => '',
    'sex_div' => '',
    'staff_tel' => '',
    'prefecture_cd' => '',
    'staff_address' => ''
];

require_once('common.php');
$H['p'] = checkPageNo((int) ($gets['p']));

$H['prefecture_select'] = getPrefecture();
if ($H['prefecture_select'] === NULL) {
    moveList();
}
clearSession('edit');

if (isPost()) {
    $H['search'] = $posts;
    setSession($H['search'], 'search');
} else if ($gets['r'] === '1') {
    setSession($H['search'], 'search');
}

$H['search'] = getSession($H['search'], 'search');
$H['total'] = getTotal(replaceQuery($H['search']));
$H['data'] = getList(replaceQuery($H['search']), $H['p']);
if ($H['data'] === NULL) {
    die;
    moveList();
}
$H['count'] = countPage($H['total'], $H['p']);

$H = escape($H);
?>
<!DOCTYPE html>
<html lang = "ja">

    <head>
        <meta charset = "UTF-8">
        <title>社員一覧</title>
        <link rel = "stylesheet" href = "common.css">
    </head>
    <body>
        <header>
            <h1>社員一覧</h1>
        </header>
        <main>
            <form action="list.php" method="POST">
                <section class = "horizontal_rayout_sp">
                    <div class = "horizontal_rayout">
                        <p class = "space staffInputTitle">社員ID</p>
                        <input type = "text" id="staff_id" name = "staff_id" value = "<?= $H['search']['staff_id'] ?>" class = "staffInputWidth">
                    </div>
                    <div class = "horizontal_rayout">
                        <p class = "space staffInputTitle">社員名</p>
                        <input type = "text" id="staff_name" name = "staff_name" value = "<?= $H['search']['staff_name'] ?>" class = "staffInputWidth">
                    </div>                
                </section>
                <section class = "horizontal_rayout_sp">
                    <div class = "horizontal_rayout">
                        <p class = "space staffInputTitle">性別</p>
                        <input type = "radio" id="sex_div" name = "sex_div" value = "3" <?= selectRadioButton($H['search']['sex_div'], 3) ?> class="radioButton">全て
                        <input type = "radio" id="sex_div" name = "sex_div" value = "1" <?= selectRadioButton($H['search']['sex_div'], 1) ?> class="radioButton">男
                        <input type = "radio" id="sex_div" name = "sex_div" value = "2" <?= selectRadioButton($H['search']['sex_div'], 2) ?> class="radioButton">女
                    </div>
                    <div class = "horizontal_rayout">
                        <p class = "space staffInputTitle">電話番号</p>
                        <input type = "text" id="staff_tel" name = "staff_tel" value = "<?= $H['search']['staff_tel'] ?>" class = "staffInputWidth">
                    </div>                
                </section>
                <section class = "horizontal_rayout_sp">
                    <div class = "horizontal_rayout">
                        <p class = "space staffInputTitle">都道府県</p>
                        <select id="prefecture_cd" name="prefecture_cd" class = "staffInputWidth">
                            <?php
                            if (!empty($H['search']['prefecture_cd'])) {
                                foreach ($H['prefecture_select'] as $v) {
                                    if ($v['prefecture_cd'] === $H['search']['prefecture_cd']) {
                                        ?>
                                        <option value="<?= $v['prefecture_cd'] ?>" selected><?= $v['prefecture_name'] ?></option>
                                    <?php } else { ?>    
                                        <option value="<?= $v['prefecture_cd'] ?>"><?= $v['prefecture_name'] ?></option>
                                        <?php
                                    }
                                }
                            } else {
                                ?>                               
                                <option value="" selected></option>
                                <?php foreach ($H['prefecture_select'] as $v) { ?>
                                    <option value="<?= $v['prefecture_cd'] ?>"><?= $v['prefecture_name'] ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class = "horizontal_rayout">
                        <p class = "space staffInputTitle">住所</p>
                        <input type = "text" id="staff_address" name = "staff_address" value = "<?= $H['search']['staff_address'] ?>" class = "staffInputWidth">
                    </div>                
                </section>
                <section class = "horizontal_rayout_sp">
                    <input class = "b_style" type="submit" id="btn_search" value = "検索">
                    <input class = "b_style" type="button" id="btn_reset" value = "リセット" onclick="location.href = 'list.php?r=1'">
                    <input class = "b_style" type="button" id="btn_insert" value = "追加" onclick="location.href = 'edit.php'">
                </section>
            </form>
            <div class = "horizontal_rayout"><p>
                    <?php
                    /* データの有無 */
                    if ($H['total'] == 0) {
                        /* 無い時 */
                        echo 'データが存在しません';
                    } else {
                        /* ある時(データ表示) */
                        ?>
                    </p></div>
                <section>
                    <div class = "table_info">
                        <label class = "staff_table_info_p">
                            <?= $H['total'] ?>件中
                            <?= $H['count']['start_num'] ?>
                            ～
                            <?= $H['count']['finish_num'] ?>件
                        </label>
                    </div>
                    <table class = "table_section_rayout">
                        <tr>
                            <th class="tableWidth150">社員ID</th>
                            <th class="tableWidth150">社員名</th>
                            <th class="tableWidth60">性別</th>
                            <th class="tableWidth150">電話番号</th>
                            <th class="tableWidth250">住所</th>
                            <th class="tableWidth60">年齢</th>
                            <th class="tableWidth60">更新</th>
                            <th class="tableWidth60">削除</th>
                        </tr>
                        <?php
                        foreach ($H['data'] as $v) {
                            ?>
                            <tr>
                                <td><label><?= $v['staff_id'] ?></label></td>
                                <td><label><?= $v['staff_name'] ?></label></td>
                                <td><label><?= getSex($v['sex_div']) ?></label></td>
                                <td><label><?= $v['staff_tel'] ?></label></td>
                                <td><label><?= $v['staff_address'] ?></label></td>
                                <td><label><?php getAge($v['staff_birthday']) ?></label></td>
                                <td><a href="edit.php?i=<?= $v['staff_id'] ?>">更新</a></td>
                                <td><a href="conf.php?i=<?= $v['staff_id'] ?>">削除</a></td>
                            </tr>
                        <?php } ?>
                    </table>
                    <div class = "table_info">
                        <div class = "tableDiv">
                            <?php if ($H['p'] >= 2) { ?>
                                <a href="list.php?p=<?= $H['p'] - 1 ?>">前のページ</a>
                            <?php } else { ?>
                                <p></p>
                            <?php } ?>

                            <?php if ($H['count']['finish_num'] != $H['total']) { ?>
                                <a href="list.php?p=<?= $H['p'] + 1 ?>">次のページ</a>
                            <?php } ?>
                        </div>
                    </div>
                </section>
            <?php } ?>
        </main>
    </body>
</html>