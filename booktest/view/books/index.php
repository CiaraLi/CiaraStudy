<?php View('common/header') ?> 
<form id="listinput" >
    <div >
        <p>  
        <td>    
            <label>ISBN</label><input type="text"  name="isbn[]" class="text" onblur="NumberValidate($(this),'listinput')">
        </td>  
        <td>     
            <label>Quantity</label>
            <input type="number"  name="quantity[]" class="quan" onblur="NumberValidate($(this),'listinput')"> 
        </td>  
        <td>    
            <label>NAME</label><input type="text"  name="name[]" class="text" >
        </td>  
        <td>    
            <input type="button"  class="btn" value="add" onclick="add()">
        </td>  
        </p>
    </div> 
    <div class="list" >
    </div> 
    <div>
        <input type="button" value="submit" class="btn" onclick="addsubmit()"/>
    </div>
</form>   
<div >
<?php View('books/table', ['list' => $list]) ?>
</div>
<?php
View('common/footer')?>