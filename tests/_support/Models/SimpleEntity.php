<?php namespace Tests\Support\Models;

use CodeIgniter\Entities\Entity;

/**
 * Class SimpleEntity
 *
 * Simple Entity-type class for testing creating and saving entities
 * in the model so we can support Entity/Repository type patterns.
 *
 * @package Tests\Support\Models
 */
class SimpleEntity extends Entity
{
    protected $id;
    protected $name;
    protected $description;
    protected $created_at;

}
