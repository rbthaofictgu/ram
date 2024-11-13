// {
// $('tr').click( function() {
// if(typeof($(this).find('a').attr('href')) != "undefined" && $(this).find('a').attr('href') !== null) {	
//     window.location = $(this).find('a').attr('href');
// }	
// }).hover( function() {
//     $(this).toggleClass('hover');
// });
// }

$('tr').click(function() {
    var href = $(this).find('a').attr('href');
    if (href) {	
        window.location = href;
    }	
}).hover(function() {
    $(this).toggleClass('hover');
});
