<?php
namespace DB;
use DB\DataBase; 
/**
 * Description of books
 *
 * @author ciara
 */
class Books extends  DataBase{   
    function __construct() {
        parent::__construct(); 
        $this->DB('books');
    }
    function getAllBooks(){
        $books=$this->QueryFetch();
        return  isset($books->datalist) ? $books->datalist : [];
    }
    function addBooks($input) {  
        $isbn = $input['isbn'];
        $quantity = $input['quantity'];
        $name = $input['name'];
        foreach ($isbn as $key => $value) {
            if (!empty($value)) {
                if ($this->isExists(['isbn' => $value])) {
                    $quant = $this->Filed(['quantity'])->QueryFirst(['isbn' => $value]);
                    $this->Update(['isbn' => $value], ['quantity' => $quant['quantity'] + $quantity[$key], 'name' => $name[$key]]);
                } else {
                    $this->Insert(["isbn" => $value, 'quantity' => $quantity[$key], 'name' => $name[$key]]);
                }
            }
        }
    }
    function editBooks($input) { 
        $id = $input['id'];
        $isbn = $input['isbn'];
        $quantity = $input['quantity'];
        $name = $input['name'];
        if (!empty($id)) {
            if (!$this->isExists(['isbn' => $isbn],['id'=>$id])) { 
                $this->Update(['id' => $id], ['isbn' => $isbn,'quantity' =>  $quantity, 'name' => $name]);
                return 1;
            } else {
                return -1;
            }
        }
          return -1;
    }
    function getInfobyId($id) {   
        $books = $this->QueryFirst(['id' => $id]); 
        return  isset($books) ? $books : []; 
    }
    function bookDelete($id) {   
        $books = $this->delete(['id' => $id]); 
        return  isset($books) ? $books : []; 
    }
}
