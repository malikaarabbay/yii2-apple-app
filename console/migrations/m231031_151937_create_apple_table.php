<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apple}}`.
 */
class m231031_151937_create_apple_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apple}}', [
            'id' => $this->primaryKey(),
            'color' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'fell_at' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'eaten' => $this->integer()->defaultValue(0),
            'size' => $this->decimal(65,2)->defaultValue(1)
        ]);

        $this->batchInsert('{{%apple}}',
        ['id', 'color', 'eaten', 'status', 'created_at', 'fell_at', 'size'], 
        [
            [1, 'red', 0, 1, time(), null, 1],
            [2, 'green', 0, 1, time(), null, 1],
            [3, 'yellow', 0, 1, time(), null, 1],
        ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apple}}');
    }
}
