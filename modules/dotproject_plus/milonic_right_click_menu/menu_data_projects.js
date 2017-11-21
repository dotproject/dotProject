function setContextDisabled(state)
{
    contextDisabled = state;
    if (!contextDisabled) {
        if (ns4) {
            _d.captureEvents(Event.MOUSEDOWN);
            _d.onmousedown = rclick;
        }
        else {
            _d.onmouseup = rclick
            _d.oncontextmenu = new Function("return false")
        }
    }
}

setContextDisabled(true);

fixMozillaZIndex = true; //Fixes Z-Index problem  with Mozilla browsers but causes odd scrolling problem, toggle to see if it helps
_menuCloseDelay = 500;
_menuOpenDelay = 150;
_subOffsetTop = 2;
_subOffsetLeft = -2;

with (contextStyle = new mm_style()) {
    bordercolor = "#999999";
    borderstyle = "solid";
    borderwidth = 1;
    fontfamily = "arial, verdana, tahoma";
    fontsize = "100%";
    fontstyle = "normal";
    headerbgcolor = "#4F8EB6";
    headerborder = 1;
    headercolor = "#ffffff";
    offbgcolor = "#ffffff";
    offcolor = "#000000";
    onbgcolor = "#ECF4F9";
    onborder = "1px solid #316AC5";
    oncolor = "#000000";
    outfilter = "randomdissolve(duration=0.4)";
    overfilter = "Fade(duration=0.2);Shadow(color=#777777', Direction=135, Strength=3)";
    padding = 8;
    pagebgcolor = "#eeeeee";
    pageborder = "1px solid #ffffff";
    //pageimage = "http://img.milonic.com/db_red.gif";
    separatorcolor = "#999999";
    //subimage = "http://img.milonic.com/black_13x13_greyboxed.gif";
}

/*
with (milonic = new menuname("contextMenu")) {
    margin = 3;
    style = contextStyle;
    top = "offset=5";
    aI("image=./images/icons/stock_ok-16.png;text=Adicionar Projeto;url=javascript:rightClickMenuAddProject();");
    aI("image=./images/icons/stock_edit-16.png;text=Editar Projeto;url=javascript:rightClickMenuEditProject();");
}
drawMenus();
*/

