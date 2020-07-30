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