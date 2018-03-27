  <tr id="b_<?php echo $id?>">
        <td>
            <?php echo  $id?>
        </td>
        <td>
            <?php echo  $isbn ?>
        </td>
        <td>
            <?php echo  $name ?>
        </td>
        <td>
           <?php echo $quantity ?>
        </td>
        <td>
               <input type="button"  class="btn" value="edit" onclick="edit(<?php echo $id ?>)"> 
               <input type="button"  class="btn" value="delete" onclick="delbook(<?php echo $id ?>)"> 
        </td>
    </tr> 