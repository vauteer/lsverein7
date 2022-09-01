<?php
namespace App;

enum ClubRole: int {
    case Basic = 1;
    case Advanced = 128;
    case Admin = 256;
}
?>
