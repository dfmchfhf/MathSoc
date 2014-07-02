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
function onCheckouts(data) {
  var fav = null,
      occs = {},
      total = 0,
      current = 0;
  $('title').text('{0} | People'.format(data.id));
  $.each(data.checkouts, function(index, co) {
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

  console.log(fav.obj);
  $('[field="name"]').text(fav.obj.name);
  $('[field="uwid"]').text(fav.obj.uwid);
  $('[field="fav"]').text(fav.obj.asset);
  $('[field="checkedout"]').text(current);
  $('[field="total"]').text(total);
}

$('document').ready(function() {
  var id = getQueryParam('id');
  searchId(id);
});
