<?php
namespace Mortar\Engine\Display;

use Mortar\Foundation\Tools\DependencyInjector as DI;

class Model {
    protected $db;

    protected $table;

    public function __construct() {
        if(!$this->table) echo 'undefined table @'.get_class($this);
        $this->db = DI::get('database');
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
