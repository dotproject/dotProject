# Security Policy

## Supported Versions

Use this section to tell people about which versions of your project are
currently being supported with security updates.

| Version | Supported          |
| ------- | ------------------ |
| 5.1.x   | :white_check_mark: |
| 5.0.x   | :x:                |
| 4.0.x   | :white_check_mark: |
| < 4.0   | :x:                |

## Reporting a Vulnerability

Use this section to tell people how to report a vulnerability.

The function projects_list_data inside project.class file, utilizes create and drop SQL statements.
Attempting to apply the minimum privilege principle, it is recommended the database user utilized by the application have only DML statements permission (create, select, update and delete). But in case you apply those police, the code in this function breaks. It is important to limit the impact in case of an incident, such as SQL injections.
It is necessary to adapt this code. You can do that rewriting the select to set the $projects variable, but probably will lose some native functionality, as this function currently does complex calculations. But in case you are already adopting a customized version of the original code, you may be able to refactor that in a way it meet your needs.

Tell them where to go, how often they can expect to get an update on a
reported vulnerability, what to expect if the vulnerability is accepted or
declined, etc.
