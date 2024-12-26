<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
   */
   public function CKEupload(Request $request)
   {

       // FORMAT FILE TO STORE
       $originName = $request->file('upload')->getClientOriginalName();
       $fileName = pathinfo($originName, PATHINFO_FILENAME);
       $extension = $request->file('upload')->getClientOriginalExtension();
       $fileName = $fileName . '_' . time() . '.' . $extension;

       // STORE FILE
       $request->file('upload')->storeAs('media', $fileName, 'public');

       // RETURN URL
       $url = asset('storage/media/' . $fileName);

       // RETURN
       return response()->json([
           'fileName' => $fileName,
           'uploaded' => 1,
           'url' => $url
       ]);

   }
}
