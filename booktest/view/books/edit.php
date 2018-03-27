<tr  class="hidden" id="edittr">           
        <td> EditInfo:<input type="hidden"  name="e_id" value=""></td>
        <td> <input type="text"  name="e_isbn" class="text" value="" onblur="NumberValidate($(this),'editform')">
        <td><input type="text"  name="e_name" class="text" value=""></td>
        <td> 
            <input type="number"  name="e_quantity" class="quan" onblur="NumberValidate($(this),'listinput')"> 
        </td>
        <td>  <input type="button"  class="btn" value="ok" onclick="editsubmit($(this))"></td>
    </tr> 

