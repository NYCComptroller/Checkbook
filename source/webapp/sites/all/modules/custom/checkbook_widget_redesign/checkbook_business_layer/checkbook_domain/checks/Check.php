<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/11/16
 * Time: 11:06 AM
 */

/**
 * Class DBCheck used to hold the structure of the DB for simple object mapping
 */
class DBCheck {
    public $check_eft_issued_date;
    public $agency_id;
    public $agency_name;
    public $vendor_id;
    public $vendor_name;
    public $expenditure_object_id;
    public $expenditure_object_name;
    public $department_name;
    public $check_amount;
}

class Check implements ICheck
{
    public $id;
    public $agency;
    public $prime_vendor;
    public $expense_category;
    public $check_amount;
    public $issued_date;

    function populateFromObject($obj) {
        $this->id = null;
        $this->agency = $obj->agency_name;
        $this->prime_vendor = $obj->vendor_name;
        $this->expense_category = $obj->expenditure_object_name;
        $this->check_amount = $obj->check_amount;
        $this->issued_date = $obj->check_eft_issued_date;
    }
}

class OgeCheck implements ICheck
{
    function populateFromObject($obj)
    {
        // TODO: Implement populateFromObject() method.
    }
}

class MwbeCheck implements ICheck
{
    function populateFromObject($obj)
    {
        // TODO: Implement populateFromObject() method.
    }
}

class SubVendorCheck implements ICheck
{
    function populateFromObject($obj)
    {
        // TODO: Implement populateFromObject() method.
    }
}
