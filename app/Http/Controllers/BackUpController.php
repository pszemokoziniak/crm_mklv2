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
        $files = File::allFiles(storage_path('backup/'));
        foreach ($files as $file) {
            $files = pathinfo($file);
//            $allFiles[] = [$files['basename'],storage_path('backup/').$files['basename']];
            $allFiles[] = $files['basename'];
        }
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
    }

    public function download($file)
    {
        $file = storage_path('backup/'.$file);
        return Response::download($file);
    }
}
