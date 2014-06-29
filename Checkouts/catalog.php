<?php
/*
    Copyright (C) 2014 Ford Peprah

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
?>
<html lang="en">
  <head>
    <title>MathSoc Catalog</title>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <link rel="stylesheet" href="css/jquery-ui-1.10.4.custom.min.css">
    <link rel="stylesheet" href="css/checkouts.css">

    <script src="js/jquery-2.1.1.min.js"></script>
    <script src="js/jquery-ui-1.10.4.custom.min.js"></script>

  </head>

  <body>
    <?php include 'menu.php'; ?>
    <div class="page">
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Assets</h3>
        </div>
        <div class="panel-body">
          <div class="container container-fluid">
            <div class="cell-4">
              <div class="input-group">
                <label class="input-group-item has-tooltip" for="se_asset_name" title="Name of the item you want to search for.">
                  <span class="ui-icon ui-icon-image">&nbsp;</span>
                </label>
                <input id="se_asset_name" class="form-control" placeholder="Enter the item name">
                <span class="input-group-btn">
                  <button id="se_asset_search" class="btn">
                    <span class="ui-icon ui-icon-search">&nbsp;</span>
                  </button>
                </span>
              </div>
            </div>
            <div class="cell-2">
              <button class="view_assets btn" data-fn="all">
                View All Items
              </button>
            </div>
            <div class="cell-2">
              <button class="view_assets btn" data-fn="out-stock">
                View All Items Not in Stock
              </button>
            </div>
            <div class="cell-2">
              <button class="view_assets btn" data-fn="in-stock">
                View All Items in Stock
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Items</h3>
        </div>
        <div class="panel-body">
          <div class="asset_panel" class="panel-body">
            <table id="asset_table" class="panel-table">
              <caption><kbd>MathSoc's Item List</kbd></caption>
              <thead>
                <tr>
                  <th><span>Name</span></th>
                  <th><span>In Stock</span></th>
                  <th><span>Total</span></th>
                  <th><span>Checked Out To</span></th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <script src="js/checkouts.js"></script>
  </body>
</html>
