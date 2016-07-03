<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use Artisan;
use Storage;
use Exception;
use App\Factories\BackupFactory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Factories\DataExportFactory as ExportHelper;
use App\Http\Controllers\Controller as BaseController;

/**
 *
 */
class BackupController extends BaseController
{
    /**
     * Lists available backups.
     *
     * @todo Restrict access based on roles
     */
    public function index()
    {
        // List all backup files.
        $files = [];
        $disk = Storage::disk('backups');
        $filenames = $disk->allFiles('/');

        foreach ($filenames as $filename)
        {
            $nameIndex = strrpos($filename, '/') + 1;
            $extIndex = strrpos($filename, '.') + 1;

            $files[] = [
                'name' => substr($filename, $nameIndex, $extIndex - $nameIndex - 1),
                'ext' => substr($filename, $extIndex),
                'size' => number_format($disk->size($filename) / 1000) .' kb',
                'date' => date('F Y', $disk->lastModified($filename)),
                'timestamp' => $disk->lastModified($filename)
            ];
        }

        // Query parameters for sliced list (paginator).
        $total = count($files);

        $limit = (int) $this->getParam('limit', 15);
        $limit = max($limit, 1);
        $limit = min($limit, $total);
        $this->setParam('limit', $limit);

        $limits = [];
        if ($total > 15)    $limits[15] = 15;
        if ($total > 30)    $limits[30] = 30;
        if ($total > 50)    $limits[50] = 50;
        if ($total > 100)    $limits[100] = 100;
        $limits[$total] = $total;

        $orders = collect(['timestamp' => 'Date', 'name' => 'Name', 'size' => 'Size']);
        $order = $this->getParam('order', 'timestamp');
        $order = $orders->has($order) ? $order : 'timestamp';
        $this->setParam('order', $order);

        $dirs = collect(['asc' => 'ascending', 'desc' => 'descending']);
        $dir = strtolower($this->getParam('dir', 'desc'));
        $dir = $dirs->has($dir) ? $dir : 'desc';
        $this->setParam('dir', $dir);

        // Re-order file list.
        $files = collect($files);
        $sortedFiles = $dir == 'asc' ? $files->sortBy($order) : $files->sortByDesc($order);

        // Slice file list.
        $page = $this->setParam('page', $this->getParam('page', 1));
        $slicedFiles = $sortedFiles->slice(($page - 1) * $limit, $limit);

        // Create paginator.
        $paginator = new LengthAwarePaginator($slicedFiles, count($files), $limit, $page);

        return view('admin.backup', compact([
            'total',
            'limit',
            'limits',
            'order',
            'orders',
            'dir',
            'dirs',
            'paginator',
        ]));
    }

    /**
     * Launches a new backup task.
     *
     * @todo Restrict access based on roles
     */
    public function create()
    {
        // Launch backup task in the background.
        Artisan::queue('backup');

        return redirect(route('admin.backup.index'))
            ->withMessages(['Creating backup file... Please check back in a few moments.']);
    }

    /**
     * Uploads a backup file.
     *
     * @param App\Factories\BackupFactory $helper
     *
     * @todo Restrict access based on roles
     */
    public function upload(BackupFactory $helper)
    {
        // Performance check.
        if (!$this->request->hasFile('file')) {
            return back()->withMessages(['No backup file received :/']);
        }

        // Upload file to backups disk.
        $file = $this->request->file('file');

        try
        {
            $results = $helper->upload($file);
        }
        catch (Exception $e)
        {
            return redirect(route('admin.backup.index'))->withMessages([$e->getMessage()]);
        }

        return redirect(route('admin.backup.index'))->withMessages($results->getMessages());
    }

    /**
     * Deletes a backup file.
     *
     * @param
     *
     * @todo Restrict access based on roles
     */
    public function destroy($filename)
    {
        abort(501);
    }
}
