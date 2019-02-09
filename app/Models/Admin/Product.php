<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = ['name', 'information'];

    public static function add($data) {
        $product = self::create($data);
        return $product;
    }

    public static function edit($pid, $data) {
        $product = self::find($pid);
        $product->update($data);
        return $product;
    }

    public static function deleteByPid($pid) {
        self::destroy($pid);
    }

    public static function getAllProducts() {
        $products = self::all();
        return $products;
    }
}
