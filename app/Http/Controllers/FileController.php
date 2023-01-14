<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = File::all();

        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'files' => $files,
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
        Storage::disk('local')->put('/files/example.txt', 'Contents');

        $user = Auth::user();

        $file_name = $request->file('file')->getClientOriginalName();
        $ext = $request->file('file')->getClientOriginalExtension();
        $size = $request->file('file')->getSize();
        $max_size = 20000; // 20000 kb = 20 mb

        if (round($size / 8000, 2) > $max_size) {
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

        $file = File::create([
            'title' => $file_name,
            'ext' => $ext,
            'size' => $size . ' B',
        ]);

        $request->file('file')->storeAs("files/{$user['id']}/", $file_name);

        return response()->json([
            'status' => 200,
            'message' => 'file been save',
            'file' => $file,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}