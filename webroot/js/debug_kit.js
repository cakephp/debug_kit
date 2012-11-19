$(document).ready(function () {
	$('#debug-kit-toolbar > ul#panel-tabs table').each(function() {
        var $table = $(this);
        $('th', $table).each(function(column) {
            var $header = $(this);
            $header.live('click', function() {
				var direction = 1;
				if($header.hasClass('asc')) {
					$('th', $table).removeClass('asc desc');
					$header.removeClass('asc');
					$header.addClass('desc');
				} else if($header.hasClass('desc')) {
					$('th', $table).removeClass('asc desc');
					direction = 0;
					$header.removeClass('desc');
					$header.addClass('asc');
				} else {
					$('th', $table).removeClass('asc desc');
					$header.addClass('asc');
				}

                var rows = $table.find('tbody > tr').get();
                rows.sort(function(a, b) {
                    var keyA = $(a).children('td').eq(column).text();
                    var keyB = $(b).children('td').eq(column).text();

					if(isNaN(keyA) || isNaN(keyB)) {
						keyA.toUpperCase();
						keyB.toUpperCase();
					} else {
						keyA = parseFloat(keyA);
						keyB = parseFloat(keyB);
					}
                    if(keyA < keyB) {
						return direction ? 1 : -1;
					}
                    if(keyA > keyB) {
						return direction ? -1 : 1;
					}
                    return 0;
                });

                $.each(rows, function(index, row) {
                    $table.children('tbody').append(row);
                });
            });
        });
    });
});