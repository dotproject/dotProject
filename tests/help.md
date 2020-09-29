
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