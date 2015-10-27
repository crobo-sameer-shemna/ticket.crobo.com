/**
 * Created by sameer on 27/10/15.
 */

$(document).ready(function(){

});

$(window).load(function(){
    $("body").bind("ajaxSend", function(e, xhr, settings){
        //Sent
    }).bind("ajaxComplete", function(e, xhr, settings){
        bodyAjaxComplete(e, xhr, settings);
    }).bind("ajaxError", function(e, xhr, settings, thrownError){
        //Error
    });
    hidePlatformDependents();
});

var parentPlatform = 'select.platform';
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
    $(parentPlatform).on('change', function() {
        resetPlatformDependents();
        hidePlatformDependents();
    });
    hidePlatformDependents();
}

function resetPlatformDependents(){
    for(var key in dependentsPlatform){
        $('select.'+key).val('');
    }
}
function hidePlatformDependents(){
    for(var key in dependentsPlatform){
        $($('select.'+key).parents('tr')[0]).hide();
    }
    if((typeof($(parentPlatform)) !== 'undefined')
        && ($(parentPlatform).val() !== '')) {
        var selDependent = getSelectedDependent($(parentPlatform).children('option:selected').text());
        if(typeof(selDependent) !== 'undefined'){
            $($('select.' + selDependent.cssClass).parents('tr')[0]).show();
        }
    }
}
function getSelectedDependent(selText){
    for(var key in dependentsPlatform){
        if(dependentsPlatform[key].parentText === selText){
            return dependentsPlatform[key];
        }
    }
}

