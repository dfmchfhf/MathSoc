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
    <title>MathSoc Candy Catalog</title>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <link rel="stylesheet" href="css/jquery-ui-1.10.4.custom.min.css">
    <link rel="stylesheet" href="css/checkouts.css">

    <script src="js/jquery-2.1.1.min.js"></script>
    <script src="js/jquery-ui-1.10.4.custom.min.js"></script>

  </head>

  <body>
    <?php include 'menu.php'; ?>
    <div class="page">

      <div class="panel panel-collapsible collapsed">
        <div class="panel-heading">
          <h3 class="panel-title">Search Candy</h3>
        </div>
        <div class="panel-body">
          <div class="container container-fluid">
            <div class="cell-4">
              <div class="input-group">
                <label class="input-group-item has-tooltip" for="se_candy_name" title="Name of the candy you want to search for.">
                  <span class="ui-icon ui-icon-image">&nbsp;</span>
                </label>
                <input id="se_candy_name" class="form-control" placeholder="Enter the candy name">
                <span class="input-group-btn">
                  <button id="se_candy_search" class="btn">
                    <span class="ui-icon ui-icon-search">&nbsp;</span>
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
          <h3 class="panel-title">Add Candy</h3>
        </div>
        <div class="panel-body">
          <div class="container container-fluid">
            <div class="cell-4">
              <div class="input-group">
                <input id="se_add_candy_name" class="form-control" placeholder="Enter the candy name">
              </div>
            </div>
            <div class="cell-4">
              <div class="input-group">
                <input id="se_add_candy_cost" class="form-control" placeholder="Enter cost per 100g">
              </div>
            </div>
            <div class="cell-1" style="width: 20px;">
              <div class="input-group">
                <span class="input-group-btn">
                  <button id="se_add_candy" class="btn">
                    <span class="ui-icon ui-icon-plus">&nbsp;</span>
                  </button>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <br/>
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Current Candy</h3>
        </div>
        <div id="candy_cur_panel" class="panel-body">
          <table id="candy_cur_table" class="panel-table">
            <caption><kbd>Current Candy</kbd></caption>
            <thead>
              <tr>
                <th><span>Name</span></th>
                <th><span>Cost($)</span></th>
                <th><span>Out Since</span></th>
                <th><span>Average Time (minutes)</span></th>
                <th><span>Finished?</span></th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <br/>
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">All Candy</h3>
        </div>
        <div id="candy_hist_panel" class="panel-body">
          <table id="candy_hist_table" class="panel-table">
            <caption><kbd>Candy History</kbd></caption>
            <thead>
              <tr>
                <th><span>Name</span></th>
                <th><span>Cost($)</span></th>
                <th><span>Average Time (minutes)</span></th>
                <th><span>Cost($) per Minute (100 g) </span></th>
                <th><span>Put Out</span></th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="js/checkouts.js"></script>
    <script type="text/javascript" src="js/candy.js"></script>
  </body>
</html>
