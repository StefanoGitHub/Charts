//script.js
"use strict";

//validate form
$("button[type='submit']").click(function(event) {
    //check mm-dd-yy
    var regexDate = /(^(((0[1-9]|1[012])[\-](0[1-9]|1[0-9]|2[0-8]))|((0[13578]|1[02])[\-](29|30|31))|((0[4,6,9]|11)[\-](29|30)))[\-]\d\d$)|(^02[\-]29[\-](00|04|08|12|16|20|24|28|32|36|40|44|48|52|56|60|64|68|72|76|80|84|88|92|96)$)/g;

    ////match mm-dd-yyyy
    //var regexDate = /(^(((0[1-9]|1[012])[\-](0[1-9]|1[0-9]|2[0-8]))|((0[13578]|1[02])[\-](29|30|31))|((0[4,6,9]|11)[\-](29|30)))[\-](19|[2-9][0-9])\d\d$)|(^02[\-]29[\-](19|[2-9][0-9])(00|04|08|12|16|20|24|28|32|36|40|44|48|52|56|60|64|68|72|76|80|84|88|92|96)$)/g;

    var dateValid = true;
    $("input.date").each(function (i, e) {
        if (!$(e).val().match(regexDate)) {
            //check if at least one date input does not match the pattern
            e.focus();
            dateValid = false;
        }
    });
    if (!dateValid) {
        $("#error").text("Please check date format").show().fadeOut(2000);
        //prevent submission
        event.preventDefault();
        return;
    }

    if ($('#upload').length) {
        //submit the form if at least one file were selected
        var anySelected = false;
        $("input[name='files[]']").each(function (i, e) {
            //check if at least one file was selected
            if (e.checked) {
                anySelected = true;
            }
        });
        if (!anySelected) {
            $("#error").text("Please select at least one file").show().fadeOut(2000);
            //prevent submission
            event.preventDefault();
            return;
        }
    }
    
    if ($('#select').length) {
        //submit the form if at leas onr position and number were selected
        var anyNumSelected = false;
        var anyPosSelected = false;

        $('input[data-check="num"]').each(function (i, e) {
            //check if at least one file was selected
            if (e.checked) {
                anyNumSelected = true;
            }
        });
        $('input[data-check="pos"]').each(function (i, e) {
            //check if at least one file was selected

            if (e.checked) {
                anyPosSelected = true;
            }
        });
        if (!anyNumSelected || !anyPosSelected) {
            $("#error").text("Please select position and number for each measure").show().fadeOut(2000);
            //prevent submission
            event.preventDefault();
            return;
        }
    }
    
});
