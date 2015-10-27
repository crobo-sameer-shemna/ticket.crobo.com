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

var dependentsPlatform = {
    'salesforce': {
        cssClass: 'salesforce',
        parentText: 'Salesforce'
    },
    'cis': {
        cssClass: 'cis',
        parentText: 'Crobo Intelligence System'
    }
};

function bodyAjaxComplete(e, xhr, settings){
    console.log('bodyAjaxComplete');

    $('select.platform').on('change', function() {
        var selText = $(this).children('option:selected').text();
        console.log(selText);
        resetPlatformDependents();
        var selDependent = getSelectedDependent(selText);
        console.log(selDependent.cssClass);
        $('select.'+selDependent.cssClass).show();
    });
    resetPlatformDependents();
}

function resetPlatformDependents(){
    for(var key in dependentsPlatform){
        $('select.'+key).val('').hide();
    }
}
function getSelectedDependent(selText){
    for(var key in dependentsPlatform){
        if(dependentsPlatform[key].parentText === selText){
            return dependentsPlatform[key];
        }
    }
}

