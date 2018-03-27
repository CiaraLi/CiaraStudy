<table id="tablelist" cellspacing="0"> 
    <tr>
        <td>
            ID
        </td>
        <td>
            ISBN
        </td>
        <td>
            NAME
        </td>
        <td>
            Quantity
        </td>
        <td>
            Option
        </td>
    </tr>
    <?php
    if (isset($list)) {
        foreach ($list as $key => $value) {
            View('books/tr', $value);
        }
    }
    View('books/edit');
    ?> 
    
</table>  