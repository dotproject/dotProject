# README

## Goal
- Put acceptance tests in place
- Refactor code so that it can be unit tested
- Put unit tests in place
- Upgrade to work with later versions of PHP


## System Requirements
- PHP up to version 7.4
- Mysql up to version 8

## Other Notes
My current setup to run Dotproject is on a Mac using [Laravel Valet](https://laravel.com/docs/9.x/valet).

In the terminal and in the current directory of the project:
- type: valet link
- type: valet isolate php@7.4 
- then in the browser: http://dotproject.test

Notes on [running tests](./tests/help.md). Tests are in no way complete or nearing anything useful. They are there for the purpose of learning to test. 
For what it is worth, codeception is setup so that developers can jump in start writing tests.


---
For running it in docker, see the [Original DotProject Readme](./ORIGINAL_README.md)

