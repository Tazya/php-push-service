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
        'authorized',
        'user_id',
        'device_id',
        'token',
        'os',
        'version',
    ];

    /**
     * Атрибуты, которые будут скрыты из JSON представления токена
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'authorized',
    ];
}
