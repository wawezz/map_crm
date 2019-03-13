<?php

namespace backend\db\models;

class Country
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;


    /**
     * @var string
     */
    public $currencies;

    /**
     * Country constructor.
     */
    public function __construct()
    {

    }
}
