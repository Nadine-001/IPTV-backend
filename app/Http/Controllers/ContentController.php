<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    public function add_content(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'greeting' => 'required',
            'hotel_about' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            Content::create([
                // 'path' => $path,
                'greeting' => $request->greeting,
                'hotel_about' => $request->hotel_about,
            ]);

            // if ($request->hasFile('content')) {
            //     $greeting = $request->file('content');

            //     $ext = $greeting->getClientOriginalExtension();
            //     $file_name = "greeting";
            //     // $file_name = str_replace(' ', '', $file_name);
            //     $path = asset("uploads/" . $file_name . "." . $ext);
            //     $greeting->move(public_path('uploads/'), $file_name . "." . $ext);

            //     $greeting = Room::create([
            //         'path' => $path,
            //         'content' => $file_name,
            //     ]);
            // }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save text',
                'errors' => $th->getMessage()
            ]);
        }

        return response()->json('HTML content saved successfully!');
    }

    public function show_content()
    {
        return response()->json(Content::all());
    }
}
