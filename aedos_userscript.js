// ==UserScript==
// @name         Aedos Merlin
// @namespace    https://merlin.cydcor.com/merlin/apps/CampaignInfo
// @version      0.1
// @description  Automate Aedos daily numbers
// @author       MitchZinck <github.com/mitchzinck>
// @match        https://merlin.cydcor.com/merlin/apps/CampaignInfo
// @require      https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js
// @grant        none
// ==/UserScript==

//Cable - 1
//DK - 2
//Net - 4
//Phone - 5
//Pres - 6
//AC - 7
//RGUS - 8
//DM - 9
//Total apps - 10
//Security - 13
//Install - 15

function fillPerson(repid, numbers) {
    var form = $('form[name="form1"]');
    var rows = $(form).next();
    var cols = rows[0].getElementsByTagName("td");
    var i, j, k;

    for (j = 0; j < cols.length; j++) { //loop through columns
        var tile = cols[j].textContent.replace(/\s/g,''); //remove column whitespace
        var tileId = tile.substr(tile.length - 7);
        if(tileId === ("(" + repid + ")")) { //e.x. ZINCK,MITCHELL(82364)
            cols[j + 1].children[0].checked = true; //check worked as true
            for(k = j + 2; k < j + 18; k++) { //loop through workers tiles
                var input = cols[k].children[0].children[0];
                input.value = numbers[k - j - 2];
                input.onchange();
            }
            j = k;
        }
    }

}

function keydownHandler(e) {

    if (e.keyCode == 13) {
        getNumbers();
    }
}

function getNumbers() {
    $.ajax({
        url: "https://mzinck.com/aedos/aedos_today.php",

        // The name of the callback parameter, as specified by the YQL service
        jsonp: "getResponse",

        // Tell jQuery we're expecting JSONP
        dataType: "jsonp",

        // Work with the response
        success: function( response ) {
            var i;
            for(i = 0; i < response.length; i++) {
                fillPerson(response[i]['repid'], response[i]['numbers']);
            }
            window.valueChanged = true;
        }
    });

}

if (document.addEventListener) {
    document.addEventListener('keydown', keydownHandler, false);
}
else if (document.attachEvent) {
    document.attachEvent('onkeydown', keydownHandler);
}