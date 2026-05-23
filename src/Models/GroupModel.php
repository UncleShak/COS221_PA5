<?php
class GroupModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // 1. Fetch the main group details and join with the package name
    public function getGroupDetails($groupTripId) {
        $sql = "SELECT g.*, p.title as package_name, p.destination
                FROM grouptrips g
                JOIN packages p ON g.package_id = p.package_id
                WHERE g.group_trip_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$groupTripId]);
        return $stmt->fetch();
    }
    
    public function getGroupRoster($groupTripId) {
        $sql = "SELECT t.first_name, t.last_name, t.profile_picture_url, gp.status, gp.joined_at
                FROM grouptripparticipants gp
                JOIN travellers t ON gp.traveller_id = t.user_id
                WHERE gp.group_trip_id = ? AND gp.status = 'confirmed'
                ORDER BY gp.joined_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$groupTripId]);
        return $stmt->fetchAll();
    }

    public function isUserInGroup($travellerId, $groupTripId) {
        $sql = "SELECT 1 
                FROM grouptripparticipants 
                WHERE traveller_id = ? AND group_trip_id = ? AND status = 'confirmed'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$travellerId, $groupTripId]);
        return (bool) $stmt->fetch();
    }
}
?>