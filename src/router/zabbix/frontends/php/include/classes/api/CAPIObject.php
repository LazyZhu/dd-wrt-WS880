<?php
/*
** Zabbix
** Copyright (C) 2001-2015 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/
?>
<?php

class CAPIObject {
	private $_name;

	public function __construct($name) {
		$this->_name = $name;
	}

	public function __call($method, $params) {
		if (!isset(CWebUser::$data['sessionid']))
			CWebUser::$data['sessionid'] = null;

		$param = empty($params) ? null : reset($params);
		$result = czbxrpc::call($this->_name.'.'.$method, $param, CWebUser::$data['sessionid']);

		// saving API call for the debug statement
		CProfiler::getInstance()->profileApiCall($this->_name, $method, $params, isset($result['result']) ? $result['result'] : '');

		if (isset($result['result'])) {
			return $result['result'];
		}
		else {
			$trace = $result['data'];

			if (isset($result['debug'])) {
				$trace .= ' ['.CProfiler::getInstance()->formatCallStack($result['debug']).']';
			}

			error($trace);

			return false;
		}
	}
}
