<?php
/**
 * klasa odgovorna za manipuliranje s postovima u bazi
 */
class PostService {
    /**
     * dohvaća sve postove određenog korisnika
     * 
     * @param int
     * @return array svih postova određenog usera
     */
    function getPostsByUser($userId) {
        try {
            $db = DB::getConnection();
            $st = $db->prepare('SELECT * FROM post where user_id=:user_id');
            $st->execute(array('user_id' => $userId));
        } catch (PDOException $e) {
            exit('PDO error ' . $e->getMessage());
        }

        $arr = array();
        while ($row = $st->fetch()) {
            $arr[] = new Post($row['id'], $row['title'], $row['text'], $row['created'], $row['user_id']);
        }

        return $arr;
    }
    /**
     * dohvaća post s određenim id-em
     * 
     * @param int
     * @return Post
     */
    function getPostById($postId) {
        try {
            $db = DB::getConnection();
            $st = $db->prepare('SELECT * FROM post where id=:id LIMIT 1');
            $st->execute(array('id' => $postId));

            $row = $st->fetch();
        } catch (PDOException $e) {
            exit('PDO error ' . $e->getMessage());
        }

        return new Post($row['id'], $row['title'], $row['text'], $row['created'], $row['user_id']);
    }    
    /**
     * unos posta u bazu
     * 
     * @param string $title naslov posta
     * @param string $text tekst posta
     * @param bool $change novi posta ili izmjena postojećeg
     * @param string $pid naslov posta
     * 
     * @return void
     */
    function insertPost($title, $text, $pid = false) {
        try {
            $db = DB::getConnection();
            if ($pid) {
                $st = $db->prepare('UPDATE post SET title=:title, text=:text 
                        WHERE id=:id');
                // Izvrši sad tu naredbu. 
                $st->execute(array('id' => $pid, 'title' => $title, 'text' => $text));
            } else {
                // Prvo pripremi insert naredbu.
                $st = $db->prepare('INSERT INTO post (title, text, user_id) '
                        . 'VALUES (:title, :text, :user_id)');
                // Izvrši sad tu insert naredbu. 
                $st->execute(array('title' => $title, 'text' => $text, 'user_id' => $_SESSION['user']->id));
            }
        } catch (PDOException $e) {
            echo( 'Greška:' . $e->getMessage() );
            exit;
        }
    }
    /**
     * briše post s danim id-em iz baze
     * 
     * @param int $postId id posta
     * @return void ako je došlo do greške
     */
    function deletePost($postId) {
        try {
            $db = DB::getConnection();

            // Prvo pripremi delete naredbu.
            $st = $db->prepare('DELETE FROM post '
                    . 'WHERE id LIKE :id');

            // Izvrši sad tu delete naredbu. 
            $st->execute(array('id' => $postId));
        } catch (PDOException $e) {
            echo( 'Greška:' . $e->getMessage() );
            exit();
        }
    }

}
?>

