/**
 * Created by Stefano on 9/1/15.
 *
 * icanhaz.js
 *
 *
 *
 */
"use strict";


//add a measure table after the last one
$('#addTable').click(function () {
    var newTableContent,
        newTable,
        tablesCount = $('table').length,
        //measureNumber = tablesCount;
    //console.log(maesu)
    //assigning {{ tableNumber }} a value
    newTableContent = {
        measureNumber: ((tablesCount < 10 ? '0' : '') + tablesCount)
    };
    // Here's all the magic of ICanHaz.js
    newTable = ich.newTable(newTableContent);
    // append it to the list, tada!
    $('#tables').append(newTable);
    //increment the number of measuresToChart
    $('#linesToChart').attr('value', tablesCount);
});

//delete last table
$('#delTable').click(function () {
    var tablesCount = $('table').length;
    //do not delete header and first two tables
    if (tablesCount > 2) {
        $('#tables div:last-child').remove();
        $('#linesToChart').attr('value', tablesCount - 2);
    }
});



//add new row at the bottom of the correspondent table
$("#tables").on("click", "button.addRow", function() {
//$('button.addRow').click(function () {
    var newRowContent,
        newRow;
    var measureNumber = (this.id).slice(1, 3);
    var rowCount = $('#chartLine'+measureNumber+' tr').length;
    //assigning {{ index }} a value
    newRowContent = {
        measureNumber: measureNumber,
        index: rowCount - 1
    };
    // Here's all the magic of ICanHaz.js
    newRow = ich.newRow(newRowContent);
    // append it to the list, tada!
    $('#chartLine'+measureNumber).append(newRow);

    //increment the number of measuresToChart
    $('#m'+measureNumber+'_measuresToAverage').attr('value', rowCount);


});

//delete last row in the correspondent table
$("#tables").on("click", "button.delRow", function() {
    //$('button.delRow').click(function () {
    var measureNumber = (this.id).slice(1, 3);
    var rowCount = $('#chartLine'+measureNumber+' tr').length;
    //do not delete header and first row
    if (rowCount > 2) {
        $('#chartLine'+measureNumber+' tr:last').remove();
        $('#m'+measureNumber+'_measuresToAverage').attr('value', rowCount - 2);
    }
});




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