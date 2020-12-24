<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// миграции не поддерживают неймспейс

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Класс для создания таблицы с push-токенами
 */
class CreateTokensTable extends Migration
{
    /**
     * Запуск миграций.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->boolean('authorized');
            $table->bigInteger('user_id')->nullable();
            $table->string('device_id');
            $table->string('token');
            $table->string('os')->nullable();
            $table->string('version')->nullable();
        });
    }

    /**
     * Отмена миграций.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
}
