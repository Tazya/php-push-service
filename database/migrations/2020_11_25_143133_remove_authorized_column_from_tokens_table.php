<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Класс для удаления колонки authorized из таблицы tokens
 */
class RemoveAuthorizedColumnFromTokensTable extends Migration
{
    /**
     * Запуск миграций.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            $table->dropColumn('authorized');
        });
    }

    /**
     * Отмена миграций.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            $table->boolean('authorized');
        });
    }
}
