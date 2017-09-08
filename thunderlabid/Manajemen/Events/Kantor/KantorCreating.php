<?php

namespace Thunderlabid\Manajemen\Events\Kantor;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use Thunderlabid\Manajemen\Models\Kantor;

use Carbon\Carbon;

class KantorCreating
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $data;
	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Kantor $data)
	{
		$this->data     = $data;
		$this->data->id = $this->setIDKantor();
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

	protected function setIDKantor()
	{
		$first_letter       = Carbon::now()->format('ym').'.';
		$prev_data          = Kantor::where('id', 'like', $first_letter.'%')->orderby('id', 'desc')->first();

		if($prev_data)
		{
			$last_letter	= explode('.', $prev_data['id']);
			$last_letter	= ((int)$last_letter[1] * 1) + 1;
		}
		else
		{
			$last_letter	= 1;
		}

		$last_letter		= str_pad($last_letter, 4, '0', STR_PAD_LEFT);

		return $first_letter.$last_letter;
	}
}
