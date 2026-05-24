<?php
class GroupModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
// 1. Fetch the main group details and join with the package name
    public function getGroupDetails($groupTripId) {
        $sql = "SELECT g.*, p.title as package_name
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

    public function getGroupMessages($groupTripId) {
        $sql = "SELECT gm.*, t.first_name, t.last_name
                FROM groupmessages gm
                JOIN travellers t ON gm.traveller_id = t.user_id
                WHERE gm.group_trip_id = ?
                ORDER BY gm.sent_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$groupTripId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveMessage($groupTripId, $travellerId, $messageText) {
        $sql = "INSERT INTO groupmessages (group_trip_id, traveller_id, message_text, sent_at) 
                VALUES (?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$groupTripId, $travellerId, $messageText]);
    }
}
?>