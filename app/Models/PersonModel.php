<?php

namespace App\Models;
use CodeIgniter\Model;

class PersonModel extends Model
{
    protected $table = 'persons';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'bday', 'lastname'];
}