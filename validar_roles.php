<?php
    function isSubset($subset, $superset) {
        return empty(array_diff($subset, $superset));
    }
?>