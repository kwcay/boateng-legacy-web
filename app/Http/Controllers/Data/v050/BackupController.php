<?php
/**
 * @file    BackupController.php
 * @brief   Creates and restores backup files.
 */
namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class BackupController extends Controller
{
    /**
     * Backup directory.
     */
    private $path;

    /**
     * Error message.
     */
    private $error = '';


    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        // Define the directory used to store backups.
        $this->backupPath = storage_path().'/app/bak';
    }

    public function make()
    {
        $this->mockBackupTask();

        abort(501);
    }

    public function restore()
    {
        abort(501);
    }

    public function mockBackupTask()
    {
    }
}
