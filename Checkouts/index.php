<html>
  <head>
    <title>MathSoc Signouts</title>
    <style type="text/css">
      a.ci,a.co {
        color: #0000FF;
      }
      a:hover{
        text-decoration: underline;
      }
      input[type=text].invalid {
        color: #FF0000;
      }
      input[type=text][readonly] {
        color: #AAAAAA;
      }
      table.form {
        border: 1px solid #000000;
      }
      table#hist,table#out {
        border: 1px solid #000000;
        border-collapse: collapse;
      }
      table#hist>thead>tr>td, table#out>thead>tr>td {
        font-weight: bold;
        text-align: center;
        border-bottom: 2px solid #000000;
        border-left: 1px solid #000000;
        border-right: 1px solid #000000;
        padding: 3px 12px 3px 12px;
      }
      table#hist>tbody>tr:hover, table#out>tbody>tr:hover {
        background-color: #DDDDDD;
      }
      table#hist>tbody>tr>td, table#out>tbody>tr>td {
        border-left: 1px solid #000000;
        border-right: 1px solid #000000;
        padding: 3px 12px 3px 12px;
      }
      div#checkout, div#out, div#hist {
        padding: 50px 0 0 0;
      }
    </style>
    <script type="text/javascript">
    var id = null;
    function getId() { return id; }

    function escapeInnerString(str) {
      return str.replace("'","\\x27").replace('"',"\\x22");
    }

    function submitID(id) {
      if(!validateID(id)) {
        return;
      }
      xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
          resp = JSON.parse(xmlhttp.responseText);
          setupScreen(resp);
        }
      };
      xmlhttp.open("POST", "exec.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
      xmlhttp.send("id=" + encodeURIComponent(id) + "&action=get");
    }

    function validateID(id) {
      return /^\d{8}$/.test(id);
    }

    function validateIDField(field) {
      if (validateID(field.value)) {
        field.className = field.className.replace(" invalid", "");
      } else {
        field.className = field.className.replace(" invalid", "") + " invalid";
      }
      return validateID(field.value);
    }

    function checkItemStatus(item) {
      xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
          resp = JSON.parse(xmlhttp.responseText);
          setupScreen(resp);
        }
      };
      xmlhttp.open("POST", "exec.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
      if (item != null) {
        xmlhttp.send("id=" + encodeURIComponent(item) + "&action=status");
      } else {
        xmlhttp.send("action=getAllCheckouts");
      }
    }

    function saveName(name) {
      if(name.length > 0) {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            resp = JSON.parse(xmlhttp.responseText);
            setupScreen(resp);
          }
        };
        xmlhttp.open("POST", "exec.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
        xmlhttp.send("id=" + encodeURIComponent(id) + "&name=" + encodeURIComponent(name) + "&action=saveName");
        n = document.getElementById("name");
        n.readOnly = true;
      }
    }

    function forceAdd(item, button) {
      if(item.length > 0) {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          button.parentNode.removeChild(button);
          assets = document.getElementById("assets");
          o = document.createElement("option");
          o.setAttribute("value", item);
          assets.appendChild(o);
          assetList.push(item);
        };
        xmlhttp.open("POST", "exec.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
        xmlhttp.send("id=" + encodeURIComponent(item) + "&action=assetCreate");
      }
    }

    function setupScreen(resp) {
      a = document.getElementById("item");
      a.value="";
      i = document.getElementById("uwid");
      i.value="";
      id = null;
      n = document.getElementById("name");
      n.readOnly = true;
      n.value="";
      checkout = document.getElementById("checkout");
      out = document.getElementById("out");
      hist = document.getElementById("hist");
      checkout.innerHTML = "";
      out.innerHTML = "";
      hist.innerHTML = "";
      if(resp.uwID) {
        id = resp.uwID;
        i.value=id;
        validateIDField(i);
        if(resp.name == null) {
          n.readOnly = false;
          n.focus();
        } else {
          n.value=resp.name;
          displayHist(resp.co);
          checkout.appendChild(document.createTextNode("Check out "));
          co = document.createElement("input");
          co.setAttribute("type", "text");
          co.setAttribute("list", "assets");
          co.setAttribute("id", "co_item");
          co.setAttribute("onkeyup", "if(this.value.length != 0 && event.keyCode == 13) { checkOut('" + escapeInnerString(resp.uwID) + "', this.value); }");
          checkout.appendChild(co);
          checkout.appendChild(document.createTextNode(" to " + resp.uwID + " (" + resp.name + ") "));
          co.focus();
          save = document.createElement("button");
          save.setAttribute("type", "button");
          save.innerHTML="Check out";
          save.setAttribute("onclick", "co = document.getElementById('co_item'); if (co.value.length != 0) { checkOut('" + escapeInnerString(resp.uwID) + "', co.value);}");
          checkout.appendChild(save);
        }
      } else if(resp.asset) {
        a.value=resp.asset;
        displayHist(resp.co);
        checkout.appendChild(document.createTextNode("Check out " + resp.asset + " to "));
        co = document.createElement("input");
        co.setAttribute("size",8);
        co.setAttribute("maxlength",8);
        co.setAttribute("type", "text");
        co.setAttribute("list", "uwids");
        co.setAttribute("id", "co_uwid");
        co.setAttribute("onkeyup", "if(validateIDField(this) && event.keyCode == 13) { checkOut(this.value, '" + escapeInnerString(resp.asset) + "'); }");
        checkout.appendChild(co);
        checkout.appendChild(document.createTextNode(" "));
        co.focus();
        save = document.createElement("button");
        save.setAttribute("type", "button");
        save.innerHTML="Check out";
        save.setAttribute("onclick", "co = document.getElementById('co_uwid'); if (validateIDField(co)) { checkOut(co.value, '" + escapeInnerString(resp.asset) + "');}");
        checkout.appendChild(save);
        if(assetList.indexOf(resp.asset) == -1) {
          forceadd = document.createElement("button");
          forceadd.innerHTML="Add item to database";
          forceadd.setAttribute("onclick", "forceAdd('" + escapeInnerString(resp.asset) + "', this);");
          checkout.appendChild(forceadd);
        }
      } else if (resp.co) {
        displayHist(resp.co);
      }
    }

    function displayHist(all_co) {
      out_tab = document.createElement("table");
      hist_tab = document.createElement("table");
      out_tab.id = "out";
      hist_tab.id = "hist";
      out_header = out_tab.createTHead().insertRow(0);
      out_header.insertCell(-1).innerHTML="uwID";
      out_header.insertCell(-1).innerHTML="Name";
      out_header.insertCell(-1).innerHTML="Item";
      out_header.insertCell(-1).innerHTML="Checkout time";
      out_header.insertCell(-1).innerHTML="Check in";
      hist_header = hist_tab.createTHead().insertRow(0);
      hist_header.insertCell(-1).innerHTML="uwID";
      hist_header.insertCell(-1).innerHTML="Name";
      hist_header.insertCell(-1).innerHTML="Item";
      hist_header.insertCell(-1).innerHTML="Checkout time";
      hist_header.insertCell(-1).innerHTML="Checkin time";
      hist_header.insertCell(-1).innerHTML="Check back out";
      out_bdy = out_tab.createTBody();
      hist_bdy = hist_tab.createTBody();

      for(i = 0; i < all_co.length; i++) {
        co = all_co[i];
        coid = co.id;
        aname = document.createElement("a");
        aname.setAttribute("ondblclick", "submitID('" + escapeInnerString(co.uwid) + "');");
        aname.innerHTML = co.name;
        uwid = document.createElement("a");
        uwid.setAttribute("ondblclick", "submitID('" + escapeInnerString(co.uwid) + "');");
        uwid.innerHTML = co.uwid;
        item = document.createElement("a");
        item.setAttribute("ondblclick", "checkItemStatus('" + escapeInnerString(co.asset) + "');");
        item.innerHTML=co.asset;
        outtime = co.out;
        intime = co.in == null ? null : co.in
        if(intime) {
          row = hist_bdy.insertRow(0);
          row.insertCell(-1).appendChild(uwid);
          row.insertCell(-1).appendChild(aname);
          row.insertCell(-1).appendChild(item);
          row.insertCell(-1).innerHTML=outtime;
          row.insertCell(-1).innerHTML=intime;
          ci = document.createElement("a");
          ci.setAttribute("onDblClick","checkOut('" + escapeInnerString(co.uwid) + "','" + escapeInnerString(co.asset) + "');");
          ci.innerHTML = "Check Out";
          ci.className = "co";
          row.insertCell(-1).appendChild(ci);
        } else {
          row = out_bdy.insertRow(0);
          row.insertCell(-1).appendChild(uwid);
          row.insertCell(-1).appendChild(aname);
          row.insertCell(-1).appendChild(item);
          row.insertCell(-1).innerHTML=outtime;
          ci = document.createElement("a");
          ci.className = "ci";
          ci.setAttribute("onDblClick","checkIn('" + escapeInnerString(co.uwid) + "'," + escapeInnerString(co.id) + ");");
          ci.innerHTML = "Check In";
          row.insertCell(-1).appendChild(ci);
        }
      }
      out.innerHTML = "Items checked out:";
      out.appendChild(out_tab);
      hist.innerHTML = "History:";
      hist.appendChild(hist_tab);
    }

    function checkIn(uwid, coid) {
      xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
          resp = JSON.parse(xmlhttp.responseText);
          setupScreen(resp);
        }
      };
      xmlhttp.open("POST", "exec.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
      xmlhttp.send("id=" + encodeURIComponent(uwid) + "&co=" + encodeURIComponent(coid) + "&action=checkin");
    }

    function checkOut(uwid, asset) {
      xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
          resp = JSON.parse(xmlhttp.responseText);
          setupScreen(resp);
        }
      };
      xmlhttp.open("POST", "exec.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
      xmlhttp.send("id=" + encodeURIComponent(uwid) + "&asset=" + encodeURIComponent(asset) + "&action=checkout");
    }

    var uwidList = new Array();
    var assetList = new Array();

    function updateUwidList(async) {
      if(typeof(async) === 'undefined') { async = true; }
      active = document.activeElement;
      xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
          resp = JSON.parse(xmlhttp.responseText);
          uwids = document.getElementById("uwids");
          if(resp.length != uwidList.length) {
            while(uwids.firstChild) {
              uwids.removeChild(uwids.firstChild);
            }
            for(i = 0; i < resp.length; i++) {
              o = document.createElement("option");
              o.setAttribute("value", resp[i]);
              uwids.appendChild(o);
            }
            uwidList = resp;
            active.focus();
          }
        }
      };
      xmlhttp.open("POST", "exec.php", async);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
      xmlhttp.send("action=getUwidList");
    }

    function updateAssetList(async) {
      if(typeof(async) === 'undefined') { async = true; }
      active = document.activeElement;
      xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
          resp = JSON.parse(xmlhttp.responseText);
          assets = document.getElementById("assets");
          if(resp.length != assetList.length) {
            while(assets.firstChild) {
              assets.removeChild(assets.firstChild);
            }
            for(i = 0; i < resp.length; i++) {
              o = document.createElement("option");
              o.setAttribute("value", resp[i]);
              assets.appendChild(o);
            }
            assetList = resp;
            active.focus();
          }
        }
      };
      xmlhttp.open("POST", "exec.php", async);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
      xmlhttp.send("action=getAssetList");
    }

    function updateLists(async) {
      if(typeof(async) === 'undefined') { async = false; }
      active = document.activeElement;
      xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
          resp = JSON.parse(xmlhttp.responseText);
          uwids = document.getElementById("uwids");
          if(resp.uwids && resp.uwids.length != uwidList.length) {
            while(uwids.firstChild) {
              uwids.removeChild(uwids.firstChild);
            }
            uwidList = resp.uwids;
            for(i = 0; i < uwidList.length; i++) {
              o = document.createElement("option");
              o.setAttribute("value", uwidList[i]);
              uwids.appendChild(o);
            }
            active.focus();
          }
          assets = document.getElementById("assets");
          if(resp.assets && resp.assets.length != assetList.length) {
            while(assets.firstChild) {
              assets.removeChild(assets.firstChild);
            }
            assetList = resp.assets;
            for(i = 0; i < assetList.length; i++) {
              o = document.createElement("option");
              o.setAttribute("value", assetList[i]);
              assets.appendChild(o);
            }
            active.focus();
          }
        }
      };
      xmlhttp.open("POST", "exec.php", async);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
      xmlhttp.send("action=getLists");
    }
    </script>
  </head>

  <body onload="updateLists(); setInterval(updateLists, 5000);">
    <datalist id="uwids"></datalist>
    <datalist id="assets"></datalist>
    <table>
      <tr>
        <td>
          <table class="form">
            <tr><td><label for="uwid">uwID:</label></td><td><input type="text" name="uwid" id="uwid" maxlength=8 size=8 autofocus="autofocus" list="uwids" onkeyup="if(validateIDField(this) && event.keyCode == 13) {submitID(this.value);}" onblur="if(validateIDField(this)) { submitID(this.value);}" /> <span id="loading"></span></td></tr>
            <tr><td><label for="name">Name:</label></td><td><input type="text" name="name" id="name" title="Double-click to edit" readonly=true ondblclick="if(getId() != null) {this.readOnly=false;}" onkeyup="if(!this.readonly && event.keyCode == 13) {this.readOnly=true; saveName(this.value);}" onblur="if(!this.readonly) {this.readOnly=true; saveName(this.value);}" /></td></tr>
          </table>
        </td>
        <td style="padding:12px;"> or </td>
        <td>
          <table class="form"><tr><td><label for="item">Item:</label></td><td><input type="text" name="item" id="item" list="assets" onkeyup="if(this.value.length != 0 && event.keyCode == 13) {checkItemStatus(this.value);}" onblur="if(this.value.length != 0) {checkItemStatus(this.value);} " /></td></tr></table>
        </td>
        <td style="padding:12px;"> or </td>
        <td>
          <a href="javascript:checkItemStatus(null);">View All Checkouts</a>
        </td>
      </tr>
    </table>

    <div id="checkout"></div>

    <div id="out"></div>
    
    <div id="hist"></div>
  </body>
</html>

