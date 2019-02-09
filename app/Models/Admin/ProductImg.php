<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ProductImg extends Model
{
    protected $table = 'product_imgs';

    protected $fillable = ['pid', 'name', 'path', 'url'];

    public $timestamps = false;

    public static function add($data) {
        $productImg = self::create($data);
        return $productImg;
    }

    public static function deleteAllByPid($pid) {
        self::where('pid', $pid)->delete();
    }
}
