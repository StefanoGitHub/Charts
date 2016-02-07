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
    $('#linesToChart').attr('value', rowCount);

});

//delete last row in the table
$('#delRow').click(function () {
    var rowCount = $('#table2 tr').length;
    //do not delete header and first row
    if (rowCount > 2) {
        $('#table2 tr:last').remove();
        $('#linesToChart').attr('value', rowCount - 2);
    }
});

//if checkbox non is selected all other sample chackbox have to be unchecked
$("#table2").on("click", "input:checkbox.none", function() {
    var row = (this.id).charAt(0);
    console.log(row);
    if($(this).is(':checked') ){
        $('#'+row+'_num1').prop('disabled', true);
        $('#'+row+'_num1').prop('checked', false);
        $('#'+row+'_num2').prop('disabled', true);
        $('#'+row+'_num2').prop('checked', false);
        $('#'+row+'_num3').prop('disabled', true);
        $('#'+row+'_num3').prop('checked', false);
    } else {
        $('#'+row+'_num1').prop('disabled', false);
        $('#'+row+'_num2').prop('disabled', false);
        $('#'+row+'_num3').prop('disabled', false);

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
    window.location.href = "../select_data.php";
});

//redirect to upload page
$('#newUpload').click(function () {
    //redirect to page
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

$('#sameDate').click(function() {
    $('input.date').prop('value', $('#thisDate').val());
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
$('tr td input:checkbox.select_checkbox').click(function() {
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


$( document ).ready(function() {
    //in each row of the table preset the values base on the file name
    for (var i = 0; i < $('.upload tr td.file').length; i++) {
        //get file name
        var fileName = $('.upload .file:eq('+ i +')').text();
        //get the name of the measure
        var measureID = fileName.split('.')[0];
        //unset the default option
        $('.upload .datalist:eq('+ i +') option:selected').attr('selected', false);
        var netColor = measureID.split('_')[0];
        netColor = netColor.charAt(0).toUpperCase() + netColor.slice(1).toLowerCase();
        if (!isNaN(netColor.slice(-1))) {
            netColor += 'Q';
        }
        //set the net color
        $('.upload .datalist:eq('+ i +') option[value="'+netColor+'"]').attr('selected', true);

        //set the position and number of measure
        var position_number = measureID.split('_').slice(1);
        $('.upload .measurePosition:eq('+ i +') input[name="measurePosition'+ i +'"][value="'+position_number[0]+'"]').prop('checked', true);
        $('.upload .measurePosition:eq('+ i +') input[name="measureNumber'+ i +'"][value="'+position_number[1]+'"]').prop('checked', true);
        if (position_number[2] == 'SCAT') {
            $('.upload .measurePosition:eq('+ i +') input[name="scattered'+ i +'"]').prop('checked', true);
        }

        //get the measure type from the file name
        var measureType = '';
        var fileExt = fileName.split('.')[1].trim();
        switch (fileExt) {
            case 'TRM':
                measureType = 'Transmittance';
                break;
            case 'SSM':
                measureType = 'Reference';
                break;
            case 'IRR':
                measureType = 'Irradiance';
                break;
        }
        //set the measure type
        $('.upload .measureType:eq('+ i +') .'+measureType.toLowerCase()).prop('checked', true);
    }
});