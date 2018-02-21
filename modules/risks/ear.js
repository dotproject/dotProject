var nextId = 1000;
function addItem(id, itemName, index, identation) {
    nextId++;
    if (id == '') {
        id = nextId;
    }

    var table = document.getElementById("tb_eap");
    var lastRow = table.rows.length;
    if (index == 0 || index == "") {
        index = lastRow;
    } else {
        index = document.getElementById(index).rowIndex + 1;
    }
    var row = table.insertRow(index);
    row.id = "row_" + id;

    //numbering
    var td = row.insertCell(0);
    td.noWrap = true;
    td.className = "shortTD";
    var div = document.createElement("div");
    div.id = "div_numbering_" + id;
    div.innerHTML = "";
    td.appendChild(div);

    //add field to store numbering
    var field = document.createElement("input");
    field.id = "number_field_" + id;
    field.name = "number_field_" + id;
    field.type = "hidden";
    td.appendChild(field);

    //add field to store leaf info
    var field = document.createElement("input");
    field.id = "leaf_field_" + id;
    field.name = "leaf_field_" + id;
    field.type = "hidden";
    td.appendChild(field);

    //sorter
    var td = row.insertCell(1);
    td.noWrap = true;
    var div = document.createElement("div");
    div.innerHTML = "<input type='button' value='&uarr;' class='button' onclick=moveRow(-1,'" + row.id + "')>";
    div.innerHTML += "<input type='button' class='button' value='&darr;' onclick=moveRow(1,'" + row.id + "')>";
    td.appendChild(div);
    //Identation
    var td = row.insertCell(2);
    td.noWrap = true;
    var div = document.createElement("div");
    div.innerHTML = "<input type=\"button\" value=\"&#8592;\" class=\"button\" onclick=identation(\"" + id + "\",-1)>";
    div.innerHTML = div.innerHTML + "<input type=\"button\" value=\"&#8594;\" class=\"button\" onclick=identation(\"" + id + "\",1)>";
    td.appendChild(div);
    //Identation inputs
    var td = row.insertCell(3);
    td.noWrap = true;
    var span = document.createElement("span");
    span.id = "identation_" + id;
    span.innerHTML = identation;
    td.appendChild(span);
    var identation_field = document.createElement("input");
    identation_field.id = "identation_field_" + id;
    identation_field.name = "identation_field_" + id;
    identation_field.type = "hidden";
    identation_field.value = identation;
    td.appendChild(identation_field);
    //Description
    var field = document.createElement("input");
    field.type = "text";
    field.className = "text";
    field.id = "description_" + id;
    field.name = "description_" + id;
    field.value = itemName;
    field.style.width = "80%";
    td.appendChild(field);

    //add a exclude button
    td = row.insertCell(4);
    td.noWrap = true;
    td.width = "50";
    div = field = document.createElement("div");

    div.innerHTML = "<img src='modules/risks/images/stock_delete-16.png' border='0' style='cursor:pointer' onclick=deleteRole('" + row.id + "') /> <img src='modules/risks/images/stock_new_small.png'  style='cursor:pointer' onclick=addItem('','','" + row.id + "','') />";
    td.appendChild(div);

    updateEAPItemsNumbers();
}

function identation(i, type) {
    var div = document.getElementById("identation_" + i);
    var field = document.getElementById("identation_field_" + i);
    var row = document.getElementById("row_" + i);
    var row_index = row.rowIndex;

    if (type == 1) {
        div.innerHTML += "&nbsp;&nbsp;&nbsp;";
        field.value += "&nbsp;&nbsp;&nbsp;";
    } else {
        div.innerHTML = div.innerHTML.replace("&nbsp;&nbsp;&nbsp;", "");
        field.value = div.innerHTML.replace("&nbsp;&nbsp;&nbsp;", "");
        ;
    }
    updateEAPItemsNumbers();
}

function deleteRole(rowId) {
    var id = rowId.substring(4, rowId.length);
    var field = document.getElementById("items_ids_to_delete");
    field.value += field.value == "" ? id : "," + id;
    var i = document.getElementById(rowId).rowIndex;
    document.getElementById('tb_eap').deleteRow(i);
    updateEAPItemsNumbers();
}

function moveRow(direction, rowId) {
    var oTable = document.getElementById("tb_eap");
    var trs = oTable.tBodies[0].getElementsByTagName("tr");
    var i = document.getElementById(rowId).rowIndex;
    var j = i + direction;
    if (j == 0) {
        return false;
    }
    if (i >= 0 && j >= 0 && i < trs.length && j < trs.length) {
        if (i == j + 1) {
            oTable.tBodies[0].insertBefore(trs[i], trs[j]);
        } else if (j == i + 1) {
            oTable.tBodies[0].insertBefore(trs[j], trs[i]);
        } else {
            var tmpNode = oTable.tBodies[0].replaceChild(trs[i], trs[j]);
            if (typeof(trs[i]) != "undefined") {
                oTable.tBodies[0].insertBefore(tmpNode, trs[i]);
            } else {
                oTable.appendChild(tmpNode);
            }
        }
    } else {
        //alert("Invalid Values!");
        return false;
    }
    updateEAPItemsNumbers();
}

function getCurrentSubLevelIndex(sublevelString) {
    if (sublevelString.indexOf(".") == -1) {
        return 0;
    }
    var sublevels = sublevelString.split(".");
    return sublevels.length - 1;
}

function getFormatedNumberOnLevel(sublevelString, level) {
    var sublevels = sublevelString.split(".");
    var out = "";
    for (k = 0; k <= level; k++) {
        out = out + sublevels[k] + ".";
    }
    return out;
}

function resetSubNumbersAfterLevel(list, level) {
    for (k = level + 1; k < list.length; k++) {
        list[k] = 0;
    }
    return list;
}


function updateEAPItemsNumbers() {
    var table = document.getElementById("tb_eap");
    var lastRow = table.rows.length;
    var currentNumber = 0;
    var currentFormatedNumber = "";//String,used to concat sublevels
    var currentSubNumbers = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    var previousIdentation = "";

    for (i = 1; i < lastRow; i++) { //starts with 1 to let table header safe from transformations.
        var row_id = table.rows[i].id;
        if (row_id.indexOf("row_") != -1) {
            var id = row_id.substring(4, row_id.length);
            var identation = document.getElementById("identation_" + id).innerHTML;
            var number_div = document.getElementById("div_numbering_" + id);
            var number_field = document.getElementById("number_field_" + id);
            var is_leaf_field = document.getElementById("leaf_field_" + id);
            //set is leaf info
            if ((i + 1) < lastRow) {
                var next_row_id = table.rows[i + 1].id;
                var foundNext=true;
                //workaround to work in screen that mix activities and wbs
                if(next_row_id.indexOf("row_")==-1){
                    var j=i+1;
                    foundNext=false;
                    while(j<lastRow && !foundNext){
                        next_row_id = table.rows[j].id;
                        if(next_row_id.indexOf("row_")!=-1){
                            foundNext=true;
                        }
                        j++;
                    }
                }
                if(foundNext){
                    var next_id = next_row_id.substring(4, next_row_id.length);
                    var next_identation = document.getElementById("identation_" + next_id).innerHTML;
                }
                //case the next row has a larger identation, it means the current row is a leaf of its works package 
                if (foundNext && identation.length >= next_identation.length) {
                    is_leaf_field.value = "1";
                } else {
                    is_leaf_field.value = "0";
                }
            } else {
                is_leaf_field.value = "1";
            }
            var is_leaf_icon = is_leaf_field.value == "1" ? "*" : "";
            //case there isn´t any identation, is considered this row is a WBS new level.
            if (identation.length == 0) {
                currentNumber++;
                currentFormatedNumber = currentNumber + "";
                number_div.innerHTML = currentNumber + is_leaf_icon;
                number_field.value = currentNumber;
                for (j = 0; j < currentSubNumbers.length; j++) {
                    currentSubNumbers[j] = 0;
                }
            } else {
                if (identation.length > previousIdentation.length) {
                    var subIndex = getCurrentSubLevelIndex(currentFormatedNumber);
                    currentSubNumbers[subIndex + 1]++;
                    if (currentFormatedNumber.indexOf(".") == -1) {
                        currentFormatedNumber += ".";
                    } else {
                        currentFormatedNumber = getFormatedNumberOnLevel(currentFormatedNumber, subIndex - 1) + currentSubNumbers[subIndex] + ".";
                    }
                    number_div.innerHTML = currentFormatedNumber + currentSubNumbers[subIndex + 1] + is_leaf_icon;
                    number_field.value = currentFormatedNumber + currentSubNumbers[subIndex + 1];
                } else {
                    if (identation.length < previousIdentation.length) {
                        //Is searched for the parent of current row.
                        var parentNumber = getNumberOfParentWBSItem(identation, i);
                        currentFormatedNumber = parentNumber;//return pointer to new/real parent
                        var subIndex = getCurrentSubLevelIndex(parentNumber);
                        currentFormatedNumber = getFormatedNumberOnLevel(currentFormatedNumber, subIndex - 1);//-1  to let the last part of the number out, and save the space for the new number to be concact.
                        currentSubNumbers[subIndex]++;
                        number_div.innerHTML = currentFormatedNumber + currentSubNumbers[subIndex] + is_leaf_icon;
                        number_field.value = currentFormatedNumber + currentSubNumbers[subIndex];
                        currentSubNumbers = resetSubNumbersAfterLevel(currentSubNumbers, subIndex);
                    } else {
                        //case current identation is equals last one, both are leafs.
                        if (identation.length == previousIdentation.length) {
                            var subIndex = getCurrentSubLevelIndex(currentFormatedNumber);
                            currentSubNumbers[subIndex]++;
                            number_div.innerHTML = currentFormatedNumber + currentSubNumbers[subIndex] + is_leaf_icon;
                            number_field.value = currentFormatedNumber + currentSubNumbers[subIndex];
                        }
                    }
                }
            }
            previousIdentation = identation;
        }
    }
}

/**
 * This function will return "" when the wbs item has no item.
 * It will return the formatted number of the wbs item which is
 * the first one with less identation. 
 */
function getNumberOfParentWBSItem(identationChild, rowIndex) {
    var parentFormatedNumber = "";
    var foundParent = false;
    var table = document.getElementById("tb_eap");
    rowIndex--;//move index to row above.
    while (rowIndex >= 1 && !foundParent) {
        var row_id = table.rows[rowIndex].id;
        //workaround to work with screen thar has wbs itens and activities
        var foundNext = true;
        if (row_id.indexOf("row_") == -1) {
            var j = rowIndex-1;
            foundNext = false;
            while (j >=0 && !foundNext) {
                row_id = table.rows[j].id;
                if (row_id.indexOf("row_") != -1) {
                    foundNext = true;
                }
                j--;
            }
        }
        if (foundNext) {
            var id = row_id.substring(4, row_id.length);
            var identation = document.getElementById("identation_" + id).innerHTML;
            if (identation.length <= identationChild.length) {
                foundParent = true;
                parentFormatedNumber = document.getElementById("number_field_" + id).value;
            }
        }
        rowIndex--;
    }
    return parentFormatedNumber;
}

function saveEAP() {
    var idsField = document.getElementById("eap_items_ids");
    idsField.value = "";
    var table = document.getElementById("tb_eap");
    var lastRow = table.rows.length;
    for (i = 1; i < lastRow; i++) {
        var row_id = table.rows[i].id;
        var id = row_id.substring(4, row_id.length);
        if (idsField.value == "") {
            idsField.value = id;
        } else {
            idsField.value += "," + id;
        }
    }
    if (document.getElementById("form_eap") != null) {
        document.getElementById("form_eap").submit();
    }
}