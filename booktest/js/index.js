/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var host="";
var validate=[];
function add(){ 
    $.get(host+"?mod=getaddinput",function(data){
        $('.list').append(data);
    });
}
function delbook(id){ 
    $.post(host+"?mod=bookdelete",{id:id},function(data){ 
              $('#tablelist').html(data);
              $('.list').html("");
    });
}
function remove(obj){
      obj.parents('p').remove();
}
function addsubmit(){
       
        if(validate["listinput"]!=undefined||false==NumberValidate($('input[name="isbn[]"]'),'listinput')){
            return false;
        }
        var formData =new FormData($("#listinput")[0]); 
        var ISBN=[];
        $('input[name="isbn[]"]').each(function(){
            ISBN.push($(this).val());
        });
        var quantity=[];
        $('input[name="quantity[]"]').each(function(){
            quantity.push($(this).val());
        }); 
        var name=[];
        $('input[name="name[]"]').each(function(){
            name.push($(this).val());
        }); 
     $.ajax({  
          url: host+"?mod=savedata",  
          type: 'POST',  
//          dataType:'json',
          data: {
              'isbn':ISBN,
              'quantity':quantity,
              'name':name,
          },   
          success: function (data) { 
              $('#tablelist').html(data);
              $('.list').html("");
          },   
        });         
}
function edit(id){ 
    $('#edittr').insertAfter($('#b_'+id));
    $('#edittr').removeClass('hidden');   
    $.ajax({  
          url: host+"?mod=bookinfo",  
          type: 'POST',  
          dataType:'json',
          data: {
              'id':id, 
          },   
          success: function (data) {
              $('input[name="e_id"]').val(data.id);
              $('input[name="e_isbn"]').val(data.isbn);
              $('input[name="e_quantity"]').val(data.quantity);
              $('input[name="e_name"]').val(data.name);
          },   
        });         
}
function editsubmit(){ 
    if(validate["editform"]!=undefined||false==NumberValidate($('input[name="e_isbn"]'),'editform')){
        return false;
    }
     $.ajax({  
          url: host+"?mod=editdata",  
          type: 'POST',  
//          dataType:'json',
          data: {
              'id':$('input[name="e_id"]').val(),
              'isbn':$('input[name="e_isbn"]').val(),
              'quantity':$('input[name="e_quantity"]').val(),
              'name':$('input[name="e_name"]').val(),
          },   
          success: function (data) { 
              if(data=="-1"){   
                $('input[name="e_isbn"]').addClass('input-worng');
                  return false;
              }
              $('#tablelist').html(data);
              $('.list').html("");
          },   
        });         
}
function NumberValidate(obj,form){ 
    if(obj.val().trim()==''||!/^(\d)+$/.test(obj.val())){
        obj.addClass('input-worng');
        validate.push(form);
        return false;
    }else{
        obj.removeClass('input-worng');
       delete  validate[form];
        return true;
    }
} 
$(function(){   
});
