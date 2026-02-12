<?php

function blockUser($mysqli, $id){
    $stmt = $mysqli->prepare("UPDATE user SET status='blocked' WHERE id=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function unblockUser($mysqli, $id){
    $stmt = $mysqli->prepare("UPDATE user SET status='active' WHERE id=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
