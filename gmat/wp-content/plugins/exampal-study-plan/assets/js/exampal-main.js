jQuery(function($) {


    $(document).on('ready', function () {
        if ($('.txtDate').length > 0) {
            /*$('#txtDate').datepicker({ dateFormat: 'dd-mm-yy' });*/
            $('.txtDate').datepicker({
                firstDay: 1
            });
            $('#custominputbg .txtDate').datepicker('setDate', 'today');
        }
        /* Start of weird previous developer code */
        $(".custommobiledropdown").hide();
        $('.custombutton').click(function () {
            $(".custommobiledropdown").toggle('slow');
        });

        $("#dropdowndatefield").hide();
        $('#parent_dropdowndatefield').click(function () {
            $(this).toggleClass('parent_dropdowndatefield_bg');
            $("#dropdowndatefield").toggle('slow');
        });

        /*$("#customdropdowncalendar").hide();
        $('#parent_dropdowndatefield2').click(function () {
            $(this).toggleClass('parent_dropdowndatefield_bg');
            $("#customdropdowncalendar").toggle('slow');
        });*/

        $("#customdropdownplan").hide();
        $('#parent_dropdowndatefield3').click(function () {
            $(this).toggleClass('parent_dropdowndatefield_bg');
            $("#customdropdownplan").toggle('slow');
        });
        $("#dropdowndatefield5").hide();
        $('#parent_dropdowndatefield4').click(function () {
            $(this).toggleClass('parent_dropdowndatefield_bg');
            $("#dropdowndatefield5").toggle('slow');
        });
        /* End of weird previous developer code */
    });

    /*Submit form on gmat-study-planner-calendar page if user change plan*/

    $('.plan-list').change(function(){  
        $(this).parents('form').submit();   
    });

     /*Submit form on gmat-study-planner-calendar page if user change date*/

    $('.customcalendarhead5 .txtDate').change(function(){
        $(this).parents('form').submit();  
    });

    /*Download study plan*/

    $('.customcalendarhead6 a').on('click', function (e) {
        e.preventDefault();
        $('.csv-button').hide();
        $('.csv-loading').show();
        var plan = $('#customselectionmenu').val(),
            date = $('input[name="exampal-plan-date"]').val(),
            data = {
                action: 'study_plan_csv',
                'plan': plan,
                'exampal-plan-date': date,
                security: exampalGlobal.ajax_nonce_csv
            };
        jQuery.post(
            exampalGlobal.ajaxurl,
            data,
            function (result) {
                $('.csv-loading').hide();
                $('.csv-button').show();
                document.location.href = result;
            }
        );
    });

    /* Submit export to google or outlook */
    $('.calendar-export-google').on('change', function(e){
        var url = $(this).val();
        if (url !== undefined) {
            window.open(url);
            $(this).val('');
            $(this).find('option').first().attr('selected', 'selected');
        }
    });

   /* $('.addcalendar_desk').change(function(){
        $(this).parents('form').attr('target', '_blank');
        $(this).parents('form').submit();
        $(this).val('');
        $(this).find('option').first().attr('selected', 'selected');
        $(this).parents('form').attr('target', '');
        /!*$('.export-button-wrap').hide();
        $('.export-loading').show();*!/
    });*/

    $('.addcalendar_desk').change(function(){

    });

    /* Validate email */
    $("#exampal-plan-form").validate({
        messages: {
            'exampal-plan-email': {
                required: "email address not valid",
                minlength: "email address not valid"
            }
        }
    });
   
});