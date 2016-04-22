<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/11/16
 * Time: 11:18 AM
 */

/* Entity Interface */

interface ICheck
{
    function populate($obj);
    function populateAdditional($obj);
}