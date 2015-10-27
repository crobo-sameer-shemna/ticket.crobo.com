/**
 * Created by sameer on 27/10/15.
 */

$(document).ready(function(){
    console.log('doc ready');
});

$(window).load(function(){
    console.log('win load');
    $("body").bind("ajaxSend", function(e, xhr, settings){
        //Sent
    }).bind("ajaxComplete", function(e, xhr, settings){
        bodyAjaxComplete(e, xhr, settings);
    }).bind("ajaxError", function(e, xhr, settings, thrownError){
        //Error
    });
});

function bodyAjaxComplete(e, xhr, settings){
    console.log('bodyAjaxComplete');
    $('select.list-2').on('change', function() {
        alert( this.value ); // or $(this).val()
    });
}
