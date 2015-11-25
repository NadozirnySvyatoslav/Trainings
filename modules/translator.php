<?php
class Translator {
	public function __get($name) {
		if (! defined ( $name ))
			define ( $name, $name );
		return constant ( $name );
	}
}
