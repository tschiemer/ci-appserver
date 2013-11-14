<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Geo lookup helper
 * 
 * @copyright (c) 2013, Philip Tschiemer
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 * @package ci-appserver
 * @link https://github.com/tschiemer/ci-appserver 
 */
class Geo_model extends CI_Model {
    
    var $tbl_lookup = 'geo_lookup';
    
    /**
     * 
     * @param string $address
     * @param string $tld
     * @return null|\stdClass
     */
    public function lookup($address,$tld='ch')
    {
        $loc = $this->get_lookup($address, $tld);
        
        if ( ! empty($loc))
        {
            return $loc;
        }
        $addr = urlencode($address);

        $url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&region={$tld}&address={$addr}";


        $json = file_get_contents($url);

        $obj = json_decode($json);


        if ($obj->status != 'OK')
        {
            return NULL;
        }
        
        
        $loc = new stdClass();
        $found = FALSE;
        
        foreach($obj->results as $result)
        {
            if (isset($result->geometry->location))
            {
                $loc->lat = $result->geometry->location->lat;
                $loc->long = $result->geometry->location->lng;
                $found = TRUE;
                break;
            }
        }
        
        if ($found)
        {
            $this->insert_lookup($address, $loc->lat, $loc->long, $tld);
            return $loc;
        }
        
        return NULL;
    }
    
    public function get_lookup($address,$tld='ch')
    {
        $address = trim($address);
        $address = preg_replace('/(\h+)/',' ',$address);
        
        $query = $this->db->select('lat,long')
                            ->where('address',$address)
                            ->where('tld',$tld)
                            ->limit(1)
                            ->get($this->tbl_lookup);
        
        if ($query->num_rows() == 1)
        {
            return $query->row();
        }
        else
        {
            return NULL;
        }
    }
    
    public function insert_lookup($address,$lat,$long,$tld='ch')
    {
        $address = trim($address);
        $address = preg_replace('/(\h+)/',' ',$address);
        
        $this->db->set('address',$address)
                 ->set('lat',$lat)
                 ->set('long',$long)
                 ->set('tld',$tld)
                 ->insert($this->tbl_lookup);
        
        return $this->db->affected_rows();
    }
    
    public function clear_lookups()
    {
        $this->db->delete($this->tbl_lookup);
    }
    
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    /*::                                                                         :*/
    /*::  This routine calculates the distance between two points (given the     :*/
    /*::  latitude/longitude of those points). It is being used to calculate     :*/
    /*::  the distance between two locations using GeoDataSource(TM) Products    :*/
    /*::                     													 :*/
    /*::  Definitions:                                                           :*/
    /*::    South latitudes are negative, east longitudes are positive           :*/
    /*::                                                                         :*/
    /*::  Passed to function:                                                    :*/
    /*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
    /*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
    /*::    unit = the unit you desire for results                               :*/
    /*::           where: 'M' is statute miles                                   :*/
    /*::                  'K' is kilometers (default)                            :*/
    /*::                  'N' is nautical miles                                  :*/
    /*::  Worldwide cities and other features databases with latitude longitude  :*/
    /*::  are available at http://www.geodatasource.com                          :*/
    /*::                                                                         :*/
    /*::  For enquiries, please contact sales@geodatasource.com                  :*/
    /*::                                                                         :*/
    /*::  Official Web site: http://www.geodatasource.com                        :*/
    /*::                                                                         :*/
    /*::         GeoDataSource.com (C) All Rights Reserved 2013		   		     :*/
    /*::                                                                         :*/
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    function distance($lat1, $lon1, $lat2, $lon2, $unit) {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);

      if ($unit == "K") {
        return ($miles * 1.609344);
      } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
            return $miles;
          }
    }
}