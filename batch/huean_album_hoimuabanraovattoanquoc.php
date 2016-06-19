<?php
$albums = [
    'huean_album_hoimuabanraovattoanquoc_baloinphongcachthethao.php',
    'huean_album_hoimuabanraovattoanquoc_baloinphongcachvpop.php',
    'huean_album_hoimuabanraovattoanquoc_balolaptop.php',
    'huean_album_hoimuabanraovattoanquoc_balonam.php',
    'huean_album_hoimuabanraovattoanquoc_balonu.php',
    'huean_album_hoimuabanraovattoanquoc_balosinhvien.php',
    'huean_album_hoimuabanraovattoanquoc_tuicheosinhvien.php',
    'huean_album_hoimuabanraovattoanquoc_tuirutphongcachvpop.php',
    'huean_album_hoimuabanraovattoanquoc_tuixachcaptapnam.php',
    'huean_album_hoimuabanraovattoanquoc_tuixachnu.php',
];
$album = $albums[rand(0, count($albums) - 1)];
include ($album);