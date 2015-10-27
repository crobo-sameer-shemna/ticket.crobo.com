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

var dependentsPlatform = [
    'salesforce',
    'cis'
];

function bodyAjaxComplete(e, xhr, settings){
    console.log('bodyAjaxComplete');

    $('select.platform').on('change', function() {
        alert( this.value ); // or $(this).val()
        resetPlatformDependents();
        $('select.'+this.value).show();
    });
    resetPlatformDependents();
}

function resetPlatformDependents(){
    for(var i=0; i<dependentsPlatform.length; i++){
        $('select.'+dependentsPlatform[i]).val('').hide();
    }
}

