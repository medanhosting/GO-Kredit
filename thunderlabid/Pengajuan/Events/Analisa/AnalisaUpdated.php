<?php

namespace Thunderlabid\Pengajuan\Events\Analisa;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use Thunderlabid\Pengajuan\Models\Analisa;

class AnalisaUpdated
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $data;
	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Analisa $data)
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
