# jquery.ui-contextmenu
[![GitHub version](https://badge.fury.io/gh/mar10%2Fjquery-ui-contextmenu.svg)](https://github.com/mar10/jquery-ui-contextmenu/releases/latest)
[![Build Status](https://travis-ci.org/mar10/jquery-ui-contextmenu.svg?branch=master)](https://travis-ci.org/mar10/jquery-ui-contextmenu)
[![Selenium Test Status](https://saucelabs.com/buildstatus/sauce-contextmenu)](https://saucelabs.com/u/sauce-contextmenu)
[![npm](https://img.shields.io/npm/dm/ui-contextmenu.svg)](https://www.npmjs.com/package/ui-contextmenu)

> A jQuery plugin that provides a context menu (based on the standard [jQueryUI menu] widget).

  * Define menus from `<ul>` element or definition list (i.e.
    `[{title: "Paste", cmd: "paste"}, ...]`).
  * Themable using [jQuery ThemeRoller](http://jqueryui.com/themeroller/).
  * Supports delegation (i.e. can be bound to elements that don't exist at the
    time the context menu is initialized).
  * Optional support for touch devices.


## Status

The latest release is available at [npm Registry](https://www.npmjs.org/package/ui-contextmenu):
```shell
$ npm install ui-contextmenu
```

[![GitHub version](https://badge.fury.io/gh/mar10%2Fjquery-ui-contextmenu.svg)](https://github.com/mar10/jquery-ui-contextmenu/releases/latest)
See also the [Change Log](https://github.com/mar10/jquery-ui-contextmenu/blob/master/CHANGELOG.md).


## Demo

[Live demo page](http://wwwendt.de/tech/demo/jquery-contextmenu/demo/):<br>
[ ![sample](demo/teaser.png?raw=true) ](http://wwwendt.de/tech/demo/jquery-contextmenu/demo/ "Live demo")

See also the unit tests and live examples

**More:**

  * Play with [jsFiddle](http://jsfiddle.net/mar10/6o3u8a88/) or
    [Plunker](http://plnkr.co/edit/Bbcoqy?p=preview)
  * Run the [unit tests](http://rawgit.com/mar10/jquery-ui-contextmenu/master/test/index.html).


## Getting Started

First, include dependencies:

* jQuery 1.7+ (3.x or later recommended)
* jQuery UI 1.9+ (at least core, widget, menu), 1.12+ recommended
* One of the ThemeRoller CSS themes or a custom one
* jquery.ui-contextmenu.js (also available as CDN on
  [![](https://data.jsdelivr.com/v1/package/npm/ui-contextmenu/badge)](https://www.jsdelivr.com/package/npm/ui-contextmenu),
  [cdnjs](https://cdnjs.com/libraries/jquery.ui-contextmenu), or
  [UNPKG](https://unpkg.com/ui-contextmenu@latest/jquery.ui-contextmenu.min.js))

for example
```html
<head>
    <link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />
    <script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="assets/jquery.ui-contextmenu.min.js"></script>
```

Assume we have some HTML elements that we want to attach a popup menu to:

```html
<div id="container">
    <div class="hasmenu">AAA</div>
    <div class="hasmenu">BBB</div>
    <div class="hasmenu">CCC</div>
</div>
```

Now we can enable a context menu like so:

```js
$("#container").contextmenu({
	delegate: ".hasmenu",
	menu: [
		{title: "Copy", cmd: "copy", uiIcon: "ui-icon-copy"},
		{title: "----"},
		{title: "More", children: [
			{title: "Sub 1", cmd: "sub1"},
			{title: "Sub 2", cmd: "sub1"}
			]}
		],
	select: function(event, ui) {
		alert("select " + ui.cmd + " on " + ui.target.text());
	}
});
```

Alternatively we can
<a href="https://github.com/mar10/jquery-ui-contextmenu/wiki#initialize-menu-from-an-existing-ul-element">
initialize the menu from embedded &lt;ul> markup</a>.

For more information:

  * [Read the Tutorial](https://github.com/mar10/jquery-ui-contextmenu/wiki) and
    [API Reference](https://github.com/mar10/jquery-ui-contextmenu/wiki/ApiRef)
  * Have a look at the [Live demo page](http://wwwendt.de/tech/demo/jquery-contextmenu/demo/)
  * Ask questions on [Stackoverflow](http://stackoverflow.com/questions/tagged/jquery-ui-contextmenu)
  * Play with [jsFiddle](http://jsfiddle.net/mar10/6o3u8a88/) or
    [Plunker](http://plnkr.co/edit/Bbcoqy?p=preview)


# Credits

Thanks to all [contributors](https://github.com/mar10/jquery-ui-contextmenu/contributors).


# Browser Status Matrix

[![Selenium Test Status](https://saucelabs.com/browser-matrix/sauce-contextmenu.svg)](https://saucelabs.com/u/sauce-contextmenu)


[jQueryUI menu]: http://jqueryui.com/menu/
