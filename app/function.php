<?php

/**
 * Creat all function
 */
function all($path)
{
    return json_decode(file_get_contents($path), false);
}
