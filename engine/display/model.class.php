<?php
namespace Mortar\Engine\Display;

use Mortar\Engine\Core;

class Model {
    protected $mortar;
    protected $db;

    protected $table;

    public function __construct($mortar) {
        if(!$this->table) echo 'undefined table @'.get_class($this);
        $this->mortar = $mortar;
        $this->db = $mortar->component('database');
    }

    public function find($id) {
        return $this->db->run(
            "SELECT * FROM {$this->table} WHERE {$this->table}_id = :id",
            [
                'id' => $id
            ])
        ->fetch();
    }
}
