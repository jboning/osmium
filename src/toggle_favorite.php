<?php
/* Osmium
 * Copyright (C) 2012 Romain "Artefact2" Dalmaso <artefact2@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Osmium\ToggleFavorite;

require __DIR__.'/../inc/root.php';

if(!\Osmium\State\is_logged_in()) {
	\Osmium\fatal(403, 'Not logged in.');
}

$loadoutid = intval($_GET['loadoutid']);

if(!isset($_GET['tok']) || $_GET['tok'] != \Osmium\State\get_token()) {
	\Osmium\fatal(403, 'Invalid token.');
}

$accountid = \Osmium\State\get_state('a')['accountid'];

$row = \Osmium\Db\fetch_row(\Osmium\Db\query_params('SELECT loadoutid FROM osmium.allowedloadoutsbyaccount WHERE accountid = $1 AND loadoutid = $2', array($accountid, $loadoutid)));

if($row === false) {
	\Osmium\fatal(404, 'No such loadout.');
}

$fav = \Osmium\Db\fetch_row(\Osmium\Db\query_params('SELECT loadoutid FROM osmium.accountfavorites WHERE accountid = $1 AND loadoutid = $2', array($accountid, $loadoutid)));

if($fav === false) {
	\Osmium\Db\query_params('INSERT INTO osmium.accountfavorites (accountid, loadoutid) VALUES ($1, $2)',
	                        array($accountid, $loadoutid));
} else {
	\Osmium\Db\query_params('DELETE FROM osmium.accountfavorites WHERE accountid = $1 AND loadoutid = $2',
	                        array($accountid, $loadoutid));
}

header('Location: ../loadout/'.$loadoutid);
die();