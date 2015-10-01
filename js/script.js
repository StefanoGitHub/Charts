/**
 * Created by Stefano on 9/1/15.
 *
 * icanhaz.js
 *
 *
 *
 */
"use strict";

//if (document.getElementById("addRow")) {
//if ($('#addRow').length) {
    //$(document).ready(function () {
        // add a simple click handler for the "add user" button.
        $('#addRow').click(function () {
            var newRowContent,
                newRow,
                rowCount = $('#table2 tr').length;

            // build a simple user object, in a real app this
            // would probably come from a server somewhere.
            // Otherwise hardcoding here is just silly.
            newRowContent = {
                index: rowCount - 1
            };

            // Here's all the magic.
            newRow = ich.newRow(newRowContent);

            // append it to the list, tada!
            $('#table2').append(newRow);

            //increment the number of measuresToChart
            //var inputFiles = document.getElementById("files");
            //inputFiles.value = i;
            $('#files').attr('value', rowCount);

        });
    //});
//}

//if (document.getElementById("delRow")) {
//if ($('#delRow').length) {
        $('#delRow').click(function () {
        var rowCount = $('#table2 tr').length;
        //do not delete header and first row
        if (rowCount > 2) {
            $('#table2 tr:last').remove();
            $('#files').attr('value', rowCount - 2);
        }
    });
//}


//if (document.getElementById("deleteTable")) {
    $('#deleteTable').click(function (event) {
    //document.getElementById("deleteTable").addEventListener("click", function (event) {
        if (!confirm('Are you sure you want to \nDELETE the ENTIRE TABLE??')) {
            event.preventDefault()
        }
    });
//}

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
//if (document.getElementById("upload")) {
    $('#upload').submit(function () {
        var files = document.getElementsByName("files[]");
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
    });
//}


//button to redirect to a new chart page
//if (document.getElementById("#newChart")) {
//if ($('#newChart').length) {
    //$(document).ready(function () {
        $('#newChart').click(function () {
            //redirect to page
            window.location.href = "select_data.php";
        });
    //});

//}

//button to redirect to a new chart page
//if (document.getElementById("#newUpload")) {
//if ($('#newUpload').length) {
    //$(document).ready(function () {
        $('#newUpload').click(function () {
            //redirect to page
            //window.location.href = "upload.php";
            //open a new tab to upload.php
            window.open('upload.php', '_blank');
        });
    //});
//}


//select all files
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

//set the same measurement type (as the first one)
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