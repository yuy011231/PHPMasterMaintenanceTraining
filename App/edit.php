<?php

//MODEL
//必須
function checkMand($data) {
    if (empty($data)) {
        return '入力がありません';
    }
    return NULL;
}

//文字数
function checkLength($data, $max, $min = 0) {
    if ($min > mb_strlen($data) || mb_strlen($data) > $max) {
        if ($min === $max) {
            $rtn = $max . '文字で入力してください';
        } else if ($min != 0) {
            $rtn = $min . '文字以上' . $max . '文字以内で入力してください';
        } else {
            $rtn = $max . '文字以内で入力してください';
        }
        return $rtn;
    } else {
        return NULL;
    }
}

//一意性
function checkUnique($staff_id, $check_id) {
    try {
        $pdo = createPdoInstance();
        $regist = $pdo->prepare('SELECT staff_id FROM mst_staff WHERE staff_id=:staff_id and delete_flg=0');
        $regist->bindValue(':staff_id', $staff_id);
        $data = Fetch($regist);
        if (!empty($data) && $staff_id != $check_id) {
            $rtn = 'ユニークなキーではありません';
        } else {
            $rtn = NULL;
        }
        return $rtn;
    } catch (PDOException $Exception) {
        return NULL;
    }
}

//型
function checkHalfAlphaAndNum($data) {
    if (!preg_match("/^[a-zA-Z0-9]+$/", $data)) {
        $rtn = '半角数字で入力してください';
        return $rtn;
    } else {
        return NULL;
    }
}

function checkHalfNum($data) {
    if (!preg_match("/^[0-9]+$/", $data)) {
        $rtn = '半角英数字で入力してください';
        return $rtn;
    } else {
        return NULL;
    }
}

function checkStaffId($id, $check_id) {
    $rtn = checkMand($id);
    if ($rtn === NULl) {
        $rtn = checkHalfAlphaAndNum($id);
    }
    if ($rtn === NULL) {
        $rtn = checkLength($id, 20, 4);
    }
    if ($rtn === NULL) {
        $rtn = checkUnique($id, $check_id);
    }
    return $rtn;
}

function checkStaffName($name) {
    $rtn = checkMand($name);
    if ($rtn === NULL) {
        $rtn = checkLength($name, 20);
    }
    return $rtn;
}

function checkSexDiv($sex) {
    $rtn = checkMand($sex);
    return $rtn;
}

function checkStaffTel($tel) {
    $rtn = checkMand($tel);
    if ($rtn === NULL) {
        if (mb_strlen($tel) === 10) {
            if (!preg_match('/^0/', $tel)) {
                $rtn = '電話番号を入力してください';
            }
        } else if (mb_strlen($tel) === 11) {
            if (!preg_match('/^0[5-9]0/', $tel)) {
                $rtn = '電話番号を入力してください';
            }
        } else {
            $rtn = '電話番号を入力してください';
        }
    }
    return $rtn;
}

function checkPrefectureCd($cd) {
    $rtn = checkMand($cd);
    if ($rtn === NULL) {
        $rtn = checkLength($cd, 2, 2);
    }
    if ($rtn === NULL) {
        $rtn = checkHalfNum($cd);
    }
    return $rtn;
}

function checkStaffAddress($address) {
    $rtn = checkMand($address);
    if ($rtn === NULL) {
        $rtn = checkLength($address, 255);
    }
    return $rtn;
}

function checkStaffBirthday($date) {
    $rtn = checkMand($date);
    if ($rtn === NULL && !preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', $date)) {
        $rtn = '年/月/日の形式で入力してください';
    }
    return $rtn;
}

function checkStaffPassword($pass) {
    $rtn = checkMand($pass);
    if ($rtn === NULL) {
        $rtn = checkLength($pass, 255, 8);
    }
    if ($rtn === NULL) {
        $rtn = checkHalfAlphaAndNum($pass);
    }
    return $rtn;
}

function checkStaffNote($note) {
    $rtn = checkLength($note, 255);
    return $rtn;
}

function checkErr($err) {
    foreach ($err as $val) {
        if (!empty($val)) {
            return false;
        }
    }
    return true;
}
?>
<?php
//CONTROLLER
$gets = [
    'i' => ''
];

$posts = [
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
$H['err'] = [];

require_once('common.php');

//都道府県マスタ存在チェック
$H['prefecture_select'] = getPrefecture();
if ($H['prefecture_select'] === NULL) {
    moveList();
}

if (isPost()) {
    $H['data'] = $posts;
    $H['data']['check_id'] = $gets['i'];

    $H['err']['id_err'] = checkStaffId($H['data']['staff_id'], $H['data']['check_id']);
    $H['err']['name_err'] = checkStaffName($H['data']['staff_name']);
    $H['err']['sex_err'] = checkSexDiv($H['data']['sex_div']);
    $H['err']['tel_err'] = checkStaffTel($H['data']['staff_tel']);
    $H['err']['cd_err'] = checkPrefectureCd($H['data']['prefecture_cd']);
    $H['err']['address_err'] = checkStaffAddress($H['data']['staff_address']);
    $H['err']['birthday_err'] = checkStaffBirthday($H['data']['staff_birthday']);
    $H['err']['pass_err'] = checkStaffPassword($H['data']['staff_password']);
    $H['err']['note_err'] = checkStaffNote($H['data']['staff_note']);

    if (!empty($gets['i'])) {
        $H['input_type'] = 'hidden';
    } else {
        $H['input_type'] = 'text';
    }

    if (checkErr($H['err'])) {
        setSession($H['data'], 'edit');
        moveConf();
    }
} else if (!empty($gets['i'])) {
    $H['input_type'] = 'hidden';
    $H['data'] = getDataById($gets['i']);
    if ($H['data'] === NULL) {
        moveList();
    }
    $H['data']['staff_birthday'] = str_replace('-', '/', $H['data']['staff_birthday']);
} else {
    $H['input_type'] = 'text';
    $H['data'] = getSession($H['data'], 'edit');
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
            <h1>都道府県登録</h1>
        </header>
        <main>
            <form action="" method="POST">
                <section>
                    <?php if (!checkErr($H['err'])) { ?>
                        <div class = "horizontal_rayout">
                            <label class="font_red">
                                正しく入力してください
                            </label>
                        </div>
                    <?php } ?>
                    <div class = "horizontal_rayout_sp">
                        <p class = "space_e">社員ID</p> 
                        <?php if (!empty($gets['i'])) { ?>
                            <label class = "space_e"><?= $H["data"]["staff_id"] ?></label>
                        <?php } ?>
                        <input type = "<?= $H['input_type'] ?>" id="staff_id" name = "staff_id" value = "<?= $H["data"]["staff_id"] ?>" class = "inputWidth">
                    </div>
                    <?php if (!empty($H['err']['id_err'])) { ?>
                        <div class = "horizontal_rayout_sp">
                            <label class="font_red">
                                <?= $H['err']['id_err'] ?>
                            </label>
                        </div>
                    <?php } ?>
                    <div class = "horizontal_rayout_sp">
                        <p class = "space_e">社員名</p>
                        <input type = "text" id="staff_name" name = "staff_name" value = "<?= $H["data"]["staff_name"] ?>" class = "inputWidth">
                    </div>
                    <?php if (!empty($H['err']['name_err'])) { ?>
                        <div class = "horizontal_rayout_sp">
                            <label class="font_red">
                                <?= $H['err']['name_err'] ?>
                            </label>
                        </div>
                    <?php } ?>
                    <div class = "horizontal_rayout_sp">
                        <p class = "space_e">性別</p> 
                        <div class = "inputWidth horizontal_rayout">
                            <input type = "radio" id="sex_div" name = "sex_div" value = "1" <?= selectRadioButton($H['data']['sex_div'], 1) ?> class="radioButton">男
                            <input type = "radio" id="sex_div" name = "sex_div" value = "2" <?= selectRadioButton($H['data']['sex_div'], 2) ?> class="radioButton">女
                        </div>
                    </div>
                    <?php if (!empty($H['err']['sex_err'])) { ?>
                        <div class = "horizontal_rayout_sp">
                            <label class="font_red">
                                <?= $H['err']['sex_err'] ?>
                            </label>
                        </div>
                    <?php } ?>
                    <div class = "horizontal_rayout_sp">
                        <p class = "space_e">社員電話番号</p> 
                        <input type = "text" id="staff_tel" name = "staff_tel" value = "<?= $H["data"]["staff_tel"] ?>" class = "inputWidth">
                    </div>
                    <?php if (!empty($H['err']['tel_err'])) { ?>
                        <div class = "horizontal_rayout_sp">
                            <label class="font_red">
                                <?= $H['err']['tel_err'] ?>
                            </label>
                        </div>
                    <?php } ?>
                    <div class = "horizontal_rayout_sp">
                        <p class = "space_e">都道府県名</p> 
                        <select id="prefecture_cd" name="prefecture_cd" class = "inputWidth">
                            <?php
                            if (!empty($H['data']['prefecture_cd'])) {
                                foreach ($H['prefecture_select'] as $v) {
                                    if ($v['prefecture_cd'] === $H['data']['prefecture_cd']) {
                                        ?>
                                        <option value="<?= $v['prefecture_cd'] ?>" selected><?= $v['prefecture_name'] ?></option>
                                    <?php } else { ?>    
                                        <option value="<?= $v['prefecture_cd'] ?>"><?= $v['prefecture_name'] ?></option>
                                        <?php
                                    }
                                }
                            } else {
                                ?>                               
                                <option value="" selected>---</option>
                                <?php foreach ($H['prefecture_select'] as $v) { ?>
                                    <option value="<?= $v['prefecture_cd'] ?>"><?= $v['prefecture_name'] ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <?php if (!empty($H['err']['cd_err'])) { ?>
                        <div class = "horizontal_rayout_sp">
                            <label class="font_red">
                                <?= $H['err']['cd_err'] ?>
                            </label>
                        </div>
                    <?php } ?>
                    <div class = "horizontal_rayout_sp">
                        <p class = "space_e">社員住所</p> 
                        <input type = "text" id="staff_address" name = "staff_address" value = "<?= $H["data"]["staff_address"] ?>" class = "inputWidth">
                    </div>
                    <?php if (!empty($H['err']['address_err'])) { ?>
                        <div class = "horizontal_rayout_sp">
                            <label class="font_red">
                                <?= $H['err']['address_err'] ?>
                            </label>
                        </div>
                    <?php } ?>
                    <div class = "horizontal_rayout_sp">
                        <p class = "space_e">社員誕生日</p> 
                        <input type = "text" id="staff_birthday" name = "staff_birthday" value = "<?= $H["data"]["staff_birthday"] ?>" class = "inputWidth">
                    </div>
                    <?php if (!empty($H['err']['birthday_err'])) { ?>
                        <div class = "horizontal_rayout_sp">
                            <label class="font_red">
                                <?= $H['err']['birthday_err'] ?>
                            </label>
                        </div>
                    <?php } ?>
                    <div class = "horizontal_rayout_sp">
                        <p class = "space_e">社員パスワード</p> 
                        <input type = "password" id="staff_password" name = "staff_password" value = "<?= $H["data"]["staff_password"] ?>" class = "inputWidth">
                    </div>
                    <?php if (!empty($H['err']['pass_err'])) { ?>
                        <div class = "horizontal_rayout_sp">
                            <label class="font_red">
                                <?= $H['err']['pass_err'] ?>
                            </label>
                        </div>
                    <?php } ?>
                    <div class = "horizontal_rayout_sp">
                        <p class = "space_e">備考</p> 
                        <textarea name = "staff_note" id="staff_note" class = "inputWidth inputHeight"><?= $H["data"]["staff_note"] ?></textarea>
                    </div>
                    <?php if (!empty($H['err']['note_err'])) { ?>
                        <div class = "horizontal_rayout_sp">
                            <label class="font_red">
                                <?= $H['err']['note_err'] ?>
                            </label>
                        </div>
                    <?php } ?>

                </section>
                <section class = "horizontal_rayout">
                    <div>
                        <input class = "b_style" type="button" id="btn_back" value="戻る" onclick="location.href = 'list.php'">
                        <input class = "b_style" type="submit" id="btn_conf" value="確認">
                    </div>
                </section>
            </form>
        </main>
    </body>
</html>