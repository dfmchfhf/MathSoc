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
    <title>People</title>
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
          </div>
        </div>
      </div>
      <br/>
      <div class="panel panel-collapsible collapsed">
        <div class="panel-heading">
          <h3 class="panel-title">Profile</h3>
        </div>
        <div id="hist_panel" class="panel-body">
          <div id="profile_panel" class="profile">
            <div class="profile_picture cell-2 inline-block">
              <img src="css/images/mathsoc.png" alt="profile-picture" />
            </div>
            <div class="profile_information cell-4 inline-block">
              <h2 field="name"></h2>
              <div>
                <span>Uwaterloo ID: </span>
                <span field="uwid"></span>
              </div>
              <div>
                <span>Favourite Item: </span>
                <span field="fav"></span>
              </div>
              <div>
                <span>Items Currently Checked Out: </span>
                <span field="checkedout"></span>
              </div>
              <div>
                <span>Total Items Checked Out: </span>
                <span field="total"></span>
              </div>
            </div>
          </div>
          <hr/>
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
    <script src="js/people.js"></script>
  </body>
</html>
