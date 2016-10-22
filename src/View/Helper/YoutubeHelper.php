<?php
namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;


class YoutubeHelper extends AbstractHelper {
    private $key;
	private $data;
	private $embed;
	private $image = array('big' => NULL,
		'small' => NULL);

	public function __construct($url = null) {
		$this->getKey($url);
	}

	public function getImages($url = NULL){
		$this->getKey($url);
        $this->image['big'] = '//img.youtube.com/vi/' . $this->key . '/0.jpg';
        $this->image['small'] = '//i1.ytimg.com/vi/' . $this->key . '/default.jpg';
	    return $this->image;
	}
	public function getEmbed($url = NULL){
		$this->getKey($url);
        return $this->embed = '//youtube.com/embed/' . $this->key;
	}
	public function getInfor($url = NULL){
		$this->getKey($url);
		$this->gData();
		$this->getImages();
		$this->getEmbed();

		return array('image' => $this->image,
			'key' => $this->key,
			'embed' => $this->embed,
			'published' => $this->data->published,
			'updated' => $this->data->updated,
			'title' => (string)$this->data->title,
			'description' => (string)$this->data->content,
			'author' => (string)$this->data->author->name);
	}
    private function getKey($url = NULL){

        if($url !== NULL && $url !== "#") {
            preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
           if(isset($matches[1]))
           	$this->key = $matches[1];
           else
           	$this->key = "";
        }
        return $this->key;
    }
	private function gData($url = NULL) {
		$this->getKey($url);
		$curl = curl_init('http://gdata.youtube.com/feeds/api/videos/' . $this->key);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($curl);
		return $this->data = simplexml_load_string($xml);
	}

}