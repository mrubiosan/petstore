<?php

use Phinx\Migration\AbstractMigration;

class SchemaCreation extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('category')
            ->addColumn('name', 'string')
            ->create();

        $this->table('tag')
            ->addColumn('name', 'string')
            ->create();

        $this->table('pet')
            ->addColumn('category_id', 'integer', ['null' => true])
            ->addForeignKey(['category_id'], 'category', ['id'], ['delete' => 'set null'])
            ->addColumn('name', 'string')
            ->addColumn('status', 'string')
            ->create();

        $this->table('pet_photo')
            ->addColumn('pet_id', 'integer')
            ->addForeignKey(['pet_id'], 'pet', ['id'], ['delete' => 'cascade'])
            ->addColumn('url', 'text')
            ->create();

        $this->table('pet_tag', ['id' => false, 'primary_key' => ['pet_id', 'tag_id']])
            ->addColumn('pet_id', 'integer')
            ->addForeignKey(['pet_id'], 'pet', ['id'], ['delete' => 'cascade'])
            ->addColumn('tag_id', 'integer')
            ->addForeignKey(['tag_id'], 'tag', ['id'], ['delete' => 'cascade'])
            ->create();
    }
}
