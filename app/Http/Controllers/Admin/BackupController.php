<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved.
 */
namespace App\Http\Controllers\Admin;

use Artisan;
use Storage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Factories\BackupFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller as BaseController;

class BackupController extends BaseController
{
    /**
     * @param App\Factories\BackupFactory $factory
     */
    public function __construct(Request $request, Response $response, BackupFactory $factory)
    {
        parent::__construct($request, $response);

        $this->factory = $factory;
    }

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
        $filenames = $disk->files('/');

        foreach ($filenames as $filename) {
            $files[] = [
                'name' => $filename,
                'ext' => substr($filename, strrpos($filename, '.') + 1),
                'size' => number_format($disk->size($filename) / 1000).' kb',
                'date' => date('M j, Y', $disk->lastModified($filename)),
                'timestamp' => $disk->lastModified($filename),
            ];
        }

        // Query parameters for sliced list (paginator).
        $total = count($files);

        $limit = (int) $this->getParam('limit', 15);
        $limit = max($limit, 1);
        $limit = min($limit, $total);
        $this->setParam('limit', $limit);

        $limits = [];
        if ($total > 15) {
            $limits[15] = 15;
        }
        if ($total > 30) {
            $limits[30] = 30;
        }
        if ($total > 50) {
            $limits[50] = 50;
        }
        if ($total > 100) {
            $limits[100] = 100;
        }
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

        // Reset index limit.
        $this->resetParam('limit');

        return redirect(route('admin.backup.index'))
            ->withMessages(['Creating backup file... Please check back in a few moments.']);
    }

    /**
     * Uploads a backup file.
     *
     * @todo Restrict access based on roles
     */
    public function upload()
    {
        // Performance check.
        if (! $this->request->hasFile('file')) {
            return back()->withMessages(['No backup file received :/']);
        }

        // Upload file to backups disk.
        $file = $this->request->file('file');

        try {
            $results = $this->factory->upload($file);
        } catch (Exception $e) {
            return redirect(route('admin.backup.index'))->withMessages([$e->getMessage()]);
        }

        // Reset index limit.
        $this->resetParam('limit');

        return redirect(route('admin.backup.index'))->withMessages($results->getMessages());
    }

    /**
     * Downloads a backup file.
     *
     * @param   int     $timestamp
     * @param   string  $file
     *
     * @todo Restrict access based on roles
     */
    public function download($file)
    {
        // Retrieve local path.
        try {
            $filename = $this->factory->getPath($file);
        } catch (Exception $e) {
            abort(404);
        }

        // Disable compression.
        @\ini_set('zlib.output_compression', 'Off');

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $this->response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', 'application/x-dinkomo-backup')
            ->header('Content-Disposition',
                $this->response->headers->makeDisposition('attachment', $file))
            ->setContent(Storage::disk('backups')->get($filename));
    }

    /**
     * Deletes a backup file.
     *
     * @param string $filename
     *
     * @todo Restrict access based on roles
     */
    public function destroy($filename)
    {
        // Try to delete backup file.
        try {
            $results = $this->factory->delete($filename, $this->request->get('timestamp', 0));
        } catch (Exception $e) {
            return redirect(route('admin.backup.index'))->withMessages([$e->getMessage()]);
        }

        // Reset index limit.
        $this->resetParam('limit');

        return redirect(route('admin.backup.index'))->withMessages($results->getMessages());
    }

    /**
     * Restores a backup file.
     *
     * @param   string  $filename
     */
    public function restore($filename)
    {
        // Try to restore backup file.
        try {
            $results = $this->factory->restore($filename);
        } catch (Exception $e) {
            return redirect(route('admin.backup.index'))->withMessages([$e->getMessage()]);
        }

        return redirect(route('admin.backup.index'))->withMessages($results->getMessages());
    }
}
