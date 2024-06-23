<?php

//セッション関係
//set
function setSession($data, $key) {
    $_SESSION[$key] = $data;
}

//get
function getSession($default, $key) {
    foreach ($default as $data_key => $data_val) {
        if (isset($_SESSION[$key])) {
            $rtn[$data_key] = $_SESSION[$key][$data_key];
        } else {
            $rtn[$data_key] = $data_val;
        }
    }
    return $rtn;
}

//clear
function clearSession($key) {
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

//DB関連
//PDOインスタンス生成
function createPdoInstance() {
    $rtn = new PDO(
            "mysql:dbname=test;host=localhost;charset=utf8;", "root", ""
    );
    return $rtn;
}

//ワイルドカード対策
function replaceQuery($sql) {
    foreach ($sql as $key => $val) {
        $rtn[$key] = str_replace('%', '[%]', $val);
    }
    foreach ($rtn as $key => $val) {
        $rtn[$key] = str_replace('_', '[_]', $val);
    }
    return $rtn;
}

//fetch
function Fetch($data) {
    $data->execute();
    $rtn = $data->fetch(PDO::FETCH_ASSOC);
    return $rtn;
}

//fetchAll
function FetchAll($data) {
    $data->execute();
    $rtn = $data->fetchAll(PDO::FETCH_ASSOC);
    return $rtn;
}

//バインド
function BindVal($bind, $param) {
    foreach ($bind as $key => $val) {
        if (!empty($val)) {
            $param->bindValue(':' . $key, $val);
        }
    }
}

//都道府県データ取得
function getPrefecture() {
    try {
        $pdo = createPdoInstance();
        $data = $pdo->prepare('SELECT * FROM mst_prefecture');
        $prefecture = FetchAll($data);
        return $prefecture;
    } catch (Exception $ex) {
        return NULL;
    }
}

//都道府県名取得
function getPrefectureName($cd) {
    try {
        $pdo = createPdoInstance();
        $data = $pdo->prepare('SELECT prefecture_name FROM mst_prefecture WHERE prefecture_cd=:cd');
        $data->bindValue(':cd', $cd);
        $prefecture_name = Fetch($data);
        return $prefecture_name['prefecture_name'];
    } catch (Exception $ex) {
        moveList();
    }
}

//社員データ取得
function getList($data, $p) {
    try {
        $pdo = createPdoInstance();
        $rtnCreateWhere = createWhere($data);
        $view_data = $pdo->prepare('SELECT * FROM mst_staff where ' . $rtnCreateWhere['where'] . ' LIMIT 25 OFFSET :page');
        BindVal($rtnCreateWhere['binds'], $view_data);
        if ($p === 1) {
            $sqlp = 0;
        } else {
            $sqlp = $p * 25 - 25;
        }
        $view_data->bindValue(':page', $sqlp, PDO::PARAM_INT);
        $rtn = FetchAll($view_data);
        return $rtn;
    } catch (Exception $ex) {
        return NULL;
    }
}

//社員IDでデータ取得
function getDataById($staff_id) {
    try {
        $pdo = createPdoInstance();
        $data = $pdo->prepare('SELECT * FROM mst_staff WHERE staff_id=:staff_id');
        $data->bindValue(':staff_id', $staff_id);
        return Fetch($data);
    } catch (PDOException $Exception) {
        return NULL;
    }
}

//件数取得
function getTotal($data) {
    $pdo = createPdoInstance();
    $rtnCreateWhere = createWhere($data);
    $count_data = $pdo->prepare('SELECT COUNT(*) as count FROM mst_staff where ' . $rtnCreateWhere['where']);
    BindVal($rtnCreateWhere['binds'], $count_data);
    $count_data->execute();
    $total = $count_data->fetch();
    return $total['count'];
}

//where文生成
function createWhere($data) {
    $rtn['where'] = '';
    $rtn['binds'] = [
        'staff_id' => '',
        'staff_name' => '',
        'sex_div' => '',
        'staff_tel' => '',
        'prefecture_cd' => '',
        'staff_address' => ''
    ];
    $rtn['and'] = '';
    //($data, $key, $where, $param_f = '', $param_e = '', $connect = '=')
    $rtn = createWhereParam($data['staff_id'], 'staff_id', $rtn, '', '%', 'LIKE');
    $rtn = createWhereParam($data['staff_name'], 'staff_name', $rtn, '%', '%', 'LIKE');
    if ($data['sex_div'] != 3) {
        $rtn = createWhereParam($data['sex_div'], 'sex_div', $rtn);
    }
    $rtn = createWhereParam($data['staff_tel'], 'staff_tel', $rtn, '%', '', 'LIKE');
    $rtn = createWhereParam($data['prefecture_cd'], 'prefecture_cd', $rtn);
    $rtn = createWhereParam($data['staff_address'], 'staff_address', $rtn, '%', '%', 'LIKE');

    if (empty($rtn['where'])) {
        $rtn['where'] = 'delete_flg=0';
    } else {
        $rtn['where'] = $rtn['where'] . 'and delete_flg=0';
    }
    /* $rtn = [
      'where' => $where,
      'binds' => $binds,
      ]; */
    return $rtn;
}

function createWhereParam($data, $key, $iRtn, $param_f = '', $param_e = '', $connect = '=') {
    $rtn = [
        'where' => $iRtn['where'],
        'binds' => $iRtn['binds'],
        'and' => $iRtn['and']
    ];
    if (!empty($data)) {
        $rtn['where'] = $rtn['where'] . $rtn['and'] . $key . ' ' . $connect . ' ' . ':' . $key . ' ';
        $rtn['binds'][$key] = $param_f . $data . $param_e;
        $rtn['and'] = 'and';
    }
    return $rtn;
}

//その他
//年齢取得
function getAge($birthdate) {
    $now = date("Ymd");
    $birthday = str_replace("-", "", $birthdate);
    echo floor(($now - $birthday) / 10000) . '歳';
}

//性別取得
function getSex($data) {
    if ($data === '1') {
        $rtn = '男';
    } else if ($data === '2') {
        $rtn = '女';
    } else {
        $rtn = '不明';
    }
    return $rtn;
}

//ページ関連
function countPage($total, $p) {
    $start_num = $p * 25 - 25 + 1;
    if ($p * 25 < $total) {
        $finish_num = $p * 25;
    } else {
        $finish_num = $total;
    }
    $rtn = [
        'start_num' => $start_num,
        'finish_num' => $finish_num
    ];
    return $rtn;
}

//遷移関連
function moveList() {
    header('location:list.php');
    exit();
}

function moveConf() {
    header('location:conf.php');
    exit();
}

//ラジオボタン判定
function selectRadioButton($data, $sex_no) {
    if (!empty($data) && (int) $data === $sex_no) {
        echo 'checked';
    }
}

//エスケープ処理
function escape($inputs) {
    if (is_array($inputs)) {
        $_input = array();
        foreach ($inputs as $key => $val) {
            if (is_array($val)) {
                $key = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
                $_input[$key] = escape($val);
            } else {
                $key = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
                $_input[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
            }
        }
        return $_input;
    } else {
        return htmlspecialchars($inputs, ENT_QUOTES, 'UTF-8');
    }
}

//ページ番号判定
function checkPageNo($p) {
    if (is_int($p)) {
        return $p;
    } else {
        return 1;
    }
}

//POST判定
function isPost() {
    $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    if ($method === 'POST') {
        return 1;
    } else {
        return 0;
    }
}

session_start();
date_default_timezone_set('Asia/Tokyo');

//GET取得
foreach ($gets as $gets_key => $val) {
    $gets[$gets_key] = filter_input(INPUT_GET, $gets_key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if ($gets[$gets_key] === NULL) {
        $gets[$gets_key] = $val;
    }
}
//POST取得
foreach ($posts as $posts_key => $val) {
    $posts[$posts_key] = filter_input(INPUT_POST, $posts_key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if ($posts[$posts_key] === NULL) {
        $posts[$posts_key] = $val;
    }
}