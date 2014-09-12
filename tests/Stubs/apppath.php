<?php
/**
 * This is for testing purposes only, as the Sqlite configuration has a hard dependency on Laravel's
 * app_path() method, which helps us to ascertain where an sqlite database file is kept.
 */

function app_path() {
	return 'app-path';
}

