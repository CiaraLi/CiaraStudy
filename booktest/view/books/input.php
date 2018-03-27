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
    <input type="button"  class="btn" value="remove" onclick="remove($(this))">
</td>      
</p>
