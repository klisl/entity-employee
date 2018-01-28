<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmployeesTables extends Migration
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
        //Таблица sql_employees
        Schema::create('sql_employees', function (Blueprint $table){
            $table->char('id', 36);
            $table->dateTime('create_date');
            $table->dateTime('receipt_date');
            $table->string('name_last');
            $table->string('name_first');
            $table->string('name_middle')->nullable();
            $table->string('code', 10);
            $table->dateTime('DoB');
            $table->string('sex');
            $table->dateTime('dismissal_date')->nullable();
            $table->string('current_status', 16);

            $table->primary('id'); //добавить первичный ключ
        });

        //Таблица sql_employee_phones
        Schema::create('sql_employee_phones', function (Blueprint $table){
            $table->increments('id');
            $table->char('employee_id', 36);
            $table->string('number');

            $table->foreign('employee_id')->references('id')->on('sql_employees')->onDelete('cascade')->onUpdate('restrict');
        });

        //Таблица sql_employee_statuses
        Schema::create('sql_employee_statuses', function (Blueprint $table){
            $table->increments('id');
            $table->char('employee_id', 36);
            $table->string('value', 32);
            $table->dateTime('date');

            $table->foreign('employee_id')->references('id')->on('sql_employees')->onDelete('cascade')->onUpdate('restrict');
        });

    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sql_employee_statuses');
        Schema::dropIfExists('sql_employee_phones');
        Schema::dropIfExists('sql_employees');
    }
}
