<?php

//MODEL
function updateData($data) {
    try {
        $now = date('Y/m/d H:i:s');
        $pdo = createPdoInstance();
        $sql = 'UPDATE mst_staff SET update_at = CAST( :now AS DATETIME),';
        foreach ($data as $key => $val) {
            if (empty($val) || $key === 'check_id') {
                continue;
            }
            $sql = $sql . $key . '=:' . $key . ',';
            $binds[$key] = $val;
        }
        $sql_pre = mb_substr($sql, 0, -1, "UTF-8");
        $where = ' WHERE staff_id=:check_id and delete_flg=0';
        $update_data = $pdo->prepare($sql_pre . $where);
        $update_data->bindValue(':now', $now);
        foreach ($binds as $key => $val) {
            $update_data->bindValue(':' . $key, $val);
        }
        $update_data->bindValue(':check_id', $data['check_id']);
        $update_data->execute();
        $comp_mes = '完了';
        return $comp_mes;
    } catch (PDOException $Exception) {
        $comp_mes = '失敗';
        return $comp_mes;
    }
}

function insertData($data) {
    $now = date('Y/m/d H:i:s');
    try {
        $pdo = createPdoInstance();
        $sql = 'INSERT INTO mst_staff ';
        $into = '';
        $values = '';
        foreach ($data as $key => $val) {
            if (empty($val) || $key === 'check_id') {
                continue;
            }
            $into = $into . $key . ',';
            $values = $values . ':' . $key . ',';
            $binds[$key] = $val;
        }
        $into_pre = $into . 'insert_at'; //mb_substr($into, 0, -1, "UTF-8");
        $values_pre = $values . 'CAST(:now AS DATETIME)'; //mb_substr($values, 0, -1, "UTF-8");
        $insert_data = $pdo->prepare($sql . '(' . $into_pre . ')VALUES(' . $values_pre . ')');
        foreach ($binds as $key => $val) {
            $insert_data->bindValue(':' . $key, $val);
        }
        $insert_data->bindValue(':now', $now);
        $insert_data->execute();
        $comp_mes = '完了';
        return $comp_mes;
    } catch (PDOException $Exception) {
        $comp_mes = '失敗';
        return $comp_mes;
    }
}

function deleteData($data) {
    try {
        $pdo = createPdoInstance();
        $delete_data = $pdo->prepare('UPDATE mst_staff SET delete_flg=1 WHERE staff_id=:staff_id and delete_flg=0');
        $delete_data->bindValue(':staff_id', $data);
        $delete_data->execute();
        $comp_mes = '完了';
        return $comp_mes;
    } catch (PDOException $Exception) {
        $comp_mes = '失敗';
        return $comp_mes;
    }
}
?>
<?php
//CONTROLLER
$gets = [];
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
    'staff_note' => '',
    'check_id' => ''
];

require_once('common.php');

$H['data'] = getSession($H['data'],'edit');

if ($H['data'] === NULL) {
    moveList();
} else if (!empty($H['data']['check_id'])) {
//更新時
    $H['title'] = '更新';
    $H['comp_mes'] = updateData($H['data']);
} else if (!empty($H['data']['staff_name'])) {
//追加時
    $H['title'] = '追加';
    $H['comp_mes'] = insertData($H['data']);
} else {
//削除時
    $H['title'] = '削除';
    $H['comp_mes'] = deleteData($H['data']['staff_id']);
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
        <label><h1>都道府県<?= $H['title'] ?><?= $H['comp_mes'] ?></h1></label>
        <main>
            <section class = "horizontal_rayout_sp">
                <div>
                    <label>都道府県の<?= $H['title'] ?>が<?= $H['comp_mes'] ?>しました。</label>
                </div>
            </section>
            <section class = "horizontal_rayout_sp">
                <input class = "b_style" type="button" id="btn_back" value="戻る" onclick="location.href = 'list.php'">
            </section>
        </main>
    </body>
</html>