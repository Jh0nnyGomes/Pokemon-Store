/* eslint-env jquery */
/*eslint-disable no-unused-vars*/
/*jslint browser: true*/
/*global $, jQuery*/
function UserIcon(username) {
    "use strict";
    var html = "<ul class='userIcon'><li><img src='../img/player.png'>" + username + "</li> <li><a href='logout.php'>Logout</a></li></ul>";
    $("ul.login-navbar").replaceWith(html);
}