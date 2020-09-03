<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ad;
use App\Answer;

/**
 * Controller for requests to ad data
 * 
 * This controller validate data, call model functions, 
 * pass data to model, transform data from model
 * for response, send response
 * 
 * @author Alexandr Khurtin <axurtin.rep@gmail.com>
 * @version 1.0
 */

class AdController extends Controller
{
    /**
     *
     * @var type Ad
     */
    private $model;
    
    public function __construct() {
        $this->model = new Ad();
    }
    
    /**
     * Get one of table ads with match rows from table links
     * 
     * @param Request $request
     * @return type Illuminate\Http\Response
     */
    
    public function getOne(Request $request) : \Illuminate\Http\JsonResponse {
        // Type request: get, post, put or delete
        $typeRequest = 'get';
        // Rules for validate data
        $rules = [
            'id' => 'required|integer',
        ];
        // Validate data
        $notValid = $this->validator(['id' => $request->id], $rules);
        if ($notValid) {
            return $this->response($notValid, $typeRequest, '', true);
        }
        
        // Get one of ads
        $adDB = $this->model->selectOne($request->id, $request->fields);
        
        if (empty($adDB)) {
            return $this->response($adDB, $typeRequest);
        }
        
        /* 
         Transform data from model for response
         Model normaly send array content 1 - 3 
         same rows from ads table with different links 
         (from links table). It's result leftJoin method
         Next code transform it to one array with array links in last bit
         */
        $ad = [];
        foreach ($adDB[0] as $key => $value) {
            $ad[$key] = $value;
        }
        
        $links = [];
        for ($i=0; $i< count($adDB); $i++) {
            $links[$i] = $adDB[$i]->link;
        }
        
        unset($ad['link']);
        $ad['links'] = $links;

        return $this->response($ad, 'get');
        
    }
    
    /**
     * 
     * @param Request $request
     * @return type Illuminate\Http\Response
     */
    
    public function getAll(Request $request) : \Illuminate\Http\JsonResponse {
        // Type request: get, post, put or delete
        $typeRequest = 'get';
        // Rules for validate data
        $rules = [
            'sort' => 'string|in:created_at,cost',
            'way' => 'string|in:asc,desc',
            'number' => 'integer'
        ];
        // Validate data
        $notValid = $this->validator($request->all(), $rules);
        if ($notValid) {
            return $this->response($notValid, $typeRequest, '', true);
        }
        // Get everything of ads
        $result = $this->model->selectAll($request->sort, $request->way, $request->number, request()->url());
        
        return $this->response($result['data'], 'get', $result['next']);
    }
    
    /**
     * 
     * @param Request $request
     * @return type Illuminate\Http\Response
     */
    
    public function create(Request $request) : \Illuminate\Http\JsonResponse {
        // Type request: get, post, put or delete
        $typeRequest = 'post';
        // Rules for validate data
        $rules = [
            'name' => 'required|max:200',
            'description' => 'max:1000',
            'cost' => 'required|integer',
            'general_link' => 'string|max:255|url',
            'other_link_1' => 'string|max:255|url',
            'other_link_2' => 'string|max:255|url',

        ];
        // Validate data
        $notValid = $this->validator($request->all(), $rules);
        if ($notValid) {
            return $this->response($notValid, $typeRequest, '', true);
        }
        // Insert row to DB
        $result = $this->model->insertOne($request->all());
        
        return $this->response($result, $typeRequest);
    }
    
    /**
     * 
     * @param Request $request
     * @param array $rules
     * @return boolean|\Illuminate\Support\MessageBag
     */
    
    private function validator($request, $rules) {
        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return false;
    }
    
    /**
     * 
     * @param array|boolean $data
     * @param string $typeRequest
     * @param string $next
     * @param boolean $forse
     * @return Illuminate\Http\Response
     */
    
    private function response($data, $typeRequest, $next = '', $forse = false) : \Illuminate\Http\JsonResponse {
        $resposne = new Answer();

        return $resposne->returnResponse($typeRequest, $data, $next, $forse);
    }
}
