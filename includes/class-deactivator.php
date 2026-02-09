<?php
namespace RealEstatePro;
class Deactivator {
    public static function deactivate() {
        flush_rewrite_rules();
    }
}
