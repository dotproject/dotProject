#jshint node: true, camelcase: false
"use strict"
module.exports = (grunt) ->
  grunt.initConfig
    pkg:
      grunt.file.readJSON("package.json")

    connect:
      demo:
        options:
          hostname: "*" # make accessible from everywhere
          port: 8080
          base: "./"
          keepalive: true
      dev: # pass on, so subsequent tastks (like watch) can start
          options:
              port: 8080
              base: "./"
              keepalive: false
      sauce:
        options:
          hostname: "localhost"
          port: 9999
          base: ""
          keepalive: false

    exec:
      tabfix:
        # Cleanup whitespace according to http://contribute.jquery.org/style-guide/js/
        # (requires https://github.com/mar10/tabfix)
        cmd: "tabfix -t -r -m *.js,*.css,*.html,*.yaml -i node_modules ."

      upload:
        # FTP upload the demo files (requires https://github.com/mar10/pyftpsync)
        stdin: true  # Allow interactive console
        cmd: "pyftpsync --progress upload . ftp://www.wwwendt.de/tech/demo/jquery-contextmenu --delete-unmatched --exclude dist,node_modules,.*,_*"
        # cmd: "pyftpsync --progress upload . ftp://www.wwwendt.de/tech/demo/jquery-contextmenu --delete-unmatched --omit dist,node_modules,.*,_*"

    jscs:
      src: ["jquery.ui-contextmenu.js", "test/tests.js"]
      options:
        config: ".jscsrc"
        force: true

    jshint:
      files: ["jquery.ui-contextmenu.js", "test/tests.js"]
      options:
        jshintrc: ".jshintrc"

    qunit:
      all: [
        "test/test-jquery-3-ui-1.12.html",
        "test/test-jquery-1.12-ui-1.12.html",
        "test/test-jquery-1.11-ui-1.11.html",
        "test/test-jquery-1.9-ui-1.10.html",
      ]

    "saucelabs-qunit":
      options:
        build: process.env.TRAVIS_JOB_ID
        throttled: 5
        # statusCheckAttempts: 180
        recordVideo: false
        videoUploadOnPass: false

      ui_12:
        options:
          testname: "jquery.ui-contextmenu qunit tests (jQuery UI 12)"
          urls: [
            "http://localhost:9999/test/test-jquery-3-ui-1.12.html"
          ]
          browsers: [
            { browserName: "chrome", platform: "Windows 8.1" }
            { browserName: "firefox", platform: "Windows 8.1" }
            { browserName: "firefox", platform: "Linux" }
            # jQuery UI 12+ stopped support for IE <= 10
            { browserName: "internet explorer", version: "11", platform: "Windows 8.1" }
            { browserName: "microsoftedge", platform: "Windows 10" }
            # { browserName: "safari", version: "6", platform: "OS X 10.8" }
            # { browserName: "safari", version: "7", platform: "OS X 10.9" }
            # { browserName: "safari", version: "8", platform: "OS X 10.10" }
            { browserName: "safari", version: "9", platform: "OS X 10.11" }
            { browserName: "safari", version: "10", platform: "OS X 10.12" }
            { browserName: "safari", version: "11", platform: "OS X 10.12" }
          ]

      ui_11:  # UI Menu 11+ dropped support for IE7
        options:
          testname: "jquery.ui-contextmenu qunit tests (jQuery UI 11+)"
          urls: [
            "http://localhost:9999/test/test-jquery-1.11-ui-1.11.html"
          ]
          browsers: [
            { browserName: "chrome", platform: "Windows 8.1" }
            { browserName: "firefox", platform: "Linux" }
            # jQuery UI 11+ stopped support for IE <= 7
            { browserName: "internet explorer", version: "8", platform: "Windows 7" }
            { browserName: "internet explorer", version: "9", platform: "Windows 7" }
            { browserName: "internet explorer", version: "10", platform: "Windows 8" }
            { browserName: "internet explorer", version: "11", platform: "Windows 8.1" }
            { browserName: "microsoftedge", platform: "Windows 10" }
            { browserName: "safari", version: "10", platform: "OS X 10.12" }
          ]

      ui_10:
        options:
          testname: "jquery.ui-contextmenu qunit tests (jQuery UI 10)"
          urls: [
            "http://localhost:9999/test/test-jquery-1.9-ui-1.10.html"
          ]
          browsers: [
            { browserName: "chrome", platform: "Windows 8.1" }
            { browserName: "firefox", platform: "Linux" }
            { browserName: "internet explorer", version: "11", platform: "Windows 8.1" }
            { browserName: "microsoftedge", platform: "Windows 10" }
            { browserName: "safari", version: "10", platform: "OS X 10.12" }
          ]

    uglify:
      options:
        banner: "/*! <%= pkg.title || pkg.name %> - v<%= pkg.version %> - " + "<%= grunt.template.today('yyyy-mm-dd') %> | " + "<%= pkg.homepage ? ' ' + pkg.homepage + ' | ' : '' %>" + " Copyright (c) 2013 -<%= grunt.template.today('yyyy') %> <%= pkg.author.name %>;" + " Licensed <%= _.map(pkg.licenses, 'type').join(', ') %> */\n"
        report: "gzip"

      build:
        options:
          sourceMap: true
        src: "jquery.ui-contextmenu.js"
        dest: "jquery.ui-contextmenu.min.js"

    watch:
      dev:
        options:
          atBegin: true
        files: ["jquery.ui-contextmenu.js", "test/tests.js"]
        tasks: ["jshint", "jscs"]
      # jshint:
      #   options:
      #     atBegin: true
      #   files: ["jquery.ui-contextmenu.js"]
      #   tasks: ["jshint"]

    yabs:
      release:
        common: # defaults for all tools
          manifests: ['package.json', 'bower.json']
        # The following tools are run in order:
        check: { branch: ['master'], canPush: true, clean: true, cmpVersion: 'gte' }
        run_test: { tasks: ['test'] }
        bump: {} # 'bump' uses the increment mode `yabs:release:MODE` by default
        run_build: { tasks: ['build'] }
        replace_build:
          files: ['jquery.ui-contextmenu.min.js']
          patterns: [
            { match: /@VERSION/g, replacement: '{%= version %}'}
          ]
        commit: {}
        check_after_build: { clean: true } # Fails if new files are found
        tag: {}
        push: { tags: true, useFollowTags: true }
        githubRelease:
          repo: 'mar10/jquery-ui-contextmenu'
          draft: false
        npmPublish: {}
        bump_develop: { inc: 'prepatch' }
        commit_develop: { message: 'Bump prerelease ({%= version %}) [ci skip]' }
        push_develop: {}  # another push (append a suffix for a uniqu ename)


  # Load "grunt*" dependencies
  for key of grunt.file.readJSON("package.json").devDependencies
    grunt.loadNpmTasks key  if key isnt "grunt" and key.indexOf("grunt") is 0

  grunt.registerTask "server", ["connect:demo"]
  grunt.registerTask "dev", ["connect:dev", "watch:dev"]
  grunt.registerTask "test", ["jshint", "jscs", "qunit"]
  grunt.registerTask "sauce", ["connect:sauce", "saucelabs-qunit:ui_12", "saucelabs-qunit:ui_11", "saucelabs-qunit:ui_10"]
  if parseInt(process.env.TRAVIS_PULL_REQUEST, 10) > 0
      # saucelab keys do not work on forks
      # http://support.saucelabs.com/entries/25614798
      grunt.registerTask "travis", ["test"]
  else
      grunt.registerTask "travis", ["test", "sauce"]
  grunt.registerTask "default", ["test"]
  grunt.registerTask "ci", ["test"]  # Called by 'npm test'

  # "sauce",
  grunt.registerTask "build", ["exec:tabfix", "test", "uglify"]
  grunt.registerTask "upload", ["build", "exec:upload"]
  grunt.registerTask "server", ["connect:demo"]
