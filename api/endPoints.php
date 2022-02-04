<?php
	function getEndPoints($queries) {
		// Tässä end pointtien määritykset API:lle
		// .htaccess -tiedostoon pitää tehdä seuraava lisäys, jotta End Pointit löytyvät:
		//
		// RewriteEngine On
		// RewriteBase /
		//
		// RewriteCond %{REQUEST_FILENAME} !-f
		// RewriteCond %{REQUEST_FILENAME} !-d
		// RewriteRule ^api/(.*)$ /api/index.php
		//
		$endPoints = array();
		$movie = array();
		$movie['uri'] = '/api/getMovie';
		$movie['ep'] = 'http://www.omdbapi.com';
		$movie['parameter'][0] = 'apikey';
		$movie['parameter'][1] = 't';
		$movie['parameter'][2] = 'y';
		$movie['parameter'][3] = 'plot';
		$movie['value'][0] = 'd4b50d25'; // API Key testausta varten
		$movie['value'][1] = $queries->title;
		$movie['value'][2] = $queries->year;
		$movie['value'][3] = $queries->plot;
		$endPoints[] = $movie;
		$book = array();
		$book['uri'] = '/api/getBook';
		$book['ep'] = 'https://openlibrary.org/api/books';
		$book['parameter'][0] = 'bibkeys';
		$book['parameter'][1] = 'jscmd';
		$book['parameter'][2] = 'format';
		$book['value'][0] = $queries->isbn;
		$book['value'][1] = 'data';
		$book['value'][2] = 'json';
		$endPoints[] = $book;
		//
		foreach ($endPoints as $index => $endPoint) {
			$endPointObjects[$index] = new Entity();
			$endPointArrayKeys = array_keys($endPoint);
			foreach ($endPointArrayKeys as $endPointArrayKey) {
				$endPointObjects[$index]->__set($endPointArrayKey, $endPoint[$endPointArrayKey]);
			}
		}
		return $endPointObjects;
	}
?>