<div class="clearfix">&nbsp;</div>
<div class="row p-b-sm">
	<div class="col-sm-6">
		@if(is_null($kantor['id']))
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-10">
				<h5 class="text-uppercase">Input Data</h5>
				<p>Isi form dibawah ini untuk menambahkan kantor baru.</p>
			</div>
		</div>
		@endif

		@if(is_null($kantor['id']))
			{!! Form::open(['url' => route('kantor.store', ['kantor_aktif_id' => $kantor_aktif['id']])]) !!}
		@else
			{!! Form::open(['url' => route('kantor.update', ['id' => $kantor['id'], 'kantor_aktif_id' => $kantor_aktif['id']]), 'method' => 'PATCH']) !!}
		@endif
			<fieldset class="form-group">
				<label class="text-sm">Nama kantor</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						{!! Form::text('nama', $kantor['nama'], ['class' => 'form-control required', 'placeholder' => 'Masukkan nama kantor']) !!}			
					</div>
				</div>
			</fieldset>

			<fieldset class="form-group">
				<label class="text-sm">Tipe Kantor</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						{!! Form::select('tipe', ['cabang' => 'Cabang', 'pusat' => 'Pusat'], $kantor['tipe'], ['class' => 'form-control custom-select required', 'placeholder' => 'Masukkan tipe kantor']) !!}			
					</div>
				</div>
			</fieldset>
			<fieldset class="form-group">
				<label class="text-sm">Jenis Kantor</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						{!! Form::select('jenis', ['bpr' => 'BPR', 'koperasi' => 'Koperasi'], $kantor['jenis'], ['class' => 'form-control custom-select required', 'placeholder' => 'Masukkan jenis kantor']) !!}			
					</div>
				</div>
			</fieldset>

			<fieldset class="form-group">
				<label class="text-sm">Kode Pusat</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						@include('v2.kantor.ajax-kode-pusat')
					</div>
				</div>
			</fieldset>

			<fieldset class="gllpLatlonPicker">
				<label class="text-sm">Alamat Lengkap</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						<input id="pac-input" class="form-control col-6" type="text" placeholder="Search Box">
						<div id="map" style="height: 300px;width: 100%;"></div>

						<!-- <div class="input-group">
							{!! Form::text('alamat', $kantor['alamat'], [
								'class' => 'form-control gllpSearchField required', 
								'placeholder' => 'Masukkan alamat',
								'onFocus' => 'geolocate()',
								'id' => 'autocomplete'
							]) !!}
							<span class="input-group-btn">
								<button class="btn btn-default gllpSearchButton" type="button" style="padding-bottom: 9px;">
									<i class="fa fa-search" aria-hidden="true"></i>
								</button>
							</span>
						</div> -->
					</div>
				</div>
				<br/>
				{{--
				<!-- <div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						<label class="text-sm">Lokasi Dalam Peta</label>
					</div>
				</div> -->
				--}}
				<!-- <div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						<div class="gllpMap">
							Loading Google Maps
							<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
						</div>
					</div>
				</div>
				<br/> -->
				{!! Form::hidden('latitude', $kantor['latitude'], ['class' => 'gllpLatitude']) !!}
				{!! Form::hidden('longitude', $kantor['longitude'], ['class' => 'gllpLongitude']) !!}

				<input type="hidden" name="latitude" class="gllpLatitude" value="{{ $kantor['latitude'] }}" />
				<input type="hidden" name="longitude" class="gllpLongitude" value="{{ $kantor['longitude'] }}" />
				<input type="hidden" class="gllpZoom"/>
			</fieldset>

			<fieldset class="form-group">
				<label class="text-sm">Telepon</label>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						{!! Form::text('telepon', $kantor['telepon'], ['class' => 'form-control required', 'placeholder' => 'Masukkan nomor telepon']) !!}			
					</div>
				</div>
			</fieldset>	

			<fieldset class="form-group">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10">
						<!-- @if(!is_null($kantor['id']))
						<a class="btn btn-default" href="{{ URL::previous() }}" no-data-pjax>Batal</a>
						@endif -->
						<button type="submit" class="btn btn-primary">{{ is_null($kantor['id']) ? 'Tambahkan' : 'Simpan' }}</button>
					</div>
				</div>
			</fieldset>
		{!! Form::close() !!}
	</div>
</div>

@push('js')
	<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDhGU-wSjC89hoHPStx7bYGOjHpULJQHGI&libraries=places&callback=initAutocomplete" async defer></script>        
	<script type="text/javascript">

	function initAutocomplete() {
		var map = new google.maps.Map(document.getElementById('map'), {
		  center: {lat: -7.9540221, lng: 112.5684764},
		  zoom: 10,
		  mapTypeId: 'roadmap'
		});

		// Create the search box and link it to the UI element.
		var input = document.getElementById('pac-input');
		var searchBox = new google.maps.places.SearchBox(input);
		map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

		// Bias the SearchBox results towards current map's viewport.
		map.addListener('bounds_changed', function() {
		  searchBox.setBounds(map.getBounds());
		});

		var markers = [];
		// Listen for the event fired when the user selects a prediction and retrieve
		// more details for that place.
		searchBox.addListener('places_changed', function() {
		  var places = searchBox.getPlaces();

		  if (places.length == 0) {
			return;
		  }

		  // Clear out the old markers.
		  markers.forEach(function(marker) {
			marker.setMap(null);
		  });
		  markers = [];

		  // For each place, get the icon, name and location.
		  var bounds = new google.maps.LatLngBounds();
		  places.forEach(function(place) {
			if (!place.geometry) {
			  console.log("Returned place contains no geometry");
			  return;
			}

			var alamat 	= {
				alamat : place.address_components[0].long_name,
				kelurahan : place.address_components[1].long_name,
				kecamatan : place.address_components[2].long_name,
				kota : place.address_components[3].long_name,
			}

			var geolocation = {
				latitude : bounds.b.b,
				longitude : bounds.f.b,
			}


			var icon = {
			  url: place.icon,
			  size: new google.maps.Size(71, 71),
			  origin: new google.maps.Point(0, 0),
			  anchor: new google.maps.Point(17, 34),
			  scaledSize: new google.maps.Size(25, 25)
			};

			// Create a marker for each place.
			markers.push(new google.maps.Marker({
			  map: map,
			  icon: icon,
			  title: place.name,
			  position: place.geometry.location
			}));
			// console.log(markers.position);

			if (place.geometry.viewport) {
			  // Only geocodes have viewport.
			  bounds.union(place.geometry.viewport);
			} else {
			  bounds.extend(place.geometry.location);
			}
		  });
		  map.fitBounds(bounds);
		});
	  }
	// var placeSearch, autocomplete;
	// var componentForm = {
	// street_number: 'short_name',
	// route: 'long_name',
	// locality: 'long_name',
	// administrative_area_level_1: 'short_name',
	// country: 'long_name',
	// postal_code: 'short_name'
	// };

	// function initAutocomplete() {
	// // Create the autocomplete object, restricting the search to geographical
	// // location types.
	// autocomplete = new google.maps.places.Autocomplete(
	// 	/** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
	// 	{types: ['geocode']});

	// // When the user selects an address from the dropdown, populate the address
	// // fields in the form.
	// autocomplete.addListener('place_changed', fillInAddress);
	// }

	// function fillInAddress() {
	// // Get the place details from the autocomplete object.
	// var place = autocomplete.getPlace();

	// for (var component in componentForm) {
	// 	document.getElementById(component).value = '';
	// 	document.getElementById(component).disabled = false;
	// }

	// // Get each component of the address from the place details
	// // and fill the corresponding field on the form.
	// for (var i = 0; i < place.address_components.length; i++) {
	// 	var addressType = place.address_components[i].types[0];
	// 	if (componentForm[addressType]) {
	// 		var val = place.address_components[i][componentForm[addressType]];
	// 		document.getElementById(addressType).value = val;
	// 	}
	// }
	// }

	// // Bias the autocomplete object to the user's geographical location,
	// // as supplied by the browser's 'navigator.geolocation' object.
	// function geolocate() {
	// 	if (navigator.geolocation) {
	// 		navigator.geolocation.getCurrentPosition(function(position) {
	// 			var geolocation = {
	// 				lat: position.coords.latitude,
	// 				lng: position.coords.longitude
	// 			};
	// 			var circle = new google.maps.Circle({
	// 				center: geolocation,
	// 				radius: position.coords.accuracy
	// 			});
	// 			autocomplete.setBounds(circle.getBounds());
	// 		});
	// 	}
	// }

	// $(window).load( function() {
	// 	window.mapInit();
	// });	
	</script>
@endpush