<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Link;

/**
 * Model for getting and inserting ad data
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
     * @var array
     */    
    private $baseColumns = [
        'ads.name',
        'ads.cost',
        'links.link',
    ];
    
    /**
     * Content array names columns for getting from DB additionaly for normal
     * 
     * @var array
     */   
    private $additionalColumns = [
        'ads.description',
    ];
    
    /**
     * Default sorting params
     * 
     * @var array
     */
    private $defaultValues = [
        'sort' => 'created_at',
        'way' => 'asc',
        'number' => 0
    ];
    
    /**
     * Default values for links general column
     * 
     * @var array
     */
    private $availableLinks = [
        'general_link' => 1,
        'other_link_1' => 0,
        'other_link_2' => 0
    ];
    
    /**
     * 
     * @param array $data
     * @return integer
     */
    
    public function insertOne(array $data) : array {
 
        $id = $this->insertGetId([
            'name' => $data['name'],
            'description' => $data['description'],
            'cost' => $data['cost']
        ]);
        
        $result = $this->insertLinks($data, $id);

        return ['id' => $id];
    }
    
    /**
     * 
     * @param array|boolean $data
     * @param integer $id
     * @return boolean
     */
    function insertLinks(array $data, int $id) : bool {
        //Build links array for sending to DB
        $links = $this->buildLinksData($data, $id);
        
        
        if (!empty($links)) {
            $link = new Link();
            return $link->insert($links);
        }
        return true;
    }
    
    /**
     * Prepares data for pass to DB.
     * Create array contain:
     * ['link' => ..., 'ad_id' => ...]
     * 
     * @param array $data
     * @param int $id
     * @return array
     */
    private function buildLinksData(array $data, int $id) : array {
        $links = [];
        foreach ($this->availableLinks as $key => $value) {
            if (isset($data[$key])) {
                $links[] = ['link' => $data[$key], 'ad_id' => $id];
            }
        }
        return $links;
    }
    
    /**
     * 
     * @param string $sort
     * @param string $way
     * @param integer $number
     * @param string $uri
     * @return array
     */
    
    public function selectAll($sort, $way, $number, string $uri) : array {
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
    
    public function selectOne(int $id, bool $fields) : array {
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
    
    private function selectOneDB(int $id, array $columns, int $limit, array $where, array $orWhere) : array {
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
    
    private function chooseColumns(int $id, bool $fields) : array {
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
}
