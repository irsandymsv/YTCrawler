<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client; 
use Google_Service_YouTube;

if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

class youtubeController extends Controller
{
	/**
 	 * Sample PHP code for youtube.search.list
 	 * See instructions for running these code samples locally:
 	 * https://developers.google.com/explorer-help/guides/code_samples#php
 	*/

	protected $client;
	protected $service;
	protected $stemmer;

	public function __construct()
   {
      $this->client = new Google_Client();
    	$this->client->setApplicationName('API code samples');
    	$this->client->setDeveloperKey('API_KEY_HERE'); 
    	// Define service object for making API requests.
    	$this->service = new Google_Service_YouTube($this->client);
   }

	public function searchVideo()
	{
    	$queryParams = [
         'maxResults' => 3,
    	   'order' => 'viewCount',
    	   'publishedBefore' => '2020-03-05T00:00:00.000Z',
    	   'q' => 'soto',
    	   'type' => 'video'
    	];

    	$response = $this->service->search->listSearch('snippet', $queryParams);
    	// dd($response);

    	foreach ($response['modelData']['items'] as $item) {
    		echo "judul: <a href=https://www.youtube.com/watch?v=".$item['id']['videoId'].">".$item['snippet']['title']."</a>"."<br>";
    		echo "nama channel: ".$item['snippet']['channelTitle']."<br>";
    		echo "Comments:"."<br>";
    		try {
    			$this->getComment($item['id']['videoId']);
    		} catch (\Google_Service_Exception $e) {
          echo "======================================================================="."<br><br>";
    			continue;
    		}
    		echo "======================================================================="."<br><br>";
    	}
   }

   public function getComment($videoId = '_VB39Jo8mAQ')
   {
   	$queryParams = [
   		'maxResults' => 5,
   		'moderationStatus' => 'published',
   		'order' => 'time',
   		'textFormat' => 'plainText',
   		'videoId' => $videoId
   	];

   	
   	$response = $this->service->commentThreads->listCommentThreads('snippet,replies', $queryParams);

   	foreach ($response['modelData']['items'] as $item) {
   		$nama_user = $item['snippet']['topLevelComment']['snippet']['authorDisplayName'];
   		$komentar = $item['snippet']['topLevelComment']['snippet']['textOriginal'];

   		echo "*"."<br>";
    		echo "nama: ".$nama_user."<br>";
    		echo "komen: ".$komentar."<br>";
    		// echo "jml balasan: ".$item['snippet']['totalReplyCount']."<br>";
    		echo "<br>";
    	}
   }
}
