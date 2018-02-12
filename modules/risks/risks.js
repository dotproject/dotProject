var riskModule = riskModule || {};

riskModule.start = function() {
    riskModule.addListener(window, 'keyup', riskModule.keyUp);
}

riskModule.keyUp = function(e) {
    e = e || window.event;
    var form = riskModule.getParent(e.target, "FORM");
    if (form == null) {
        return;
    }
    if (form.riskModuleDirty) {
        return;
    }
    window.onbeforeunload = function() {
        return true;
    };
    riskModule.addListener(form, 'submit', function(){
        form.riskModuleDirty = false;
        window.onbeforeunload = null;
    });
    form.riskModuleDirty = true;
}

riskModule.getParent = function(element, parentNodeName) {
    while (element && element.parentElement !== null) {
        element = element.parentElement;
        if (element.nodeName == parentNodeName) {
            return element;
        }
    }
    return null;
}

riskModule.addListener = function(obj, action, callback) {
    if (obj.addEventListener) {                // For all major browsers, except IE 8 and earlier
        return obj.addEventListener(action, callback);
    } else if (obj.attachEvent) {              // For IE 8 and earlier versions
        return obj.attachEvent("on"+action, callback);
    }
}

riskModule.backToProject = function(id) {
    window.location.href = "?m=projects&a=view&project_id="+id+"&tab=7";
}

riskModule.start();