<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMembleyCoreTables extends Migration
{
    public function up()
    {
        $id = [
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
        ];

        $timestamps = [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $tables = [

            'course_categories' => [
                'fields' => [
                    'name'        => ['type' => 'VARCHAR', 'constraint' => 150],
                    'slug'        => ['type' => 'VARCHAR', 'constraint' => 180],
                    'description' => ['type' => 'TEXT', 'null' => true],
                    'status'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
                ],
                'unique' => ['slug'],
            ],

            'courses' => [
                'fields' => [
                    'category_id'   => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                    'title'         => ['type' => 'VARCHAR', 'constraint' => 200],
                    'slug'          => ['type' => 'VARCHAR', 'constraint' => 220],
                    'short_description' => ['type' => 'TEXT', 'null' => true],
                    'description'   => ['type' => 'LONGTEXT', 'null' => true],
                    'duration'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                    'fee'           => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
                    'requirements'  => ['type' => 'TEXT', 'null' => true],
                    'outcomes'      => ['type' => 'TEXT', 'null' => true],
                    'featured_image'=> ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                    'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'development'],
                    'is_featured'   => ['type' => 'BOOLEAN', 'default' => false],
                    'published_at'  => ['type' => 'DATETIME', 'null' => true],
                ],
                'unique' => ['slug'],
                'indexes' => ['category_id', 'status'],
            ],

            'posts' => [
                'fields' => [
                    'title'        => ['type' => 'VARCHAR', 'constraint' => 220],
                    'slug'         => ['type' => 'VARCHAR', 'constraint' => 240],
                    'excerpt'      => ['type' => 'TEXT', 'null' => true],
                    'content'      => ['type' => 'LONGTEXT'],
                    'featured_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                    'author_name'  => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                    'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'],
                    'meta_title'   => ['type' => 'VARCHAR', 'constraint' => 220, 'null' => true],
                    'meta_description' => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
                    'published_at' => ['type' => 'DATETIME', 'null' => true],
                ],
                'unique' => ['slug'],
                'indexes' => ['status', 'published_at'],
            ],

            'announcements' => [
                'fields' => [
                    'title'        => ['type' => 'VARCHAR', 'constraint' => 220],
                    'slug'         => ['type' => 'VARCHAR', 'constraint' => 240],
                    'content'      => ['type' => 'LONGTEXT'],
                    'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'],
                    'starts_at'    => ['type' => 'DATETIME', 'null' => true],
                    'ends_at'      => ['type' => 'DATETIME', 'null' => true],
                    'published_at' => ['type' => 'DATETIME', 'null' => true],
                ],
                'unique' => ['slug'],
                'indexes' => ['status', 'starts_at', 'ends_at'],
            ],

            'pages' => [
                'fields' => [
                    'title'        => ['type' => 'VARCHAR', 'constraint' => 220],
                    'slug'         => ['type' => 'VARCHAR', 'constraint' => 240],
                    'content'      => ['type' => 'LONGTEXT'],
                    'meta_title'   => ['type' => 'VARCHAR', 'constraint' => 220, 'null' => true],
                    'meta_description' => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
                    'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'],
                    'published_at' => ['type' => 'DATETIME', 'null' => true],
                ],
                'unique' => ['slug'],
                'indexes' => ['status'],
            ],

            'media' => [
                'fields' => [
                    'original_name' => ['type' => 'VARCHAR', 'constraint' => 255],
                    'stored_name'   => ['type' => 'VARCHAR', 'constraint' => 255],
                    'file_path'     => ['type' => 'VARCHAR', 'constraint' => 500],
                    'mime_type'     => ['type' => 'VARCHAR', 'constraint' => 120],
                    'file_size'     => ['type' => 'BIGINT', 'unsigned' => true],
                    'alt_text'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                ],
                'indexes' => ['stored_name'],
            ],

            'enquiries' => [
                'fields' => [
                    'enquiry_type' => ['type' => 'VARCHAR', 'constraint' => 40],
                    'name'         => ['type' => 'VARCHAR', 'constraint' => 180],
                    'email'        => ['type' => 'VARCHAR', 'constraint' => 180],
                    'phone'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                    'subject'      => ['type' => 'VARCHAR', 'constraint' => 220, 'null' => true],
                    'message'      => ['type' => 'TEXT'],
                    'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'new'],
                ],
                'indexes' => ['enquiry_type', 'status', 'created_at'],
            ],

            'applications' => [
                'fields' => [
                    'application_number' => ['type' => 'VARCHAR', 'constraint' => 50],
                    'course_id'    => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                    'full_name'    => ['type' => 'VARCHAR', 'constraint' => 180],
                    'email'        => ['type' => 'VARCHAR', 'constraint' => 180],
                    'phone'        => ['type' => 'VARCHAR', 'constraint' => 50],
                    'message'      => ['type' => 'TEXT', 'null' => true],
                    'status'       => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'received'],
                ],
                'unique' => ['application_number'],
                'indexes' => ['course_id', 'status'],
            ],

            'employer_training_requests' => [
                'fields' => [
                    'organisation' => ['type' => 'VARCHAR', 'constraint' => 200],
                    'contact_name' => ['type' => 'VARCHAR', 'constraint' => 180],
                    'email'        => ['type' => 'VARCHAR', 'constraint' => 180],
                    'phone'        => ['type' => 'VARCHAR', 'constraint' => 50],
                    'training_needs' => ['type' => 'TEXT'],
                    'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'new'],
                ],
                'indexes' => ['status'],
            ],

            'site_settings' => [
                'fields' => [
                    'setting_key'   => ['type' => 'VARCHAR', 'constraint' => 150],
                    'setting_value' => ['type' => 'LONGTEXT', 'null' => true],
                ],
                'unique' => ['setting_key'],
            ],

            'audit_logs' => [
                'fields' => [
                    'user_id'      => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                    'action'       => ['type' => 'VARCHAR', 'constraint' => 120],
                    'entity_type'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                    'entity_id'    => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                    'details'      => ['type' => 'TEXT', 'null' => true],
                    'ip_address'  => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
                ],
                'indexes' => ['user_id', 'created_at'],
            ],
        ];

        foreach ($tables as $tableName => $definition) {
            $fields = array_merge($id, $definition['fields'], $timestamps);
            $this->forge->addField($fields);
            $this->forge->addKey('id', true);

            foreach ($definition['unique'] ?? [] as $column) {
                $this->forge->addUniqueKey($column);
            }

            foreach ($definition['indexes'] ?? [] as $column) {
                $this->forge->addKey($column);
            }

            $this->forge->createTable($tableName, true);
        }
    }

    public function down()
    {
        $tables = [
            'audit_logs',
            'site_settings',
            'employer_training_requests',
            'applications',
            'enquiries',
            'media',
            'pages',
            'announcements',
            'posts',
            'courses',
            'course_categories',
        ];

        foreach ($tables as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
