<?php
class FavouriteModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getFavouritePackages($travellerId) {
        $sql = "SELECT p.package_id, p.title, p.base_price, p.duration_days, 
                       f.favourite_id, f.favouritable_id
                FROM favourites f
                JOIN favouritable ft ON f.favouritable_id = ft.favouritable_id
                JOIN packages p ON p.favouritable_id = ft.favouritable_id
                WHERE f.traveller_id = ? AND ft.target_type = 'package'
                ORDER BY f.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$travellerId]);
        return $stmt->fetchAll();
    }

    public function isFavourited($travellerId, $favouritableId) {
        $stmt = $this->pdo->prepare("SELECT favourite_id FROM favourites WHERE traveller_id = ? AND favouritable_id = ?");
        $stmt->execute([$travellerId, $favouritableId]);
        return $stmt->fetch() !== false;
    }

    public function addFavourite($travellerId, $favouritableId) {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO favourites (traveller_id, favouritable_id) VALUES (?, ?)");
        return $stmt->execute([$travellerId, $favouritableId]);
    }

    public function removeFavourite($travellerId, $favouritableId) {
        $stmt = $this->pdo->prepare("DELETE FROM favourites WHERE traveller_id = ? AND favouritable_id = ?");
        return $stmt->execute([$travellerId, $favouritableId]);
    }
}
?>