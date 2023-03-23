<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Area
 *
 * @property int $id
 * @property int $country_id
 * @property int|null $code
 * @property string|null $name
 * @property string|null $cname
 * @property string|null $lower_name
 * @method static \Illuminate\Database\Eloquent\Builder|Area newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area query()
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereLowerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereName($value)
 * @mixin \Eloquent
 */
class Area extends Model
{
    //
    protected $table="area";
    protected $primaryKey="id";
}
