<?php

require_once 'model/db.class.php';

/**
 * klasa za upravljanje komentarima
 */
class CommentController extends BaseController {
    
    /**
     * popunjava odgovarajući template s komentarima i redirecta na stranicu s komentarima
     */
    public function index() {
        $cs = new CommentService();
        $ps = new PostService();

        $postId = $_GET['pid'];

        $comments = $cs->getCommentsOnPost($postId);
        $post = $ps->getPostById($postId);

        // Popuni template potrebnim podacima
        $this->registry->template->post = $post;
        $this->registry->template->commentList = $comments;
        $this->registry->template->show('post_comments');
    }
    
    /**
     * sprema novi komentar
     * 
     * @return void (ako je došlo do greške)
     */
    public function saveComment() {
        // Provjeri je li unešen komentar
        if (!isset($_POST["comment"])) {
            return;
        }
        //Sve je ok. Odi na funkciju za ubacivanje komentara.
        $cs = new CommentService();
        try {
            $cs->insertComment($_POST['comment'], $_POST['postId']);
            $this->redirect('comment/index/?pid=' . $_POST['postId']);
        } catch (Exception $ex) {
            $this->registry->template->errorMessage = $ex->getMessage();
            $this->index();
            return;
        }
    }
	
	public function updateComment() {
        // Provjeri je li unešen komentar
        if (!isset($_POST["comment"])) {
            return;
        }
        $cs = new CommentService();
        try {
            $cs->updateComment($_POST['comment'], $_POST['commentId']);
            $this->redirect('comment/index/?pid=' . $_POST['postId']);
        } catch (Exception $ex) {
            $this->registry->template->errorMessage = $ex->getMessage();
            $this->index();
            return;
        }
    }

    public function DeleteEditComment() {
        $cs = new CommentService();
        try {

            // kliknuli smo na delete
            if (isset($_POST['delete_button']))
			{
                $cs->deleteComment($_POST['commentId']);
				$this->redirect('comment/index/?pid=' . $_POST['postId']);
			}

            // kliknuli smo na edit
            elseif (isset($_POST['edit_button']))
			{
				$ps = new PostService();
        		$postId = $_POST['postId'];

				$comments = $cs->getCommentsOnPost($postId);
				$post = $ps->getPostById($postId);

				// Popuni template potrebnim podacima
				$this->registry->template->post = $post;
				$this->registry->template->commentList = $comments;
				
				// spremi u template id komentara kojeg želimo editirati
				$this->registry->template->commentToEditId = $_POST['commentId'];
				
				
				$this->registry->template->show('edit_comments');
			}
       

            //$this->redirect('comment/index/?pid=' . $_POST['postId']);
        } catch (Exception $ex) {
            $this->registry->template->errorMessage = $ex->getMessage();
            $this->index();
            return;
        }
    }
	
	
	
}

?>
