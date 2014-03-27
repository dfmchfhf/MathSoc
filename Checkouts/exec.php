<?php
function getFromRequest($k, $d = null) {
  return isset($_REQUEST[$k]) ? $_REQUEST[$k] : $d;
}
function error($msg) {
  die();
}

$action = strtolower(getFromRequest('action', 'get'));

$dbsn = 'mysql:dbname=mathsoc;host=localhost';
$dbuser = 'mathsoc';
$dbpass = 'Vx5dXfpjMm9naBcv';
$dbh = new PDO($dbsn,$dbuser,$dbpass) or die();

$return = "uwid";
switch($action) {
  case "status":
    $return = "item";
    break;
  case "getassetlist":
  case "getuwidlist":
  case "getlists":
    $return = "list";
    break;
  case "getallcheckouts":
    $return = "co";
    break;
  case "assetcreate":
    $return = "true";
    break;
}

$id = getFromRequest('id');
$get_stmt = null;
$result = false;
switch($return) {
  case "uwid":
    if (!preg_match('/^\\d{8}$/',$id)) {
      error("Invalid ID");
    }
    $get_stmt = $dbh->prepare(
      'SELECT * '
      . 'FROM `customers` '
      . 'WHERE `uwID` = :uwID;'
    );
    $get_stmt->execute(array(":uwID" => $id));

    $result = $get_stmt->fetch();
    break;
  case "item":
    $get_stmt = $dbh->prepare(
      'SELECT * '
      . 'FROM `assets` '
      . 'WHERE `name` = :name;'
    );
    $get_stmt->execute(array(":name" => $id));
    break;
  case "list":
    $result = array();
    break;
}

switch($action) {
  case "savename":
    $name = getFromRequest('name');
    if ($result !== false) {
      $save_stmt = $dbh->prepare(
        'UPDATE `customers` '
        . 'SET `name` = :name '
        . 'WHERE `uwID` = :uwID;'
      );
    } else {
      $save_stmt = $dbh->prepare(
        'INSERT INTO `customers`(`uwID`, `name`) '
        . 'VALUES (:uwID, :name)'
      );
    }
    $save_stmt->execute(array(":name" => $name, ":uwID" => $id));
    break;
  case "checkout":
    $asset_name = getFromRequest('asset');
    $asset_stmt = $dbh->prepare(
      'SELECT asset_id '
      . 'FROM `assets` '
      . 'WHERE `name` = :name;'
    );
    $asset_stmt->execute(array(":name" => $asset_name));
    $asset_res = $asset_stmt->fetch();
    if ($asset_res === false) {
      $asset_inst = $dbh->prepare(
        'INSERT INTO `assets`(`name`) '
        . 'VALUES (:name);'
      );
      $asset_inst->execute(array(":name" => $asset_name));
      $asset_stmt->execute(array(":name" => $asset_name));
      $asset_res = $asset_stmt->fetch();
    }
    $assetid = $asset_res['asset_id'];
    $cust_stmt = $dbh->prepare(
      'SELECT * '
      . 'FROM `customers` '
      . 'WHERE `uwID` = :uwID'
    );
    $cust_stmt->execute(array(":uwID" => $id));
    $cust_res = $cust_stmt->fetch();
    if ($cust_res === false) {
      $cust_inst = $dbh->prepare(
        'INSERT INTO `customers`(`uwID`, `name`)'
        . 'VALUES (:uwID, :name)'
      );
      $cust_inst->execute(array(":uwID" => $id, ":name" => ""));
    }
    $co_stmt = $dbh->prepare(
      'INSERT INTO `checkouts`(`uwID`, `asset_id`, `checkout`) '
      . 'VALUES (:uwid, :assetid, NOW());'
    );
    $co_stmt->execute(array(":uwid" => $id, ":assetid" => $assetid));
    break;
  case "checkin":
    $co = getFromRequest('co');
    $ci_stmt = $dbh->prepare(
      'UPDATE `checkouts` '
      . 'SET `checkin` = NOW() '
      . 'WHERE `uwID` = :uwID AND `checkout_id` = :co AND `checkin` IS NULL'
    );
    $ci_stmt->execute(array(":uwID" => $id, ":co" => $co));
    break;
  case "assetcreate":
    $asset_stmt = $dbh->prepare(
      'SELECT asset_id '
      . 'FROM `assets` '
      . 'WHERE `name` = :name;'
    );
    $asset_stmt->execute(array(":name" => $id));
    $asset_res = $asset_stmt->fetch();
    if ($asset_res === false) {
      $asset_inst = $dbh->prepare(
        'INSERT INTO `assets`(`name`) '
        . 'VALUES (:name);'
      );
      $asset_inst->execute(array(":name" => $id));
    }
    break;
  case "status":
    break;
  case "getassetlist":
    $asset_stmt = $dbh->prepare(
      'SELECT a.* '
      . 'FROM `assets` a '
      . 'LEFT JOIN ('
      .   'SELECT `asset_id`, count(`asset_id`) AS `count` '
      .     'FROM `checkouts` '
      .     'GROUP BY `asset_id`'
      . ') c '
      .   'ON c.`asset_id` = a.`asset_id` '
      . 'ORDER BY c.count DESC, a.name ASC;'
    );
    $asset_stmt->execute();
    $asset_res = $asset_stmt->fetchAll();
    foreach($asset_res as $asset) {
      $result[] = $asset['name'];
    }
    break;
  case "getuwidlist":
    $uwid_stmt = $dbh->prepare(
      'SELECT * '
      . 'FROM `customers` '
      . 'ORDER BY uwID;'
    );
    $uwid_stmt->execute();
    $uwid_res = $uwid_stmt->fetchAll();
    foreach($uwid_res as $uwid) {
      $result[] = $uwid['uwID'];
    }
    break;
  case "getlists":
    $asset_stmt = $dbh->prepare(
      'SELECT a.* '
      . 'FROM `assets` a '
      . 'LEFT JOIN ('
      .   'SELECT `asset_id`, count(`asset_id`) AS `count` '
      .     'FROM `checkouts` '
      .     'GROUP BY `asset_id`'
      . ') c '
      .   'ON c.`asset_id` = a.`asset_id` '
      . 'ORDER BY c.count DESC, a.name ASC;'
    );
    $asset_stmt->execute();
    $asset_res = $asset_stmt->fetchAll();
    $result['assets'] = array();
    foreach($asset_res as $asset) {
      $result['assets'][] = $asset['name'];
    }
    $uwid_stmt = $dbh->prepare(
      'SELECT * '
      . 'FROM `customers` '
      . 'ORDER BY uwID;'
    );
    $uwid_stmt->execute();
    $uwid_res = $uwid_stmt->fetchAll();
    $result['uwids'] = array();
    foreach($uwid_res as $uwid) {
      $result['uwids'][] = $uwid['uwID'];
    }
    break;
}

switch($return) {
  case "uwid":
    $get_stmt->execute(array(":uwID" => $id));

    $result = $get_stmt->fetch();

    if($result !== false) {
      $co_stmt = $dbh->prepare(
        'SELECT c.`checkout_id`, c.`uwID`, u.`name`, a.`name` AS `asset`, c.`checkout`, c.`checkin` '
        . 'FROM `checkouts` c '
        . 'JOIN `assets` a '
        .   'ON a.`asset_id` = c.`asset_id` '
        . 'JOIN `customers` u '
        .   'ON u.`uwID` = c.`uwID` '
        . 'WHERE u.`uwID` = :uwID '
        . 'ORDER BY c.checkin ASC, c.checkout_id ASC;'
      );
      $co_stmt->execute(array(":uwID" => $result['uwID']));
      $co_res = $co_stmt->fetchAll();
      $cos = array();
      foreach($co_res as $co) {
        $cos[] = array('id' => $co['checkout_id'], 'uwid' => $co['uwID'], 'name' => $co['name'], 'asset' => $co['asset'], 'out' => $co['checkout'], 'in' => ($co['checkin'] == null ? null : $co['checkin']));
      }
      echo json_encode(array('uwID' => $result['uwID'], 'name' => $result['name'], 'co' => $cos));
    } else {
      echo json_encode(array('uwID' => $id, 'name' => null, 'co' => array()));
    }
    break;
  case "item":
    $get_stmt->execute(array(":name" => $id));

    $result = $get_stmt->fetch();

    if ($result !== false) {
      $co_stmt = $dbh->prepare(
        'SELECT c.`checkout_id`, c.`uwID`, u.`name`, a.`name` AS `asset`, c.`checkout`, c.`checkin` '
        . 'FROM `checkouts` c '
        . 'JOIN `assets` a '
        .   'ON a.`asset_id` = c.`asset_id` '
        . 'JOIN `customers` u '
        .   'ON u.`uwID` = c.`uwID` '
        . 'WHERE a.`asset_id` = :assetid '
        . 'ORDER BY c.checkin ASC, c.checkout_id ASC;'
      );
      $co_stmt->execute(array(":assetid" => $result['asset_id']));
      $co_res = $co_stmt->fetchAll();
      $cos = array();
      foreach($co_res as $co) {
        $cos[] = array('id' => $co['checkout_id'], 'uwid' => $co['uwID'], 'name' => $co['name'], 'asset' => $co['asset'], 'out' => $co['checkout'], 'in' => ($co['checkin'] == null ? null : $co['checkin']));
      }
      echo json_encode(array('assetid' => $result['asset_id'], 'asset' => $result['name'], 'co' => $cos));
    } else {
      echo json_encode(array('assetid' => null, 'asset' => $id, 'co' => array()));
    }
    break;
  case "list":
    echo json_encode($result);
    break;
  case "co":
    $co_stmt = $dbh->prepare(
      'SELECT c.`checkout_id`, c.`uwID`, u.`name`, a.`name` AS `asset`, c.`checkout`, c.`checkin` '
      . 'FROM `checkouts` c '
      . 'JOIN `assets` a '
      .   'ON a.`asset_id` = c.`asset_id` '
      . 'JOIN `customers` u '
      .   'ON u.`uwID` = c.`uwID` '
      . 'ORDER BY c.checkin ASC, c.checkout_id ASC;'
    );
    $co_stmt->execute();
    $co_res = $co_stmt->fetchAll();
    $cos = array();
    foreach($co_res as $co) {
      $cos[] = array('id' => $co['checkout_id'], 'uwid' => $co['uwID'], 'name' => $co['name'], 'asset' => $co['asset'], 'out' => $co['checkout'], 'in' => ($co['checkin'] == null ? null : $co['checkin']));
    }
    echo json_encode(array('co' => $cos));
    break;
  case "true":
    echo true;
    break;
}
?>
