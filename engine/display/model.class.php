<?php
namespace Mortar\Engine\Display;

class Model {
    protected $db;

    protected $table;

    public function __construct($database) {
        if(!$this->table) echo 'undefined table @'.get_class($this);
        $this->db = $database;
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
