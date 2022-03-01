<?php

use yii\db\Migration;

/**
 * Class m220301_141304_start
 */
class m220301_141304_start extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'fio' => $this->string()->notNull(),
            'email' => $this->string(),
            'phone' => $this->string(),
            'password' => $this->string()->notNull(),
            'date_create' => $this->dateTime()->defaultExpression('current_timestamp()'),
            'email_verified' => $this->binary()->defaultValue(0),
        ]);

        $this->createTable('{{%cities}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'date_create' => $this->dateTime()->defaultExpression('current_timestamp()'),
        ]);

        $this->insert('cities', [
            'name' => 'Все города'
        ]);

        $this->createTable('{{%reviews}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(100),
            'text' => $this->string(255),
            'rating' => $this->tinyInteger(),
            'img' => $this->string(),
            'user_id' => $this->integer(),
            'date_create' => $this->dateTime()->defaultExpression('current_timestamp()'),
        ]);

        //<cities 2 reviews>
        $this->createTable("{{%cities2reviews}}", [
            'city_id' => $this->integer(),
            'review_id' => $this->integer(),
            'PRIMARY KEY(city_id, review_id)'
        ]);

        $this->createIndex(
            'idx-cities2reviews-city_id',
            'cities2reviews',
            'city_id'
        );

        $this->addForeignKey(
            'fk-cities2reviews-city_id',
            'cities2reviews',
            'city_id',
            'cities',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-cities2reviews-review_id',
            'cities2reviews',
            'review_id'
        );

        $this->addForeignKey(
            'fk-cities2reviews-review_id',
            'cities2reviews',
            'review_id',
            'reviews',
            'id',
            'RESTRICT'
        );
        //</cities2reviews>
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //<cities 2 reviews>
        $this->dropForeignKey(
            'fk-cities2reviews-city_id',
            'cities2reviews'
        );

        $this->dropIndex(
            'idx-cities2reviews-city_id',
            'cities2reviews'
        );

        $this->dropForeignKey(
            'fk-cities2reviews-review_id',
            'cities2reviews'
        );

        $this->dropIndex(
            'idx-cities2reviews-review_id',
            'cities2reviews'
        );
        //</cities2reviews>

        $this->dropTable('cities');
        $this->dropTable('reviews');
        $this->dropTable('cities2reviews');
        $this->dropTable('users');
    }
}
