<?php
	function preview($data)
	{
		echo "<pre>";
		print_r ($data);
		exit;
	}

	function format_date($date)
	{
		return date('d M, Y',strtotime($date));
	}