<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubmissionNotes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'entity_type' => ['type' => 'VARCHAR', 'constraint' => 40],
            'entity_id'   => ['type' => 'BIGINT', 'unsigned' => true],
            'author_id'   => ['type' => 'BIGINT', 'unsigned' => true],
            'note'        => ['type' => 'TEXT'],
            'follow_up_at' => ['type' => 'DATE', 'null' => true],
            'created_at'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['entity_type', 'entity_id']);
        $this->forge->createTable('submission_notes');
    }

    public function down()
    {
        $this->forge->dropTable('submission_notes', true);
    }
}
