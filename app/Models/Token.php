<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Класс модели push-токена
 */
class Token extends Model
{
    /**
     * Атрибуты, доступные для записи
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'device_id',
        'token',
        'os',
        'version',
    ];

    /**
     * Параметры атрибутов для записи по-умолчанию
     *
     * @var array
     */
    protected $attributes = [
        'user_id' => null,
    ];

    /**
     * Атрибуты, которые будут скрыты из JSON представления токена
     *
     * @var array
     */
    protected $hidden = [
        'id',
    ];
}
