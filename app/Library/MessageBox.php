<?php


namespace App\Library;


class MessageBox
{
    public static function echoInfo($msg,$time=5000): string
    {
        return '$(document.body).append(\''.
            '<div class="alert alert-info alert-dismissible" style="position: fixed;width: 100%;z-index: 1000;top: 0;" role="alert"><i class="bi bi-info-circle-fill"></i> '.$msg.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>\');
            window.setTimeout(function(){$(".alert-dismissible").children("button").alert("close");},'.$time.');';
    }
    public static function echoSuccess($msg,$time=5000): string
    {
        return '<div class="alert alert-success alert-dismissible" role="alert">
                <i class="bi bi-check-circle-fill"></i> '.$msg .'
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                    <script>window.setTimeout(function(){$(".alert-dismissible").children("button").alert("close");},'.$time . ');</script>';
    }
    public static function echoWarning($msg): string
    {
        return '<div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i> '.$msg .'
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    public static function echoDanger($msg,$time=5000): string
    {
        return '$(document.body).append(\''.
            '<div class="alert alert-danger alert-dismissible" role="alert" style="position: fixed;width: 100%;z-index: 1000;top: 0;" ><i class="bi bi-exclamation-triangle-fill"></i> '.$msg.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>\');
            window.setTimeout(function(){$(".alert-dismissible").children("button").alert("close");},'.$time.');';
    }
}