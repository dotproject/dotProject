<?php
    $random = substr(md5(mt_rand()), 0, 7);
?>
<p class="gantt-controls" id="gantt-controls-<?php echo $random ?>">
    <button class="button" data-view="Quarter Day"><?php echo $AppUI->_("Quarter Day"); ?></button>
    <button class="button" data-view="Half Day"><?php echo $AppUI->_("Half Day"); ?></button>
    <button class="button" data-view="Day"><?php echo $AppUI->_("Day"); ?></button>
    <button class="button" data-view="Week"><?php echo $AppUI->_("Week"); ?></button>
    <button class="button" data-view="Month"><?php echo $AppUI->_("Month"); ?></button>
    <button class="button" data-view="Year"><?php echo $AppUI->_("Year"); ?></button>
</p>
<p id="gantt-message-<?php echo $random ?>"></p>
<svg id="gantt-<?php echo $random ?>"></svg>
<script>
    (function(){
        var tasks = function() {
            return <?php echo $json ?>;
        }
        var resetGantt = function() {
            setTimeout(() => {
                gantt.clear();
                gantt.setup_tasks(tasks());
                gantt.render();
            }, 100);
        }
        var getLastView = function() {
            if (typeof(Storage) !== "undefined") {
                var views = JSON.parse(localStorage.getItem("frappe-views"));
                if (views === null) {
                    return null;
                }
                var viewID = <?php echo $this->viewID === null ? "null" : "\"$this->viewID\"" ?>;
                if (viewID !== null) {
                    if (typeof views[viewID] === 'string') {
                        return views[viewID];
                    };
                } else {
                    if (typeof views['default'] === 'string') {
                        return views['default'];
                    };
                }
            }
            return null;
        }
        var saveCurrentView = function(mode) {
            if (typeof(Storage) !== "undefined") {
                var views = JSON.parse(localStorage.getItem("frappe-views"));
                if (views === null) {
                    views = {};
                }
                var viewID = <?php echo $this->viewID === null ? "null" : "\"$this->viewID\"" ?>;
                if (viewID !== null) {
                    views[viewID] = mode;
                } else {
                    views['default'] = mode;
                }
                localStorage.setItem("frappe-views", JSON.stringify(views));
            }
        }
        var id = "<?php echo $random ?>";
        var controls = document.getElementById('gantt-controls-'+id);
        var buttons = controls.getElementsByTagName('BUTTON');
        var ganttTasks = tasks();
        if (ganttTasks.length > 0) {
            var gantt = new Gantt("#gantt-<?php echo $random ?>", ganttTasks, {
                on_click: function (task) {
                    var href = "<?php echo $this->taskClickURL ?>";
                    href = href.replace('%id%', task.id);
                    window.location.href = href;
                },
                on_date_change: function(task, start, end) {
                    resetGantt();
                },
                on_progress_change: function(task, progress) {
                    resetGantt();
                },
                on_view_change: function(mode) {
                    for (var buttonID = 0; buttonID < buttons.length; buttonID++) {
                        var button = buttons[buttonID];
                        var active = mode == button.getAttribute('data-view');
                        button.disabled = active;
                        button.className = active ? "active" : "button";
                    }
                }
            });
            for (var buttonID = 0; buttonID < buttons.length; buttonID++) {
                buttons[buttonID].onclick = function(e) {
                    var button = e.target;
                    gantt.change_view_mode(button.getAttribute('data-view'));
                    saveCurrentView(button.getAttribute('data-view'));
                }
            }
            var viewMode = getLastView();
            if (viewMode != null) {
                gantt.change_view_mode(viewMode);
            }
        } else {
            var messageBox = document.getElementById("gantt-message-<?php echo $random ?>");
            messageBox.innerHTML = "There are no items to display";
        }
    })();
    	/* Function for labels to follow container */
    var div = document.getElementsByClassName('gantt-container')[0];
    div.onscroll = function() {
        Array.from(document.getElementsByClassName('bar-label')).forEach(function(labelItem) {
            try {
                var barWidth = labelItem.previousSibling.previousSibling.getWidth();
                var barX = labelItem.previousSiblinb.previousSibling.getX()
            }
            catch {
                var barWidth = labelItem.previousSibling.getBBox().width;
                var barX = labelItem.previousSibling.getX();
            }


            if (div.scrollLeft > labelItem.getAttribute('data-initial-pos')) { // If user scrolls LEFT past label position
                labelItem.setAttribute('x', div.scrollLeft+5);
            } else if (div.scrollLeft + div.clientWidth - labelItem.getBBox().width < labelItem.getAttribute('data-initial-pos')) { // If user scrolls RIGHT past label position
                labelItem.setAttribute('x', div.scrollLeft + div.clientWidth - labelItem.getBBox().width -5 );
            } else {
                labelItem.setAttribute('x', labelItem.getAttribute('data-initial-pos'));
            }

            if (barWidth > labelItem.getBBox().width) {
                if (labelItem.getBBox().x < barX || labelItem.getBBox().x + labelItem.getBBox().width > barX + barWidth) { // If label is outside bounds of bar
                    labelItem.classList.add('big');
                } else {
                    labelItem.classList.remove('big');
                }
            }
        });
    }
</script>
<style>
    .gantt-controls .active {
        color: black;
        background: none;
        border: none;
        font-weight: bold;
    }
</style>
