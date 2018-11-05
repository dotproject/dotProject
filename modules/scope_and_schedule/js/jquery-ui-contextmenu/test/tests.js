 // jQUnit defines:
 // asyncTest,deepEqual,equal,expect,module,notDeepEqual,notEqual,notStrictEqual,
 // ok,QUnit,raises,start,stop,strictEqual,test

 /*globals QUnit */

/**
 * Tools inspired by https://github.com/jquery/jquery-ui/blob/master/tests/unit/menu/
 */
function TestHelpers() {

	var lastItem = "",
		log = [],
		$ = jQuery,
		match = $.ui.menu.version.match(/^(\d)\.(\d+)/),
		uiVersion = {
			major: parseInt(match[1], 10),
			minor: parseInt(match[2], 10)
		},
		uiVersionBefore11 = ( uiVersion.major < 2 && uiVersion.minor < 11 ),
		uiVersionBefore12 = ( uiVersion.major < 2 && uiVersion.minor < 12 ),
		findEntry = function( menu, indexOrCommand ) {
			if ( typeof indexOrCommand === "number" ) {
				return menu.children( ":eq(" + indexOrCommand + ")" );
			}
			return menu.find("li[data-command=" + indexOrCommand + "]");
		},
		findEntryInner = function( menu, indexOrCommand ) {
			if ( uiVersionBefore11 ) {
				// jQuery UI <= 1.10 used `<a>` tags
				return findEntry(menu, indexOrCommand).find( "a:first" );
			} else if ( uiVersionBefore12 ) {
				// jQuery UI == 1.11 prefered to avoid `<a>` tags
				return findEntry(menu, indexOrCommand);
			} else {
				// jQuery UI 1.12+ introduced `<div>` wrappers
				return findEntry(menu, indexOrCommand).find( ">div:first" );
				// return findEntry(menu, indexOrCommand).children( ".ui-menu-item-wrapper" );
			}
		};

	return {
		log: function( message, clear ) {
			if ( clear ) {
				log.length = 0;
			}
			if ( message === undefined ) {
				message = lastItem;
			}
//	        window.console.log(message);
			log.push( $.trim( message ) );
		},
		logOutput: function() {
			return log.join( "," );
		},
		clearLog: function() {
			log.length = 0;
		},
		entryEvent: function( menu, item, type ) {
			lastItem = item;
			findEntryInner(menu, item).trigger( type );
		},
		click: function( menu, item ) {
			lastItem = item;
			// console.log("click", menu, item, findEntryInner(menu, item));
			findEntryInner(menu, item).trigger( "click" );
		},
		entry: findEntry,
		entryTitle: function( menu, item ) {
			// return the plain text (without sub-elements)
			var ei =  findEntryInner(menu, item);
			if ( !ei || !ei.length ) { return null; }
			return ei.contents().filter(function() {
					return this.nodeType === 3;
				})[0].nodeValue;
		}
	};
}

// ****************************************************************************

jQuery(document).ready(function() {

/*******************************************************************************
 * QUnit setup
 */

QUnit.config.requireExpects = true;

var th = new TestHelpers(),
	$ = jQuery,
	log = th.log,
	logOutput = th.logOutput,
	click = th.click,
	entryEvent = th.entryEvent,
	entryTitle = th.entryTitle,
	entry = th.entry,
	lifecycle = {
		setup: function() {
			th.clearLog();
			// Always create a fresh copy of the menu <UL> definition
			$("#sampleMenuTemplate").clone().attr("id", "sampleMenu").appendTo("body");
		},
		teardown: function() {
			$(":moogle-contextmenu").contextmenu("destroy");
			$("#sampleMenu").remove();
		}
	},
	SAMPLE_MENU = [
		{ title: "Cut", cmd: "cut", uiIcon: "ui-icon-scissors" },
		{ title: "Copy", cmd: "copy", uiIcon: "ui-icon-copy" },
		{ title: "Paste", cmd: "paste", uiIcon: "ui-icon-clipboard", disabled: true },
		{ title: "----" },
		{ title: "More", children: [
			{ title: "Sub Item 1", cmd: "sub1" },
			{ title: "Sub Item 2", cmd: "sub2" }
			] }
		],
	sauceLabsLog = [];

// SauceLabs integration
QUnit.testStart(function(testDetails) {
	QUnit.log(function(details) {
		if (!details.result) {
			details.name = testDetails.name;
			sauceLabsLog.push(details);
		}
	});
});

QUnit.done(function(testResults) {
	var tests = [],
		i, len, details;
	for (i = 0, len = sauceLabsLog.length; i < len; i++) {
		details = sauceLabsLog[i];
		tests.push({
			name: details.name,
			result: details.result,
			expected: details.expected,
			actual: details.actual,
			source: details.source
		});
	}
	testResults.tests = tests;

	/*jshint camelcase:false*/ // jscs: disable
	window.global_test_results = testResults; // used by saucelabs
	/*jshint camelcase:true*/ // jscs: enable
});

//---------------------------------------------------------------------------

QUnit.module("prototype", lifecycle);

QUnit.test("globals", function(assert) {
	assert.expect(2);
	assert.ok( !!$.moogle.contextmenu, "exists in ui namnespace");
	assert.ok( !!$.moogle.contextmenu.version, "has version number");
});

// ---------------------------------------------------------------------------

QUnit.module("create", lifecycle);

function _createTest(menu, assert) {
	var $ctx;

	assert.expect(5);

	log( "constructor");
	$("#container").contextmenu({
		delegate: ".hasmenu",
		menu: menu,
		preventSelect: true,
		create: function() {
			log("create");
		},
		createMenu: function() {
			log("createMenu");
		}
	});
	log( "afterConstructor");
	$ctx = $(":moogle-contextmenu");
	assert.equal( $ctx.length, 1, "widget created");
	// equal( $("#sampleMenu").hasClass( "ui-contextmenu" ), true,
	// 	"Class set to menu definition");
	assert.equal( $("head style.moogle-contextmenu-style").length, 1, "global stylesheet created");

	$ctx.contextmenu("destroy");

	assert.equal( $(":moogle-contextmenu").length, 0, "widget destroyed");
  //   equal( $("#sampleMenu").hasClass("ui-contextmenu"), false,
		// "Class removed from menu definition");
	assert.equal( $("head style.moogle-contextmenu-style").length, 0, "global stylesheet removed");

	assert.equal(logOutput(), "constructor,createMenu,create,afterConstructor",
		  "Event sequence OK." );
}

QUnit.test("create from UL", function(assert) {
	_createTest("ul#sampleMenu", assert);
});

QUnit.test("create from array", function(assert) {
	_createTest(SAMPLE_MENU, assert);
});

//---------------------------------------------------------------------------

QUnit.module("open", lifecycle);

function _openTest(menu, assert) {
	var $ctx, $popup,
		done = assert.async();

	assert.expect(19);

	$("#container").contextmenu({
		delegate: ".hasmenu",
		menu: menu,
		beforeOpen: function(event, ui) {
			log("beforeOpen");

			assert.equal( event.type, "contextmenubeforeopen",
				   "beforeOpen: Got contextmenubeforeopen event" );
			assert.equal( ui.target.text(), "AAA",
				  "beforeOpen: ui.target is set" );
			assert.ok( $popup.is(":hidden"),
				"beforeOpen: Menu is hidden" );
			assert.ok( !entry($popup, 0).hasClass("ui-state-disabled"),
				"beforeOpen: Entry 0 is enabled" );
			assert.ok( entry($popup, 2).hasClass("ui-state-disabled"),
				"beforeOpen: Entry 2 is disabled" );

			assert.ok($ctx.contextmenu("isOpen"), "isOpen() false in beforeOpen event");

			$("#container").contextmenu("enableEntry", "cut", false);
			$("#container").contextmenu("showEntry", "copy", false);
		},
		open: function(event) {
			log("open");

			assert.ok( $popup.is(":visible"),
				"open: Menu is visible" );
			assert.ok( $popup.hasClass("ui-contextmenu"),
				"Class removed from menu definition");
			assert.ok( entry($popup, 2).hasClass("ui-state-disabled"),
				"open: Entry is disabled" );

			assert.ok( $ctx.contextmenu("isOpen"),
				"isOpen() true in open event");

			assert.ok( entry($popup, 0).is(":visible"),
				"beforeOpen: Entry 0 is visible" );
			assert.ok( entry($popup, 0).hasClass("ui-state-disabled"),
				"beforeOpen: Entry 0 is disabled: enableEntry(false) worked" );

			assert.ok( entry($popup, 1).is(":hidden"),
				"beforeOpen: Entry 1 is hidden: showEntry(false) worked" );
			assert.ok( !entry($popup, 1).hasClass("ui-state-disabled"),
				"beforeOpen: Entry 1 is enabled" );

			assert.equal(logOutput(), "open(),beforeOpen,after open(),open",
				  "Event sequence OK.");
			done();
		}
	});

	$ctx = $(":moogle-contextmenu");
	$popup = $ctx.contextmenu("getMenu");

	assert.ok($popup, "getMenu() works");
	assert.ok(!$ctx.contextmenu("isOpen"), "menu initially closed");

	assert.equal( $ctx.length, 1, "widget created");
	assert.ok($popup.is(":hidden"), "Menu is hidden");
	log("open()");
	$ctx.contextmenu("open", $("span.hasmenu:first"));
	log("after open()");
}

QUnit.test("UL menu", function(assert) {
	_openTest("ul#sampleMenu", assert);
});

QUnit.test("Array menu", function(assert) {
	_openTest(SAMPLE_MENU, assert);
});

//---------------------------------------------------------------------------

QUnit.module("click event sequence", lifecycle);

function _clickTest(menu, assert) {
	var $ctx, $popup,
		done = assert.async();

	assert.expect(13);

	$("#container").contextmenu({
		delegate: ".hasmenu",
		menu: menu,
//        show: false,
//        hide: false,
		beforeOpen: function(event, ui) {
			log("beforeOpen(" + ui.target.text() + ")");
			assert.equal( ui.target.text(), "AAA", "beforeOpen: ui.target is set" );
			assert.equal( ui.extraData.foo, "bar", "beforeOpen: ui.extraData is set" );
			ui.extraData.helloFromBO = true;
		},
		create: function(event, ui) {
			log("create");
		},
		createMenu: function(event, ui) {
			log("createMenu");
		},
		/*TODO: Seems that focus gets called twice in Safary, but not PhantomJS */
//        focus: function(event, ui) {
//            var t = ui.item ? $(ui.item).find("a:first").attr("href") : ui.item;
//            log("focus(" + t + ")");
////            equal( ui.cmd, "cut", "focus: ui.cmd is set" );
////            ok( !ui.target || ui.target.text() === "AAA", "focus: ui.target is set" );
//        },
//        /* blur seems always to have ui.item === null. Also called twice in Safari? */
//		blur: function(event, ui) {
//		    var t = ui.item ? $(ui.item).find("a:first").attr("href") : ui.item;
//			log("blur(" + t + ")");
////            equal( ui.cmd, "cut", "blur: ui.cmd is set" );
////            equal( ui.target && ui.target.text(), "AAA", "blur: ui.target is set" );
//		},
		select: function(event, ui) {
//			window.console.log("select");
			var t = ui.item ? $(ui.item).attr("data-command") : ui.item;
			log("select(" + t + ")");
			assert.equal( ui.cmd, "cut", "select: ui.cmd is set" );
			assert.equal( ui.target.text(), "AAA", "select: ui.target is set" );
			assert.equal( ui.extraData.foo, "bar", "select: ui.extraData is set" );
			assert.equal( ui.extraData.helloFromBO, true, "select: ui.extraData is maintained" );
		},
		open: function(event, ui) {
			log("open");
			assert.equal( ui.target.text(), "AAA", "open: ui.target is set" );
			assert.equal( ui.extraData.foo, "bar", "open: ui.extraData is set" );
			assert.equal( ui.extraData.helloFromBO, true, "open: ui.extraData is maintained" );
			setTimeout(function() {
				entryEvent($popup, 0, "mouseenter");
				click($popup, 0);
			}, 10);
		},
		close: function(event, ui) {
			log("close");
			assert.equal( ui.target.text(), "AAA", "close: ui.target is set" );
			assert.equal( ui.extraData.foo, "bar", "close: ui.extraData is set" );
			assert.equal( ui.extraData.helloFromBO, true, "close: ui.extraData is maintained" );
			assert.equal(logOutput(),
				  "createMenu,create,open(),beforeOpen(AAA),after open(),open,select(cut),close",
				  "Event sequence OK.");
			done();
		}
	});

	$ctx = $(":moogle-contextmenu");
	$popup = $ctx.contextmenu("getMenu");

	log("open()");
	$ctx.contextmenu("open", $("span.hasmenu:first"), { foo: "bar" });
	log("after open()");
}

QUnit.test("Array menu", function(assert) {
	_clickTest(SAMPLE_MENU, assert);
});

QUnit.test("UL menu", function(assert) {
	_clickTest("ul#sampleMenu", assert);
});

// ****************************************************************************

QUnit.module("Dynmic options", lifecycle);

QUnit.test("'action' option", function(assert) {
	var $ctx, $popup,
		menu  = [
			{ title: "Cut", cmd: "cut", uiIcon: "ui-icon-scissors",
				data: { foo: "bar" }, addClass: "custom-class-1",
				action: function(event, ui) {
					log("cut action");
					assert.equal( ui.cmd, "cut", "action: ui.cmd is set" );
					assert.equal( ui.target.text(), "AAA", "action: ui.target is set" );
					assert.equal( ui.item.data().foo, "bar", "action: ui.item.data() is set" );
					assert.ok( ui.item.hasClass("custom-class-1"),
						"action: addClass property works" );
				}
			},
			{ title: "Copy", cmd: "copy", uiIcon: "ui-icon-copy" },
			{ title: "Paste", cmd: "paste", uiIcon: "ui-icon-clipboard", disabled: true }
		],
		done = assert.async();

	assert.expect(9);

	$("#container").contextmenu({
		delegate: ".hasmenu",
		menu: menu,
		open: function(event) {
			log("open");
			setTimeout(function() {
				click($popup, 0);
			}, 10);
		},
		select: function(event, ui) {
			var t = ui.item ? $(ui.item).attr("data-command") : ui.item;
			log("select(" + t + ")");
			assert.equal( ui.cmd, "cut", "select: ui.cmd is set" );
			assert.equal( ui.target.text(), "AAA", "select: ui.target is set" );
			assert.equal( ui.item.data().foo, "bar", "ui.item.data() is set" );
			assert.ok( ui.item.hasClass("custom-class-1"), "addClass property works" );
		},
		close: function(event) {
			log("close");
			assert.equal(logOutput(),
				"open(),after open(),open,select(cut),cut action,close",
				"Event sequence OK.");
			done();
		}
	});

   $ctx = $(":moogle-contextmenu");
   $popup = $ctx.contextmenu("getMenu");

   log("open()");
   $ctx.contextmenu("open", $("span.hasmenu:first"));
   log("after open()");
});

QUnit.test("'tooltip' / 'disabled' options", function(assert) {
	var $ctx, $popup,
		menu  = [ {
			title: "Cut", cmd: "cut", tooltip: function(event, ui) {
					log("tooltip(cut)");
					assert.equal( ui.cmd, "cut", "ui.cmd is set" );
					assert.equal( ui.target.text(), "AAA", "ui.target is set" );
					assert.equal( ui.item.text(), "Cut", "ui.item is set" );
					return "dynamic tt";
				}
			},
			{ title: "Copy", cmd: "copy", tooltip: "static tt" },
			{ title: "Paste", cmd: "paste", disabled: true },
			{ title: "Delete", cmd: "delete", disabled: function(event, ui) {
					log("disabled(delete)");
					return false;
				}
			},
			{ title: "Edit", cmd: "edit", disabled: function(event, ui) {
				log("disabled(edit)");
				return true; }
			},
			{ title: "Hidden", cmd: "hidden", disabled: function(event, ui) {
					log("disabled(hidden)");
					return "hide";
				}
			}
		],
		done = assert.async();

	assert.expect(12);

	$("#container").contextmenu({
		delegate: ".hasmenu",
		menu: menu,
		open: function(event) {
			log("open");
			assert.equal(entry($popup, "cut").attr("title"), "dynamic tt",
				"tooltip callback result was used");
			assert.equal(entry($popup, "copy").attr("title"), "static tt",
				"static tooltip value was used");

			assert.equal(entry($popup, "paste").hasClass("ui-state-disabled"), true,
				"static disabled value was used");
			assert.equal(entry($popup, "delete").hasClass("ui-state-disabled"), false,
				"dynamic disabled value 'false' was used");
			assert.ok(entry($popup, "delete").is(":visible"),
				"dynamic disabled value 'false' does not hide");
			assert.equal(entry($popup, "paste").hasClass("ui-state-disabled"), true,
				"dynamic disabled value 'true' was used");
			assert.ok(entry($popup, "delete").is(":visible"),
				"dynamic disabled value 'true' does not hide");
			assert.ok(entry($popup, "hidden").is(":hidden"),
				"dynamic disabled value 'hide' was used");

			// Click s.th. to close the menu
			setTimeout(function() {
				click($popup, 0);
			}, 10);
		},
		close: function(event) {
			log("close");
			assert.equal(logOutput(),
				"open(),tooltip(cut),disabled(delete),disabled(edit),disabled(hidden)," +
					"after open(),open,close",
				"Event sequence OK.");
			done();
		}
	});

   $ctx = $(":moogle-contextmenu");
   $popup = $ctx.contextmenu("getMenu");

   log("open()");
   $ctx.contextmenu("open", $("span.hasmenu:first"));
   log("after open()");
});

// ****************************************************************************

QUnit.module("'beforeOpen' event", lifecycle);

QUnit.test("modify on open", function(assert) {
	var $ctx, $popup,
		menu  = [
		   { title: "Entry 1", cmd: "e1", uiIcon: "ui-icon-copy" },
		   { title: "Entry 2", cmd: "e2", uiIcon: "ui-icon-copy" },
		   { title: "Entry 3", cmd: "e3", uiIcon: "ui-icon-copy" },
		   { title: "Entry 4", cmd: "e4", uiIcon: "ui-icon-copy" },
		   { title: "Entry 5", cmd: "e5", uiIcon: "ui-icon-copy" },
		   { title: "Entry 6", cmd: "e6", uiIcon: "ui-icon-copy" },
		   { title: "Entry 7", cmd: "e7", uiIcon: "ui-icon-copy" },
		   { title: "Entry 8", cmd: "e8", uiIcon: "ui-icon-copy" },
		   { title: "Entry 9", cmd: "e9", uiIcon: "ui-icon-copy" }
		   ],
		done = assert.async();

	assert.expect(29);

	$("#container").contextmenu({
		delegate: ".hasmenu",
		menu: menu,
		beforeOpen: function(event, ui) {
			log("beforeOpen");
			$ctx
				.contextmenu("setTitle", "e1", "Entry 1 - changed")
				.contextmenu("setEntry", "e2", { uiIcon: "ui-icon-changed" })
				.contextmenu("setEntry", "e3", {
					title: "Entry 3 - changed", cmd: "e3",
					children: [
						{ title: "Sub 1", cmd: "e3_1" },
						{ title: "Sub 2", cmd: "e3_2", disabled: true }
						]
					} )
				.contextmenu("setEntry", "e4", { title: "Entry 4 - changed", cmd: "e4_changed" })
				.contextmenu("updateEntry", "e5", { uiIcon: "ui-icon-changed" });
		},
		open: function(event) {
			log("open");
			// setTitle()
			assert.equal(entryTitle($popup, "e1"), "Entry 1 - changed",
				"setTitle(string) changed title");
			assert.equal(entry($popup, "e1").find("span.ui-icon").length, 1,
				"setTitle(string) keeps existing icon");

			// setEntry() with icon
			assert.equal(entry($popup, "e2").find("span.ui-icon-changed").length, 1,
				"setEntry(<uiIcon>) sets icon");
			assert.equal(entryTitle($popup, "e2"), "undefined",
				"setEntry(<uiIcon>) resets title");

			// setEntry() with new sub-elements
			assert.equal(entryTitle($popup, "e3"), "Entry 3 - changed",
				"setEntry(object) set nested title");
			assert.ok( !entry($popup, "e3").hasClass("ui-state-disabled"),
				"setEntry(object) has reset 'disabled' attribute");
			assert.equal(entryTitle($popup, "e3_1"), "Sub 1",
				"setEntry(object) created nested entry");
			assert.ok(entry($popup, "e3_2").hasClass("ui-state-disabled"),
				"setEntry(object) created nested disabled entry");

			// setEntry() with different CMD
			assert.equal(entry($popup, "e4").length, 0,
				"setEntry(object) change command id (old is gone)");
			assert.equal(entry($popup, "e4_changed").length, 1,
				"setEntry(object) change command id (new is set)");
			assert.equal(entryTitle($popup, "e4_changed"), "Entry 4 - changed",
				"setEntry(object) set title");

			// updateEntry() with icon
			assert.equal(entry($popup, "e5").find("span.ui-icon-changed").length, 1,
				"updateEntry(<uiIcon>) sets icon");
			assert.equal(entryTitle($popup, "e5"), "Entry 5",
				"updateEntry(<uiIcon>) keeps  title");

			// Some more on-the-fly modifications

			assert.ok( !entry($popup, "e9").is(":hidden"),
				"Entry 9 is visible" );
			assert.ok( !entry($popup, "e9").hasClass("ui-state-disabled"),
				"Entry 9 is enabled" );

			$ctx.contextmenu("enableEntry", "e9", false);
			assert.ok( entry($popup, "e9").hasClass("ui-state-disabled"),
				"enableEntry(false)" );

			$ctx.contextmenu("setIcon", "e9", "ui-icon-changed");
			assert.equal(entry($popup, "e9").find("span.ui-icon-changed").length, 1,
				"setIcon()");

			$ctx.contextmenu("setTitle", "e9", "Entry 9 - changed");
			assert.equal(entryTitle($popup, "e9"), "Entry 9 - changed",
				"setTitle()");

			$ctx.contextmenu("showEntry", "e9", false);
			assert.ok( entry($popup, "e9").is(":hidden"),
				"showEntry(false)" );

			// Use updateEntry()
			$ctx.contextmenu("updateEntry", "e9", {
				title: "Entry 9 - updated",
				uiIcon: "ui-icon-updated",
				tooltip: "tooltip updated",
				disabled: false,
				hide: false,
				data: { foo: "bar" },
				setClass: "custom-class"
			});
			assert.ok( !entry($popup, "e9").hasClass("ui-state-disabled"),
				"updateEntry(disabled: false)" );
			assert.equal(entry($popup, "e9").find("span.ui-icon-updated").length, 1,
				"updateEntry(icon)");
			assert.ok( !entry($popup, "e9").is(":hidden"),
				"updateEntry(hide: false)" );
			assert.equal(entryTitle($popup, "e9"), "Entry 9 - updated",
				"updateEntry(title)");
			assert.equal(entry($popup, "e9").attr("title"), "tooltip updated",
				"updateEntry(tooltip)");
			assert.equal(entry($popup, "e9").data().foo, "bar",
				"updateEntry(data)");
			assert.ok(entry($popup, "e9").is(".ui-menu-item.custom-class"),
				"updateEntry(setClass)");

			setTimeout(function() {
				click($popup, "e1");
			}, 10);
		},
		select: function(event, ui) {
			var t = ui.item ? $(ui.item).attr("data-command") : ui.item;
			log("select(" + t + ")");
			assert.equal( ui.cmd, "e1", "select: ui.cmd is set" );
			assert.equal( ui.target.text(), "AAA", "select: ui.target is set" );
		},
		close: function(event) {
			log("close");
			assert.equal(logOutput(), "open(),beforeOpen,after open(),open,select(e1),close",
				"Event sequence OK.");
			done();
		}
	});

   $ctx = $(":moogle-contextmenu");
   $popup = $ctx.contextmenu("getMenu");

   log("open()");
   $ctx.contextmenu("open", $("span.hasmenu:first"));
   log("after open()");
});

});
