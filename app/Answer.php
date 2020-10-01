<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for sending responses
 * 
 * This model build json respons and send it
 * 
 * @author Alexandr Khurtin <axurtin.rep@gmail.com>
 * @version 1.0
 */

class Answer extends Model
{
    /**
     * This var content codes HTTP response for
     * different types of request methods
     * 
     * @var array
     */
    private $codes = [
        'get' => [
            true => 200,
            false => 404
        ],
        'post' => [
            true => 201,
            false => 400
        ]
    ];
    /**
     * 
     * @param string $typeRequest
     * @param array|boolean $data
     * @param string $next
     * @param boolean $forse
     * @return \Illuminate\Http\JsonResponse
     */
    public function returnResponse(string $typeRequest,  $data = '', string $next = '', bool $forse = false) : \Illuminate\Http\JsonResponse {
        
        if (empty($data)) {
            $status = false;
        }
        else {
            $status = true;
        }
        
        // Change status relate forse flag
        // It needs if response is positive but data is empty
        
        if ($forse) {
            $status = !$status;
        }
        // Test return
//        return [$typeRequest,  $data, $next, $forse, $status, $this->codes[$typeRequest][$status]];
        
        return response()->json([
                'data' => $data,
                'next' => $next,
            ], $this->codes[$typeRequest][$status]);
    }
}
