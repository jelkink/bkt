function addLine(first = false) {

  var html = [];

  html.push("<tr>\n");

  if (first) {
    html.push("<td><input type=\"text\" name=\"date[]\" style=\"width: 70%;\" value=\"" + moment().format('YYYYMMDD') + "\"></td>\n");
  } else {
    html.push("<td></td>\n");
  }
  html.push("<td><select name=\"account[]\">\n");

  for (var i = 0; i < accountsArray.length; i++) {

    html.push("  <option value=\"" + accountsArray[i].id + "\">" + accountsArray[i].id + " " + accountsArray[i].name + "</option>\n");
  }

  html.push("</select></td>\n");
  html.push("<td><select name=\"currency[]\">\n");

  for (var i = 0; i < currenciesArray.length; i++) {

    html.push("  <option value=\"" + currenciesArray[i] + "\"" + (currenciesArray[i] == "EUR" ? " selected" : "") + ">" + currenciesArray[i] + "</option>\n");
  }

  html.push("</select></td>\n");
  html.push("<td><input type=\"text\" name=\"amount[]\"></td>");
  html.push("<td><input type=\"checkbox\" name=\"credit[]\" value=\"" + ($('#entryTable').length) + "\"> cr</td>");
  html.push("<td><input type=\"text\" name=\"description[]\"></td>");
  html.push("</tr>\n");

  $('#entryTable tr:last').after(
    html.join("")
  );
}

$(document).ready(function() {

  $("textarea").keydown(function(e) {

      if(e.keyCode === 9) { // tab was pressed
          // get caret position/selection
          var start = this.selectionStart;
          var end = this.selectionEnd;

          var $this = $(this);
          var value = $this.val();

          // set textarea value to: text before caret + tab + text after caret
          $this.val(value.substring(0, start)
                      + "\t"
                      + value.substring(end));

          // put caret at right position again (add one for the tab)
          this.selectionStart = this.selectionEnd = start + 1;

          // prevent the focus lose
          e.preventDefault();
      }
  });

  addLine(true);
  addLine();
  addLine();
});
