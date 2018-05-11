/* eslint-env jquery */
/*eslint-disable no-unused-vars*/
/*jslint browser: true*/
/*global $, jQuery*/
function UserIcon(username) {
    "use strict";
    var html = "<ul class='userIcon'><li><a href='logout.php'>Logout</a></li> <li><a href='userprofile.php'>" + username + "</li></ul>";
    $("ul.login-navbar").replaceWith(html);
}