<?php

namespace App\Http\Controllers;

use App\helpers\MbCalculate;
use App\Models\Drive;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FunctionStatusController extends Controller
{
    public function fileCopy($id){
        $file = Drive::all()->find($id);
        $drive = new Drive();
        $drive->original_name = $file->original_name;
        $drive->new_name = $file->new_name;
        $drive->extension = $file->extension;
        $drive->user_id = Auth::id();
        $drive->folder_id = $file->forder_id;
        $drive->save();

        return redirect()->back()->with('status', 'file was copy');
    }

    public function folderCopy($id){
        $getFolder = Folder::all()->find($id);
        $folder = new Folder();
        $folder->name = $getFolder->name;
        $folder->user_id = Auth::id();
        $folder->save();
        return redirect()->back()->with('status', 'folder was copy');
    }

    public function uploadFolder(Request $request){

        $folder = new Folder();
        $folder->name = $request->folder_name;
        $folder->user_id = Auth::id();
        $folder->save();

        foreach ($request->folder_child as $child){
            $drive = new Drive();
            $drive->original_name = $child->getClientOriginalName();
            $drive->new_name = $child->store('public/myDrive');
            if($child->getClientOriginalExtension()){
                $drive->extension = $child->getClientOriginalExtension();
            }else{
                $drive->extension = "txt";
            }
            $drive->user_id = Auth::id();
            $drive->folder_id = $folder->id;
            $drive->save();
        }

        return redirect()->route('myDrive.index')->with('status', 'folder was upload');
    }

    public function fileDownload($id){
        $file = Drive::all()->find($id);
        return Storage::download($file->new_name, $file->original_name);
    }


}
