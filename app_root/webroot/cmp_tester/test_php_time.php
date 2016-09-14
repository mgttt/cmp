<?php
require_once "../cmp_demo/inc.app.php";

println('<pre>');
println("date_default_timezone_get=".date_default_timezone_get());
print("my_isoDateTime=");
println(my_isoDateTime());
adjust_timezone();
print("my_isoDateTime=");
println(my_isoDateTime());
println("date_default_timezone_get=".date_default_timezone_get());

