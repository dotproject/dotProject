# 1.19.0 / Unreleased
  - [FEATURE] Add `appendTo` option.

# 1.18.1 / 2017-08-28

* [BUGFIX] Fix deprecation warnings when menu with `title` callback is opened

# 1.18.0 / 2017-08-26

* [CHANGE] Refactored entry update methods
  - `setEntry(cmd, data)`:
  	Clarify that this method resets all attributes *not* passed in
  	`data` are reset to defaults.<br>
    Also, passing a plain title string as `data` is deprecated: use `setTitle()` instead.
  - Add method `updateEntry(cmd, data)`.<br>
    Example: `updateEntry("info", {title: "Show info...", enable: false})`<br>
    Unlike `setEntry()`, this method only updates menu attibutes that are
    passed in `data`, leaving other attributes intact.
  - Add methods `getEntry()`, `getEntryWrapper()`, `setIcon`, `setTitle`
    (in addition to the existing `enableEntry` and `showEntry`).

# 1.17.0 / 2017-04-17

* [FEATURE] #59 Allow callbacks for `disabled`, `title`, and `tooltip` options
* [FEATURE] #116 `autoFocus` skips leading disabled entries.
* [FEATURE] #118 New option `closeOnWindowBlur`
* Use `.on()` / `.off()` syntax
* Add check for missing `.delegate` option
* Set @VERSION in minified script

# 1.16.0 / 2017-03-30

* [FEATURE] #114: Pass `extraData` argument to `select` and other events.
  The same ui.extraData instance is passed to all events of a open-select-close
  sequence, so it can also be used to pass data between events.

# 1.15.0 / 2017-03-18

* [FEATURE] #113: Pass `ui` argument to `open` event.

# 1.14.0 / 2017-01-30

* #108: Update AMD dependency to be compatible with the jQuery 1.12 layout:
  "jquery-ui/ui/widgets/menu"
  NOTE: this is not backwards compatible with jQuery 1.11 and before:
  http://jqueryui.com/upgrade-guide/1.12/#official-package-on-npm

# 1.13.1 / 2017-01-27

* [BUGFIX] #109 Fix `setEntry(title)` for titles containing icons
* [BUGFIX] #110 Fix `setEntry({...})` for jQuery UI 1.12

# 1.13.0 / 2016-08-21

* [FEATURE] Test with jQuery 3
* [FEATURE] #104 Allow to pass mouse click events to `open()`
* Update grunt to 1.0
* Upate QUnit to 1.23 and refactor tests to use `assert`

# 1.12.0 / 2016-05-18

* [FEATURE] Support jQuery UI 1.12 (wrap menu items in separate `<div>` tags)
* [FEATURE] #100 new menu option `isHeader` for category headers.
* Add jQuery UI 1.12 sample with new 'Base' theme

# 1.11.0 / 2015-09-10

* [CHANGE] #98 Allow to show/hide separators
* [FEATURE] #94 Add `main` field to package.json

# 1.10.0 / 2015-06-27

* [FEATURE] New option `tooltip`, adds a `title` attribute to the menu markup

# 1.9.0 / 2015-04-20

* [FEATURE] New option `autoFocus`, defaults to *false*
* [BUGFIX] #82 Fixed AMD dependencies

# 1.8.2 / 2015-02-08

* [CHANGE] #82 Add "jquery-ui/menu" as AMD dependency
* [FEATURE] #85 Allow custom classes per entry
* [BUGFIX] #88 Exception when target element is deleted in select event

# 1.8.1 / 2014-12-21

* [FEATURE] Publish on npm Registry
* [FEATURE] Publish on [cdnjs](https://cdnjs.com/libraries/jquery.ui-contextmenu)
* [FEATURE] Include a source map file
* [FEATURE] New option `addClass`, defaults to "ui-contextmenu"

# 1.8.0 / 2014-11-23

* [FEATURE] #80 setEntry() supports creating nested menus
* [FEATURE] #81 beforeOpen event accepts deferred as return value
* [BUGFIX] entry data attached to parentLi instead of `<a>`
* Use jscs

# 1.7.0 / 2014-09-09

* [FEATURE] AMD support (topolm).
* [FEATURE] CDN support on [jsDelivr](http://www.jsdelivr.com/#!jquery.ui-contextmenu).

# 1.6.1 / 2014-08-18

* [FEATURE] #27: New option `uiMenuOptions` allows to pass custom options to UI Menu Widget.

# 1.5.0 / 2014-07-06

* [CHANGE] Use data-command="copy" instead of `<a href='...'>` to store command ids.
* [FEATURE] Support jQuery UI 1.11.

# 1.4.0 / 2014-06-22

* [FEATURE] Support bower.
* [BUGFIX] #58: 'replaceMenu' in beforeOpen causing select event to lose ui.target

# 1.3.0 / 2014-03-09

* [FEATURE] New optional parameter open(..., extraData).
* [FEATURE] New option `autoTrigger: true` can be set to `false` to prevent
	opening menus on browser's `contextmenu` event (if you want to use the `open()`
	method instead).
* [FEATURE] New option `preventContextMenuForPopup`to prevent opening the browser's
	system context menu on menu entries.
* [CHANGE] `setEntry()` and `replaceMenu()` now allow to define titles with HTML
	markup.

# 1.2.4 / 2013-12-25

* [BUGFIX] Fixed #44 'Double click on .hasmenu currentTarget empy'
* [BUGFIX] Fixed #46 'Not working on XHTML page' (poofeg)
* Added test matrix for different platforms.

# 1.2.3 / 2013-10-19

* [CHANGE] Detection of separators compliant with UI Menu 1.10
* [BUGFIX] Fixed setEntry for entries that don't have icons and handle missing
	`cmd` option.

# 1.2.2 / 2013-07-28

* [CHANGE] Added ThemeRoller switcher to demo page.

# 1.2.0 / 2013-07-04

* [FEATURE] Added `data` option for menu entries (Francis Rath).

# 1.1.0 / 2013-06-30

* [FEATURE] Added `action` option for menu entries.

# 1.0.0 / 2013-06-12

* [CHANGE] Removed `init` event, added `createMenu` event.
* [CHANGE] Added unit tests.
* [FEATURE] Added `getMenu()` and `isOpen()` methods.
* [BUGFIX] Fixed custom position sample.

# 0.5.0 / 2013-06-02

* [CHANGE] Changed widget namespace from 'ui' to 'moogle'.
* [CHANGE] Default position is now at click event.pageX/Y.
* [CHANGE] Show-animation now faster by default.
* [FEATURE] `select` event has new parameters `ui.cmd` and `ui.target`.
* [BUGFIX] Fixed replaceMenu() for `<ul>` defined menus.

# 0.4.0 / 2013-05-28

* [FEATURE] `position` option (thanks to Jeffrey Dean Altemus).

# 0.3.0 / 2013-05-20

* [CHANGE] Renamed project from "jquery-contextmenu" to "jquery-ui-contextmenu".
* [FEATURE] Support `taphold` events.
* [FEATURE] Show / hide effects configurable.
* [BUGFIX] Fixed markup for disabled entries.

# 0.2.0 / 2013-05-10

* First release.
