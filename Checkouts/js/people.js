/*global $, jQuery, searchId, getQueryParam */
function searchForUser(id, callback) {
  $.ajax({
    url: 'exec.php',
    type: 'POST',
    data: {
      action: 'getUwidCheckouts',
      id: id
    },
    dataType: 'JSON'
  }).done(callback);
}

var profilePanel = $('#profile_panel');

function onCheckouts(data) {
  var fav = null,
      occs = {},
      total = 0,
      current = 0;
  $('title').text('{0} | People'.format(data.id));
  $.each(data.checkouts || [], function(index, co) {
    if (occs[co.asset]) {
      occs[co.asset]['count'] += 1;
    } else {
      occs[co.asset] = {
        count: 1,
        obj: co
      };
    }
    if (fav === null || occs[co.asset]['count'] > fav['count']) {
      fav = occs[co.asset];
    }

    if (co.in === null) {
      current++;
    }
    total++;
  });

  $('[field="name"]').text(data.name);
  $('[field="uwid"]').text(data.id);
  $('[field="fav"]').text(fav ? fav.obj.asset : '--');
  $('[field="checkedout"]').text(current);
  $('[field="total"]').text(total);
  if (data.image) {
    $('[field="picture"]').attr('src', data.image);
  }
  profilePanel.show('blind');
  profilePanel.children('.panel-body').show('blind');
}

$('document').ready(function() {
  var id = getQueryParam('id');
  profilePanel.hide();
  searchId(id);
});
