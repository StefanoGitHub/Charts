/**
 * Created by Stefano on 9/1/15.
 *
 * icanhaz.js
 *
 *
 *
 */
"use strict";

if (document.getElementById("addRow")) {
    document.getElementById("addRow").addEventListener("click", function () {

        // Get a reference to the table
        var tableRef = document.getElementById("table2");
        var index = tableRef.getElementsByTagName('tr').length;
        // Insert a row at the end of the table
        var newRow = tableRef.insertRow(index);

        // Insert a cell in the new row
        var firstColumn = newRow.insertCell(0);
        var secondColumn = newRow.insertCell(1);
        var thirdColumn = newRow.insertCell(2);
        var fourthColumn = newRow.insertCell(3);


        //create new <input> tag
        //example: <input type="radio" name="position1" id="1_pos1" value="1" >
        var newInput = function (type, name, value, id) {
            var input = document.createElement("input");
            input.type = type;
            input.name = name + (index - 1);
            input.value = value;
            if (id) {
                input.id = (index - 1)+"_"+id;
            }
            //checkboxes are not required
            if (type != "checkbox") {
                input.setAttribute("required", "required");
            }

            //insert the input into the right cell
            if (name == "selected[]") {
                input.value = (index -1);
                input.name = name;
                firstColumn.appendChild(input);
            }
            if (name == "netColor") {
                //create new netColor radio buttons (column 1)
                secondColumn.appendChild(input);
                // Append a text node to the cell
                var netColorText = document.createTextNode(value);
                secondColumn.appendChild(netColorText);
            } else if (name == "position" || name == "number" || name == "scattered") {
                thirdColumn.appendChild(input);
                //lable tag will be added for text
            } else if (name == "sessionDate") {
                input.placeholder = "mmddyy";
                input.value = "";
                fourthColumn.appendChild(input);
            }

        };


        //create new <label> tag
        //example: <label for="1_pos1" id="1_Mark1">Mark 1</label>
        var newLabel = function (_for, id, text) {
            var label = document.createElement("label");
            label.setAttribute("for", (index - 1)+"_"+_for);
            label.id = (index - 1)+"_"+id;
            label.innerHTML = text;

            //insert the input into the right cell
            thirdColumn.appendChild(label);
        };


        //create a new <br> tag
        var newBr = function (cell) {
            var br = document.createElement("br");
            cell.appendChild(br);
        };

        newInput("checkbox", 'selected[]', "");

        newInput("radio", 'netColor', "Blue");
        newInput("radio", 'netColor', "Red");
        newInput("radio", 'netColor', "White");
        newBr(secondColumn);
        newInput("radio", 'netColor', "Light_Ref");
        newInput("radio", 'netColor', "Ctrl");

        newInput("radio", 'position', 1, "pos1");
            newLabel("pos1", "Mark1", "Mark 1");
        newInput("radio", 'position', 2, "pos2");
            newLabel("pos2", "Mark2", "Mark 2");
        newBr(thirdColumn);
        newInput("radio", 'number', 1, "num1");
            newLabel("num1", "1st", "1st");
        newInput("radio", 'number', 2, "num2");
            newLabel("num2", "2nd", "2nd");
        newInput("radio", 'number', 3, "num3");
            newLabel("num3", "3rd", "3rd");
        newBr(thirdColumn);

        newInput("checkbox", 'scattered', "scattered", "scat");
            newLabel("scat", "scat", "SCAT");

        newInput('sessionDate', "sessionDate", "text");


        //increment the number of measuresToChart
        var inputFiles = document.getElementById("files");
        inputFiles.value = index;

    });
}



//var deleteRowButton = document.getElementById("delRow");
//deleteRowButton.onclick = function() {
if (document.getElementById("delRow")) {
    document.getElementById("delRow").addEventListener("click", function(){
        var table = document.getElementById("table2");
        var rowCount = table.rows.length;

        //do not delete header and first row
        if (rowCount > 2) {
            //delete last row
            table.deleteRow(rowCount - 1);
            //decrement the number of measuresToChart
            var inputFiles = document.getElementById("files");
            inputFiles.value = rowCount - 2;
        }
    });
}


if (document.getElementById("deleteTable")) {
    document.getElementById("deleteTable").addEventListener("click", function (event) {
        if (!confirm('Are you sure you want to \nDELETE the ENTIRE TABLE??')) {
            event.preventDefault()
        }
    });
}


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

//for select_root.php
//still under development
if (document.getElementById("select")) {
    document.getElementById("select").onchange = function () {
        var select = document.getElementById("select");
        var selected = select.options[select.selectedIndex].value;
        alert(selected);
    }
}

//button to redirect to a new chart page
if (document.getElementById("newChart")) {
    document.getElementById("newChart").onclick = function () {
        //redirect to page
        window.location.href = "view_chart.php";
    }
}
