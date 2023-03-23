<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Report
 *
 * @property int $rid
 * @property int $uid
 * @property int $lid
 * @property string $rtype
 * @property string $rstate
 * @property mixed $rinfo
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereLid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereRid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereRinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereRstate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereRtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUid($value)
 * @mixin \Eloquent
 */
class Report extends Model
{
    //
    protected $table="report";
    protected $primaryKey="rid";
    public $timestamps=false;
}
