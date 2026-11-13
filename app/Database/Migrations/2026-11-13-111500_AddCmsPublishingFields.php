<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCmsPublishingFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'publication_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'draft',
            ],
            'fee_verified' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
        ]);
        $this->forge->addColumn('posts', [
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
        ]);
        $this->forge->addColumn('pages', [
            'featured_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
        $this->forge->addColumn('announcements', [
            'featured_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->db->table('courses')
            ->whereIn('status', ['available', 'proposed', 'development'])
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->where('published_at !=', null)
            ->update(['publication_status' => 'published']);
    }

    public function down()
    {
        $this->forge->dropColumn('announcements', 'featured_image');
        $this->forge->dropColumn('pages', 'featured_image');
        $this->forge->dropColumn('posts', 'category');
        $this->forge->dropColumn('courses', ['publication_status', 'fee_verified']);
    }
}
