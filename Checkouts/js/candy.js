/*global jQuery, $, parseInt, dateString */
function candyRequest(data) {
  $.ajax({
    url: 'exec.php',
    type: 'POST',
    data: data,
    dataType: 'JSON'
  }).done(displayCandy);
}
function finishedCandy(id) {
  candyRequest({
    id: id,
    action: 'pullOutCandy'
  });
}
function putOutCandy(id) {
  candyRequest({
    id: id,
    action: 'putOutCandy'
  });
}
function displayCandy(res) {
  var index,
      tbody,
      fmt = "",
      candies = res.candies || [],
      history = $('#candy_hist_table'),
      current = $('#candy_cur_table');

  for (index = 0; index < 4; ++index) {
    fmt += '<td>{' + index + '}</td>';
  }
  tbody = history.find('tbody').eq(0);
  tbody.empty();
  $.each(candies, function(i, c) {
    var average,
        cpt,
        btn = $('<button/>'),
        row = $('<tr/>');
    c.current_run = parseInt(c.current_run);
    c.time = parseInt(c.time);
    c.cost = new Number(parseFloat(c.cost));
    if (c.current_run === 0) {
      btn.addClass('btn has-tooltip');
      btn.on('blur mouseout', function () {
        $(this).removeClass('btn-confirm');
      });
      btn.text('Put Out Candy');
      btn.on('click', function () {
        if ($(this).hasClass('btn-confirm')) {
          putOutCandy(c.id);
        } else {
          $(this).addClass('btn-confirm');
        }
      });
      average = c.time / Math.max(c.runs, 1) / (1000 * 60);
      average = new Number(average);
      cpt = c.time == 0 ? 0 : (c.cost * c.runs) / (c.time / (1000 * 60));
      cpt = new Number(cpt);
      row.append($(fmt.format(c.name, c.cost.toPrecision(2), average.toPrecision(2), cpt.toPrecision(2))))
         .append($('<td/>').append(btn))
         .appendTo(tbody);
    }
  });

  tbody = current.find('tbody').eq(0);
  tbody.empty();
  $.each(candies, function(i, c) {
    var average,
        btn = $('<button/>'),
        row = $('<tr/>');  
    if (c.current_run !== 0) {
      btn.addClass('btn has-tooltip');
      btn.on('blur mouseout', function () {
        $(this).removeClass('btn-confirm');
      });
      btn.text('Finished');
      btn.on('click', function () {
        if ($(this).hasClass('btn-confirm')) {
          finishedCandy(c.id);
        } else {
          $(this).addClass('btn-confirm');
        }
      });
      average = c.time / Math.max(c.runs, 1) / (1000 * 60);
      average = new Number(average);
      row.append($(fmt.format(c.name, c.cost.toPrecision(2), dateString(c.current_run), average.toPrecision(2))))
         .append($('<td/>').append(btn))
         .appendTo(tbody);
    }
  });
}
$('#se_add_candy').on('click', function (ev) {
  var name = $('#se_add_candy_name'),
      cost = $('#se_add_candy_cost');

  if (name.val().length == 0) {
    name.addClass('invalid');
  }

  if (cost.val().length == 0) {
    cost.addClass('invalid');
  }

  if (!(name.hasClass('invalid') || cost.hasClass('invalid'))) {
    candyRequest({
      action: 'addCandy',
      name: name.val(),
      cost: cost.val()
    });
    name.val('');
    cost.val('');
  }
});
$('#se_candy_search').on('click', function (ev) {
  var name;
  name = $('#se_candy_name').val();
  candyRequest({
    action: 'getAllCandy',
    name: name
  });
});
$('#se_add_candy_name, #se_add_candy_cost').on('input propertychange paste autocompleteselect', function () {
  $(this).removeClass('invalid');
});
$('#se_candy_name').autocomplete({
  minLength: 0,
  source: function (r, cb) {
    $.ajax({
      url: 'exec.php',
      type: 'POST',
      data: {
        action: 'getAllCandy',
        q: r.term
      },
      dataType: 'JSON'
    }).done(function(resp) {
      cb($.map(resp['candies'], function(item) {
        return {
          label: item.name,
          value: item.name
        };
      }));
    });
  }
});
$('#se_candy_name').on('keydown', function (ev) {
  if (ev.keyCode == 13) {
    $('#se_candy_search').click();
  }
});

$('document').ready(function () {
  candyRequest({action: 'getAllCandy'});
});
