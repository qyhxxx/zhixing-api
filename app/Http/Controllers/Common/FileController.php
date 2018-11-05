<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function upload(Request $request) {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $name = $file->getClientOriginalName();
            $path = $file->store('/', 'admin');
            $url = Storage::disk('admin')->url($path);
            return response()->json([
                'code' => 0,
                'message' => '上传成功',
                'name' => $name,
                'url' => $url
            ]);
        } else {
            return response()->json([
                'code' => 1,
                'message' => '上传失败'
            ], 400);
        }
    }

    public function download(Request $request) {
        $url = $request->input('url');
        $name = $request->input('name');
        if (file_exists($url)) {
            return response()->download($url, $name);
        } else {
            return response()->json([
                'code' => 3,
                'message' => '文件不存在'
            ], 404);
        }
    }
}
