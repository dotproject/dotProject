### Things to try when things are not going right with codecept

- ./vendor/bin/codecept clean
- ./vendor/bin/codecept build


### running tests

reset && php vendor/bin/codecept run acceptance
or
reset && php vendor/bin/codecept run acceptance <testname>

reset && php vendor/bin/codecept run unit

reset && php vendor/bin/codecept run functional