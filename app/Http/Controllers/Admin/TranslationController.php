<?php
/**
 * @file    TranslationController.php
 * @brief   ...
 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController as Controller;

/**
 *
 */
class TranslationController extends Controller
{
    /**
     *
     */
    protected $name = 'translation';

    /**
     *
     */
    protected $queryLimit = 20;

    /**
     *
     */
    protected $supportedOrderColumns = [
        'id',
        'practical',
        'literal',
        'meaning',
    ];

    /**
     *
     */
    protected $defaultOrderColumn = 'practical';

    /**
     *
     */
    protected $defaultOrderDirection = 'asc';
}
