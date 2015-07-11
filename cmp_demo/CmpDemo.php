<?php
class CmpDemo
	extends ApiDemo
{
	public function DefaultIndex(){
		include($this->TPL("DefaultIndex","demo"));
	}
}
