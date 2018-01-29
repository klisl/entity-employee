<?php

use yii\db\Migration;

/**
 * Создание таблиц для сущности "сотрудник"
 */
class m180126_204719_create_employee_tables extends Migration
{

    /**
     * Создание таблиц для сущности Employee (сотрудник):
     * - sql_employees;
     * - sql_employee_phones;
     * - sql_employee_statuses
     * и связей между ними
     * @return void
     */
    public function up()
    {
        $this->createTable('{{%sql_employees}}',[
            'id' => $this->char(36)->notNull(),
            'create_date' => $this->dateTime(),
            'receipt_date' => $this->dateTime(),
            'name_last' => $this->string(),
            'name_first' => $this->string(),
            'name_middle' => $this->string(),
            'code' => $this->string(),
            'DoB' => $this->dateTime(),
            'sex' => $this->string(),
            'dismissal_date' => $this->dateTime(),
            'current_status' => $this->string(16)->notNull(),
        ]);

        $this->addPrimaryKey('pk-sql_employees', '{{%sql_employees}}', 'id');

        $this->createTable('{{%sql_employee_phones}}', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->char(36)->notNull(),
            'number' => $this->string()->notNull(),
        ]);

        $this->createIndex('idx-sql_employee_phones-employee_id', '{{%sql_employee_phones}}', 'employee_id');
        $this->addForeignKey('fk-sql_employee_phones-employee', '{{%sql_employee_phones}}', 'employee_id', '{{%sql_employees}}', 'id', 'CASCADE', 'RESTRICT');

        $this->createTable('{{%sql_employee_statuses}}', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->char(36)->notNull(),
            'value' => $this->string(32)->notNull(),
            'date' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('idx-sql_employee_statuses-employee_id', '{{%sql_employee_statuses}}', 'employee_id');
        $this->addForeignKey('fk-sql_employee_statuses-employee', '{{%sql_employee_statuses}}', 'employee_id', '{{%sql_employees}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        $this->dropTable('{{%sql_employee_statuses}}');
        $this->dropTable('{{%sql_employee_phones}}');
        $this->dropTable('{{%sql_employees}}');
    }

}
