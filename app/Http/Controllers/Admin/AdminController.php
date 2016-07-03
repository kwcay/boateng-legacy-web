<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use Lang;
use Session;
use Storage;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Factories\DataExportFactory as ExportHelper;
use App\Http\Controllers\Controller as BaseController;

class AdminController extends BaseController
{
    /**
     * Admin landing page.
     */
    public function index() {
        return view('admin.index');
    }

    /**
     * Import landing page.
     */
    public function import() {
        return view('admin.import');
    }

    /**
     * Backups landing page.
     */
    public function backup()
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
                'date' => date('F Y', $disk->lastModified($filename))
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

        $orders = collect(['date' => 'Date', 'name' => 'Name', 'size' => 'Size']);
        $order = $this->getParam('order', 'date');
        $order = $orders->has($order) ? $order : 'date';
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
     * Uploads a backup file.
     */
    public function uploadBackup()
    {
        // Performance check.
        if (!$this->request->hasFile('file')) {
            Session::push('messages', 'No backup file received :/');
            return back();
        }

        // Upload file to backups disk.
        $file = $this->request->file('file');
        $fileName = /*date('Y-m') .'/'.*/ $file->getClientOriginalName();

        if (Storage::disk('backups')->put($fileName, file_get_contents($file->getRealPath()))) {
            Session::push('messages', 'Backup file successfully uploaded.');
        } else {
            Session::push('messages', 'Could not upload backup file.');
        }

        return redirect(route('admin.backup'));
    }

    /**
     * Exports a resource to file.
     *
     * @param string $resourceName
     * @param string $format
     * @return Response
     */
    public function export(ExportHelper $helper, $resourceName, $format)
    {
        // Use the DataExportFactory to export and format data from the database.
        try
        {
            $result = $helper->exportResource($resourceName, $format);
        }
        catch (Exception $e)
        {
            return redirect(route('admin.export'))->withMessages([$e->getMessage()]);
        }

        // Disable compression.
        @\ini_set('zlib.output_compression', 'Off');

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $this->response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', $result['Content-Type'])
            ->header('Content-Disposition',
                $this->response->headers->makeDisposition('attachment', $result['filename']))
            ->setContent($result['content']);
    }
}
