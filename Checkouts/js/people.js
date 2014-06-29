/*global $, jQuery, searchId */
RegExp.escape = function(s) {
  return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
};
String.prototype.format = function () {
  var patt,
      string = this,
      replacements = Array.prototype.slice.call(arguments, 0);

  // For each replacement text, replace the appropriately index
  // item
  for (var i = 0; i < replacements.length; ++i) {
    patt = new RegExp(RegExp.escape('{' + i + '}'), 'g');
    string = string.replace(patt, replacements[i]);
  }
  return string;
};
function getQueryParam(query) {
  var result = undefined,
      params = window.location.search;
  params = params.split(/[&\?]/);
  $.each(params, function(i, p) {
    var q, v;
    p = p.split(/[=]/);
    q = p[0];
    v = p[1];
    if (q === query) {
      result = v;
      return false;
    }
    return true;
  });
  return result;
}
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

  $('[field="fav"]').text(fav.obj.asset);
  $('[field="checkedout"]').text(current);
  $('[field="total"]').text(total);
}

$('document').ready(function() {
  var id = getQueryParam('id');
  searchId(id);
});
