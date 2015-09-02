/**
 * Created by Stefano on 9/1/15.
 */

var addRowButton = document.getElementById("addRow");
addRowButton.onclick = function() {
    // Get a reference to the table
    var tableRef = document.getElementById("table2");
    var index = tableRef.getElementsByTagName('tr').length;
    // Insert a row at the end of the table
    var newRow = tableRef.insertRow(index);

    // Insert a cell in the new row
    var netColorCell = newRow.insertCell(0);
    var positionCell = newRow.insertCell(1);
    var sessionDateCell = newRow.insertCell(2);

    var newInput = function(name, value, type) {
        var input = document.createElement("input");
        input.type = type || "radio";
        input.name = name + (index - 1);
        input.value = value;
        input.required = "required";
        //insert the input into the right cell
        if (name == "netColor") {
            //create new netColor radio buttons (column 1)
            netColorCell.appendChild(input);
            // Append a text node to the cell
            var netColorText = document.createTextNode(value);
            netColorCell.appendChild(netColorText);
        } else if (name == "position") {
            positionCell.appendChild(input);
            var positionDateText = document.createTextNode(value);
            positionCell.appendChild(positionDateText);
        } else if (name == "sessionDate") {
            input.placeholder = "mmddyy";
            input.value = "";
            //tableRef.lastChild.lastChild.lastChild.appendChild(input);
            sessionDateCell.appendChild(input);
            //no text
        }
        //increment the number of measuresToChart
        var inputFiles = document.getElementById("files");
        inputFiles.value = index;

    }

    var newBr = function() {
        var br = document.createElement("br");
        positionCell.appendChild(br);
    }

    newInput('netColor', "Blue");
    newInput('netColor', "Red");
    newInput('netColor', "White");
    newInput('netColor', "Light_Ref");
    newInput('netColor', "Ctrl");

    newInput('position', "1_1");
    newInput('position', "1_2");
    newInput('position', "1_3");
    newBr();
    newInput('position', "2_1");
    newInput('position', "2_2");
    newInput('position', "2_3");
    newBr();
    newInput('position', "1_1_SCAT");
    newInput('position', "1_2_SCAT");
    newInput('position', "1_3_SCAT");
    newBr();
    newInput('position', "2_1_SCAT");
    newInput('position', "2_2_SCAT");
    newInput('position', "2_3_SCAT");

    newInput('sessionDate', "sessionDate", "text");

}


var deleteRowButton = document.getElementById("delRow");
deleteRowButton.onclick = function() {
    var table = document.getElementById("table2");
    var rowCount = table.rows.length;
    //delete last row
    table.deleteRow(rowCount -1);

    //decrement the number of measuresToChart
    var inputFiles = document.getElementById("files");
    inputFiles.value = rowCount-2;
}
