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
?>
<html lang="en">
  <head>
    <title>MathSoc Signouts</title>
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
          <h3 class="panel-title">Search</h3>
        </div>
        <div class="panel-body">
          <div class="container container-fluid">
            <div class="cell-4">
              <div class="input-group">
                <label class="input-group-item has-tooltip" for="se_uwid_id" title="ID<br />WatSFiC IDs should be zero-padded.">
                  <span class="ui-icon ui-icon-person">&nbsp;</span>
                </label>
                <input id="se_uwid_id" class="form-control" placeholder="Search IDs or student name">
                <span class="input-group-btn">
                  <button id="se_uwid_submit" class="btn">
                    <span class="ui-icon ui-icon-search">&nbsp;</span>
                  </button>
                </span>
              </div>
              <div id="se_uwid_name" class="input-group" style="display:none;">
                <label class="input-group-item has-tooltip" for="se_uwid_name_txt" title="Name">
                  <span class="ui-icon ui-icon-comment">&nbsp;</span>
                </label>
                <input id="se_uwid_name_id" type="hidden">
                <input id="se_uwid_name_txt" class="form-control has-tooltip" readonly placeholder="Name" title="Double-click to edit">
                <span class="input-group-btn">
                  <button id="se_uwid_name_btn" class="btn">
                    <span class="ui-icon ui-icon-pencil">&nbsp;</span>
                  </button>
                </span>
              </div>
            </div>
            <div class="cell-4">
              <div class="input-group">
                <label class="input-group-item has-tooltip" for="se_item_id" title="Item">
                  <span class="ui-icon ui-icon-calculator">&nbsp;</span>
                </label>
                <input id="se_item_id" class="form-control" placeholder="Search items">
                <span class="input-group-btn">
                  <button id="se_item_submit" class="btn">
                    <span class="ui-icon ui-icon-search">&nbsp;</span>
                  </button>
                </span>
              </div>
            </div>
            <div class="cell-2">
              <button id="view_all" class="btn">
                View All Checkouts
              </button>
            </div>
          </div>
        </div>
      </div>

      <br/>

      <div id="co_panel" class="panel panel-collapsible collapsed">
        <div class="panel-heading">
          <h3 class="panel-title">Check out</h3>
        </div>
        <div class="panel-body">
          <div id="co_uwid" class="container container-fluid">
            <div class="cell-10">
              <div class="input-group">
                <label class="input-group-item" for="co_uwid_item">
                  Check out to <strong id="co_uwid_user">user</strong>
                </label>
                <input id="co_uwid_uwid" type="hidden">
                <input id="co_uwid_item" class="form-control" placeholder="Item to check out; please enter each item in a separate line">
                <span class="input-group-btn">
                  <button id="co_uwid_submit" class="btn">
                    <span class="ui-icon ui-icon-carat-1-e">&nbsp;</span>
                  </button>
                </span>
              </div>
            </div>
          </div>
          <div id="co_item" class="container container-fluid">
            <div class="cell-10">
              <div class="input-group">
                <label class="input-group-item" for="co_item_uwid">
                  Check out <strong id="co_item_item">item</strong> to
                </label>
                <input id="co_item_name" type="hidden">
                <input id="co_item_uwid" class="form-control" placeholder="uwID (pad WatSFiC IDs with 0s), or search by name">
                <span class="input-group-btn">
                  <button id="co_item_submit" class="btn">
                    <span class="ui-icon ui-icon-carat-1-e">&nbsp;</span>
                  </button>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <br/>

      <div id="hist_panel" class="panel panel-collapsible collapsed">
        <div class="panel-heading">
          <h3 class="panel-title">Checkouts History</h3>
        </div>
        <div class="panel-body">
          <table id="hist_cur_table" class="panel-table">
            <caption><kbd>Items currently checked out:</kbd></caption>
            <thead>
              <tr>
                <th><span>uwID</span></th>
                <th><span>Name</span></th>
                <th><span>Item</span></th>
                <th><span>Checkout time</span></th>
                <th><span>Check item in</span></th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
          <table id="hist_prev_table" class="panel-table">
            <caption><kbd>History:</kbd></caption>
            <thead>
              <tr>
                <th><span>uwID</span></th>
                <th><span>Name</span></th>
                <th><span>Item</span></th>
                <th><span>Checkout time</span></th>
                <th><span>Checkin time</span></th>
                <th><span>Check item back out</span></th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <script src="js/checkouts.js"></script>
  </body>
</html>
