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
        $this->middleware('verifyToken', ['except' => ['uploadImg']]);
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

    public function edit(Request $request, $pid) {
        $data = $request->all();
        $product = Product::edit($pid, $data);
        return response()->json([
            'code' => 0,
            'message' => '编辑成功',
            'product' => $product
        ]);
    }

    public function uploadImg(Request $request, $pid) {
        if ($request->hasFile('upload') && $request->file('upload')->isValid()) {
            $upload = $request->file('upload');
            $img = Functions::uploadFile($upload, 'products/'.$pid);
            $img['pid'] = $pid;
            ProductImg::add($img);
            return response()->json([
                'code' => 0,
                'message' => '上传成功',
                'uploaded' => true,
                'pid' => $pid,
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

    public function find($pid) {
        $product = Product::getProductByPid($pid);
        return response()->json([
            'code' => 0,
            'product' => $product
        ]);
    }

    public function delete($pid) {
        ProductImg::deleteAllByPid($pid);
        $directory = 'products/'.$pid;
        Functions::deleteDirectory($directory);
        Product::deleteByPid($pid);
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
