<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>Kantor Tidak Aktif</title>

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

		<!-- Styles -->
		<style>
			html, body {
				background-color: #fff;
				color: #636b6f;
				font-family: 'Raleway', sans-serif;
				font-weight: 100;
				height: 100vh;
				margin: 0;
			}

			.full-height {
				height: 100vh;
			}

			.flex-center {
				align-items: center;
				display: flex;
				justify-content: center;
			}

			.position-ref {
				position: relative;
			}

			.content {
				text-align: center;
			}

			.title {
				font-size: 84px;
			}
		</style>
	</head>
	<body>
		<div class="flex-center position-ref full-height">
			<div class="content">
				<h1>
					Kantor Ini Belum Aktif
				</h1>
				<h5>
					Silahkan Hubungi Admin Untuk Melengkapi Data Kantor; <a href="{{route('pilih.koperasi', ['prev_url' => route('home')])}}">Pilih Kantor Lain</a>
				</h5>
			</div>
		</div>
	</body>
</html>
