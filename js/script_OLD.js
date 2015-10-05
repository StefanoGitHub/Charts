/**
 * Created by Stefano on 9/1/15.
 *
 * icanhaz.js
 *
 *
 *
 */
"use strict";

//add new row at the bottom of the table
$('#addRow').click(function () {

    var newRowContent,
        newRow,
        rowCount = $('#table2 tr').length;

    //assigning {{ index }} a value
    newRowContent = {
        index: rowCount - 1
    };

    // Here's all the magic of ICanHaz.js
    newRow = ich.newRow(newRowContent);

    // append it to the list, tada!
    $('#table2').append(newRow);

    //increment the number of measuresToChart
    $('#files').attr('value', rowCount);

});

//delete last row in the table
$('#delRow').click(function () {
    var rowCount = $('#table2 tr').length;
    //do not delete header and first row
    if (rowCount > 2) {
        $('#table2 tr:last').remove();
        $('#files').attr('value', rowCount - 2);
    }
});

/*
$('#deleteTable').click(function (event) {
    if (!confirm('Are you sure you want to \nDELETE the ENTIRE TABLE??')) {
        event.preventDefault()
    }
});
*/

/* no jQuery, working:
 if (document.getElementById("upload")) {
 document.getElementById("upload").onsubmit = function () {
 var files = document.getElementsByName("files[]")
 var anySelected = false;
 for (var i=0; i<files.length; i++) {
 if (files[i].checked) {
 anySelected = true;
 }
 }
 // prevent a form from submitting if no email.
 if (anySelected) {
 // reset and allow the form to submit
 document.getElementById("error").innerHTML = "";
 return true;
 } else {
 document.getElementById("error").innerHTML = "Please select at least one file";
 // to STOP the form from submitting
 return false;
 }
 };
 }
 */

//submit the form if at least one file were selected
$('#upload').submit(function () {
    var files = document.getElementsByName("files[]");
    var anySelected = false;
    //check that at least one file were selected
    for (var i=0; i<files.length; i++) {
        if (files[i].checked) {
            anySelected = true;
        }
    }
    // prevent to submit if no file were selected.
    if (anySelected) {
        // reset and allow the form to submit
        document.getElementById("error").innerHTML = "";
        return true;
    } else {
        document.getElementById("error").innerHTML = "Please select at least one file";
        //stop the form from submitting
        return false;
    }
});

//redirect to select_data page
$('#newChart').click(function () {
    //redirect to page
    window.location.href = "select_data.php";
});

//redirect to upload page
$('#newUpload').click(function () {
    //redirect to page
    //window.location.href = "upload.php";
    //open a new tab to upload.php
    window.open('upload.php', '_blank');
});

//select all files; all data in all rows becomes required
$('#selectAll').click(function() {
    if($('#selectAll').is(':checked') ){
        $('.select_checkbox').prop('checked', true);
        $('td.measureType input:radio').prop('required', true);
        $('td.measurePosition input:radio').prop('required', true);
        $('td.select select').prop('required', true);
    } else {
        $('.select_checkbox').removeAttr('checked');
        $('td.measureType input:radio').prop('required', false);
        $('td.measurePosition input:radio').prop('required', false);
        $('td.select select').prop('required', false);
    }
});

//set the same measurement type in all rows as the first one
$('#sameType').click(function() {
    if($('#sameType').is(':checked') ){
        if ($('.irradiance:first').is(':checked')) {
            $('.irradiance').prop('checked', true);
            $('.transmittance').prop('checked', false);
            $('.reference').prop('checked', false);
        }
        if ($('.transmittance:first').is(':checked')) {
            $('.irradiance').prop('checked', false);
            $('.transmittance').prop('checked', true);
            $('.reference').prop('checked', false);
        }
        if ($('.reference:first').is(':checked')) {
            $('.irradiance').prop('checked', false);
            $('.transmittance').prop('checked', false);
            $('.reference').prop('checked', true);
        }
    }
});

//if a file is selected, all data in the same row become required
$('tr td input:checkbox').click(function() {
    var row = this.id;
    if($(this).is(':checked') ){
        $('tr:nth-child('+row+') td.measureType input:radio').prop('required', true);
        $('tr:nth-child('+row+') td.measurePosition input:radio').prop('required', true);
        $('tr:nth-child('+row+') td.select select').prop('required', true);
    } else {
        $('tr:nth-child('+row+') td.measureType input:radio').prop('required', false);
        $('tr:nth-child('+row+') td.measurePosition input:radio').prop('required', false);
        $('tr:nth-child('+row+') td.select select').prop('required', false);
    }
});