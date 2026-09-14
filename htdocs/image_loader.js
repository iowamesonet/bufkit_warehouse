function disable2(archiveMode) {
  document.sidemenus.year.disabled = !archiveMode;
  document.sidemenus.month.disabled = !archiveMode;
  document.sidemenus.day.disabled = !archiveMode;
  document.sidemenus.hour.disabled = !archiveMode;
}
function writeText(txt) {
  document.getElementById("desc").innerHTML = txt;
}

function setMouse(obj) {
  obj.style.cursor = "crosshair";
}
