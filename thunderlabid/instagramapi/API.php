<?php

namespace Thunderlabid\Instagramapi;

//////////////////
// Dependencies //
//////////////////
use GuzzleHttp\Client;
use Exception;

class API 
{
	protected $access_token;
	protected $ch;

	protected $url = [
		'self'				=> 'https://api.instagram.com/v1/users/self/?access_token={access-token}',
		'self_media'		=> 'https://api.instagram.com/v1/users/self/media/recent/?access_token={access-token}&count=100&max_id={max-id}',
		'self_liked'		=> 'https://api.instagram.com/v1/users/self/media/liked?access_token={access-token}&max_like_id={max-id}',
		'self_follows'		=> 'https://api.instagram.com/v1/users/self/follows?access_token={access-token}',
		'self_followed_by'	=> 'https://api.instagram.com/v1/users/self/followed-by?access_token={access-token}',
		'self_follow'		=> 'https://api.instagram.com/v1/users/{user-id}/relationship?access_token={access-token}&action=follow',
		'self_unfollow'		=> 'https://api.instagram.com/v1/users/{user-id}/relationship?access_token={access-token}&action=unfollow',
		'user'				=> 'https://api.instagram.com/v1/users/{user-id}/?access_token={access-token}',
		'user_media'		=> 'https://api.instagram.com/v1/users/{user-id}/media/recent/?access_token={access-token}&max_id={max-id}',
		'user_search'		=> 'https://api.instagram.com/v1/users/search?q={search}&access_token={access-token}',
		'media_by_location'	=> 'https://api.instagram.com/v1/media/search?lat={lat}&lng={lng}&access_token={access-token}&distance={distance}',
		'media_comments'	=> 'https://api.instagram.com/v1/media/{media-id}/comments?access_token={access-token}',
		'media_comment'		=> 'https://api.instagram.com/v1/media/{media-id}/comments',
		'media_likes'		=> 'https://api.instagram.com/v1/media/{media-id}/likes?access_token={access-token}',
		'media_like'		=> 'https://api.instagram.com/v1/media/{media-id}/likes',
		'media_unlike'		=> 'https://api.instagram.com/v1/media/{media-id}/likes?access_token={access-token}',
		'media_by_tags'		=> 'https://api.instagram.com/v1/tags/{tag-name}/media/recent?access_token={access-token}&max_tag_id={max-id}',
	];

	function __construct($access_token)
	{
		$this->access_token = $access_token;

		///////////////////
		// Initiate CURL //
		///////////////////
        $this->ch = new Client();
	}

	function __destruct()
	{
		curl_close($this->ch);
	}

	// ------------------------------------------------------------------------------------------------------------------------
	// EXEC
	// ------------------------------------------------------------------------------------------------------------------------
	private function exec($mode, $url, $options = null)
	{
		$response 		= $this->ch->request($mode, $url, is_array($options) ? $options : []);
		$body		 	= json_decode((string)$response->getBody());
		$header		 	= $response->getHeaders();

		// close cURL resource, and free up system resources
		return new Response($body, $header);
	}

	// ------------------------------------------------------------------------------------------------------------------------
	// SELF
	// ------------------------------------------------------------------------------------------------------------------------
	public function self()
	{
		$url = $this->url['self'];
		$url = str_replace('{access-token}', $this->access_token, $url);

		$response = $this->exec('GET', $url);
		return $response;
	}

	public function self_media($next_max_id = null)
	{
		$url = $this->url['self_media'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{max-id}', $next_max_id ? $next_max_id : '', $url);

		$response = $this->exec('GET', $url);
		return $response;
	}

	public function self_liked($next_max_id = null)
	{
		$url = $this->url['self_liked'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{max-id}', $next_max_id ? $next_max_id : '', $url);

		$response = $this->exec('GET', $url);
		return $response;
	}

	public function self_follows()
	{
		$url = $this->url['self_follows'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{max-id}', $next_max_id ? $next_max_id : '', $url);

		$response = $this->exec('GET', $url);
		return $response;
	}

	public function self_followed_by($next_max_id = null)
	{
		$url = $this->url['self_followed_by'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{max-id}', $next_max_id ? $next_max_id : '', $url);

		$response = $this->exec('GET', $url);
		return $response;
	}

	public function self_follow($user_id)
	{
		$url = $this->url['self_follow'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{user-id}', $user_id, $url);

		$response = $this->exec('POST', $url);
		return $response;
	}

	public function self_unfollow($user_id)
	{
		$url = $this->url['self_unfollow'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{user-id}', $user_id, $url);

		$response = $this->exec('POST', $url);
		return $response;
	}

	// ------------------------------------------------------------------------------------------------------------------------
	// USER
	// ------------------------------------------------------------------------------------------------------------------------
	public function user($user_id)
	{
		$url = $this->url['user'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{user-id}', $user_id, $url);

		$response = $this->exec('GET', $url);
		return $response;
	}

	public function user_media($user_id, $next_max_id = null)
	{
		$url = $this->url['user_media'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{user-id}', $user_id, $url);
		$url = str_replace('{max-id}', $next_max_id ? $next_max_id : '', $url);

		$response = $this->exec('GET', $url);
		return $response;
	}

	public function user_search($search)
	{
		$url = $this->url['user_search'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{search}', $search, $url);

		$response = $this->exec('GET', $url);
		return $response;
	}


	// ------------------------------------------------------------------------------------------------------------------------
	// MEDIA
	// ------------------------------------------------------------------------------------------------------------------------
	public function media_by_location($lat, $lng, $distance = 1000)
	{
		$url = $this->url['media_by_location'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{lat}', $lat, $url);
		$url = str_replace('{lng}', $lng, $url);
		$url = str_replace('{distance}', $distance, $url);

		$response = $this->exec('GET', $url);
		return $response;
	}

	public function media_comments($media_id)
	{
		$url = $this->url['media_comments'];
		$url = str_replace('{access-token}', $this->access_token, $url);
		$url = str_replace('{media-id}', $media_id, $url);

		$response = $this->exec('GET', $url);
		return $response;
	}

	public function media_comment($media_id, $text)
	{
		$url = $this->url['media_comment'];
		$url = str_replace('{media-id}', $media_id, $url);

		$response = $this->exec('POST', $url, [
				'form_params' => [
					'access_token' => $this->access_token,
					'text' => $text,
				]
			]);
		return $response;
	}

	public function media_likes($media_id)
	{
		$url = $this->url['media_likes'];
		$url = str_replace('{media-id}', $media_id, $url);
		$response = $this->exec('GET', $url);
		return $response;
	}

	public function media_like($media_id)
	{
		$url = $this->url['media_like'];
		$url = str_replace('{media-id}', $media_id, $url);

		$response = $this->exec('POST', $url, [
				'form_params' => [
					'access_token' => $this->access_token,
				]
			]);
		return $response;
	}

	public function media_unlike($media_id)
	{
		$url = $this->url['media_unlike'];
		$url = str_replace('{media-id}', $media_id, $url);

		$response = $this->exec('DELETE', $url);
		return $response;
	}

	public function media_by_tags($tag, $next_max_id)
	{
		$url = $this->url['media_by_tags'];
		$url = str_replace('{tag-name}', $tag, $url);
		$url = str_replace('{max-id}', $next_max_id, $url);
		$response = $this->exec('GET', $url);
		return $response;
	}
}