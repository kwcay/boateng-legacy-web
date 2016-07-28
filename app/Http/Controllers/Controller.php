<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved.
 *
 * @brief   This controller serves as an abstract for all the models of the applications.
 */
namespace App\Http\Controllers;

use Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
    use ValidatesRequests;

    /**
     * Internal name used to map controller to its model, views, etc.
     *
     * @var string
     */
    protected $name;


    protected $defaultQueryLimit = 20;


    protected $supportedOrderColumns = [];


    protected $defaultOrderColumn = 'id';


    protected $defaultOrderDirection = 'desc';

    /**
     * @param Illuminate\Http\Request $request
     * @param Illuminate\Http\Response $response
     * @return void
     */
    public function __construct(Request $request, Response $response)
    {
        // Determine internal name from class name.
        if (! $this->name) {
            $namespace = explode('\\', get_class($this));
            $this->name = strtolower(substr(array_pop($namespace), 0, -10));
        }

        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (! is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    /**
     * Displays a listing of the resource.
     *
     * @todo Restrict access based on roles.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        // Query builder.
        $builder = $this->getModel();

        // Add trashed items.
        if (in_array(SoftDeletes::class, class_uses_recursive(get_class($builder)))) {
            $builder = $builder->withTrashed();
        }

        // Query parameters
        $total = $builder->count();

        $limit = (int) $this->getParam('limit', $this->defaultQueryLimit);
        $limit = max($limit, 1);
        $limit = min($limit, $total);
        $this->setParam('limit', $limit);

        $limits = [];
        if ($total > 10) {
            $limits[10] = 10;
        }
        if ($total > 20) {
            $limits[20] = 20;
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

        $orders = collect($this->supportedOrderColumns);
        $order = $this->getParam('order', $this->defaultOrderColumn);
        $order = $orders->has($order) ? $order : $this->defaultOrderColumn;
        // $order = in_array($order, $this->supportedOrderColumns) ? $order : $this->defaultOrderColumn;
        $this->setParam('order', $order);

        $dirs = collect(['asc' => 'ascending', 'desc' => 'descending']);
        $dir = strtolower($this->getParam('dir', $this->defaultOrderDirection));
        $dir = $dirs->has($dir) ? $dir : $this->defaultOrderDirection;
        // $dir = in_array($dir, ['asc', 'desc']) ? $dir : $this->defaultOrderDirection;
        $this->setParam('dir', $dir);

        // Paginator.
        $page = $this->setParam('page', $this->getParam('page', 1));
        $paginator = $builder->orderBy($order, $dir)->paginate($limit, ['*'], 'page', $page);

        // Other data.
        $controllerName = $this->name;

        return view("admin.{$this->name}.index", compact([
            'total',
            'limit',
            'limits',
            'order',
            'orders',
            'dir',
            'dirs',
            'paginator',
            'controllerName',
        ]));
    }

    /**
     * Shows the specified resource.
     *
     * @todo Restrict access based on roles.
     *
     * @param int|string $id
     * @return Illuminate\View\View
     */
    public function show($id)
    {
        if (! $model = $this->getModelInstance($id)) {
            abort(404);
        }

        return view("pages.{$this->name}", [
            $this->name => $model,
        ]);
    }

    /**
     * Displays the form to add a new resource.
     *
     * @return Illuminate\View\View
     */
    public function walkthrough()
    {
        return view("forms.{$this->name}.walkthrough");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @todo Restrict access based on roles.
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        // Retrieve model classname.
        $className = $this->getModelClassName();
        if (! class_exists($className)) {
            abort(500);
        }

        // Validate incoming data.
        $this->validate($this->request, (new $className)->validationRules);

        // Create resource.
        $model = $className::create($this->getAttributesFromRequest());

        // Send success message to client, and a thank you.
        $return = Auth::check() ?
            route("admin.{$this->name}.edit", $model->uniqueId) :
            ($model->uri ?: route("{$this->name}.create"));

        Session::push('messages',
            'The details for <em>'.($model->name ?: $model->title).'</em> were successfully saved, thanks :)');

        return redirect($return);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @todo Restrict access based on roles.
     *
     * @param mixed $id ID or Eloquent model.
     * @return Illuminate\View\View
     */
    public function edit($id)
    {
        // If we already have an instance of the model, great.
        if (is_a($id, 'Illuminate\Database\Eloquent\Model')) {
            $model = $id;
        }

        // If we have an encoded ID, decode it.
        elseif (! is_numeric($id) && is_string($id) && strlen($id) >= 8) {
            $className = $this->getModelClassName();

            if (! $model = $className::find($id)) {
                abort(404);
            }
        }

        // Performance check.
        elseif (! is_numeric($id)) {
            abort(404);
        }

        // Retrieve the model by ID.
        elseif (! $model = $this->getModel()->find($id)) {
            // TODO: should this throw a 400 Bad Request if ID was found?

            abort(404);
        }

        return view("admin.{$this->name}.edit", compact([
            'model',
        ]));
    }

    /**
     * Updates the specified resource in storage.
     *
     * @todo Restrict access based on roles.
     *
     * @param int|string $id
     * @return Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        // Retrieve model.
        $className = $this->getModelClassName();
        if (! $model = $className::find($id)) {
            abort(404);
        }

        // Validate incoming data.
        $this->validate($this->request, (new $className)->validationRules);

        // Update attributes.
        $model->fill($this->getAttributesFromRequest());

        if (! $model->save()) {
            abort(500);
        }

        // Send success message to client, and a thank you.
        Session::push('messages', 'The details for <em>'.($model->name ?: $model->title).
            '</em> were successfully saved, thanks :)');

        // Return URI
        switch ($this->request->get('return')) {
            case 'edit':
                $return = $model->editUri;
                break;

            case 'finish':
            case 'summary':
                $return = $model->uri;
                break;

            case 'admin':
            default:
                $return = route("admin.{$this->name}.index");
        }

        return redirect($return);
    }

    /**
     * Removes or trashes the specified resource from storage.
     *
     * @todo Restrict access based on roles.
     *
     * @param int $id
     * @return Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Retrieve model.
        $className = $this->getModelClassName();
        if (! $model = $className::find($id)) {
            abort(404);
        }

        // Delete record
        $name = $model->name ?: $model->title;
        if ($model->delete()) {
            Session::push('messages', '<em>'.$name.'</em> has been succesfully deleted.');
        } else {
            Session::push('messages', 'Could not delete <em>'.$name.'</em>.');
        }

        // Return URI
        switch ($this->request->get('return')) {
            case 'home':
                $return = route('home');
                break;

            case 'admin':
            default:
                $return = route("admin.{$this->name}.index");
        }

        return redirect($return);
    }

    /**
     * Restores a soft-deleted model.
     *
     * @todo Restrict access based on roles.
     *
     * @param int|string $id
     * @return Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        // Retrieve model.
        $className = $this->getModelClassName();
        if (! $model = $className::findTrashed($id)) {
            abort(404);
        }

        // Make sure the model can soft-delete.
        if (! in_array(SoftDeletes::class, class_uses_recursive(get_class($model)))) {
            abort(400);
        }

        // Restore model.
        if ($model->restore()) {
            Session::push('messages', '<em>'.($model->name ?: $model->title).
                '</em> was successfully restored.');
        } else {
            Session::push('messages', 'Could not restore '.($model->name ?: $model->title).'.');
        }

        // Return URI
        switch ($this->request->get('return')) {
            case 'edit':
                $return = $model->editUri;
                break;

            case 'finish':
            case 'summary':
                $return = $model->uri;
                break;

            case 'admin':
            default:
                $return = route("admin.{$this->name}.index");
        }

        return redirect($return);
    }

    /**
     * Permanently deletes the specified resource from storage.
     *
     * @todo Restrict access based on roles.
     *
     * @param int $id
     * @return Illuminate\Http\RedirectResponse
     */
    public function forceDestroy($id)
    {
        // Retrieve model.
        $className = $this->getModelClassName();
        if (! $model = $className::findTrashed($id)) {
            abort(404);
        }

        // Delete record
        $name = $model->name ?: $model->title;
        $model->forceDelete();
        Session::push('messages', '<em>'.$name.'</em> has been permanently deleted.');

        // Return URI
        switch ($this->request->get('return')) {
            case 'home':
                $return = route('home');
                break;

            case 'admin':
            default:
                $return = route("admin.{$this->name}.index");
        }

        return redirect($return);
    }

    /**
     * Retrieves the relations and attributes that may be appended to a model.
     *
     * @param array|string $embed   The properties to be appended to a model.
     * @param array $appendable     Those properties which aren't database relations.
     * @return array
     */
    protected function getEmbedArray($embed = null, array $appendable = [])
    {
        // Relations and attributes to append.
        $separator = isset($this->embedSeparator) ? $this->embedSeparator : ',';
        $embed = is_string($embed) ? @explode($separator, $embed) : (array) $embed;

        // Extract the attributes from the list of embeds.
        $attributes = array_intersect($appendable, $embed);

        // Separate the database relations from the appendable attributes.
        foreach ($embed as $key => $embedable) {
            // Remove invalid relations.
            $embedable = preg_replace('/[^0-9a-z_]/i', '', $embedable);
            if (empty($embedable)) {
                unset($embed[$key]);
            }

            if (in_array($embedable, $attributes)) {
                unset($embed[$key]);
            }
        }

        return [
            'relations' => collect($embed),
            'attributes' => collect($attributes),
        ];
    }

    /**
     * Applies the appendable attributes to the model.
     *
     * @abstract    Was meant to be exported to frnkly/laravel-embeds.
     *
     * @param mixed $model      Model to append attributes to.
     * @param array $attributes Attributes to append to the model.
     * @return void
     */
    protected function applyEmbedableAttributes($model, array $attributes = null)
    {
        // TODO: support passing a colletion of models.
        if (is_a($model, 'Illuminate\Support\Collection')) {
        }

        // Retrieve list of appendable attributes.
        if (is_null($attributes)) {
            $className = get_class($model);
            if (isset($className::$embedableAttributes) && is_array($className::$embedableAttributes)) {
                $attributes = $className::$embedableAttributes;
            }

            // ...
            elseif (isset($model->embedableAttributes) && is_array($model->embedableAttributes)) {
                $attributes = $model->embedableAttributes;
            }

            // ...
            else {
                $attributes = [];
            }
        }

        // Append extra attributes.
        if (count($attributes)) {
            foreach ($attributes as $accessor) {
                // TODO: support model types other than Illuminate\Database\Eloquent\Model
                $model->setAttribute($accessor, $model->$accessor);
            }
        }
    }

    /**
     * Retrieves the attributes to be updated.
     *
     * @return Illuminate\Support\Collection
     */
    protected function getAttributesFromRequest()
    {
        $className = $this->getModelClassName();

        return $this->request->only(array_flip((new $className)->validationRules));
    }

    /**
     * Retrieves a parameter from the request, or the session.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getParam($key, $default = null)
    {
        return $this->request->get($key, Session::get('res-'.$this->name.'-'.$key, $default));
    }

    /**
     * Saves a parameter value to the session.
     *
     * @param string $key
     * @param mixed $value
     * @return ??
     */
    protected function setParam($key, $value = null)
    {
        Session::put('res-'.$this->name.'-'.$key, $value);

        return $value;
    }

    /**
     * Resets a parameter value.
     *
     * @param string $key
     * @return mixed
     */
    protected function resetParam($key)
    {
        return Session::pull('res-'.$this->name.'-'.$key);
    }

    /**
     * Determines the class name of the model associated with this controller.
     *
     * @return string
     */
    protected function getModelClassName()
    {
        return '\\App\\Models\\'.ucfirst($this->name);
    }

    /**
     * Retrieves an instance of a model by ID.
     *
     * @param mixed $id
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function getModelInstance($id)
    {
        // If we already have an instance of the model, great.
        if (is_a($id, 'Illuminate\Database\Eloquent\Model')) {
            return $id;
        }

        // If we have an encoded ID, decode it.
        elseif (! is_numeric($id) && is_string($id) && strlen($id) >= 8) {
            $className = $this->getModelClassName();

            return $className::find($id);
        }

        // If the ID was already decoded, this defeats the purpose of obfuscation...
        // We won't allow it !!
        elseif (is_numeric($id)) {
            return;
        }
    }

    /**
     * Creates a new instance of the model associated with this controller.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function getModel()
    {
        $className = $this->getModelClassName();

        return new $className;
    }
}
