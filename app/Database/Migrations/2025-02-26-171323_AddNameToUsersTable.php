<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNameToUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'after' => 'id',
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'nome');
    }
}
