<?php
/*
    Copyright (C) 2014 Henry Fung

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function getFromRequest($k, $d = null) {
  return isset($_POST[$k]) ? $_POST[$k] : $d;
}
function error($msg) {
  die();
}
function scoreMatch($searchwords, $targetwords) { /* {{{ */
  $score = 0;
  foreach ($searchwords as $word) {
    $maxdelta = 0;
    foreach ($targetwords as &$target) {
      if (stristr($target, $word)) {
        $maxdelta += strlen($word);
        if (stripos($target, $word) == 0) {
          $maxdelta += strlen($word);
        }
        if (strripos($target, $word) + strlen($word) == strlen($target)) {
          $maxdelta += strlen($word);
        }
        break;
      }
    }
    $score += $maxdelta;
  }
  return $score;
} /* }}} */
function getUwidCheckouts($dbh, $id) {
  $get_user_stmt = $dbh->prepare(
    'SELECT * '
    .' FROM `customers` '
    .' WHERE `uwID` = :id;'
  );
  $get_user_stmt->execute(array(':id' => $id));

  $user = $get_user_stmt->fetch();

  $checkouts = array();
  if ($user) {
    $get_co_stmt = $dbh->prepare(
      'SELECT o.`checkout_id`, o.`uwID`, c.`name`, a.`name` AS `asset`, o.`checkout`, o.`checkin` '
      .' FROM `checkouts` o '
      .' JOIN `assets` a '
      .'   ON a.`asset_id` = o.`asset_id` '
      .' JOIN `customers` c '
      .'   ON c.`uwID` = o.`uwID` '
      .' WHERE o.`uwID` = :id '
      .' ORDER BY o.checkin DESC, o.checkout DESC;'
    );
    $get_co_stmt->execute(array(':id' => $id));
    $cos = $get_co_stmt->fetchAll();
    foreach ($cos as $co) {
      $checkouts[] = array('id' => $co['checkout_id'], 'uwid' => $co['uwID'], 'name' => $co['name'], 'asset' => $co['asset'], 'out' => $co['checkout'], 'in' => $co['checkin']);
    }
  } else {
    $user['uwID'] = $id;
    $user['name'] = null;
  }
  $result = array('type' => 'uwid', 'id' => $user['uwID'], 'name' => $user['name'], 'checkouts' => $checkouts);
  return $result;
}
function getAllCandy($dbh, $q, $candy) {
  $cndy_stmt = '';
  $candies = [];
  if (isset($candy) && strlen($candy)) {
    $cndy_stmt = $dbh->prepare(
      'SELECT * '
      . 'FROM `candy` '
      . 'WHERE (`candy_id` = :id OR `name` = :id);'
    );
    $cndy_stmt->execute(array(":id" => $candy));
  } else {
    $cndy_stmt = $dbh->prepare(
      'SELECT * '
      . 'FROM `candy`; '
    );
    $cndy_stmt->execute();
  }
  $candies = $cndy_stmt->fetchAll();
  if ($q !== null && strlen($q)) {
    $searchwords = preg_split('/[ .-]+/', $q);
    usort($searchwords, function($a, $b) {
      return strlen($b) - strlen($a);
    });
    foreach ($candies as &$candy) {
      $candywords = preg_split('/[ .-]+/', $candy['name']);
      usort($candywords, function($a, $b) {
        return strlen($b) - strlen($a);
      });
      $candy['score'] = scoreMatch($searchwords, $candywords);
    }
    $candies = array_filter($candies, function ($c) {
      return $c['score'];
    });
    usort($candies, function($a, $b) {
      return $b['score'] - $a['score'] ?
          $b['score'] - $a['score'] :
          $b['last'] > $a['last'];
    }); /* HENRY WHAT YOU DOING */
  }
  $candy_list = array();
  foreach ($candies as $ca) {
    $candy_list[] = array('id' => $ca['candy_id'], 'name' => $ca['name'], 'cost' => $ca['cost'], 'time' => $ca['total_time'], 'runs' => $ca['times_out'], 'current_run' => $ca['current_run']);
  }
  $result = array('type' => 'candy', 'candies' => $candy_list);
  return $result;
}

$action = strtolower(getFromRequest('action', 'getAllTransactions'));

$dbsn = 'mysql:dbname=mathsoc;host=localhost';
$dbuser = 'mathsoc';
$dbpass = 'Vx5dXfpjMm9naBcv';
$dbh = new PDO($dbsn,$dbuser,$dbpass) or die();

$result = null;
switch($action) {
  case 'getuwidlist': /* {{{ */
    $get_stmt = $dbh->prepare(
      'SELECT c.`uwID` AS `uwID`, c.`name` AS `name`, GREATEST(MAX(IFNULL(o.`checkout`, 0)), MAX(IFNULL(o.`checkin`, 0))) AS `last` '
      .' FROM `customers` c '
      .' LEFT JOIN `checkouts` o '
      .'   ON o.`uwID` = c.`uwID` '
      .' GROUP BY c.`uwID`;'
    );
    $get_stmt->execute();
    $users = $get_stmt->fetchAll();
    $q = getFromRequest('q');
    if ($q !== null && strlen($q)) {
      $searchwords = preg_split('/[ .-]+/', $q);
      usort($searchwords, function($a, $b) { return strlen($b) - strlen($a); });
      foreach ($users as &$user) {
        $userwords = preg_split('/[ .-]+/', $user['name'] . ' ' . $user['uwID']);
        usort($userwords, function($a, $b) { return strlen($b) - strlen($a); });
        $user['score'] = scoreMatch($searchwords, $userwords);
      }
      $users = array_filter($users, function($u) { return $u['score']; });
    } else {
      foreach ($users as &$user) {
        $user['score'] = 0;
      }
    }
    usort($users, function($a, $b) { return $b['score'] - $a['score'] ? $b['score'] - $a['score'] : $b['last'] > $a['last']; }); /* BOO PHP unstable sort */
    $result = array_map(function($r) { return array('id' => $r['uwID'], 'name' => $r['name'], 'score' => $r['score']); }, $users);
    break; /* }}} */
  case 'getitemlist': /* {{{ */
    $get_stmt = $dbh->prepare(
      'SELECT a.`name`, COUNT(*) as `count` '
      .' FROM `assets` a'
      .' LEFT JOIN `checkouts` o '
      .'   ON o.`asset_id` = a.`asset_id` '
      .' GROUP BY a.`asset_id`;'
    );
    $get_stmt->execute();
    $items = $get_stmt->fetchAll();
    $q = getFromRequest('q');
    if ($q !== null && strlen($q)) {
      $searchwords = preg_split('/[ .-]+/', $q);
      usort($searchwords, function($a, $b) { return strlen($b) - strlen($a); });
      foreach ($items as &$item) {
        $itemwords = preg_split('/[ .-]+/', $item['name']);
        usort($itemwords, function($a, $b) { return strlen($b) - strlen($a); });
        $item['score'] = scoreMatch($searchwords, $itemwords);
      }
      $items = array_filter($items, function($i) { return $i['score']; });
    } else {
      foreach ($items as &$item) {
        $item['score'] = 0;
      }
    }
    usort($items, function($a, $b) { return $b['score'] - $a['score'] ? $b['score'] - $a['score'] : $b['count'] > $a['count']; }); /* PHP unstable sort can die in a fire */
    $result = array_map(function($r) { return array('name' => $r['name'], 'score' => $r['score']); }, $items);
    break; /* }}} */
  case 'savename': /* {{{ */
    $id = getFromRequest('id');
    if (!preg_match('/^\\d{8}$/',$id)) {
      error("Invalid ID");
    }
    $name = getFromRequest('name');
    $get_user_stmt = $dbh->prepare(
      'SELECT * '
      .' FROM `customers` '
      .' WHERE `uwID` = :id;'
    );
    $get_user_stmt->execute(array(':id' => $id));
    $user = $get_user_stmt->fetch();
    $save_stmt = null;
    if ($user !== false) {
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
    $result = getUwidCheckouts($dbh, $id);
    break; /* }}} */
  case 'getuwidcheckouts': /* {{{ */
    $id = getFromRequest('id');
    if (!preg_match('/^\\d{8}$/',$id)) {
      error("Invalid ID");
    }
    $result = getUwidCheckouts($dbh, $id);
    break; /* }}} */
  case 'getitemcheckouts': /* {{{ */
    $item = getFromRequest('item');
    if (!strlen($item)) {
      error("No item");
    }
    $get_asset_stmt = $dbh->prepare(
      'SELECT * '
      . 'FROM `assets` '
      . 'WHERE `name` = :name;'
    );
    $get_asset_stmt->execute(array(':name' => $item));
    $asset = $get_asset_stmt->fetch();

    $checkouts = null;
    if ($asset !== false) {
      $get_co_stmt = $dbh->prepare(
        'SELECT o.`checkout_id`, o.`uwID`, c.`name`, a.`name` AS `asset`, o.`checkout`, o.`checkin` '
        .' FROM `checkouts` o '
        .' JOIN `assets` a '
        .'   ON a.`asset_id` = o.`asset_id` '
        .' JOIN `customers` c '
        .'   ON c.`uwID` = o.`uwID` '
        .' WHERE a.`asset_id` = :assetid '
        .' ORDER BY o.checkin ASC, o.checkout ASC;'
      );
      $get_co_stmt->execute(array(':assetid' => $asset['asset_id']));
      $cos = $get_co_stmt->fetchAll();
      foreach ($cos as $co) {
        $checkouts[] = array('id' => $co['checkout_id'], 'uwid' => $co['uwID'], 'name' => $co['name'], 'asset' => $co['asset'], 'out' => $co['checkout'], 'in' => $co['checkin']);
      }
    } else {
      $asset['name'] = $item;
    }
    $result = array('type' => 'item', 'item' => $asset['name'], 'checkouts' => $checkouts);
    break; /* }}} */
  case 'getallcheckouts': /* {{{ */
    $get_co_stmt = $dbh->prepare(
      'SELECT o.`checkout_id`, o.`uwID`, c.`name`, a.`name` AS `asset`, o.`checkout`, o.`checkin` '
      .' FROM `checkouts` o '
      .' JOIN `assets` a '
      .'   ON a.`asset_id` = o.`asset_id` '
      .' JOIN `customers` c '
      .'   ON c.`uwID` = o.`uwID` '
      .' ORDER BY o.checkin DESC, o.checkout DESC;'
    );
    $get_co_stmt->execute();
    $cos = $get_co_stmt->fetchAll();
    foreach ($cos as $co) {
      $checkouts[] = array('id' => $co['checkout_id'], 'uwid' => $co['uwID'], 'name' => $co['name'], 'asset' => $co['asset'], 'out' => $co['checkout'], 'in' => $co['checkin']);
    }
    $result = array('type' => 'plain', 'checkouts' => $checkouts);
    break;/* }}} */
  case 'getitems':
    $get_asset_stmt = '';
    $assets = array();
    $items = null;
    $item = getFromRequest('item');
    if (isset($item) && strlen($item)) {
      $get_item_stmt = $dbh->prepare(
        'SELECT * '
        . 'FROM `assets` '
        . 'WHERE `name` = :name;'
      );
      $get_item_stmt->execute(array(':name' => $item));
      $asset = $get_item_stmt->fetch();
      if ($asset !== false) {
        $get_asset_stmt = $dbh->prepare(
          'SELECT o.`asset_id`, o.`name`, o.`stock`, o.`total`, GROUP_CONCAT(c.`uwID`) AS students '
          .' FROM `assets` o '
          .' LEFT JOIN `checkouts` c'
          .'   ON c.`asset_id` = :assetid '
          .'   AND c.`checkin` IS NULL '
          .' WHERE o.`asset_id` = :assetid '
          .' GROUP BY o.`asset_id` '
          .' ORDER BY o.name;'
        );
        $get_asset_stmt->execute(array(':assetid' => $asset['asset_id']));
        $assets = $get_asset_stmt->fetchAll();
      }
    } else {
      $get_asset_stmt = $dbh->prepare(
        'SELECT o.`asset_id`, o.`name`, o.`stock`, o.`total`, GROUP_CONCAT(c.`uwID`) AS students '
        .' FROM `assets` o '
        .' LEFT JOIN `checkouts` c'
        .'   ON c.`asset_id` = o.`asset_id` '
        .'   AND c.`checkin` IS NULL '
        .' GROUP BY o.`asset_id` '
        .' ORDER BY o.name;'
      );
      $get_asset_stmt->execute();
      $assets = $get_asset_stmt->fetchAll();
    }

    foreach ($assets as $asset) {
      if (!$asset['students'] || !strlen($asset['students'])) {
        $asset['students'] = [];
      } else {
        $asset['students'] = explode(",", $asset['students']);
      }
      $items[] = array('id' => $asset['asset_id'], 'stock' => intval($asset['stock']), 'total' => intval($asset['total']), 'students' => $asset['students'], 'name' => $asset['name']);
    }
    $result = array('type' => 'plain', 'items' => $items);
    break;
  case 'checkin': /* {{{ */
    $id = getFromRequest('id');
    if (!preg_match('/^\\d{8}$/',$id)) {
      error("Invalid ID");
    }
    $co_id = getFromRequest('coid');
    $ci_stmt = $dbh->prepare(
      'SELECT * '
      . 'FROM `checkouts` '
      . 'WHERE `checkout_id` = :co;'
    );
    $ci_stmt->execute(array(":co" => $co_id));
    $checkout = $ci_stmt->fetch();
    $ci_stmt = $dbh->prepare(
      'UPDATE `checkouts` '
      . 'SET `checkin` = NOW() '
      . 'WHERE `uwID` = :uwID AND `checkout_id` = :co AND `checkin` IS NULL'
    );
    $ci_stmt->execute(array(":uwID" => $id, ":co" => $co_id));
    $asset_stmt = $dbh->prepare(
      'UPDATE `assets` '
      . 'SET `stock`=`stock`+1 '
      . 'WHERE `asset_id` = :assetid;'
    );
    $asset_stmt->execute(array(":assetid" => $checkout['asset_id']));
    $result = getUwidCheckouts($dbh, $id);
    break; /* }}} */
  case 'checkout': /* {{{ */
    $id = getFromRequest('id');
    if (!preg_match('/^\\d{8}$/',$id)) {
      error("Invalid ID");
    }
    $asset_name = getFromRequest('asset');
    $asset_stmt = $dbh->prepare(
      'SELECT * '
      . 'FROM `assets` '
      . 'WHERE `name` = :name;'
    );
    $asset_stmt->execute(array(":name" => $asset_name));
    $asset_res = $asset_stmt->fetch();
    if ($asset_res === false) {
      $asset_inst = $dbh->prepare(
        'INSERT INTO `assets`(`name`, `stock`, `total`) '
        . 'VALUES (:name, 0, 1);'
      );
      $asset_inst->execute(array(":name" => $asset_name));
      $asset_stmt->execute(array(":name" => $asset_name));
      $asset_res = $asset_stmt->fetch();
    } else {
      $total = $asset_res['total'] + ($asset_res['stock'] == 0 ? 1 : 0);
      $stock = $asset_res['stock'] - ($asset_res['stock'] == 0 ? 0 : 1);
      $asset_inst = $dbh->prepare(
        'UPDATE `assets` '
        . 'SET `stock` = :stock ,`total` = :total '
        . 'WHERE `name` = :name AND `asset_id` = :assetid;'
      );
      $asset_inst->execute(array(":name" => $asset_name, ":stock" => $stock, ":total" => $total, ":assetid" => $asset_res['asset_id']));
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
    $result = getUwidCheckouts($dbh, $id);
    break; /* }}} */
  case 'addcandy':
    $candy = getFromRequest('name');
    $cost = getFromRequest('cost');
    $cndy_stmt = $dbh->prepare(
      'INSERT INTO `candy`(`name`, `cost`) '
      . 'VALUES (:name, :cost);'
    );
    $cndy_stmt->execute(array(":name" => $candy, ":cost" => $cost));
    $result = getAllCandy($dbh, null, null);
    break;
  case 'putoutcandy':
    $candy = getFromRequest('id');
    $time = time() * 1000;
    $cndy_stmt = $dbh->prepare(
      'UPDATE `candy` '
      . 'SET `current_run` = :time '
      . 'WHERE `candy_id` = :id;'
    );
    $cndy_stmt->execute(array(":id" => $candy, ":time" => $time));
    $result = getAllCandy($dbh, null, null);
    break;
  case 'pulloutcandy':
    $candy = getFromRequest('id');
    $time = time() * 1000;
    $cndy_stmt = $dbh->prepare(
      'UPDATE `candy` '
      . 'SET `total_time` = `total_time` + (:time - `current_run`)'
      . ', `times_out` = `times_out` + 1 '
      . ', `current_run` = 0 '
      . 'WHERE `candy_id` = :id;'
    );
    $cndy_stmt->execute(array(":id" => $candy, ":time" => $time));
    $result = getAllCandy($dbh, null, null);
    break;
  case 'getallcandy':
    $q = getFromRequest('q');
    $candy = getFromRequest('id');
    if (!isset($candy) || !strlen($candy)) {
      $candy = getFromRequest('name');
    }
    $result = getAllCandy($dbh, $q, $candy);
    break;
}

echo json_encode($result);

