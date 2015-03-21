<?php

use \Symfony\Component\HttpFoundation\Response as SymRes;

/**
 * This controller takes care of all API calls (internal or external)
 */
class ApiController extends BaseController
{
    /**
     * Filter out unauthorized requests. Used by router rules in routes.php.
     */
    public function filter()
    {
        // Internal API calls
        if (Input::has('_token')) {
            if (Input::get('_token') !== Session::token()) {
                return $this->abort();
            }
        }
        
        // External API calls
        elseif (false) {
            return $this->abort();
        }
        
        // Unauthorized requests
        else {
            return $this->abort();
        }
    }
    
    /**
     * Fallback for GET "/resource"
     */
    public function index() {
        return $this->abort(SymRes::HTTP_NOT_IMPLEMENTED, SymRes::$statusTexts[SymRes::HTTP_NOT_IMPLEMENTED]);
    }
    
    /**
     * Fallback for POST "/resource"
     */
    public function store() {
        return $this->abort(SymRes::HTTP_NOT_IMPLEMENTED, SymRes::$statusTexts[SymRes::HTTP_NOT_IMPLEMENTED]);
    }
    
    /**
     * Fallback for GET "/resource/create"
     */
    public function create() {
        return $this->abort(SymRes::HTTP_NOT_IMPLEMENTED, SymRes::$statusTexts[SymRes::HTTP_NOT_IMPLEMENTED]);
    }
    
    /**
     * Fallback for GET "/resource/{resource}"
     */
    public function show() {
        return $this->abort(SymRes::HTTP_NOT_IMPLEMENTED, SymRes::$statusTexts[SymRes::HTTP_NOT_IMPLEMENTED]);
    }
    
    /**
     * Fallback for PUT/PATCH "/resource/{resource}"
     */
    public function update() {
        return $this->abort(SymRes::HTTP_NOT_IMPLEMENTED, SymRes::$statusTexts[SymRes::HTTP_NOT_IMPLEMENTED]);
    }
    
    /**
     * Fallback for DELETE "/resource/{resource}"
     */
    public function destroy() {
        return $this->abort(SymRes::HTTP_NOT_IMPLEMENTED, SymRes::$statusTexts[SymRes::HTTP_NOT_IMPLEMENTED]);
    }
    
    /**
     * Fallback for GET "/resource/{resource}/edit"
     */
    public function edit() {
        return $this->abort(SymRes::HTTP_NOT_IMPLEMENTED, SymRes::$statusTexts[SymRes::HTTP_NOT_IMPLEMENTED]);
    }
    
    /**
     * Shortcut for sending JSON results.
     */
    public function send($data, $headers = array()) {
        return Response::json(array('status' => 200, 'results' => $data), 200, $headers);
    }
    
    /**
     * Shortcut for sending '401 Unauthorized' JSON response.
     */
    public function abort($code = 401, $msg = 'Unauthorized') {
        return Response::json(array('status' => $code, 'message' => $msg), $code);
    }
}
