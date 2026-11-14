<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPublicSubmissionWorkflows extends Migration
{
    public function up()
    {
        $this->forge->addColumn('applications', [
            'country' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'privacy_accepted' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
        ]);

        $this->forge->addColumn('enquiries', [
            'privacy_accepted' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
        ]);

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'organization' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'contact_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 180,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 180,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'service_category' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'privacy_accepted' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'new',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->createTable('security_enquiries', true);
    }

    public function down()
    {
        $this->forge->dropTable('security_enquiries', true);
        $this->forge->dropColumn('enquiries', 'privacy_accepted');
        $this->forge->dropColumn('applications', ['privacy_accepted', 'country']);
    }
}
