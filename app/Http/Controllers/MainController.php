<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DOMDocument;
use DOMNode;
use Log;
use Redis;
// use Illuminate\Support\Facades\Redis;

// use App\Http\Controllers\DOMDocument;
class MainController extends Controller
{
    //
    public function homePage(){
    	return view('MainPages.home');
    }
    public function aboutPage(){
    	return view('MainPages.about');
    }
    public function loadData(){
    	$countries=json_decode(file_get_contents('../resources/json/nationality.json'));
 		foreach ($countries as $key => $country) {
 			$data=file_get_contents('https://en.wikipedia.org/wiki/Visa_requirements_for_'.$country->nationality.'_citizens
    		');
    		$dom = new domDocument;
			@$dom->loadHTML($data);
			$dom->preserveWhiteSpace = false;
			$table = $dom->getElementsByTagName('table');
			if(isset($country->index)){
				$rows = $table->item($country->index)->getElementsByTagName('tr');
			}else{
				$rows = $table->item(0)->getElementsByTagName('tr');
			}
			$tableData=[];
			for ($i=1; $i <sizeof($rows)-1 ; $i++) { 
				$data=[];
				$cols=$this->getHTMLData($rows[$i]);
				
				if(sizeof($cols)>6){
					$data["country"]=$cols[0];
					$data["visa_required"]= preg_replace('/\[.*\]/', '', $cols[2]);
					$data["stay_allowd"]=preg_replace('/\[.*\]/', '', $cols[4]);
					$data["notes"]= preg_replace('/\[.*\]/', '', $cols[6]);
					array_push($tableData, $data);
				}else{
					Log::info($cols);
				}
			}
			Redis::set($country->country,json_encode($tableData));
    		// dd($data);
 		}
 	}
 	public function getData(){
 		$countries=json_decode(file_get_contents('../resources/json/nationality.json'));
 	
 		foreach ($countries as $key => $country) {
 			$data=[];
 			$data=json_decode(Redis::get($country->country));
 			// dd($data);
 			foreach ($data as $key2 => $countryInfo) {
 				$info=array($countryInfo->visa_required,$countryInfo->stay_allowd,$countryInfo->notes);
	 			$myArray=array($country->country,$countryInfo->country,$info);
	 			List($a[],$b[],$c[])=$myArray;
 			}
 		}
 		dd($a);
 	}
	public function getHTMLData(DOMNode $element) 
	{ 
		// dd("innn");
	    $innerHTML = []; 
	    $children  = $element->childNodes;
	    foreach ($children as $child) 
	    {
	    	// Log::info($element->ownerDocument->saveHTML($child));
	    	// preg_replace('/\r\n|\r|\n/','',
	        array_push($innerHTML, trim(strip_tags($element->ownerDocument->saveHTML($child)))); 
		}

	    return $innerHTML; 
	}
 

// foreach ($rows as $row) {
//         $cols = $row->getElementsByTagName('td');
// dd($cols[0]);
        
        // echo $cols[2];
// print_r($cols);
// }
    	// echo $html;
  //   	$c = curl_init('https://en.wikipedia.org/wiki/Visa_requirements_for_Lebanese_citizens');
  //   	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		// $html = curl_exec($c);
		// if (curl_error($c))
		//     die(curl_error($c));
		// $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		// curl_close($c);
    // }
}
