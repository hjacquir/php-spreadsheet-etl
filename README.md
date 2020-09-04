# php-spreadsheet-etl
An Extract Transform and Load datas from spreadsheet files (xls, xlsx, ods and csv) writen in php.

## Description

Spreadsheet-etl is a command line tool developed in PHP.

Spreadsheet-etl allows you to extract the data from an XLSX, XLS, ODS and CSV file to save it in the Mysql database.

Spreadsheet-etl retrieves files from a remote FTP, then processes them in its working directory.

Spreadsheet-etl contains a local log file: `spreadsheet-etl.log` and it sends email notifications in case of error.

Spreadsheet-etl supports:

    - console logs
    - logs in a file: the log file is stored in the directory: logs and it is daily
    - logs by email notification: two types of notifications are sent:
        - for administrators: connection problem, ftp, server ...
        - for users: correction of files

To get the list of command : 'php console.php'

## Requirements

* php-7.4.1
* php.ini extensions :
  * extension=curl
  * extension=fileinfo
  * extension=gd2
  * extension=mbstring
  * extension=openssl
  * extension=pdo_mysql
  * extension=php_ftp.dll
* Composer

## Install

Install all the dependencies via composer: `composer install`

## Usage

* Copy-paste the file : `config.yaml.example` and rename it `config.yaml` for example. 
Regardless of the name of the configuration file. 
The important thing is that it is in YAML.
* Use your custom value of config in your `config.yaml` file.
* You ca get list of command with description by doing : `php console.php` in a shell

## Tests

Unit and functional tests are launched on the local workstation dedicated 
to development.

The test environment uses:
* A local portable MySQL database
* A portable SMTP server simulating an SMTP server
* A portable FTP server simulating a remote ftp server
* A directory for storing files located on: `C:` (for windows environnment)

Recommendations:
* Java must be installed on the computer.

* The MySQL server, is located in `tests\mysql_mini_server_11`. 
To start the server double click on: `mysql_start.bat` and to stop it:` mysql_stop.bat`. 
Before and after each test, the database is filled and then emptied of this data. 
Therefore, the portable server only contains the structure of the tables. 
The test data is contained in the file: `tests\Functional\fixtures\fixtures.yaml`

* Functional tests which use send mails, use a test SMTP for sending. 
Therefore before launching the tests you must start this server.
  * Go to the server directory: `tests\fake_smtp_server`
  * Open a git bash terminal and type the command from the file: `launch_command.txt` located in this same directory.

* The functional tests which use the upload or the download on an FTP server (MigrateCommandTest for example) use a local FTP server. 
Therefore before launching the tests you must start this server.
  * Go to the server directory : `tests\portable_ftp_server`
  * Open a git bash terminal and type the command from the file: `README.txt` 
  located in this same directory.
  * The directory structure for the tests is created and is located 
  on the local disk at the location: `C:\for-spreadsheet-etl-tests`. 
  Between each test, the structure is deleted and recreated.
  
Once all the servers are up, you can run the tests by typing the command: 
`vendor/bin/phpunit` in git bash.

If everything is OK all tests should pass.

The duration of the tests is variable but it can exceed 5 minutes.


