<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user = Auth::user();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $res = [];

        $files = Storage::allFiles("files/{$this->user['id']}/");

        foreach ($files as $file) {
            $tmp = explode("files/{$this->user['id']}/", $file);

            $res[] = $tmp[1];
        }

        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'files' => $res
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file_name = $request->file('file')->getClientOriginalName();
        $directory = isset($request->directory) ? $request->directory : '';
        $ext = $request->file('file')->getClientOriginalExtension();
        $size = $request->file('file')->getSize();
        $max_upload_size = 20000; // 20000 kb = 20 mb
        $max_disk_size = 100000; // 100000 kb = 100 mb
        $user_disk_size = 0;

        $files = Storage::allFiles("files/{$this->user['id']}/");

        foreach ($files as $file) {
            $user_disk_size += Storage::size($file);
        }

        if (round($user_disk_size / 1000, 2) > $max_disk_size) {
            return response()->json([
                'status' => 400,
                'message' => "max disk size 100 mb"
            ]);
        }


        if (round($size / 1000, 2) > $max_upload_size) {
            return response()->json([
                'status' => 400,
                'message' => "max size for upload file 20 mb"
            ]);
        }

        if (strtolower($ext) == 'php') {
            return response()->json([
                'status' => 400,
                'message' => "cannot save 'php' files"
            ]);
        }

        $request->file('file')->storeAs("files/{$this->user['id']}/{$directory}", $file_name);

        return response()->json([
            'user_id' => $this->user['id'],
            'status' => 200,
            'message' => 'file been save',
            'file_name' => $file_name,
            'size' => $size . ' B'
        ]);
    }

    /**
     * Download file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request  $request, $file_name)
    {

        $search = isset($request->directory) ? $request->directory . '/' . $file_name : $file_name;

        if (Storage::disk('local')->exists("files/{$this->user['id']}/{$search}")) {
            return Storage::download("files/{$this->user['id']}/{$search}");
        } else {
            return response()->json([
                'status' => 404,
                'message' => "File not found"
            ]);
        }
    }

    /**
     * Rename the specified file in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $file_name)
    {


        $search = isset($request->directory) ? $request->directory . '/' . $file_name : $file_name;
        $new_name = isset($request->directory) ? $request->directory . '/' . $request->new_name : $request->new_name;

        if (Storage::disk('local')->exists("files/{$this->user['id']}/{$search}")) {

            Storage::move("files/{$this->user['id']}/{$search}", "files/{$this->user['id']}/{$new_name}");

            return response()->json([
                'status' => 200,
                'message' => 'The file has been renamed'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "File not found"
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $file_name)
    {
        $search = isset($request->directory) ? $request->directory . '/' . $file_name : $file_name;


        if (Storage::disk('local')->exists("files/{$this->user['id']}/{$search}")) {

            Storage::delete("files/{$this->user['id']}/{$search}");

            return response()->json([
                'status' => 200,
                'message' => 'The file has been removed'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "File not found"
            ]);
        }
    }


    /**
     * Create directory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createDirectory(Request $request)
    {
        Storage::makeDirectory("files/{$this->user['id']}/{$request->directory_name}");

        return response()->json([
            'status' => 200,
            'message' => "the '{$request->directory_name}' directory has been successfully created"
        ]);
    }


    /**
     * Return user disk size.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDiskSize()
    {
        $user_disk_size = 0;
        $files = Storage::allFiles("files/{$this->user['id']}/");

        foreach ($files as $file) {
            $user_disk_size += Storage::size($file);
        }

        return response()->json([
            'status' => 200,
            'message' => "User '{$this->user['name']}' disk size - {$user_disk_size} B"
        ]);
    }

    /**
     * Return user disk directory size.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDirectorySize(Request $request)
    {
        $user_directory_size = 0;

        if(!isset($request->directory)){
            return response()->json([
                'status' => 400,
                'message' => "Need directory name"
            ]);
        }

        if (Storage::disk('local')->exists("files/{$this->user['id']}/{$request->directory}")) {
            $files = Storage::allFiles("files/{$this->user['id']}/{$request->directory}");

            foreach ($files as $file) {
                $user_directory_size += Storage::size($file);
            }

            return response()->json([
                'status' => 200,
                'message' => "User '{$this->user['name']}' directory '{$request->directory}' size - {$user_directory_size} B"
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "Not found directory '{$request->directory}'"
            ]);
        }
    }
}
