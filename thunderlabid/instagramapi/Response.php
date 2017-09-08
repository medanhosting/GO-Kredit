<?php

namespace Thunderlabid\Instagramapi;

//////////////////
// Dependencies //
//////////////////
use Exception;

class Response 
{
	protected $body;
	protected $headers;

	function __construct($body, $headers)
	{
		$this->body = $body;
		$this->headers = $headers;
	}

	//////////////
	// GET META //
	//////////////
	public function isSuccess()
	{
		return $this->body->meta->code == 200;
	}

	public function isRateLimitException()
	{
		return $this->body->meta->code == 429;
	}

	//////////////
	// Get Data //
	//////////////
	public function getData()
	{
		return collect($this->body->data);
	}

	////////////////////
	// Get Pagination //
	////////////////////
	public function getNextUrl()
	{
		return $this->body->pagination->next_url;
	}

	public function getNextMaxId()
	{
		if ($this->body->pagination->next_max_id)
		{
			return $this->body->pagination->next_max_id;
		}
		elseif ($this->body->pagination->max_id)
		{
			return $this->body->pagination->max_id;
		}
		else
		{
			return null;
		}
	}

	////////////////////
	// Get Header 	  //
	////////////////////
	public function getLimitRemaining()
	{
		return $this->headers['x-ratelimit-remaining'][0];
	}

	public function getLimit()
	{
		return $this->headers['x-ratelimit-limit'][0];
	}
}