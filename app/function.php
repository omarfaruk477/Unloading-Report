<?php

/**
 * Creat all function
 */
function all($path)
{
    return json_decode(file_get_contents($path), false);
}


function creatAlert($msg, $type = 'danger')
{
    return "<p class=\"alert alert-$type d-flex justify-content-between mx-3 mt-3 mb-0\">$msg<button class=\"btn btn-close\" data-bs-dismiss=\"alert\"></button></p>";
}
