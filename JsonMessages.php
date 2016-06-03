<?php
/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-05-01
 * Time: 11:11 PM
 */

define('OPERATION_SUCCESS','{
                    error_code : "1",
                    message : "operation success"
                }');

define('OPERATION_UNSUCCESSFUL','{
                    error_code : "2",
                    message : "operation unsuccessful"
                }');

define('CONNECTION_ERROR','{
                "error_code" : "4",
                "message" : "database connection unsuccessful"
            }');

define('INVALID_USER','{
                "error_code" : "5",
                "message" : "Invalid user name or password."
            }');

define('RESULT_NOT_FOUND','{
                "error_code" : "6",
                "message" : "Result not found."
            }');

define('NULL_QUERY','{
                "error_code" : "7",
                "message" : "query is null"
            }');

define('MYSQL_QUERY_ERROR','{
                "error_code" : "8",
                "message" : "MySQL query executing error"
            }');