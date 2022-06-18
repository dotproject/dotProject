# TESTING HELP

### creating tests

Acceptance tests:
php vendor/bin/codecept generate:cest acceptance TestName

Unit tests:
php vendor/bin/codecept generate:test unit TestName

### running tests

reset && php vendor/bin/codecept run acceptance
or
reset && php vendor/bin/codecept run acceptance <testname>

reset && php vendor/bin/codecept run unit

reset && php vendor/bin/codecept run functional

### Things to try when things are not going right with codecept

- ./vendor/bin/codecept clean
- ./vendor/bin/codecept build


### Run code coverage

First you will need to have a debugger installed.

then run the following command

vendor/bin/codecept run unit --coverage --coverage-xml --coverage-html

if you just want an html report:

vendor/bin/codecept run unit --coverage --coverage-html



### Notes

- make sure you configure acceptance.suite.yml so that it reflects the domain you are working with
- If you are working on your mac locally, make sure session.save_path is set
- If tests need to login, modify acceptance.suite.yml with username and password
