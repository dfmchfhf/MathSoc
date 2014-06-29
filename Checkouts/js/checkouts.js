/*global $, jQuery, onCheckouts */
$('.panel-collapsible > .panel-heading').on('click', function() {
  $(this).siblings('.panel-body').toggle('blind');
});

$('.has-tooltip').tooltip({
  content: function (cb) {
    cb($(this).prop('title'));
  }
});
$('.has-tooltip').removeClass('has-tooltip');

var checkoutPanel = $('#co_panel');
var checkoutByUwid = $('#co_uwid');
var checkoutByItem = $('#co_item');

checkoutByUwid.hide();
checkoutByItem.hide();

(function($) {
  $.fn.toggleReadonly = function(state) {
    return this.each(function() {
      if (typeof(state) === "undefined" || $(this).prop('readonly') == !state) {
        $(this).prop('readonly', !$(this).prop('readonly'));
      }
    });
  };
})(jQuery);
function hideEditName() {
  $('#se_uwid_name').hide('blind');
}
function showEditName() {
  lockEditName();
  $('#se_uwid_name').show('blind');
}
function allowEditName() {
  $('#se_uwid_name_txt').tooltip("disable");
  $('#se_uwid_name_txt').toggleReadonly(false);
  $('#se_uwid_name_btn').children('span').addClass('ui-icon-disk');
  $('#se_uwid_name_btn').children('span').removeClass('ui-icon-pencil');
  $('#se_uwid_name_txt').focus();
}
function lockEditName() {
  $('#se_uwid_name_txt').tooltip("enable");
  $('#se_uwid_name_txt').toggleReadonly(true);
  $('#se_uwid_name_btn').children('span').addClass('ui-icon-pencil');
  $('#se_uwid_name_btn').children('span').removeClass('ui-icon-disk');
}
function saveEditName() {
  if ($('#se_uwid_name_txt').val()) {
    lockEditName();
    $.ajax({
      url: 'exec.php',
      type: 'POST',
      data: {
        action: 'saveName',
        id: $('#se_uwid_name_id').val(),
        name: $('#se_uwid_name_txt').val()
      },
      dataType: 'JSON'
    }).done(displayCheckouts);
  } else {
    $('#se_uwid_name_txt').focus();
  }
}
function validateId(id) {
  return id.match(/^\d{8}$/);
}
function submitIdSearch() {
  var idField = $('#se_uwid_id');
  if (validateId(idField.val())) {
    searchId(idField.val());
  } else {
    idField.addClass('invalid');
    idField.focus();
  }
}
function searchId(id) {
  $('#se_item_id').val(null);
  clearScreen();
  $.ajax({
    url: 'exec.php',
    type: 'POST',
    data: {
      action: 'getUwidCheckouts',
      id: id
    },
    dataType: 'JSON'
  }).done(displayCheckouts);
}
function submitItemSearch() {
  var itemField = $('#se_item_id');
  if (itemField.val()) {
    searchItem(itemField.val());
  } else {
    itemField.focus();
  }
}
function searchItem(item) {
  $('#se_uwid_id').val(null);
  $('#se_uwid_name_txt').val(null);
  clearScreen();
  $.ajax({
    url: 'exec.php',
    type: 'POST',
    data: {
      action: 'getItemCheckouts',
      item: item
    },
    dataType: 'JSON'
  }).done(displayCheckouts);
}
function viewAllCheckouts() {
  $('#se_item_id').val(null);
  $('#se_uwid_id').val(null);
  $('#se_uwid_name_txt').val(null);
  clearScreen();
  $.ajax({
    url: 'exec.php',
    type: 'POST',
    data: {
      action: 'getAllCheckouts'
    },
    dataType: 'JSON'
  }).done(displayCheckouts);
}
function viewProfile(id) {
  window.location = '/people.php?id=' + id;
}
function clearScreen() {
  hideEditName();
  checkoutPanel.hide();
  checkoutByUwid.hide();
  checkoutByItem.hide();
  $('#hist_panel').hide();
  $('#hist_cur_table > tbody').empty();
  $('#hist_prev_table > tbody').empty();
}
function displayCheckouts(resp) {
  clearScreen();
  if (resp.type == 'uwid') {
    showEditName();
    $('#se_uwid_id').val(resp.id);
    $('#se_uwid_name_id').val(resp.id);
    $('#se_uwid_name_txt').val(resp.name);
    if (!resp.name) {
      allowEditName();
      return;
    }
    checkoutByUwid.show();
    $('#co_uwid_item').val(null);
    $('#co_uwid_user').text(resp.name + ' (' + resp.id + ')');
    $('#co_uwid_uwid').val(resp.id);
    checkoutPanel.show('blind');
    $('#co_uwid_item').focus();
  } else if (resp.type == 'item') {
    checkoutByItem.show();
    $('#co_item_uwid').val(null);
    $('#co_item_item').text(resp.item);
    $('#co_item_name').val(resp.item);
    checkoutPanel.show('blind');
    $('#co_item_uwid').focus();
  }
  var checkouts = resp.checkouts || [];
  $.each(checkouts, function(i, co) {
    var uwid = $('<a>').text(co.uwid),
        unam = $('<a>').text(co.name),
        item = $('<a>').text(co.asset),
        otime = document.createTextNode(co.out),
        itime = co.in == null ? null : document.createTextNode(co.in);
    uwid.on('dblclick', function() {
      viewProfile(co.uwid);
    });
    unam.on('dblclick', function() {
      viewProfile(co.uwid);
    });
    item.on('dblclick', function() {
      searchItem(co.asset);
    });
    uwid.attr('title', 'Double-click to view customer history');
    unam.attr('title', 'Double-click to view customer history');
    item.attr('title', 'Double-click to view item history');
    uwid.addClass('has-tooltip');
    unam.addClass('has-tooltip');
    item.addClass('has-tooltip');

    var ci = $('<button>');
    ci.addClass('btn has-tooltip');
    ci.on('blur mouseout', function() {
      $(this).removeClass('btn-confirm');
    });

    var tbody = null,
        cols = [uwid, unam, item, otime];
    if (co.in) {
      ci.text('Check Out');
      ci.attr('title', 'Click to check <strong>' + co.asset + '</strong> back out to <strong>' + co.name + '</strong>');
      var doCheckOut = function() {
        checkOut(co.uwid, co.asset);
      };
      ci.on('click', function() {
        if ($(this).hasClass('btn-confirm')) {
          doCheckOut();
        } else {
          $(this).addClass('btn-confirm');
        }
      });
      cols.push(itime);
      tbody = $('#hist_prev_table > tbody');
    } else {
      ci.text('Check In');
      ci.attr('title', 'Click to check <strong>' + co.asset + '</strong> in from <strong>' + co.name + '</strong>');
      var doCheckIn = function() {
        checkIn(co.uwid, co.id);
      };
      ci.on('click', function() {
        if ($(this).hasClass('btn-confirm')) {
          doCheckIn();
        } else {
          $(this).addClass('btn-confirm');
        }
      });
      tbody = $('#hist_cur_table > tbody');
    }
    cols.push(ci);
    var row = $('<tr>');
    $.each(cols, function(i, s) {
      row.append($('<td>').append(s));
    });
    tbody.append(row);
  });
  $('#hist_panel').show('blind');
  $('.has-tooltip').tooltip({
    content: function (cb) {
      cb($(this).prop('title'));
    }
  });
  $('.has-tooltip').removeClass('has-tooltip');

  if (typeof onCheckouts == 'function') {
    onCheckouts(resp);
  }
}
$('#se_uwid_id').on('input propertychange paste autocompleteselect', function() {
  hideEditName();
  $('#se_uwid_id').removeClass('invalid');
});
$('#se_uwid_id').on('keydown', function(e) {
  if (e.which == 13) /* ENTER */ {
    submitIdSearch();
  }
});
$('#se_uwid_submit').on('click', submitIdSearch);
$('#se_uwid_name_txt').on('dblclick', function() {
  allowEditName();
});
$('#se_uwid_name_txt').on('keydown', function(e) {
  if (e.which == 13) /* ENTER */ {
    saveEditName();
  }
});
$('#se_uwid_name_btn').on('click', function() {
  if ($('#se_uwid_name_btn').children('span').hasClass('ui-icon-disk')) {
    saveEditName();
  } else {
    allowEditName();
  }
});
$('#se_item_id').on('keydown', function(e) {
  if (e.which == 13) /* ENTER */ {
    submitItemSearch();
  }
});
$('#se_item_submit').on('click', submitItemSearch);
$('#se_uwid_id, #co_item_uwid').autocomplete({
  minLength: 0,
  source: function(r, cb) {
    $.ajax({
      url: 'exec.php',
      type: 'POST',
      data: {
        action: 'getUwidList',
        q: r.term
      },
      dataType: 'JSON'
    }).done(function(resp) {
      cb($.map(resp, function(item) { return { label: item.id + ' \u2014 ' + item.name, value: item.id }; }));
    });
  }
});
$('#se_item_id, #co_uwid_item, #se_asset_name').autocomplete({
  minLength: 0,
  source: function(r, cb) {
    $.ajax({
      url: 'exec.php',
      type: 'POST',
      data: {
        action: 'getItemList',
        q: r.term
      },
      dataType: 'JSON'
    }).done(function(resp) {
      cb($.map(resp, function(item) { return { label: item.name, value: item.name }; }));
    });
  }
});
$('#se_uwid_id, #co_item_uwid, #se_item_id, #co_uwid_item, #se_asset_name').on('dblclick', function() {
  $(this).autocomplete("search");
});
$('#view_all').on('click', viewAllCheckouts);

function checkIn(uwid, coid) {
  $.ajax({
    url: 'exec.php',
    type: 'POST',
    data: {
      action: 'checkin',
      id: uwid,
      coid: coid
    },
    dataType: 'JSON'
  }).done(displayCheckouts);
}
function checkOut(uwid, asset) {
  $.ajax({
    url: 'exec.php',
    type: 'POST',
    data: {
      action: 'checkout',
      id: uwid,
      asset: asset
    },
    dataType: 'JSON'
  }).done(displayCheckouts);
}
function submitCheckoutUwid() {
  var itemField = $('#co_uwid_item');
  if (itemField.val()) {
    checkOut($('#co_uwid_uwid').val(), itemField.val());
  } else {
    itemField.focus();
  }
}
function submitCheckoutItem() {
  var idField = $('#co_item_uwid');
  if (validateId(idField.val())) {
    checkOut(idField.val(), $('#co_item_name').val());
  } else {
    idField.addClass('invalid');
    idField.focus();
  }
}
$('#co_uwid_submit').on('click', submitCheckoutUwid);
$('#co_uwid_item').on('keydown', function(e) {
  if (e.event == 13 || e.keyCode == 13) /* ENTER */ {
    submitCheckoutUwid();
  }
});
$('#co_item_submit').on('click', submitCheckoutItem);
$('#co_item_uwid').on('input propertychange paste autocompleteselect', function() {
  $('#co_item_uwid').removeClass('invalid');
});
$('#co_item_uwid').on('keydown', function(e) {
  if (e.event == 13) /* ENTER */ {
    submitCheckoutItem();
  }
});
function sortByColumn(index, elem) {
  var table = elem.parents('table').eq(0),
      tbody = table.find('tbody'),
      rows = tbody.children('tr'),
      carat = $('<span/>', {
        'class': "ui-icon"
      }),
      sortFn = function (a, b) {
        if (table.data('sort') == 'ascending') {
            return a > b;
        } else {
            return a < b;
        }
      };

  table.find('th .ui-icon').remove();
  if (table.data('index') != index) {
    table.data('index', index);
    table.data('sort', 'ascending');
    carat.addClass('ui-icon-carat-1-n');
  } else {
    if (table.data('sort') == 'ascending') {
      table.data('sort', 'descending');
      carat.addClass('ui-icon-carat-1-s');
    } else {
      table.data('sort', 'ascending');
      carat.addClass('ui-icon-carat-1-n');
    }
  }

  carat.addClass('inline-block')
        .addClass('sort-icon')
        .appendTo(elem);
  tbody.empty();
  rows.sort(function (row1, row2) {
    var col1, col2;
    col1 = $(row1).children('td').eq(index).text();
    col2 = $(row2).children('td').eq(index).text();
    return sortFn(col1, col2);
  }).appendTo(tbody);
}
$('table th').on('click', function () {
  var index;

  index = $(this).parent()
        .children()
        .index($(this));
  sortByColumn(index, $(this));
});
function searchItems(callback, data) {
  $.ajax({
    url: 'exec.php',
    type: 'POST',
    data: $.extend({}, data, {
        action: 'getItems'
    }),
    dataType: 'JSON'
  }).done(callback);
}
function displayItems(data, filterFn) {
  var tbody,
      items = data.items || [],
      table = $('#asset_table');

  if (filterFn && typeof filterFn == 'function') {
    items = items.filter(filterFn);
  }

  tbody = table.find('tbody');
  tbody.empty();
  items = items.map(function (item, index) {
    var students = item.students || [],
        row = $('<tr/>'),
        col = $('<td/>');

    students = students.map(function (student, i) {
      var a = $('<a/>');
      a.attr('href', "/people.php?id=" + student);
      a.text(student);
      return a[0].outerHTML;
    });

    col.clone().text(item.name).appendTo(row);
    col.clone().text(item.stock).appendTo(row);
    col.clone().text(item.total).appendTo(row);
    col.append($(students.join(', ')));
    col.appendTo(row);
    return row[0].outerHTML;
  });
  tbody.append($(items.join('')));
}
$('#se_asset_search').on('click', function (ev) {
  $('.view_assets').removeClass('selected');
  searchItems(displayItems, {
    'item': $('#se_asset_name').val()
  });
});
$('#se_asset_name').on('keydown', function (ev) {
  if (ev.keyCode == 13) {
    $('#se_asset_search').click();
  }
});
$('.view_assets').on('click', function (ev) {
  var fn, filter;

  $('.view_assets').removeClass('selected');
  $('#se_asset_name').val('');
  fn = $(this).addClass('selected')
              .data('fn');

  filter = function (item) {
    if (fn == "out-stock") {
      return item.stock == 0;
    } else if (fn == "in-stock") {
      return item.stock > 0;
    }
    return true;
  };

  searchItems(function (data) {
    displayItems(data, filter);
  });
});
