jQuery(function($) {

    // If change obfuscated field
    $('#exampal_mailchimp_api_key_obfuscated').on('change textInput input', function(){
        var obgFieldValue = $(this).val();

        if (obgFieldValue.indexOf("**") != -1)
            obgFieldValue = '';

        $(this).attr('type', 'hidden');
        $('#exampal_mailchimp_api_key').attr('type', 'text');
        $('#exampal_mailchimp_api_key').val(obgFieldValue);
    });

    // Set checkboxes values into text input
    $('#exampal_mailchimp_lists_checkboxes input').on('change', function(){
        var curListsStr = $('#exampal_mailchimp_lists').val();
        var curListsArr = new Array();

        if (curListsStr.length > 0) var curListsArr = curListsStr.split(",");

        var changedList = $(this).val();
        if ($(this).prop('checked')) {
            if (curListsStr.indexOf(changedList) == -1) {
                curListsArr.push(changedList);
            }
        } else {
            if (curListsStr.indexOf(changedList) != -1) {
                var indexList = curListsArr.indexOf(changedList);
                if (indexList > -1) {
                    curListsArr.splice(indexList, 1);
                }
            }
        }

        curListsStr = curListsArr.join(",");
        $('#exampal_mailchimp_lists').val(curListsStr);
    });

    // Spreadsheet tabs
    $('#exampalAdminSpreadsheet .exampal-tab-button').on('click', function(e){
        e.preventDefault();
        $('#exampalAdminSpreadsheet .exampal-tab-button').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        var setTab = $(this).data('exampal-tab');

        $('#exampalAdminSpreadsheetTabs .exampal-tab').hide();
        $('#exampalAdminSpreadsheetTabs .exampal-tab[data-exampal-tab = "'+setTab+'"]').show();

        return false;
    });

});