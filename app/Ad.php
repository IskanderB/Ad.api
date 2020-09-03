<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Link;

/**
 * Controller for getting and inserting ad data
 * 
 * This model transform enter data, 
 * get one row
 * get everywhere rows
 * insert row
 * 
 * @author Alexandr Khurtin <axurtin.rep@gmail.com>
 * @version 1.0
 */

class Ad extends Model
{
    /**
     * Content array names columns for getting from DB normaly
     * 
     * @var type array
     */    
    private $baseColumns = [
        'ads.name',
        'ads.cost',
        'links.link',
    ];
    
    /**
     * Content array names columns for getting from DB additionaly for normal
     * 
     * @var type array
     */   
    private $additionalColumns = [
        'ads.description',
    ];
    
    /**
     * Default sorting params
     * 
     * @var type array
     */
    private $defaultValues = [
        'sort' => 'created_at',
        'way' => 'asc',
        'number' => 0
    ];
    
    /**
     * Default values for links general column
     * 
     * @var type array
     */
    private $availableLinks = [
        'general_link' => 1,
        'other_link_1' => 0,
        'other_link_2' => 0
    ];
    
    /**
     * 
     * @param array|boolean $data
     * @return boolean
     */
    
    public function insertOne($data) : bool {
 
        $id = $this->insertGetId([
            'name' => $data['name'],
            'description' => $data['description'],
            'cost' => $data['cost']
        ]);
        
        $result = $this->insertLinks($data, $id);

        return $id and $result;
    }
    
    /**
     * 
     * @param array|boolean $data
     * @param integer $id
     * @return boolean
     */
    function insertLinks($data, $id) : bool {
        //Build links array for sending to DB
        $links = [];
        foreach ($this->availableLinks as $key => $value) {
            if (isset($data[$key])) {
                $links[] = ['link' => $data[$key], 'ad_id' => $id];
            }
        }
        
        if (!empty($links)) {
            $link = new Link();
            return $link->insert($links);
        }
        return true;
    }
    
    /**
     * 
     * @param string $sort
     * @param string $way
     * @param integer $number
     * @param string $uri
     * @return array
     */
    
    public function selectAll($sort, $way, $number, $uri) : array {
        // Set default values in sorting params
        if (!$sort) $sort = $this->defaultValues['sort'];
        if (!$way) $way = $this->defaultValues['way'];
        if (!$number) $number = $this->defaultValues['number'];
        

        $ads = \DB::table('ads')
                ->leftJoin('links', 'ads.id', '=', 'links.ad_id')
                ->select('ads.name', 'ads.cost', 'links.link')
                ->where('links.general', '=', 1)
                ->orWhere('links.link', '=', NULL)
                ->orderBy('ads.' . $sort, $way)
                ->skip($number)
                ->take(10)
                ->get()
                ->toArray();
        
        // Build link for next page (pagination)
        $next = $uri . '?sort=' . $sort . '&way=' . $way . '&number=' . (string)($number + 10);

        return [
            'data' => $ads,
            'next' => $next
        ];
    }
    
    /**
     * 
     * @param integer $id
     * @param string $fields
     * @return array|boolean
     */
    
    public function selectOne($id, $fields) {
        return $this->chooseColumns($id, $fields);        
    }
    
    /**
     * 
     * @param integer $id
     * @param array $columns
     * @param integer $limit
     * @param array $where
     * @param array $orWhere
     * @return array|boolean
     */
    
    private function selectOneDB($id, $columns, $limit, $where, $orWhere) {
        return  \DB::table('ads')
                ->leftJoin('links', 'ads.id', '=', 'links.ad_id')
                ->where($where)
                ->orWhere($orWhere)
                ->limit($limit)
                ->get($columns)
                ->toArray();
    }
    
    /**
     * 
     * @param integer $id
     * @param string $fields
     * @return array|boolean
     */
    
    private function chooseColumns($id, $fields) {
        // Check fielsds param to choose 
        // get usial set or full set
        if ($fields) {
            // Full set columns
           $columns = array_merge($this->baseColumns, $this->additionalColumns);
           $limit = 3;
           $where = [
               ['ads.id', '=', $id],
           ];
           $orWhere = $where;
        }
        else {
            $columns = $this->baseColumns;
            $limit = 1;
            $where = [
               ['ads.id', '=', $id],
               ['links.general', '=', 1]
           ];
            $orWhere = [
               ['ads.id', '=', $id],
               ['links.link', '=', NULL]
           ];
        }
        
        return $this->selectOneDB($id, $columns, $limit, $where, $orWhere);
    }
    
    /**
     * 
     * @param array|boolean $data
     * @param string $typeRequest
     * @param string $next
     * @return Illuminate\Http\Response
     */
    
    private function response($data, $typeRequest, $next = '') : Illuminate\Http\Response {
        $resposne = new Answer();

        return $resposne->returnResponse($typeRequest, $data, $next);
    }
    
    
}
