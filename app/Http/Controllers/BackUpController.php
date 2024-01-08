<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class BackUpController extends Controller
{
    public function index()
    {
        $files = File::allFiles(storage_path('backup'));
        foreach ($files as $file) {
            $files = pathinfo($file);
            $allFiles[] = $files['basename'];
        }
        krsort($allFiles);

        return Inertia::render('BackUp/Index', [
            'files' => $allFiles,
        ]);
    }
    public function store()
    {
        $filename=date('Y-m-d_H-i-s').'.sql';

        $DATABASE=env('DB_DATABASE');
        $DBUSER=env('DB_USERNAME');
        $DBPASSWD=env('DB_PASSWORD');
        $PATH=storage_path('backup/');

        exec('mysqldump -u '.$DBUSER.' -p'.$DBPASSWD.' '.$DATABASE.' > '.$PATH.$filename);

        $this->index();
//        return Inertia::render('BackUp/Index');
    }

    public function downloadBackUp($file)
    {
        $file_path = storage_path('backup/'.$file);
//        dd($file_path);
        $headers = array(
            'Content-Type:' . mime_content_type($file_path),
        );
        $file_name = $file;

        return response()->download($file_path, $file_name, $headers);
    }
}
