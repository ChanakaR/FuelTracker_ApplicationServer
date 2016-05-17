<?php

/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-05-04
 * Time: 10:59 PM
 */
interface Access
{
    public function selectAll();
    public function insertRow($data_array);
    public function updateRow($data_array);
    public function deleteRow($data_array);
    public function select($data_array);
    public function disconnect();

}