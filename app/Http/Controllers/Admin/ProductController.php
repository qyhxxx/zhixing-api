<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Product;
use App\Models\Admin\ProductImg;
use App\Utils\Functions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('verifyToken');
    }

    public function create(Request $request) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'unique:products',
        ], [
            'name.unique' => '已有同名产品'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 1,
                'message' => $validator->errors()->first()
            ], 400);
        }
        $product = Product::add($data);
        return response()->json([
            'code' => 0,
            'message' => '新建成功',
            'product' => $product
        ]);
    }

    public function edit(Request $request) {
        $data = $request->all();
        $pid = $data['id'];
        $product = Product::edit($pid, $data);
        return response()->json([
            'code' => 0,
            'message' => '编辑成功',
            'product' => $product
        ]);
    }

    public function uploadImg(Request $request) {
        if ($request->hasFile('upload') && $request->file('upload')->isValid()) {
            $upload = $request->file('upload');
            $id = $request->input('id');
            $img = Functions::uploadFile($upload, 'products/'.$id);
            $img['pid'] = $id;
            ProductImg::add($img);
            return response()->json([
                'code' => 0,
                'message' => '上传成功',
                'uploaded' => true,
                'name' => $img['name'],
                'path' => $img['path'],
                'url' => $img['url']
            ]);
        } else {
            return response()->json([
                'code' => 1,
                'message' => '上传失败',
                'uploaded' => false
            ], 400);
        }
    }

    public function delete($id) {
        ProductImg::deleteAllByPid($id);
        $directory = 'products/'.$id;
        Functions::deleteDirectory($directory);
        Product::deleteByPid($id);
        return response()->json([
            'code' => 0,
            'message' => '删除成功'
        ]);
    }

    public function getList() {
        $products = Product::getAllProducts();
        return response()->json([
            'code' => 0,
            'products' => $products
        ]);
    }
}
