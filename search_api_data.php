<?php

class dataSearch
{
    
    private $base;
    private $term;
    private $hits;
    private $paged;
    private $per_page;
    
    
    public function __construct()
    {
        $this->base     = 'index.php';
        $this->per_page = 10;
        
        if (isset($_GET['term'])) {
            $this->term = $_GET['term'];
        }
        
        if (isset($_GET['pgn'])) {
            $this->paged = $_GET['pgn'];
        }
    }
    
    
    public function data_search()
    {
        if ($this->term) {
            $json = file_get_contents("personnel_data.json");
            $data = json_decode(utf8_encode($json), true);
            
            $results    = $this->search_data($data);
            $this->hits = count($results);
            return $this->display_results($results, $this->term);
        }
    }
    
    
    private function search_data($data)
    {
        $results = array();
        $arr     = array();
        $query   = explode(' ', $this->term);
        
        foreach ($data as $item) {
            if (stripos($item['title'], $query[0]) !== false) {
                if (!isset($query[1])) {
                    // if only one query return results from one term
                    array_push($results, $item);
                } else if (stripos($item['title'], $query[0]) !== false && stripos($item['title'], $query[1]) !== false) {
                    // if there are two query terms search both of them
                    array_push($results, $item);
                }
            }
        }
        return $results;
    }
    
    
    private function display_results($results)
    {
        $page = 1;
        // check for pagination
        if ($this->paged) {
            $page = $this->paged;
        }
        
        $out = '<h4>You searched for: <strong>"' . $this->term . '"</strong></h4>';
        
        $out .= '<div class="results-container">';
        
        $out .= $this->pagination();
        
        $out .= '<div class="results">';
        
        $displayCount = 0;
        $resultCount  = 0;
        
        foreach ($results as $result) {
            
            // skip results less than paginated value
            if ($resultCount < (($page - 1) * $this->per_page)) {
                $resultCount++;
                continue;
            }
            
            // dont show more than paginated limit
            if ($displayCount >= $this->per_page) {
                continue;
            }
            $displayCount++;
            
            $snippet = $result['name'];
            if ($snippet) {
                if (strlen($snippet) > 220) {
                    $snippet = substr($snippet, 0, 220) . '...';
                }
            }
            
            $out .= '<div class="result" style="margin:30px;">';
            $out .= '<a href="mailto:' . $result['email'] . '"><h4>' . $result['title'] . '</h4></a>';
            $out .= '<p>' . $snippet . '</p>';
            $out .= '</div>';
        }
        
        $out .= '</div>';
        
        $out .= '</div>';
        
        return $out;
    }
    
    
    private function pagination()
    {
        $page     = $this->paged;
        $pages    = ($this->hits - $this->hits % $this->per_page) / $this->per_page;
        $afterout = null;
        
        $out = '<div class="pagination">';
        
        $out .= '<div class="total-results">Total results: <strong>' . $this->hits . '</strong></div>';
        
        if ($this->hits > $this->per_page) {
            
            for ($i = 0; $i <= $pages; $i++) {
                
                if ($i < ($page - 4)) {
                    if ($i == ($page - 5)) {
                        $out .= '<span><a href="' . $this->base . '?term=' . $this->term . '&pgn=1">1</a></span> ...';
                    }
                    continue;
                }
                
                if ($i > 7 && $i > ($page + 2)) {
                    if (!$afterout) {
                        $afterout = 1;
                        $out .= '... ';
                        $pages = $pages + 1;
                        $out .= '<span><a href="' . $this->base . '?term=' . $this->term . '&pgn=' . $pages . '">' . $pages . '</a></span>';
                    }
                    continue;
                }
                
                $no    = $i + 1;
                $class = '';
                if ($no == $page) {
                    $class .= 'current';
                }
                $out .= ' <span class="' . $class . '"><a href="' . $this->base . '?term=' . $this->term . '&pgn=' . $no . '">' . $no . '</a></span> ';
            }
        }
        
        $out .= '</div>';
        
        return $out;
    }
}

