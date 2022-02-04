<?php
	class Entity {
		private $data = array();

		public function __set($name, $value) {
			$this->data[$name] = $value;
		}

		public function __get($name) {
			if (array_key_exists($name, $this->data)) {
				return $this->data[$name];
			}
		}

		public function __isset($name) {
			return isset($this->data[$name]);
		}
		
		public function getData() {
			return $this->data;
		}
	}
?>