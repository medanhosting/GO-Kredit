<?php

namespace Thunderlabid\Survei\Events\SurveiDetail;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use Thunderlabid\Survei\Models\SurveiDetail;

class SurveiDetailCreating
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $data;
	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(SurveiDetail $data)
	{
		$this->data     = $data;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('channel-name');
	}
}
