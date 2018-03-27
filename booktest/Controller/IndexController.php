<?php 
use DB\Books;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class IndexController {

    public $book;

    function __construct() {
        $this->book = new Books();
    }

    function index() {
        $books = $this->book->getAllBooks();
        View('books/index', ['list' => $books]);
    }

    function getaddinput() {
        return View('books/input');
    }

    function savedata() {
        $this->book->addBooks($_POST);
        $books = $this->book->getAllBooks();
        View('books/table', ['list' => $books]);
    }

    function editdata() {
        $code = $this->book->editBooks($_POST);
        if ($code < 0) {
            echo -1;
        } else {
            $books = $this->book->getAllBooks();
            View('books/table', ['list' => $books]);
        }
    }

    function bookinfo() {
        $book = $this->book->getInfobyId($_REQUEST['id']);
        echo json_encode($book);
    }

    function bookdelete() {
        $this->book->bookDelete($_REQUEST['id']);
        $books = $this->book->getAllBooks();
        View('books/table', ['list' => $books]);
    }

}
